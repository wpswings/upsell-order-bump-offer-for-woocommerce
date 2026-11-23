<?php
/**
 * Offer Heatmaps - admin side.
 *
 * Registers the "Offer Heatmaps" sub-menu, serves aggregated heatmap data and
 * template previews over AJAX, and stores the feature settings.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      3.1.8
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/admin
 * @author     WP Swings <webmaster@wpswings.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Offer Heatmaps - admin side.
 *
 * @since 3.1.8
 */
class Upsell_Order_Bump_Heatmap_Admin {

	/**
	 * Sub-menu slug.
	 *
	 * @var string
	 */
	const PAGE_SLUG = 'upsell-order-bump-offer-for-woocommerce-heatmaps';

	/**
	 * Nonce action for admin AJAX.
	 *
	 * @var string
	 */
	const NONCE_ACTION = 'wps_ubo_heatmap_admin';

	/**
	 * Capability required.
	 *
	 * @var string
	 */
	const CAPABILITY = 'manage_woocommerce';

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Hook suffix of the sub-menu page.
	 *
	 * @var string
	 */
	private $hook_suffix = '';

	/**
	 * Constructor.
	 *
	 * @param string $version Plugin version.
	 */
	public function __construct( $version ) {
		$this->version = $version;
	}

	/**
	 * Register the sub-menu under "Upsell Funnel Builder".
	 *
	 * @return void
	 */
	public function add_menu() {
		$this->hook_suffix = (string) add_submenu_page(
			'upsell-order-bump-offer-for-woocommerce-setting',
			esc_html__( 'Offer Heatmaps', 'upsell-order-bump-offer-for-woocommerce' ),
			esc_html__( 'Offer Heatmaps', 'upsell-order-bump-offer-for-woocommerce' ),
			self::CAPABILITY,
			self::PAGE_SLUG,
			array( $this, 'render_page' )
		);
	}

	/**
	 * Enqueue admin assets on the heatmap page only.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 * @return void
	 */
	public function enqueue_assets( $hook_suffix ) {
		if ( empty( $this->hook_suffix ) || $hook_suffix !== $this->hook_suffix ) {
			return;
		}

		$base = plugin_dir_url( __DIR__ );

		// Bump templates need their front-end styles for an accurate preview.
		wp_enqueue_style( 'wps-ubo-heatmap-bump-public', $base . 'public/css/upsell-order-bump-offer-for-woocommerce-public.css', array(), $this->version );
		wp_enqueue_style( 'wps-ubo-heatmap-admin', $base . 'admin/css/wps-ubo-heatmap-admin.css', array(), $this->version );

		wp_enqueue_script( 'wps-ubo-heatmap-admin', $base . 'admin/js/wps-ubo-heatmap-admin.js', array(), $this->version, true );

		$zone_labels = array(
			'game'            => __( 'Spin wheel / scratch card', 'upsell-order-bump-offer-for-woocommerce' ),
			'accept_checkbox' => __( 'Accept checkbox', 'upsell-order-bump-offer-for-woocommerce' ),
			'accept_button'   => __( 'Accept button', 'upsell-order-bump-offer-for-woocommerce' ),
			'accept_text'     => __( 'Accept bar / text', 'upsell-order-bump-offer-for-woocommerce' ),
			'decline_button'  => __( 'Decline ("No thanks")', 'upsell-order-bump-offer-for-woocommerce' ),
			'quantity'        => __( 'Quantity', 'upsell-order-bump-offer-for-woocommerce' ),
			'variation'       => __( 'Variation selector', 'upsell-order-bump-offer-for-woocommerce' ),
			'image'           => __( 'Product image', 'upsell-order-bump-offer-for-woocommerce' ),
			'title'           => __( 'Product title', 'upsell-order-bump-offer-for-woocommerce' ),
			'price'           => __( 'Price', 'upsell-order-bump-offer-for-woocommerce' ),
			'timer'           => __( 'Countdown timer', 'upsell-order-bump-offer-for-woocommerce' ),
			'discount'        => __( 'Discount headline', 'upsell-order-bump-offer-for-woocommerce' ),
			'offer_text'      => __( 'Offer description', 'upsell-order-bump-offer-for-woocommerce' ),
			'description'     => __( 'Product description', 'upsell-order-bump-offer-for-woocommerce' ),
			'other'           => __( 'Other / empty space', 'upsell-order-bump-offer-for-woocommerce' ),
		);

		wp_localize_script(
			'wps-ubo-heatmap-admin',
			'wpsUboHeatmapAdmin',
			array(
				'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
				'nonce'      => wp_create_nonce( self::NONCE_ACTION ),
				'offers'     => $this->get_offer_catalog(),
				'zones'      => Upsell_Order_Bump_Heatmap_Public::zone_selectors(),
				'zoneLabels' => $zone_labels,
				'i18n'       => array(
					'loading'       => __( 'Loading…', 'upsell-order-bump-offer-for-woocommerce' ),
					'noData'        => __( 'No interactions recorded for this selection yet.', 'upsell-order-bump-offer-for-woocommerce' ),
					'noOffers'      => __( 'No offers found. Create an order bump or upsell funnel first.', 'upsell-order-bump-offer-for-woocommerce' ),
					'previewError'  => __( 'Preview could not be rendered (is the offer product still published?). Heatmap is drawn on a blank canvas.', 'upsell-order-bump-offer-for-woocommerce' ),
					'confirmDelete' => __( 'Delete ALL heatmap data? This cannot be undone.', 'upsell-order-bump-offer-for-woocommerce' ),
					'deleted'       => __( 'All heatmap data deleted.', 'upsell-order-bump-offer-for-woocommerce' ),
					'saved'         => __( 'Settings saved.', 'upsell-order-bump-offer-for-woocommerce' ),
					'error'         => __( 'Something went wrong. Please reload and try again.', 'upsell-order-bump-offer-for-woocommerce' ),
					'allTemplates'  => __( 'All templates', 'upsell-order-bump-offer-for-woocommerce' ),
					'template'      => __( 'Template', 'upsell-order-bump-offer-for-woocommerce' ),
					'current'       => __( 'current', 'upsell-order-bump-offer-for-woocommerce' ),
					'bumpLabel'     => __( 'Order bump', 'upsell-order-bump-offer-for-woocommerce' ),
					'funnelLabel'   => __( 'Upsell funnel', 'upsell-order-bump-offer-for-woocommerce' ),
					'active'        => __( 'Tracking active', 'upsell-order-bump-offer-for-woocommerce' ),
					'paused'        => __( 'Tracking paused', 'upsell-order-bump-offer-for-woocommerce' ),
				),
			)
		);
	}

	/**
	 * All bumps and funnel offers for the selectors.
	 *
	 * @return array
	 */
	private function get_offer_catalog() {
		$catalog = array(
			'bump'   => array(),
			'upsell' => array(),
		);

		$bumps = get_option( 'wps_ubo_bump_list', array() );
		if ( is_array( $bumps ) ) {
			foreach ( $bumps as $bump_id => $bump ) {
				if ( ! is_array( $bump ) ) {
					continue;
				}
				$catalog['bump'][] = array(
					'id'       => absint( $bump_id ),
					'name'     => ! empty( $bump['wps_upsell_bump_name'] ) ? sanitize_text_field( $bump['wps_upsell_bump_name'] ) : sprintf( /* translators: %d: bump id */ __( 'Order Bump #%d', 'upsell-order-bump-offer-for-woocommerce' ), $bump_id ),
					'template' => ! empty( $bump['wps_ubo_selected_template'] ) ? sanitize_text_field( $bump['wps_ubo_selected_template'] ) : '1',
					// Where the bump's template and its design are changed.
					'editUrl'  => add_query_arg(
						array(
							'page'    => 'upsell-order-bump-offer-for-woocommerce-setting',
							'tab'     => 'creation-setting',
							'bump_id' => absint( $bump_id ),
						),
						admin_url( 'admin.php' )
					),
				);
			}
		}

		foreach ( Upsell_Order_Bump_Heatmap_Db::get_funnels() as $funnel_id => $funnel ) {
			$offers = array();
			foreach ( array_keys( $funnel['offers'] ) as $offer_id ) {
				$offers[] = array(
					'id'   => absint( $offer_id ),
					/* translators: %d: offer number inside the funnel */
					'name' => sprintf( __( 'Offer #%d', 'upsell-order-bump-offer-for-woocommerce' ), $offer_id ),
				);
			}
			$catalog['upsell'][] = array(
				'id'     => absint( $funnel_id ),
				'name'   => '' !== $funnel['name'] ? sanitize_text_field( $funnel['name'] ) : sprintf( /* translators: %d: funnel id */ __( 'Funnel #%d', 'upsell-order-bump-offer-for-woocommerce' ), $funnel_id ),
				'offers' => $offers,
			);
		}

		return $catalog;
	}

	/**
	 * Page markup.
	 *
	 * @return void
	 */
	public function render_page() {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'upsell-order-bump-offer-for-woocommerce' ) );
		}

		$settings = Upsell_Order_Bump_Heatmap_Db::get_settings();
		require UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/templates/wps-ubo-heatmap-display.php';
	}

	/**
	 * Shared permission + nonce gate for admin AJAX.
	 *
	 * @return void
	 */
	private function verify_request() {
		if ( ! check_ajax_referer( self::NONCE_ACTION, 'nonce', false ) || ! current_user_can( self::CAPABILITY ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'upsell-order-bump-offer-for-woocommerce' ) ), 403 );
		}
	}

	/**
	 * Read + sanitize filters from the request.
	 *
	 * @return array
	 */
	private function read_filters() {
		if ( ! check_ajax_referer( self::NONCE_ACTION, 'nonce', false ) || ! current_user_can( self::CAPABILITY ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'upsell-order-bump-offer-for-woocommerce' ) ), 403 );
		}
		$offer_type = isset( $_POST['offer_type'] ) ? sanitize_key( wp_unslash( $_POST['offer_type'] ) ) : 'bump';
		$offer_type = in_array( $offer_type, Upsell_Order_Bump_Heatmap_Db::OFFER_TYPES, true ) ? $offer_type : 'bump';

		$device = isset( $_POST['device'] ) ? sanitize_key( wp_unslash( $_POST['device'] ) ) : '';
		$device = in_array( $device, Upsell_Order_Bump_Heatmap_Db::DEVICES, true ) ? $device : '';

		$days = isset( $_POST['days'] ) ? absint( $_POST['days'] ) : 30;
		$days = in_array( $days, array( 1, 7, 30, 90, 365 ), true ) ? $days : 30;

		$filters = array(
			'offer_type'   => $offer_type,
			'offer_id'     => isset( $_POST['offer_id'] ) ? absint( $_POST['offer_id'] ) : 0,
			'sub_offer_id' => 'upsell' === $offer_type && isset( $_POST['sub_offer_id'] ) ? absint( $_POST['sub_offer_id'] ) : 0,
			'device'       => $device,
			'template'     => isset( $_POST['template'] ) ? substr( sanitize_key( wp_unslash( $_POST['template'] ) ), 0, 20 ) : '',
			'date_from'    => gmdate( 'Y-m-d H:i:s', time() - ( $days * DAY_IN_SECONDS ) ),
			'date_to'      => gmdate( 'Y-m-d H:i:s' ),
		);
		// phpcs:enable

		return $filters;
	}

	/**
	 * AJAX: aggregated heatmap data.
	 *
	 * @return void
	 */
	public function ajax_get_data() {
		$this->verify_request();
		$filters = $this->read_filters();

		wp_send_json_success(
			array(
				'click'     => Upsell_Order_Bump_Heatmap_Db::get_grid( $filters, 'click' ),
				'hover'     => Upsell_Order_Bump_Heatmap_Db::get_grid( $filters, 'hover' ),
				'zones'     => Upsell_Order_Bump_Heatmap_Db::get_zones( $filters ),
				'totals'    => Upsell_Order_Bump_Heatmap_Db::get_totals( $filters ),
				'templates' => Upsell_Order_Bump_Heatmap_Db::get_templates_with_data( $filters['offer_type'], $filters['offer_id'], $filters['sub_offer_id'] ),
			)
		);
	}

	/**
	 * AJAX: template preview backdrop.
	 *
	 * Bumps are rendered with the same functions used on checkout. Upsell offers
	 * return the offer page URL that the admin loads in a same-origin iframe.
	 *
	 * @return void
	 */
	public function ajax_get_preview() {
		$this->verify_request();
		$filters = $this->read_filters();

		if ( 'upsell' === $filters['offer_type'] ) {
			wp_send_json_success( array( 'url' => $this->get_upsell_preview_url( $filters['offer_id'], $filters['sub_offer_id'] ) ) );
		}

		wp_send_json_success( $this->render_bump_preview( $filters['offer_id'], $filters['template'] ) );
	}

	/**
	 * Offer page URL for an upsell offer.
	 *
	 * @param int $funnel_id Funnel id.
	 * @param int $offer_id  Offer id.
	 * @return string
	 */
	private function get_upsell_preview_url( $funnel_id, $offer_id ) {
		$funnels = Upsell_Order_Bump_Heatmap_Db::get_funnels();
		$funnel  = isset( $funnels[ $funnel_id ] ) ? $funnels[ $funnel_id ] : array();

		$url = '';
		if ( ! empty( $funnel['custom_url'][ $offer_id ] ) ) {
			$url = $funnel['custom_url'][ $offer_id ];
		} elseif ( ! empty( $funnel['page'][ $offer_id ] ) ) {
			$url = get_permalink( absint( $funnel['page'][ $offer_id ] ) );
		}

		if ( empty( $url ) ) {
			return '';
		}

		// Only allow same-site URLs so the iframe can be measured.
		if ( wp_parse_url( $url, PHP_URL_HOST ) !== wp_parse_url( home_url(), PHP_URL_HOST ) ) {
			return '';
		}

		// The funnel and offer ids tell the offer shortcodes which offer to render ( the shop
		// manager preview mode ); without them the page shows no product, price or image.
		// No order key or nonce is added, so nothing on the page can act on a real order.
		return esc_url_raw(
			add_query_arg(
				array(
					'wps_ubo_heatmap_preview' => '1',
					'ocuf_fid'                => absint( $funnel_id ),
					'ocuf_ofd'                => absint( $offer_id ),
				),
				$url
			)
		);
	}

	/**
	 * Render a bump exactly as checkout does.
	 *
	 * @param int    $bump_id  Bump id.
	 * @param string $template Template to draw instead of the bump's current one ( the
	 *                         heatmap filtered to data recorded with an older template ).
	 * @return array { html, css }
	 */
	private function render_bump_preview( $bump_id, $template = '' ) {
		$bumps = get_option( 'wps_ubo_bump_list', array() );
		if ( empty( $bumps[ $bump_id ] ) || ! function_exists( 'wps_ubo_lite_fetch_bump_offer_details' ) ) {
			return array(
				'html' => '',
				'css'  => '',
			);
		}

		$current  = ! empty( $bumps[ $bump_id ]['wps_ubo_selected_template'] ) ? (string) $bumps[ $bump_id ]['wps_ubo_selected_template'] : '1';
		$template = (string) $template;
		// Another template than the saved one: draw it with that template's default design.
		$design   = '' !== $template && $template !== $current && function_exists( 'wps_ubo_lite_offer_template_' . $template ) ? call_user_func( 'wps_ubo_lite_offer_template_' . $template ) : null;
		$template = $design ? $template : $current;
		$map      = array(
			'3'  => 'wps_ubo_lite_bump_offer_html_3',
			'4'  => 'wps_ubo_lite_bump_offer_html_4',
			'5'  => 'wps_ubo_lite_bump_offer_html_5',
			'6'  => 'wps_ubo_lite_bump_offer_html_pro_6',
			'7'  => 'wps_ubo_lite_bump_offer_html_pro_6',
			'8'  => 'wps_ubo_lite_bump_offer_html_pro_6',
			'9'  => 'wps_ubo_lite_bump_offer_html_pro_6',
			'10' => 'wps_ubo_lite_bump_offer_html_10',
			'11' => 'wps_ubo_lite_bump_offer_html_11',
			'12' => 'wps_ubo_lite_bump_offer_html_12',
			'13' => 'wps_ubo_lite_bump_offer_html_13',
			'14' => 'wps_ubo_lite_bump_offer_html_14',
		);
		$renderer = isset( $map[ $template ] ) && function_exists( $map[ $template ] ) ? $map[ $template ] : 'wps_ubo_lite_bump_offer_html';

		$html    = '';
		$printed = '';
		ob_start();
		try {
			$bump = wps_ubo_lite_fetch_bump_offer_details( $bump_id, '' );
			if ( ! empty( $bump ) && $design ) {
				$bump['design_css'] = array_merge( isset( $bump['design_css'] ) ? (array) $bump['design_css'] : array(), $design );
			}
			if ( ! empty( $bump ) ) {
				$html = (string) call_user_func( $renderer, $bump, $bump_id, '' );
			}
		} catch ( Throwable $e ) {
			$html = '';
		}
		$printed = (string) ob_get_clean();

		// Template functions print their <style> blocks directly; collect CSS from both outputs.
		$css = '';
		if ( preg_match_all( '#<style[^>]*>(.*?)</style>#is', $printed . $html, $matches ) ) {
			$css = implode( "\n", $matches[1] );
		}
		$html = preg_replace( '#<style[^>]*>.*?</style>#is', '', $html );

		return array(
			'html'     => wp_kses( $html, wps_ubo_lite_allowed_html() ),
			// CSS is inserted with textContent on a <style> element (cannot execute script).
			'css'      => wp_strip_all_tags( $css ),
			'template' => $template,
		);
	}

	/**
	 * AJAX: save settings.
	 *
	 * @return void
	 */
	public function ajax_save_settings() {
		if ( ! check_ajax_referer( self::NONCE_ACTION, 'nonce', false ) || ! current_user_can( self::CAPABILITY ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'upsell-order-bump-offer-for-woocommerce' ) ), 403 );
		}

		$settings = array(
			'enabled'        => ( isset( $_POST['enabled'] ) && 'on' === sanitize_key( wp_unslash( $_POST['enabled'] ) ) ) ? 'on' : 'off',
			'track_hover'    => ( isset( $_POST['track_hover'] ) && 'on' === sanitize_key( wp_unslash( $_POST['track_hover'] ) ) ) ? 'on' : 'off',
			'sample_rate'    => isset( $_POST['sample_rate'] ) ? min( 100, max( 1, absint( $_POST['sample_rate'] ) ) ) : 100,
			// Clamp before absint(): absint( -3 ) would save 3.
			'retention_days' => isset( $_POST['retention_days'] ) ? min( 730, max( 1, (int) sanitize_text_field( wp_unslash( $_POST['retention_days'] ) ) ) ) : 90,
		);
		// phpcs:enable

		update_option( Upsell_Order_Bump_Heatmap_Db::SETTINGS_OPTION, $settings, false );

		wp_send_json_success( $settings );
	}

	/**
	 * AJAX: delete all data.
	 *
	 * @return void
	 */
	public function ajax_delete_all() {
		$this->verify_request();
		Upsell_Order_Bump_Heatmap_Db::truncate();
		wp_send_json_success();
	}
}
