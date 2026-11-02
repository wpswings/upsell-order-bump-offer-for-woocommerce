<?php
/**
 * Smart Offer Recommendations - storage, settings and scheduling.
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
 * Tables, settings and model metadata for the recommendation engine.
 *
 * @since 3.1.8
 */
class Upsell_Order_Bump_Reco_Db {

	/**
	 * Schema version.
	 *
	 * @var string
	 */
	const DB_VERSION = '1.0.0';

	/**
	 * Option holding the installed schema version.
	 *
	 * @var string
	 */
	const DB_VERSION_OPTION = 'wps_ubo_reco_db_version';

	/**
	 * Settings option.
	 *
	 * @var string
	 */
	const SETTINGS_OPTION = 'wps_ubo_reco_settings';

	/**
	 * Model metadata option.
	 *
	 * @var string
	 */
	const MODEL_OPTION = 'wps_ubo_reco_model';

	/**
	 * Training hook.
	 *
	 * @var string
	 */
	const TRAIN_HOOK = 'wps_ubo_reco_train';

	/**
	 * Action Scheduler group.
	 *
	 * @var string
	 */
	const AS_GROUP = 'wps-ubo-reco';

	/**
	 * Order meta key holding upsell product decisions.
	 *
	 * @var string
	 */
	const ORDER_META = '_wps_ubo_reco_upsell';

	/**
	 * Targeting modes (per bump / funnel).
	 *
	 * @var string[]
	 */
	const TARGETING_MODES = array( 'static', 'smart', 'hybrid' );

	/**
	 * Offer product sources (per bump / funnel).
	 *
	 * @var string[]
	 */
	const PRODUCT_SOURCES = array( 'static', 'ai' );

	/**
	 * Pairs table name.
	 *
	 * @param bool $tmp Whether to return the staging twin.
	 * @return string
	 */
	public static function pairs_table( $tmp = false ) {
		global $wpdb;
		return $wpdb->prefix . 'wps_ubo_reco_pairs' . ( $tmp ? '_tmp' : '' );
	}

	/**
	 * Products table name.
	 *
	 * @param bool $tmp Whether to return the staging twin.
	 * @return string
	 */
	public static function products_table( $tmp = false ) {
		global $wpdb;
		return $wpdb->prefix . 'wps_ubo_reco_products' . ( $tmp ? '_tmp' : '' );
	}

	/**
	 * Default settings.
	 *
	 * @return array
	 */
	public static function default_settings() {
		return array(
			'enabled'          => 'off',
			'lookback_days'    => 365,
			'min_support'      => 3,
			'min_confidence'   => 5,
			'min_lift'         => 1.0,
			'seq_window_days'  => 60,
			'seq_weight'       => 50,
			'match_threshold'  => 10,
			'exclude_purchased' => 'on',
			'max_candidates'   => 20,
			'schedule'         => 'daily',
			'ai_enabled'       => 'off',
			'ai_provider'      => '',
			'ai_anchor_limit'  => 20,
			'ai_weight'        => 30,
			'ai_cold_start'    => 'on',
		);
	}

	/**
	 * Settings merged with defaults.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$saved = get_option( self::SETTINGS_OPTION, array() );
		return wp_parse_args( is_array( $saved ) ? $saved : array(), self::default_settings() );
	}

	/**
	 * Sanitize a raw settings array (whitelist + clamp).
	 *
	 * @param array $raw Raw input.
	 * @return array
	 */
	public static function sanitize_settings( $raw ) {
		$d   = self::default_settings();
		$raw = is_array( $raw ) ? $raw : array();

		$int = static function ( $key, $min, $max ) use ( $raw, $d ) {
			$v = isset( $raw[ $key ] ) && is_numeric( $raw[ $key ] ) ? (int) $raw[ $key ] : $d[ $key ];
			return max( $min, min( $max, $v ) );
		};
		$on  = static function ( $key ) use ( $raw ) {
			return ! empty( $raw[ $key ] ) && 'on' === $raw[ $key ] ? 'on' : 'off';
		};

		$lift = isset( $raw['min_lift'] ) && is_numeric( $raw['min_lift'] ) ? (float) $raw['min_lift'] : $d['min_lift'];

		$provider = isset( $raw['ai_provider'] ) ? sanitize_key( $raw['ai_provider'] ) : '';
		if ( '' !== $provider && ! array_key_exists( $provider, Upsell_Order_Bump_Reco_Ai::get_ai_connectors() ) ) {
			$provider = '';
		}

		$schedule = isset( $raw['schedule'] ) && in_array( $raw['schedule'], array( 'daily', 'weekly', 'manual' ), true ) ? $raw['schedule'] : $d['schedule'];

		return array(
			'enabled'           => $on( 'enabled' ),
			'lookback_days'     => $int( 'lookback_days', 7, 1825 ),
			'min_support'       => $int( 'min_support', 1, 1000 ),
			'min_confidence'    => $int( 'min_confidence', 0, 100 ),
			'min_lift'          => round( max( 0, min( 50, $lift ) ), 2 ),
			'seq_window_days'   => $int( 'seq_window_days', 0, 365 ),
			'seq_weight'        => $int( 'seq_weight', 0, 100 ),
			'match_threshold'   => $int( 'match_threshold', 1, 100 ),
			'exclude_purchased' => $on( 'exclude_purchased' ),
			'max_candidates'    => $int( 'max_candidates', 3, 100 ),
			'schedule'          => $schedule,
			'ai_enabled'        => $on( 'ai_enabled' ),
			'ai_provider'       => $provider,
			'ai_anchor_limit'   => $int( 'ai_anchor_limit', 1, 200 ),
			'ai_weight'         => $int( 'ai_weight', 0, 100 ),
			'ai_cold_start'     => $on( 'ai_cold_start' ),
		);
	}

	/**
	 * Whether the engine is switched on.
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		$settings = self::get_settings();
		return 'on' === $settings['enabled'];
	}

	/**
	 * Model metadata.
	 *
	 * @return array
	 */
	public static function get_model() {
		$meta = get_option( self::MODEL_OPTION, array() );
		return wp_parse_args(
			is_array( $meta ) ? $meta : array(),
			array(
				'status'     => 'idle',
				'trained_at' => 0,
				'started_at' => 0,
				'duration'   => 0,
				'source'     => '',
				'orders'     => 0,
				'products'   => 0,
				'pairs'      => 0,
				'ai_status'  => '',
				'ai_anchors' => 0,
				'ai_error'   => '',
				'error'      => '',
				'version'    => 0,
			)
		);
	}

	/**
	 * Merge and store model metadata.
	 *
	 * @param array $changes Keys to update.
	 * @return array Updated metadata.
	 */
	public static function update_model( $changes ) {
		$meta = array_merge( self::get_model(), $changes );
		update_option( self::MODEL_OPTION, $meta, false );
		return $meta;
	}

	/**
	 * Whether a trained model is available.
	 *
	 * @return bool
	 */
	public static function has_model() {
		$meta = self::get_model();
		return ! empty( $meta['trained_at'] ) && (int) $meta['pairs'] > 0;
	}

	/**
	 * Table DDL for dbDelta.
	 *
	 * @param bool $tmp Staging twins.
	 * @return string[]
	 */
	private static function schema( $tmp ) {
		global $wpdb;
		$collate = $wpdb->get_charset_collate();
		$pairs   = self::pairs_table( $tmp );
		$prods   = self::products_table( $tmp );

		return array(
			"CREATE TABLE {$pairs} (
  source_id bigint(20) unsigned NOT NULL,
  target_id bigint(20) unsigned NOT NULL,
  co_count int(10) unsigned NOT NULL DEFAULT 0,
  seq_count int(10) unsigned NOT NULL DEFAULT 0,
  ml_score float NOT NULL DEFAULT 0,
  lift float NOT NULL DEFAULT 0,
  ai_score float DEFAULT NULL,
  ai_reason varchar(191) NOT NULL DEFAULT '',
  score float NOT NULL DEFAULT 0,
  PRIMARY KEY  (source_id,target_id),
  KEY target_id (target_id),
  KEY source_score (source_id,score)
) {$collate};",
			"CREATE TABLE {$prods} (
  product_id bigint(20) unsigned NOT NULL,
  order_count int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY  (product_id)
) {$collate};",
		);
	}

	/**
	 * Create / upgrade tables and schedule training.
	 *
	 * @return void
	 */
	public static function install() {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( array_merge( self::schema( false ), self::schema( true ) ) );

		update_option( self::DB_VERSION_OPTION, self::DB_VERSION, false );

		if ( false === get_option( self::SETTINGS_OPTION, false ) ) {
			add_option( self::SETTINGS_OPTION, self::default_settings(), '', false );
		}

		self::reschedule();
	}

	/**
	 * Run install when the stored schema is outdated (updates without re-activation).
	 *
	 * @return void
	 */
	public static function maybe_upgrade() {
		if ( get_option( self::DB_VERSION_OPTION ) !== self::DB_VERSION ) {
			self::install();
		}
	}

	/**
	 * Schedule recurring training according to settings.
	 *
	 * @return void
	 */
	public static function reschedule() {
		self::unschedule();

		$settings = self::get_settings();
		if ( 'on' !== $settings['enabled'] || 'manual' === $settings['schedule'] ) {
			return;
		}

		$interval = 'weekly' === $settings['schedule'] ? WEEK_IN_SECONDS : DAY_IN_SECONDS;
		$first    = time() + HOUR_IN_SECONDS;

		if ( function_exists( 'as_schedule_recurring_action' ) ) {
			as_schedule_recurring_action( $first, $interval, self::TRAIN_HOOK, array(), self::AS_GROUP );
		} else {
			wp_schedule_event( $first, 'weekly' === $settings['schedule'] ? 'weekly' : 'daily', self::TRAIN_HOOK );
		}
	}

	/**
	 * Queue a single training run as soon as possible.
	 *
	 * @return void
	 */
	public static function queue_training_now() {
		if ( function_exists( 'as_enqueue_async_action' ) ) {
			as_enqueue_async_action( self::TRAIN_HOOK, array( 'manual' => 1 ), self::AS_GROUP );
		} else {
			wp_schedule_single_event( time(), self::TRAIN_HOOK, array( array( 'manual' => 1 ) ) );
			spawn_cron();
		}
	}

	/**
	 * Remove all scheduled training.
	 *
	 * @return void
	 */
	public static function unschedule() {
		if ( function_exists( 'as_unschedule_all_actions' ) ) {
			as_unschedule_all_actions( self::TRAIN_HOOK, array(), self::AS_GROUP );
		}
		wp_clear_scheduled_hook( self::TRAIN_HOOK );
	}

	/**
	 * Empty both live tables and reset metadata.
	 *
	 * @return void
	 */
	public static function delete_model() {
		global $wpdb;
		$wpdb->query( 'TRUNCATE TABLE ' . self::pairs_table() ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery
		$wpdb->query( 'TRUNCATE TABLE ' . self::products_table() ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery
		$meta = self::get_model();
		update_option(
			self::MODEL_OPTION,
			array(
				'status'  => 'idle',
				'version' => (int) $meta['version'] + 1,
			),
			false
		);
	}

	/**
	 * Drop everything (uninstall).
	 *
	 * @return void
	 */
	public static function uninstall() {
		global $wpdb;

		self::unschedule();

		foreach ( array( self::pairs_table(), self::pairs_table( true ), self::products_table(), self::products_table( true ) ) as $table ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
		}

		delete_option( self::DB_VERSION_OPTION );
		delete_option( self::SETTINGS_OPTION );
		delete_option( self::MODEL_OPTION );
		delete_transient( 'wps_ubo_reco_training_lock' );

		if ( function_exists( 'delete_metadata' ) ) {
			delete_metadata( 'post', 0, self::ORDER_META, '', true );
		}
		$hpos_meta = $wpdb->prefix . 'wc_orders_meta';
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $hpos_meta ) ) === $hpos_meta ) { // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->delete( $hpos_meta, array( 'meta_key' => self::ORDER_META ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.SlowDBQuery.slow_query_meta_key
		}
	}
}
