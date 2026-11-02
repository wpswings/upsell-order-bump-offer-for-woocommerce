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
				$prizes[] = array(
					'label'    => mb_substr( sanitize_text_field( $row['label'] ), 0, 24 ),
					'discount' => max( 0, min( $max, (int) ( isset( $row['discount'] ) ? $row['discount'] : 0 ) ) ),
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

		$settings = self::get_settings();
		$prizes   = self::prizes_for( $bump_id );

		if ( $skip ) {
			$prize = array(
				'index'    => -1,
				'label'    => '',
				'discount' => 0,
				'skipped'  => true,
			);
		} else {
			$index = self::weighted_index( $prizes );
			$row   = $prizes[ $index ];
			$prize = array(
				'index'    => $index,
				'label'    => (string) $row['label'],
				'discount' => max( 0, min( (int) $settings['max_discount'], (int) $row['discount'] ) ),
				'skipped'  => false,
			);
		}
		$prize['format'] = self::format_for( $bump_id );
		$prize['time']   = time();

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

		self::bump_stat( $bump_id, $skip ? 'skips' : 'plays', $skip ? null : (int) $prize['index'] );

		return array(
			'prize' => $prize,
			'new'   => true,
		);
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
		$discount = (int) $prize['discount'];

		if ( $discount > 0 && 'on' === $settings['guarantee_offer_discount']
			&& isset( $bump['wps_upsell_offer_price_type'] ) && '%' === $bump['wps_upsell_offer_price_type']
			&& isset( $bump['wps_upsell_bump_offer_discount_price'] ) && is_numeric( $bump['wps_upsell_bump_offer_discount_price'] ) ) {
			$discount = max( $discount, (int) $bump['wps_upsell_bump_offer_discount_price'] );
		}

		/**
		 * Filters the % discount applied to a bump after a game.
		 *
		 * @since 3.1.8
		 * @param int   $discount Effective % (0 = standard offer).
		 * @param array $prize    Prize state.
		 * @param array $bump     Bump array.
		 */
		return max( 0, min( 100, (int) apply_filters( 'wps_ubo_game_effective_discount', $discount, $prize, $bump ) ) );
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
	 * @return void
	 */
	public static function bump_stat( $bump_id, $field, $prize = null ) {
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
