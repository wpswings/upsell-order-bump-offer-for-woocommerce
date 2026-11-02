<?php
/**
 * Smart Offer Recommendations - training and scoring engine.
 *
 * Item-to-item association rules mined from order history (co-purchase in the
 * same order + "bought next" by the same customer), optionally blended with an
 * LLM complementarity score obtained through the WordPress AI Client.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      3.1.8
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/includes
 * @author     WP Swings <webmaster@wpswings.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Recommendation engine.
 *
 * @since 3.1.8
 */
class Upsell_Order_Bump_Reco_Engine {

	/**
	 * Training lock transient.
	 *
	 * @var string
	 */
	const LOCK = 'wps_ubo_reco_training_lock';

	/**
	 * Per-request cache of pair rows keyed by source id.
	 *
	 * @var array
	 */
	private static $pair_cache = array();

	/*
	|--------------------------------------------------------------------------
	| Training
	|--------------------------------------------------------------------------
	*/

	/**
	 * Cron / Action Scheduler callback.
	 *
	 * @return void
	 */
	public static function run_scheduled() {
		self::train();
	}

	/**
	 * Train a new model and publish it atomically.
	 *
	 * @return array|WP_Error Model metadata or error.
	 */
	public static function train() {
		global $wpdb;

		if ( get_transient( self::LOCK ) ) {
			return new WP_Error( 'wps_ubo_reco_locked', __( 'A training run is already in progress.', 'upsell-order-bump-offer-for-woocommerce' ) );
		}
		set_transient( self::LOCK, 1, 15 * MINUTE_IN_SECONDS );

		if ( function_exists( 'wc_set_time_limit' ) ) {
			wc_set_time_limit( 0 );
		}

		$started  = microtime( true );
		$settings = Upsell_Order_Bump_Reco_Db::get_settings();
		Upsell_Order_Bump_Reco_Db::maybe_upgrade();
		Upsell_Order_Bump_Reco_Db::update_model(
			array(
				'status'     => 'training',
				'started_at' => time(),
				'error'      => '',
			)
		);

		try {
			$pairs_tmp = Upsell_Order_Bump_Reco_Db::pairs_table( true );
			$prods_tmp = Upsell_Order_Bump_Reco_Db::products_table( true );

			$wpdb->query( "TRUNCATE TABLE {$pairs_tmp}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery
			$wpdb->query( "TRUNCATE TABLE {$prods_tmp}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery

			$cutoff_ts   = time() - ( (int) $settings['lookback_days'] * DAY_IN_SECONDS );
			$statuses    = self::order_statuses();
			$total_paid  = self::count_paid_orders( $statuses, $cutoff_ts );
			$analytics_n = self::count_analytics_orders( $statuses, $cutoff_ts );

			if ( $total_paid > 0 && $analytics_n >= 0.9 * $total_paid ) {
				$source = 'analytics';
				$orders = self::build_from_analytics( $settings, $statuses, $cutoff_ts );
			} else {
				$source = 'orders';
				$orders = self::build_from_orders( $settings, $statuses, $cutoff_ts );
			}

			if ( $orders > 0 ) {
				self::directional_sequences();
				self::score_and_prune( $settings, $orders );
			}

			// Optional LLM enrichment; never blocks publishing.
			$ai_meta = array(
				'ai_status'  => '',
				'ai_anchors' => 0,
				'ai_error'   => '',
			);
			if ( 'on' === $settings['ai_enabled'] && $orders > 0 ) {
				$ai_meta = Upsell_Order_Bump_Reco_Ai::enrich( $settings );
			}

			self::swap_tables();

			$model = Upsell_Order_Bump_Reco_Db::get_model();
			$meta  = Upsell_Order_Bump_Reco_Db::update_model(
				array_merge(
					$ai_meta,
					array(
						'status'     => 'idle',
						'trained_at' => time(),
						'duration'   => round( microtime( true ) - $started, 2 ),
						'source'     => $source,
						'orders'     => (int) $orders,
						'products'   => (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . Upsell_Order_Bump_Reco_Db::products_table() ), // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery
						'pairs'      => (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . Upsell_Order_Bump_Reco_Db::pairs_table() ), // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery
						'error'      => '',
						'version'    => (int) $model['version'] + 1,
					)
				)
			);

			self::$pair_cache = array();

			/**
			 * Fires after a recommendation model is published.
			 *
			 * @since 3.1.8
			 * @param array $meta Model metadata.
			 */
			do_action( 'wps_ubo_reco_trained', $meta );

			delete_transient( self::LOCK );
			return $meta;

		} catch ( Throwable $e ) {
			Upsell_Order_Bump_Reco_Db::update_model(
				array(
					'status' => 'error',
					'error'  => sanitize_text_field( $e->getMessage() ),
				)
			);
			delete_transient( self::LOCK );
			return new WP_Error( 'wps_ubo_reco_failed', $e->getMessage() );
		}
	}

	/**
	 * Order statuses used for training.
	 *
	 * @return string[] Prefixed statuses (wc-*).
	 */
	public static function order_statuses() {
		/**
		 * Filters the order statuses that count as purchases for training.
		 *
		 * @since 3.1.8
		 * @param string[] $statuses Prefixed order statuses.
		 */
		$statuses = (array) apply_filters( 'wps_ubo_reco_order_statuses', array( 'wc-completed', 'wc-processing' ) );
		$statuses = array_values( array_filter( array_map( 'sanitize_key', $statuses ) ) );
		return $statuses ? $statuses : array( 'wc-completed' );
	}

	/**
	 * Paid orders in the window, according to the order store.
	 *
	 * @param string[] $statuses  Statuses.
	 * @param int      $cutoff_ts Window start (unix).
	 * @return int
	 */
	private static function count_paid_orders( $statuses, $cutoff_ts ) {
		$result = wc_get_orders(
			array(
				'type'         => 'shop_order',
				'status'       => $statuses,
				'date_created' => '>=' . $cutoff_ts,
				'limit'        => 1,
				'paginate'     => true,
				'return'       => 'ids',
			)
		);
		return is_object( $result ) && isset( $result->total ) ? (int) $result->total : 0;
	}

	/**
	 * Paid orders in the window present in WooCommerce Analytics tables.
	 *
	 * @param string[] $statuses  Statuses.
	 * @param int      $cutoff_ts Window start (unix).
	 * @return int
	 */
	private static function count_analytics_orders( $statuses, $cutoff_ts ) {
		global $wpdb;

		$stats  = $wpdb->prefix . 'wc_order_stats';
		$lookup = $wpdb->prefix . 'wc_order_product_lookup';
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $stats ) ) !== $stats || $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $lookup ) ) !== $lookup ) { // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			return 0;
		}

		$in = implode( ',', array_fill( 0, count( $statuses ), '%s' ) );
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, WordPress.DB.DirectDatabaseQuery
		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$stats} WHERE parent_id = 0 AND status IN ({$in}) AND date_created_gmt >= %s",
				array_merge( $statuses, array( gmdate( 'Y-m-d H:i:s', $cutoff_ts ) ) )
			)
		);
		// phpcs:enable
	}

	/**
	 * Build counts with SQL from wc_order_product_lookup / wc_order_stats.
	 *
	 * @param array    $settings  Settings.
	 * @param string[] $statuses  Statuses.
	 * @param int      $cutoff_ts Window start.
	 * @return int Number of orders used.
	 */
	private static function build_from_analytics( $settings, $statuses, $cutoff_ts ) {
		global $wpdb;

		$lookup    = $wpdb->prefix . 'wc_order_product_lookup';
		$stats     = $wpdb->prefix . 'wc_order_stats';
		$pairs_tmp = Upsell_Order_Bump_Reco_Db::pairs_table( true );
		$prods_tmp = Upsell_Order_Bump_Reco_Db::products_table( true );
		$in        = implode( ',', array_fill( 0, count( $statuses ), '%s' ) );
		$cutoff    = gmdate( 'Y-m-d H:i:s', $cutoff_ts );
		$where     = "s.parent_id = 0 AND s.status IN ({$in}) AND s.date_created_gmt >= %s";
		$args      = array_merge( $statuses, array( $cutoff ) );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, WordPress.DB.DirectDatabaseQuery

		$orders = (int) $wpdb->get_var(
			$wpdb->prepare( "SELECT COUNT(DISTINCT l.order_id) FROM {$lookup} l INNER JOIN {$stats} s ON s.order_id = l.order_id WHERE {$where} AND l.product_id > 0", $args )
		);
		if ( 0 === $orders ) {
			return 0;
		}

		$wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$prods_tmp} (product_id, order_count)
				SELECT l.product_id, COUNT(DISTINCT l.order_id)
				FROM {$lookup} l INNER JOIN {$stats} s ON s.order_id = l.order_id
				WHERE {$where} AND l.product_id > 0
				GROUP BY l.product_id",
				$args
			)
		);

		$wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$pairs_tmp} (source_id, target_id, co_count)
				SELECT l1.product_id, l2.product_id, COUNT(DISTINCT l1.order_id)
				FROM {$lookup} l1
				INNER JOIN {$lookup} l2 ON l2.order_id = l1.order_id AND l2.product_id <> l1.product_id
				INNER JOIN {$stats} s ON s.order_id = l1.order_id
				WHERE {$where} AND l1.product_id > 0 AND l2.product_id > 0
				GROUP BY l1.product_id, l2.product_id",
				$args
			)
		);

		$window = (int) $settings['seq_window_days'];
		if ( $window > 0 && (int) $settings['seq_weight'] > 0 ) {
			$wpdb->query(
				$wpdb->prepare(
					"INSERT INTO {$pairs_tmp} (source_id, target_id, seq_count)
					SELECT l1.product_id, l2.product_id, COUNT(DISTINCT l1.customer_id) AS seq
					FROM {$lookup} l1
					INNER JOIN {$stats} s ON s.order_id = l1.order_id
					INNER JOIN {$lookup} l2 ON l2.customer_id = l1.customer_id
						AND l2.order_id <> l1.order_id
						AND l2.product_id <> l1.product_id
						AND l2.date_created > l1.date_created
						AND l2.date_created <= DATE_ADD(l1.date_created, INTERVAL %d DAY)
					INNER JOIN {$stats} s2 ON s2.order_id = l2.order_id AND s2.parent_id = 0 AND s2.status IN ({$in})
					WHERE {$where} AND l1.customer_id > 0 AND l1.product_id > 0 AND l2.product_id > 0
					GROUP BY l1.product_id, l2.product_id
					ON DUPLICATE KEY UPDATE seq_count = VALUES(seq_count)",
					array_merge( array( $window ), $statuses, $args )
				)
			);
		}

		// phpcs:enable
		return $orders;
	}

	/**
	 * Build counts in PHP from wc_get_orders() (when Analytics data is missing).
	 *
	 * @param array    $settings  Settings.
	 * @param string[] $statuses  Statuses.
	 * @param int      $cutoff_ts Window start.
	 * @return int Number of orders used.
	 */
	private static function build_from_orders( $settings, $statuses, $cutoff_ts ) {
		/**
		 * Filters the maximum number of orders read by the PHP training fallback.
		 *
		 * @since 3.1.8
		 * @param int $max Maximum orders (newest first).
		 */
		$max      = max( 100, (int) apply_filters( 'wps_ubo_reco_max_orders', 5000 ) );
		$per_page = 200;
		$page     = 1;
		$orders   = 0;
		$prods    = array();
		$co       = array();
		$by_cust  = array();

		do {
			$ids = wc_get_orders(
				array(
					'type'         => 'shop_order',
					'status'       => $statuses,
					'date_created' => '>=' . $cutoff_ts,
					'limit'        => $per_page,
					'page'         => $page,
					'orderby'      => 'date',
					'order'        => 'DESC',
					'return'       => 'ids',
				)
			);

			foreach ( $ids as $order_id ) {
				$order = wc_get_order( $order_id );
				if ( ! $order ) {
					continue;
				}
				$basket = array();
				foreach ( $order->get_items() as $item ) {
					$pid = (int) $item->get_product_id();
					if ( $pid > 0 ) {
						$basket[ $pid ] = true;
					}
				}
				if ( empty( $basket ) ) {
					continue;
				}
				$basket = array_keys( $basket );
				++$orders;

				foreach ( $basket as $a ) {
					$prods[ $a ] = isset( $prods[ $a ] ) ? $prods[ $a ] + 1 : 1;
					foreach ( $basket as $b ) {
						if ( $a !== $b ) {
							$key        = $a . ':' . $b;
							$co[ $key ] = isset( $co[ $key ] ) ? $co[ $key ] + 1 : 1;
						}
					}
				}

				$customer = $order->get_customer_id() ? 'u' . $order->get_customer_id() : ( $order->get_billing_email() ? 'e' . md5( strtolower( $order->get_billing_email() ) ) : '' );
				$created  = $order->get_date_created();
				if ( $customer && $created ) {
					$by_cust[ $customer ][] = array( $created->getTimestamp(), $basket );
				}

				if ( $orders >= $max ) {
					break 2;
				}
			}
			++$page;
		} while ( count( $ids ) === $per_page );

		if ( 0 === $orders ) {
			return 0;
		}

		// "Bought next": B within the window after A, counted once per customer.
		$seq    = array();
		$window = (int) $settings['seq_window_days'] * DAY_IN_SECONDS;
		if ( $window > 0 && (int) $settings['seq_weight'] > 0 ) {
			foreach ( $by_cust as $customer => $history ) {
				if ( count( $history ) < 2 ) {
					continue;
				}
				usort(
					$history,
					static function ( $x, $y ) {
						return $x[0] <=> $y[0];
					}
				);
				$seen = array();
				$n    = count( $history );
				for ( $i = 0; $i < $n; $i++ ) {
					for ( $j = $i + 1; $j < $n; $j++ ) {
						$gap = $history[ $j ][0] - $history[ $i ][0];
						if ( $gap > $window ) {
							break;
						}
						if ( $gap <= 0 ) {
							continue;
						}
						foreach ( $history[ $i ][1] as $a ) {
							foreach ( $history[ $j ][1] as $b ) {
								if ( $a !== $b ) {
									$seen[ $a . ':' . $b ] = true;
								}
							}
						}
					}
				}
				foreach ( array_keys( $seen ) as $key ) {
					$seq[ $key ] = isset( $seq[ $key ] ) ? $seq[ $key ] + 1 : 1;
				}
			}
		}

		$product_rows = array();
		foreach ( $prods as $pid => $count ) {
			$product_rows[] = array( $pid, $count );
		}
		self::bulk_insert( Upsell_Order_Bump_Reco_Db::products_table( true ), array( 'product_id', 'order_count' ), $product_rows, '(%d,%d)' );

		$pair_rows = array();
		foreach ( array_keys( $co + $seq ) as $key ) {
			list( $a, $b ) = array_map( 'intval', explode( ':', $key ) );
			$pair_rows[]   = array( $a, $b, isset( $co[ $key ] ) ? $co[ $key ] : 0, isset( $seq[ $key ] ) ? $seq[ $key ] : 0 );
		}
		self::bulk_insert( Upsell_Order_Bump_Reco_Db::pairs_table( true ), array( 'source_id', 'target_id', 'co_count', 'seq_count' ), $pair_rows, '(%d,%d,%d,%d)' );

		return $orders;
	}

	/**
	 * Multi-row prepared INSERT in chunks.
	 *
	 * @param string $table       Table.
	 * @param array  $columns     Column names.
	 * @param array  $rows        Rows (numeric arrays).
	 * @param string $placeholder Row placeholder, e.g. "(%d,%d)".
	 * @return void
	 */
	private static function bulk_insert( $table, $columns, $rows, $placeholder ) {
		global $wpdb;

		foreach ( array_chunk( $rows, 500 ) as $chunk ) {
			$values = array();
			$sql    = array();
			foreach ( $chunk as $row ) {
				$sql[]  = $placeholder;
				$values = array_merge( $values, $row );
			}
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery
			$wpdb->query( $wpdb->prepare( "INSERT INTO {$table} (" . implode( ',', $columns ) . ') VALUES ' . implode( ',', $sql ), $values ) );
		}
	}

	/**
	 * Keep only the directional part of "bought next".
	 *
	 * Repeat customers buy many products in every order of time, so "B after A"
	 * alone is mostly coincidence. A real sequence is one that happens more often
	 * after A than before it: seq(A→B) becomes max(0, seq(A→B) − seq(B→A)).
	 *
	 * @return void
	 */
	private static function directional_sequences() {
		global $wpdb;

		$pairs = Upsell_Order_Bump_Reco_Db::pairs_table( true );
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery
		$rows = (array) $wpdb->get_results( "SELECT source_id, target_id, seq_count FROM {$pairs} WHERE seq_count > 0", ARRAY_A );

		$seq = array();
		foreach ( $rows as $row ) {
			$seq[ $row['source_id'] . ':' . $row['target_id'] ] = (int) $row['seq_count'];
		}

		$by_value = array();
		foreach ( $rows as $row ) {
			$reverse = isset( $seq[ $row['target_id'] . ':' . $row['source_id'] ] ) ? $seq[ $row['target_id'] . ':' . $row['source_id'] ] : 0;
			$net     = max( 0, (int) $row['seq_count'] - $reverse );
			if ( $net !== (int) $row['seq_count'] ) {
				$by_value[ $net ][] = '(' . (int) $row['source_id'] . ',' . (int) $row['target_id'] . ')';
			}
		}

		foreach ( $by_value as $net => $keys ) {
			foreach ( array_chunk( $keys, 500 ) as $chunk ) {
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery
				$wpdb->query( $wpdb->prepare( "UPDATE {$pairs} SET seq_count = %d WHERE (source_id, target_id) IN (" . implode( ',', $chunk ) . ')', $net ) );
			}
		}
	}

	/**
	 * Compute confidence / lift, drop weak pairs and keep top-K per anchor.
	 *
	 * @param array $settings Settings.
	 * @param int   $orders   Total orders (N).
	 * @return void
	 */
	private static function score_and_prune( $settings, $orders ) {
		global $wpdb;

		$pairs = Upsell_Order_Bump_Reco_Db::pairs_table( true );
		$prods = Upsell_Order_Bump_Reco_Db::products_table( true );
		$w_seq = (int) $settings['seq_weight'] / 100;

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$pairs} p
				INNER JOIN {$prods} a ON a.product_id = p.source_id
				LEFT JOIN {$prods} b ON b.product_id = p.target_id
				SET p.ml_score = LEAST(1, (p.co_count + %f * p.seq_count) / a.order_count),
					p.lift = CASE WHEN b.order_count > 0 THEN ((p.co_count + %f * p.seq_count) / a.order_count) / (b.order_count / %d) ELSE 0 END,
					p.score = LEAST(1, (p.co_count + %f * p.seq_count) / a.order_count)
				WHERE a.order_count > 0",
				$w_seq,
				$w_seq,
				max( 1, (int) $orders ),
				$w_seq
			)
		);

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$pairs} WHERE (co_count + seq_count) < %d OR ml_score < %f OR lift < %f",
				(int) $settings['min_support'],
				(int) $settings['min_confidence'] / 100,
				(float) $settings['min_lift']
			)
		);

		// Keep top-K per anchor (portable: no window functions needed).
		$k     = (int) $settings['max_candidates'];
		$heavy = $wpdb->get_col( $wpdb->prepare( "SELECT source_id FROM {$pairs} GROUP BY source_id HAVING COUNT(*) > %d", $k ) );
		foreach ( $heavy as $source_id ) {
			$drop = $wpdb->get_col(
				$wpdb->prepare( "SELECT target_id FROM {$pairs} WHERE source_id = %d ORDER BY score DESC, co_count DESC LIMIT %d, 100000", $source_id, $k )
			);
			foreach ( array_chunk( array_map( 'absint', $drop ), 500 ) as $chunk ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$pairs} WHERE source_id = %d AND target_id IN (" . implode( ',', $chunk ) . ')', $source_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			}
		}
		// phpcs:enable
	}

	/**
	 * Atomically swap the staging tables in.
	 *
	 * @return void
	 */
	private static function swap_tables() {
		global $wpdb;

		$pairs     = Upsell_Order_Bump_Reco_Db::pairs_table();
		$pairs_tmp = Upsell_Order_Bump_Reco_Db::pairs_table( true );
		$prods     = Upsell_Order_Bump_Reco_Db::products_table();
		$prods_tmp = Upsell_Order_Bump_Reco_Db::products_table( true );
		$pairs_old = $pairs . '_old';
		$prods_old = $prods . '_old';

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( "DROP TABLE IF EXISTS {$pairs_old}, {$prods_old}" );
		$wpdb->query( "RENAME TABLE {$pairs} TO {$pairs_old}, {$pairs_tmp} TO {$pairs}, {$pairs_old} TO {$pairs_tmp}, {$prods} TO {$prods_old}, {$prods_tmp} TO {$prods}, {$prods_old} TO {$prods_tmp}" );
		// phpcs:enable

		if ( $wpdb->last_error ) {
			throw new RuntimeException( $wpdb->last_error ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Runtime scoring
	|--------------------------------------------------------------------------
	*/

	/**
	 * Parent product id for a product / variation id.
	 *
	 * @param int $product_id Product or variation id.
	 * @return int
	 */
	public static function parent_id( $product_id ) {
		$product = wc_get_product( $product_id );
		if ( $product && $product->get_parent_id() && $product->is_type( 'variation' ) ) {
			return (int) $product->get_parent_id();
		}
		return (int) $product_id;
	}

	/**
	 * Pair rows for the given anchors (live model), cached per request.
	 *
	 * @param int[] $source_ids Anchor product ids.
	 * @return array source_id => [ target_id => row ].
	 */
	private static function pairs_for( $source_ids ) {
		global $wpdb;

		$source_ids = array_values( array_unique( array_filter( array_map( 'absint', $source_ids ) ) ) );
		$missing    = array_diff( $source_ids, array_keys( self::$pair_cache ) );

		if ( $missing ) {
			foreach ( $missing as $id ) {
				self::$pair_cache[ $id ] = array();
			}
			$table = Upsell_Order_Bump_Reco_Db::pairs_table();
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery
			$rows = $wpdb->get_results( "SELECT source_id, target_id, co_count, seq_count, ml_score, lift, ai_score, ai_reason, score FROM {$table} WHERE source_id IN (" . implode( ',', $missing ) . ')', ARRAY_A );
			foreach ( (array) $rows as $row ) {
				self::$pair_cache[ (int) $row['source_id'] ][ (int) $row['target_id'] ] = $row;
			}
		}

		return array_intersect_key( self::$pair_cache, array_flip( $source_ids ) );
	}

	/**
	 * Combine anchors with noisy-OR: P = 1 - Π(1 - s).
	 *
	 * @param int[] $context_ids Anchor ids (parents).
	 * @return array target_id => [ score, anchor, anchor_score, row ].
	 */
	private static function combine( $context_ids ) {
		$out = array();
		foreach ( self::pairs_for( $context_ids ) as $source_id => $targets ) {
			foreach ( $targets as $target_id => $row ) {
				$s = max( 0, min( 1, (float) $row['score'] ) );
				if ( ! isset( $out[ $target_id ] ) ) {
					$out[ $target_id ] = array(
						'miss'         => 1.0,
						'anchor'       => $source_id,
						'anchor_score' => -1,
						'row'          => $row,
					);
				}
				$out[ $target_id ]['miss'] *= ( 1 - $s );
				if ( $s > $out[ $target_id ]['anchor_score'] ) {
					$out[ $target_id ]['anchor']       = $source_id;
					$out[ $target_id ]['anchor_score'] = $s;
					$out[ $target_id ]['row']          = $row;
				}
			}
		}
		foreach ( $out as $target_id => $data ) {
			$out[ $target_id ]['score'] = 1 - $data['miss'];
			unset( $out[ $target_id ]['miss'] );
		}
		return $out;
	}

	/**
	 * How strongly a product is recommended for the given context.
	 *
	 * @param int   $product_id  Candidate (product or variation).
	 * @param int[] $context_ids Context product ids (cart / order items, parents).
	 * @return array { score: float, anchor: int }
	 */
	public static function score_for( $product_id, $context_ids ) {
		$target   = self::parent_id( $product_id );
		$combined = self::combine( $context_ids );
		if ( empty( $combined[ $target ] ) ) {
			return array(
				'score'  => 0.0,
				'anchor' => 0,
			);
		}
		return array(
			'score'  => (float) $combined[ $target ]['score'],
			'anchor' => (int) $combined[ $target ]['anchor'],
		);
	}

	/**
	 * Ranked recommendations for a context.
	 *
	 * @param int[] $context_ids Context product ids (parents).
	 * @param array $args {
	 *     @type int[]  $exclude     Product ids never to return.
	 *     @type int    $limit       Max results. Default 5.
	 *     @type string $context     bump|upsell|preview.
	 *     @type int    $customer_id Customer for purchase-history exclusion.
	 *     @type bool   $validate    Check stock / status / purchasability. Default true.
	 * }
	 * @return array[] Each: id, score, anchor, ml, ai, reason, co, lift.
	 */
	public static function recommend( $context_ids, $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'exclude'     => array(),
				'limit'       => 5,
				'context'     => 'bump',
				'customer_id' => 0,
				'validate'    => true,
			)
		);

		$context_ids = array_values( array_unique( array_filter( array_map( 'absint', $context_ids ) ) ) );
		if ( empty( $context_ids ) ) {
			return array();
		}

		$combined = self::combine( $context_ids );
		uasort(
			$combined,
			static function ( $x, $y ) {
				return $y['score'] <=> $x['score'];
			}
		);

		/**
		 * Filters the ranked candidates before validation.
		 *
		 * @since 3.1.8
		 * @param array  $combined    target_id => data (score, anchor, row).
		 * @param int[]  $context_ids Context product ids.
		 * @param string $context     bump|upsell|preview.
		 */
		$combined = (array) apply_filters( 'wps_ubo_reco_candidates', $combined, $context_ids, $args['context'] );

		$settings = Upsell_Order_Bump_Reco_Db::get_settings();
		$exclude  = array_flip( array_merge( $context_ids, array_map( 'absint', (array) $args['exclude'] ) ) );
		$check_history = $args['validate'] && 'on' === $settings['exclude_purchased'] && $args['customer_id'] > 0;
		$customer      = $check_history ? get_userdata( (int) $args['customer_id'] ) : false;

		$out = array();
		foreach ( $combined as $target_id => $data ) {
			if ( isset( $exclude[ $target_id ] ) ) {
				continue;
			}
			if ( $args['validate'] ) {
				$product = wc_get_product( $target_id );
				if ( ! $product || 'publish' !== $product->get_status() || ! $product->is_purchasable() || ! $product->is_in_stock() || $product->is_type( array( 'grouped', 'external' ) ) ) {
					continue;
				}
				if ( $customer && wc_customer_bought_product( $customer->user_email, $customer->ID, $target_id ) ) {
					continue;
				}
			}
			$row   = $data['row'];
			$out[] = array(
				'id'     => (int) $target_id,
				'score'  => round( (float) $data['score'], 4 ),
				'anchor' => (int) $data['anchor'],
				'ml'     => round( (float) $row['ml_score'], 4 ),
				'ai'     => null === $row['ai_score'] ? null : round( (float) $row['ai_score'], 4 ),
				'reason' => (string) $row['ai_reason'],
				'co'     => (int) $row['co_count'],
				'seq'    => (int) $row['seq_count'],
				'lift'   => round( (float) $row['lift'], 2 ),
			);
			if ( count( $out ) >= (int) $args['limit'] ) {
				break;
			}
		}
		return $out;
	}

	/**
	 * Strongest pairs of the live model (admin insight).
	 *
	 * @param int $limit Rows.
	 * @return array[]
	 */
	public static function top_pairs( $limit = 25 ) {
		global $wpdb;
		$table = Upsell_Order_Bump_Reco_Db::pairs_table();
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery
		return (array) $wpdb->get_results( $wpdb->prepare( "SELECT source_id, target_id, co_count, seq_count, ml_score, lift, ai_score, ai_reason, score FROM {$table} ORDER BY score DESC, co_count DESC LIMIT %d", $limit ), ARRAY_A );
	}

	/**
	 * Reset the per-request cache (tests / after training).
	 *
	 * @return void
	 */
	public static function flush_cache() {
		self::$pair_cache = array();
	}
}
