<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      1.0.0
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/public
 */

use Automattic\WooCommerce\Utilities\OrderUtil;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/public
 * @author     WP Swings <webmaster@wpswings.com>
 */
class Upsell_Order_Bump_Offer_For_Woocommerce_Public
{


	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Upsell_Order_Bump_Offer_For_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Upsell_Order_Bump_Offer_For_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		global $post;
		$shortcode_name = 'wps_bump_offer_shortcode';
		if (null !== $post) {
			$wps_is_shortcode = has_shortcode($post->post_content, $shortcode_name);
			WC()->session->set('wps_is_shortcode', $wps_is_shortcode);
		} else {
			$wps_is_shortcode = false;
		}

		wp_enqueue_style($this->plugin_name . 'recommendated_popup', plugin_dir_url(__FILE__) . 'css/wps-recommendation-popup.css', array(), $this->version, 'all');

		if (is_checkout() || is_cart() || $wps_is_shortcode) {
			wp_enqueue_style($this->plugin_name . '_slick_css', plugin_dir_url(__FILE__) . 'css/slick.min.css', array(), $this->version, 'all');
			wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/upsell-order-bump-offer-for-woocommerce-public.css', array(), $this->version, 'all');
		}


		wp_enqueue_style($this->plugin_name . 'one-click-front', plugin_dir_url(__FILE__) . 'css/woocommerce_one_click_upsell_funnel_pro-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Upsell_Order_Bump_Offer_For_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Upsell_Order_Bump_Offer_For_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		global $post;
		$shortcode_name = 'wps_bump_offer_shortcode';
		if (null !== $post) {
			$wps_is_shortcode = has_shortcode($post->post_content, $shortcode_name);
		} else {
			$wps_is_shortcode = false;
		}
		// Only enqueue on the Checkout page.
		if (is_checkout() || is_cart() || $wps_is_shortcode) {

			$wps_is_checkout_page = false;
			$wps_popup_body_class = 'No';
			$wps_ubo_timer_evegreen_countdown = array();
			$wps_traditional_checkout = false;
			$wps_traditional_cart = false;
			// To check the checkout page is there or not for jquery.
			if ((function_exists('is_checkout') || is_checkout()) || (function_exists('is_cart') || is_cart()) || $wps_is_shortcode) {
				$wps_is_checkout_page = true;
			}

			// To Check whther checkout or cart block Start Here.

			// Get the ID of the selected checkout page from WooCommerce settings.
			$checkout_page_id = get_option('woocommerce_checkout_page_id');

			// Get the content of the checkout page.
			$checkout_page_content = get_post_field('post_content', $checkout_page_id);

			// Check if the content contains a class associated with the block editor.
			if (strpos($checkout_page_content, 'wp-block-woocommerce-checkout') !== false) {
				$wps_traditional_checkout = true;
			} else {
				$wps_traditional_checkout = false;
			}

			// Get the ID of the selected cart page from WooCommerce settings.
			$cart_page_id = get_option('woocommerce_cart_page_id');

			// Get the content of the checkout page.
			$cart_page_content = get_post_field('post_content', $cart_page_id);

			// Check if the content contains a class associated with the block editor.
			if (strpos($cart_page_content, 'wp-block-woocommerce-cart') !== false) {
				$wps_traditional_cart = true;
			} else {
				$wps_traditional_cart = false;
			}
			// To Check whther checkout or cart block End Here.

			$current_theme = wp_get_theme();
			$wps_silde_cart_plgin = false;
			if (is_plugin_active('side-cart-woocommerce/xoo-wsc-main.php')) {

				$wps_silde_cart_plgin = true;
			}


			// Enable the bump offer with or without pop-up.
			$wps_ubo_global_options = get_option('wps_ubo_global_options', wps_ubo_lite_default_global_options());
			$wps_bump_target_popup_bump = ! empty($wps_ubo_global_options['wps_bump_popup_bump_offer']) ? $wps_ubo_global_options['wps_bump_popup_bump_offer'] : 'on';
			if ('without_popup' == $wps_bump_target_popup_bump) {
				$wps_popup_body_class = 'yes';
			}

			// Enable the popup exit intent.
			$wps_bump_popup_exit_intent = ! empty($wps_ubo_global_options['wps_ubo_enable_popup_exit_intent']) ? $wps_ubo_global_options['wps_ubo_enable_popup_exit_intent'] : 'on';

			// Public Script.
			wp_enqueue_script('wps-ubo-lite-public-script', plugin_dir_url(__FILE__) . 'js/wps_ubo_lite_public_script.js', array('jquery'), $this->version, false);
			wp_enqueue_script('wps-ubo-lite-public-script-new', plugin_dir_url(__FILE__) . 'js/wps_ubo_lite_public_script_new_template.js', array('jquery'), $this->version, false);

			// Checkout Block and Cart block Comaptibility.
			$wps_ubo_global_options = get_option('wps_ubo_global_options', array());
			$bump_offer_location = ! empty($wps_ubo_global_options['wps_ubo_offer_location']) ? $wps_ubo_global_options['wps_ubo_offer_location'] : '_before_order_summary';
			$bump_cart_offer_location = ! empty($wps_ubo_global_options['wps_enable_cart_upsell_location']) ? $wps_ubo_global_options['wps_enable_cart_upsell_location'] : '';
			$bump_cart_offer_enable = ! empty($wps_ubo_global_options['wps_enable_cart_upsell_feature']) ? $wps_ubo_global_options['wps_enable_cart_upsell_feature'] : '';

			// Localizing Array.
			$local_arr = array(
				'ajaxurl'     => admin_url('admin-ajax.php'),
				'mobile_view' => wp_is_mobile(),
				'auth_nonce'  => wp_create_nonce('wps_ubo_lite_nonce'),
				'current_theme' => $current_theme->get('Name'),
				'wps_silde_cart_plgin_active' => $wps_silde_cart_plgin,
				'is_checkout_page' => $wps_is_checkout_page,
				'wps_popup_body_class' => $wps_popup_body_class,
				'wps_popup_exit_intent' => $wps_bump_popup_exit_intent,
				'wps_order_bump_location_on_checkout' => $bump_offer_location,
				'wps_order_bump_location_on_cart' => $bump_cart_offer_location,
				'wps_enable_cart_upsell' => $bump_cart_offer_enable,
				'wps_is_checkout_block_use' => $wps_traditional_checkout,
				'wps_is_cart_block_use' => $wps_traditional_cart,
			);

			// Timer Functionality starts.
			if (isset(WC()->session) && ! empty(WC()->session->get('encountered_bump_array'))) {

				$wps_upsell_bumps_list = get_option('wps_ubo_bump_list', array());

				$wps_ubo_timer_countdown   = array();

				$encountered_order_bump_id = WC()->session->get('encountered_bump_array');
				// To fetch the countdown timer for the encountered bump.
				if (! empty($encountered_order_bump_id) && ! empty($wps_upsell_bumps_list) && (is_array($encountered_order_bump_id) || is_object($encountered_order_bump_id))) {
					foreach ($encountered_order_bump_id as $key => $order_bump_id) {
						if (! empty($wps_upsell_bumps_list[$order_bump_id]['wps_ubo_offer_timer']) && 'yes' === $wps_upsell_bumps_list[$order_bump_id]['wps_ubo_offer_timer']) {
							$wps_ubo_timer_countdown[$order_bump_id] = array(
								'enabled' => 'yes',
								'counter' => ! empty($wps_upsell_bumps_list[$order_bump_id]['wps_upsell_bump_offer_timer']) ? $wps_upsell_bumps_list[$order_bump_id]['wps_upsell_bump_offer_timer'] : '',
							);
						} elseif (! empty($wps_upsell_bumps_list[$order_bump_id]['wps_evergreen_timer_switch']) && 'yes' === $wps_upsell_bumps_list[$order_bump_id]['wps_evergreen_timer_switch']) {
							$wps_ubo_timer_evegreen_countdown[$order_bump_id] = array(
								'enabled' => 'yes',
								'evegreen_counter' => ! empty($wps_upsell_bumps_list[$order_bump_id]['wps_upsell_bump_offer_evergreen_timer']) ? $wps_upsell_bumps_list[$order_bump_id]['wps_upsell_bump_offer_evergreen_timer'] : '',
							);
						}
					}
				} elseif (empty($encountered_order_bump_id)) {
					$encountered_order_bump_id = 'reload';
				}
			}
			if (! empty($encountered_order_bump_id)) {
				$local_arr['check_if_reload'] = $encountered_order_bump_id;
			} else {
				$local_arr['check_if_reload'] = 'reload';
			}
			if (! empty($wps_ubo_timer_countdown)) {
				$local_arr['timer'] = $wps_ubo_timer_countdown;
			}
			$local_arr['evergreen_timer'] = $wps_ubo_timer_evegreen_countdown;
			// Timer Functionality ends.

			wp_localize_script(
				'wps-ubo-lite-public-script',
				'wps_ubo_lite_public',
				$local_arr
			);
			wp_localize_script(
				'wps-ubo-lite-public-script-new',
				'wps_ubo_lite_public_new',
				$local_arr
			);
			// Do not work in mobile-view mode.
			if (! wp_is_mobile()) {

				wp_enqueue_script('zoom-script', plugins_url('/js/zoom-script.js', __FILE__), array('jquery'), $this->version, true);
			}

			wp_enqueue_script('script_slick_js', plugins_url('/js/slick.min.js', __FILE__), array('jquery'), $this->version, true);
		}
		if (is_product()) {
			$wps_plugin_list = get_option('active_plugins');
			$wps_is_pro_active = false;
			$wps_plugin = 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php';
			if (in_array($wps_plugin, $wps_plugin_list)) {
				$wps_is_pro_active = true;
			}

			$wps_is_recommendation_enable = get_post_meta(get_the_ID(), 'is_recommendation_enable');
			$wps_selected_recommendated_product = get_post_meta(get_the_ID(), 'wps_recommendated_product_ids');

			if (! empty($wps_is_recommendation_enable) && (true == $wps_is_pro_active)) {
				if (('yes' == $wps_is_recommendation_enable[0]) && isset($wps_is_recommendation_enable[0]) && ! empty($wps_selected_recommendated_product) && is_array($wps_selected_recommendated_product) && isset($wps_selected_recommendated_product)) {
					// Localizing Array.
					$local_arr = array(
						'ajaxurl'     => admin_url('admin-ajax.php'),
						'mobile_view' => wp_is_mobile(),
						'auth_nonce'  => wp_create_nonce('wps_ubo_lite_nonce_recommend'),
						'product_id'  => get_the_ID(),
					);

					// Public facing regarding popup.
					wp_enqueue_script('wps-ubo-lite-public-script-for-recommdation', plugin_dir_url(__FILE__) . 'js/wps_ubo_lite_recommdation_popup.js', array('jquery'), $this->version, false);
					wp_localize_script(
						'wps-ubo-lite-public-script-for-recommdation',
						'wps_ubo_lite_public_recommendated',
						$local_arr
					);
				}
			}
		}

		if (is_cart()) {
			$wps_plugin_list = get_option('active_plugins');
			$wps_is_pro_active = false;
			$wps_plugin = 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php';
			if (in_array($wps_plugin, $wps_plugin_list)) {
				$wps_is_pro_active = true;
			}

			// Localizing Array.
			$local_arr = array(
				'ajaxurl'     => admin_url('admin-ajax.php'),
				'auth_nonce'  => wp_create_nonce('wps_ubo_lite_nonce_recommend'),
			);

			if (true == $wps_is_pro_active) {
				// Public facing regarding popup.
				wp_enqueue_script('wps-ubo-lite-public-script-for-cart', plugin_dir_url(__FILE__) . 'js/wps_ubo_lite_public_cart_script.js', array('jquery'), $this->version, false);
				wp_localize_script(
					'wps-ubo-lite-public-script-for-cart',
					'wps_ubo_lite_public_cart',
					$local_arr
				);
			}
		}

		// JS Enqueue For The Product Page.
		// Localizing Array.
		$local_arr = array(
			'ajaxurl'     => admin_url('admin-ajax.php'),
			'mobile_view' => wp_is_mobile(),
			'auth_nonce'  => wp_create_nonce('wps_ubo_lite_nonce_recommend'),
			'product_id'  => get_the_ID(),
		);

		// Public facing regarding popup.
		wp_enqueue_script('wps-ubo-lite-public-script-for-fbt', plugin_dir_url(__FILE__) . 'js/wps_ubo_lite_fbt.js', array('jquery'), $this->version, false);
		wp_localize_script(
			'wps-ubo-lite-public-script-for-fbt',
			'wps_ubo_lite_public_fbt',
			$local_arr
		);


		//Js For One CLick Upsell Start.
		wp_enqueue_script('wps-upsell-sweet-alert-js', plugin_dir_url(__FILE__) . 'js/sweet-alert.js', array(), '2.1.2', false);


		wp_enqueue_script('woocommerce-one-click-upsell-public-script', plugin_dir_url(__FILE__) . 'js/woocommerce-oneclick-upsell-funnel-public.js', array('jquery'), $this->version, true);

		$show_upsell_loader = false;

		// Add Upsell loader only when Live Offer or admin view.
		if ($this->validate_shortcode()) {

			$show_upsell_loader    = true;
			$upsell_global_options = get_option('wps_upsell_lite_global_options', array());

			$upsell_loader_redirect_link = ! empty($upsell_global_options['upsell_actions_message']) ? sanitize_text_field($upsell_global_options['upsell_actions_message']) : '';
		}


		wp_localize_script(
			'woocommerce-one-click-upsell-public-script',
			'wps_upsell_public',
			array(
				'alert_preview_title'    => esc_html__('One Click Upsell', 'woo-one-click-upsell-funnel'),
				'alert_preview_content'  => esc_html__('This is Preview Mode, please checkout to see Live Offers.', 'woo-one-click-upsell-funnel'),
				'show_upsell_loader'     => $show_upsell_loader,
				'upsell_actions_message' => ! empty($show_upsell_loader) ? $upsell_loader_redirect_link : '',
			)
		);

		$secure_nonce      = wp_create_nonce('wps-upsell-auth-nonce');
		$id_nonce_verified = wp_verify_nonce($secure_nonce, 'wps-upsell-auth-nonce');

		if ($id_nonce_verified) {
			$is_upsell_page = '';
			if (isset($_GET['ocuf_ns'])) {
				$is_upsell_page = true;
			}
		}

		if (! empty($is_upsell_page)) {
			$upsell_global_options = get_option('wps_upsell_lite_global_options', array());
			$upsell_skip_function = ! empty($upsell_global_options['wps_wocuf_pro_skip_exit_intent_toggle']) ? sanitize_text_field($upsell_global_options['wps_wocuf_pro_skip_exit_intent_toggle']) : '';
			$upsell_exit_intent_message = __('Enhance your shopping experience! Explore additional products at a discount before you exit.',  'woo-one-click-upsell-funnel');

			wp_enqueue_script('woocommerce-one-click-upsell-public-exit-intent-script', plugin_dir_url(__FILE__) . 'js/woocommerce-one-click-upsell-funnel-public-exit-intent_lite.js', array('jquery'), $this->version, true);

			wp_localize_script(
				'woocommerce-one-click-upsell-public-exit-intent-script',
				'wps_upsell_public_exit',
				array(
					'ajaxurl'                => admin_url('admin-ajax.php'),
					'nonce'                  => wp_create_nonce('wps_wocuf_nonce'),
					'skip_enabled'           => $upsell_skip_function,
					'skip_message'           => $upsell_exit_intent_message,
				)
			);
		}
	}

	/**
	 * Add custom hook to show offer bump after payment gateways but before
	 * terms as one is not provided by Woocommerce.
	 *
	 * @param    string $template_name       Get checkout page template.
	 * @param    string $template_path       Get checkout page template path.
	 * @since    1.0.0
	 */
	public function add_bump_offer_custom_hook($template_name, $template_path)
	{

		if ('checkout/terms.php' === $template_name) {

			do_action('wps_ubo_after_pg_before_terms');

			remove_action('woocommerce_before_template_part', array($this, 'add_bump_offer_custom_hook'), 10);
		}
	}

	/**
	 * Register the Filter for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function show_offer_bump()
	{
		/**
		 * This adds the bump to checkout page.
		 */
		if (function_exists('is_checkout') && is_checkout()) {

			require_once plugin_dir_path(__FILE__) . '/partials/upsell-order-bump-offer-for-woocommerce-public-display.php';
		}
	}

	/**
	 * Register the shortcode for the public-facing side of the site.
	 *
	 * @since    2.2.9
	 */
	public function wps_show_offer_bump_shortcode()
	{
		add_shortcode('wps_bump_offer_shortcode', array($this, 'wps_bump_offer_shortcode_callback'));
	}

	/**
	 * Shortcode Callback for the public-facing side of the site.
	 *
	 * @since    2.2.9
	 */
	public function wps_bump_offer_shortcode_callback()
	{
		if (! is_admin()) {
			ob_start();
			require_once plugin_dir_path(__FILE__) . '/partials/upsell-order-bump-offer-for-woocommerce-public-display.php';
?>
		<?php
			return ob_get_clean();
		}
	}

	/**
	 * Register the Filter for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function show_offer_bump_on_cart()
	{
		/**
		 * This adds the bump to cart page.
		 */
		if (function_exists('is_cart') && is_cart()) {
			$wps_ubo_global_options = get_option('wps_ubo_global_options', array());

			// Check cart upsell enabled.
			$wps_enable_cart_upsell_feature = ! empty($wps_ubo_global_options['wps_enable_cart_upsell_feature']) ? $wps_ubo_global_options['wps_enable_cart_upsell_feature'] : 'on';
			if ('on' == $wps_enable_cart_upsell_feature) {
				require_once plugin_dir_path(__FILE__) . '/partials/upsell-order-bump-offer-for-woocommerce-public-display.php';
			}
		}
	}

	/**
	 * Add bump offer product to cart ( checkbox ).
	 *
	 * @since    1.0.0
	 */
	public function add_offer_in_cart()
	{
		// Nonce verification.
		check_ajax_referer('wps_ubo_lite_nonce', 'nonce');

		// The id of the offer to be added.
		$bump_product_id = ! empty($_POST['id']) ? sanitize_text_field(wp_unslash($_POST['id'])) : '';

		$bump_discounted_price = ! empty($_POST['discount']) ? sanitize_text_field(wp_unslash($_POST['discount'])) : '';
		$bump_index            = ! empty($_POST['bump_index']) ? sanitize_text_field(wp_unslash($_POST['bump_index'])) : '';
		$bump_target_cart_key  = ! empty($_POST['bump_target_cart_key']) ? sanitize_text_field(wp_unslash($_POST['bump_target_cart_key'])) : '';
		$order_bump_id         = ! empty($_POST['order_bump_id']) ? sanitize_text_field(wp_unslash($_POST['order_bump_id'])) : '';
		$smart_offer_upgrade   = ! empty($_POST['smart_offer_upgrade']) ? sanitize_text_field(wp_unslash($_POST['smart_offer_upgrade'])) : '';
		$form_data             = ! empty($_POST['form_data']) ? map_deep(wp_unslash($_POST['form_data']), 'sanitize_text_field') : array();

		// Quantity of product.
		$wps_qty_variable = ! empty($_POST['wps_qty_variable']) ? sanitize_text_field(wp_unslash($_POST['wps_qty_variable'])) : '1';

		$active_plugin = get_option('active_plugins', false);
		if (in_array('woo-gift-cards-lite/woocommerce_gift_cards_lite.php', $active_plugin, true) && wps_ubo_lite_if_pro_exists() && ! empty($form_data)) {
			$gift_card_form = array(
				'wps_wgm_to_email'      => '',
				'wps_wgm_from_name'     => '',
				'wps_wgm_message'       => '',
				'delivery_method'       => '',
				'wps_wgm_price'         => '',
				'wps_wgm_selected_temp' => '',
			);
			$gift_card_data = get_post_meta($bump_product_id, 'wps_wgm_pricing');
			foreach ($gift_card_data as $key => $value) {
				$gift_card_form = array_merge(
					$gift_card_form,
					array(
						'wps_wgm_price'         => $value['default_price'],
						'wps_wgm_selected_temp' => $value['template'][0],
					)
				);
			}

			foreach ($form_data as $key => $value) {
				if ('from' === $value['name']) {
					$gift_card_form = array_merge($gift_card_form, array('wps_wgm_from_name' => $value['value']));
				} elseif ('gift message' === $value['name']) {
					$gift_card_form = array_merge($gift_card_form, array('wps_wgm_message' => $value['value']));
				} elseif ('mail to recepient' === $value['name']) {
					$gift_card_form = array_merge($gift_card_form, array('wps_wgm_to_email' => $value['value']));
				}
			}

			$cart_item_data = array(
				'wps_ubo_offer_product' => true,
				'wps_ubo_offer_index'   => $bump_index,
				'wps_ubo_bump_id'       => $order_bump_id,
				'wps_discounted_price'  => $bump_discounted_price,
				'wps_ubo_target_key'    => $bump_target_cart_key,
				'flag_' . uniqid()      => true,
				'wps_ubo_meta_form'     => $form_data,
				'product_meta'          => array('meta_data' => $gift_card_form),
			);
		} else {
			$cart_item_data = array(
				'wps_ubo_offer_product' => true,
				'wps_ubo_offer_index'   => $bump_index,
				'wps_ubo_bump_id'       => $order_bump_id,
				'wps_discounted_price'  => $bump_discounted_price,
				'wps_ubo_target_key'    => $bump_target_cart_key,
				'flag_' . uniqid()      => true,
				'wps_ubo_meta_form'     => $form_data,
			);
		}

		$_product = wc_get_product($bump_product_id);

		$added = 'added';

		if (wps_ubo_lite_reload_required_after_adding_offer($_product)) {

			$added = 'subs_reload';
		}

		if (! empty($_product) && $_product->has_child()) {

			// Generate default price html.
			$bump_price_html = wps_ubo_lite_custom_price_html($bump_product_id, $bump_discounted_price);

			$response = array(
				'key'     => 'true',
				'message' => $bump_price_html,
			);

			// Now we have to add a pop up.
			echo wp_json_encode($response);
		} elseif (! empty($_product)) {

			// If simple product or any single variations.
			// Add to cart the same.

			$bump_offer_cart_item_key = WC()->cart->add_to_cart($bump_product_id, $quantity = $wps_qty_variable, $variation_id = 0, $variation = array(), $cart_item_data);

			// Add Order Bump Offer Accept Count for the respective Order Bump.
			$sales_by_bump = new Wps_Upsell_Order_Bump_Report_Sales_By_Bump($order_bump_id);
			$sales_by_bump->add_offer_accept_count();

			if (is_plugin_active('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php')) {

				$sales_by_bump->add_offer_accept_count_pro();
			}

			WC()->session->set('bump_offer_status', 'added');
			WC()->session->set("bump_offer_status_$bump_index", $bump_offer_cart_item_key);

			// Smart offer Upgrade.
			if (wps_ubo_lite_if_pro_exists() && 'yes' === $smart_offer_upgrade) {

				// Get all saved bumps.
				$wps_ubo_bump_callback          = Upsell_Order_Bump_Offer_For_Woocommerce::$wps_upsell_bump_list_callback_function;
				$wps_ubo_offer_array_collection = Upsell_Order_Bump_Offer_For_Woocommerce::$wps_ubo_bump_callback();

				$encountered_bump_array = ! empty($wps_ubo_offer_array_collection[$order_bump_id]) ? $wps_ubo_offer_array_collection[$order_bump_id] : array();

				$wps_upsell_bump_replace_target = ! empty($encountered_bump_array['wps_ubo_offer_replace_target']) ? $encountered_bump_array['wps_ubo_offer_replace_target'] : '';

				if ('yes' === $wps_upsell_bump_replace_target && class_exists('Upsell_Order_Bump_Offer_For_Woocommerce_Pro') && method_exists('Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'wps_ubo_upgrade_offer')) {

					Upsell_Order_Bump_Offer_For_Woocommerce_Pro::wps_ubo_upgrade_offer($bump_offer_cart_item_key, $bump_target_cart_key);
				}
			}

			/**
			 * After v1.3.0 (pro)
			 * Apply Exclusive Limits in case of pro is active.
			 */
			if (wps_ubo_lite_if_pro_exists()) {
				if (class_exists('Upsell_Order_Bump_Offer_For_Woocommerce_Pro')) {
					if (method_exists('Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'wps_ubo_manage_exclusive_limit')) {
						$single_bump_id = str_replace('index_', '', $bump_index);
						if (true === Upsell_Order_Bump_Offer_For_Woocommerce_Pro::wps_ubo_manage_exclusive_limit($single_bump_id)) {
							$associations = WC()->session->get('bump_offer_associations');

							if (null !== $associations) {
								$associations .= '___';
							} else {
								$associations = '';
							}

							$associations .= $bump_index;

							WC()->session->set('bump_offer_associations', $associations);
						}
					}
				}
			}

			echo wp_json_encode($added);
		}

		wp_die();
	}

	/**
	 * Mini Cart Price( checkbox ).
	 *
	 * @param string $cart_item_data is an output.
	 * @param object $cart_item is cart item object.
	 * @param string $cart_item_key is key.
	 * @return string
	 * @since    1.0.0
	 */
	public function change_mini_cart_content($cart_item_data, $cart_item, $cart_item_key)
	{

		if (! empty($cart_item['wps_ubo_offer_index'])) {
			if (! empty($cart_item['wps_discounted_price'])) {

				$product_id = ! empty($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id'];

				$price_discount = wps_ubo_lite_custom_price_html($product_id, $cart_item['wps_discounted_price'], 'price');
				if (is_wps_role_based_pricing_active()) {
					if ((-1 < strpos($cart_item['wps_discounted_price'], 'no_disc'))) {
						$prod_obj   = wc_get_product($product_id);
						$prod_type  = $prod_obj->get_type();
						$bump_price = wps_mrbpfw_role_based_price($prod_obj->get_price(), $prod_obj, $prod_type);
						$bump_price = wp_strip_all_tags(str_replace(get_woocommerce_currency_symbol(), '', $bump_price));

						$cart_item['data']->set_price($bump_price);
					} else {
						$cart_item['data']->set_price($price_discount);
					}
				} else {
					$cart_item['data']->set_price($price_discount);
				}
			}
		}
		return $cart_item['data'];
	}


	/**
	 * Remove bump offer product to cart.
	 *
	 * @since    1.0.0
	 */
	public function remove_offer_in_cart()
	{
		// Nonce verification.
		check_ajax_referer('wps_ubo_lite_nonce', 'nonce');

		$bump_index = ! empty($_POST['bump_index']) ? sanitize_text_field(wp_unslash($_POST['bump_index'])) : '';

		if (null !== WC()->session->get("bump_offer_status_$bump_index")) {

			WC()->cart->remove_cart_item(WC()->session->get("bump_offer_status_$bump_index"));
		}

		WC()->session->__unset("bump_offer_status_$bump_index");

		/**
		 * After v1.3.0 (pro)
		 * Apply Exclusive Limits in case of pro is active.
		 */
		if (wps_ubo_lite_if_pro_exists()) {

			if (class_exists('Upsell_Order_Bump_Offer_For_Woocommerce_Pro')) {
				if (method_exists('Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'wps_ubo_manage_exclusive_limit')) {
					$single_bump_id = str_replace('index_', '', $bump_index);
					if (true === Upsell_Order_Bump_Offer_For_Woocommerce_Pro::wps_ubo_manage_exclusive_limit($single_bump_id)) {
						$associations = WC()->session->get('bump_offer_associations');
						$associations = array_unique(explode('___', $associations));
						$key          = array_search($bump_index, $associations, true);
						unset($associations[$key]);
						WC()->session->set('bump_offer_associations', implode('___', $associations));
					}
				}
			}
		}

		echo wp_json_encode('removed');

		wp_die();
	}


	/**
	 * Search selected variation.
	 *
	 * @since    1.0.0
	 */
	public function search_variation_id_by_select()
	{
		// Nonce verification.
		check_ajax_referer('wps_ubo_lite_nonce', 'nonce');

		$bump_offer_id = ! empty($_POST['id']) ? sanitize_text_field(wp_unslash($_POST['id'])) : '';  // variation parent.

		$bump_offer_discount = ! empty($_POST['discount']) ? sanitize_text_field(wp_unslash($_POST['discount'])) : '';

		$attributes_selected_options = ! empty($_POST['attributes_selected_options']) ? array_map('sanitize_text_field', wp_unslash($_POST['attributes_selected_options'])) : array();

		// Got all values to search for variation id from selected attributes.
		$product = wc_get_product($bump_offer_id);

		if (empty($product)) {

			echo wp_json_encode(esc_html__('Product Not Found.', 'upsell-order-bump-offer-for-woocommerce'));
			return;
		}

		$product_data_store         = new WC_Product_Data_Store_CPT();
		$variation_id               = $product_data_store->find_matching_product_variation($product, $attributes_selected_options);
		$selected_variation_product = wc_get_product($variation_id);

		// Image to reflect on select change.
		$image_id = get_post_thumbnail_id($variation_id);

		if (! empty($image_id)) {

			$html           = wc_get_gallery_image_html($image_id, true);
			$bump_var_image = apply_filters('woocommerce_single_product_image_thumbnail_html', $html, $image_id);
		} else {

			// If no variation image is present show default one.
			$bump_var_image = wps_ubo_lite_get_bump_image($bump_offer_id);
		}

		// Variation id will be empty if selected variation is not available.
		if (empty($variation_id) || empty($selected_variation_product)) {

			$response = array(
				'key'     => 'not_available',
				'message' => '<p class="stock out-of-stock">' . esc_html__('Sorry, this variation is not available.', 'upsell-order-bump-offer-for-woocommerce') . '</p>',
				'image'   => $bump_var_image,
			);
			echo wp_json_encode($response);
		} else if (! empty($variation_id) || ! empty($selected_variation_product)) {

			// Check if in stock?
			if (! $selected_variation_product->is_in_stock()) {

				// Out of stock.
				$response = array(

					'key'     => 'stock',
					'message' => '<p class="stock out-of-stock">' . esc_html__('Out of stock.', 'upsell-order-bump-offer-for-woocommerce') . '</p>',
					'image'   => $bump_var_image,
				);

				echo wp_json_encode($response);
			} else {

				$response = array(
					'key'     => $variation_id,
					'message' => wps_ubo_lite_custom_price_html($variation_id, $bump_offer_discount),
					'image'   => $bump_var_image,
				);

				echo wp_json_encode($response);
			}
		}

		wp_die();
	}

	/**
	 * Add selected variation as bump offer product to cart ( by button )
	 *
	 * @since    1.0.0
	 */
	public function add_variation_offer_in_cart()
	{
		check_ajax_referer('wps_ubo_lite_nonce', 'nonce');

		// Contains selected variation ID.
		$variation_id = ! empty($_POST['id']) ? sanitize_text_field(wp_unslash($_POST['id'])) : '';

		// Contains parent variable ID.
		$variation_parent_id = ! empty($_POST['parent_id']) ? sanitize_text_field(wp_unslash($_POST['parent_id'])) : '';

		// Contains bump discount.
		$bump_offer_discount = ! empty($_POST['discount']) ? sanitize_text_field(wp_unslash($_POST['discount'])) : '';

		// Contains target cart key.
		$bump_target_cart_key = ! empty($_POST['bump_target_cart_key']) ? sanitize_text_field(wp_unslash($_POST['bump_target_cart_key'])) : '';

		$bump_index = ! empty($_POST['bump_index']) || '0' === (string) sanitize_text_field(wp_unslash($_POST['bump_index'])) ? sanitize_text_field(wp_unslash($_POST['bump_index'])) : '';

		$order_bump_id       = ! empty($_POST['order_bump_id']) ? sanitize_text_field(wp_unslash($_POST['order_bump_id'])) : '';
		$smart_offer_upgrade = ! empty($_POST['smart_offer_upgrade']) ? sanitize_text_field(wp_unslash($_POST['smart_offer_upgrade'])) : '';
		$form_data           = ! empty($_POST['form_data']) ? map_deep(wp_unslash($_POST['form_data']), 'sanitize_text_field') : array();

		// variation product data.
		$wps_orderbump_any_variation = ! empty($_POST['wps_orderbump_any_variation']) ? map_deep(wp_unslash($_POST['wps_orderbump_any_variation']), 'sanitize_text_field') : array();

		// Quantity of product.
		$wps_qty_variable = ! empty($_POST['wps_qty_variable']) ? sanitize_text_field(wp_unslash($_POST['wps_qty_variable'])) : '1';

		// Now safe to add to cart.
		$cart_item_data = array(
			'wps_ubo_offer_product' => true,
			'wps_discounted_price'  => $bump_offer_discount,
			'flag_' . uniqid()      => true,
			'wps_ubo_offer_index'   => 'index_' . $bump_index,
			'wps_ubo_bump_id'       => $order_bump_id,
			'wps_ubo_target_key'    => $bump_target_cart_key,
			'wps_ubo_meta_form'     => $form_data,
		);

		$_product = wc_get_product($variation_id);

		$added = 'added';

		if (wps_ubo_lite_reload_required_after_adding_offer($_product)) {

			$added = 'subs_reload';
		}

		$bump_offer_cart_item_key = WC()->cart->add_to_cart($variation_parent_id, $quantity = $wps_qty_variable, $variation_id, $variation = $wps_orderbump_any_variation, $cart_item_data);

		// Add Order Bump Offer Accept Count for the respective Order Bump.
		$sales_by_bump = new Wps_Upsell_Order_Bump_Report_Sales_By_Bump($order_bump_id);
		$sales_by_bump->add_offer_accept_count();

		if (is_plugin_active('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php')) {

			$sales_by_bump->add_offer_accept_count_pro();
		}

		WC()->session->set("bump_offer_status_index_$bump_index", $bump_offer_cart_item_key);

		WC()->session->set('bump_offer_status', 'added');

		// Smart offer Upgrade.
		if (wps_ubo_lite_if_pro_exists() && 'yes' === $smart_offer_upgrade) {

			// Get all saved bumps.
			$wps_ubo_bump_callback          = Upsell_Order_Bump_Offer_For_Woocommerce::$wps_upsell_bump_list_callback_function;
			$wps_ubo_offer_array_collection = Upsell_Order_Bump_Offer_For_Woocommerce::$wps_ubo_bump_callback();

			$encountered_bump_array = ! empty($wps_ubo_offer_array_collection[$order_bump_id]) ? $wps_ubo_offer_array_collection[$order_bump_id] : array();

			$wps_upsell_bump_replace_target = ! empty($encountered_bump_array['wps_ubo_offer_replace_target']) ? $encountered_bump_array['wps_ubo_offer_replace_target'] : '';

			if ('yes' === $wps_upsell_bump_replace_target && class_exists('Upsell_Order_Bump_Offer_For_Woocommerce_Pro') && method_exists('Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'wps_ubo_upgrade_offer')) {

				Upsell_Order_Bump_Offer_For_Woocommerce_Pro::wps_ubo_upgrade_offer($bump_offer_cart_item_key, $bump_target_cart_key);
			}
		}
		echo wp_json_encode($added);
		wp_die();
	}

	/**
	 * Disabling the offer quantity for bump product in Cart page.
	 *
	 * @param    string $product_quantity       Quantity at cart page.
	 * @param    string $cart_item_key          Cart item key.
	 * @since    1.0.0
	 */
	public function disable_quantity_bump_product_in_cart($product_quantity, $cart_item_key)
	{

		if (null !== WC()->session->get('bump_offer_status')) {

			$cart_item = WC()->cart->cart_contents[$cart_item_key];

			if (! empty($cart_item['wps_ubo_offer_product'])) {

				// For Bump product allowed quantity is one.
				$product_quantity = 1;
				return $product_quantity;
			}
		}

		return $product_quantity;
	}

	/**
	 * When cart item product remove is triggered.
	 *
	 * Remove encountered session to refetch order bumps when any product is removed from cart.
	 *
	 * Removal of target and bump offer product is handled here.
	 *
	 * @param   string $key_to_be_removed      The cart item key which is being removed.
	 * @param   object $cart_object            The cart object.
	 * @since   1.0.0
	 */
	public function after_remove_product($key_to_be_removed, $cart_object)
	{

		if (empty($key_to_be_removed) || empty(WC()->cart->cart_contents)) {

			return;
		}

		$current_cart_item = ! empty($cart_object->cart_contents[$key_to_be_removed]) ? $cart_object->cart_contents[$key_to_be_removed] : '';

		// When the removed product is an Offer product.
		if (! empty($current_cart_item['wps_ubo_offer_product'])) {

			// Hide Undo notice for Offer Products.
			add_filter('woocommerce_cart_item_removed_notice_type', '__return_null');

			$bump_index = ! empty($current_cart_item['wps_ubo_offer_index']) ? $current_cart_item['wps_ubo_offer_index'] : '';
			$bump_id    = ! empty($current_cart_item['wps_ubo_bump_id']) ? $current_cart_item['wps_ubo_bump_id'] : '';

			// Add Order Bump Offer Remove Count for the respective Order Bump.
			$sales_by_bump = new Wps_Upsell_Order_Bump_Report_Sales_By_Bump($bump_id);
			$sales_by_bump->add_offer_remove_count();

			// When the removed product is a Smart Offer Upgrade - Offer product.
			if (! empty($current_cart_item['wps_ubo_sou_offer']) && ! empty($current_cart_item['wps_ubo_target_key'])) {

				// Restore Target product.
				if (class_exists('Upsell_Order_Bump_Offer_For_Woocommerce_Pro') && method_exists('Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'wps_ubo_retrieve_target')) {

					Upsell_Order_Bump_Offer_For_Woocommerce_Pro::wps_ubo_retrieve_target($current_cart_item['wps_ubo_target_key']);
				}
			}

			/**
			 * Unset order bump params from WC cart and index session for the removed offer product.
			 * Do not unset other session variables.
			 */

			/**
			 * Unset order bump params from WC cart to prevent Offer rollback on undo.
			 * Commenting : Unset in WC() Cart doesn't work for the current cart item
			 * that is being removed, works with other cart items though.
			 */
			WC()->session->__unset('bump_offer_status_' . $bump_index);

			// As offer product is removed so no need remove encountered session to refetch order bumps.
			return;
		} elseif (! empty($current_cart_item['wps_ubo_sou_target'])) { // When the removed product is a Smart Offer Upgrade - Target product.

			// Do nothing.
			return;
		} elseif (! empty($cart_object->cart_contents) && is_array($cart_object->cart_contents)) { // When the removed product is a Normal or Target product.

			// Global settings.
			$wps_ubo_global_options = get_option('wps_ubo_global_options', wps_ubo_lite_default_global_options());

			foreach ($cart_object->cart_contents as $cart_offer_item_key => $cart_offer_item) {

				// Check Offer product and Target keys.
				if (! empty($cart_offer_item['wps_ubo_offer_product']) && ! empty($cart_offer_item['wps_ubo_target_key'])) {

					// When Target key matches means Removed product is a Target product.
					if ($cart_offer_item['wps_ubo_target_key'] === $key_to_be_removed) {

						// If the same target key is found in order cart item, Handle offer product too.
						$bump_index = ! empty($cart_offer_item['wps_ubo_offer_index']) ? $cart_offer_item['wps_ubo_offer_index'] : '';
						$bump_id    = ! empty($cart_offer_item['wps_ubo_bump_id']) ? $cart_offer_item['wps_ubo_bump_id'] : '';

						$sales_by_bump = new Wps_Upsell_Order_Bump_Report_Sales_By_Bump($bump_id);

						// When Target dependency is set to Remove Offer product.
						if (! empty($wps_ubo_global_options['wps_ubo_offer_removal']) && 'yes' === $wps_ubo_global_options['wps_ubo_offer_removal']) {

							/**
							 * Remove Target dependent Offer product.
							 * Unset order bump params from WC cart and index session for the dependent offer product.
							 * Do not unset other session variables.
							 */
							if (! empty($cart_offer_item_key)) {

								// Unset order bump params from WC cart to prevent Offer rollback on undo.
								unset(WC()->cart->cart_contents[$cart_offer_item_key]['wps_ubo_offer_product']);
								unset(WC()->cart->cart_contents[$cart_offer_item_key]['wps_ubo_offer_index']);
								unset(WC()->cart->cart_contents[$cart_offer_item_key]['wps_ubo_bump_id']);
								unset(WC()->cart->cart_contents[$cart_offer_item_key]['wps_discounted_price']);
								unset(WC()->cart->cart_contents[$cart_offer_item_key]['wps_ubo_target_key']);

								// Remove the Offer product.
								unset(WC()->cart->cart_contents[$cart_offer_item_key]);

								// Add Order Bump Offer Remove Count for the respective Order Bump.
								$sales_by_bump->add_offer_remove_count();

								WC()->session->__unset('bump_offer_status_' . $bump_index);
							}
						} else if (empty($wps_ubo_global_options['wps_ubo_offer_removal']) && 'yes' !== $wps_ubo_global_options['wps_ubo_offer_removal']) { // When Target dependency is set to Keep Offer product.

							/**
							 * Convert Target dependent Offer product into Normal product.
							 * Unset order bump params from WC cart and index session for the dependent offer product.
							 * Do not unset other session variables.
							 */
							if (! empty($cart_offer_item_key)) {

								// Convert Offer product to normal product.
								unset(WC()->cart->cart_contents[$cart_offer_item_key]['wps_ubo_offer_product']);
								unset(WC()->cart->cart_contents[$cart_offer_item_key]['wps_ubo_offer_index']);
								unset(WC()->cart->cart_contents[$cart_offer_item_key]['wps_ubo_bump_id']);
								unset(WC()->cart->cart_contents[$cart_offer_item_key]['wps_discounted_price']);
								unset(WC()->cart->cart_contents[$cart_offer_item_key]['wps_ubo_target_key']);

								// Add Order Bump Offer Remove Count for the respective Order Bump.
								$sales_by_bump->add_offer_remove_count();

								WC()->session->__unset('bump_offer_status_' . $bump_index);
							}
						}
					}
				}
			}
		}

		// Remove encountered session to refetch order bumps.
		wps_ubo_destroy_encountered_session();
	}

	/**
	 * Change price at last for bump offer product.
	 *
	 * @param   object $cart_object            The cart object.
	 * @since    1.0.0
	 */
	public function woocommerce_custom_price_to_cart_item($cart_object)
	{

		if (! WC()->session->__isset('reload_checkout')) {

			foreach ($cart_object->cart_contents as $key => $value) {

				if (! empty($value['wps_discounted_price'])) {

					$product_id = ! empty($value['variation_id']) ? $value['variation_id'] : $value['product_id'];

					$price_discount = wps_ubo_lite_custom_price_html($product_id, $value['wps_discounted_price'], 'price');
					if (is_wps_role_based_pricing_active()) {
						if ((-1 < strpos($value['wps_discounted_price'], 'no_disc'))) {
							$prod_obj   = wc_get_product($product_id);
							$prod_type  = $prod_obj->get_type();
							$bump_price = wps_mrbpfw_role_based_price($prod_obj->get_price(), $prod_obj, $prod_type);
							$bump_price = wp_strip_all_tags(str_replace(get_woocommerce_currency_symbol(), '', $bump_price));

							$value['data']->set_price($bump_price);
						} else {
							$value['data']->set_price($price_discount);
						}
					} else {
						$value['data']->set_price($price_discount);
					}
				}
			}
		}
	}

	/**
	 * Adds custom CSS to site.
	 *
	 * @since    1.0.2
	 */
	public function global_custom_css()
	{

		global $post;
		$shortcode_name = 'wps_bump_offer_shortcode';
		if (null !== $post) {
			$wps_is_shortcode = has_shortcode($post->post_content, $shortcode_name);
		} else {
			$wps_is_shortcode = false;
		}

		// Only enqueue on the cart,shortcode and checkout page.
		if (is_cart() || is_checkout() || $wps_is_shortcode) {

			// Ignore admin, feed, robots or trackbacks.
			if (is_admin() || is_feed() || is_robots() || is_trackback()) {

				return;
			}

			$wps_ubo_global_options = get_option('wps_ubo_global_options', wps_ubo_lite_default_global_options());

			$global_custom_css = ! empty($wps_ubo_global_options['wps_ubo_offer_global_css']) ? $wps_ubo_global_options['wps_ubo_offer_global_css'] : '';

			if (empty($global_custom_css)) {

				return;
			}

		?>

			<style id="wps-ubo-global-css" type="text/css">
				<?php echo wp_kses_post(wp_unslash($global_custom_css)); ?>
			</style>

		<?php
		}
	}

	/**
	 * Adds custom JS to site.
	 *
	 * @since    1.0.2
	 */
	public function global_custom_js()
	{
		global $post;
		$shortcode_name = 'wps_bump_offer_shortcode';
		$wps_is_shortcode = false;
		if (null !== $post) {
			$wps_is_shortcode = has_shortcode($post->post_content, $shortcode_name);
		} else {
			$wps_is_shortcode = false;
		}

		// Only enqueue on the cart,shortcode and checkout page.
		if (is_cart() || is_checkout() || $wps_is_shortcode) {

			// Ignore admin, feed, robots or trackbacks.
			if (is_admin() || is_feed() || is_robots() || is_trackback()) {

				return;
			}

			$wps_ubo_global_options = get_option('wps_ubo_global_options', wps_ubo_lite_default_global_options());

			$global_custom_js = ! empty($wps_ubo_global_options['wps_ubo_offer_global_js']) ? $wps_ubo_global_options['wps_ubo_offer_global_js'] : '';

			if (empty($global_custom_js)) {

				return;
			}

		?>

			<script id="wps-ubo-global-js" type="text/javascript">
				<?php echo wp_kses_post(wp_unslash($global_custom_js)); ?>
			</script>

			<?php
		}
	}

	/**
	 * Disable quantity field for bump offer product.
	 *
	 * @param   boolean $boolean             Show/Hide.
	 * @param   object  $cart_item           The cart object.
	 * @since    1.2.0
	 */
	public function disable_quantity_field_in_aerocheckout($boolean, $cart_item)
	{

		if (! empty($cart_item['wps_ubo_offer_product'])) {

			return false;
		}

		return $boolean;
	}

	/**
	 * Hide undo notice for bump target/offer product.
	 *
	 * @param   boolean $boolean             Show/Hide.
	 * @param   object  $cart_item           The cart object.
	 * @since    1.2.0
	 */
	public function hide_undo_notice_in_aerocheckout($boolean, $cart_item)
	{

		if (! empty($cart_item['wps_ubo_offer_product'])) {

			return true;
		}

		if (! empty($cart_item['key']) && null !== WC()->session->get('encountered_bump_tarket_key_array') && is_array(WC()->session->get('encountered_bump_tarket_key_array'))) {

			if (in_array($cart_item['key'], WC()->session->get('encountered_bump_tarket_key_array'), true)) {

				return true;
			}
		}

		return $boolean;
	}

	/**
	 * Trigger order bump according to targets.
	 *
	 * @param   array $order_bump_collection   All order bump collection.
	 * @param   array $wps_ubo_global_options  All global collection.
	 * @since    1.2.0
	 */
	public static function fetch_order_bump_from_collection($order_bump_collection = array(), $wps_ubo_global_options = array())
	{

		/**
		 * Check enability of the plugin at settings page,
		 * Get all bump lists,
		 * Check for live ones and scheduled for today only,
		 * Rest leave No need to check,
		 * For live one check if target id is present and after this category check,
		 * Save the array index that is encountered and target product key.
		 */

		// Get Multiple Order Bumps limit. Default limit is 1.
		$order_bump_limit = ! empty($wps_ubo_global_options['wps_bump_order_bump_limit']) ? $wps_ubo_global_options['wps_bump_order_bump_limit'] : '1';

		$global_skip_settings = ! empty($wps_ubo_global_options['wps_bump_skip_offer']) ? $wps_ubo_global_options['wps_bump_skip_offer'] : 'yes';

		$encountered_bump_key_array   = array();
		$encountered_target_key_array = array();

		if (! empty($order_bump_collection) && is_array($order_bump_collection)) {

			foreach ($order_bump_collection as $single_bump_id => $single_bump_array) {

				if (count($encountered_bump_key_array) >= $order_bump_limit) {
					continue;
				}

				// If already encountered and saved. ( Just if happens : Worst case. )!
				if (! empty($encountered_bump_key_array) && in_array((string) $single_bump_id, $encountered_bump_key_array, true)) {
					continue;
				}

				// Check Bump status.
				$single_bump_status = ! empty($single_bump_array['wps_upsell_bump_status']) ? $single_bump_array['wps_upsell_bump_status'] : '';

				// Not live so continue.
				if ('yes' !== $single_bump_status) {
					continue;
				}

				/**
				 * Check for Bump Schedule.
				 * For earlier versions here we will get a string instaed of array.
				 */
				if (empty($single_bump_array['wps_upsell_bump_schedule'])) {

					// Could be '0' or array( '0' ).
					$single_bump_array['wps_upsell_bump_schedule'] = array('0');
				} elseif (! empty($single_bump_array['wps_upsell_bump_schedule']) && ! is_array($single_bump_array['wps_upsell_bump_schedule'])) {                // If is string means for earlier versions.

					$single_bump_array['wps_upsell_bump_schedule'] = array($single_bump_array['wps_upsell_bump_schedule']);
				}

				// Check for current day condition.
				if (! is_array($single_bump_array['wps_upsell_bump_schedule'])) {

					continue;
				}

				// Got an array. Now check.
				if (! in_array('0', $single_bump_array['wps_upsell_bump_schedule'], true) && ! in_array(gmdate('N'), $single_bump_array['wps_upsell_bump_schedule'], true)) {

					continue;
				}

				// WIW-CC : Comment - Don't check target products and categories as we always have to show the offer.
				// Check if target products or target categories are empty.
				$single_bump_target_ids = ! empty($single_bump_array['wps_upsell_bump_target_ids']) ? $single_bump_array['wps_upsell_bump_target_ids'] : array();
				$single_bump_categories = ! empty($single_bump_array['wps_upsell_bump_target_categories']) ? $single_bump_array['wps_upsell_bump_target_categories'] : array();
				$is_global_funnel       = ! empty($single_bump_array['wps_ubo_offer_global_funnel']) ? $single_bump_array['wps_ubo_offer_global_funnel'] : '';

				// When both target products or target categories are empty, continue.
				if ((empty($single_bump_target_ids) && empty($single_bump_categories)) && ('yes' !== $is_global_funnel)) {
					continue;
				}

				// Lets check for offer be present.
				if (! empty($single_bump_array['wps_upsell_bump_products_in_offer'])) {

					/**
					 * After v1.0.1 (pro)
					 * Apply smart-skip in case of pro is active.
					 */
					if (wps_ubo_lite_if_pro_exists() && is_user_logged_in()) {

						$wps_upsell_bump_global_smart_skip = ! empty($wps_ubo_global_options['wps_ubo_offer_purchased_earlier']) ? $wps_ubo_global_options['wps_ubo_offer_purchased_earlier'] : '';
						if ('yes' === $wps_upsell_bump_global_smart_skip && class_exists('Upsell_Order_Bump_Offer_For_Woocommerce_Pro')) {

							if (method_exists('Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'wps_ubo_skip_for_pre_order') && Upsell_Order_Bump_Offer_For_Woocommerce_Pro::wps_ubo_skip_for_pre_order($single_bump_array['wps_upsell_bump_products_in_offer'])) {

								continue;
							}
						}
					}

					/**
					 * After v1.3.0 (pro)
					 * Apply Exclusive Limits in case of pro is active.
					 */
					if (wps_ubo_lite_if_pro_exists()) {

						if (class_exists('Upsell_Order_Bump_Offer_For_Woocommerce_Pro')) {
							if (method_exists('Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'wps_ubo_manage_exclusive_limit')) {

								if (false === Upsell_Order_Bump_Offer_For_Woocommerce_Pro::wps_ubo_manage_exclusive_limit($single_bump_id)) {
									continue;
								}
							}
						}
					}

					/**
					 * WPS Fix :: for mutliple order bump for categories.
					 */
					if (! empty($encountered_bump_array)) {
						$encountered_bump_array = 0;
					}

					// If  target category is present.
					if (! empty($single_bump_array['wps_upsell_bump_target_ids']) && is_array($single_bump_array['wps_upsell_bump_target_ids'])) :

						// Check if these product are present in cart one by one.
						foreach ($single_bump_array['wps_upsell_bump_target_ids'] as $key => $single_target_id) {

							// Check if present in cart.
							$wps_upsell_bump_target_key = wps_ubo_lite_check_if_in_cart($single_target_id);

							// If we product is present we get the cart key.
							if (! empty($wps_upsell_bump_target_key)) {

								// Check offer product must be in stock.
								$offer_product = wc_get_product($single_bump_array['wps_upsell_bump_products_in_offer']);

								if (empty($offer_product)) {
									continue;
								}

								if ('publish' !== $offer_product->get_status()) {

									continue;
								}

								if (! $offer_product->is_in_stock()) {

									continue;
								}

								// Check if offer product is already in cart.
								if (wps_ubo_lite_already_in_cart($single_bump_array['wps_upsell_bump_products_in_offer']) && 'yes' === $global_skip_settings) {

									continue;
								}

								// If everything is good just break !!
								$encountered_bump_array = $single_bump_id;

								// Push the data on same index.
								array_push($encountered_bump_key_array, $encountered_bump_array);
								array_push($encountered_target_key_array, $wps_upsell_bump_target_key);
							}
						}

					endif;

					// 2nd foreach end for product id.

					// If target key is still empty means no target category is found yet.
					if (empty($encountered_bump_array) && ! empty($single_bump_array['wps_upsell_bump_target_categories']) && is_array($single_bump_array['wps_upsell_bump_target_categories'])) {

						foreach ($single_bump_array['wps_upsell_bump_target_categories'] as $key => $single_category_id) {

							// No target Id is found go for category,
							// Check if the category is in cart.
							$wps_upsell_bump_target_key = wps_ubo_lite_check_category_in_cart($single_category_id);

							// If we product is present we get the cart key.
							if (! empty($wps_upsell_bump_target_key)) {

								// Check offer product must be in stock.
								$offer_product = wc_get_product($single_bump_array['wps_upsell_bump_products_in_offer']);

								if (empty($offer_product)) {

									continue;
								}

								if ('publish' !== $offer_product->get_status()) {

									continue;
								}

								if (! $offer_product->is_in_stock()) {

									continue;
								}

								// Check if offer product is already in cart.
								if (wps_ubo_lite_already_in_cart($single_bump_array['wps_upsell_bump_products_in_offer']) && 'yes' === $global_skip_settings) {

									continue;
								}

								// If everything is good just break !!
								$encountered_bump_array = $single_bump_id;

								// Push the data on same index.
								array_push($encountered_bump_key_array, $encountered_bump_array);
								array_push($encountered_target_key_array, $wps_upsell_bump_target_key);
							}
						} // Second foreach for category search end.
					}
					$session = WC()->session;

					if ($session) {
						$wps_is_shortcode = WC()->session->get('wps_is_shortcode');
					}
					// If no target product/category not matched/added in bump.
					if (empty($encountered_bump_array) && ('yes' === $is_global_funnel || true == $wps_is_shortcode)) {

						// Check offer product must be in stock.
						$offer_product = wc_get_product($single_bump_array['wps_upsell_bump_products_in_offer']);

						if (empty($offer_product)) {
							continue;
						}

						if ('publish' !== $offer_product->get_status()) {

							continue;
						}

						if (! $offer_product->is_in_stock()) {

							continue;
						}

						// Check if offer product is already in cart.
						if (wps_ubo_lite_already_in_cart($single_bump_array['wps_upsell_bump_products_in_offer']) && 'yes' === $global_skip_settings) {

							continue;
						}

						// If everything is good just break !!
						$encountered_bump_array     = $single_bump_id;
						$wps_upsell_bump_target_key = 'NoTarGetProDuctIsGlobalFunnel'; // Just because for global there is not target product.

						// Push the data on same index.
						if (! empty($encountered_bump_array)) {
							array_push($encountered_bump_key_array, $encountered_bump_array);
						}
						if (! empty($wps_upsell_bump_target_key)) {
							array_push($encountered_target_key_array, $wps_upsell_bump_target_key);
						}
					}
				} else {

					// If offer product is not saved, continue.
					continue;
				}
			} // $order_bump_collection foreach end.

		} // Empty and Array Condition check for $order_bump_collection.

		if (! empty($encountered_bump_key_array) && ! empty($encountered_target_key_array)) {

			$result = array(
				'encountered_bump_array'     => $encountered_bump_key_array, // Order Bump IDs to be shown.
				'wps_upsell_bump_target_key' => $encountered_target_key_array,
			);

			return $result;
		}
	}

	/**
	 * Add functionality after WC exists/loaded.
	 *
	 * @since    1.2.0
	 */
	public function woocommerce_init_ubo_functions()
	{
		// Check woocommrece class exists.

		// Cost calculations only when the offer is added.
		add_action('woocommerce_before_calculate_totals', array($this, 'woocommerce_custom_price_to_cart_item'));

		// Disable quantity field at cart page.
		add_filter('woocommerce_cart_item_quantity', array($this, 'disable_quantity_bump_product_in_cart'), 10, 2);

		// For Aerocheckout pages.
		add_filter('wfacp_show_item_quantity', array($this, 'disable_quantity_field_in_aerocheckout'), 10, 2);

		add_filter('wfacp_show_undo_message_for_item', array($this, 'hide_undo_notice_in_aerocheckout'), 10, 2);

		// Removing offer or target product manually by cart.
		add_action('woocommerce_remove_cart_item', array($this, 'after_remove_product'), 10, 2);

		// Add meta data to order item for order review.
		add_action('woocommerce_checkout_create_order', array($this, 'add_order_item_meta'), 10);

		// Add Order Bump - Order Post meta.
		add_action('woocommerce_checkout_order_processed', array($this, 'add_bump_order_post_meta'), 10);

		// Handle Order Bump Orders on Thankyou for Success Rate and Stats.
		add_action('woocommerce_thankyou', array($this, 'report_sales_by_bump_handling'), 15);

		// Reset Order Bump session data.
		add_action('woocommerce_cart_emptied', array($this, 'reset_order_bump'), 11);
		add_action('woocommerce_thankyou', array($this, 'reset_session_variable'), 55);
		add_action('woocommerce_order_status_changed', array($this, 'wps_send_custom_email_on_order_status_change'), 10, 3);

		// Add the custom price for the recommendation product on product page.
		add_action('woocommerce_before_calculate_totals', array($this, 'wps_add_custom_price_to_cart_item'));
	}

	/**
	 * Add order item meta to bump product.
	 *
	 * @param    object $order      The order in which bump offer is added.
	 * @since    1.0.0
	 */
	public function add_order_item_meta($order)
	{

		$order_items = $order->get_items();

		if (! empty($order_items) && is_array($order_items)) {

			foreach ($order_items as $item_key => $single_order_item) {

				if (! empty($single_order_item->legacy_values['wps_ubo_offer_product'])) {

					$single_order_item->update_meta_data('is_order_bump_purchase', 'true');
				}

				if (! empty($single_order_item->legacy_values['wps_ubo_bump_id'])) {

					$single_order_item->update_meta_data('wps_order_bump_id', $single_order_item->legacy_values['wps_ubo_bump_id']);
				}

				if (! empty($single_order_item->legacy_values['wps_ubo_meta_form']) && is_array($single_order_item->legacy_values['wps_ubo_meta_form'])) {

					foreach ($single_order_item->legacy_values['wps_ubo_meta_form'] as $key => $value) {
						$single_order_item->update_meta_data($value['name'], $value['value']);
					}
				}
			}
		}
	}

	/**
	 * Hide Order Bump meta from order items.
	 *
	 * @param array $formatted_meta formatted meta.
	 *
	 * @since       1.4.0
	 */
	public function hide_order_bump_meta($formatted_meta)
	{

		if (! empty($formatted_meta) && is_array($formatted_meta)) {

			// Hide bump id meta for both Customers and Admin.
			foreach ($formatted_meta as $key => $meta) {

				if (! empty($meta->key) && 'wps_order_bump_id' === $meta->key) {

					unset($formatted_meta[$key]);
				}
			}

			// To hide bump purchase meta only for Customers place the below foreach loop in a condition( ! is_admin() ).

			foreach ($formatted_meta as $key => $meta) {

				if (! empty($meta->key) && 'is_order_bump_purchase' === $meta->key) {

					unset($formatted_meta[$key]);
				}
			}
		}

		return $formatted_meta;
	}

	/**
	 * Add Order Bump - Order Post meta.
	 *
	 * @param string $order_id order id.
	 *
	 * @since    1.0.0
	 */
	public function add_bump_order_post_meta($order_id)
	{

		$order = new WC_Order($order_id);

		$order_items = $order->get_items();

		if (! empty($order_items) && is_array($order_items)) {

			foreach ($order_items as $item_id => $single_item) {

				if (! empty(wc_get_order_item_meta($item_id, 'is_order_bump_purchase', true))) {

					if (OrderUtil::custom_orders_table_usage_is_enabled()) {
						// HPOS usage is enabled.
						$order->update_meta_data('wps_bump_order', 'true');
						$order->update_meta_data('wps_bump_order_process_sales_stats', 'true');
						$order->save();
					} else {
						// Add post meta as this is a Order Bump order.
						update_post_meta($order_id, 'wps_bump_order', 'true');

						// Add post meta for processing Success Rate and Stats on Thankyou page.
						update_post_meta($order_id, 'wps_bump_order_process_sales_stats', 'true');
					}
					break;
				}
			}
		}
	}

	/**
	 * Handle Order Bump Orders on Thankyou for Success Rate and Stats.
	 *
	 * @param string $order_id order id.
	 *
	 * @since    1.4.0
	 */
	public function report_sales_by_bump_handling($order_id)
	{

		if (! $order_id) {

			return;
		}
		// Get Order Object.
		$order = new WC_Order($order_id);

		// Process once and only for Order Bump orders.
		if (OrderUtil::custom_orders_table_usage_is_enabled()) {
			// HPOS usage is enabled.
			$bump_order = $order->get_meta('wps_bump_order_process_sales_stats', true);
		} else {
			$bump_order = get_post_meta($order_id, 'wps_bump_order_process_sales_stats', true);
		}

		if (empty($bump_order)) {

			return;
		}

		$order = new WC_Order($order_id);

		if (empty($order)) {

			return;
		}

		$processed_order_statuses = array(
			'processing',
			'completed',
			'on-hold',
		);

		if (! in_array($order->get_status(), $processed_order_statuses, true)) {

			return;
		}

		$order_items = $order->get_items();

		if (! empty($order_items) && is_array($order_items)) {

			foreach ($order_items as $item_id => $single_item) {

				if (! empty(wc_get_order_item_meta($item_id, 'is_order_bump_purchase', true)) && ! empty(wc_get_order_item_meta($item_id, 'wps_order_bump_id', true))) {

					$order_bump_item_total = wc_get_order_item_meta($item_id, '_line_total', true);

					// Add Order Bump Success count and Total Sales for the respective Order Bump.
					$sales_by_bump = new Wps_Upsell_Order_Bump_Report_Sales_By_Bump(wc_get_order_item_meta($item_id, 'wps_order_bump_id', true));

					$sales_by_bump->add_bump_success_count();
					$sales_by_bump->add_bump_total_sales($order_bump_item_total);

					// Delete bump id as it might change so no need to associate the order item with it.
					wc_delete_order_item_meta($item_id, 'wps_order_bump_id');
				}
			}
		}

		/**
		 * Delete Order Bump sales stats meta so that this is processed only once.
		 */
		if (OrderUtil::custom_orders_table_usage_is_enabled()) {
			// HPOS usage is enabled.
			$order->delete_meta_data('wps_bump_order_process_sales_stats');
			$order->save();
		} else {
			delete_post_meta($order_id, 'wps_bump_order_process_sales_stats');
		}
	}

	/**
	 * On successful order reset data.
	 *
	 * @param string $order_id The order id.
	 *
	 * @since    1.0.0
	 */
	public function reset_session_variable($order_id = '')
	{

		if (class_exists('Upsell_Order_Bump_Offer_For_Woocommerce_Pro')) {
			$pro_class = new Upsell_Order_Bump_Offer_For_Woocommerce_Pro('Order Bump', '2.3.1');
			$pro_class->exclusive_limit_callback($order_id);
		}

		// Destroy session on order completed.
		wps_ubo_session_destroy();
	}


	/**
	 * Send a custom email notification when the WooCommerce order status changes.
	 *
	 * @param int    $order_id   The ID of the order whose status has changed.
	 * @param string $old_status The previous status of the order.
	 * @param string $new_status The new status of the order.
	 */
	public function wps_send_custom_email_on_order_status_change($order_id, $old_status, $new_status)
	{
		// Make sure the order ID is valid.
		if (! $order_id) {
			return;
		}
		// Check if the new status is 'completed'.
		if ('completed' !== $new_status) {
			return;
		}

		if (class_exists('Upsell_Order_Bump_Offer_For_Woocommerce_Pro')) {
			$pro_class = new Upsell_Order_Bump_Offer_For_Woocommerce_Pro('Order Bump', '2.4.0');
			$pro_class->wps_coupon_mail_send_callback($order_id);
		}
	}

	/**
	 * On successful order reset data.
	 *
	 * @param string $order_id The order id.
	 *
	 * @since    1.0.0
	 */
	public function reset_order_bump($order_id = '')
	{

		// Destroy session on order completed.
		wps_ubo_session_destroy();
	}

	/**
	 * Add selected product product to cart ( by button )
	 *
	 * @since    1.0.0
	 */
	public function wps_add_the_product()
	{
		// The id of the offer to be added.
		check_ajax_referer('wps_ubo_lite_nonce', 'nonce');
		$bump_product_id = ! empty($_POST['id']) ? sanitize_text_field(wp_unslash($_POST['id'])) : '';

		$bump_discounted_price = ! empty($_POST['discount']) ? sanitize_text_field(wp_unslash($_POST['discount'])) : '';
		$bump_index            = ! empty($_POST['bump_index']) ? sanitize_text_field(wp_unslash($_POST['bump_index'])) : '';
		$bump_target_cart_key  = ! empty($_POST['bump_target_cart_key']) ? sanitize_text_field(wp_unslash($_POST['bump_target_cart_key'])) : '';
		$order_bump_id         = ! empty($_POST['order_bump_id']) ? sanitize_text_field(wp_unslash($_POST['order_bump_id'])) : '';
		$smart_offer_upgrade   = ! empty($_POST['smart_offer_upgrade']) ? sanitize_text_field(wp_unslash($_POST['smart_offer_upgrade'])) : '';
		$form_data             = ! empty($_POST['form_data']) ? map_deep(wp_unslash($_POST['form_data']), 'sanitize_text_field') : array();

		// Quantity of product.
		$wps_qty_variable = ! empty($_POST['wps_qty_variable']) ? sanitize_text_field(wp_unslash($_POST['wps_qty_variable'])) : '1';

		$active_plugin = get_option('active_plugins', false);
		if (in_array('woo-gift-cards-lite/woocommerce_gift_cards_lite.php', $active_plugin, true) && wps_ubo_lite_if_pro_exists() && ! empty($form_data)) {
			$gift_card_form = array(
				'wps_wgm_to_email'      => '',
				'wps_wgm_from_name'     => '',
				'wps_wgm_message'       => '',
				'delivery_method'       => '',
				'wps_wgm_price'         => '',
				'wps_wgm_selected_temp' => '',
			);
			$gift_card_data = get_post_meta($bump_product_id, 'wps_wgm_pricing');
			foreach ($gift_card_data as $key => $value) {
				$gift_card_form = array_merge(
					$gift_card_form,
					array(
						'wps_wgm_price'         => $value['default_price'],
						'wps_wgm_selected_temp' => $value['template'][0],
					)
				);
			}

			foreach ($form_data as $key => $value) {
				if ('from' === $value['name']) {
					$gift_card_form = array_merge($gift_card_form, array('wps_wgm_from_name' => $value['value']));
				} elseif ('gift message' === $value['name']) {
					$gift_card_form = array_merge($gift_card_form, array('wps_wgm_message' => $value['value']));
				} elseif ('mail to recepient' === $value['name']) {
					$gift_card_form = array_merge($gift_card_form, array('wps_wgm_to_email' => $value['value']));
				}
			}

			$cart_item_data = array(
				'wps_ubo_offer_product' => true,
				'wps_ubo_offer_index'   => $bump_index,
				'wps_ubo_bump_id'       => $order_bump_id,
				'wps_discounted_price'  => $bump_discounted_price,
				'wps_ubo_target_key'    => $bump_target_cart_key,
				'flag_' . uniqid()      => true,
				'wps_ubo_meta_form'     => $form_data,
				'product_meta'          => array('meta_data' => $gift_card_form),
			);
		} else {
			$cart_item_data = array(
				'wps_ubo_offer_product' => true,
				'wps_ubo_offer_index'   => $bump_index,
				'wps_ubo_bump_id'       => $order_bump_id,
				'wps_discounted_price'  => $bump_discounted_price,
				'wps_ubo_target_key'    => $bump_target_cart_key,
				'flag_' . uniqid()      => true,
				'wps_ubo_meta_form'     => $form_data,
			);
		}

		$_product = wc_get_product($bump_product_id);

		$added = 'added';

		if (wps_ubo_lite_reload_required_after_adding_offer($_product)) {

			$added = 'subs_reload';
		}

		if (! empty($_product) && $_product->has_child()) {

			// Generate default price html.
			$bump_price_html = wps_ubo_lite_custom_price_html($bump_product_id, $bump_discounted_price);

			$response = array(
				'key'     => 'true',
				'message' => $bump_price_html,
			);

			// Now we have to add a pop up.
			echo wp_json_encode($response);
		} elseif (! empty($_product)) {

			// If simple product or any single variations.
			// Add to cart the same.

			$bump_offer_cart_item_key = WC()->cart->add_to_cart($bump_product_id, $quantity = $wps_qty_variable, $variation_id = 0, $variation = array(), $cart_item_data);

			// Add Order Bump Offer Accept Count for the respective Order Bump.
			$sales_by_bump = new Wps_Upsell_Order_Bump_Report_Sales_By_Bump($order_bump_id);
			$sales_by_bump->add_offer_accept_count();

			if (is_plugin_active('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php')) {

				$sales_by_bump->add_offer_accept_count_pro();
			}

			WC()->session->set('bump_offer_status', 'added');
			WC()->session->set("bump_offer_status_$bump_index", $bump_offer_cart_item_key);

			// Smart offer Upgrade.
			if (wps_ubo_lite_if_pro_exists() && 'yes' === $smart_offer_upgrade) {

				// Get all saved bumps.
				$wps_ubo_bump_callback          = Upsell_Order_Bump_Offer_For_Woocommerce::$wps_upsell_bump_list_callback_function;
				$wps_ubo_offer_array_collection = Upsell_Order_Bump_Offer_For_Woocommerce::$wps_ubo_bump_callback();

				$encountered_bump_array = ! empty($wps_ubo_offer_array_collection[$order_bump_id]) ? $wps_ubo_offer_array_collection[$order_bump_id] : array();

				$wps_upsell_bump_replace_target = ! empty($encountered_bump_array['wps_ubo_offer_replace_target']) ? $encountered_bump_array['wps_ubo_offer_replace_target'] : '';

				if ('yes' === $wps_upsell_bump_replace_target && class_exists('Upsell_Order_Bump_Offer_For_Woocommerce_Pro') && method_exists('Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'wps_ubo_upgrade_offer')) {

					Upsell_Order_Bump_Offer_For_Woocommerce_Pro::wps_ubo_upgrade_offer($bump_offer_cart_item_key, $bump_target_cart_key);
				}
			}

			/**
			 * After v1.3.0 (pro)
			 * Apply Exclusive Limits in case of pro is active.
			 */
			if (wps_ubo_lite_if_pro_exists()) {
				if (class_exists('Upsell_Order_Bump_Offer_For_Woocommerce_Pro')) {
					if (method_exists('Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'wps_ubo_manage_exclusive_limit')) {
						$single_bump_id = str_replace('index_', '', $bump_index);
						if (true === Upsell_Order_Bump_Offer_For_Woocommerce_Pro::wps_ubo_manage_exclusive_limit($single_bump_id)) {
							$associations = WC()->session->get('bump_offer_associations');

							if (null !== $associations) {
								$associations .= '___';
							} else {
								$associations = '';
							}

							$associations .= $bump_index;

							WC()->session->set('bump_offer_associations', $associations);
						}
					}
				}
			}

			echo wp_json_encode($added);
		}

		wp_die();
	}

	/**
	 * Remove the selected variation as bump offer product to cart ( by button )
	 *
	 * @since    1.0.0
	 */
	public function wps_remove_offer_product()
	{
		check_ajax_referer('wps_ubo_lite_nonce', 'nonce');
		$bump_index = ! empty($_POST['bump_index']) ? sanitize_text_field(wp_unslash($_POST['bump_index'])) : '';

		if (null !== WC()->session->get("bump_offer_status_$bump_index")) {

			WC()->cart->remove_cart_item(WC()->session->get("bump_offer_status_$bump_index"));
		}

		WC()->session->__unset("bump_offer_status_$bump_index");

		/**
		 * After v1.3.0 (pro)
		 * Apply Exclusive Limits in case of pro is active.
		 */
		if (wps_ubo_lite_if_pro_exists()) {

			if (class_exists('Upsell_Order_Bump_Offer_For_Woocommerce_Pro')) {
				if (method_exists('Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'wps_ubo_manage_exclusive_limit')) {
					$single_bump_id = str_replace('index_', '', $bump_index);
					if (true === Upsell_Order_Bump_Offer_For_Woocommerce_Pro::wps_ubo_manage_exclusive_limit($single_bump_id)) {
						$associations = WC()->session->get('bump_offer_associations');
						$associations = array_unique(explode('___', $associations));
						$key          = array_search($bump_index, $associations, true);
						unset($associations[$key]);
						WC()->session->set('bump_offer_associations', implode('___', $associations));
					}
				}
			}
		}

		echo wp_json_encode('removed');

		wp_die();
	}

	/**
	 * Add selected variation as bump offer product to cart ( by button )
	 *
	 * @since    1.0.0
	 */
	public function wps_variation_select_added()
	{
		check_ajax_referer('wps_ubo_lite_nonce', 'nonce');
		$bump_offer_id = ! empty($_POST['id']) ? sanitize_text_field(wp_unslash($_POST['id'])) : '';  // variation parent.

		$bump_offer_discount = ! empty($_POST['discount']) ? sanitize_text_field(wp_unslash($_POST['discount'])) : '';

		$attributes_selected_options = ! empty($_POST['attributes_selected_options']) ? array_map('sanitize_text_field', wp_unslash($_POST['attributes_selected_options'])) : array();

		// Got all values to search for variation id from selected attributes.
		$product = wc_get_product($bump_offer_id);

		if (empty($product)) {

			echo wp_json_encode(esc_html__('Product Not Found.', 'upsell-order-bump-offer-for-woocommerce'));
			return;
		}

		$product_data_store         = new WC_Product_Data_Store_CPT();
		$variation_id               = $product_data_store->find_matching_product_variation($product, $attributes_selected_options);
		$selected_variation_product = wc_get_product($variation_id);

		// Image to reflect on select change.
		$image_id = get_post_thumbnail_id($variation_id);

		if (! empty($image_id)) {

			$html           = wc_get_gallery_image_html($image_id, true);
			$bump_var_image = apply_filters('woocommerce_single_product_image_thumbnail_html', $html, $image_id);
		} else {

			// If no variation image is present show default one.
			$bump_var_image = wps_ubo_lite_get_bump_image($bump_offer_id);
		}

		// Variation id will be empty if selected variation is not available.
		if (empty($variation_id) || empty($selected_variation_product)) {

			$response = array(
				'key'     => 'not_available',
				'message' => '<p class="stock out-of-stock">' . esc_html__('Sorry, this variation is not available.', 'upsell-order-bump-offer-for-woocommerce') . '</p>',
				'image'   => $bump_var_image,
			);
			echo wp_json_encode($response);
		} else if (! empty($variation_id) || ! empty($selected_variation_product)) {

			// Check if in stock?
			if (! $selected_variation_product->is_in_stock()) {

				// Out of stock.
				$response = array(

					'key'     => 'stock',
					'message' => '<p class="stock out-of-stock">' . esc_html__('Out of stock.', 'upsell-order-bump-offer-for-woocommerce') . '</p>',
					'image'   => $bump_var_image,
				);

				echo wp_json_encode($response);
			} else {

				$response = array(
					'key'     => $variation_id,
					'message' => wps_ubo_lite_custom_price_html($variation_id, $bump_offer_discount),
					'image'   => $bump_var_image,
				);

				echo wp_json_encode($response);
			}
		}

		wp_die();
	}

	/**
	 * Add selected variation as bump offer product to cart ( by button )
	 *
	 * @since    1.0.0
	 */
	public function wps_add_recommendated_offer_in_popup()
	{
		check_ajax_referer('wps_ubo_lite_nonce_recommend', 'nonce');
		$wps_target_product = isset($_POST['target_product_id']) ? absint($_POST['target_product_id']) : '';
		$wps_product_id_shop = isset($_POST['wps_product_id_shop']) ? absint($_POST['wps_product_id_shop']) : '';
		$wps_targeted_varaition_id = isset($_POST['wps_targeted_varaition_id']) ? absint($_POST['wps_targeted_varaition_id']) : '';
		$wps_show_recommend_product_in_popup = false;

		if (isset($wps_product_id_shop) && ! empty($wps_product_id_shop)) {

			$wps_product_id_set = $wps_product_id_shop;
		} elseif (isset($wps_target_product) && ! empty($wps_target_product)) {

			$wps_product_id_set = $wps_target_product;
		} elseif (isset($wps_targeted_varaition_id) && ! empty($wps_targeted_varaition_id)) {

			$wps_product_id = $wps_targeted_varaition_id;
			$variation = wc_get_product($wps_product_id);
			$wps_product_id_set = $variation->get_parent_id();
		}

		if (isset($wps_product_id_set) && ! empty($wps_product_id_set)) {
			$wps_is_recommendation_enable = get_post_meta($wps_product_id_set, 'is_recommendation_enable');
			$wps_selected_recommendated_product = get_post_meta($wps_product_id_set, 'wps_recommendated_product_ids');

			if (! empty($wps_is_recommendation_enable)) {
				if (('yes' == $wps_is_recommendation_enable[0]) && isset($wps_is_recommendation_enable[0]) && (0 < count($wps_selected_recommendated_product)) && is_array($wps_selected_recommendated_product) && isset($wps_selected_recommendated_product)) {
					$wps_show_recommend_product_in_popup = true;
				} else {
					$wps_show_recommend_product_in_popup = false;
				}
			}
		}

		$response = array(
			'wps_target_product' => $wps_product_id_set,
			'wps_show_recommend_product_in_popup'   => $wps_show_recommend_product_in_popup,
			'cart_item_number' => WC()->cart->get_cart_contents_count(),
			'count' => $wps_selected_recommendated_product,
		);

		echo wp_json_encode($response);
		wp_die();
	}

	/**
	 * Ajax add to cart.
	 *
	 * @since    1.0.0
	 */
	public function ql_woocommerce_ajax_add_to_cart()
	{
		check_ajax_referer('wps_ubo_lite_nonce_recommend', 'nonce');
		$product_id = apply_filters('ql_woocommerce_add_to_cart_product_id', isset($_POST['product_id']) ? absint($_POST['product_id']) : '');
		$quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount(sanitize_text_field(wp_unslash($_POST['quantity'])));
		$variation_id = isset($_POST['variation_id']) ? absint($_POST['variation_id']) : '';
		$passed_validation = apply_filters('ql_woocommerce_add_to_cart_validation', true, $product_id, $quantity);
		$product_status = get_post_status($product_id);
		if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id) && 'publish' === $product_status) {
			do_action('ql_woocommerce_ajax_added_to_cart', $product_id);
			if ('yes' === get_option('ql_woocommerce_cart_redirect_after_add')) {
				wc_add_to_cart_message(array($product_id => $quantity), true);
			}
		} else {
			$data = array(
				'error' => true,
				'product_url' => apply_filters('ql_woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id),
			);
		}
		echo wp_json_encode(WC()->cart->get_cart_contents_count());
		wp_die();
	}


	/**
	 * Add selected variation as bump offer product to cart ( by button )
	 *
	 * @since    1.0.0
	 */
	public function wps_add_to_cart_recommendation()
	{
		check_ajax_referer('wps_ubo_lite_nonce_recommend', 'nonce');
		if (isset($_POST['wps_product_id'])) {

			$wps_product_id = isset($_POST['wps_product_id']) ? absint($_POST['wps_product_id']) : '';
			$wps_product_price = isset($_POST['wps_product_price']) ? absint($_POST['wps_product_price']) : '';

			$wps_target_product_id = isset($_POST['wps_target_product_id']) ? absint($_POST['wps_target_product_id']) : '';

			$wps_select_option_discount = get_post_meta($wps_target_product_id, 'wps_select_option_discount', true);
			$wps_recommendation_discount_val = get_post_meta($wps_target_product_id, 'wps_recommendation_discount_val', true);

			if ('no_disc' == $wps_select_option_discount) {

				// Add the product to the cart when no discount is set.
				WC()->cart->add_to_cart($wps_product_id, 1, 0, array(), array('wps_cart_offer_custom_price' => $wps_product_price));
			} elseif ('wps_percent' == $wps_select_option_discount) {

				// Get the discounted price.
				$discount_percentage = $wps_recommendation_discount_val / 100;
				$custom_discounted_price = $wps_product_price * (1 - $discount_percentage);

				// Add the product to the cart with Discounted Price.
				WC()->cart->add_to_cart($wps_product_id, 1, 0, array(), array('wps_cart_offer_custom_price' => $custom_discounted_price));
			} elseif ('wps_fixed' == $wps_select_option_discount) {

				// Add the product to the cart with Fixed Price.
				WC()->cart->add_to_cart($wps_product_id, 1, 0, array(), array('wps_cart_offer_custom_price' => $wps_recommendation_discount_val));
			}
			// Return a success response.
			$data = array(
				'cart_count' => WC()->cart->get_cart_contents_count(),
				'product_id' => $wps_product_id,
			);
			echo wp_json_encode($data);
		} else {

			// Return an error response.
			wp_send_json_error();
		}
		wp_die();
	}

	/**
	 * Change price at last for bump offer product.
	 *
	 * @param   object $cart_object The cart object.
	 * @since    1.0.0
	 */
	public function wps_add_custom_price_to_cart_item($cart_object)
	{
		foreach ($cart_object->get_cart() as $item) {

			if (array_key_exists('wps_cart_offer_custom_price', $item)) {
				$item['data']->set_price($item['wps_cart_offer_custom_price']);
			}
			if (array_key_exists('fbt_price', $item)) {
				$item['data']->set_price($item['fbt_price']);
			}
		}
	}

	/**
	 * Set The Order Success Page On Placing Order.
	 *
	 * @since    1.0.0
	 */
	public function wps_redirect_custom_thank_you()
	{
		$secure_nonce      = wp_create_nonce('wps-upsell-auth-nonce');
		$id_nonce_verified = wp_verify_nonce($secure_nonce, 'wps-upsell-auth-nonce');

		if (! $id_nonce_verified) {
			wp_die(esc_html__('Nonce Not verified', 'upsell-order-bump-offer-for-woocommerce'));
		}
		// Saved Global Options.
		$wps_ubo_global_options = get_option('wps_ubo_global_options', array());
		$wps_custom_order_success_page = ! empty($wps_ubo_global_options['wps_custom_order_success_page']) ? $wps_ubo_global_options['wps_custom_order_success_page'] : '';

		// Do nothing if we are not on the order received page.
		if (! is_wc_endpoint_url('order-received') || empty($_GET['key'])) {
			return;
		}
		if (! empty($wps_custom_order_success_page) || '' != $wps_custom_order_success_page) {
			wp_safe_redirect(site_url($wps_custom_order_success_page));
		} else {
			return;
		}
	}

	/**
	 * Triggered The Shortcode On Placing Order.
	 *
	 * @since    1.0.0
	 */
	public function wps_triggered_shortcode_page()
	{
		add_shortcode('wps_order_details', array($this, 'wps_display_order_details_shortcode'), 10);
	}

	/**
	 * Shortcode callback On Placing Order.
	 *
	 * @since    1.0.0
	 */
	public function wps_display_order_details_shortcode()
	{
		if (! is_admin()) {
			$stored_order_id = WC()->session->get('custom_order_id');
			$order_id = absint($stored_order_id);
			if (! empty($order_id)) {
				ob_start();

				// Load WooCommerce templates for order details.
				wc_get_template('order/order-details.php', array('order_id' => $order_id));

				// Now that you've used the value, you can remove it from the session.
				return ob_get_clean();
			}
		}
	}

	/**
	 * Set the Order ID On Placing Order.
	 *
	 * @param   int $order_id The cart object.
	 * @since    1.0.0
	 */
	public function wps_custom_get_current_order_id($order_id)
	{

		// This action hook fires when a new order is created.
		// The $order_id parameter contains the ID of the newly created order.
		if (WC()->session !== null) {

			WC()->session->set('custom_order_id', $order_id);
		}
		return $order_id;
	}

	/**
	 * Set the discount on cart section html.
	 *
	 * @since    1.0.0
	 */
	public function wps_woo_cart_discount_section()
	{
		// Define a condition (e.g., extracting even numbers).
		$condition = function ($value) {
			// Check if the index exists in the array before accessing it.
			if (isset($value)) {
				if (get_post_meta($value, 'is_recommendation_enable_for_cart', true) == 'yes') {
					return true;
				}
			} else {
				// Handle the case where the index does not exist.
				return false; // Or any appropriate default value.
			}
		};

		$wps_target_product_array = array();

		$args = array(
			'limit' => -1,
			'status' => 'publish',
			'return' => 'ids',
		);
		$products = wc_get_products($args);

		foreach ($products as $key => $value) {
			if ($condition($value)) {
				$wps_target_product_array[] = $value;
			}
		}

		// Initialize an empty array to store converted integers.
		$wps_int_array = $wps_target_product_array;

		$wps_html_discount_section = '';

		// Get the cart contents.
		$cart_contents = WC()->cart->get_cart();

		if (! empty($wps_int_array) && is_array($wps_int_array)) {
			foreach ($wps_int_array as $wps_outer_array) {
				// Check if the product ID is in the cart.
				foreach ($cart_contents as $cart_item) {

					if ($cart_item['product_id'] == $wps_outer_array) {

						$wps_offer_product_array = get_post_meta($cart_item['product_id'], 'wps_recommendated_product_ids');
						$wps_offer_product_discount_type = get_post_meta($cart_item['product_id'], 'wps_select_option_discount');
						$wps_offer_product_discount_val = get_post_meta($cart_item['product_id'], 'wps_recommendation_discount_val');

						if (is_array($wps_offer_product_array) && ! empty($wps_offer_product_array)) {

							foreach ($wps_offer_product_array as $values) {
								if (is_array($values) && ! empty($values)) {
									foreach ($values as $value) {

										$product = wc_get_product($value);

										$image = wp_get_attachment_image_src(get_post_thumbnail_id($value), 'single-post-thumbnail');
										if (is_array($image) && isset($image[0])) {
											$wps_image_url = esc_url($image[0]);
										} else {
											$wps_image_url = '';
										}

										$wps_html_discount_section .= '<div class="wps_main_class_order" id="wps_main_class_id_' . $value . '">';
										$wps_html_discount_section .= '<div class ="wps_product_image"><img width="100" height="300" src =' . esc_url($wps_image_url) . ' /></div>';
										$wps_html_discount_section .= '<div class ="wps_product_name">' . $product->get_name() . '</div>';

										if ($product->is_type('variable')) {
											// Get all variations of the parent product.
											$variations = $product->get_available_variations();

											$wps_html_discount_section .= '<div class ="wps_product_select"> <select name="select-category" id="wps-order-bump-child-id_' . $value . '">';

											foreach ($variations as $variation) {

												$variation_id = $variation['variation_id'];
												$variation_obj = wc_get_product($variation_id);
												$variation_name = $variation_obj->get_name();
												$wps_discount_price = $this->wps_get_cart_offer_discount_value($wps_offer_product_discount_type, $wps_offer_product_discount_val, $variation_obj->get_price());
												$wps_html_discount_section .= ' <option value="' . $variation_id . '">' . $variation_name . ' - ' . wc_price($wps_discount_price) . '</option>';
											}

											$wps_html_discount_section .= '</select>';
											$wps_html_discount_section .= '</div>';
										}

										$wps_discount_price = $this->wps_get_cart_offer_discount_value($wps_offer_product_discount_type, $wps_offer_product_discount_val, $product->get_price());

										if ('no_disc' == $wps_offer_product_discount_type[0]) {
											$wps_html_discount_section .= '<div class ="wps_discounted_price">' . esc_html__('Price:', 'upsell-order-bump-offer-for-woocommerce') . wc_price($product->get_price()) . '</div>';
										} else {
											$wps_html_discount_section .= '<div class ="wps_discounted_price">' . esc_html__('Price:', 'upsell-order-bump-offer-for-woocommerce') . '<strike>' . wc_price($product->get_price()) . '</strike>' . wc_price($wps_discount_price) . '</div>';
										}

										$wps_html_discount_section .= '<div class ="wps_discounted_qty">' . esc_html__('Quantity: 01', 'upsell-order-bump-offer-for-woocommerce') . '</div>';
										$wps_html_discount_section .= '<div class ="wps_discounted_offer_title">' . esc_html__('Offer!', 'upsell-order-bump-offer-for-woocommerce') . '</div>';
										$wps_html_discount_section .= '<div class ="wps_product_discount" value =' . $value . '><button type="button" class="button">' . esc_html__('Add to Cart', 'upsell-order-bump-offer-for-woocommerce') . '</button></div>';
										$wps_html_discount_section .= '<input id="wps_cart_offer_quantity" type="hidden" value ="1">';
										$wps_html_discount_section .= '<input id="wps_cart_offer_product_id_' . $value . '" type="hidden" value =' . $cart_item['product_id'] . '>';
										$wps_html_discount_section .= '<input class ="wps_offered_product_id" type="hidden" value =' . $value . '>';
										$wps_html_discount_section .= '<input id="wps_cart_offer_product_price_' . $value . '" type="hidden" value =' . $product->get_price() . '>';
										$wps_html_discount_section .= '</div>';
									}
								}
							}
						} else {
							wc_add_notice('Check Recommendation Product.', 'error');
						}
					}
				}
			}
		}
		$allowed_html = wps_ubo_lite_allowed_html();
		echo wp_kses($wps_html_discount_section, $allowed_html);
	}

	/**
	 * Ajax to add the cart offer in cart.
	 *
	 * @since    1.0.0
	 */
	public function wps_add_cart_discount_offer_in_cart()
	{
		check_ajax_referer('wps_ubo_lite_nonce_recommend', 'nonce');
		$parent_product_id = isset($_POST['parent_product_id']) ? absint($_POST['parent_product_id']) : '';
		$child_product_id = isset($_POST['child_product_id']) ? absint($_POST['child_product_id']) : '';
		$wps_cart_offer_product_price = isset($_POST['wps_cart_offer_product_price']) ? absint($_POST['wps_cart_offer_product_price']) : '';
		$wps_cart_offer_quantity_value = isset($_POST['wps_cart_offer_quantity_value']) ? absint($_POST['wps_cart_offer_quantity_value']) : '';
		$wps_cart_offer_product_id_value = ! empty($_POST['wps_cart_offer_product_id_value']) ? sanitize_text_field(wp_unslash($_POST['wps_cart_offer_product_id_value'])) : '';

		$message = '';
		$wps_discount_price = '';

		$wps_offer_product_discount_type = get_post_meta($wps_cart_offer_product_id_value, 'wps_select_option_discount');
		$wps_offer_product_discount_val = get_post_meta($wps_cart_offer_product_id_value, 'wps_recommendation_discount_val');

		$product = wc_get_product($parent_product_id);

		if ($product->is_type('variable')) {
			try {
				if (! empty($child_product_id)) {
					// Get the variation object.
					$variation_product = wc_get_product($child_product_id);
					// Get the price of the variation.
					$variation_price = $variation_product->get_price();
					$wps_discount_price = $this->wps_get_cart_offer_discount_value($wps_offer_product_discount_type, $wps_offer_product_discount_val, $variation_price);
					// Create an array of product data to add to the cart.
					$cart_item_data = array(
						'_price' => $wps_discount_price, // Set the discounted price.
					);
					// Add the product to the cart.
					$result = WC()->cart->add_to_cart($child_product_id, $wps_cart_offer_quantity_value, 0, array(), $cart_item_data);
				}

				if ($result) {
					// Product added to the cart successfully.
					$message = 'remove';
					wc_add_notice('Cart Offer Successfully Added To cart.', 'success');
				} else {
					// Product could not be added to the cart (e.g., if it's out of stock).
					$message = 'Product could not be added to the cart.';
					wc_add_notice('Cart Offer Unable  To  Add To cart.', 'error');
				}
			} catch (\Exception $e) {
				wc_add_notice('Unexpected error occurred.', 'error');
			}
		} else {

			try {
				if (! empty($parent_product_id)) {
					$wps_discount_price = $this->wps_get_cart_offer_discount_value($wps_offer_product_discount_type, $wps_offer_product_discount_val, $wps_cart_offer_product_price);
					$cart_item_data = array(
						'_price' => $wps_discount_price, // Set the discounted price.
					);

					$result = WC()->cart->add_to_cart($parent_product_id, $wps_cart_offer_quantity_value, 0, array(), $cart_item_data);
				}

				if ($result) {
					// Product added to the cart successfully.
					$message = 'remove';
					wc_add_notice('Cart Offer Successfully Added To cart.', 'success');
				} else {
					// Product could not be added to the cart (e.g., if it's out of stock).
					$message = 'Product could not be added to the cart.';
					wc_add_notice('Cart Offer Unable  To  Add To cart.', 'error');
				}
			} catch (\Exception $e) {
				wc_add_notice('Unexpected error occurred.', 'error');
			}
		}

		$response = array(
			'key'     => 'true',
			'message' => $message,
		);

		echo wp_json_encode($response);
		wp_die();
	}

	/**
	 * Get the discounted price on cart offer.
	 *
	 * @param   array  $wps_offer_product_discount_type The discount string.
	 * @param   array  $wps_offer_product_discount_val The discount string.
	 * @param   string $wps_cart_offer_product_price The discount string.
	 * @since    1.0.0
	 */
	public function wps_get_cart_offer_discount_value($wps_offer_product_discount_type, $wps_offer_product_discount_val, $wps_cart_offer_product_price)
	{

		if (is_array($wps_offer_product_discount_type) && ! empty($wps_offer_product_discount_type) && is_array($wps_offer_product_discount_val) && ! empty($wps_offer_product_discount_val)) {
			if ('wps_percent' == $wps_offer_product_discount_type[0]) {   // For the Percentaged count.
				// Get the product's regular price.
				$regular_price = floatval($wps_cart_offer_product_price);

				$wps_discounted_price = $regular_price - ($regular_price * ($wps_offer_product_discount_val[0] / 100));
			}
			if ('wps_fixed' == $wps_offer_product_discount_type[0]) {

				$regular_price = floatval($wps_offer_product_discount_val[0]);
				$wps_discounted_price = $wps_offer_product_discount_val[0];
			}
			if ('no_disc' == $wps_offer_product_discount_type[0]) {
				$wps_discounted_price = $wps_cart_offer_product_price;
			}
		}

		return $wps_discounted_price;
	}

	/**
	 * Set the custom price in cart.
	 *
	 * @param   object $cart_object cart object.
	 * @since    1.0.0
	 */
	public function wps_order_cart_custom_price_refresh($cart_object)
	{

		foreach ($cart_object->get_cart() as $item) {

			if (array_key_exists('_price', $item)) {
				$item['data']->set_price($item['_price']);
			}
		}
	}

	/**
	 * Show Order Bump On Checkout Block.
	 *
	 * @since    1.0.0
	 */
	public function wps_show_bump_on_checkout_block_callback()
	{
		// Get the ID of the selected checkout page from WooCommerce settings.
		$checkout_page_id = get_option('woocommerce_checkout_page_id');

		// Get the content of the checkout page.
		$checkout_page_content = get_post_field('post_content', $checkout_page_id);

		// Check if the content contains a class associated with the block editor.
		if (strpos($checkout_page_content, 'wp-block-woocommerce-checkout') !== false) {
			require_once plugin_dir_path(__FILE__) . '/partials/upsell-order-bump-offer-for-woocommerce-public-display.php';
		}
	}

	/**
	 * Show Order Bump On Cart Block.
	 *
	 * @since    1.0.0
	 */
	public function wps_show_bump_on_cart_block_callback()
	{
		$wps_ubo_global_options = get_option('wps_ubo_global_options', array());
		// Get the ID of the selected cart page from WooCommerce settings.
		$cart_page_id = get_option('woocommerce_cart_page_id');

		// Get the content of the checkout page.
		$cart_page_content = get_post_field('post_content', $cart_page_id);

		// Check cart upsell enabled.
		$wps_enable_cart_upsell_feature = ! empty($wps_ubo_global_options['wps_enable_cart_upsell_feature']) ? $wps_ubo_global_options['wps_enable_cart_upsell_feature'] : 'on';
		if ('on' == $wps_enable_cart_upsell_feature && strpos($cart_page_content, 'wp-block-woocommerce-cart') !== false) {
			require_once plugin_dir_path(__FILE__) . '/partials/upsell-order-bump-offer-for-woocommerce-public-display.php';
		}
	}


	/**
	 * Show Order Bump On Cart Block.
	 *
	 * @since    1.0.0
	 */
	public function wps_add_to_cart_fbt_product_callback()
	{
		$secure_nonce      = wp_create_nonce('wps-upsell-auth-nonce');
		$id_nonce_verified = wp_verify_nonce($secure_nonce, 'wps-upsell-auth-nonce');
		if (! $id_nonce_verified) {
			wp_die(esc_html__('Nonce Not verified', 'upsell-order-bump-offer-for-woocommerce'));
		}
		$wps_product_id = isset($_POST['wps_product_id']) ? array_map('sanitize_text_field', wp_unslash($_POST['wps_product_id'])) : array();
		$wps_main_product_id = isset($_POST['wps_main_prod_id']) ? absint($_POST['wps_main_prod_id']) : '';
		// Loop through each product and add it to the cart.
		foreach ($wps_product_id as $product_id) {
			$product = wc_get_product($product_id);
			$price = $product->get_price();
			$discount = $this->wps_custom_discount_price($price, $wps_main_product_id, $wps_method_upsell = 'yes');
			// Add the product to the cart.
			WC()->cart->add_to_cart($product_id, 1, 0, array(), array('fbt_price' => $discount));
		}
		$data = array(
			'wps_product_added'  => 'yes',
		);
		echo wp_json_encode($data);
		wp_die();
	}





	/**
	 * Bump Offer Discount Price.
	 *
	 * @param   int    $wps_product_price       current product price.
	 * @param   int    $wps_target_product_id        target product id.
	 * @param   string $wps_method_upsell        target product id.
	 * @since   1.0.0
	 */
	public function wps_custom_discount_price($wps_product_price, $wps_target_product_id = 270, $wps_method_upsell = '')
	{
		$wps_select_option_discount = get_post_meta($wps_target_product_id, 'wps_fbt_select_option_discount', true);
		$wps_recommendation_discount_val = get_post_meta($wps_target_product_id, 'wps_fbt_discount_val', true);

		$wps_discounted_price = '';
		if ('no_disc' == $wps_select_option_discount) {

			$wps_discounted_price = $wps_product_price;
		} elseif ('wps_percent' == $wps_select_option_discount) {

			// Get the discounted price whediscount is percentage.
			$discount_percentage = $wps_recommendation_discount_val / 100;
			$custom_discounted_price = $wps_product_price * (1 - $discount_percentage);

			$wps_discounted_price = $custom_discounted_price;
		} elseif ('wps_fixed' == $wps_select_option_discount) {

			// Get the discounted price whediscount is fixed.
			$wps_discounted_price = $wps_product_price - $wps_recommendation_discount_val;
			if ($wps_discounted_price < 0) {
				$wps_discounted_price = 0;
			}
		}
		return $wps_discounted_price;
	}


	/**
	 * Remove Currency switcher in session on deactivate.
	 *
	 * @since 3.6.3
	 */
	public function check_compatibltiy_instance_cs()
	{
		if (function_exists('wps_upsell_lite_is_plugin_active')) {
			$cs_exists = wps_upsell_lite_is_plugin_active('wps-multi-currency-switcher-for-woocommerce/wps-multi-currency-switcher-for-woocommerce.php');
			if (false === $cs_exists) {
				if (! empty(WC()->session) && WC()->session->__isset('s_selected_currency')) {
					WC()->session->__unset('s_selected_currency');
				}
			}
		}
	}


	/**
	 * Add custom cron recurrence time interval.
	 *
	 * @since    1.0.0
	 * @param       array $schedules       Array of cron Schedule times for recurrence.
	 */
	public function set_cron_schedule_time($schedules)
	{

		if (! isset($schedules['wps_wocuf_twenty_minutes'])) {

			$schedules['wps_wocuf_twenty_minutes'] = array(
				'interval' => 20 * 60,
				'display'  => esc_html__('Once every 20 minutes', 'woo-one-click-upsell-funnel'),
			);
		}

		return $schedules;
	}


	/**
	 * Redirect upsell offer pages if not admin or upsell nonce expired.
	 *
	 * @since    3.0.0
	 */
	public function upsell_offer_page_redirect()
	{

		$saved_offer_post_ids = get_option('wps_upsell_lite_offer_post_ids', array());

		if (! empty($saved_offer_post_ids) && is_array($saved_offer_post_ids) && count($saved_offer_post_ids)) {

			global $post;

			// When current page is one of the upsell offer page.
			if (! empty($post->ID) && in_array($post->ID, $saved_offer_post_ids, true)) {

				$validate_shortcode = $this->validate_shortcode();

				if (wps_upsell_divi_builder_plugin_active()) {

					// 1833px
					update_post_meta($post->ID, '_et_pb_page_layout', 'et_no_sidebar');
			?>
					<style type="text/css">
						body {
							margin: auto;
						}

						.main_title {
							display: none !important;
						}

						.page-id-<?php echo esc_attr($post->ID); ?>header,
						.page-id-<?php echo esc_attr($post->ID); ?>footer {
							display: none !important;
						}

						.container {
							width: 100% !important;
							max-width: 100% !important;
						}

						body:not(.et-tb) #main-content .container,
						body:not(.et-tb-has-header) #main-content .container {
							padding-top: 0px !important;
						}
					</style>
		<?php
				}
				if (false === $validate_shortcode) {

					$this->expire_offer();
				}
			}
		}
	}


	/**
	 * Validate shortcode for rendering content according to user( live offer )
	 * and admin ( for viewing purpose ).
	 *
	 * @since    3.0.0
	 */
	public function validate_shortcode()
	{

		$secure_nonce      = wp_create_nonce('wps-upsell-auth-nonce');
		$id_nonce_verified = wp_verify_nonce($secure_nonce, 'wps-upsell-auth-nonce');

		if (! $id_nonce_verified) {
			wp_die(esc_html__('Nonce Not verified', 'woo-one-click-upsell-funnel'));
		}

		if (isset($_GET['ocuf_ns']) && isset($_GET['ocuf_ok']) && isset($_GET['ocuf_ofd']) && isset($_GET['ocuf_fid'])) {

			if (wps_upsell_lite_validate_upsell_nonce()) {

				return 'live_offer';
			}
		} elseif (current_user_can('manage_options')) {

			return 'admin_view';
		}

		return false;
	}



	/**
	 * Expire offer and show return to shop link.
	 *
	 * @since    2.0.0
	 */
	private function expire_offer()
	{

		$shop_page_url = function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : get_permalink(woocommerce_get_page_id('shop'));
		?>
		<div style="text-align: center;margin-top: 30px;" id="wps_upsell_offer_expired">
			<h2 style="font-weight: 200;"><?php esc_html_e('Sorry, Offer expired.', 'woo-one-click-upsell-funnel'); ?></h2><a class="button wc-backward" href="<?php echo esc_url($shop_page_url); ?>"><?php esc_html_e('Return to Shop ', 'woo-one-click-upsell-funnel'); ?>&rarr;</a>
		</div>
		<?php

		// It just displayes the html itself. Content in it is already escaped.

		wp_die();
	}



	/**
	 * Hide upsell offer pages from nav menu front-end.
	 *
	 * @param mixed $args args.
	 * @since    3.0.0
	 */
	public function exclude_pages_from_front_end($args)
	{

		$saved_offer_post_ids = get_option('wps_upsell_lite_offer_post_ids', array());

		if (! empty($saved_offer_post_ids) && is_array($saved_offer_post_ids) && count($saved_offer_post_ids)) {

			$exclude_pages     = $saved_offer_post_ids;
			$exclude_pages_ids = '';

			foreach ($exclude_pages as $_post_id) {

				if (! empty($exclude_pages_ids)) {

					$exclude_pages_ids .= ', ';
				}

				$exclude_pages_ids .= $_post_id;
			}

			if (! empty($args['exclude'])) {

				$args['exclude'] .= ',';
			} else {

				$args['exclude'] = '';
			}

			$args['exclude'] .= $exclude_pages_ids;
		}

		return $args;
	}


	/**
	 * Hide upsell offer pages from added menu list in customizer and admin panel.
	 *
	 * @param mixed $items items.
	 * @since    3.0.0
	 */
	public function exclude_pages_from_menu_list($items)
	{

		$saved_offer_post_ids = get_option('wps_upsell_lite_offer_post_ids', array());

		if (! empty($saved_offer_post_ids) && is_array($saved_offer_post_ids) && count($saved_offer_post_ids)) {

			$exclude_pages     = $saved_offer_post_ids;
			$exclude_pages_ids = array();

			foreach ($exclude_pages as $_post_id) {

				array_push($exclude_pages_ids, (int) $_post_id);
			}

			if (! empty($exclude_pages_ids)) {

				foreach ($items as $key => $item) {

					if (in_array((int) $item->object_id, $exclude_pages_ids, true)) {

						unset($items[$key]);
					}
				}
			}
		}

		return $items;
	}


	/**
	 * Remove all styles from offer pages
	 *
	 * @since    3.0.0
	 */
	public function remove_styles_offer_pages()
	{

		$saved_offer_post_ids = get_option('wps_upsell_lite_offer_post_ids', array());

		if (! empty($saved_offer_post_ids) && is_array($saved_offer_post_ids) && count($saved_offer_post_ids)) {

			global $post;

			if (! empty($post->ID) && in_array((int) $post->ID, $saved_offer_post_ids, true)) {

				global $wp_styles;

				// To dequeue all wp-content styles.
				foreach ($wp_styles->registered as $k => $s) {

					if (mb_strpos($s->src, 'wp-content/')) {

						// Except for upsell and elementor plugins.
						if (mb_strpos($s->src, 'elementor') || mb_strpos($s->src, 'woo-one-click-upsell-funnel')) {

							continue;
						}

						wp_deregister_style($k);
					}
				}

				global $wp_scripts;

				// To dequeue all theme scripts.
				foreach ($wp_scripts->registered as $k => $s) {

					if (mb_strpos($s->src, 'wp-content/themes/')) {

						wp_deregister_script($k);
					}
				}

		?>

				<style type="text/css">
					body {
						margin: auto;
					}
				</style>

			<?php
			}
		}
	}



	/**
	 * Shortcodes for Upsell action and Product attributes.
	 * The life of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function upsell_shortcodes()
	{

		// OLD shortcodes.

		// Creating shortcode for accept link on custom page.
		add_shortcode('wps_wocuf_pro_yes', array($this, 'wps_wocuf_pro_custom_page_action_link_yes'));

		// creating shortcode for thanks link on custom page.
		add_shortcode('wps_wocuf_pro_no', array($this, 'wps_wocuf_pro_custom_page_action_link_no'));

		// creating shortcode for showing product price on custom page.
		add_shortcode('wps_wocuf_pro_offer_price', array($this, 'wps_wocuf_pro_custom_page_product_offer_price'));
		// creating shortcode for showing order details link on custom page.
		add_shortcode('wps_wocuf_pro_order_details', array($this, 'wps_wocuf_pro_custom_page_order_details_link'));

		// adding shortcode for default funnel offer page.
		add_shortcode('wps_wocuf_pro_funnel_default_offer_page', array($this, 'wps_wocuf_pro_funnel_offers_shortcode'));

		// New shortcodes.

		// Upsell Action.

		add_shortcode('wps_upsell_yes', array($this, 'buy_now_shortcode_content'));

		add_shortcode('wps_upsell_no', array($this, 'no_thanks_shortcode_content'));

		// Product.
		add_shortcode('wps_upsell_title', array($this, 'product_title_shortcode_content'));

		add_shortcode('wps_upsell_desc', array($this, 'product_description_shortcode_content'));

		add_shortcode('wps_upsell_desc_short', array($this, 'product_short_description_shortcode_content'));

		add_shortcode('wps_upsell_image', array($this, 'product_image_shortcode_content'));

		add_shortcode('wps_upsell_price', array($this, 'product_price_shortcode_content'));

		add_shortcode('wps_upsell_product_shipping_price', array($this, 'upsell_product_shipping_price_shortcode_content'));

		add_shortcode('wps_upsell_variations', array($this, 'variations_selector_shortcode_content'));

		// Review.
		add_shortcode('wps_upsell_star_review', array($this, 'product_star_review'));

		// Default Gutenberg offer.
		add_shortcode('wps_upsell_default_offer_identification', array($this, 'default_offer_identification'));

		/**
		 * Shortcodes since v3.0.0.
		 * Quantity Field and Timer Shortcode.
		 */
		add_shortcode('wps_upsell_timer', array($this, 'timer_shortcode_content'));

		add_shortcode('wps_upsell_quantity', array($this, 'quantity_shortcode_content'));
	}

	/**
	 * Creating shortcode for yes link on custom page.
	 *
	 * @since 1.0.0
	 * @param mixed $atts attributes of the shortcode.
	 * @param mixed $content content under wrapping mode.
	 */
	public function wps_wocuf_pro_custom_page_action_link_yes($atts, $content = '')
	{
		$atts = shortcode_atts(
			array(
				'style' => '',
				'class' => '',
			),
			$atts
		);

		return $this->wps_wocuf_pro_custom_page_yes_link_for_all(
			array(
				'style' => $atts['style'],
				'class' => $atts['class'],
			),
			$content
		);
	}
	/**
	 * Internal functioning of yes link shortcode.
	 *
	 * @since 1.0.0
	 * @param mixed $atts attributes of the shortcode.
	 * @param mixed $content content under wrapping mode.
	 */
	public function wps_wocuf_pro_custom_page_yes_link_for_all($atts, $content = '')
	{
		$result = '';

		if (empty($atts[0])) {
			$atts[0] = 'yes';
		}

		if (empty($atts['style'])) {
			$atts['style'] = '';
		}

		if (empty($atts['class'])) {
			$atts['class'] = '';
		}

		$wps_wocuf_pro_all_funnels = get_option('wps_wocuf_funnels_list', array());

		$wps_wocuf_pro_buy_text = get_option('wps_wocuf_pro_buy_text', esc_html__('Add to my order', 'woo-one-click-upsell-funnel'));

		if (empty($content)) {
			$content = $wps_wocuf_pro_buy_text;
		}

		$secure_nonce      = wp_create_nonce('wps-upsell-auth-nonce');
		$id_nonce_verified = wp_verify_nonce($secure_nonce, 'wps-upsell-auth-nonce');

		if (! $id_nonce_verified) {
			wp_die(esc_html__('Nonce Not verified', 'woo-one-click-upsell-funnel'));
		}

		$offer_id = isset($_GET['ocuf_ofd']) ? sanitize_text_field(wp_unslash($_GET['ocuf_ofd'])) : '';

		$funnel_id = isset($_GET['ocuf_fid']) ? sanitize_text_field(wp_unslash($_GET['ocuf_fid'])) : '';

		$order_key = isset($_GET['ocuf_ok']) ? sanitize_text_field(wp_unslash($_GET['ocuf_ok'])) : '';

		$wp_nonce = isset($_GET['ocuf_ns']) ? sanitize_text_field(wp_unslash($_GET['ocuf_ns'])) : '';

		$order_id = wc_get_order_id_by_order_key($order_key);

		$wps_wocuf_pro_offered_products = isset($wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_products_in_offer']) ? $wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_products_in_offer'] : array();

		$wps_wocuf_pro_offered_discount = isset($wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_offer_discount_price']) ? $wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_offer_discount_price'] : array();

		$wps_wocuf_pro_single_offered_product = '';

		if (! empty($wps_wocuf_pro_offered_products[$offer_id])) {

			// In v2.0.0, it was array so handling to get the first product id.
			if (is_array($wps_wocuf_pro_offered_products[$offer_id]) && count($wps_wocuf_pro_offered_products[$offer_id])) {

				foreach ($wps_wocuf_pro_offered_products[$offer_id] as $handling_offer_product_id) {

					$wps_wocuf_pro_single_offered_product = absint($handling_offer_product_id);
					break;
				}
			} else {

				$wps_wocuf_pro_single_offered_product = absint($wps_wocuf_pro_offered_products[$offer_id]);
			}
		}

		if (! empty($wps_wocuf_pro_single_offered_product)) {

			$wps_wocuf_pro_original_offered_product = wc_get_product($wps_wocuf_pro_single_offered_product);

			$wps_wocuf_pro_offered_product = $this->wps_wocuf_pro_change_offered_product_price($wps_wocuf_pro_original_offered_product, $wps_wocuf_pro_offered_discount[$offer_id]);

			$product = $wps_wocuf_pro_offered_product;

			$result .= '<form method="post" class="wps_wocuf_pro_custom_offer">
							<input type="hidden" name="ocuf_ns" value="' . esc_attr($wp_nonce) . '">
							<input type="hidden" name="ocuf_fid" value="' . esc_attr($funnel_id) . '">
							<input type="hidden" name="product_id" class="wps_wocuf_pro_variation_id" value="' . absint($product->get_id()) . '">
							<input type="hidden" name="ocuf_ofd" value="' . esc_attr($offer_id) . '">
							<input type="hidden" name="ocuf_ok" value="' . esc_attr($order_key) . '">
							<input type="hidden" name="wps_wocuf_post_nonce" value="' . esc_attr(wp_create_nonce('wps_wocuf_field_post_nonce')) . '">
							<input type="hidden" name="wps_wocuf_after_post_nonce" value="' . esc_attr(wp_create_nonce('wps_wocuf_after_field_post_nonce')) . '">
							<button style="' . esc_attr($atts['style']) . '" class="wps_wocuf_pro_custom_buy ' . esc_attr($atts['class']) . '" type="submit" onclick="" name="wps_wocuf_pro_buy">' . esc_attr($content) . '</button>
						</form>';
		} else {
			$result .= '<form method="post" class="wps_wocuf_pro_custom_offer">
						<input type="hidden" name="ocuf_ns" value="' . esc_attr($wp_nonce) . '">
						<input type="hidden" name="ocuf_fid" value="' . esc_attr($funnel_id) . '">
						<input type="hidden" name="product_id" class="wps_wocuf_pro_variation_id" value="">
						<input type="hidden" name="ocuf_ofd" value="' . esc_attr($offer_id) . '">
						<input type="hidden" name="ocuf_ok" value="' . esc_attr($order_key) . '">
						<input type="hidden" name="wps_wocuf_post_nonce" value="' . esc_attr(wp_create_nonce('wps_wocuf_field_post_nonce')) . '">
						<input type="hidden" name="wps_wocuf_after_post_nonce" value="' . esc_attr(wp_create_nonce('wps_wocuf_after_field_post_nonce')) . '">
						<button style="' . esc_attr($atts['style']) . '" class="wps_wocuf_pro_custom_buy ' . esc_attr($atts['class']) . '" type="submit" name="wps_wocuf_pro_buy">' . esc_attr($content) . '</button>
					</form>';
		}

		return $result;
	}

	/**
	 * Creating shortcode for thanks link on custom page.
	 *
	 * @since 1.0.0
	 * @param mixed $atts attributes of the shortcode.
	 * @param mixed $content content under wrapping mode.
	 */
	public function wps_wocuf_pro_custom_page_action_link_no($atts, $content = '')
	{
		$atts = shortcode_atts(
			array(
				'style' => '',
				'class' => '',
			),
			$atts
		);

		return $this->wps_wocuf_pro_custom_page_no_link_for_all(
			array(
				'style' => $atts['style'],
				'class' => $atts['class'],
			),
			$content
		);
	}

	/**
	 * Creating shortcode for thanks link on custom page for simple as well variable product
	 *
	 * @since 1.0.0
	 * @param mixed $atts attributes of the shortcode.
	 * @param mixed $content content under wrapping mode.
	 */
	public function wps_wocuf_pro_custom_page_no_link_for_all($atts, $content = '')
	{
		$result = '';

		if (empty($atts['style'])) {
			$atts['style'] = '';
		}

		if (empty($atts['class'])) {
			$atts['class'] = '';
		}

		$secure_nonce      = wp_create_nonce('wps-upsell-auth-nonce');
		$id_nonce_verified = wp_verify_nonce($secure_nonce, 'wps-upsell-auth-nonce');

		if (! $id_nonce_verified) {
			wp_die(esc_html__('Nonce Not verified', 'woo-one-click-upsell-funnel'));
		}

		$offer_id = isset($_GET['ocuf_ofd']) ? sanitize_text_field(wp_unslash($_GET['ocuf_ofd'])) : '';

		$funnel_id = isset($_GET['ocuf_fid']) ? sanitize_text_field(wp_unslash($_GET['ocuf_fid'])) : '';

		$order_key = isset($_GET['ocuf_ok']) ? sanitize_text_field(wp_unslash($_GET['ocuf_ok'])) : '';

		$wp_nonce = isset($_GET['ocuf_ns']) ? sanitize_text_field(wp_unslash($_GET['ocuf_ns'])) : '';

		$order_id = wc_get_order_id_by_order_key($order_key);

		$th = 1;

		$wps_wocuf_pro_no_text = get_option('wps_wocuf_pro_no_text', esc_html__('No,thanks', 'woo-one-click-upsell-funnel'));

		if (empty($content)) {
			$content = $wps_wocuf_pro_no_text;
		}

		if (! empty($offer_id) && ! empty($order_key) && ! empty($wp_nonce)) {
			$result .= '<a style="' . esc_attr($atts['style']) . '" class=
			"wps_wocuf_pro_no wps_wocuf_pro_custom_skip ' . esc_attr($atts['class']) . '" 
			href="?ocuf_ns=' . esc_attr($wp_nonce) . '
			&ocuf_th=1&ocuf_ok=' . esc_attr($order_key) . '
			&ocuf_ofd=' . esc_attr($offer_id) . '
			&ocuf_fid=' . esc_attr($funnel_id) . '"
			>' . esc_attr($content) . '</a>';
		} else {
			$result .= '<a style="' . esc_attr($atts['style']) . '" 
			class="wps_wocuf_pro_custom_skip ' . esc_attr($atts['class']) . '" 
			href="">' . esc_attr($content) . '</a>';
		}

		return $result;
	}


	/**
	 * Creating shortcode for special price on custom page.
	 *
	 * @since 1.0.0
	 * @param mixed $atts attributes of the shortcode.
	 * @param mixed $content content under wrapping mode.
	 */
	public function wps_wocuf_pro_custom_page_product_offer_price($atts, $content = '')
	{
		$atts = shortcode_atts(
			array(
				'style' => '',
				'class' => '',
			),
			$atts
		);

		return $this->wps_wocuf_pro_custom_page_offer_price_for_all(
			array(
				'style' => $atts['style'],
				'class' => $atts['class'],
			),
			$content
		);
	}
	/**
	 * Internal functioning of price shortcode.
	 *
	 * @since 1.0.0
	 * @param mixed $atts attributes of the shortcode.
	 * @param mixed $content content under wrapping mode.
	 */
	public function wps_wocuf_pro_custom_page_offer_price_for_all($atts, $content = '')
	{
		$result = '';

		if (empty($atts['style'])) {
			$atts['style'] = '';
		}

		if (empty($atts['class'])) {
			$atts['class'] = '';
		}

		$secure_nonce      = wp_create_nonce('wps-upsell-auth-nonce');
		$id_nonce_verified = wp_verify_nonce($secure_nonce, 'wps-upsell-auth-nonce');

		if (! $id_nonce_verified) {
			wp_die(esc_html__('Nonce Not verified', 'woo-one-click-upsell-funnel'));
		}

		$offer_id = isset($_GET['ocuf_ofd']) ? sanitize_text_field(wp_unslash($_GET['ocuf_ofd'])) : '';

		$funnel_id = isset($_GET['ocuf_fid']) ? sanitize_text_field(wp_unslash($_GET['ocuf_fid'])) : '';

		$order_key = isset($_GET['ocuf_ok']) ? sanitize_text_field(wp_unslash($_GET['ocuf_ok'])) : '';

		$wp_nonce = isset($_GET['ocuf_ns']) ? sanitize_text_field(wp_unslash($_GET['ocuf_ns'])) : '';

		$order_id = wc_get_order_id_by_order_key($order_key);

		$wps_wocuf_pro_all_funnels = get_option('wps_wocuf_funnels_list', array());

		$wps_wocuf_pro_before_offer_price_text = get_option('wps_wocuf_pro_before_offer_price_text', esc_html__('Special Offer Price', 'woo-one-click-upsell-funnel'));

		$wps_wocuf_pro_offered_products = isset($wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_products_in_offer']) ? $wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_products_in_offer'] : array();

		$wps_wocuf_pro_offered_discount = isset($wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_offer_discount_price']) ? $wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_offer_discount_price'] : array();

		$wps_wocuf_pro_single_offered_product = '';

		if (! empty($wps_wocuf_pro_offered_products[$offer_id])) {

			// In v2.0.0, it was array so handling to get the first product id.
			if (is_array($wps_wocuf_pro_offered_products[$offer_id]) && count($wps_wocuf_pro_offered_products[$offer_id])) {

				foreach ($wps_wocuf_pro_offered_products[$offer_id] as $handling_offer_product_id) {

					$wps_wocuf_pro_single_offered_product = absint($handling_offer_product_id);
					break;
				}
			} else {

				$wps_wocuf_pro_single_offered_product = absint($wps_wocuf_pro_offered_products[$offer_id]);
			}
		}

		if (! empty($wps_wocuf_pro_single_offered_product)) {

			$wps_wocuf_pro_original_offered_product = wc_get_product($wps_wocuf_pro_single_offered_product);

			$wps_wocuf_pro_offered_product = $this->wps_wocuf_pro_change_offered_product_price($wps_wocuf_pro_original_offered_product, $wps_wocuf_pro_offered_discount[$offer_id]);

			$product = $wps_wocuf_pro_offered_product;

			$result .= '<div style="' . esc_attr($atts['style']) . '" 
			class="wps_wocuf_pro_custom_offer_price ' . esc_attr($atts['class']) . '">
			' . esc_attr($wps_wocuf_pro_before_offer_price_text) . ' :
				 ' . esc_attr($product->get_price_html()) . '</div>';
		} else {
			$result .= '<div style="' . esc_attr($atts['style']) . '" 
			class="wps_wocuf_pro_custom_offer_price ' . esc_attr($atts['class']) . '">
			' . esc_attr($content) . '</div>';
		}

		return $result;
	}


	/**
	 * Creating shortcode for showing order details page.
	 *
	 * @since 1.0.0
	 * @param mixed $atts attributes of the shortcode.
	 * @param mixed $content content under wrapping mode.
	 */
	public function wps_wocuf_pro_custom_page_order_details_link($atts, $content = '')
	{
		$atts = shortcode_atts(
			array(
				'style' => '',
				'class' => '',
			),
			$atts
		);

		return $this->wps_wocuf_pro_custom_page_order_details_link_for_all(
			array(
				'style' => $atts['style'],
				'class' => $atts['class'],
			),
			$content
		);
	}
	/**
	 * Showing order details at thankyou page.
	 *
	 * @since 1.0.0
	 * @param mixed $atts attributes of the shortcode.
	 * @param mixed $content content under wrapping mode.
	 */
	public function wps_wocuf_pro_custom_page_order_details_link_for_all($atts, $content = '')
	{
		$result = '';

		if (empty($atts['style'])) {
			$atts['style'] = '';
		}

		if (empty($atts['class'])) {
			$atts['class'] = '';
		}

		if (empty($content)) {
			$content = esc_html__('Show Order Details', 'woo-one-click-upsell-funnel');
			$content = apply_filters('wps_wocuf_pro_order_details_link_text', $content);
		}

		$secure_nonce      = wp_create_nonce('wps-upsell-auth-nonce');
		$id_nonce_verified = wp_verify_nonce($secure_nonce, 'wps-upsell-auth-nonce');

		if (! $id_nonce_verified) {
			wp_die(esc_html__('Nonce Not verified', 'woo-one-click-upsell-funnel'));
		}

		$order_key = isset($_GET['ocuf_ok']) ? sanitize_text_field(wp_unslash($_GET['ocuf_ok'])) : '';

		$order_id = wc_get_order_id_by_order_key($order_key);

		$order_received_url = wc_get_endpoint_url('order-received', $order_id, wc_get_page_permalink('checkout'));

		$order_received_url = add_query_arg('key', $order_key, $order_received_url);

		$result = '<a href="' . esc_attr($order_received_url) . '" 
		class="button' . esc_attr($atts['class']) . '" 
		style="' . esc_attr($atts['style']) . '">
		' . esc_attr($content) . '</a>';

		return $result;
	}


	/**
	 * Mwb_wocuf_pro_funnel_offers_shortcode.
	 *
	 * @return $result
	 */
	public function wps_wocuf_pro_funnel_offers_shortcode()
	{
		$result = '';

		if (isset($_GET['ocuf_ok'])) {
			$order_key = sanitize_text_field(wp_unslash($_GET['ocuf_ok']));

			$order_id = wc_get_order_id_by_order_key($order_key);

			if (isset($_GET['ocuf_ofd']) && isset($_GET['ocuf_fid'])) {
				$offer_id = sanitize_text_field(wp_unslash($_GET['ocuf_ofd']));

				$funnel_id = sanitize_text_field(wp_unslash($_GET['ocuf_fid']));

				if (isset($_GET['ocuf_ns'])) {

					$wp_nonce = sanitize_text_field(wp_unslash($_GET['ocuf_ns']));

					wp_verify_nonce($wp_nonce, 'funnel_offers');

					$wps_wocuf_pro_all_funnels = get_option('wps_wocuf_funnels_list', array());

					$wps_wocuf_pro_buy_text = get_option('wps_wocuf_pro_buy_text', esc_html__('Buy Now', 'woo-one-click-upsell-funnel'));

					$wps_wocuf_pro_no_text = get_option('wps_wocuf_pro_no_text', esc_html__('No,thanks', 'woo-one-click-upsell-funnel'));

					$wps_wocuf_pro_before_offer_price_text = get_option('wps_wocuf_pro_before_offer_price_text', esc_html__('Special Offer Price', 'woo-one-click-upsell-funnel'));

					$wps_wocuf_pro_offered_products = isset($wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_products_in_offer']) ? $wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_products_in_offer'] : array();

					$wps_wocuf_pro_offered_discount = isset($wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_offer_discount_price']) ? $wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_offer_discount_price'] : array();

					$wps_wocuf_pro_buy_button_color = get_option('wps_wocuf_pro_buy_button_color', '');

					$ocuf_th_button_color = get_option('wps_wocuf_pro_thanks_button_color', '');

					$result .= '<div style="display:none;" id="wps_wocuf_pro_offer_loader"><img id="wps-wocuf-loading-offer" src="' . WPS_WOCUF_URL . 'public/images/ajax-loader.gif"></div><div class="wps_wocuf_pro_offer_container"><div class="woocommerce"><div class="wps_wocuf_pro_special_offers_for_you">';

					$wps_wocuf_pro_offer_banner_text = get_option('wps_wocuf_pro_offer_banner_text', esc_html__('Special Offer For You Only', 'woo-one-click-upsell-funnel'));

					$result .= '<div class="wps_wocuf_pro_special_offer_banner">
								<h1>' . trim($wps_wocuf_pro_offer_banner_text, '"') . '</h1></div>';

					$wps_wocuf_pro_single_offered_product = '';

					if (! empty($wps_wocuf_pro_offered_products[$offer_id])) {

						// In v2.0.0, it was array so handling to get the first product id.
						if (is_array($wps_wocuf_pro_offered_products[$offer_id]) && count($wps_wocuf_pro_offered_products[$offer_id])) {

							foreach ($wps_wocuf_pro_offered_products[$offer_id] as $handling_offer_product_id) {

								$wps_wocuf_pro_single_offered_product = absint($handling_offer_product_id);
								break;
							}
						} else {

							$wps_wocuf_pro_single_offered_product = absint($wps_wocuf_pro_offered_products[$offer_id]);
						}
					}

					$wps_wocuf_pro_original_offered_product = wc_get_product($wps_wocuf_pro_single_offered_product);

					$original_price = $wps_wocuf_pro_original_offered_product->get_price_html();

					$product = $wps_wocuf_pro_original_offered_product;

					if (! $wps_wocuf_pro_original_offered_product->is_type('variable')) {
						$wps_wocuf_pro_offered_product = $this->wps_wocuf_pro_change_offered_product_price($wps_wocuf_pro_original_offered_product, $wps_wocuf_pro_offered_discount[$offer_id]);

						$product = $wps_wocuf_pro_offered_product;
					}

					$result .= '<div class="wps_wocuf_pro_main_wrapper">';

					$image = wp_get_attachment_image_src(get_post_thumbnail_id($wps_wocuf_pro_single_offered_product), 'full');

					if (empty($image[0])) {
						$image[0] = wc_placeholder_img_src();
					}

					$result .= '<div class="wps_wocuf_pro_product_image"><img src="' . $image[0] . '"></div>';

					$result .= '<div class="wps_wocuf_pro_offered_product"><div class="wps_wocuf_pro_product_title"><h2>' . $product->get_title() . '</h2></div>';

					$result .= '<div class="wps_wocuf_pro_offered_product_description">
							    <p class="wps_wocuf_pro_product_desc">' . $product->get_description() . '</p></div>';

					$result .= '<div class="wps_wocuf_pro_product_price">
						    	<h4>' . $wps_wocuf_pro_before_offer_price_text . ' :
									 ' . $product->get_price_html() . '</h4></div></div></div>';

					$result .= '<div class="wps_wocuf_pro_offered_product_actions">
				    			<form class="wps_wocuf_pro_offer_form" method="post">
								<input type="hidden" name="ocuf_ns" value="' . $wp_nonce . '">
								<input type="hidden" name="ocuf_fid" value="' . $funnel_id . '">
								<input type="hidden" class="wps_wocuf_pro_variation_id" name="product_id" value="' . absint($product->get_id()) . '">
								<div id="wps_wocuf_pro_variation_attributes" ></div>
								<input type="hidden" name="ocuf_ofd" value="' . $offer_id . '">
								<input type="hidden" name="ocuf_ok" value="' . $order_key . '">
								<input type="hidden" name="wps_wocuf_post_nonce" value="' . wp_create_nonce('wps_wocuf_field_post_nonce') . '">
								<input type="hidden" name="wps_wocuf_after_post_nonce" value="' . wp_create_nonce('wps_wocuf_after_field_post_nonce') . '">
								<button data-id="' . $funnel_id . '" style="background-color:' . $wps_wocuf_pro_buy_button_color . '" class="wps_wocuf_pro_buy wps_wocuf_pro_custom_buy" type="submit" name="wps_wocuf_pro_buy">' . $wps_wocuf_pro_buy_text . '</button></form>
								<a style="color:' . $ocuf_th_button_color . '" 
								class="wps_wocuf_pro_skip wps_wocuf_pro_no" 
								href="?ocuf_ns=' . $wp_nonce . '
								&ocuf_th=1&ocuf_ok=' . $order_key . '
								&ocuf_ofd=' . $offer_id . '
								&ocuf_fid=' . $funnel_id . '">
								' . $wps_wocuf_pro_no_text . '</a>
								</div>
				    		</div></div>';

					$result .= '</div>';

					$result .= '</div></div></div>';
				} else {
					$error_msg = esc_html__('You ran out of the special offers session.', 'woo-one-click-upsell-funnel');

					$link_text = esc_html__('Go to the "Order details" page.', 'woo-one-click-upsell-funnel');

					$error_msg = apply_filters('wps_wocuf_pro_error_message', $error_msg);

					$link_text = apply_filters('wps_wocuf_pro_order_details_link_text', $link_text);

					$order_received_url = wc_get_endpoint_url('order-received', $order_id, wc_get_page_permalink('checkout'));

					$order_received_url = add_query_arg('key', $order_key, $order_received_url);

					$result .= $error_msg . '<a href="' . $order_received_url . '" class="button">' . $link_text . '</a>';
				}
			} else {
				$error_msg = esc_html__('You ran out of the special offers session.', 'woo-one-click-upsell-funnel');

				$link_text = esc_html__('Go to the "Order details" page.', 'woo-one-click-upsell-funnel');

				$error_msg = apply_filters('wps_wocuf_pro_error_message', $error_msg);

				$link_text = apply_filters('wps_wocuf_pro_order_details_link_text', $link_text);

				$order_received_url = wc_get_endpoint_url('order-received', $order_id, wc_get_page_permalink('checkout'));

				$order_received_url = add_query_arg('key', $order_key, $order_received_url);

				$result .= $error_msg . '<a href="' . $order_received_url . '" class="button">' . $link_text . '</a>';
			}
		}

		if (! isset($_GET['ocuf_ok']) || ! isset($_GET['ocuf_ofd']) || ! isset($_GET['ocuf_fid'])) {
			$wps_wocuf_pro_no_offer_text = get_option('wps_wocuf_pro_no_offer_text', esc_html__('Sorry, you have no offers', 'woo-one-click-upsell-funnel'));

			$result .= '<div class="wps-wocuf_pro-no-offer"><h2>' . trim($wps_wocuf_pro_no_offer_text, '"') . '</h2>';

			$result .= '<a class="button wc-backward" href="' . esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))) . '">' . esc_html__('Return to Shop', 'woo-one-click-upsell-funnel') . '</a></div>';
		}

		return $result;
	}



	/**
	 * Applying offer discount on product price.
	 *
	 * @since    3.0.0
	 * @param    object $temp_product    Object of product.
	 * @param    string $price           Offer price.
	 * @return   object     $temp_product    Object of product with new offer price.
	 */
	public function wps_wocuf_pro_change_offered_product_price($temp_product, $price)
	{

		if (! empty($price) && ! empty($temp_product)) {

			$payable_price = $temp_product->get_price();
			$sale_price    = $temp_product->get_sale_price();
			$regular_price = $temp_product->get_regular_price();
			$is_fixed      = false;

			// Change amount in case of chargeable currency is different.
			if (! empty(WC()->session) && WC()->session->__isset('s_selected_currency') && function_exists('wps_wmcs_fixed_price_for_simple_sales_price')) {
				$_regular_price = wps_wmcs_fixed_price_for_simple_regular_price($temp_product->get_id());
				$_sale_price    = wps_wmcs_fixed_price_for_simple_sales_price($temp_product->get_id());

				$sale_price    = ! empty($_sale_price) ? $_sale_price : $sale_price;
				$regular_price = ! empty($_regular_price) ? $_regular_price : $regular_price;
				$payable_price = ! empty($sale_price) ? $sale_price : $regular_price;
				if (! empty($_regular_price) || ! empty($_sale_price)) {
					$is_fixed = true;
				}
			}

			// Discount is in %.
			if (false !== strpos($price, '%')) {

				$discounted_percent = trim($price, '%');
				$discounted_price   = floatval($payable_price) * (floatval($discounted_percent) / 100);

				// Original price must be greater than zero.
				if ($payable_price > 0) {

					$offer_price = $payable_price - $discounted_price;
				} else {

					$offer_price = $payable_price;
				}
			} else { // Discount is fixed.

				$offer_price = floatval($price);
			}

			/**
			 * Original price : $payable_price.
			 * Sale price : $sale_price.
			 * Regular price : $regular_price.
			 * Offer price : $offer_price.
			 */
			$wps_upsell_global_settings = get_option('wps_upsell_lite_global_options', array());

			$price_html_format = ! empty($wps_upsell_global_settings['offer_price_html_type']) ? $wps_upsell_global_settings['offer_price_html_type'] : 'regular';

			// ̶S̶a̶l̶e̶ ̶P̶r̶i̶c̶e̶  Offer Price.
			if ('sale' === $price_html_format) {

				if (! empty($sale_price)) {

					$temp_product->set_regular_price($sale_price);
					$temp_product->set_sale_price($offer_price);
				} else {

					// No sale price is present.
					$temp_product->set_sale_price($offer_price);
				}
			} else { // ̶R̶e̶g̶u̶l̶a̶r̶ ̶P̶r̶i̶c̶e̶ Offer Price.

				// In this case set the regular price as sale.
				$temp_product->set_sale_price($offer_price);
			}

			// Change amount in case of chargeable currency is different.
			if (false === $is_fixed && ! empty(WC()->session) && WC()->session->__isset('s_selected_currency') && class_exists('Mwb_Multi_Currency_Switcher_For_Woocommerce_Public')) {
				$currency_switcher_obj = new Mwb_Multi_Currency_Switcher_For_Woocommerce_Public('WPS Multi Currency Switcher For WooCommerce', '1.2.0');
				$offer_price           = $currency_switcher_obj->wps_mmcsfw_get_price_of_product($offer_price, $temp_product->get_id());
			}

			$temp_product->set_price($offer_price);
		} else {
			/**
			 * If the discount is 0 and fixed.
			 */
			$temp_product->set_price(0);
		}

		return $temp_product;
	}


	/**
	 * Shortcode for offer - Buy now button.
	 * Returns : Link :)
	 *
	 * Also Requires the ID to be applied on the link or button, for variable products only.
	 * Using this ID form is submitted from js.
	 *
	 * @since       3.0.0
	 */
	public function buy_now_shortcode_content()
	{

		$validate_shortcode = $this->validate_shortcode();

		if ($validate_shortcode) {

			$product_id = $this->get_upsell_product_id_for_shortcode();

			if (! empty($product_id)) {

				$post_type = get_post_type($product_id);

				if ('product' !== $post_type && 'product_variation' !== $post_type) {

					return '';
				}

				if ('live_offer' === $validate_shortcode) {

					$upsell_product = wc_get_product($product_id);

					if ($upsell_product->is_type('variable')) {

						// In this case buy now form ( in variation selector shortcode ) will be posted from js.
						$buy_now_link = '#wps_upsell';
					} else {

						$secure_nonce      = wp_create_nonce('wps-upsell-auth-nonce');
						$id_nonce_verified = wp_verify_nonce($secure_nonce, 'wps-upsell-auth-nonce');

						if (! $id_nonce_verified) {
							wp_die(esc_html__('Nonce Not verified', 'woo-one-click-upsell-funnel'));
						}

						$wp_nonce  = isset($_GET['ocuf_ns']) ? sanitize_text_field(wp_unslash($_GET['ocuf_ns'])) : '';
						$order_key = isset($_GET['ocuf_ok']) ? sanitize_text_field(wp_unslash($_GET['ocuf_ok'])) : '';
						$offer_id  = isset($_GET['ocuf_ofd']) ? sanitize_text_field(wp_unslash($_GET['ocuf_ofd'])) : '';
						$funnel_id = isset($_GET['ocuf_fid']) ? sanitize_text_field(wp_unslash($_GET['ocuf_fid'])) : '';

						$buy_now_link = '?wps_wocuf_pro_buy
						=true&ocuf_ns=' . $wp_nonce . '
						&ocuf_ok=' . $order_key . '
						&ocuf_ofd=' . $offer_id . '
						&ocuf_fid=' . $funnel_id . '
						&product_id=' . $product_id;
					}
				} elseif ('admin_view' === $validate_shortcode) {

					$buy_now_link = '#preview';
				}

				return $buy_now_link;
			}
		}
	}


	/**
	 * Get upsell product id from offer page id.
	 *
	 * @since    3.0.0
	 */
	public function get_upsell_product_id_for_shortcode()
	{

		// Firstly try to get product id from url offer and funnel id i.e. the case of live offer.

		$product_id_from_get = wps_upsell_lite_get_pid_from_url_params();

		// When it is live offer.
		if ('true' === $product_id_from_get['status']) {

			$funnel_id = $product_id_from_get['funnel_id'];
			$offer_id  = $product_id_from_get['offer_id'];

			// Get all funnels.
			$all_funnels = get_option('wps_wocuf_funnels_list', array());

			$product_id = ! empty($all_funnels[$funnel_id]['wps_wocuf_products_in_offer'][$offer_id]) ? $all_funnels[$funnel_id]['wps_wocuf_products_in_offer'][$offer_id] : '';

			return $product_id;
		}

		// Will only execute from here when it is not live offer.

		// Get product id from current offer page post id.
		global $post;
		$offer_page_id = $post->ID;

		// Means this is Upsell offer template.
		$funnel_data = get_post_meta($offer_page_id, 'wps_upsell_funnel_data', true);

		$product_found_in_funnel = false;

		if (! empty($funnel_data) && is_array($funnel_data) && count($funnel_data)) {

			$funnel_id = $funnel_data['funnel_id'];
			$offer_id  = $funnel_data['offer_id'];

			// Get all funnels.
			$all_funnels = get_option('wps_wocuf_funnels_list', array());

			$product_id = ! empty($all_funnels[$funnel_id]['wps_wocuf_products_in_offer'][$offer_id]) ? $all_funnels[$funnel_id]['wps_wocuf_products_in_offer'][$offer_id] : '';

			if (! empty($product_id)) {

				$product_found_in_funnel = true;
				return $product_id;
			}
		}

		// Get global product only for Custom Offer page and not for Upsell offer templates.
		if (empty($funnel_data) && ! $product_found_in_funnel) {

			$wps_upsell_global_settings = get_option('wps_upsell_lite_global_options', array());

			$product_id = ! empty($wps_upsell_global_settings['global_product_id']) ? $wps_upsell_global_settings['global_product_id'] : '';

			if (! empty($product_id)) {

				return $product_id;
			}
		}

		/**
		 * Product not selected? show alert! Will run one time in one reload.
		 * Run this alert only on a page.
		 */
		if (is_page() && false === wp_cache_get('wps_upsell_no_product_in_offer')) {

			$product_not_selected_alert = esc_html__('One Click Upsell', 'woo-one-click-upsell-funnel');

			// For Upsell offer template.
			if (! empty($funnel_data)) {

				$product_not_selected_content = esc_html__('Offer Product is not selected, please save a Offer Product in Funnel Offer settings.', 'woo-one-click-upsell-funnel');
			} else {

				// For Custom offer page.
				$product_not_selected_content = esc_html__('Custom Offer page - detected! Please save a global Offer product in Global settings for testing purpose.', 'woo-one-click-upsell-funnel');
			}

			?>

			<script type="text/javascript">
				var product_not_selected_alert = '<?php echo esc_html($product_not_selected_alert); ?>';

				var product_not_selected_content = '<?php echo esc_html($product_not_selected_content); ?> ';

				swal(product_not_selected_alert, product_not_selected_content, 'warning');
			</script>

		<?php
		}

		wp_cache_set('wps_upsell_no_product_in_offer', 'true');
	}



	/**
	 * Shortcode for offer - No thanks button.
	 * Returns : Link :)
	 *
	 * @since       3.0.0
	 */
	public function no_thanks_shortcode_content()
	{

		$validate_shortcode = $this->validate_shortcode();

		if ($validate_shortcode) {

			$product_id = $this->get_upsell_product_id_for_shortcode();

			if (! empty($product_id)) {

				$post_type = get_post_type($product_id);

				if ('product' !== $post_type && 'product_variation' !== $post_type) {

					return '';
				}

				if ('live_offer' === $validate_shortcode) {

					$secure_nonce      = wp_create_nonce('wps-upsell-auth-nonce');
					$id_nonce_verified = wp_verify_nonce($secure_nonce, 'wps-upsell-auth-nonce');

					if (! $id_nonce_verified) {
						wp_die(esc_html__('Nonce Not verified', 'woo-one-click-upsell-funnel'));
					}
					$wp_nonce  = isset($_GET['ocuf_ns']) ? sanitize_text_field(wp_unslash($_GET['ocuf_ns'])) : '';
					$order_key = isset($_GET['ocuf_ok']) ? sanitize_text_field(wp_unslash($_GET['ocuf_ok'])) : '';
					$offer_id  = isset($_GET['ocuf_ofd']) ? sanitize_text_field(wp_unslash($_GET['ocuf_ofd'])) : '';
					$funnel_id = isset($_GET['ocuf_fid']) ? sanitize_text_field(wp_unslash($_GET['ocuf_fid'])) : '';

					$no_thanks_link = '?ocuf_ns=' . $wp_nonce . '
					&ocuf_th=1&ocuf_ok=' . $order_key . '
					&ocuf_ofd=' . $offer_id . '
					&ocuf_fid=' . $funnel_id;
				} elseif ('admin_view' === $validate_shortcode) {

					$no_thanks_link = '#preview';
				}

				return $no_thanks_link;
			}
		}
	}



	/**
	 * Shortcode for Upsell product title.
	 * Returns : Just the Content :)
	 *
	 * @since       3.0.0
	 */
	public function product_title_shortcode_content()
	{

		$validate_shortcode = $this->validate_shortcode();

		if ($validate_shortcode) {

			$product_id = $this->get_upsell_product_id_for_shortcode();

			if (! empty($product_id)) {

				$post_type = get_post_type($product_id);

				if ('product' !== $post_type && 'product_variation' !== $post_type) {

					return '';
				}

				$upsell_product = wc_get_product($product_id);

				$upsell_product_title = $upsell_product->get_title();
				$upsell_product_title = ! empty($upsell_product_title) ? $upsell_product_title : '';

				return $upsell_product_title;
			}
		}
	}


	/**
	 * Shortcode for Upsell product description.
	 * Returns : Just the Content :)
	 *
	 * @since       3.0.0
	 */
	public function product_description_shortcode_content()
	{

		$validate_shortcode = $this->validate_shortcode();

		if ($validate_shortcode) {

			$product_id = $this->get_upsell_product_id_for_shortcode();

			if (! empty($product_id)) {

				$post_type = get_post_type($product_id);

				if ('product' !== $post_type && 'product_variation' !== $post_type) {

					return '';
				}

				$upsell_product = wc_get_product($product_id);

				$upsell_product_desc = $upsell_product->get_description();
				$upsell_product_desc = ! empty($upsell_product_desc) ? $upsell_product_desc : '';

				return $upsell_product_desc;
			}
		}
	}


	/**
	 * Shortcode for Upsell product short description.
	 * Returns : Just the Content :)
	 *
	 * @since       3.0.0
	 */
	public function product_short_description_shortcode_content()
	{

		$validate_shortcode = $this->validate_shortcode();

		if ($validate_shortcode) {

			$product_id = $this->get_upsell_product_id_for_shortcode();

			if (! empty($product_id)) {

				$post_type = get_post_type($product_id);

				if ('product' !== $post_type && 'product_variation' !== $post_type) {

					return '';
				}

				$upsell_product = wc_get_product($product_id);

				$upsell_product_short_desc = $upsell_product->get_short_description();
				$upsell_product_short_desc = ! empty($upsell_product_short_desc) ? $upsell_product_short_desc : '';

				return $upsell_product_short_desc;
			}
		}
	}


	/**
	 * Shortcode for Upsell product image.
	 *
	 * @param mixed $atts shortcode attributes.
	 * @since       3.0.0
	 */
	public function product_image_shortcode_content($atts)
	{

		$validate_shortcode = $this->validate_shortcode();
		if ($validate_shortcode) {

			$live_params_from_url = wps_upsell_lite_get_pid_from_url_params();

			// When Live Offer.
			if (! empty($live_params_from_url['status']) && 'true' === $live_params_from_url['status']) {

				$offer_id  = ! empty($live_params_from_url['offer_id']) ? wc_clean($live_params_from_url['offer_id']) : '';
				$funnel_id = ! empty($live_params_from_url['funnel_id']) ? wc_clean($live_params_from_url['funnel_id']) : '';

				if (! empty($funnel_id) && ! empty($offer_id)) {

					$all_funnels = get_option('wps_wocuf_funnels_list', array());

					$upsell_product_image_post_id = ! empty($all_funnels[$funnel_id]['wps_upsell_offer_image'][$offer_id]) ? $all_funnels[$funnel_id]['wps_upsell_offer_image'][$offer_id] : '';

					if (! empty($upsell_product_image_post_id)) {

						$image_attributes = wp_get_attachment_image_src($upsell_product_image_post_id, 'full');

						$upsell_product_image_src = ! empty($image_attributes[0]) && filter_var($image_attributes[0], FILTER_VALIDATE_URL) ? $image_attributes[0] : false;
					}

					if (! empty($upsell_product_image_src)) {

						// Shortcode attributes.
						$atts = shortcode_atts(
							array(
								'id'    => '',
								'class' => '',
								'style' => '',
							),
							$atts
						);

						$id    = $atts['id'];
						$class = $atts['class'];
						$style = $atts['style'];

						$upsell_product_image_src_div =
							"<div id='$id' class='wps_upsell_offer_product_image $class' style='$style'>
								<img src='$upsell_product_image_src'>
							</div>";

						return $upsell_product_image_src_div;
					}
				}
			} else { // When not Live Offer.

				global $post;
				$offer_page_id = $post->ID;

				// Means this is Upsell offer template.
				$funnel_data = get_post_meta($offer_page_id, 'wps_upsell_funnel_data', true);

				if (! empty($funnel_data) && is_array($funnel_data)) {

					$funnel_id = $funnel_data['funnel_id'];
					$offer_id  = $funnel_data['offer_id'];

					if (! empty($funnel_id) && ! empty($offer_id)) {

						$all_funnels = get_option('wps_wocuf_funnels_list', array());

						$upsell_product_image_post_id = ! empty($all_funnels[$funnel_id]['wps_upsell_offer_image'][$offer_id]) ? $all_funnels[$funnel_id]['wps_upsell_offer_image'][$offer_id] : '';

						if (! empty($upsell_product_image_post_id)) {

							$image_attributes = wp_get_attachment_image_src($upsell_product_image_post_id, 'full');

							$upsell_product_image_src = ! empty($image_attributes[0]) && filter_var($image_attributes[0], FILTER_VALIDATE_URL) ? $image_attributes[0] : false;
						}

						if (! empty($upsell_product_image_src)) {

							// Shortcode attributes.
							$atts = shortcode_atts(
								array(
									'id'    => '',
									'class' => '',
									'style' => '',
								),
								$atts
							);

							$id    = $atts['id'];
							$class = $atts['class'];
							$style = $atts['style'];

							$upsell_product_image_src_div =
								"<div id='$id' class='wps_upsell_offer_product_image $class' style='$style'>
									<img src='$upsell_product_image_src'>
								</div>";

							return $upsell_product_image_src_div;
						}
					}
				}
			}

			$product_id = $this->get_upsell_product_id_for_shortcode();

			if (! empty($product_id)) {

				$post_type = get_post_type($product_id);

				if ('product' !== $post_type && 'product_variation' !== $post_type) {

					return '';
				}

				$upsell_product_image = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'full');

				$upsell_product_image_src = ! empty($upsell_product_image[0]) ? $upsell_product_image[0] : wc_placeholder_img_src();

				// Shortcode attributes.
				$atts = shortcode_atts(
					array(
						'id'    => '',
						'class' => '',
						'style' => '',
					),
					$atts
				);

				$id    = $atts['id'];
				$class = $atts['class'];
				$style = $atts['style'];

				$upsell_product_image_src_div =
					"<div id='$id' class='wps_upsell_offer_product_image $class' style='$style'>
						<img src='$upsell_product_image_src'>
					</div>";
				return $upsell_product_image_src_div;
			}
		}
	}


	/**
	 * Shortcode for Upsell product price.
	 *
	 * @param mixed $atts shortcode attributes.
	 * @since       3.0.0
	 */
	public function product_price_shortcode_content($atts)
	{

		$validate_shortcode = $this->validate_shortcode();

		if ($validate_shortcode) {

			$product_id = $this->get_upsell_product_id_for_shortcode();

			if (! empty($product_id)) {

				$post_type = get_post_type($product_id);

				if ('product' !== $post_type && 'product_variation' !== $post_type) {
					return '';
				}

				$upsell_product = wc_get_product($product_id);

				// Get offer discount.
				$upsell_offered_discount = wps_upsell_lite_get_product_discount();

				// Apply discount on product.
				if (! empty($upsell_offered_discount)) {

					$upsell_product = $this->wps_wocuf_pro_change_offered_product_price($upsell_product, $upsell_offered_discount);
				} else {
					$upsell_product->set_price(0);
				}

				$upsell_product_price_html = $upsell_product->get_price_html();
				$upsell_product_price_html = ! empty($upsell_product_price_html) ? $upsell_product_price_html : '';

				/**
				 * Replaces the currency switcher fixed price.
				 */
				if (! empty(WC()->session) && WC()->session->__isset('s_selected_currency')) {

					if (function_exists('wps_wmcs_fixed_price_for_simple_sales_price')) {
						$_regular_price = wps_wmcs_fixed_price_for_simple_regular_price($upsell_product->get_id());
						$_sale_price    = wps_wmcs_fixed_price_for_simple_sales_price($upsell_product->get_id());
					} else {
						$_regular_price = $upsell_product->get_regular_price();
						$_sale_price    = $upsell_product->get_price();
					}

					if (empty($upsell_offered_discount)) {
						$_sale_price = 'full_disc';
					}

					// In case of fixed custom price in currency switcher.
					if (! empty($_regular_price) || ! empty($_sale_price)) {
						/**
						 * Upsell offer will be zero then the offer price will not be changed.
						 * In that case add sale price as the payable price.
						 */
						if (empty($upsell_offered_discount)) {
							if (! empty($_sale_price) && 'full_disc' !== $_sale_price) {
								$upsell_product_price_html = wc_format_sale_price($_regular_price, $_sale_price);
							} elseif ('full_disc' === $_sale_price) {
								$upsell_product_price_html = wc_format_sale_price($_regular_price, 0);
							} else {
								$upsell_product_price_html = wc_price($_regular_price);
							}
						} else {
							$upsell_product_price_html = wc_format_sale_price($_regular_price, $upsell_product->get_price());
						}
					}
				}

				// Remove amount class, as it changes price css wrt theme change.
				$upsell_product_price_html = str_replace(' amount', ' wps-upsell-amount', $upsell_product_price_html);

				// Shortcode attributes.
				$atts = shortcode_atts(
					array(
						'id'    => '',
						'class' => '',
						'style' => '',
					),
					$atts
				);

				$id    = $atts['id'];
				$class = $atts['class'];
				$style = $atts['style'];

				$upsell_product_price_html_div = "<div id='$id' class='wps_upsell_offer_product_price $class' style='$style'>
						$upsell_product_price_html</div>";

				/**
				 * Replaces the currrency symbol.
				 */
				if (! empty(WC()->session) && WC()->session->__isset('s_selected_currency')) {

					$selected_currency = WC()->session->get('s_selected_currency');
					$store_currency    = get_woocommerce_currency();

					if ($selected_currency !== $store_currency) {
						$store_currency_symbol    = get_option('wps_mmcsfw_symbol_' . $store_currency);
						$selected_currency_symbol = get_option('wps_mmcsfw_symbol_' . $selected_currency);

						// Remove default currency into selected currency.
						$upsell_product_price_html_div = str_replace($store_currency_symbol, $selected_currency_symbol, $upsell_product_price_html_div);
					}
				}

				return $upsell_product_price_html_div;
			}
		}
	}


	/**
	 * Shortcode for Upsell product shipping price.
	 *
	 * @param mixed $atts shortcode attributes.
	 * @since       3.0.0
	 */
	public function upsell_product_shipping_price_shortcode_content($atts)
	{

		$validate_shortcode = $this->validate_shortcode();

		if ($validate_shortcode) {

			$product_id = $this->get_upsell_product_id_for_shortcode();
			$atts = shortcode_atts(
				array(
					'id'    => '',
					'class' => '',
					'style' => '',
				),
				$atts
			);
			$id    = $atts['id'];
			$class = $atts['class'];
			$style = $atts['style'];
			$upsell_shipping_product = wps_wocfo_hpos_get_meta_data($product_id, 'wps_upsell_simple_shipping_product_' . $product_id, true);

			$upsell_product_price_html_div = "Shipping Price <br> <div id='$id' class='wps_upsell_offer_product_price $class' style='$style'>
						" . wc_price($upsell_shipping_product) . '</div>';
		}

		return $upsell_product_price_html_div;
	}

	/**
	 * Shortcode for offer - product variations.
	 *
	 * @since       3.0.0
	 */
	public function variations_selector_shortcode_content()
	{

		return '';
	}


	/**
	 * Shortcode for Default Gutenberg offer identification.
	 * Returns : empty string.
	 *
	 * @since       3.0.0
	 */
	public function default_offer_identification()
	{

		return '';
	}


	/**
	 * Shortcode for star review.
	 * Returns : star review html.
	 *
	 * @param mixed $atts shrtcode attributes.
	 * @since       3.0.0
	 */
	public function product_star_review($atts)
	{

		$stars = ! empty($atts['stars']) ? abs($atts['stars']) : '5';

		$stars = ($stars >= 1 && $stars <= 5) ? $stars : '5';

		$stars_percent = $stars * 20;

		$review_html = '<div class="wps-upsell-star-rating"><span style="width: ' . esc_attr($stars_percent) . '%;"></div>';

		return $review_html;
	}


	/**
	 * Shortcode for offer - Timer button.
	 *
	 * @param array  $atts    atts.
	 * @param string $content content.
	 *
	 * @since       3.0.0
	 */
	public function timer_shortcode_content($atts, $content = '')
	{

		$validate_shortcode = $this->validate_shortcode();

		if ($validate_shortcode) {

			$minutes    = ! empty($atts['minutes']) ? abs($atts['minutes']) : 5;
			$expiration = $minutes * 60;

			if (empty($expiration) || ! is_numeric($expiration)) {

				return esc_html__('Time is not specified correctly.', 'woo-one-click-upsell-funnel');
			}

		?>

			<?php ob_start(); ?>

			<?php if (false === wp_cache_get('wps_upsell_countdown_timer')) : ?>

				<script type="text/javascript">
					jQuery(document).ready(function($) {

						// Set the date we're counting down to.
						var current = new Date();
						var expiration = parseFloat(<?php echo (esc_html($expiration)); ?>); // Digit in seconds.
						<?php
						$secure_nonce      = wp_create_nonce('wps-upsell-auth-nonce');
						$id_nonce_verified = wp_verify_nonce($secure_nonce, 'wps-upsell-auth-nonce');

						if (! $id_nonce_verified) {
							wp_die(esc_html__('Nonce Not verified', 'woo-one-click-upsell-funnel'));
						}
						?>
						var offer_id = <?php echo ! empty($_GET['ocuf_ofd']) ? esc_html(sanitize_text_field(wp_unslash($_GET['ocuf_ofd']))) : 'null'; ?>;

						var timer_limit = sessionStorage.getItem('timerlimit_' + offer_id);
						var countDowntime = null != offer_id && null != timer_limit ? timer_limit : current.setSeconds(current.getSeconds() + expiration);

						// Update the count down every 1 second.
						var timer = setInterval(function() {

							// Find the distance between now and the count down time.
							var distance = countDowntime - new Date().getTime();

							// Time calculations for days, hours, minutes and seconds
							var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
							var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
							var seconds = Math.floor((distance % (1000 * 60)) / 1000);

							// If the count down is finished, redirect;
							if (distance < 0) {

								clearInterval(timer);

								// Expired the session before redirecting.
								$('a').each(function() {

									if (this.href.includes('ocuf_th')) {

										jQuery(this)[0].click();
									}
								});

							} else {

								if (seconds.toString().length == '1') {

									seconds = '0' + seconds;

								}

								if (minutes.toString().length == '1') {

									minutes = '0' + minutes;

								}

								$('.wps_upsell_lite_display_hours').html(hours);
								$('.wps_upsell_lite_display_minutes').html(minutes);
								$('.wps_upsell_lite_display_seconds').html(seconds);

							}

						}, 300);

						sessionStorage.setItem('timerlimit_' + offer_id, countDowntime);
					});
				</script>

				<?php wp_cache_set('wps_upsell_countdown_timer', 'true'); ?>

			<?php endif; ?>

			<!-- Countdown timer html. -->
			<span class="wps_upsell_lite_display_timer_wrap">
				<span class="wps_upsell_lite_timer_digit">
					<span class="wps_upsell_lite_display_minutes wps_upsell_lite_display_timer">00</span>
					<span class="wps_upsell_lite_text"><?php esc_html_e('minutes', 'woo-one-click-upsell-funnel'); ?></span>
				</span>
				<span class="wps_upsell_lite_timer_digit">
					<span class="wps_upsell_lite_display_timer_col">:</span>
				</span>
				<span class="wps_upsell_lite_timer_digit">
					<span class="wps_upsell_lite_display_seconds wps_upsell_lite_display_timer">00</span>
					<span class="wps_upsell_lite_text"><?php esc_html_e('seconds', 'woo-one-click-upsell-funnel'); ?></span>
				</span>
			</span>

			<?php

			$output = ob_get_contents();
			ob_end_clean();

			return $output;
		}
	}


	/**
	 * Shortcode for quantity.
	 * Returns : html :)
	 *
	 * Shows woocommerce quantity field.
	 *
	 * @param mixed $atts shortcode attributes.
	 * @since       3.0.0
	 */
	public function quantity_shortcode_content($atts)
	{

		$validate_shortcode = $this->validate_shortcode();

		if ($validate_shortcode) {

			$maximum = ! empty($atts['max']) ? abs($atts['max']) : 3;
			$minimum = ! empty($atts['min']) ? abs($atts['min']) : 1;

			$product_id = $this->get_upsell_product_id_for_shortcode();

			if (! empty($product_id)) {

				$post_type = get_post_type($product_id);
				$product   = wc_get_product($product_id);

				if (empty($product)) {

					return '';
				}

				if ('product' !== $post_type && 'product_variation' !== $post_type) {

					return '';
				}

				ob_start();

			?>

				<!-- Countdown timer html. -->
				<div class="wps_upsell_quantity quantity">
					<label class="screen-reader-text" for="wps_upsell_quantity_field"><?php echo esc_html($product->get_title()); ?></label>
					<input type="number" id="wps_upsell_quantity_field" class="input-text qty text wps_upsell_quantity_input" step="1" min="<?php echo (esc_html($minimum)); ?>" max="<?php echo (esc_html($maximum)); ?>" value="1" title="Qty" inputmode="numeric">
				</div>

		<?php

				$output = ob_get_contents();
				ob_end_clean();

				return $output;
			}
		}
	}

	/**
	 * Remove Currency switcher on upsell page.
	 *
	 * @param mixed $content content.
	 * @since 3.6.3
	 */
	public function hide_switcher_on_upsell_page($content = '')
	{

		$validate_shortcode = $this->validate_shortcode();
		if ('live_offer' === $validate_shortcode) {
			return '';
		} else {
			return $content;
		}
	}


	/**
	 * Remove http and https from Upsell Action shortcodes added by Page Builders.
	 *
	 * @param string $content content.
	 *
	 * @since    2.0.3
	 */
	public function filter_upsell_shortcodes_content($content = '')
	{

		$upsell_yes_shortcode = array('http://?wps_wocuf_pro_buy', 'https://?wps_wocuf_pro_buy');
		$upsell_no_shortcode  = array('http://?ocuf_ns', 'https://?ocuf_ns');

		$content = str_replace($upsell_yes_shortcode, '?wps_wocuf_pro_buy', $content);
		$content = str_replace($upsell_no_shortcode, '?ocuf_ns', $content);

		$upsell_yes_shortcode = array('http://[wps_upsell_yes]', 'https://[wps_upsell_yes]');
		$upsell_no_shortcode  = array('http://[wps_upsell_no]', 'https://[wps_upsell_no]');

		$content = str_replace($upsell_yes_shortcode, '[wps_upsell_yes]', $content);
		$content = str_replace($upsell_no_shortcode, '[wps_upsell_no]', $content);

		return $content;
	}

	/**
	 * Allow Script tags in wp_kses
	 *
	 * @param array $allowedposttags allowed post tags.
	 * @return array
	 */
	public function wocuf_lite_allow_script_tags($allowedposttags)
	{
		$allowedposttags['script'] = array(
			'src'    => true,
			'height' => true,
			'width'  => true,
		);
		return $allowedposttags;
	}

	/**
	 * Initiate Upsell Orders before processing payment in case of checkout shortcode.
	 *
	 * @param int $order_id order id.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception Throws exception when error.
	 */
	public function wps_wocuf_initate_upsell_orders_shortcode_checkout_org($order_id)
	{
		$checkout_nonce = ! empty($_POST['checkout_order_processed_nonce']) ? sanitize_text_field(wp_unslash($_POST['checkout_order_processed_nonce'])) : '';

		if (isset($_POST['checkout_order_processed_nonce']) && wp_verify_nonce($checkout_nonce, 'checkout_order_processed_nonce')) {
			if (empty($_GET['wc-ajax']) || 'checkout' !== $_GET['wc-ajax']) {
				return;
			}
		}
		$order = wc_get_order($order_id);
		$this->wps_wocuf_initiate_upsell_orders($order);
	}



	/**
	 * Initiate Upsell Orders before processing payment.
	 *
	 * @param object $order Order data.
	 * @since    1.0.0
	 * @throws Exception Throws exception when error.
	 */
	public function wps_wocuf_initiate_upsell_orders($order)
	{

		$order_id = $order->get_id();
		$payment_method = $order->get_payment_method();

		$supported_gateways = wps_upsell_lite_supported_gateways();

		if (in_array($payment_method, $supported_gateways, true)) {

			$wps_wocuf_pro_all_funnels = get_option('wps_wocuf_funnels_list', array());

			$wps_wocuf_pro_flag = 0;

			$wps_wocuf_pro_proceed = false;

			if (empty($wps_wocuf_pro_all_funnels)) {

				return;
			} elseif (empty($order)) {

				return;
			}

			$funnel_redirect = false;

			if (! empty($order)) {
				if (function_exists('wcs_order_contains_subscription') && (wcs_order_contains_subscription($order_id) || wcs_order_contains_renewal($order_id))) {
					return;
				}

				$wps_wocuf_pro_placed_order_items = $order->get_items();

				$ocuf_ok = $order->get_order_key();

				$ocuf_ofd = 0;

				if (is_array($wps_wocuf_pro_all_funnels)) {

					// Move Global Funnels at the last of the array while maintaining it's key, so they execute at last.
					foreach ($wps_wocuf_pro_all_funnels as $funnel_key => $single_funnel_array) {

						$global_funnel = ! empty($single_funnel_array['wps_wocuf_global_funnel']) ? $single_funnel_array['wps_wocuf_global_funnel'] : '';

						// Check if global funnel.
						if ('yes' === $global_funnel) {

							// Unset.
							unset($wps_wocuf_pro_all_funnels[$funnel_key]);

							// Append at last with the same key.
							$wps_wocuf_pro_all_funnels[$funnel_key] = $single_funnel_array;
						}
					}

					// Main Foreach for Triggering Upsell Offers.
					foreach ($wps_wocuf_pro_all_funnels as $wps_wocuf_pro_single_funnel => $wps_wocuf_pro_funnel_data) {

						$is_global_funnel = ! empty($wps_wocuf_pro_funnel_data['wps_wocuf_global_funnel']) && 'yes' === $wps_wocuf_pro_funnel_data['wps_wocuf_global_funnel'] ? $wps_wocuf_pro_funnel_data['wps_wocuf_global_funnel'] : false;

						$wps_wocuf_pro_funnel_target_products = ! empty($wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_wocuf_target_pro_ids']) ? $wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_wocuf_target_pro_ids'] : array();
						$funnel_target_product_categories = ! empty($wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['target_categories_ids']) ? $wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['target_categories_ids'] : array();

						$wps_wocuf_pro_existing_offers = ! empty($wps_wocuf_pro_funnel_data['wps_wocuf_applied_offer_number']) ? $wps_wocuf_pro_funnel_data['wps_wocuf_applied_offer_number'] : array();

						// To get the first offer from current funnel.
						if (count($wps_wocuf_pro_existing_offers)) {

							foreach ($wps_wocuf_pro_existing_offers as $key => $value) {

								$ocuf_ofd = $key;
								break;
							}
						}

						if (is_array($wps_wocuf_pro_placed_order_items) && count($wps_wocuf_pro_placed_order_items)) {
							foreach ($wps_wocuf_pro_placed_order_items as $item_key => $wps_wocuf_pro_single_item) {
								$wps_wocuf_pro_variation_id = $wps_wocuf_pro_single_item->get_variation_id();

								$wps_wocuf_pro_product_id = $wps_wocuf_pro_single_item->get_product_id();

								$product_categories = array();

								if (! empty($funnel_target_product_categories)) {

									$product_cat_obj_array = get_the_terms($wps_wocuf_pro_product_id, 'product_cat');

									if (! empty($product_cat_obj_array) && is_array($product_cat_obj_array) && count($product_cat_obj_array)) {

										foreach ($product_cat_obj_array as $product_cat_obj) {

											if (! empty($product_cat_obj->term_id)) {

												$product_categories[] = $product_cat_obj->term_id;
											}
										}
									}
								}

								if (in_array((string) $wps_wocuf_pro_product_id, $wps_wocuf_pro_funnel_target_products, true) || (! empty($wps_wocuf_pro_variation_id) && in_array((string) $wps_wocuf_pro_variation_id, $wps_wocuf_pro_funnel_target_products, true)) || ($is_global_funnel) || (! empty($product_categories) && ! empty(array_intersect($product_categories, $funnel_target_product_categories)))) {

									// Check if funnel is saved after version 3.0.0.
									$funnel_saved_after_version_3 = ! empty($wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_upsell_fsav3']) ? $wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_upsell_fsav3'] : '';

									// For funnels saved after version 3.0.0.
									if ('true' === $funnel_saved_after_version_3) {

										// Check if funnel is live or not.
										$funnel_status = ! empty($wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_upsell_funnel_status']) ? $wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_upsell_funnel_status'] : '';

										// For Admin Funnel Will trigger for both Live and Sandbox statuses.

										if (! current_user_can('manage_options')) {

											if ('yes' !== $funnel_status) {

												// Break from placed order items loop and move to next funnel.
												break;
											}
										}
									}

									/**
									 * Check for funnel schedule.
									 * Since v3.0.0 convert data into array first.
									 */
									$wps_wocuf_pro_funnel_schedule = ! empty($wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_wocuf_pro_funnel_schedule']) ? $wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_wocuf_pro_funnel_schedule'] : array('7');

									if ('0' === $wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_wocuf_pro_funnel_schedule']) {

										$wps_wocuf_pro_funnel_schedule = array('0');
									} elseif (! is_array($wps_wocuf_pro_funnel_schedule)) {

										$wps_wocuf_pro_funnel_schedule = array($wps_wocuf_pro_funnel_schedule);
									}

									// In order to use server time only.
									$current_schedule = gmdate('w');

									if (in_array('7', $wps_wocuf_pro_funnel_schedule, true)) {

										$wps_wocuf_pro_proceed = true;
									} elseif (in_array((string) $current_schedule, $wps_wocuf_pro_funnel_schedule, true)) {

										$wps_wocuf_pro_proceed = true;
									}

									if (false === $wps_wocuf_pro_proceed) {

										// Break from placed order items loop and move to next funnel.
										break;
									}

									// Array of offers with product id.
									if (! empty($wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_wocuf_products_in_offer']) && is_array($wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_wocuf_products_in_offer'])) {

										/**
										 * Set funnel as shown if is exclusive offer funnel.
										 * Do it just after checking target.
										 * Exclusive Offer
										 */
										if (! empty($wps_wocuf_pro_funnel_data['wps_wocuf_exclusive_offer']) && 'yes' === $wps_wocuf_pro_funnel_data['wps_wocuf_exclusive_offer']) {

											// Check if funnel still exists.
											if (! empty($wps_wocuf_pro_funnel_data)) {

												if (! empty($wps_wocuf_pro_funnel_data['wps_wocuf_exclusive_offer']) && 'yes' === $wps_wocuf_pro_funnel_data['wps_wocuf_exclusive_offer']) {

													$offer_already_shown_to_users = ! empty($wps_wocuf_pro_funnel_data['offer_already_shown_to_users']) ? $wps_wocuf_pro_funnel_data['offer_already_shown_to_users'] : array();

													$current_customer = ! empty($order) ? $order->get_billing_email() : '';

													if (! empty($current_customer) && ! empty($offer_already_shown_to_users) && in_array($current_customer, $offer_already_shown_to_users, true)) {

														// Skip to next funnel.
														break;
													}

													// Not skipped. Mark as shown to this customer.
													array_push($offer_already_shown_to_users, $current_customer);
													$wps_wocuf_pro_funnel_data['offer_already_shown_to_users'] = $offer_already_shown_to_users;

													$wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel] = $wps_wocuf_pro_funnel_data;

													// Sort Funnels before saving.
													$sorted_upsell_funnels = $wps_wocuf_pro_all_funnels;

													ksort($sorted_upsell_funnels);

													update_option('wps_wocuf_funnels_list', $sorted_upsell_funnels);
												}
											}
										}

										/**
										 * Smart Offer Upgrade. ( Will not work for Global Funnel )
										 */
										if (! empty($wps_wocuf_pro_funnel_data['wps_wocuf_smart_offer_upgrade']) && 'yes' === $wps_wocuf_pro_funnel_data['wps_wocuf_smart_offer_upgrade'] && ! $is_global_funnel) {

											if (! empty($item_key)) {

												wps_wocfo_hpos_update_meta_data($order_id, '__smart_offer_upgrade_target_key', $item_key);
											}
										}

										// To skip funnel if any funnel offer product is already present during checkout ( Order Items ).
										$wps_upsell_global_settings = get_option('wps_upsell_lite_global_options', array());

										$skip_similar_offer = ! empty($wps_upsell_global_settings['skip_similar_offer']) ? $wps_upsell_global_settings['skip_similar_offer'] : 'yes';

										if ('yes' === $skip_similar_offer) {

											$offer_product_in_cart = false;

											foreach ($wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_wocuf_products_in_offer'] as $product_in_funnel_id_array) {

												if (! empty($product_in_funnel_id_array)) {

													// In v2.0.0, it was array so handling accordingly.
													if (is_array($product_in_funnel_id_array) && count($product_in_funnel_id_array)) {

														foreach ($product_in_funnel_id_array as $product_in_funnel_id) {

															foreach ($wps_wocuf_pro_placed_order_items as $item_key => $wps_wocuf_pro_single_item) {

																/**
																 * Get get_product()->get_id() will return actual id, no need to call
																 * get_variation_id() separately.
																 */
																if ((int) $wps_wocuf_pro_single_item->get_product()->get_id() === absint($product_in_funnel_id)) {

																	$offer_product_in_cart = true;
																	break 3;
																}
															}
														}
													} else {

														foreach ($wps_wocuf_pro_placed_order_items as $item_key => $wps_wocuf_pro_single_item) {

															/**
															 * Get get_product()->get_id() will return actual id, no need to call
															 * get_variation_id() separately.
															 */
															if ((int) $wps_wocuf_pro_single_item->get_product()->get_id() === absint($product_in_funnel_id_array)) {

																$offer_product_in_cart = true;
																break 2;
															}
														}
													}
												}
											}

											if (true === $offer_product_in_cart) {

												break;
											}
										}

										/**
										 * Smart Skip if already purchased.
										 * since v3.0.0
										 */
										$smart_skip_if_purchased = ! empty($wps_upsell_global_settings['smart_skip_if_purchased']) ? $wps_upsell_global_settings['smart_skip_if_purchased'] : '';

										if (is_user_logged_in() && 'yes' === $smart_skip_if_purchased) {

											$offer_product_already_purchased = false;

											if (! empty($wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_wocuf_products_in_offer']) && is_array($wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_wocuf_products_in_offer'])) {

												foreach ($wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_wocuf_products_in_offer'] as $single_offer_id) {

													if (true === self::wps_wocuf_skip_for_pre_order($single_offer_id)) {

														// If already purchased.
														$offer_product_already_purchased = true;
														break;
													}
												}
											}

											if (true === $offer_product_already_purchased) {

												break;
											}
										}

										// To skip funnel if any offer product in funnel is out of stock.

										$product_in_funnel_stock_out = false;
										foreach ($wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_wocuf_products_in_offer'] as $product_in_funnel_id_array) {

											if (! empty($product_in_funnel_id_array)) {

												// In v2.0.0, it was array so handling accordingly.
												if (is_array($product_in_funnel_id_array) && count($product_in_funnel_id_array)) {

													foreach ($product_in_funnel_id_array as $product_in_funnel_id) {

														$product_in_funnel = wc_get_product($product_in_funnel_id);

														if (! $product_in_funnel->is_in_stock()) {

															$product_in_funnel_stock_out = true;
															break 2;
														}
													}
												} else {

													$product_in_funnel = wc_get_product($product_in_funnel_id_array);

													if (! $product_in_funnel->is_in_stock()) {

														$product_in_funnel_stock_out = true;
														break;
													}
												}
											}
										}

										if (true === $product_in_funnel_stock_out) {

											break;
										}
									}

									// $ocuf_ofd is first offer id in funnel, check if product id is set in it.
									if (! empty($wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_wocuf_products_in_offer'][$ocuf_ofd])) {

										$funnel_offer_post_id_assigned = ! empty($wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_upsell_post_id_assigned'][$ocuf_ofd]) ? $wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_upsell_post_id_assigned'][$ocuf_ofd] : '';
										wps_wocfo_hpos_delete_meta_data($funnel_offer_post_id_assigned, '_elementor_element_cache');

										// When funnel is saved since v3.0.0 and offer post id is assigned and elementor active.
										if (! empty($funnel_offer_post_id_assigned) && ('true' === $funnel_saved_after_version_3 && wps_upsell_lite_elementor_plugin_active()) || wps_upsell_divi_builder_plugin_active()) {

											$redirect_to_upsell = false;

											$offer_template = ! empty($wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_wocuf_pro_offer_template'][$ocuf_ofd]) ? $wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_wocuf_pro_offer_template'][$ocuf_ofd] : '';

											// When template is set to custom.
											if ('custom' === $offer_template) {

												$custom_offer_page_url = ! empty($wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_wocuf_offer_custom_page_url'][$ocuf_ofd]) ? $wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_wocuf_offer_custom_page_url'][$ocuf_ofd] : '';

												if (! empty($custom_offer_page_url)) {

													$redirect_to_upsell = true;
													$redirect_to_url    = $custom_offer_page_url;
												}
											} elseif (! empty($offer_template)) {

												// When template is set to one, two or three.
												$offer_assigned_post_id = ! empty($wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_upsell_post_id_assigned'][$ocuf_ofd]) ? $wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_upsell_post_id_assigned'][$ocuf_ofd] : '';
												if (! empty($offer_assigned_post_id) && 'publish' === get_post_status($offer_assigned_post_id)) {

													$redirect_to_upsell = true;
													$redirect_to_url    = get_page_link($offer_assigned_post_id);
												}
											}

											if (true === $redirect_to_upsell) {

												$funnel_redirect = true;

												$wps_wocuf_pro_nonce = wp_create_nonce('funnel_offers');

												$result = add_query_arg(
													array(
														'ocuf_ns' => $wps_wocuf_pro_nonce,
														'ocuf_fid' => $wps_wocuf_pro_single_funnel,
														'ocuf_ok' => $ocuf_ok,
														'ocuf_ofd' => $ocuf_ofd,
													),
													$redirect_to_url
												);

												$wps_wocuf_pro_flag = 1;

												// Break from placed order items loop with both funnel redirect and pro flag as true.
												break;
											}
										} else { // When funnel is saved before v3.0.0.
											$wps_wocuf_pro_offer_page_id = get_option('wps_wocuf_pro_funnel_default_offer_page', '');

											if (isset($wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_wocuf_offer_custom_page_url'][$ocuf_ofd]) && ! empty($wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_wocuf_offer_custom_page_url'][$ocuf_ofd])) {
												$redirect_to_url = $wps_wocuf_pro_all_funnels[$wps_wocuf_pro_single_funnel]['wps_wocuf_offer_custom_page_url'][$ocuf_ofd];
											} elseif (! empty($wps_wocuf_pro_offer_page_id) && 'publish' === get_post_status($wps_wocuf_pro_offer_page_id)) {
												$redirect_to_url = get_page_link($wps_wocuf_pro_offer_page_id);
											} else {

												// Break from placed order items loop and move to next funnel.
												break;
											}

											$funnel_redirect = true;

											$wps_wocuf_pro_nonce = wp_create_nonce('funnel_offers');

											$result = add_query_arg(
												array(
													'ocuf_ns' => $wps_wocuf_pro_nonce,
													'ocuf_fid' => $wps_wocuf_pro_single_funnel,
													'ocuf_ok' => $ocuf_ok,
													'ocuf_ofd' => $ocuf_ofd,
												),
												$redirect_to_url
											);

											$wps_wocuf_pro_flag = 1;

											// Break from placed order items loop with both funnel redirect and pro flag as true.
											break;
										}
									}
								}
							}
						}

						if (1 === $wps_wocuf_pro_flag) {

							// Break from 'all funnels' loop.
							break;
						}
					}
				}

				if ($funnel_redirect) {

					$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

					if ('stripe_cc' === $payment_method) {

						$checkout_nonce = ! empty($_POST['checkout_order_processed_nonce']) ? sanitize_text_field(wp_unslash($_POST['checkout_order_processed_nonce'])) : '';

						if (isset($_POST['checkout_order_processed_nonce']) && wp_verify_nonce($checkout_nonce, 'checkout_order_processed_nonce')) {
							if (empty($_GET['wc-ajax']) || 'checkout' !== $_GET['wc-ajax']) {
								return;
							}
						}
						wps_wocfo_hpos_update_meta_data($order_id, '_post_data', $_POST);




						if (empty($available_gateways[$payment_method])) {

							wc_clear_notices();
							throw new Exception(esc_html__('Error processing checkout. Please try again with another payment method.',  'woo-one-click-upsell-funnel'));
						} else {

							// Process Subscriptions for pre upsell products from Order.
							if (wps_upsell_org_order_contains_subscription($order_id) && wps_upsell_org_pg_supports_subs($order_id)) {

								wps_upsell_org_create_subscriptions_for_order($order_id, $order);
							}

							/**
							 * Process WPS Subscriptions for pre upsell products from Order.
							 */
							if (class_exists('Subscriptions_For_Woocommerce_Compatiblity') && true === Subscriptions_For_Woocommerce_Compatiblity::pg_supports_subs($order_id) && true === Subscriptions_For_Woocommerce_Compatiblity::order_contains_subscription($order_id)) {

								$secure_nonce      = wp_create_nonce('wps-upsell-auth-nonce');
								$id_nonce_verified = wp_verify_nonce($secure_nonce, 'wps-upsell-auth-nonce');

								if (! $id_nonce_verified) {
									wp_die(esc_html__('Nonce Not verified',  'woo-one-click-upsell-funnel'));
								}

								$compat_class = new Subscriptions_For_Woocommerce_Compatiblity('Subscriptions_For_Woocommerce_Compatiblity', '1.0.0');
								$compat_class->create_subscriptions_for_order($order_id, $_POST);
							}

							$is_block_checkout = wps_wocfo_hpos_get_meta_data($order_id, '_wps_wocfo_org_checkout_through_block', true);

							if ('block_checkout' == $is_block_checkout) {

								$this->initial_redirection_to_upsell_offer_and_triggers($order_id, $wps_wocuf_pro_single_funnel, $result);
								return;
							} else {



								$this->initial_redirection_to_upsell_offer_and_triggers($order_id, $wps_wocuf_pro_single_funnel, $result);
								return;
							}
						}
					} else {
						// For cron - Upsell is initialized. As just going to Redirect.
						wps_wocfo_hpos_update_meta_data($order_id, 'wps_ocufp_upsell_initialized', time());

						$this->initial_redirection_to_upsell_offer_and_triggers($order_id, $wps_wocuf_pro_single_funnel, $result);
					}
				} else {

					return;
				}
			}

			return;
		}
	}



	/**
	 * Skip offer product in case of the purchased in prevous orders.
	 *
	 * @param      string $offer_product_id    The Offer product id to check.
	 *
	 * @since    3.0.0
	 */
	public static function wps_wocuf_skip_for_pre_order($offer_product_id = '')
	{

		if (empty($offer_product_id)) {

			return;
		}

		$offer_product = wc_get_product($offer_product_id);

		// In case the offer is variable parent then no need to check this.
		if (! empty($offer_product) && is_object($offer_product) && $offer_product->has_child()) {

			return false;
		}

		// Current user ID.
		$customer_user_id = get_current_user_id();

		// Getting current customer orders.
		$order_statuses = array('wc-on-hold', 'wc-processing', 'wc-completed');

		$customer_user_id = get_current_user_id();
		$customer_orders = wc_get_orders(
			array(
				'customer' => $customer_user_id,
				'order' => 'DESC',
				'status' => array('wc-on-hold', 'wc-processing', 'wc-completed'),
				'return'   => 'ids',
				'numberposts' => -1,
			)
		);

		// Past Orders.
		foreach ($customer_orders as $key => $single_order_id) {

			// Continue if order is not a valid one.
			if (! $single_order_id) {

				continue;
			}

			$single_order = wc_get_order($single_order_id);

			// Continue if Order object is not a valid one.
			if (empty($single_order) || ! is_object($single_order) || is_wp_error($single_order)) {

				continue;
			}

			$items_purchased = $single_order->get_items();

			foreach ($items_purchased as $key => $single_item) {

				$product_id = ! empty($single_item['variation_id']) ? $single_item['variation_id'] : $single_item['product_id'];

				if ((int) $product_id === (int) $offer_product_id) {

					return true;
				}
			}
		}

		return false;
	}


	/**
	 * Initial redirection to Upsell offers
	 * and important Triggers.
	 *
	 * @param int   $order_id order id.
	 * @param mixed $funnel_id funnel id.
	 * @param mixed $upsell_offer_link upsell offer link.
	 * @param mixed $safe_redirect safe redirect.
	 *
	 * @since    3.0.0
	 */
	public function initial_redirection_to_upsell_offer_and_triggers($order_id, $funnel_id, $upsell_offer_link, $safe_redirect = false)
	{

		/**
		 * As just going to redirect, means upsell is initialized for this order.
		 *
		 * This can be used to track upsell orders in which browser window was closed
		 * and other purposes.
		 */
		wps_wocfo_hpos_update_meta_data($order_id, 'wps_upsell_order_started', 'true');

		// Add Upsell Funnel Id to order meta for Sales by Funnel tracking.
		wps_wocfo_hpos_update_meta_data($order_id, 'wps_upsell_funnel_id', $funnel_id);

		// Add Funnel Triggered count and Offer View Count for the current Funnel.
		$sales_by_funnel = new WPS_Upsell_Report_Sales_By_Funnel($funnel_id);
		$sales_by_funnel->add_funnel_triggered_count();
		$sales_by_funnel->add_offer_view_count();

		// Store Order ID in session so it can be re-used after payment failure.
		WC()->session->set('order_awaiting_payment', $order_id);

		$upsell_result = array(
			'result'   => 'success',
			'redirect' => $upsell_offer_link,
		);

		$is_block_checkout = wps_wocfo_hpos_get_meta_data($order_id, '_wps_wocfo_org_checkout_through_block', true);

		if ('block_checkout' == $is_block_checkout) {
			wps_wocfo_hpos_update_meta_data($order_id, 'wps_wocfo_upsell_funnel_redirection_link_org', $upsell_offer_link);
		} else {
			// Redirect to upsell offer page.
			if (! is_ajax()) {
				wp_redirect($upsell_result['redirect']); //phpcs:ignore
				exit;
			}

			wp_send_json($upsell_result);
		}
	}


	/**
	 * Initiate Upsell Orders before processing payment in case of checkout block.
	 *
	 * @param object $order is the current order object.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception Throws exception when error.
	 */
	public function wps_wocuf_initate_upsell_orders_api_checkout_org($order)
	{
		$order_id = $order->get_id();
		wps_wocfo_hpos_update_meta_data($order_id, '_wps_wocfo_org_checkout_through_block', 'block_checkout');

		$this->wps_wocuf_initiate_upsell_orders($order);
	}

	/**
	 * Validate if current offer is already processed on Upsell Action.
	 *
	 * @param mixed $order_id order id.
	 * @param mixed $current_offer_id current offer id.
	 * @since    3.0.1
	 */
	public function validate_offers_processed_on_upsell_action($order_id, $current_offer_id)
	{

		if (empty($order_id) || empty($current_offer_id)) {

			return;
		}

		$offers_processed = wps_wocfo_hpos_get_meta_data($order_id, '_wps_upsell_offers_processed', true);

		if (! empty($offers_processed) && is_array($offers_processed)) {

			foreach ($offers_processed as $offer_id => $url) {

				// When offer is already processed, redirect to previous result of action that was taken.
				if ((int) $current_offer_id === (int) $offer_id) {

					wp_redirect($url); //phpcs:ignore
					exit;
				}
			}
		}
	}


	/**
	 * Initiate Order Payment and redirect.
	 *
	 * @since    1.0.0
	 * @param    int $order_id    Order ID.
	 */
	public function initiate_order_payment_and_redirect($order_id)
	{

		$order = new WC_Order($order_id);

		if (empty($order)) {

			return false;
		}

		// As Order Payment is initiated so Expire further Offers.
		wps_wocfo_hpos_update_meta_data($order_id, '_wps_upsell_expire_further_offers', true);

		// Delete Offers Processed data as now we don't need it.
		wps_wocfo_hpos_delete_meta_data($order_id, '_wps_upsell_offers_processed');

		$result = $this->upsell_order_final_payment($order_id);

		$is_block_checkout = wps_wocfo_hpos_get_meta_data($order_id, '_wps_wocfo_org_checkout_through_block', true);

		if ('block_checkout' == $is_block_checkout) {

			$url    = wps_wocfo_hpos_get_meta_data($order_id, 'wps_wocuf_upsell_funnel_order_redirection_link', true);

			wp_redirect($url); //phpcs:ignore
			exit();
		}

		$url = $order->get_checkout_order_received_url();

		if (isset($result['result']) && 'success' === $result['result']) {

			wp_redirect($result['redirect']); //phpcs:ignore
			exit;
		}

		if (isset($result['result']) && 'failure' === $result['result']) {

			global $woocommerce;
			$cart_page_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : $woocommerce->cart->get_cart_url();

			wp_redirect($cart_page_url); //phpcs:ignore
			exit;
		} else {

			wp_redirect($url); //phpcs:ignore
			exit;
		}
	}


	/**
	 * When user clicks on No thanks for Upsell offer.
	 *
	 * @since    1.0.0
	 */
	public function wps_wocuf_pro_process_the_funnel()
	{

		$secure_nonce      = wp_create_nonce('wps-upsell-auth-nonce');
		$id_nonce_verified = wp_verify_nonce($secure_nonce, 'wps-upsell-auth-nonce');

		if (! $id_nonce_verified) {
			wp_die(esc_html__('Nonce Not verified', 'woo-one-click-upsell-funnel'));
		}

		if (isset($_GET['ocuf_th']) && 1 === (int) $_GET['ocuf_th'] && isset($_GET['ocuf_ofd']) && isset($_GET['ocuf_fid']) && isset($_GET['ocuf_ok']) && isset($_GET['ocuf_ns'])) {

			$offer_id = sanitize_text_field(wp_unslash($_GET['ocuf_ofd']));

			$funnel_id = sanitize_text_field(wp_unslash($_GET['ocuf_fid']));

			$order_key = sanitize_text_field(wp_unslash($_GET['ocuf_ok']));

			$wp_nonce = sanitize_text_field(wp_unslash($_GET['ocuf_ns']));

			$order_id = wc_get_order_id_by_order_key($order_key);

			if (! empty($order_id)) {

				$order = wc_get_order($order_id);

				$already_processed_order_statuses = array(
					'failed',
				);

				// If order or payment is already processed.
				if (in_array($order->get_status(), $already_processed_order_statuses, true) || $this->expire_further_offers($order_id)) {

					$this->expire_offer();
				}

				// Check for offers processed.
				$current_offer_id = $offer_id;
				$this->validate_offers_processed_on_upsell_action($order_id, $current_offer_id);
			}

			// Add Offer Reject Count for the current Funnel.
			$sales_by_funnel = new WPS_Upsell_Report_Sales_By_Funnel($funnel_id);
			$sales_by_funnel->add_offer_reject_count();

			$wps_wocuf_pro_all_funnels = get_option('wps_wocuf_funnels_list', array());

			$wps_wocuf_pro_action_on_no = isset($wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_attached_offers_on_no']) ? $wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_attached_offers_on_no'] : array();

			$wps_wocuf_pro_check_action = isset($wps_wocuf_pro_action_on_no[$offer_id]) ? $wps_wocuf_pro_action_on_no[$offer_id] : '';

			if ('thanks' === $wps_wocuf_pro_check_action) {

				$this->initiate_order_payment_and_redirect($order_id);
			} elseif ('thanks' !== $wps_wocuf_pro_check_action) {

				// Next offer id.
				$offer_id = $wps_wocuf_pro_check_action;

				// Check if next offer has product.
				$wps_wocuf_pro_upcoming_offer = isset($wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_products_in_offer'][$offer_id]) ? $wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_products_in_offer'][$offer_id] : array();

				// If next offer has no product then redirect.
				if (empty($wps_wocuf_pro_upcoming_offer)) {

					$this->initiate_order_payment_and_redirect($order_id);
				}

				$funnel_saved_after_version_3 = ! empty($wps_wocuf_pro_all_funnels[$funnel_id]['wps_upsell_fsav3']) ? $wps_wocuf_pro_all_funnels[$funnel_id]['wps_upsell_fsav3'] : '';

				$funnel_offer_post_id_assigned = ! empty($wps_wocuf_pro_all_funnels[$funnel_id]['wps_upsell_post_id_assigned'][$offer_id]) ? $wps_wocuf_pro_all_funnels[$funnel_id]['wps_upsell_post_id_assigned'][$offer_id] : '';

				// When funnel is saved since v3.0.0 and offer post id is assigned and elementor active.
				if (! empty($funnel_offer_post_id_assigned) && ('true' === $funnel_saved_after_version_3 && wps_upsell_lite_elementor_plugin_active()) || wps_upsell_divi_builder_plugin_active()) {

					$redirect_to_upsell = false;

					$offer_template = ! empty($wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_pro_offer_template'][$offer_id]) ? $wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_pro_offer_template'][$offer_id] : '';

					// When template is set to custom.
					if ('custom' === $offer_template) {

						$custom_offer_page_url = ! empty($wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_offer_custom_page_url'][$offer_id]) ? $wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_offer_custom_page_url'][$offer_id] : '';

						if (! empty($custom_offer_page_url)) {

							$redirect_to_upsell = true;
							$redirect_to_url    = $custom_offer_page_url;
						}
					} elseif (! empty($offer_template)) {
						// When template is set to one, two or three.
						$offer_assigned_post_id = ! empty($wps_wocuf_pro_all_funnels[$funnel_id]['wps_upsell_post_id_assigned'][$offer_id]) ? $wps_wocuf_pro_all_funnels[$funnel_id]['wps_upsell_post_id_assigned'][$offer_id] : '';
						wps_wocfo_hpos_delete_meta_data($funnel_offer_post_id_assigned, '_elementor_element_cache');
						if (! empty($offer_assigned_post_id) && 'publish' === get_post_status($offer_assigned_post_id)) {

							$redirect_to_upsell = true;
							$redirect_to_url    = get_page_link($offer_assigned_post_id);
						}
					}

					if (true === $redirect_to_upsell) {

						$url = add_query_arg(
							array(
								'ocuf_ns'  => $wp_nonce,
								'ocuf_fid' => $funnel_id,
								'ocuf_ok'  => $order_key,
								'ocuf_ofd' => $offer_id,
							),
							$redirect_to_url
						);

						// Set offers processed when there is another offer to come up means when not last offer.
						$this->set_offers_processed_on_upsell_action($order_id, $current_offer_id, $url);
					} else {

						$this->initiate_order_payment_and_redirect($order_id);
					}
				} else { // When funnel is saved before v3.0.0.

					$wps_wocuf_pro_offer_page_id = get_option('wps_wocuf_pro_funnel_default_offer_page', '');

					if (isset($wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_offer_custom_page_url'][$offer_id]) && ! empty($wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_offer_custom_page_url'][$offer_id])) {

						$wps_wocuf_pro_next_offer_url = $wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_offer_custom_page_url'][$offer_id];
					} elseif (! empty($wps_wocuf_pro_offer_page_id) && get_post_status('publish' === $wps_wocuf_pro_offer_page_id)) {

						$wps_wocuf_pro_next_offer_url = get_page_link($wps_wocuf_pro_offer_page_id);
					} else {

						$this->initiate_order_payment_and_redirect($order_id);
					}

					$wps_wocuf_pro_next_offer_url = add_query_arg(
						array(
							'ocuf_ns'  => $wp_nonce,
							'ocuf_fid' => $funnel_id,
							'ocuf_ok'  => $order_key,
							'ocuf_ofd' => $offer_id,
						),
						$wps_wocuf_pro_next_offer_url
					);

					$url = $wps_wocuf_pro_next_offer_url;
				}

				// Add Offer View Count for the current Funnel.
				$sales_by_funnel = new WPS_Upsell_Report_Sales_By_Funnel($funnel_id);
				$sales_by_funnel->add_offer_view_count();

				wp_redirect($url); //phpcs:ignore
				exit();
			}
		}
	}



	/**
	 * When user clicks on Add upsell product to my Order.
	 *
	 * @since    1.0.0
	 */
	public function wps_wocuf_pro_charge_the_offer()
	{

		$add_product_nonce = ! empty($_POST['wps_wocuf_post_nonce']) ? sanitize_text_field(wp_unslash($_POST['wps_wocuf_post_nonce'])) : '';

		if ((isset($add_product_nonce)) && (wp_verify_nonce($add_product_nonce, 'wps_wocuf_field_post_nonce') && isset($_POST['wps_wocuf_pro_buy'])) || isset($_GET['wps_wocuf_pro_buy'])) {

			unset($_POST['wps_wocuf_pro_buy']);

			$live_offer_url_params = wps_upsell_lite_live_offer_url_params();

			if ('true' === $live_offer_url_params['status']) {

				$is_product_with_variations = false;

				if (! empty($_POST['wocuf_var_attb'])) {

					// Retrieve all variations from form.
					$variation_attributes = sanitize_text_field(wp_unslash($_POST['wocuf_var_attb']));
					$variation_attributes = stripslashes($variation_attributes);
					$variation_attributes = str_replace("'", '"', $variation_attributes);

					$variation_attributes = json_decode($variation_attributes, true);

					$is_product_with_variations = true;
				}

				$wp_nonce = $live_offer_url_params['upsell_nonce'];

				$offer_id = $live_offer_url_params['offer_id'];

				$funnel_id = $live_offer_url_params['funnel_id'];

				$product_id = $live_offer_url_params['product_id'];
				$order_key = $live_offer_url_params['order_key'];
				$order_id = wc_get_order_id_by_order_key($order_key);
				$shipping_price = floatval(get_post_meta($product_id, 'wps_upsell_simple_shipping_product_' . $product_id, true));
				if (! empty($shipping_price)) {
					$shipping_price_order = floatval(wps_wocfo_hpos_get_meta_data($order_id, 'wps_upsell_simple_shipping_product_', true));
					$shipping_price_order += $shipping_price;
					wps_wocfo_hpos_update_meta_data($order_id, 'wps_upsell_simple_shipping_product_', $shipping_price_order);
				}

				$offer_quantity = ! empty($live_offer_url_params['quantity']) ? $live_offer_url_params['quantity'] : '1';

				if (! empty($order_id)) {

					$order = wc_get_order($order_id);

					$already_processed_order_statuses = array(
						'failed',
					);

					// If order or payment is already processed.
					if (in_array($order->get_status(), $already_processed_order_statuses, true) || $this->expire_further_offers($order_id)) {

						$this->expire_offer();
					}

					// Check for offers processed.
					$current_offer_id = $offer_id;
					$this->validate_offers_processed_on_upsell_action($order_id, $current_offer_id);
				}

				if (! empty($order)) {
					$upsell_product = wc_get_product($product_id);

					if (! empty($upsell_product) && $upsell_product->is_purchasable()) {

						$wps_wocuf_pro_all_funnels = get_option('wps_wocuf_funnels_list');

						$wps_wocuf_pro_offered_discount = ! empty($wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_offer_discount_price'][$offer_id]) ? $wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_offer_discount_price'][$offer_id] : '';

						$upsell_product = $this->wps_wocuf_pro_change_offered_product_price($upsell_product, $wps_wocuf_pro_offered_discount);

						if ($is_product_with_variations) {

							if ('variation' === $upsell_product->get_type()) {

								$upsell_var_attb = $upsell_product->get_variation_attributes();

								// Variation has blank attribute when it is set as 'Any..' in backend.

								// Check if upsell product variation has any blank attribute ?
								if (false !== array_search('', $upsell_var_attb, true)) {

									// If yes then set attributes retrieved from form.
									$upsell_product->set_attributes($variation_attributes);
								}
							}
						}

						$upsell_item_id = $order->add_product($upsell_product, $offer_quantity);

						// Add Offer Accept Count for the current Funnel.
						$sales_by_funnel = new WPS_Upsell_Report_Sales_By_Funnel($funnel_id);
						$sales_by_funnel->add_offer_accept_count();

						$target_item_id = wps_wocfo_hpos_get_meta_data($order_id, '__smart_offer_upgrade_target_key', true);

						$force_payment = false;

						if (! empty($target_item_id) && is_numeric($target_item_id)) {

							foreach ((array) $order->get_items() as $item_id => $item) {

								if ((int) $item_id === (int) $target_item_id) {

									$order->remove_item($item_id);
									wps_wocfo_hpos_delete_meta_data($order_id, '__smart_offer_upgrade_target_key');
									$force_payment = true;
								}
							}

							$order->save();
						}
						foreach ($order->get_items() as $item_id => $item) {
							// Check if this is the product you want to add custom meta to (optional).
							if ($item_id == $upsell_item_id) {
								$item->add_meta_data('is_upsell_purchase', 'true');
							}
						}

						$order->calculate_totals();

						// Upsell product was purchased for this order.
						wps_wocfo_hpos_update_meta_data($order_id, 'wps_wocuf_upsell_order', 'true');
					}

					$wps_wocuf_pro_all_funnels = get_option('wps_wocuf_funnels_list', array());

					$wps_wocuf_pro_buy_action = isset($wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_attached_offers_on_buy']) ? $wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_attached_offers_on_buy'] : '';

					$url = '';

					/**
					 * After v3.4.1 :: Smart offer upgraded.
					 * If target product is removed, then process the payment.
					 */
					if (! empty($force_payment) && true === $force_payment) {

						$this->initiate_order_payment_and_redirect($order_id);
					} elseif (isset($wps_wocuf_pro_buy_action[$offer_id]) && 'thanks' === $wps_wocuf_pro_buy_action[$offer_id]) {

						$this->initiate_order_payment_and_redirect($order_id);
					} elseif (isset($wps_wocuf_pro_buy_action[$offer_id]) && 'thanks' !== $wps_wocuf_pro_buy_action[$offer_id]) {
						// Next offer id.
						$offer_id = $wps_wocuf_pro_buy_action[$offer_id];

						// Check if next offer has product.
						$wps_wocuf_pro_upcoming_offer = isset($wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_products_in_offer'][$offer_id]) ? $wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_products_in_offer'][$offer_id] : '';

						// If next offer has no product then redirect.
						if (empty($wps_wocuf_pro_upcoming_offer)) {

							$this->initiate_order_payment_and_redirect($order_id);
						} else {

							$funnel_saved_after_version_3 = ! empty($wps_wocuf_pro_all_funnels[$funnel_id]['wps_upsell_fsav3']) ? $wps_wocuf_pro_all_funnels[$funnel_id]['wps_upsell_fsav3'] : '';

							$funnel_offer_post_id_assigned = ! empty($wps_wocuf_pro_all_funnels[$funnel_id]['wps_upsell_post_id_assigned'][$offer_id]) ? $wps_wocuf_pro_all_funnels[$funnel_id]['wps_upsell_post_id_assigned'][$offer_id] : '';

							// When funnel is saved since v3.0.0 and offer post id is assigned and elementor active.
							if (! empty($funnel_offer_post_id_assigned) && ('true' === $funnel_saved_after_version_3 && wps_upsell_lite_elementor_plugin_active()) || wps_upsell_divi_builder_plugin_active()) {

								$redirect_to_upsell = false;

								$offer_template = ! empty($wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_pro_offer_template'][$offer_id]) ? $wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_pro_offer_template'][$offer_id] : '';

								// When template is set to custom.
								if ('custom' === $offer_template) {

									$custom_offer_page_url = ! empty($wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_offer_custom_page_url'][$offer_id]) ? $wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_offer_custom_page_url'][$offer_id] : '';

									if (! empty($custom_offer_page_url)) {

										$redirect_to_upsell = true;
										$redirect_to_url    = $custom_offer_page_url;
									}
								} elseif (! empty($offer_template)) { // When template is set to one, two or three.

									$offer_assigned_post_id = ! empty($wps_wocuf_pro_all_funnels[$funnel_id]['wps_upsell_post_id_assigned'][$offer_id]) ? $wps_wocuf_pro_all_funnels[$funnel_id]['wps_upsell_post_id_assigned'][$offer_id] : '';

									if (! empty($offer_assigned_post_id) && 'publish' === get_post_status($offer_assigned_post_id)) {

										$redirect_to_upsell = true;
										$redirect_to_url    = get_page_link($offer_assigned_post_id);
									}
								}

								if (true === $redirect_to_upsell) {

									$url = add_query_arg(
										array(
											'ocuf_ns'  => $wp_nonce,
											'ocuf_fid' => $funnel_id,
											'ocuf_ok'  => $order_key,
											'ocuf_ofd' => $offer_id,
										),
										$redirect_to_url
									);

									// Set offers processed when there is another offer to come up means when not last offer.
									$this->set_offers_processed_on_upsell_action($order_id, $current_offer_id, $url);
								} else {

									$this->initiate_order_payment_and_redirect($order_id);
								}
							} else {
								// When funnel is saved before v3.0.0.
								$wps_wocuf_pro_offer_page_id = get_option('wps_wocuf_pro_funnel_default_offer_page', '');

								if (isset($wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_offer_custom_page_url'][$offer_id]) && ! empty($wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_offer_custom_page_url'][$offer_id])) {

									$wps_wocuf_pro_next_offer_url = $wps_wocuf_pro_all_funnels[$funnel_id]['wps_wocuf_offer_custom_page_url'][$offer_id];
								} elseif (! empty($wps_wocuf_pro_offer_page_id) && 'publish' === get_post_status($wps_wocuf_pro_offer_page_id)) {

									$wps_wocuf_pro_next_offer_url = get_page_link($wps_wocuf_pro_offer_page_id);
								} else {

									$this->initiate_order_payment_and_redirect($order_id);
								}

								$url = add_query_arg(
									array(
										'ocuf_ns'  => $wp_nonce,
										'ocuf_fid' => $funnel_id,
										'ocuf_ok'  => $order_key,
										'ocuf_ofd' => $offer_id,
									),
									$wps_wocuf_pro_next_offer_url
								);
							}
						}

						// Add Offer View Count for the current Funnel.
						$sales_by_funnel = new WPS_Upsell_Report_Sales_By_Funnel($funnel_id);
						$sales_by_funnel->add_offer_view_count();

						wp_redirect($url); //phpcs:ignore
						exit;
					}
				} else {

					$this->initiate_order_payment_and_redirect($order_id);
				}
			}
		}
	}


	/**
	 * Set Offers Processed for Order on Upsell Action.
	 * Except for last offer.
	 *
	 * @param mixed $order_id order id.
	 * @param mixed $current_offer_id current offer id.
	 * @param mixed $url url.
	 * @since    3.0.1
	 */
	public function set_offers_processed_on_upsell_action($order_id, $current_offer_id, $url)
	{

		if (empty($order_id) || empty($current_offer_id) || empty($url)) {

			return;
		}

		$offers_processed = wps_wocfo_hpos_get_meta_data($order_id, '_wps_upsell_offers_processed', true);

		$offers_processed = ! empty($offers_processed) ? $offers_processed : array();

		$offers_processed[$current_offer_id] = $url;

		wps_wocfo_hpos_update_meta_data($order_id, '_wps_upsell_offers_processed', $offers_processed);
	}


	/**
	 * Cron schedule fire Event for Order payment process.
	 *
	 * @since    1.0.0
	 */
	public function order_payment_cron_fire_event()
	{

		// Pending Orders.
		$pending_upsell_orders = get_posts(
			array(
				'numberposts' => -1,
				'post_status' => 'wc-pending',
				'fields'      => 'ids', // return only ids.
				'meta_key'    => 'wps_ocufp_upsell_initialized', // phpcs:ignore
				'post_type'   => 'shop_order',
				'order'       => 'ASC',
			)
		);

		if (! empty($pending_upsell_orders) && is_array($pending_upsell_orders) && count($pending_upsell_orders)) {

			foreach ($pending_upsell_orders as $order_id) {

				$time_stamp = wps_wocfo_hpos_get_meta_data($order_id, 'wps_ocufp_upsell_initialized', true);

				if (! empty($time_stamp)) {

					$fifteen_minutes = strtotime('+15 minutes', $time_stamp);

					$current_time = time();

					$time_diff = $fifteen_minutes - $current_time;

					if (0 > $time_diff) {

						global $woocommerce;

						$gateways = $woocommerce->payment_gateways->get_available_payment_gateways();

						$order = new WC_Order($order_id);

						// For cron - Payment initialized.
						wps_wocfo_hpos_delete_meta_data($order_id, 'wps_ocufp_upsell_initialized');

						$payment_method = $order->get_payment_method();

						$gateways[$payment_method]->process_payment($order_id, 'cron');
					}
				}
			}
		}
	}



	/**
	 * Global Custom CSS.
	 *
	 * @since    3.0.0
	 */
	public function post_global_custom_css()
	{

		// Ignore admin, feed, robots or trackbacks.
		if (is_admin() || is_feed() || is_robots() || is_trackback()) {

			return;
		}

		$wps_upsell_global_settings = get_option('wps_upsell_lite_global_options', array());

		$global_custom_css = ! empty($wps_upsell_global_settings['global_custom_css']) ? $wps_upsell_global_settings['global_custom_css'] : '';

		if (empty($global_custom_css)) {

			return;
		}

		wp_register_style('wps_upsell_pro_global_custom_css', false, array(), WC_VERSION, 'all');
		wp_enqueue_style('wps_upsell_pro_global_custom_css');
		wp_add_inline_style('wps_upsell_pro_global_custom_css', $global_custom_css);
	}


	/**
	 * Global Custom JS.
	 *
	 * @since    3.0.0
	 */
	public function post_global_custom_js()
	{

		// Ignore admin, feed, robots or trackbacks.
		if (is_admin() || is_feed() || is_robots() || is_trackback()) {

			return;
		}

		$wps_upsell_global_settings = get_option('wps_upsell_lite_global_options', array());

		$global_custom_js = ! empty($wps_upsell_global_settings['global_custom_js']) ? $wps_upsell_global_settings['global_custom_js'] : '';

		if (empty($global_custom_js)) {
			return;
		}

		wp_register_script('wps_upsell_pro_global_custom_js', false, array('jquery'), WC_VERSION, false);
		wp_enqueue_script('wps_upsell_pro_global_custom_js');
		wp_add_inline_script('wps_upsell_pro_global_custom_js', $global_custom_js);
	}


	/**
	 * Delete the Timer data in browser session for Timer shortcode.
	 *
	 * @since   3.0.0
	 */
	public function reset_timer_session_data()
	{

		// Don this only on thank you page.
		if (! is_wc_endpoint_url('order-received')) {

			return;
		}

		?>

		<script type="text/javascript">
			// Clear timestamp from SessionStorage.
			if (typeof sessionStorage !== 'undefined' && sessionStorage.length > 0) {

				// Must reduce these variable.
				sessionStorage.removeItem('timerlimit_1');
				sessionStorage.removeItem('timerlimit_null');

				for (var i = 0; i < sessionStorage.length; i++) {

					if (sessionStorage.key(i).search('timerlimit_') == 0) {

						sessionStorage.removeItem(sessionStorage.key(i));
					}
				}
			}
		</script>

		<?php

	}



	/**
	 * Hide upsell Items meta string.
	 *
	 * @param mixed $formatted_meta formatted meta.
	 * @since       3.0.0
	 */
	public function hide_order_item_formatted_meta_data($formatted_meta)
	{

		foreach ($formatted_meta as $key => $meta) {

			if (! empty($meta->key) && 'is_upsell_purchase' === $meta->key) {

				unset($formatted_meta[$key]);
			}
		}

		return $formatted_meta;
	}



	/**
	 * Handle Upsell Orders on Thankyou for Success Rate and Stats.
	 *
	 * @param mixed $order_id order id.
	 * @since    3.0.0
	 */
	public function upsell_sales_by_funnel_handling($order_id)
	{

		if (! $order_id) {

			return;
		}

		// Process once and only for Upsells.
		$funnel_id = wps_wocfo_hpos_get_meta_data($order_id, 'wps_upsell_funnel_id', true);

		if (empty($funnel_id)) {

			return;
		}

		$order = new WC_Order($order_id);

		if (empty($order)) {

			return;
		}

		$processed_order_statuses = array(
			'processing',
			'completed',
			'on-hold',
		);

		if (! in_array($order->get_status(), $processed_order_statuses, true)) {
			return;
		}

		$order_items = $order->get_items();

		if (! empty($order_items) && is_array($order_items)) {

			$upsell_purchased  = false;
			$upsell_item_total = 0;

			foreach ($order_items as $item_id => $single_item) {

				if (! empty($single_item->get_meta('is_upsell_purchase'))) {

					$upsell_purchased   = true;
					$upsell_item_total_data = $single_item->get_meta('_line_total');
					$upsell_item_total = $upsell_item_total + intval($upsell_item_total_data);
				}
			}
		}

		if ($upsell_purchased) {

			// Add Funnel Success count and Total Sales for the current Funnel.
			$sales_by_funnel = new WPS_Upsell_Report_Sales_By_Funnel($funnel_id);

			$sales_by_funnel->add_funnel_success_count();
			$sales_by_funnel->add_funnel_total_sales($upsell_item_total);
		}

		/**
		 * Delete Funnel id so that this is processed only once and funnel id
		 * might change so no need to associate the order with it.
		 */
		wps_wocfo_hpos_delete_meta_data($order_id, 'wps_upsell_funnel_id');
	}

	/**
	 * Add Base Code for Google Analyics and Facebook Pixel.
	 *
	 * @since    3.0.0
	 */
	public function add_ga_and_fb_pixel_base_code()
	{

		$upsell_analytics_options = get_option('wps_upsell_analytics_configuration', array());

		$ga_analytics_config = ! empty($upsell_analytics_options['google-analytics']) ? $upsell_analytics_options['google-analytics'] : array();
		$fb_pixel_config     = ! empty($upsell_analytics_options['facebook-pixel']) ? $upsell_analytics_options['facebook-pixel'] : array();

		$add_ga_base_code       = false;
		$add_fb_pixel_base_code = false;

		if (! empty($ga_analytics_config['enable_ga_gst']) && 'yes' === $ga_analytics_config['enable_ga_gst'] && ! empty($ga_analytics_config['ga_account_id'])) {

			$add_ga_base_code = true;

			$ga_tracking_id = $ga_analytics_config['ga_account_id'];
		}

		if (! empty($fb_pixel_config['enable_pixel_basecode']) && 'yes' === $fb_pixel_config['enable_pixel_basecode'] && ! empty($fb_pixel_config['pixel_account_id'])) {

			$add_fb_pixel_base_code = true;

			$pixel_id = $fb_pixel_config['pixel_account_id'];
		}

		if (true === $add_ga_base_code) :

			// Global site tag (gtag.js) - Google Analytics - Start ( 1 Click Upsell Plugin ).
			wp_enqueue_script($this->plugin_name . '-googletagmanager', 'https://www.googletagmanager.com/gtag/js?id=' . $ga_tracking_id, array('jquery'), '1.0.0', false);

		?>
			<script>
				window.dataLayer = window.dataLayer || [];

				function gtag() {
					dataLayer.push(arguments)
				};
				gtag('js', new Date());

				gtag('config', '<?php echo esc_attr($ga_tracking_id); ?>');
			</script>
			<!-- Global site tag (gtag.js) - Google Analytics - End ( 1 Click Upsell Plugin ) -->
		<?php

		endif;

		if (true === $add_fb_pixel_base_code) :

		?>
			<!-- Facebook Pixel Code ( 1 Click Upsell Plugin ) -->
			<script>
				! function(f, b, e, v, n, t, s) {
					if (f.fbq) return;
					n = f.fbq = function() {
						n.callMethod ?
							n.callMethod.apply(n, arguments) : n.queue.push(arguments)
					};
					if (!f._fbq) f._fbq = n;
					n.push = n;
					n.loaded = !0;
					n.version = '2.0';
					n.queue = [];
					t = b.createElement(e);
					t.async = !0;
					t.src = v;
					s = b.getElementsByTagName(e)[0];
					s.parentNode.insertBefore(t, s)
				}(window, document, 'script',
					'https://connect.facebook.net/en_US/fbevents.js');
				fbq('init', '<?php echo (esc_html($pixel_id)); ?>');
				fbq('track', 'PageView');
			</script>
			<noscript>
				<img height="1" width="1" style="display:none"
					src="https://www.facebook.com/tr?id=<?php echo (esc_html($pixel_id)); ?>&ev=PageView&noscript=1" />
			</noscript>
			<!-- End Facebook Pixel Code ( 1 Click Upsell Plugin ) -->
<?php

		endif;
	}


	/**
	 * GA and FB Pixel Purchase Event - Track Parent Order on 1st Upsell Offer Page.
	 *
	 * @since    3.0.0
	 */
	public function ga_and_fb_pixel_purchase_event_for_parent_order()
	{

		$validate_shortcode = $this->validate_shortcode();

		if ('live_offer' === $validate_shortcode) {

			$upsell_analytics_options = get_option('wps_upsell_analytics_configuration', array());

			$ga_analytics_config = ! empty($upsell_analytics_options['google-analytics']) ? $upsell_analytics_options['google-analytics'] : array();
			$fb_pixel_config     = ! empty($upsell_analytics_options['facebook-pixel']) ? $upsell_analytics_options['facebook-pixel'] : array();

			$add_ga_purchase_event       = false;
			$add_fb_pixel_purchase_event = false;

			if (! empty($ga_analytics_config['enable_purchase_event']) && 'yes' === $ga_analytics_config['enable_purchase_event']) {

				$add_ga_purchase_event = true;
			}

			if (! empty($fb_pixel_config['enable_purchase_event']) && 'yes' === $fb_pixel_config['enable_purchase_event']) {

				$add_fb_pixel_purchase_event = true;
			}

			if ($add_ga_purchase_event) :

				$this->add_ga_purchase_event_for_parent_order();

			endif;

			if ($add_fb_pixel_purchase_event) :

				$this->add_fb_pixel_purchase_event_for_parent_order();

			endif;
		}
	}


	/**
	 * Google Analyics Purchase Event for Parent Order.
	 *
	 * @since    3.0.0
	 */
	public function add_ga_purchase_event_for_parent_order()
	{

		$secure_nonce      = wp_create_nonce('wps-upsell-auth-nonce');
		$id_nonce_verified = wp_verify_nonce($secure_nonce, 'wps-upsell-auth-nonce');

		if (! $id_nonce_verified) {
			wp_die(esc_html__('Nonce Not verified', 'woo-one-click-upsell-funnel'));
		}
		$order_key = isset($_GET['ocuf_ok']) ? sanitize_text_field(wp_unslash($_GET['ocuf_ok'])) : '';

		$order_id = wc_get_order_id_by_order_key($order_key);

		// Process once and only for Upsells.
		if (empty($order_id) || ! wps_wocfo_hpos_get_meta_data($order_id, 'wps_upsell_order_started', true) === true || ! empty(wps_wocfo_hpos_get_meta_data($order_id, '_wps_upsell_ga_parent_tracked', true)) || wps_wocfo_hpos_get_meta_data($order_id, '_wps_upsell_ga_tracked', true) === 1) {

			return;
		}

		$order = wc_get_order($order_id);

		if (empty($order)) {

			return;
		}

		// Only for those payment gateways with which Parent Order is Secured.
		$payment_method = $order->get_payment_method();

		$gateways_with_parent_secured = wps_upsell_lite_payment_gateways_with_parent_secured();

		if (! in_array($payment_method, $gateways_with_parent_secured, true)) {

			return;
		}

		// Start Tracking handling.

		global $woocommerce;

		// Get Coupon Codes.
		$coupons_list = '';

		if (version_compare($woocommerce->version, '3.7', '>')) {

			if ($order->get_coupon_codes()) {

				$coupons_count = count($order->get_coupon_codes());
				$i             = 1;

				foreach ($order->get_coupon_codes() as $coupon) {

					$coupons_list .= $coupon;
					if ($i < $coupons_count) {
						$coupons_list .= ', ';
					}
					$i++;
				}
			}
		} else {

			if ($order->get_used_coupons()) {

				$coupons_count = count($order->get_used_coupons());
				$i             = 1;

				foreach ($order->get_used_coupons() as $coupon) {

					$coupons_list .= $coupon;
					if ($i < $coupons_count) {
						$coupons_list .= ', ';
					}
					$i++;
				}
			}
		}

		// All Order items.
		$order_items = $order->get_items();

		if (! empty($order_items) && is_array($order_items)) {

			foreach ($order_items as $item) {

				$_product = $item->get_product();

				if (isset($_product->variation_data)) {

					$categories = esc_js(wc_get_formatted_variation($_product->get_variation_attributes(), true));
				} else {

					$out = array();

					$categories = get_the_terms($_product->get_id(), 'product_cat');

					if ($categories) {

						foreach ($categories as $category) {

							$out[] = $category->name;
						}
					}

					$categories = esc_js(join(',', $out));
				}

				$product_data[get_permalink($_product->get_id())] = array(
					'p_id'    => esc_html($_product->get_id()),
					'p_sku'   => esc_js($_product->get_sku() ? $_product->get_sku() : $_product->get_id()),
					'p_name'  => html_entity_decode($_product->get_title()),
					'p_price' => esc_js($order->get_item_total($item)),
					'p_cat'   => $categories,
					'p_qty'   => esc_js($item['qty']),
				);
			}

			if (! empty($product_data)) {

				// Add Product data json.
				wc_enqueue_js('wps_upsell_ga_pd=' . wp_json_encode($product_data) . ';');
			}
		}

		// Get Shipping total.
		$total_shipping = $order->get_total_shipping();

		$order_total     = $order->get_total();
		$order_total_tax = $order->get_total_tax();

		$upsell_ga_parent_tracked_data = array(
			'order_total'          => $order_total,
			'order_total_tax'      => $order_total_tax,
			'order_total_shipping' => $total_shipping,
		);

		$transaction_data = array(
			'id'          => esc_js($order->get_order_number()),
			'affiliation' => esc_js(get_bloginfo('name')),
			'revenue'     => esc_js($order_total),
			'tax'         => esc_js($order_total_tax),
			'shipping'    => esc_js($total_shipping),
			'coupon'      => $coupons_list,
			'currency'    => get_woocommerce_currency(),
			'label'       => 'Parent Purchase',
		);

		// Add Transaction data json.
		wc_enqueue_js('wps_upsell_ga_td=' . wp_json_encode($transaction_data) . ';');

		$ga_purchase_js = '
                 var items = [];
                //set local currencies
            gtag("set", {"currency": wps_upsell_ga_td.currency});
            for(var p_item in wps_upsell_ga_pd){
                items.push({
                    "id": wps_upsell_ga_pd[p_item].p_sku,
                    "name": wps_upsell_ga_pd[p_item].p_name, 
                    "category": wps_upsell_ga_pd[p_item].p_cat,
                    "price": wps_upsell_ga_pd[p_item].p_price,
                    "quantity": wps_upsell_ga_pd[p_item].p_qty,
                });
               
            }
            gtag("event", "purchase", {
                "transaction_id":wps_upsell_ga_td.id,
                "affiliation": wps_upsell_ga_td.affiliation,
                "value":wps_upsell_ga_td.revenue,
                "tax": wps_upsell_ga_td.tax,
                "shipping": wps_upsell_ga_td.shipping,
                "coupon": wps_upsell_ga_td.coupon,
                "event_category": "ecommerce",
                "event_label": wps_upsell_ga_td.label,
                "non_interaction": true,
                "items":items
            });
        ';

		wc_enqueue_js($ga_purchase_js);

		wps_wocfo_hpos_update_meta_data($order_id, '_wps_upsell_ga_parent_tracked', $upsell_ga_parent_tracked_data);
	}


	/**
	 * Facebook Pixel Purchase Event for Parent Order.
	 *
	 * @since    3.0.0
	 */
	public function add_fb_pixel_purchase_event_for_parent_order()
	{

		$secure_nonce      = wp_create_nonce('wps-upsell-auth-nonce');
		$id_nonce_verified = wp_verify_nonce($secure_nonce, 'wps-upsell-auth-nonce');

		if (! $id_nonce_verified) {
			wp_die(esc_html__('Nonce Not verified', 'woo-one-click-upsell-funnel'));
		}

		$order_key = isset($_GET['ocuf_ok']) ? sanitize_text_field(wp_unslash($_GET['ocuf_ok'])) : '';

		$order_id = wc_get_order_id_by_order_key($order_key);

		// Process once and only for Upsells.
		if (empty($order_id) || ! wps_wocfo_hpos_get_meta_data($order_id, 'wps_upsell_order_started', true) === true || ! empty(wps_wocfo_hpos_get_meta_data($order_id, '_wps_upsell_fbp_parent_tracked', true)) || wps_wocfo_hpos_get_meta_data($order_id, '_wps_upsell_fbp_tracked', true) === true) {

			return;
		}

		$order = wc_get_order($order_id);

		if (empty($order)) {

			return;
		}

		// Only for those payment gateways with which Parent Order is Secured.
		$payment_method = $order->get_payment_method();

		$gateways_with_parent_secured = wps_upsell_lite_payment_gateways_with_parent_secured();

		if (! in_array($payment_method, $gateways_with_parent_secured, true)) {

			return;
		}

		// Start Tracking handling.

		$order_total = $order->get_total() ? $order->get_total() : 0;

		$content_type = 'product';
		$product_ids  = array();

		foreach ($order->get_items() as $item) {
			$product = wc_get_product($item['product_id']);

			$product_ids[] = $product->get_id();

			if ($product->get_type() === 'variable') {
				$content_type = 'product_group';
			}
		}

		$params = array(
			'content_ids'  => wp_json_encode($product_ids),
			'content_type' => $content_type,
			'value'        => $order_total,
			'currency'     => get_woocommerce_currency(),
		);

		$fb_purchase_js = sprintf("fbq('%s', '%s', %s);", 'track', 'Purchase', wp_json_encode($params, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT));

		wc_enqueue_js($fb_purchase_js);

		$upsell_fb_pixel_parent_tracked_data = array(
			'order_total' => $order_total,
		);

		wps_wocfo_hpos_update_meta_data($order_id, '_wps_upsell_fbp_parent_tracked', $upsell_fb_pixel_parent_tracked_data);
	}


	/**
	 * GA and FB Pixel Purchase Event - Track Order on Thankyou page.
	 *
	 * @param mixed $order_id order id.
	 * @since    3.0.0
	 */
	public function ga_and_fb_pixel_purchase_event($order_id = '')
	{

		$upsell_analytics_options = get_option('wps_upsell_analytics_configuration', array());

		$ga_analytics_config = ! empty($upsell_analytics_options['google-analytics']) ? $upsell_analytics_options['google-analytics'] : array();
		$fb_pixel_config     = ! empty($upsell_analytics_options['facebook-pixel']) ? $upsell_analytics_options['facebook-pixel'] : array();

		$add_ga_purchase_event       = false;
		$add_fb_pixel_purchase_event = false;

		if (! empty($ga_analytics_config['enable_purchase_event']) && 'yes' === $ga_analytics_config['enable_purchase_event']) {

			$add_ga_purchase_event = true;
		}

		if (! empty($fb_pixel_config['enable_purchase_event']) && 'yes' === $fb_pixel_config['enable_purchase_event']) {

			$add_fb_pixel_purchase_event = true;
		}

		if ($add_ga_purchase_event) :

			$this->add_ga_purchase_event($order_id);

		endif;

		if ($add_fb_pixel_purchase_event) :

			$this->add_fb_pixel_purchase_event($order_id);

		endif;
	}


	/**
	 * Google Analyics Purchase Event for Parent Order.
	 *
	 * @param string $order_id order id.
	 *
	 * @since    3.0.0
	 */
	public function add_ga_purchase_event($order_id = '')
	{

		if (empty($order_id) || wps_wocfo_hpos_get_meta_data($order_id, '_wps_upsell_ga_tracked', true) === 1) {

			return;
		}

		$order = wc_get_order($order_id);

		if (empty($order)) {

			return;
		}

		// Start Tracking handling.

		global $woocommerce;

		// Get Coupon Codes.
		$coupons_list = '';

		if (version_compare($woocommerce->version, '3.7', '>')) {

			if ($order->get_coupon_codes()) {

				$coupons_count = count($order->get_coupon_codes());
				$i             = 1;

				foreach ($order->get_coupon_codes() as $coupon) {

					$coupons_list .= $coupon;
					if ($i < $coupons_count) {
						$coupons_list .= ', ';
					}
					$i++;
				}
			}
		} else {

			if ($order->get_used_coupons()) {

				$coupons_count = count($order->get_used_coupons());
				$i             = 1;

				foreach ($order->get_used_coupons() as $coupon) {

					$coupons_list .= $coupon;
					if ($i < $coupons_count) {
						$coupons_list .= ', ';
					}
					$i++;
				}
			}
		}

		$upsell_ga_parent_tracked      = false;
		$upsell_ga_parent_tracked_data = wps_wocfo_hpos_get_meta_data($order_id, '_wps_upsell_ga_parent_tracked', true);

		if (! empty($upsell_ga_parent_tracked_data) && is_array($upsell_ga_parent_tracked_data)) {

			$upsell_ga_parent_tracked = true;
			$upsell_purchase          = false;
		}

		// All Order items.
		$order_items = $order->get_items();

		if (! empty($order_items) && is_array($order_items)) {

			foreach ($order_items as $item_id => $item) {

				// When Parent Order is already tracked.
				if ($upsell_ga_parent_tracked) {

					// If not Upsell Purchase Item then Continue. So this loop will only add Upsell items if Parent Order is already tracked.
					if (empty($item->get_meta('is_upsell_purchase'))) {

						continue;
					}

					// Did not continue means there is an Upsell item.
					$upsell_purchase = true;
				}

				$_product = $item->get_product();

				if (isset($_product->variation_data)) {

					$categories = esc_js(wc_get_formatted_variation($_product->get_variation_attributes(), true));
				} else {

					$out = array();

					$categories = get_the_terms($_product->get_id(), 'product_cat');

					if ($categories) {

						foreach ($categories as $category) {

							$out[] = $category->name;
						}
					}

					$categories = esc_js(join(',', $out));
				}

				$product_data[get_permalink($_product->get_id())] = array(
					'p_id'    => esc_html($_product->get_id()),
					'p_sku'   => esc_js($_product->get_sku() ? $_product->get_sku() : $_product->get_id()),
					'p_name'  => html_entity_decode($_product->get_title()),
					'p_price' => esc_js($order->get_item_total($item)),
					'p_cat'   => $categories,
					'p_qty'   => esc_js($item['qty']),
				);
			}

			if (! empty($product_data)) {

				// Add Product data json.
				wc_enqueue_js('wps_upsell_ga_pd=' . wp_json_encode($product_data) . ';');
			}
		}

		// When Parent Order is already tracked.
		if ($upsell_ga_parent_tracked) {

			// No Upsell Items so return as no need to send any data to GA as it's already tracked.
			if (false === $upsell_purchase) {

				wps_wocfo_hpos_update_meta_data($order_id, '_wps_upsell_ga_tracked', 1);
				return;
			}
		}

		// Get Shipping total.
		$total_shipping = $order->get_total_shipping();

		$order_total     = $order->get_total();
		$order_total_tax = $order->get_total_tax();

		$transaction_data = array(
			'id'          => esc_js($order->get_order_number()),
			'affiliation' => esc_js(get_bloginfo('name')),
			'revenue'     => esc_js($order_total),
			'tax'         => esc_js($order_total_tax),
			'shipping'    => esc_js($total_shipping),
			'coupon'      => $coupons_list,
			'currency'    => get_woocommerce_currency(),
			'label'       => 'Purchase',

		);

		// When Parent Order is already tracked.
		if ($upsell_ga_parent_tracked) {

			$parent_order_total          = ! empty($upsell_ga_parent_tracked_data['order_total']) ? $upsell_ga_parent_tracked_data['order_total'] : 0;
			$parent_order_total_tax      = ! empty($upsell_ga_parent_tracked_data['order_total_tax']) ? $upsell_ga_parent_tracked_data['order_total_tax'] : 0;
			$parent_order_total_shipping = ! empty($upsell_ga_parent_tracked_data['order_total_shipping']) ? $upsell_ga_parent_tracked_data['order_total_shipping'] : 0;

			$current_order_total          = $order_total - $parent_order_total;
			$current_order_total_tax      = $order_total_tax - $parent_order_total_tax;
			$current_order_total_shipping = $total_shipping - $parent_order_total_shipping;

			$transaction_data = array(
				'id'          => esc_js($order->get_order_number()),
				'affiliation' => esc_js(get_bloginfo('name')),
				'revenue'     => esc_js($current_order_total),
				'tax'         => esc_js($current_order_total_tax),
				'shipping'    => esc_js($current_order_total_shipping),
				'currency'    => get_woocommerce_currency(),
				'label'       => 'Upsell Purchase',
			);
		}

		// Add Transaction data json.
		wc_enqueue_js('wps_upsell_ga_td=' . wp_json_encode($transaction_data) . ';');

		$ga_purchase_js = '
                 var items = [];
                //set local currencies
            gtag("set", {"currency": wps_upsell_ga_td.currency});
            for(var p_item in wps_upsell_ga_pd){
                items.push({
                    "id": wps_upsell_ga_pd[p_item].p_sku,
                    "name": wps_upsell_ga_pd[p_item].p_name, 
                    "category": wps_upsell_ga_pd[p_item].p_cat,
                    "price": wps_upsell_ga_pd[p_item].p_price,
                    "quantity": wps_upsell_ga_pd[p_item].p_qty,
                });
               
            }
            gtag("event", "purchase", {
                "transaction_id":wps_upsell_ga_td.id,
                "affiliation": wps_upsell_ga_td.affiliation,
                "value":wps_upsell_ga_td.revenue,
                "tax": wps_upsell_ga_td.tax,
                "shipping": wps_upsell_ga_td.shipping,
                "coupon": wps_upsell_ga_td.coupon,
                "event_category": "ecommerce",
                "event_label": wps_upsell_ga_td.label,
                "non_interaction": true,
                "items":items
            });
        ';

		wc_enqueue_js($ga_purchase_js);

		wps_wocfo_hpos_update_meta_data($order_id, '_wps_upsell_ga_tracked', 1);
	}



	/**
	 * Facebook Pixel Purchase Event.
	 *
	 * @param string $order_id order id.
	 *
	 * @since    3.0.0
	 */
	public function add_fb_pixel_purchase_event($order_id = '')
	{

		if (empty($order_id) || true === wps_wocfo_hpos_get_meta_data($order_id, '_wps_upsell_fbp_tracked', true)) {

			return;
		}

		$order = wc_get_order($order_id);

		if (empty($order)) {

			return;
		}

		// Start Tracking handling.

		$order_total = $order->get_total() ? $order->get_total() : 0;

		$content_type = 'product';
		$product_ids  = array();

		$upsell_fb_pixel_parent_tracked      = false;
		$upsell_fb_pixel_parent_tracked_data = wps_wocfo_hpos_get_meta_data($order_id, '_wps_upsell_fbp_parent_tracked', true);

		if (! empty($upsell_fb_pixel_parent_tracked_data) && is_array($upsell_fb_pixel_parent_tracked_data)) {

			$upsell_fb_pixel_parent_tracked = true;
			$upsell_purchase                = false;
		}

		foreach ($order->get_items() as $item_id => $item) {

			// When Parent Order is already tracked.
			if ($upsell_fb_pixel_parent_tracked) {

				// If not Upsell Purchase Item then Continue.
				if (empty($item->get_meta('is_upsell_purchase'))) {

					continue;
				}

				// Did not continue means there is an Upsell item.
				$upsell_purchase = true;
			}

			$product = wc_get_product($item['product_id']);

			$product_ids[] = $product->get_id();

			if ($product->get_type() === 'variable') {
				$content_type = 'product_group';
			}
		}

		// When Parent Order is already tracked.
		if ($upsell_fb_pixel_parent_tracked) {

			// No Upsell Items so return as no need to send any data.
			if (false === $upsell_purchase) {

				wps_wocfo_hpos_update_meta_data($order_id, '_wps_upsell_fbp_tracked', true);
				return;
			}
		}

		// When Parent Order is already tracked.
		if ($upsell_fb_pixel_parent_tracked) {

			$parent_order_total = ! empty($upsell_fb_pixel_parent_tracked_data['order_total']) ? $upsell_fb_pixel_parent_tracked_data['order_total'] : 0;

			$order_total = $order_total - $parent_order_total;

			$params = array(
				'content_ids'  => wp_json_encode($product_ids),
				'content_type' => $content_type,
				'value'        => $order_total,
				'currency'     => get_woocommerce_currency(),
			);
		} else {

			$params = array(
				'content_ids'  => wp_json_encode($product_ids),
				'content_type' => $content_type,
				'value'        => $order_total,
				'currency'     => get_woocommerce_currency(),
			);
		}

		$fb_purchase_js = sprintf("fbq('%s', '%s', %s);", 'track', 'Purchase', wp_json_encode($params, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT));

		wc_enqueue_js($fb_purchase_js);

		wps_wocfo_hpos_update_meta_data($order_id, '_wps_upsell_fbp_tracked', true);
	}


	/**
	 * Compatibility for Enhanced Ecommerce Google Analytics Plugin by Tatvic.
	 * Remove plugin's Purchase Event from Thankyou page when
	 * Upsell Purchase is enabled.
	 *
	 * @since    3.0.0
	 */
	public function upsell_ga_compatibility_for_eega()
	{

		if (! class_exists('Enhanced_Ecommerce_Google_Analytics')) {

			return;
		}

		$upsell_analytics_options = get_option('wps_upsell_analytics_configuration', array());

		$ga_analytics_config = ! empty($upsell_analytics_options['google-analytics']) ? $upsell_analytics_options['google-analytics'] : array();

		$add_ga_purchase_event = false;

		if (! empty($ga_analytics_config['enable_purchase_event']) && 'yes' === $ga_analytics_config['enable_purchase_event']) {

			$add_ga_purchase_event = true;
		}

		// Only when Upsell Purchase is enabled.
		if ($add_ga_purchase_event) {

			global $wp_filter;

			foreach ($wp_filter['woocommerce_thankyou']->callbacks as $key => $plugin_cbs) {

				// Only remove the one with default priority.
				if (10 !== $key) {

					continue;
				}

				foreach ($plugin_cbs as $cb_key => $cb_obj) {

					if (isset($cb_obj['function']) && is_array($cb_obj['function'])) {

						// Check if the current object belongs to the class.
						if (is_a($cb_obj['function']['0'], 'Enhanced_Ecommerce_Google_Analytics_Public')) {

							$enhanced_ecommerce_google_analytics = $cb_obj['function']['0'];

							remove_action('woocommerce_thankyou', array($enhanced_ecommerce_google_analytics, 'ecommerce_tracking_code'));

							break 2;
						}
					}
				}
			}
		}
	}



	/**
	 * Compatibility for Facebook for WooCommerce plugin.
	 * Remove plugin's Purchase Event from Thankyou page when
	 * Upsell Purchase is enabled.
	 *
	 * @since    3.0.0
	 */
	public function upsell_fbp_compatibility_for_ffw()
	{

		if (! class_exists('WC_Facebookcommerce_EventsTracker')) {

			return;
		}

		$wc_integrations = WC()->integrations->get_integrations();

		if (isset($wc_integrations['facebookcommerce']) && $wc_integrations['facebookcommerce'] instanceof WC_Facebookcommerce_Integration) {

			$upsell_analytics_options = get_option('wps_upsell_analytics_configuration', array());

			$fb_pixel_config = ! empty($upsell_analytics_options['facebook-pixel']) ? $upsell_analytics_options['facebook-pixel'] : array();

			$add_fb_pixel_purchase_event = false;

			if (! empty($fb_pixel_config['enable_purchase_event']) && 'yes' === $fb_pixel_config['enable_purchase_event']) {

				$add_fb_pixel_purchase_event = true;
			}

			if ($add_fb_pixel_purchase_event) {

				// For Facebook for WooCommerce plugin version >= 1.1.0.
				remove_action('woocommerce_thankyou', array($wc_integrations['facebookcommerce']->events_tracker, 'inject_purchase_event'), 40);
			}
		}
	}

	/**
	 * Add custom nonce for checkout field.
	 *
	 * @return void
	 */
	public function wps_upsell_add_nonce_field_at_checkout()
	{
		wp_nonce_field('checkout_order_processed_nonce', 'checkout_order_processed_nonce');
	}


	/**
	 * Expire further Offers Order meta value.
	 *
	 * @param mixed $order_id order id.
	 * @since    3.0.0
	 */
	private function expire_further_offers($order_id = 0)
	{

		$expire_further_offers = wps_wocfo_hpos_get_meta_data($order_id, '_wps_upsell_expire_further_offers', true);

		if (! empty($expire_further_offers)) {

			return true;
		} else {

			return false;
		}
	}

	/**
	 * Process Payment for Upsell order.
	 *
	 * @since    1.0.0
	 * @param    int $order_id    Order ID.
	 */
	public function upsell_order_final_payment($order_id = '')
	{

		if (empty($order_id)) {

			return false;
		}
		$order = new WC_Order($order_id);
		$shipping_price_order = 0;
		if (! empty($order)) {
			$shipping_price_order = floatval(wps_wocfo_hpos_get_meta_data($order_id, 'wps_upsell_simple_shipping_product_', true));
		}

		if (0 != $shipping_price_order && ! empty($shipping_price_order)) {
			$item_ship = new WC_Order_Item_Shipping();
			$item_ship->set_name('Upsell shipping');
			$item_ship->set_total($shipping_price_order);
			// Add Shipping item to the order.
			$order->add_item($item_ship);
			$order->calculate_totals();
		}

		global $woocommerce;

		$gateways = $woocommerce->payment_gateways->get_available_payment_gateways();

		$payment_method = $order->get_payment_method();


		if ('stripe_cc' === $payment_method) {
			$_POST = wps_wocfo_hpos_get_meta_data($order_id, '_post_data', true);

			$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
			$stripe_class = $available_gateways[$payment_method];
			$payment_result = $stripe_class->process_payment($order_id, false, true);

			return 	$payment_result;
		} else {
			// For cron - Payment initialized.
			wps_wocfo_hpos_delete_meta_data($order_id, 'wps_ocufp_upsell_initialized');
			$result = '';
			$payment_method = $order->get_payment_method();

			if (! empty($payment_method)) {
				$result = $gateways[$payment_method]->process_payment($order_id, 'true');
			}

			$order->reduce_order_stock();
			return $result;
		}
	}

	// End of class.
}
