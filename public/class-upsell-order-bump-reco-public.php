<?php
/**
 * Smart Offer Recommendations - checkout and upsell funnel integration.
 *
 * - Smart / hybrid targeting for order bumps and upsell funnels.
 * - AI-picked offer product (fallback: the admin-selected product).
 * - Product substitution through option read filters, so every existing
 *   read of the bump / funnel settings sees the same product, plus write
 *   guards so substituted values are never persisted.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      3.1.8
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/public
 * @author     WP Swings <webmaster@wpswings.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Front-end integration of the recommendation engine.
 *
 * @since 3.1.8
 */
class Upsell_Order_Bump_Reco_Public {

	/**
	 * WC session key for bump product decisions.
	 *
	 * @var string
	 */
	const SESSION_KEY = 'wps_ubo_reco_bump';

	/**
	 * Marker key holding the admin values of a substituted offer.
	 *
	 * @var string
	 */
	const MARKER = '_wps_ubo_reco_orig';

	/**
	 * Order id whose upsell decisions apply to this request.
	 *
	 * @var int
	 */
	private static $order_context = 0;

	/**
	 * Cached upsell decisions per order id.
	 *
	 * @var array
	 */
	private static $order_decisions = array();

	/**
	 * Skip option filters (reading raw values).
	 *
	 * @var bool
	 */
	private static $bypass = false;

	/**
	 * Engine is on and a model is published.
	 *
	 * @return bool
	 */
	public static function active() {
		static $active = null;
		if ( null === $active ) {
			$active = Upsell_Order_Bump_Reco_Db::is_enabled() && Upsell_Order_Bump_Reco_Db::has_model();
		}
		return $active;
	}

	/**
	 * Configured targeting mode of a bump / funnel.
	 *
	 * @param array $offer Bump or funnel array.
	 * @return string static|smart|hybrid
	 */
	public static function targeting_mode( $offer ) {
		$mode = is_array( $offer ) && ! empty( $offer['wps_ubo_targeting_mode'] ) ? $offer['wps_ubo_targeting_mode'] : 'static';
		return in_array( $mode, Upsell_Order_Bump_Reco_Db::TARGETING_MODES, true ) ? $mode : 'static';
	}

	/**
	 * Mode actually applied: smart/hybrid fall back to rules while no model is available.
	 *
	 * @param array $offer Bump or funnel array.
	 * @return string static|smart|hybrid
	 */
	public static function effective_mode( $offer ) {
		return self::active() ? self::targeting_mode( $offer ) : 'static';
	}

	/**
	 * Configured offer product source.
	 *
	 * @param array $offer Bump or funnel array.
	 * @return string static|ai
	 */
	public static function product_source( $offer ) {
		return is_array( $offer ) && ! empty( $offer['wps_ubo_offer_product_source'] ) && 'ai' === $offer['wps_ubo_offer_product_source'] ? 'ai' : 'static';
	}

	/**
	 * Score needed for a smart match (0..1).
	 *
	 * @return float
	 */
	private static function threshold() {
		$settings = Upsell_Order_Bump_Reco_Db::get_settings();
		return (int) $settings['match_threshold'] / 100;
	}

	/*
	|--------------------------------------------------------------------------
	| Order bump
	|--------------------------------------------------------------------------
	*/

	/**
	 * Whether WC cart + session are usable in this request.
	 *
	 * @return bool
	 */
	private static function has_cart() {
		return function_exists( 'WC' ) && WC()->session && WC()->cart;
	}

	/**
	 * Parent product ids in the cart, excluding items added by order bumps.
	 *
	 * @return int[]
	 */
	public static function cart_context_ids() {
		if ( ! self::has_cart() ) {
			return array();
		}
		$ids = array();
		foreach ( WC()->cart->get_cart() as $item ) {
			if ( ! empty( $item['wps_ubo_offer_product'] ) ) {
				continue;
			}
			$ids[] = (int) $item['product_id'];
		}
		$ids = array_values( array_unique( array_filter( $ids ) ) );
		sort( $ids );
		return $ids;
	}

	/**
	 * Cart key of the first non-offer cart item for a parent product id.
	 *
	 * @param int $product_id Parent id.
	 * @return string
	 */
	private static function cart_key_for( $product_id ) {
		foreach ( WC()->cart->get_cart() as $key => $item ) {
			if ( empty( $item['wps_ubo_offer_product'] ) && (int) $item['product_id'] === (int) $product_id ) {
				return (string) $key;
			}
		}
		return '';
	}

	/**
	 * Whether this bump's offer is currently in the cart.
	 *
	 * @param int $bump_id Bump id.
	 * @return bool
	 */
	private static function bump_in_cart( $bump_id ) {
		foreach ( WC()->cart->get_cart() as $item ) {
			if ( ! empty( $item['wps_ubo_offer_product'] ) && isset( $item['wps_ubo_bump_id'] ) && (string) $item['wps_ubo_bump_id'] === (string) $bump_id ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Signature of the cart content + model version.
	 *
	 * @param int[] $ids Context ids.
	 * @return string
	 */
	private static function signature( $ids ) {
		$model = Upsell_Order_Bump_Reco_Db::get_model();
		return md5( implode( ',', $ids ) . '|' . (int) $model['version'] );
	}

	/**
	 * Substitute the offer product in a bump array (front-end only).
	 *
	 * @param array $bump       Bump array.
	 * @param int   $product_id Product to offer.
	 * @return array
	 */
	private static function apply_bump_product( $bump, $product_id ) {
		$original = isset( $bump['wps_upsell_bump_products_in_offer'] ) ? (string) $bump['wps_upsell_bump_products_in_offer'] : '';
		if ( ! $product_id || (string) $product_id === $original ) {
			return $bump;
		}
		if ( ! isset( $bump[ self::MARKER ] ) ) {
			$bump[ self::MARKER ] = array(
				'product'    => $original,
				'price_type' => isset( $bump['wps_upsell_offer_price_type'] ) ? $bump['wps_upsell_offer_price_type'] : '',
			);
		}
		$bump['wps_upsell_bump_products_in_offer'] = (string) $product_id;
		// A fixed price set for another product must not apply to the recommended one.
		if ( isset( $bump['wps_upsell_offer_price_type'] ) && 'fixed' === $bump['wps_upsell_offer_price_type'] ) {
			$bump['wps_upsell_offer_price_type'] = 'no_disc';
		}
		return $bump;
	}

	/**
	 * Resolve the AI product for a bump (called during bump matching).
	 *
	 * @param int|string $bump_id Bump id.
	 * @param array      $bump    Bump array.
	 * @return array Bump array with the resolved product.
	 */
	public static function prepare_bump( $bump_id, $bump ) {
		if ( 'ai' !== self::product_source( $bump ) || ! self::active() || ! self::has_cart() ) {
			return $bump;
		}

		$decisions = (array) WC()->session->get( self::SESSION_KEY, array() );
		$ids       = self::cart_context_ids();
		$sig       = self::signature( $ids );
		$current   = isset( $decisions[ $bump_id ] ) ? $decisions[ $bump_id ] : null;

		// Frozen once accepted, or still valid for this exact cart.
		if ( $current && ( self::bump_in_cart( $bump_id ) || $current['sig'] === $sig ) ) {
			return self::apply_bump_product( $bump, (int) $current['pid'] );
		}

		// Don't offer what another bump already offers: other AI decisions for this cart,
		// and the fixed products of other live bumps.
		$taken = array();
		foreach ( $decisions as $other_id => $decision ) {
			if ( (string) $other_id !== (string) $bump_id && isset( $decision['sig'] ) && $decision['sig'] === $sig ) {
				$taken[] = (int) $decision['pid'];
			}
		}
		self::$bypass = true;
		$all_bumps    = get_option( 'wps_ubo_bump_list', array() );
		self::$bypass = false;
		foreach ( (array) $all_bumps as $other_id => $other ) {
			if ( (string) $other_id !== (string) $bump_id && is_array( $other ) && ! empty( $other['wps_upsell_bump_products_in_offer'] )
				&& isset( $other['wps_upsell_bump_status'] ) && 'yes' === $other['wps_upsell_bump_status'] && 'ai' !== self::product_source( $other ) ) {
				$taken[] = Upsell_Order_Bump_Reco_Engine::parent_id( (int) $other['wps_upsell_bump_products_in_offer'] );
			}
		}

		$recs  = $ids ? Upsell_Order_Bump_Reco_Engine::recommend(
			$ids,
			array(
				'exclude'     => $taken,
				'limit'       => 1,
				'context'     => 'bump',
				'customer_id' => get_current_user_id(),
			)
		) : array();
		$pid   = $recs ? (int) $recs[0]['id'] : (int) ( isset( $bump['wps_upsell_bump_products_in_offer'] ) ? $bump['wps_upsell_bump_products_in_offer'] : 0 );
		$score = $recs ? (float) $recs[0]['score'] : null;

		/**
		 * Filters the product an AI-sourced order bump will offer.
		 *
		 * @since 3.1.8
		 * @param int        $pid     Product id (recommended or admin fallback).
		 * @param int|string $bump_id Bump id.
		 * @param array      $bump    Bump array.
		 */
		$pid = absint( apply_filters( 'wps_ubo_reco_bump_product', $pid, $bump_id, $bump ) );

		$decisions[ $bump_id ] = array(
			'pid'   => $pid,
			'sig'   => $sig,
			'score' => $score,
		);
		WC()->session->set( self::SESSION_KEY, $decisions );

		return self::apply_bump_product( $bump, $pid );
	}

	/**
	 * Smart targeting for a bump: returns the anchor cart key, or '' when no match.
	 *
	 * @param int|string $bump_id Bump id.
	 * @param array      $bump    Bump array (product already resolved).
	 * @return string
	 */
	public static function bump_smart_match( $bump_id, $bump ) {
		if ( ! self::active() || ! self::has_cart() || empty( $bump['wps_upsell_bump_products_in_offer'] ) ) {
			return '';
		}
		$ids = self::cart_context_ids();
		if ( empty( $ids ) ) {
			return '';
		}
		$result = Upsell_Order_Bump_Reco_Engine::score_for( (int) $bump['wps_upsell_bump_products_in_offer'], $ids );
		if ( $result['score'] < self::threshold() || ! $result['anchor'] ) {
			return '';
		}
		return self::cart_key_for( $result['anchor'] );
	}

	/**
	 * Offer product checks used by the core rule path, for the smart path.
	 *
	 * @param int|string $product_id   Offer product id.
	 * @param string     $skip_in_cart Global "skip if already in cart" (yes/no).
	 * @return bool
	 */
	public static function offer_product_is_available( $product_id, $skip_in_cart = 'yes' ) {
		$product = wc_get_product( $product_id );
		if ( empty( $product ) || 'publish' !== $product->get_status() || ! $product->is_in_stock() ) {
			return false;
		}
		return ! ( 'yes' === $skip_in_cart && wps_ubo_lite_already_in_cart( $product_id ) );
	}

	/**
	 * Re-validate a bump cached in the session after the cart changed.
	 *
	 * @param int|string $bump_id Bump id.
	 * @param array      $bump    Bump array (filtered).
	 * @return bool False to force bumps to be matched again.
	 */
	public static function session_still_valid( $bump_id, $bump ) {
		if ( ! self::active() || ! self::has_cart() || self::bump_in_cart( $bump_id ) ) {
			return true;
		}
		if ( 'ai' === self::product_source( $bump ) ) {
			$decisions = (array) WC()->session->get( self::SESSION_KEY, array() );
			if ( empty( $decisions[ $bump_id ] ) || $decisions[ $bump_id ]['sig'] !== self::signature( self::cart_context_ids() ) ) {
				return false;
			}
		}
		if ( 'smart' === self::effective_mode( $bump ) && '' === self::bump_smart_match( $bump_id, $bump ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Requests where substitution may apply: front end and front-end AJAX.
	 *
	 * @return bool
	 */
	private static function front_request() {
		return ( ! is_admin() || wp_doing_ajax() ) && ! ( defined( 'WP_CLI' ) && WP_CLI );
	}

	/**
	 * Filter: option_wps_ubo_bump_list.
	 *
	 * @param mixed $value Bump list.
	 * @return mixed
	 */
	public static function filter_bump_list( $value ) {
		if ( self::$bypass || ! is_array( $value ) || ! self::front_request() || ! self::has_cart() ) {
			return $value;
		}
		$decisions = (array) WC()->session->get( self::SESSION_KEY, array() );
		foreach ( $decisions as $bump_id => $decision ) {
			if ( isset( $value[ $bump_id ] ) && 'ai' === self::product_source( $value[ $bump_id ] ) && ! empty( $decision['pid'] ) ) {
				$value[ $bump_id ] = self::apply_bump_product( $value[ $bump_id ], (int) $decision['pid'] );
			}
		}
		return $value;
	}

	/**
	 * Filter: pre_update_option_wps_ubo_bump_list — never persist substitutions.
	 *
	 * @param mixed $value New value.
	 * @return mixed
	 */
	public static function guard_bump_list( $value ) {
		if ( ! is_array( $value ) ) {
			return $value;
		}
		foreach ( $value as $id => $bump ) {
			if ( is_array( $bump ) && isset( $bump[ self::MARKER ] ) ) {
				$value[ $id ]['wps_upsell_bump_products_in_offer'] = $bump[ self::MARKER ]['product'];
				$value[ $id ]['wps_upsell_offer_price_type']       = $bump[ self::MARKER ]['price_type'];
				unset( $value[ $id ][ self::MARKER ] );
			}
		}
		return $value;
	}

	/*
	|--------------------------------------------------------------------------
	| Upsell funnels
	|--------------------------------------------------------------------------
	*/

	/**
	 * Parent product ids of an order.
	 *
	 * @param WC_Order $order Order.
	 * @return int[]
	 */
	private static function order_context_ids( $order ) {
		$ids = array();
		foreach ( $order->get_items() as $item ) {
			$ids[] = (int) $item->get_product_id();
		}
		return array_values( array_unique( array_filter( $ids ) ) );
	}

	/**
	 * Raw (unfiltered) funnels.
	 *
	 * @return array
	 */
	private static function raw_funnels() {
		self::$bypass = true;
		$funnels      = get_option( 'wps_wocuf_funnels_list', array() );
		self::$bypass = false;
		return is_array( $funnels ) ? $funnels : array();
	}

	/**
	 * Decide AI products for every AI-sourced funnel of an order.
	 * Runs before funnels are loaded in wps_wocuf_initiate_upsell_orders().
	 *
	 * @param WC_Order $order Order.
	 * @return void
	 */
	public static function prepare_upsell_order( $order ) {
		if ( ! $order instanceof WC_Order || ! self::active() ) {
			return;
		}

		self::$order_context = (int) $order->get_id();
		$ids                 = self::order_context_ids( $order );
		$decisions           = array();

		foreach ( self::raw_funnels() as $funnel_id => $funnel ) {
			if ( 'ai' !== self::product_source( $funnel ) || empty( $funnel['wps_wocuf_products_in_offer'] ) || ! is_array( $funnel['wps_wocuf_products_in_offer'] ) ) {
				continue;
			}
			$offers = $funnel['wps_wocuf_products_in_offer'];
			$recs   = Upsell_Order_Bump_Reco_Engine::recommend(
				$ids,
				array(
					'limit'       => count( $offers ),
					'context'     => 'upsell',
					'customer_id' => (int) $order->get_customer_id(),
				)
			);
			$k      = 0;
			foreach ( array_keys( $offers ) as $offer_id ) {
				if ( empty( $recs[ $k ] ) ) {
					break;
				}
				$decisions[ $funnel_id ][ $offer_id ] = array(
					'pid'   => (int) $recs[ $k ]['id'],
					'score' => (float) $recs[ $k ]['score'],
				);
				++$k;
			}
		}

		self::$order_decisions[ self::$order_context ] = $decisions;
		if ( $decisions ) {
			$order->update_meta_data( Upsell_Order_Bump_Reco_Db::ORDER_META, $decisions );
			$order->save_meta_data();
		} elseif ( $order->get_meta( Upsell_Order_Bump_Reco_Db::ORDER_META ) ) {
			$order->delete_meta_data( Upsell_Order_Bump_Reco_Db::ORDER_META );
			$order->save_meta_data();
		}
	}

	/**
	 * Order whose decisions apply: the one being processed, or the live offer URL's order key.
	 *
	 * @return int
	 */
	private static function current_order_id() {
		if ( self::$order_context ) {
			return self::$order_context;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only lookup; the funnel handlers verify their own nonces.
		$key = isset( $_GET['ocuf_ok'] ) ? sanitize_text_field( wp_unslash( $_GET['ocuf_ok'] ) ) : '';
		if ( '' === $key || ! function_exists( 'wc_get_order_id_by_order_key' ) ) {
			return 0;
		}
		self::$order_context = (int) wc_get_order_id_by_order_key( $key );
		return self::$order_context;
	}

	/**
	 * Stored decisions for an order.
	 *
	 * @param int $order_id Order id.
	 * @return array funnel_id => offer_id => { pid, score }
	 */
	private static function decisions_for( $order_id ) {
		if ( ! isset( self::$order_decisions[ $order_id ] ) ) {
			$order = wc_get_order( $order_id );
			$meta  = $order ? $order->get_meta( Upsell_Order_Bump_Reco_Db::ORDER_META ) : array();
			self::$order_decisions[ $order_id ] = is_array( $meta ) ? $meta : array();
		}
		return self::$order_decisions[ $order_id ];
	}

	/**
	 * Smart targeting for a funnel against the order items.
	 *
	 * @param int|string $funnel_id Funnel id.
	 * @param array      $funnel    Funnel array (products already resolved).
	 * @param WC_Order   $order     Order.
	 * @return bool
	 */
	public static function funnel_matches_order( $funnel_id, $funnel, $order ) {
		if ( ! self::active() || ! $order instanceof WC_Order || empty( $funnel['wps_wocuf_products_in_offer'] ) || ! is_array( $funnel['wps_wocuf_products_in_offer'] ) ) {
			return false;
		}
		$first = reset( $funnel['wps_wocuf_products_in_offer'] );
		if ( is_array( $first ) ) {
			$first = reset( $first );
		}
		if ( empty( $first ) ) {
			return false;
		}
		$result = Upsell_Order_Bump_Reco_Engine::score_for( (int) $first, self::order_context_ids( $order ) );
		return $result['score'] >= self::threshold();
	}

	/**
	 * Filter: option_wps_wocuf_funnels_list.
	 *
	 * @param mixed $value Funnels.
	 * @return mixed
	 */
	public static function filter_funnels_list( $value ) {
		if ( self::$bypass || ! is_array( $value ) || ! self::front_request() ) {
			return $value;
		}
		$order_id = self::current_order_id();
		if ( ! $order_id ) {
			return $value;
		}
		foreach ( self::decisions_for( $order_id ) as $funnel_id => $offers ) {
			if ( ! isset( $value[ $funnel_id ] ) || 'ai' !== self::product_source( $value[ $funnel_id ] ) || ! is_array( $offers ) ) {
				continue;
			}
			$funnel = $value[ $funnel_id ];
			$marker = array(
				'products'  => isset( $funnel['wps_wocuf_products_in_offer'] ) ? $funnel['wps_wocuf_products_in_offer'] : array(),
				'discounts' => isset( $funnel['wps_wocuf_offer_discount_price'] ) ? $funnel['wps_wocuf_offer_discount_price'] : array(),
			);
			foreach ( $offers as $offer_id => $decision ) {
				if ( empty( $decision['pid'] ) || ! isset( $funnel['wps_wocuf_products_in_offer'][ $offer_id ] ) ) {
					continue;
				}
				if ( (string) $funnel['wps_wocuf_products_in_offer'][ $offer_id ] === (string) $decision['pid'] ) {
					continue;
				}
				$funnel['wps_wocuf_products_in_offer'][ $offer_id ] = (string) $decision['pid'];
				// A fixed price set for another product must not apply to the recommended one.
				$discount = isset( $funnel['wps_wocuf_offer_discount_price'][ $offer_id ] ) ? (string) $funnel['wps_wocuf_offer_discount_price'][ $offer_id ] : '';
				if ( '' !== $discount && false === strpos( $discount, '%' ) ) {
					$funnel['wps_wocuf_offer_discount_price'][ $offer_id ] = '0%';
				}
				$funnel[ self::MARKER ] = $marker;
			}
			$value[ $funnel_id ] = $funnel;
		}
		return $value;
	}

	/**
	 * Filter: pre_update_option_wps_wocuf_funnels_list — never persist substitutions.
	 *
	 * @param mixed $value New value.
	 * @return mixed
	 */
	public static function guard_funnels_list( $value ) {
		if ( ! is_array( $value ) ) {
			return $value;
		}
		foreach ( $value as $id => $funnel ) {
			if ( is_array( $funnel ) && isset( $funnel[ self::MARKER ] ) ) {
				$value[ $id ]['wps_wocuf_products_in_offer']    = $funnel[ self::MARKER ]['products'];
				$value[ $id ]['wps_wocuf_offer_discount_price'] = $funnel[ self::MARKER ]['discounts'];
				unset( $value[ $id ][ self::MARKER ] );
			}
		}
		return $value;
	}
}
