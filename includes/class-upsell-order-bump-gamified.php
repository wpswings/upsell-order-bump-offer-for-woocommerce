<?php
/**
 * Gamified bump offers - settings, prize draw, session state and price override.
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
 * Shared logic for spin-wheel and scratch-card bump formats.
 *
 * @since 3.1.8
 */
class Upsell_Order_Bump_Gamified {

	/**
	 * Settings option.
	 *
	 * @var string
	 */
	const SETTINGS_OPTION = 'wps_ubo_gamified_settings';

	/**
	 * Statistics option.
	 *
	 * @var string
	 */
	const STATS_OPTION = 'wps_ubo_gamified_stats';

	/**
	 * WC session key holding drawn prizes per bump.
	 *
	 * @var string
	 */
	const SESSION_KEY = 'wps_ubo_game';

	/**
	 * Marker key holding original bump pricing while a prize is applied.
	 *
	 * @var string
	 */
	const MARKER = '_wps_ubo_game_orig';

	/**
	 * Hidden order item meta key.
	 *
	 * @var string
	 */
	const ITEM_META = '_wps_ubo_game_prize';

	/**
	 * Cart item data key holding the prize applied to an accepted bump line.
	 *
	 * @var string
	 */
	const CART_KEY = 'wps_ubo_game_prize';

	/**
	 * Supported formats.
	 *
	 * @var string[]
	 */
	const FORMATS = array( 'standard', 'spin_wheel', 'scratch_card' );

	/**
	 * Prize rows limits.
	 *
	 * @var int
	 */
	const MIN_PRIZES = 2;
	const MAX_PRIZES = 12;

	/**
	 * Skip filters while reading raw options.
	 *
	 * @var bool
	 */
	private static $bypass = false;

	/**
	 * Default settings.
	 *
	 * @return array
	 */
	public static function default_settings() {
		return array(
			'enabled'                  => 'off',
			'formats'                  => array(),
			'prizes'                   => array(
				array( 'label' => __( '10% OFF', 'upsell-order-bump-offer-for-woocommerce' ), 'discount' => 10, 'weight' => 30, 'color' => '#2271b1' ),
				array( 'label' => __( '15% OFF', 'upsell-order-bump-offer-for-woocommerce' ), 'discount' => 15, 'weight' => 25, 'color' => '#ffb900' ),
				array( 'label' => __( '20% OFF', 'upsell-order-bump-offer-for-woocommerce' ), 'discount' => 20, 'weight' => 20, 'color' => '#00a32a' ),
				array( 'label' => __( '25% OFF', 'upsell-order-bump-offer-for-woocommerce' ), 'discount' => 25, 'weight' => 12, 'color' => '#d63638' ),
				array( 'label' => __( '30% OFF', 'upsell-order-bump-offer-for-woocommerce' ), 'discount' => 30, 'weight' => 8, 'color' => '#8c5ae0' ),
				array( 'label' => __( '50% OFF', 'upsell-order-bump-offer-for-woocommerce' ), 'discount' => 50, 'weight' => 5, 'color' => '#e65054' ),
			),
			'guarantee_offer_discount' => 'on',
			'max_discount'             => 50,
			'allow_skip'               => 'on',
			'headline_wheel'           => __( 'Spin the wheel to unlock your exclusive discount!', 'upsell-order-bump-offer-for-woocommerce' ),
			'headline_scratch'         => __( 'Scratch the card to reveal your exclusive discount!', 'upsell-order-bump-offer-for-woocommerce' ),
			'button_wheel'             => __( 'Spin now', 'upsell-order-bump-offer-for-woocommerce' ),
			'button_scratch'           => __( 'Reveal', 'upsell-order-bump-offer-for-woocommerce' ),
			'win_message'              => __( '🎉 You unlocked {discount} off {product}!', 'upsell-order-bump-offer-for-woocommerce' ),
			'color_primary'            => '#2271b1',
			'color_accent'             => '#ffb900',
			'color_text'               => '#1d2327',
		);
	}

	/**
	 * Saved settings merged with defaults.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$saved = get_option( self::SETTINGS_OPTION, array() );
		return wp_parse_args( is_array( $saved ) ? $saved : array(), self::default_settings() );
	}

	/**
	 * Sanitize and clamp a raw settings array.
	 *
	 * @param array $raw Raw input.
	 * @return array
	 */
	public static function sanitize_settings( $raw ) {
		$d   = self::default_settings();
		$raw = is_array( $raw ) ? $raw : array();
		$on  = static function ( $key ) use ( $raw ) {
			return ! empty( $raw[ $key ] ) && 'on' === $raw[ $key ] ? 'on' : 'off';
		};
		$txt = static function ( $key ) use ( $raw, $d ) {
			$value = isset( $raw[ $key ] ) ? sanitize_text_field( $raw[ $key ] ) : '';
			return '' !== $value ? mb_substr( $value, 0, 160 ) : $d[ $key ];
		};
		$hex = static function ( $key ) use ( $raw, $d ) {
			$value = isset( $raw[ $key ] ) ? sanitize_hex_color( $raw[ $key ] ) : '';
			return $value ? $value : $d[ $key ];
		};

		$max = isset( $raw['max_discount'] ) && is_numeric( $raw['max_discount'] ) ? (int) $raw['max_discount'] : $d['max_discount'];
		$max = max( 1, min( 100, $max ) );

		$formats = array();
		if ( ! empty( $raw['formats'] ) && is_array( $raw['formats'] ) ) {
			foreach ( $raw['formats'] as $bump_id => $format ) {
				$format = sanitize_key( $format );
				if ( in_array( $format, self::FORMATS, true ) && 'standard' !== $format ) {
					$formats[ absint( $bump_id ) ] = $format;
				}
			}
		}

		$prizes = array();
		if ( ! empty( $raw['prizes'] ) && is_array( $raw['prizes'] ) ) {
			foreach ( array_values( $raw['prizes'] ) as $row ) {
				if ( ! is_array( $row ) || ! isset( $row['label'] ) || '' === trim( (string) $row['label'] ) ) {
					continue;
				}
				$color    = isset( $row['color'] ) ? sanitize_hex_color( $row['color'] ) : '';
				$discount = max( 0, min( $max, (int) ( isset( $row['discount'] ) ? $row['discount'] : 0 ) ) );
				$prizes[] = array(
					// A % in the label always matches the (possibly capped) discount.
					'label'    => mb_substr( self::label_for_discount( sanitize_text_field( $row['label'] ), $discount ), 0, 24 ),
					'discount' => $discount,
					'weight'   => max( 0, min( 1000, (int) ( isset( $row['weight'] ) ? $row['weight'] : 0 ) ) ),
					'color'    => $color ? $color : '#2271b1',
				);
				if ( count( $prizes ) >= self::MAX_PRIZES ) {
					break;
				}
			}
		}
		$total_weight = array_sum( wp_list_pluck( $prizes, 'weight' ) );
		if ( count( $prizes ) < self::MIN_PRIZES || $total_weight <= 0 ) {
			$prizes = $d['prizes'];
		}

		return array(
			'enabled'                  => $on( 'enabled' ),
			'formats'                  => $formats,
			'prizes'                   => $prizes,
			'guarantee_offer_discount' => $on( 'guarantee_offer_discount' ),
			'max_discount'             => $max,
			'allow_skip'               => $on( 'allow_skip' ),
			'headline_wheel'           => $txt( 'headline_wheel' ),
			'headline_scratch'         => $txt( 'headline_scratch' ),
			'button_wheel'             => $txt( 'button_wheel' ),
			'button_scratch'           => $txt( 'button_scratch' ),
			'win_message'              => $txt( 'win_message' ),
			'color_primary'            => $hex( 'color_primary' ),
			'color_accent'             => $hex( 'color_accent' ),
			'color_text'               => $hex( 'color_text' ),
		);
	}

	/**
	 * Make a percentage written in a prize label match the discount actually given.
	 *
	 * Labels are free text, so "50% OFF" could otherwise be shown (and revealed) while
	 * a lower, capped discount is charged. Labels without a % are left as they are.
	 *
	 * @param string    $label    Prize label.
	 * @param int|float $discount Discount actually given (%).
	 * @return string
	 */
	public static function label_for_discount( $label, $discount ) {
		$label = (string) $label;
		if ( ! preg_match( '/(\d+(?:[.,]\d+)?)\s*%/u', $label, $m ) ) {
			return $label;
		}
		$written = (float) str_replace( ',', '.', $m[1] );
		if ( abs( $written - (float) $discount ) < 0.001 ) {
			return $label;
		}
		$number = ( floor( (float) $discount ) == (float) $discount ) ? (string) (int) $discount : (string) round( (float) $discount, 2 ); // phpcs:ignore Universal.Operators.StrictComparisons.LooseEqual
		return preg_replace( '/\d+(?:[.,]\d+)?(\s*%)/u', $number . '$1', $label, 1 );
	}

	/**
	 * Number of prize rows whose discount is above the maximum (they get capped on save).
	 *
	 * @param array $raw Raw settings input.
	 * @return int
	 */
	public static function count_capped_prizes( $raw ) {
		$max = isset( $raw['max_discount'] ) && is_numeric( $raw['max_discount'] ) ? max( 1, min( 100, (int) $raw['max_discount'] ) ) : self::default_settings()['max_discount'];
		$n   = 0;
		foreach ( (array) ( isset( $raw['prizes'] ) ? $raw['prizes'] : array() ) as $row ) {
			if ( is_array( $row ) && isset( $row['discount'] ) && (int) $row['discount'] > $max ) {
				++$n;
			}
		}
		return $n;
	}

	/**
	 * Whether the feature is switched on.
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		$settings = self::get_settings();
		return 'on' === $settings['enabled'];
	}

	/**
	 * Format configured for a bump (standard when the feature is off).
	 *
	 * @param int|string $bump_id Bump id.
	 * @return string
	 */
	public static function format_for( $bump_id ) {
		$settings = self::get_settings();
		if ( 'on' !== $settings['enabled'] ) {
			return 'standard';
		}
		$format = isset( $settings['formats'][ absint( $bump_id ) ] ) ? $settings['formats'][ absint( $bump_id ) ] : 'standard';
		return in_array( $format, self::FORMATS, true ) ? $format : 'standard';
	}

	/**
	 * Prize table for a bump.
	 *
	 * @param int|string $bump_id Bump id.
	 * @return array[]
	 */
	public static function prizes_for( $bump_id ) {
		$settings = self::get_settings();
		/**
		 * Filters the prize table of a gamified bump.
		 *
		 * @since 3.1.8
		 * @param array[]    $prizes  Rows { label, discount, weight, color }.
		 * @param int|string $bump_id Bump id.
		 */
		return array_values( (array) apply_filters( 'wps_ubo_game_prizes', $settings['prizes'], $bump_id ) );
	}

	/**
	 * Weighted random draw (server side).
	 *
	 * @param array[] $prizes Prize rows.
	 * @return int Index of the drawn row.
	 */
	public static function weighted_index( $prizes ) {
		$total = 0;
		foreach ( $prizes as $row ) {
			$total += max( 0, (int) $row['weight'] );
		}
		if ( $total <= 0 ) {
			return 0;
		}
		$r = wp_rand( 1, $total );
		foreach ( $prizes as $index => $row ) {
			$r -= max( 0, (int) $row['weight'] );
			if ( $r <= 0 ) {
				return (int) $index;
			}
		}
		return count( $prizes ) - 1;
	}

	/*
	|--------------------------------------------------------------------------
	| Session state
	|--------------------------------------------------------------------------
	*/

	/**
	 * Whether a WC session is available.
	 *
	 * @return bool
	 */
	private static function has_session() {
		return function_exists( 'WC' ) && WC()->session;
	}

	/**
	 * All prizes drawn in this session.
	 *
	 * @return array bump_id => prize state.
	 */
	public static function session_prizes() {
		if ( ! self::has_session() ) {
			return array();
		}
		$value = WC()->session->get( self::SESSION_KEY, array() );
		return is_array( $value ) ? $value : array();
	}

	/**
	 * Prize state for a bump, or null when not played yet.
	 *
	 * @param int|string $bump_id Bump id.
	 * @return array|null { index, label, discount, format, skipped, time }
	 */
	public static function get_prize( $bump_id ) {
		$all = self::session_prizes();
		return isset( $all[ absint( $bump_id ) ] ) ? $all[ absint( $bump_id ) ] : null;
	}

	/**
	 * Draw (or return the existing) prize for a bump.
	 *
	 * @param int|string $bump_id Bump id.
	 * @param bool       $skip    Customer skipped the game.
	 * @return array { prize: array, new: bool }
	 */
	public static function draw( $bump_id, $skip = false ) {
		$existing = self::get_prize( $bump_id );
		if ( $existing ) {
			return array(
				'prize' => $existing,
				'new'   => false,
			);
		}

		/*
		 * Two play requests at the same time must not both draw (the customer would see one
		 * prize and be charged the other). The second request waits for the lock and then
		 * reads the prize the first one saved.
		 */
		$lock   = self::acquire_lock( $bump_id );
		$stored = self::stored_prize( $bump_id );
		if ( $stored ) {
			$all                       = self::session_prizes();
			$all[ absint( $bump_id ) ] = $stored;
			WC()->session->set( self::SESSION_KEY, $all );
			self::release_lock( $lock );
			return array(
				'prize' => $stored,
				'new'   => false,
			);
		}

		$settings = self::get_settings();
		$prizes   = self::prizes_for( $bump_id );

		if ( $skip || empty( $prizes ) ) {
			// Skipped, or no prize table (e.g. emptied by a filter): standard offer.
			$prize = array(
				'index'    => -1,
				'label'    => '',
				'discount' => 0,
				'skipped'  => (bool) $skip,
			);
		} else {
			$index    = self::weighted_index( $prizes );
			$row      = $prizes[ $index ];
			$discount = max( 0, min( (int) $settings['max_discount'], (int) $row['discount'] ) );
			$prize    = array(
				'index'    => $index,
				'label'    => self::label_for_discount( (string) $row['label'], $discount ),
				'discount' => $discount,
				'skipped'  => false,
			);
		}
		$prize['format']   = self::format_for( $bump_id );
		$prize['time']     = time();
		$prize['accepted'] = false;

		/**
		 * Filters a freshly drawn prize.
		 *
		 * @since 3.1.8
		 * @param array      $prize   { index, label, discount, skipped, format, time }.
		 * @param int|string $bump_id Bump id.
		 */
		$prize             = (array) apply_filters( 'wps_ubo_game_draw', $prize, $bump_id );
		$prize['discount'] = max( 0, min( (int) $settings['max_discount'], (int) $prize['discount'] ) );

		$all                       = self::session_prizes();
		$all[ absint( $bump_id ) ] = $prize;
		WC()->session->set( self::SESSION_KEY, $all );
		// Persist now (not at shutdown) so a parallel request sees this draw.
		if ( method_exists( WC()->session, 'save_data' ) ) {
			WC()->session->save_data();
		}
		self::release_lock( $lock );

		if ( $skip ) {
			self::bump_stat( $bump_id, 'skips' );
		} elseif ( $prize['index'] >= 0 ) {
			self::bump_stat( $bump_id, 'plays', (int) $prize['index'], (string) $prize['label'] );
		}

		return array(
			'prize' => $prize,
			'new'   => true,
		);
	}

	/**
	 * Mark a prize as accepted (added to the cart). Returns true only the first time,
	 * so re-adding the offer is counted once per session.
	 *
	 * @param int|string $bump_id Bump id.
	 * @return bool
	 */
	public static function mark_accepted( $bump_id ) {
		$all = self::session_prizes();
		$id  = absint( $bump_id );
		if ( empty( $all[ $id ] ) || ! empty( $all[ $id ]['accepted'] ) ) {
			return false;
		}
		$all[ $id ]['accepted'] = true;
		WC()->session->set( self::SESSION_KEY, $all );
		return true;
	}

	/**
	 * Per-session, per-bump MySQL lock name.
	 *
	 * @param int|string $bump_id Bump id.
	 * @return string
	 */
	private static function lock_name( $bump_id ) {
		return 'wps_ubo_game_' . md5( WC()->session->get_customer_id() . '|' . absint( $bump_id ) );
	}

	/**
	 * Take the draw lock (waits up to 5 s). Returns the lock name, or '' when locking
	 * is not available (e.g. a non-MySQL database), in which case drawing still works.
	 *
	 * @param int|string $bump_id Bump id.
	 * @return string
	 */
	private static function acquire_lock( $bump_id ) {
		global $wpdb;
		if ( ! method_exists( WC()->session, 'get_customer_id' ) ) {
			return '';
		}
		$name = self::lock_name( $bump_id );
		$got  = $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, 5)', $name ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		return '1' === (string) $got ? $name : '';
	}

	/**
	 * Release the draw lock.
	 *
	 * @param string $name Lock name ('' = none).
	 * @return void
	 */
	private static function release_lock( $name ) {
		global $wpdb;
		if ( '' !== $name ) {
			$wpdb->query( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $name ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		}
	}

	/**
	 * Prize for a bump as saved in the database by another request (bypasses this
	 * request's in-memory session copy).
	 *
	 * @param int|string $bump_id Bump id.
	 * @return array|null
	 */
	private static function stored_prize( $bump_id ) {
		global $wpdb;
		if ( ! method_exists( WC()->session, 'get_customer_id' ) ) {
			return null;
		}
		$raw = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare( "SELECT session_value FROM {$wpdb->prefix}woocommerce_sessions WHERE session_key = %s", (string) WC()->session->get_customer_id() )
		);
		if ( ! $raw ) {
			return null;
		}
		$data   = maybe_unserialize( $raw );
		$prizes = is_array( $data ) && isset( $data[ self::SESSION_KEY ] ) ? maybe_unserialize( $data[ self::SESSION_KEY ] ) : array();
		$id     = absint( $bump_id );
		return is_array( $prizes ) && ! empty( $prizes[ $id ] ) && is_array( $prizes[ $id ] ) ? $prizes[ $id ] : null;
	}

	/**
	 * Forget prizes (after the order is placed).
	 *
	 * @return void
	 */
	public static function clear_session() {
		if ( self::has_session() ) {
			WC()->session->set( self::SESSION_KEY, null );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Price override
	|--------------------------------------------------------------------------
	*/

	/**
	 * Effective % discount for a bump with a prize.
	 *
	 * @param array $bump  Bump array (admin values).
	 * @param array $prize Prize state.
	 * @return int 0 when the standard offer applies.
	 */
	public static function effective_discount( $bump, $prize ) {
		$settings = self::get_settings();
		$discount = isset( $prize['discount'] ) && is_numeric( $prize['discount'] ) ? (float) $prize['discount'] : 0.0;
		// Re-cap at apply time, so lowering the maximum also limits prizes already drawn.
		$discount = max( 0.0, min( (float) $settings['max_discount'], $discount ) );

		$type   = isset( $bump['wps_upsell_offer_price_type'] ) ? $bump['wps_upsell_offer_price_type'] : '';
		$amount = isset( $bump['wps_upsell_bump_offer_discount_price'] ) && is_numeric( $bump['wps_upsell_bump_offer_discount_price'] ) ? (float) $bump['wps_upsell_bump_offer_discount_price'] : null;

		if ( $discount > 0 && 'on' === $settings['guarantee_offer_discount'] && null !== $amount ) {
			if ( '%' === $type ) {
				// The normal % offer is better: keep it (float, so 12.5% stays 12.5%).
				$discount = max( $discount, $amount );
			} elseif ( 'fixed' === $type ) {
				/*
				 * "Never worse than the normal offer" compares final prices: a prize % is taken
				 * off the product's current price, so with a cheap fixed-price bump the prize
				 * could cost more. Then the standard (fixed) offer applies.
				 */
				$base = self::offer_base_price( $bump );
				if ( $base > 0 && $amount <= $base * ( 1 - $discount / 100 ) ) {
					$discount = 0.0;
				}
			}
		}

		/**
		 * Filters the % discount applied to a bump after a game.
		 *
		 * @since 3.1.8
		 * @param int|float $discount Effective % (0 = standard offer).
		 * @param array     $prize    Prize state.
		 * @param array     $bump     Bump array.
		 */
		$discount = (float) apply_filters( 'wps_ubo_game_effective_discount', $discount, $prize, $bump );
		$discount = round( max( 0.0, min( 100.0, $discount ) ), 2 );

		// Whole numbers stay integers (labels, stored meta, existing callers).
		return floor( $discount ) == $discount ? (int) $discount : $discount; // phpcs:ignore Universal.Operators.StrictComparisons.LooseEqual
	}

	/**
	 * Price a % discount is taken from: the offer product's current price.
	 *
	 * Variable products use their highest variation price. The bump list is filtered
	 * before the shopper picks a variation (and one % applies to every variation), so
	 * the prize is kept only when it beats the fixed offer even on the most expensive
	 * variation; then it is cheaper on all of them. Otherwise the standard fixed offer
	 * applies, which can never be worse than the normal offer.
	 * 0 when unknown, in which case no fixed-price comparison is made.
	 *
	 * @param array $bump Bump array.
	 * @return float
	 */
	public static function offer_base_price( $bump ) {
		$product_id = isset( $bump['wps_upsell_bump_products_in_offer'] ) ? absint( $bump['wps_upsell_bump_products_in_offer'] ) : 0;
		$product    = $product_id && function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : null;
		if ( ! $product ) {
			return 0.0;
		}
		if ( $product->is_type( 'variable' ) ) {
			return (float) $product->get_variation_price( 'max' );
		}
		return (float) $product->get_price();
	}

	/**
	 * Front-end requests with a WC session.
	 *
	 * @return bool
	 */
	private static function front_request() {
		return ( ! is_admin() || wp_doing_ajax() ) && ! ( defined( 'WP_CLI' ) && WP_CLI ) && self::has_session();
	}

	/**
	 * Filter: option_wps_ubo_bump_list — apply won prizes.
	 *
	 * @param mixed $value Bump list.
	 * @return mixed
	 */
	public static function filter_bump_list( $value ) {
		if ( self::$bypass || ! is_array( $value ) || ! self::front_request() ) {
			return $value;
		}
		foreach ( self::session_prizes() as $bump_id => $prize ) {
			if ( empty( $value[ $bump_id ] ) || ! is_array( $value[ $bump_id ] ) || 'standard' === self::format_for( $bump_id ) ) {
				continue;
			}
			$bump     = $value[ $bump_id ];
			$discount = self::effective_discount( $bump, $prize );
			if ( $discount <= 0 ) {
				continue;
			}
			if ( ! isset( $bump[ self::MARKER ] ) ) {
				$bump[ self::MARKER ] = array(
					'price_type' => isset( $bump['wps_upsell_offer_price_type'] ) ? $bump['wps_upsell_offer_price_type'] : '',
					'discount'   => isset( $bump['wps_upsell_bump_offer_discount_price'] ) ? $bump['wps_upsell_bump_offer_discount_price'] : '',
				);
			}
			$bump['wps_upsell_offer_price_type']          = '%';
			$bump['wps_upsell_bump_offer_discount_price'] = (string) $discount;
			$value[ $bump_id ]                            = $bump;
		}
		return $value;
	}

	/**
	 * Filter: pre_update_option_wps_ubo_bump_list — never persist prizes.
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
				$value[ $id ]['wps_upsell_offer_price_type']          = $bump[ self::MARKER ]['price_type'];
				$value[ $id ]['wps_upsell_bump_offer_discount_price'] = $bump[ self::MARKER ]['discount'];
				unset( $value[ $id ][ self::MARKER ] );
			}
		}
		return $value;
	}

	/**
	 * Raw (unfiltered) bump list.
	 *
	 * @return array
	 */
	public static function raw_bumps() {
		self::$bypass = true;
		$bumps        = get_option( 'wps_ubo_bump_list', array() );
		self::$bypass = false;
		return is_array( $bumps ) ? $bumps : array();
	}

	/*
	|--------------------------------------------------------------------------
	| Statistics
	|--------------------------------------------------------------------------
	*/

	/**
	 * Increment a bump statistic.
	 *
	 * @param int|string $bump_id Bump id.
	 * @param string     $field   plays|skips|accepts.
	 * @param int|null   $prize   Prize index (for plays).
	 * @param string     $label   Prize label at draw time (for plays), so stats keep
	 *                            the right name after the prize table is edited.
	 * @return void
	 */
	public static function bump_stat( $bump_id, $field, $prize = null, $label = '' ) {
		$stats = get_option( self::STATS_OPTION, array() );
		$stats = is_array( $stats ) ? $stats : array();
		$id    = absint( $bump_id );
		$row   = isset( $stats[ $id ] ) && is_array( $stats[ $id ] ) ? $stats[ $id ] : array();
		$row   = wp_parse_args(
			$row,
			array(
				'plays'   => 0,
				'skips'   => 0,
				'accepts' => 0,
				'prizes'  => array(),
			)
		);

		$row[ $field ] = (int) $row[ $field ] + 1;
		if ( null !== $prize && $prize >= 0 ) {
			$row['prizes'][ $prize ] = isset( $row['prizes'][ $prize ] ) ? (int) $row['prizes'][ $prize ] + 1 : 1;
			if ( '' !== $label ) {
				$row['labels']           = isset( $row['labels'] ) && is_array( $row['labels'] ) ? $row['labels'] : array();
				$row['labels'][ $label ] = isset( $row['labels'][ $label ] ) ? (int) $row['labels'][ $label ] + 1 : 1;
			}
		}
		$stats[ $id ] = $row;
		update_option( self::STATS_OPTION, $stats, false );
	}

	/**
	 * All statistics.
	 *
	 * @return array
	 */
	public static function get_stats() {
		$stats = get_option( self::STATS_OPTION, array() );
		return is_array( $stats ) ? $stats : array();
	}

	/**
	 * Remove all data (uninstall).
	 *
	 * @return void
	 */
	public static function uninstall() {
		delete_option( self::SETTINGS_OPTION );
		delete_option( self::STATS_OPTION );
	}
}
