<?php
/**
 * Native cart abandonment upsell recovery - scheduled processing and offers.
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
 * Detection, email sequence, offer selection and coupons.
 *
 * @since 3.1.8
 */
class Upsell_Order_Bump_Acr_Engine {

	/**
	 * Rows handled per run.
	 *
	 * @var int
	 */
	const BATCH = 50;

	/**
	 * Run lock transient.
	 *
	 * @var string
	 */
	const LOCK = 'wps_ubo_acr_lock';

	/**
	 * Order statuses that count as a purchase.
	 *
	 * @return string[]
	 */
	public static function paid_statuses() {
		return (array) apply_filters( 'wps_ubo_acr_paid_statuses', array( 'wc-processing', 'wc-completed', 'wc-on-hold' ) );
	}

	/**
	 * Cron callback.
	 *
	 * @return array Counters (for tests / logs).
	 */
	public static function process() {
		$counts = array(
			'abandoned' => 0,
			'emails'    => 0,
			'recovered' => 0,
			'lost'      => 0,
			'purged'    => 0,
		);
		if ( ! Upsell_Order_Bump_Acr_Db::is_enabled() || get_transient( self::LOCK ) ) {
			return $counts;
		}
		set_transient( self::LOCK, 1, 4 * MINUTE_IN_SECONDS );

		try {
			$counts['abandoned'] = self::detect();
			$sent                = self::send_due();
			$counts['emails']    = $sent['emails'];
			$counts['recovered'] = $sent['recovered'];
			$counts['lost']      = self::mark_lost();
			$counts['purged']    = self::purge();
		} finally {
			delete_transient( self::LOCK );
		}
		return $counts;
	}

	/**
	 * Active carts idle longer than the timeout become abandoned.
	 *
	 * @return int
	 */
	public static function detect() {
		global $wpdb;
		$settings = Upsell_Order_Bump_Acr_Db::get_settings();
		$table    = Upsell_Order_Bump_Acr_Db::table();
		$cutoff   = Upsell_Order_Bump_Acr_Db::now( - (int) $settings['abandon_minutes'] * MINUTE_IN_SECONDS );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM %i WHERE status = 'active' AND email <> '' AND updated_at <= %s ORDER BY updated_at ASC LIMIT %d", $table, $cutoff, self::BATCH ) );

		$first = Upsell_Order_Bump_Acr_Db::next_step_index( 0 );
		foreach ( $ids as $id ) {
			$row  = Upsell_Order_Bump_Acr_Db::get_by( 'id', $id );
			$next = null;
			// Already-sent steps (cart re-activated and abandoned again) continue the sequence.
			$step_index = Upsell_Order_Bump_Acr_Db::next_step_index( (int) $row['emails_sent'] );
			if ( $step_index >= 0 && $first >= 0 ) {
				$step = Upsell_Order_Bump_Acr_Db::get_settings()['steps'][ $step_index ];
				$next = Upsell_Order_Bump_Acr_Db::now( Upsell_Order_Bump_Acr_Db::step_seconds( $step ) );
			}
			Upsell_Order_Bump_Acr_Db::update(
				$id,
				array(
					'status'        => 'abandoned',
					'abandoned_at'  => Upsell_Order_Bump_Acr_Db::now(),
					'next_email_at' => $next,
				)
			);
			/**
			 * Fires when a cart becomes abandoned.
			 *
			 * @since 3.1.8
			 * @param array $row Cart row.
			 */
			do_action( 'wps_ubo_acr_cart_abandoned', Upsell_Order_Bump_Acr_Db::get_by( 'id', $id ) );
		}
		return count( $ids );
	}

	/**
	 * Whether the email placed an order since a GMT datetime.
	 *
	 * @param string $email Email.
	 * @param string $since GMT datetime.
	 * @return int Order id or 0.
	 */
	public static function order_since( $email, $since ) {
		$ids = wc_get_orders(
			array(
				'billing_email' => $email,
				'status'        => self::paid_statuses(),
				'date_created'  => '>=' . strtotime( $since . ' UTC' ),
				'limit'         => 1,
				'return'        => 'ids',
			)
		);
		return $ids ? (int) $ids[0] : 0;
	}

	/**
	 * Send emails that are due.
	 *
	 * @return array { emails, recovered }
	 */
	public static function send_due() {
		global $wpdb;
		$table = Upsell_Order_Bump_Acr_Db::table();
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM %i WHERE status = 'abandoned' AND next_email_at IS NOT NULL AND next_email_at <= %s ORDER BY next_email_at ASC LIMIT %d", $table, Upsell_Order_Bump_Acr_Db::now(), self::BATCH ) );

		$out = array(
			'emails'    => 0,
			'recovered' => 0,
		);

		// The external abandoned-cart plugin emails these customers too: pause our sequence
		// ( stop the due steps, so no backlog goes out later ) instead of sending a second reminder.
		if ( self::external_emails_active() ) {
			foreach ( $ids as $id ) {
				Upsell_Order_Bump_Acr_Db::update( (int) $id, array( 'next_email_at' => null ) );
			}
			return $out;
		}

		foreach ( $ids as $id ) {
			$result = self::send_next( (int) $id );
			if ( 'sent' === $result ) {
				++$out['emails'];
			} elseif ( 'recovered' === $result ) {
				++$out['recovered'];
			}
		}
		return $out;
	}

	/**
	 * Whether the CartFlows "WooCommerce Cart Abandonment Recovery" plugin is active.
	 *
	 * @return bool
	 */
	public static function external_plugin_active() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		return is_plugin_active( 'woo-cart-abandonment-recovery/woo-cart-abandonment-recovery.php' );
	}

	/**
	 * Whether automatic recovery emails are left to the external plugin.
	 *
	 * @return bool
	 */
	public static function external_emails_active() {
		/**
		 * Filters whether native recovery emails pause while the external abandoned-cart
		 * plugin is active. Return false to send from both.
		 *
		 * @since 3.1.8
		 * @param bool $pause True when the external plugin is active.
		 */
		return (bool) apply_filters( 'wps_ubo_acr_pause_for_external_plugin', self::external_plugin_active() );
	}

	/**
	 * Send the next step for a cart (also used by "Send now" in admin).
	 *
	 * @param int $id Cart id.
	 * @return string sent|recovered|unsubscribed|done|failed
	 */
	public static function send_next( $id ) {
		$row = Upsell_Order_Bump_Acr_Db::get_by( 'id', $id );
		if ( ! $row || 'abandoned' !== $row['status'] || '' === $row['email'] ) {
			return 'failed';
		}

		// Bought anyway: recovered without the link. Search from when the cart was first saved:
		// abandoned_at is set 30+ minutes after the last activity, so orders in that gap were missed.
		$order_id = self::order_since( $row['email'], ! empty( $row['created_at'] ) ? $row['created_at'] : ( $row['abandoned_at'] ? $row['abandoned_at'] : $row['updated_at'] ) );
		if ( $order_id ) {
			self::mark_recovered( $row, wc_get_order( $order_id ), 'direct' );
			return 'recovered';
		}

		if ( Upsell_Order_Bump_Acr_Db::is_unsubscribed( $row['email'] ) ) {
			Upsell_Order_Bump_Acr_Db::update( $id, array( 'next_email_at' => null ) );
			return 'unsubscribed';
		}

		$settings = Upsell_Order_Bump_Acr_Db::get_settings();
		$index    = Upsell_Order_Bump_Acr_Db::next_step_index( (int) $row['emails_sent'] );
		if ( $index < 0 ) {
			Upsell_Order_Bump_Acr_Db::update( $id, array( 'next_email_at' => null ) );
			return 'done';
		}
		$step = $settings['steps'][ $index ];

		if ( 'on' === $step['include_offer'] && ! $row['offer_product'] ) {
			$offer = self::select_offer( $row, '' !== $step['discount'] ? (int) $step['discount'] : null );
			if ( $offer ) {
				Upsell_Order_Bump_Acr_Db::update(
					$id,
					array(
						'offer_product'  => $offer['product_id'],
						'offer_discount' => $offer['discount'],
						'offer_source'   => $offer['source'],
					)
				);
			}
		} elseif ( 'on' === $step['include_offer'] && '' !== $step['discount'] && (int) $step['discount'] > (int) $row['offer_discount'] ) {
			// A later step may sweeten the same offer.
			Upsell_Order_Bump_Acr_Db::update( $id, array( 'offer_discount' => (int) $step['discount'] ) );
		}

		if ( (int) $step['coupon'] > 0 ) {
			self::ensure_coupon( Upsell_Order_Bump_Acr_Db::get_by( 'id', $id ), (int) $step['coupon'] );
		}

		$row  = Upsell_Order_Bump_Acr_Db::get_by( 'id', $id );
		$sent = Upsell_Order_Bump_Acr_Mailer::send_step( $row, $index );
		if ( ! $sent ) {
			// Retry on a later run.
			Upsell_Order_Bump_Acr_Db::update( $id, array( 'next_email_at' => Upsell_Order_Bump_Acr_Db::now( 30 * MINUTE_IN_SECONDS ) ) );
			return 'failed';
		}

		$next_index = Upsell_Order_Bump_Acr_Db::next_step_index( $index + 1 );
		$next_at    = null;
		if ( $next_index >= 0 ) {
			// Delays are measured from abandonment.
			$base    = strtotime( $row['abandoned_at'] . ' UTC' );
			$due     = $base + Upsell_Order_Bump_Acr_Db::step_seconds( $settings['steps'][ $next_index ] );
			$next_at = gmdate( 'Y-m-d H:i:s', max( $due, time() + 15 * MINUTE_IN_SECONDS ) );
		}
		Upsell_Order_Bump_Acr_Db::update(
			$id,
			array(
				'emails_sent'   => $index + 1,
				'last_email_at' => Upsell_Order_Bump_Acr_Db::now(),
				'next_email_at' => $next_at,
			)
		);

		/**
		 * Fires after a recovery email was sent.
		 *
		 * @since 3.1.8
		 * @param array $row   Cart row.
		 * @param int   $index Step index.
		 */
		do_action( 'wps_ubo_acr_email_sent', $row, $index );
		return 'sent';
	}

	/**
	 * Abandoned carts whose sequence ended long enough ago become lost.
	 *
	 * @return int
	 */
	public static function mark_lost() {
		global $wpdb;
		$settings = Upsell_Order_Bump_Acr_Db::get_settings();
		$table    = Upsell_Order_Bump_Acr_Db::table();
		$cutoff   = Upsell_Order_Bump_Acr_Db::now( - (int) $settings['lost_after_days'] * DAY_IN_SECONDS );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		return (int) $wpdb->query( $wpdb->prepare( "UPDATE %i SET status = 'lost' WHERE status = 'abandoned' AND next_email_at IS NULL AND COALESCE(last_email_at, abandoned_at) <= %s", $table, $cutoff ) );
	}

	/**
	 * Delete rows past retention.
	 *
	 * @return int
	 */
	public static function purge() {
		global $wpdb;
		$settings = Upsell_Order_Bump_Acr_Db::get_settings();
		$table    = Upsell_Order_Bump_Acr_Db::table();
		$cutoff   = Upsell_Order_Bump_Acr_Db::now( - (int) $settings['retention_days'] * DAY_IN_SECONDS );
		// Unsubscribed rows are kept (they suppress future emails).
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$deleted = (int) $wpdb->query( $wpdb->prepare( "DELETE FROM %i WHERE status <> 'unsubscribed' AND updated_at <= %s LIMIT 1000", $table, $cutoff ) );
		self::delete_coupons( true );
		return $deleted;
	}

	/**
	 * Delete generated recovery coupons ( `acr-*`, tagged with `_wps_ubo_acr_cart_id` ).
	 *
	 * @param bool $expired_only Only coupons that expired more than a day ago ( purge );
	 *                           false deletes all of them ( uninstall ).
	 * @return int Deleted.
	 */
	public static function delete_coupons( $expired_only = true ) {
		$args = array(
			'post_type'      => 'shop_coupon',
			'post_status'    => 'any',
			'posts_per_page' => 200,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => '_wps_ubo_acr_cart_id',
					'compare' => 'EXISTS',
				),
			),
		);
		if ( $expired_only ) {
			$args['meta_query'][] = array(
				'key'     => 'date_expires',
				'value'   => time() - DAY_IN_SECONDS,
				'compare' => '<',
				'type'    => 'NUMERIC',
			);
		}
		$n = 0;
		foreach ( get_posts( $args ) as $coupon_id ) {
			if ( wp_delete_post( $coupon_id, true ) ) {
				++$n;
			}
		}
		return $n;
	}

	/**
	 * Mark a cart recovered by an order.
	 *
	 * @param array    $row   Cart row.
	 * @param WC_Order $order Order.
	 * @param string   $via   link|direct.
	 * @return void
	 */
	public static function mark_recovered( $row, $order, $via ) {
		if ( ! $order ) {
			return;
		}

		// One order is credited to one saved cart. Another open cart of the same customer that
		// this order also satisfies is closed without counting the order ( and revenue ) twice.
		$linked = (int) $order->get_meta( '_wps_ubo_acr_recovered' );
		if ( $linked && $linked !== (int) $row['id'] ) {
			Upsell_Order_Bump_Acr_Db::update(
				$row['id'],
				array(
					'status'          => 'recovered',
					'order_id'        => $order->get_id(),
					'recovered_total' => 0,
					'recovered_via'   => 'dup',
					'recovered_at'    => Upsell_Order_Bump_Acr_Db::now(),
					'next_email_at'   => null,
				)
			);
			return;
		}

		$accepted = 0;
		foreach ( $order->get_items() as $item ) {
			if ( ! $item->get_meta( '_wps_ubo_acr_offer' ) ) {
				continue;
			}
			$item_cart = (int) $item->get_meta( '_wps_ubo_acr_cart_id' );
			// Lines saved before the cart id was stored count as this cart's offer.
			if ( ! $item_cart || $item_cart === (int) $row['id'] ) {
				$accepted = 1;
			}
		}
		Upsell_Order_Bump_Acr_Db::update(
			$row['id'],
			array(
				'status'          => 'recovered',
				'order_id'        => $order->get_id(),
				'recovered_total' => (float) $order->get_total(),
				'recovered_via'   => $via,
				'recovered_at'    => Upsell_Order_Bump_Acr_Db::now(),
				'next_email_at'   => null,
				'offer_accepted'  => $accepted,
			)
		);
		$order->update_meta_data( '_wps_ubo_acr_recovered', (int) $row['id'] );
		$order->save_meta_data();

		/**
		 * Fires when an abandoned cart is recovered.
		 *
		 * @since 3.1.8
		 * @param array    $row   Cart row.
		 * @param WC_Order $order Order.
		 */
		do_action( 'wps_ubo_acr_cart_recovered', $row, $order );
	}

	/*
	|--------------------------------------------------------------------------
	| Offer selection
	|--------------------------------------------------------------------------
	*/

	/**
	 * Whether a product can be offered.
	 *
	 * @param int   $product_id Product.
	 * @param int[] $in_cart    Product and variation ids in the cart.
	 * @return bool
	 */
	private static function offerable( $product_id, $in_cart ) {
		$product = $product_id ? wc_get_product( $product_id ) : null;
		if ( ! $product || 'publish' !== get_post_status( $product->get_parent_id() ? $product->get_parent_id() : $product->get_id() ) ) {
			return false;
		}
		if ( ! $product->is_purchasable() || ! $product->is_in_stock() || $product->is_type( array( 'grouped', 'external' ) ) ) {
			return false;
		}
		if ( ! self::is_single_line_product( $product ) ) {
			return false;
		}
		return ! in_array( (int) $product_id, $in_cart, true ) && ! in_array( (int) $product->get_parent_id(), $in_cart, true );
	}

	/**
	 * Whether a product can be added to the cart as one line without the customer choosing
	 * options: not a variable parent, and not a variation with an "Any …" attribute.
	 *
	 * @param WC_Product $product Product.
	 * @return bool
	 */
	public static function is_single_line_product( $product ) {
		if ( ! $product || $product->is_type( array( 'variable', 'variable-subscription' ) ) ) {
			return false;
		}
		if ( $product->is_type( 'variation' ) ) {
			foreach ( (array) $product->get_variation_attributes() as $value ) {
				if ( '' === (string) $value ) {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Product, variation and parent ids in a stored cart.
	 *
	 * @param array $cart Stored cart.
	 * @return int[]
	 */
	private static function cart_ids( $cart ) {
		$ids = array();
		foreach ( (array) $cart as $item ) {
			$ids[] = (int) $item['product_id'];
			if ( ! empty( $item['variation_id'] ) ) {
				$ids[] = (int) $item['variation_id'];
			}
		}
		return array_values( array_unique( array_filter( $ids ) ) );
	}

	/**
	 * Choose the upsell offer for an abandoned cart.
	 *
	 * @param array    $row      Cart row.
	 * @param int|null $discount Step discount override.
	 * @return array|null { product_id, discount, source }
	 */
	public static function select_offer( $row, $discount = null ) {
		$settings = Upsell_Order_Bump_Acr_Db::get_settings();
		$in_cart  = self::cart_ids( $row['cart'] );
		$default  = null !== $discount ? (int) $discount : (int) $settings['offer_discount'];
		$order    = 'auto' === $settings['offer_source'] ? array( 'smart', 'bump', 'fixed' ) : array( $settings['offer_source'] );
		$offer    = null;

		foreach ( $order as $source ) {
			if ( 'smart' === $source && class_exists( 'Upsell_Order_Bump_Reco_Engine' ) && Upsell_Order_Bump_Reco_Db::is_enabled() && Upsell_Order_Bump_Reco_Db::has_model() ) {
				$parents = array();
				foreach ( (array) $row['cart'] as $item ) {
					$parents[] = (int) $item['product_id'];
				}
				$recs = Upsell_Order_Bump_Reco_Engine::recommend(
					$parents,
					array(
						'exclude'     => $in_cart,
						'limit'       => 1,
						'context'     => 'recovery',
						'customer_id' => (int) $row['user_id'],
					)
				);
				if ( $recs && self::offerable( $recs[0]['id'], $in_cart ) ) {
					$offer = array(
						'product_id' => (int) $recs[0]['id'],
						'discount'   => $default,
						'source'     => 'smart',
					);
				}
			} elseif ( 'bump' === $source ) {
				$offer = self::offer_from_bumps( $row['cart'], $in_cart, $discount, $default );
			} elseif ( 'fixed' === $source && self::offerable( (int) $settings['offer_product'], $in_cart ) ) {
				$offer = array(
					'product_id' => (int) $settings['offer_product'],
					'discount'   => $default,
					'source'     => 'fixed',
				);
			}
			if ( $offer ) {
				break;
			}
		}

		/**
		 * Filters the upsell offer chosen for an abandoned cart.
		 *
		 * @since 3.1.8
		 * @param array|null $offer { product_id, discount, source } or null.
		 * @param array      $row   Cart row.
		 */
		$offer = apply_filters( 'wps_ubo_acr_offer', $offer, $row );
		if ( ! is_array( $offer ) || empty( $offer['product_id'] ) ) {
			return null;
		}
		$offer['discount'] = max( 0, min( 90, (int) $offer['discount'] ) );
		return $offer;
	}

	/**
	 * First live order bump whose targets match the stored cart.
	 *
	 * @param array    $cart     Stored cart.
	 * @param int[]    $in_cart  Ids in cart.
	 * @param int|null $override Step discount override.
	 * @param int      $default  Default discount.
	 * @return array|null
	 */
	private static function offer_from_bumps( $cart, $in_cart, $override, $default ) {
		$bumps = get_option( 'wps_ubo_bump_list', array() );
		if ( ! is_array( $bumps ) ) {
			return null;
		}
		$cats = array();
		foreach ( (array) $cart as $item ) {
			$terms = wp_get_post_terms( (int) $item['product_id'], 'product_cat', array( 'fields' => 'ids' ) );
			if ( ! is_wp_error( $terms ) ) {
				$cats = array_merge( $cats, array_map( 'intval', $terms ) );
			}
		}

		foreach ( $bumps as $bump ) {
			if ( ! is_array( $bump ) || 'yes' !== ( isset( $bump['wps_upsell_bump_status'] ) ? $bump['wps_upsell_bump_status'] : '' ) ) {
				continue;
			}
			// Bumps made for one customer by the old abandoned-cart integration are not general offers.
			if ( isset( $bump['wps_is_abandoned_bump'] ) && 'yes' === $bump['wps_is_abandoned_bump'] ) {
				continue;
			}
			$product = isset( $bump['wps_upsell_bump_products_in_offer'] ) ? (int) $bump['wps_upsell_bump_products_in_offer'] : 0;
			if ( ! self::offerable( $product, $in_cart ) ) {
				continue;
			}
			$targets     = array_map( 'intval', (array) ( isset( $bump['wps_upsell_bump_target_ids'] ) ? $bump['wps_upsell_bump_target_ids'] : array() ) );
			$target_cats = array_map( 'intval', (array) ( isset( $bump['wps_upsell_bump_target_categories'] ) ? $bump['wps_upsell_bump_target_categories'] : array() ) );
			$global      = isset( $bump['wps_ubo_offer_global_funnel'] ) && 'yes' === $bump['wps_ubo_offer_global_funnel'];

			if ( $global || array_intersect( $targets, $in_cart ) || array_intersect( $target_cats, $cats ) ) {
				$discount = $default;
				if ( null === $override && isset( $bump['wps_upsell_offer_price_type'] ) && '%' === $bump['wps_upsell_offer_price_type'] && is_numeric( $bump['wps_upsell_bump_offer_discount_price'] ) ) {
					$discount = (int) $bump['wps_upsell_bump_offer_discount_price'];
				}
				return array(
					'product_id' => $product,
					'discount'   => $discount,
					'source'     => 'bump',
				);
			}
		}
		return null;
	}

	/**
	 * Offer price for a product and discount.
	 *
	 * @param WC_Product $product  Product.
	 * @param int        $discount Percent.
	 * @return float
	 */
	public static function offer_price( $product, $discount ) {
		// The current price ( a sale price when on sale ): taking the discount off the regular
		// price could charge more than the customer would pay without the "offer". Read from a
		// fresh product: a cart line's product already carries the offer price from the previous
		// totals calculation, and discounting that again would compound on every recalculation.
		$fresh = $product->get_id() ? wc_get_product( $product->get_id() ) : null;
		$fresh = $fresh ? $fresh : $product;
		$base  = (float) $fresh->get_price();
		if ( $base <= 0 ) {
			$base = (float) $fresh->get_regular_price();
		}
		$price = round( $base * ( 100 - max( 0, min( 90, (int) $discount ) ) ) / 100, wc_get_price_decimals() );
		return min( $base, $price );
	}

	/*
	|--------------------------------------------------------------------------
	| Coupons
	|--------------------------------------------------------------------------
	*/

	/**
	 * Create (or raise) the cart's one-time coupon.
	 *
	 * @param array $row     Cart row.
	 * @param int   $percent Percent off.
	 * @return string Coupon code.
	 */
	public static function ensure_coupon( $row, $percent ) {
		$settings = Upsell_Order_Bump_Acr_Db::get_settings();
		$coupon   = null;
		if ( $row['coupon_code'] ) {
			$existing = new WC_Coupon( $row['coupon_code'] );
			if ( $existing->get_id() ) {
				$coupon = $existing;
			}
		}
		if ( ! $coupon ) {
			$coupon = new WC_Coupon();
			$coupon->set_code( 'acr-' . strtolower( wp_generate_password( 8, false ) ) );
			$coupon->set_discount_type( 'percent' );
			$coupon->set_individual_use( true );
			$coupon->set_usage_limit( 1 );
			$coupon->set_email_restrictions( array( $row['email'] ) );
			$coupon->set_description( __( 'Cart recovery coupon (generated)', 'upsell-order-bump-offer-for-woocommerce' ) );
			$coupon->update_meta_data( '_wps_ubo_acr_cart_id', (int) $row['id'] );
		}
		if ( (float) $coupon->get_amount() < $percent ) {
			$coupon->set_amount( $percent );
		}
		$coupon->set_date_expires( time() + (int) $settings['coupon_expiry_days'] * DAY_IN_SECONDS );
		$coupon->save();

		if ( $coupon->get_code() !== $row['coupon_code'] ) {
			Upsell_Order_Bump_Acr_Db::update( $row['id'], array( 'coupon_code' => $coupon->get_code() ) );
		}
		return $coupon->get_code();
	}
}
