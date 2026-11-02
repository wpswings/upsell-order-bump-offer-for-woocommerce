<?php
/**
 * Offer Heatmaps - public side.
 *
 * Loads the front-end tracker on pages that can display an Order Bump or an
 * Upsell offer and receives batched interaction events.
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
 * Offer Heatmaps - public side.
 *
 * @since 3.1.8
 */
class Upsell_Order_Bump_Heatmap_Public {

	/**
	 * AJAX action used by the tracker.
	 *
	 * @var string
	 */
	const AJAX_ACTION = 'wps_ubo_heatmap_track';

	/**
	 * Nonce action used by the tracker.
	 *
	 * @var string
	 */
	const NONCE_ACTION = 'wps_ubo_heatmap_track';

	/**
	 * Max events accepted per request.
	 *
	 * @var int
	 */
	const MAX_EVENTS_PER_REQUEST = 100;

	/**
	 * Max requests per visitor per minute.
	 *
	 * @var int
	 */
	const MAX_REQUESTS_PER_MINUTE = 60;

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Constructor.
	 *
	 * @param string $version Plugin version.
	 */
	public function __construct( $version ) {
		$this->version = $version;
	}

	/**
	 * Zone selector maps (ordered, first match wins). Shared with admin for labels.
	 *
	 * @return array
	 */
	public static function zone_selectors() {
		$zones = array(
			'bump'   => array(
				'quantity'        => '.wps_quantity_input, .wps_input_quantity, .wps-ubo__qty-btn, .quantity',
				'variation'       => 'select, .wps-option, .wps_variable_qty_temp_12',
				'accept_checkbox' => '.add_offer_in_cart, .wps_upsell_bump_checkbox_container, .wps-ubo__temp-prod-check, .upsell-order-check, .checkmark',
				'accept_button'   => '.wps-ubo__btn, .custom-btn, button',
				'accept_text'     => '.add_offer_in_cart_text, .wps_upsell_offer_primary_section',
				'image'           => '.wps_upsell_offer_img, .wps_upsell_offer_image, .upsell-product-img, .wps-ob_ta-p-img, img',
				'title'           => '.wps_bump_name, .bump-offer-product-name, .wps-ob_ta-p-name, .wps-ob_ta-o-title, .wps_upsell_product_permalink',
				'price'           => '.wps_upsell_offer_product_price, .product-price, .wps-ob_ta-p-price, .wps-ubo__temp-prod-price-new',
				'timer'           => '.wps-timer-wrap, .upsell-offer-timer-section',
				'discount'        => '.wps_upsell_offer_discount_section, .uspell-offer-discount',
				'offer_text'      => '.wps_upsell_offer_secondary_section, .wps-ob_ta-o-desc',
				'description'     => '.wps_upsell_offer_product_description, .upsell-product-desc, .wps-ob_ta-p-desc',
			),
			'upsell' => array(
				'accept_button'  => 'a[href*="wps_wocuf_pro_buy"], [name="wps_wocuf_pro_buy"], .wps_wocuf_pro_custom_buy, .wps_wocuf_pro_buy',
				'decline_button' => 'a[href*="ocuf_th=1"], .wps_wocuf_pro_no, .wps_wocuf_pro_skip, .wps_wocuf_pro_custom_skip',
				'quantity'       => '.wps_upsell_quantity, .wps_upsell_quantity_input',
				'variation'      => 'select, .wps_upsell_offer_variation_select',
				'image'          => '.wps_upsell_offer_product_image, img',
				'price'          => '.wps_upsell_offer_product_price',
				'timer'          => '.wps_upsell_lite_display_timer_wrap',
			),
		);

		/**
		 * Filter heatmap zone selectors.
		 *
		 * @since 3.1.8
		 * @param array $zones Zone => CSS selector map keyed by offer type.
		 */
		return apply_filters( 'wps_ubo_heatmap_zone_selectors', $zones );
	}

	/**
	 * Every allowed zone key.
	 *
	 * @return array
	 */
	public static function allowed_zones() {
		$zones = array( 'other' );
		foreach ( self::zone_selectors() as $map ) {
			$zones = array_merge( $zones, array_keys( $map ) );
		}
		return array_unique( array_map( 'sanitize_key', $zones ) );
	}

	/**
	 * Detect the upsell offer context from the live offer URL.
	 *
	 * @return array|false [ funnel_id, offer_id ] or false.
	 */
	private function get_upsell_context() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only detection; the funnel offer nonce is validated by the offer page itself.
		if ( ! isset( $_GET['ocuf_fid'], $_GET['ocuf_ofd'], $_GET['ocuf_ns'] ) ) {
			return false;
		}
		$funnel_id = absint( wp_unslash( $_GET['ocuf_fid'] ) );
		$offer_id  = absint( wp_unslash( $_GET['ocuf_ofd'] ) );
		// phpcs:enable

		$funnels = get_option( 'wps_wocuf_funnels_list', array() );
		if ( empty( $funnels[ $funnel_id ] ) ) {
			return false;
		}

		return array(
			'funnel_id' => $funnel_id,
			'offer_id'  => $offer_id,
		);
	}

	/**
	 * Detect the base context for bump pages.
	 *
	 * @return string|false
	 */
	private function get_bump_context() {
		if ( function_exists( 'is_order_received_page' ) && is_order_received_page() ) {
			return false;
		}

		if ( function_exists( 'is_checkout' ) && is_checkout() ) {
			$page_id = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'checkout' ) : 0;
			return ( $page_id > 0 && has_block( 'woocommerce/checkout', $page_id ) ) ? 'checkout_block' : 'checkout';
		}

		if ( function_exists( 'is_cart' ) && is_cart() ) {
			$page_id = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'cart' ) : 0;
			return ( $page_id > 0 && has_block( 'woocommerce/cart', $page_id ) ) ? 'cart_block' : 'cart';
		}

		global $post;
		if ( $post instanceof WP_Post && has_shortcode( $post->post_content, 'wps_bump_offer_shortcode' ) ) {
			return 'shortcode';
		}

		return false;
	}

	/**
	 * Enqueue tracker when the request can show an offer.
	 *
	 * @return void
	 */
	public function enqueue_tracker() {
		$settings = Upsell_Order_Bump_Heatmap_Db::get_settings();

		if ( 'on' !== $settings['enabled'] ) {
			return;
		}

		/**
		 * Whether shop managers should be tracked (default: no, they pollute the data while testing).
		 *
		 * @since 3.1.8
		 */
		if ( current_user_can( 'manage_woocommerce' ) && ! apply_filters( 'wps_ubo_heatmap_track_admins', false ) ) {
			return;
		}

		$upsell  = $this->get_upsell_context();
		$context = $upsell ? 'upsell_page' : $this->get_bump_context();

		/**
		 * Final say on whether the tracker loads.
		 *
		 * @since 3.1.8
		 */
		if ( ! apply_filters( 'wps_ubo_heatmap_should_track', (bool) $context, $context ) ) {
			return;
		}

		$config = array(
			'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
			'action'        => self::AJAX_ACTION,
			'nonce'         => wp_create_nonce( self::NONCE_ACTION ),
			'context'       => $context,
			'upsell'        => $upsell ? $upsell : null,
			'trackHover'    => 'on' === $settings['track_hover'],
			'sampleRate'    => min( 100, max( 1, absint( $settings['sample_rate'] ) ) ),
			'zones'         => self::zone_selectors(),
			'flushInterval' => 10000,
			'flushSize'     => 25,
			'maxClicks'     => 30,
			'maxHovers'     => 60,
			'hoverInterval' => 250,
		);

		/**
		 * Filter tracker configuration.
		 *
		 * @since 3.1.8
		 */
		$config = apply_filters( 'wps_ubo_heatmap_tracker_config', $config );

		wp_enqueue_script(
			'wps-ubo-heatmap-tracker',
			plugin_dir_url( __FILE__ ) . 'js/wps-ubo-heatmap-tracker.js',
			array(),
			$this->version,
			true
		);
		// JSON (not wp_localize_script) so numbers/booleans keep their types.
		wp_add_inline_script( 'wps-ubo-heatmap-tracker', 'window.wpsUboHeatmap = ' . wp_json_encode( $config ) . ';', 'before' );
	}

	/**
	 * Hide the admin bar inside the admin heatmap preview iframe so coordinates line up.
	 *
	 * @param bool $show Show admin bar.
	 * @return bool
	 */
	public function maybe_hide_admin_bar( $show ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Cosmetic flag only.
		if ( isset( $_GET['wps_ubo_heatmap_preview'] ) && current_user_can( 'manage_woocommerce' ) ) {
			return false;
		}
		return $show;
	}

	/**
	 * Ingest a batch of events. Always answers with a small success payload so the
	 * tracker never retries; invalid data is silently discarded.
	 *
	 * @return void
	 */
	public function ingest() {
		$settings = Upsell_Order_Bump_Heatmap_Db::get_settings();

		if ( 'on' !== $settings['enabled'] || ! check_ajax_referer( self::NONCE_ACTION, 'nonce', false ) ) {
			wp_send_json_success( array( 'stored' => 0 ) );
		}

		if ( $this->is_rate_limited() ) {
			wp_send_json_success( array( 'stored' => 0 ) );
		}

		// Raw JSON is decoded first, then every field is whitelisted/sanitized in sanitize_event().
		$raw = isset( $_POST['events'] ) ? wp_unslash( $_POST['events'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- nonce verified above; fields sanitized individually.
		$raw = is_string( $raw ) && strlen( $raw ) <= 20000 ? json_decode( $raw, true ) : null;

		if ( ! is_array( $raw ) || empty( $raw ) ) {
			wp_send_json_success( array( 'stored' => 0 ) );
		}

		$raw = array_slice( $raw, 0, self::MAX_EVENTS_PER_REQUEST );

		$device = isset( $_POST['device'] ) ? sanitize_key( wp_unslash( $_POST['device'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$device = in_array( $device, Upsell_Order_Bump_Heatmap_Db::DEVICES, true ) ? $device : 'desktop';

		$visitor      = isset( $_POST['vid'] ) ? sanitize_key( wp_unslash( $_POST['vid'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$session_hash = md5( wp_salt( 'nonce' ) . substr( $visitor, 0, 64 ) . gmdate( 'Y-m-d' ) );

		$lookups = array(
			'bumps'   => get_option( 'wps_ubo_bump_list', array() ),
			'funnels' => get_option( 'wps_wocuf_funnels_list', array() ),
			'zones'   => self::allowed_zones(),
		);

		$rows = array();
		foreach ( $raw as $event ) {
			$row = $this->sanitize_event( $event, $lookups );
			if ( $row ) {
				$row['device']       = $device;
				$row['session_hash'] = $session_hash;
				$rows[]              = $row;
			}
		}

		/**
		 * Filter sanitized rows before they are stored.
		 *
		 * @since 3.1.8
		 */
		$rows = apply_filters( 'wps_ubo_heatmap_before_insert', $rows );

		$stored = Upsell_Order_Bump_Heatmap_Db::insert_events( $rows );

		/**
		 * Fires after a batch of heatmap events is stored.
		 *
		 * @since 3.1.8
		 */
		do_action( 'wps_ubo_heatmap_events_inserted', $rows, $stored );

		wp_send_json_success( array( 'stored' => $stored ) );
	}

	/**
	 * Validate + normalise a single event.
	 *
	 * @param mixed $event   Raw decoded event.
	 * @param array $lookups Pre-loaded option data.
	 * @return array|false
	 */
	private function sanitize_event( $event, $lookups ) {
		if ( ! is_array( $event ) ) {
			return false;
		}

		$offer_type = isset( $event['t'] ) ? sanitize_key( $event['t'] ) : '';
		$event_type = isset( $event['e'] ) ? sanitize_key( $event['e'] ) : '';
		$context    = isset( $event['c'] ) ? sanitize_key( $event['c'] ) : '';
		$zone       = isset( $event['z'] ) ? sanitize_key( $event['z'] ) : 'other';
		$offer_id   = isset( $event['o'] ) ? absint( $event['o'] ) : 0;
		$sub_offer  = isset( $event['s'] ) ? absint( $event['s'] ) : 0;

		if ( ! in_array( $offer_type, Upsell_Order_Bump_Heatmap_Db::OFFER_TYPES, true )
			|| ! in_array( $event_type, Upsell_Order_Bump_Heatmap_Db::EVENT_TYPES, true )
			|| ! in_array( $context, Upsell_Order_Bump_Heatmap_Db::CONTEXTS, true ) ) {
			return false;
		}

		// Template is resolved server-side; never trusted from the client.
		if ( 'bump' === $offer_type ) {
			if ( empty( $lookups['bumps'][ $offer_id ] ) || ! is_array( $lookups['bumps'][ $offer_id ] ) ) {
				return false;
			}
			$sub_offer = 0;
			$template  = ! empty( $lookups['bumps'][ $offer_id ]['wps_ubo_selected_template'] ) ? (string) $lookups['bumps'][ $offer_id ]['wps_ubo_selected_template'] : '1';
		} else {
			if ( empty( $lookups['funnels'][ $offer_id ] ) || ! is_array( $lookups['funnels'][ $offer_id ] ) ) {
				return false;
			}
			$funnel   = $lookups['funnels'][ $offer_id ];
			$template = ! empty( $funnel['wps_wocuf_pro_offer_template'][ $sub_offer ] ) ? (string) $funnel['wps_wocuf_pro_offer_template'][ $sub_offer ] : 'default';
			if ( ! empty( $funnel['wps_wocuf_offer_custom_page_url'][ $sub_offer ] ) ) {
				$template = 'custom';
			}
		}

		if ( ! in_array( $zone, $lookups['zones'], true ) ) {
			$zone = 'other';
		}

		$x = isset( $event['x'] ) ? (int) $event['x'] : 0;
		$y = isset( $event['y'] ) ? (int) $event['y'] : 0;

		return array(
			'offer_type'   => $offer_type,
			'offer_id'     => $offer_id,
			'sub_offer_id' => $sub_offer,
			'template'     => substr( sanitize_key( $template ), 0, 20 ),
			'event_type'   => $event_type,
			'x'            => 'view' === $event_type ? 0 : max( 0, min( 10000, $x ) ),
			'y'            => 'view' === $event_type ? 0 : max( 0, min( 10000, $y ) ),
			'zone'         => 'view' === $event_type ? 'other' : $zone,
			'context'      => $context,
		);
	}

	/**
	 * Simple per-IP throttle. The IP is hashed and only kept in a 60 second transient.
	 *
	 * @return bool
	 */
	private function is_rate_limited() {
		$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		$key = 'wps_ubo_hm_rl_' . md5( $ip );

		$count = (int) get_transient( $key );
		if ( $count >= self::MAX_REQUESTS_PER_MINUTE ) {
			return true;
		}

		set_transient( $key, $count + 1, MINUTE_IN_SECONDS );
		return false;
	}

	/**
	 * Privacy policy suggestion text.
	 *
	 * @return void
	 */
	public static function add_privacy_policy_content() {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return;
		}

		$content = '<p>' . esc_html__( 'When you view an offer on our checkout or post-purchase pages, we record anonymous interaction data (where on the offer you click or move the mouse, the device type and the time). This data is not linked to your name, email address, account, order or IP address, and is automatically deleted after a limited retention period. It is used only to improve the design of our offers.', 'upsell-order-bump-offer-for-woocommerce' ) . '</p>';

		wp_add_privacy_policy_content( esc_html__( 'Upsell Funnel Builder – Offer Heatmaps', 'upsell-order-bump-offer-for-woocommerce' ), wp_kses_post( $content ) );
	}
}
