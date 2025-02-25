<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      1.0.0
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/includes
 * @author     WP Swings <webmaster@wpswings.com>
 */
class Upsell_Order_Bump_Offer_For_Woocommerce {



	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @var      Upsell_Order_Bump_Offer_For_Woocommerce_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_VERSION' ) ) {
			$this->version = UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'upsell-order-bump-offer-for-woocommerce';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Upsell_Order_Bump_Offer_For_Woocommerce_Loader. Orchestrates the hooks of the plugin.
	 * - Upsell_Order_Bump_Offer_For_Woocommerce_i18n. Defines internationalization functionality.
	 * - Upsell_Order_Bump_Offer_For_Woocommerce_Admin. Defines all hooks for the admin area.
	 * - Upsell_Order_Bump_Offer_For_Woocommerce_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 */
	private function load_dependencies() {
		/**
		   * The class responsible for orchestrating the actions and filters of the
		   * core plugin.
		   */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-upsell-order-bump-offer-for-woocommerce-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-upsell-order-bump-offer-for-woocommerce-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-upsell-order-bump-offer-for-woocommerce-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-upsell-order-bump-offer-for-woocommerce-public.php';

		/**
		 * The class responsible for defining all global functions that are used through
		 * out the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-upsell-order-bump-offer-for-woocommerce-global-functions.php';

		/**
		 * The class responsible for the Onboarding functionality.
		 */
		if ( ! class_exists( 'Wpswings_Onboarding_Helper' ) ) {

			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpswings-onboarding-helper.php';
		}

		/**
		 * The class responsible for Sales by Order Bump - Data handling and Stats.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'reporting/class-wps-upsell-order-bump-report-sales-by-bump.php';

		$this->loader = new Upsell_Order_Bump_Offer_For_Woocommerce_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Upsell_Order_Bump_Offer_For_Woocommerce_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 */
	private function set_locale() {
		 $plugin_i18n = new Upsell_Order_Bump_Offer_For_Woocommerce_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 */
	private function define_admin_hooks() {
		 $plugin_admin = new Upsell_Order_Bump_Offer_For_Woocommerce_Admin( $this->get_plugin_name(), $this->get_version() );

		// Check enability of the plugin at settings page.
		$wps_ubo_global_options = get_option( 'wps_ubo_global_options', array() );

		// By default plugin will be enabled.
		$wps_upsell_bump_enable_plugin = ! empty( $wps_ubo_global_options['wps_bump_enable_plugin'] ) ? $wps_ubo_global_options['wps_bump_enable_plugin'] : 'on';


		// Add admin arena.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wps_ubo_lite_admin_menu' );

		// Load scripts and styles.
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Rest functionality for admin side ajax.
		$this->loader->add_action( 'wp_ajax_search_products_for_bump', $plugin_admin, 'search_products_for_bump' );
		$this->loader->add_action( 'wp_ajax_search_product_categories_for_bump', $plugin_admin, 'search_product_categories_for_bump' );
		$this->loader->add_action( 'wp_ajax_search_products_for_offers', $plugin_admin, 'search_products_for_offers' );
		$this->loader->add_action( 'wp_ajax_search_coupon_for_offers', $plugin_admin, 'search_coupon_for_offers' );
		$this->loader->add_action( 'wp_ajax_search_products_for_bump_offer_id', $plugin_admin, 'search_products_for_bump_offer_id' );

		// Rest functionality for admin side ajax Of One Click Funnel.
		$this->loader->add_action( 'wp_ajax_seach_products_for_offers', $plugin_admin, 'seach_products_for_offers' );
		$this->loader->add_action( 'wp_ajax_seach_products_for_funnel', $plugin_admin, 'seach_products_for_funnel' );
		$this->loader->add_action( 'wp_ajax_search_product_categories_for_funnel', $plugin_admin, 'search_product_categories_for_funnel' );

		//On clickupsell Funnel Hooks admin.

		// Dismiss Elementor inactive notice.
		$this->loader->add_action( 'wp_ajax_wps_upsell_dismiss_elementor_inactive_notice', $plugin_admin, 'dismiss_elementor_inactive_notice' );

		// Hide Upsell offer pages in admin panel 'Pages'.
		$this->loader->add_action( 'pre_get_posts', $plugin_admin, 'hide_upsell_offer_pages_in_admin' );
		$this->loader->add_filter( 'page_template', $plugin_admin, 'wps_wocuf_pro_page_template' );

		// Create new offer - ajax handle function.
		$this->loader->add_action( 'wp_ajax_wps_wocuf_pro_return_offer_content', $plugin_admin, 'return_funnel_offer_section_content' );

		// Insert and Activate respective template ajax handle function.
		$this->loader->add_action( 'wp_ajax_wps_upsell_activate_offer_template_ajax', $plugin_admin, 'activate_respective_offer_template' );

		// Add attribute to styles allowed properties.
		$this->loader->add_filter( 'safe_style_css', $plugin_admin, 'wocuf_lite_add_style_attribute' );


		if ( 'on' === $wps_upsell_bump_enable_plugin ) {

			// Adding Upsell Orders column in Orders table in backend.
			$this->loader->add_filter( 'manage_edit-shop_order_columns', $plugin_admin, 'wps_wocuf_pro_add_columns_to_admin_orders', 11 );

			// Populating Upsell Orders column with Single Order or Upsell order.
			$this->loader->add_action( 'manage_shop_order_posts_custom_column', $plugin_admin, 'wps_wocuf_pro_populate_upsell_order_column', 10, 2 );

			$this->loader->add_action( 'woocommerce_shop_order_list_table_custom_column', $plugin_admin, 'wps_wocuf_pro_populate_upsell_order_column', 10, 2 );
			$this->loader->add_filter( 'woocommerce_shop_order_list_table_columns', $plugin_admin, 'wps_wocuf_pro_add_columns_to_admin_orders', 99 );


			// Add Upsell Filtering dropdown for All Orders, No Upsell Orders, Only Upsell Orders.
			$this->loader->add_filter( 'restrict_manage_posts', $plugin_admin, 'wps_wocuf_pro_restrict_manage_posts' );

			// Modifying query vars for filtering Upsell Orders.
			$this->loader->add_filter( 'request', $plugin_admin, 'wps_wocuf_pro_request_query' );


			// Add 'Upsell Support' column on payment gateways page.
			$this->loader->add_filter( 'woocommerce_payment_gateways_setting_columns', $plugin_admin, 'upsell_support_in_payment_gateway' );

			// 'Upsell Support' content on payment gateways page.
			$this->loader->add_action( 'woocommerce_payment_gateways_setting_column_wps_upsell', $plugin_admin, 'upsell_support_content_in_payment_gateway' );

			$this->loader->add_action( 'woocommerce_product_options_general_product_data', $plugin_admin, 'upsell_simple_product_settings' );
			$this->loader->add_action( 'woocommerce_process_product_meta', $plugin_admin, 'upsell_saving_simple_product_dynamic_shipping' );
			$this->loader->add_action( 'woocommerce_product_after_variable_attributes', $plugin_admin, 'upsell_add_custom_price_to_variations', 10, 3 );
			$this->loader->add_action( 'woocommerce_save_product_variation', $plugin_admin, 'upsell_save_custom_price_variations', 10, 2 );

		}


		$this->loader->add_filter( 'woocommerce_admin_reports', $plugin_admin, 'add_upsell_reporting' );

		/*cron for notification*/
		// $this->loader->add_action( 'admin_init', $plugin_admin, 'wps_upsell_set_cron_for_plugin_notification' );
		// $this->loader->add_action( 'wps_wgm_check_for_notification_update', $plugin_admin, 'wps_upsell_save_notice_message' );
		// $this->loader->add_action( 'wp_ajax_wps_wocuf_dismiss_notice_banner', $plugin_admin, 'wps_wocuf_dismiss_notice_banner_callback' );


		// Rest functionality for order table.
		$this->loader->add_action( 'manage_shop_order_posts_custom_column', $plugin_admin, 'show_bump_total_content', 20, 2 );
		// Order Bump Report.
		$this->loader->add_filter( 'woocommerce_admin_reports', $plugin_admin, 'add_order_bump_reporting' );

		// Include Order Bump screen for Onboarding pop-up.
		$this->loader->add_filter( 'wps_helper_valid_frontend_screens', $plugin_admin, 'add_wps_frontend_screens' );

		// Include Order Bump plugin for Deactivation pop-up.
		$this->loader->add_filter( 'wps_deactivation_supported_slug', $plugin_admin, 'add_wps_deactivation_screens' );

		// Validate Pro version compatibility.
		$this->loader->add_action( 'plugins_loaded', $plugin_admin, 'validate_version_compatibility' );

		// Db migration hook.
		$this->loader->add_action( 'admin_init', $plugin_admin, 'wps_migrate_db_keys' );

		// Set The Cron For Banner Image.
		$this->loader->add_action( 'admin_init', $plugin_admin, 'wps_uob_set_cron_for_plugin_notification' );

		$this->loader->add_action( 'wps_wgm_check_for_notification_update', $plugin_admin, 'wps_uob_save_notice_message' );

		$this->loader->add_action( 'wp_ajax_wps_sfw_dismiss_notice_banner', $plugin_admin, 'wps_uob_dismiss_notice_banner_callback' );

		$this->loader->add_action( 'wp_ajax_wps_install_and_redirect_upsell_plugin', $plugin_admin, 'wps_install_and_redirect_upsell_plugin_callback' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 */
	private function define_public_hooks() {
		// Check enability of the plugin at settings page.
		$wps_ubo_global_options = get_option( 'wps_ubo_global_options', array() );

		// By default plugin will be enabled.
		$wps_upsell_bump_enable_plugin = ! empty( $wps_ubo_global_options['wps_bump_enable_plugin'] ) ? $wps_ubo_global_options['wps_bump_enable_plugin'] : 'on';
        // print_r($wps_upsell_bump_enable_plugin);
		if ( 'on' === $wps_upsell_bump_enable_plugin ) {

			$plugin_public = new Upsell_Order_Bump_Offer_For_Woocommerce_Public( $this->get_plugin_name(), $this->get_version() );

			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

			// Add custom hook to show offer bump after payment gateways but before terms as one is not provided by Woocommerce.
			$this->loader->add_action( 'woocommerce_before_template_part', $plugin_public, 'add_bump_offer_custom_hook', 10, 2 );

			// Bump Offer location.
			$bump_offer_location = ! empty( $wps_ubo_global_options['wps_ubo_offer_location'] ) ? $wps_ubo_global_options['wps_ubo_offer_location'] : '_after_payment_gateways';
			$bump_cart_offer_location = ! empty( $wps_ubo_global_options['wps_enable_cart_upsell_location'] ) ? $wps_ubo_global_options['wps_enable_cart_upsell_location'] : '';

			$plugin_path = 'woocommerce-germanized/woocommerce-germanized.php';

			if ( is_plugin_active( $plugin_path ) ) {
				$offer_location_details = wps_ubo_lite_retrieve_bump_location_details_for_wc_germanized_compatibility( $bump_offer_location );
			} else {
				$offer_location_details = wps_ubo_lite_retrieve_bump_location_details( $bump_offer_location );
			}

			// Show bump offer.
			$this->loader->add_action( $offer_location_details['hook'], $plugin_public, 'show_offer_bump', $offer_location_details['priority'] );

			// bump shortcode for all page.
			$this->loader->add_action( 'init', $plugin_public, 'wps_show_offer_bump_shortcode', 999 );

			// Show bump offer on cart.
			$this->loader->add_action( $bump_cart_offer_location, $plugin_public, 'show_offer_bump_on_cart' );

			// Ajax to add bump offer.
			$this->loader->add_action( 'wp_ajax_add_offer_in_cart', $plugin_public, 'add_offer_in_cart' );
			$this->loader->add_action( 'wp_ajax_nopriv_add_offer_in_cart', $plugin_public, 'add_offer_in_cart' );

			// Ajax for the recommdation product.
			$this->loader->add_action( 'wp_ajax_add_recommendated_offer_in_popup', $plugin_public, 'wps_add_recommendated_offer_in_popup' );
			$this->loader->add_action( 'wp_ajax_nopriv_add_recommendated_offer_in_popup', $plugin_public, 'wps_add_recommendated_offer_in_popup' );

			// Ajax for Ajac Woocommerce Cart.
			$this->loader->add_action( 'wp_ajax_ql_woocommerce_ajax_add_to_cart', $plugin_public, 'ql_woocommerce_ajax_add_to_cart' );
			$this->loader->add_action( 'wp_ajax_nopriv_ql_woocommerce_ajax_add_to_cart', $plugin_public, 'ql_woocommerce_ajax_add_to_cart' );

			// Ajax to add bump offer.
			$this->loader->add_action( 'wp_ajax_wps_add_the_product', $plugin_public, 'wps_add_the_product' );
			$this->loader->add_action( 'wp_ajax_nopriv_wps_add_the_product', $plugin_public, 'wps_add_the_product' );

			// Ajax to remove bump offer.
			$this->loader->add_action( 'wp_ajax_wps_remove_offer_product', $plugin_public, 'wps_remove_offer_product' );
			$this->loader->add_action( 'wp_ajax_nopriv_wps_remove_offer_product', $plugin_public, 'wps_remove_offer_product' );

			// Ajax to add to cart the recommendation.
			$this->loader->add_action( 'wp_ajax_add_to_cart_recommendation', $plugin_public, 'wps_add_to_cart_recommendation' );
			$this->loader->add_action( 'wp_ajax_nopriv_add_to_cart_recommendation', $plugin_public, 'wps_add_to_cart_recommendation' );

			// Ajax to varaition bump offer popup.
			$this->loader->add_action( 'wp_ajax_wps_variation_select_added', $plugin_public, 'wps_variation_select_added' );
			$this->loader->add_action( 'wp_ajax_nopriv_wps_variation_select_added', $plugin_public, 'wps_variation_select_added' );

			// Ajax to add bump offer.
			$this->loader->add_action( 'wp_ajax_add_variation_offer_in_cart', $plugin_public, 'add_variation_offer_in_cart' );
			$this->loader->add_action( 'wp_ajax_nopriv_add_variation_offer_in_cart', $plugin_public, 'add_variation_offer_in_cart' );

			// Change mini Cart content.
			$this->loader->add_filter( 'woocommerce_cart_item_product', $plugin_public, 'change_mini_cart_content', 10, 3 );

			// Ajax to search variation.
			$this->loader->add_action( 'wp_ajax_search_variation_id_by_select', $plugin_public, 'search_variation_id_by_select' );
			$this->loader->add_action( 'wp_ajax_nopriv_search_variation_id_by_select', $plugin_public, 'search_variation_id_by_select' );

			// Ajax to remove bump offer.
			$this->loader->add_action( 'wp_ajax_remove_offer_in_cart', $plugin_public, 'remove_offer_in_cart' );
			$this->loader->add_action( 'wp_ajax_nopriv_remove_offer_in_cart', $plugin_public, 'remove_offer_in_cart' );

			// Global Custom CSS.
			$this->loader->add_action( 'wp_head', $plugin_public, 'global_custom_css' );

			// Global custom JS.
			$this->loader->add_action( 'wp_footer', $plugin_public, 'global_custom_js' );

			// All mandatory functions to be called after adding offer product.
			$this->loader->add_action( 'woocommerce_init', $plugin_public, 'woocommerce_init_ubo_functions' );

			// Hide Order Bump meta from order items.
			$this->loader->add_filter( 'woocommerce_order_item_get_formatted_meta_data', $plugin_public, 'hide_order_bump_meta' );

			// Set any page as order success page.
			$this->loader->add_action( 'template_redirect', $plugin_public, 'wps_redirect_custom_thank_you' );
			$this->loader->add_action( 'init', $plugin_public, 'wps_triggered_shortcode_page' );
			$this->loader->add_action( 'woocommerce_new_order', $plugin_public, 'wps_custom_get_current_order_id', 10, 1 );

			/* Discount at cart section.*/
			$this->loader->add_action( 'woocommerce_blocks_enqueue_cart_block_scripts_after', $plugin_public, 'wps_woo_cart_discount_section' );   // cart discount.

			// Ajax to add the cart discount product in the cart.
			$this->loader->add_action( 'wp_ajax_add_cart_discount_offer_in_cart', $plugin_public, 'wps_add_cart_discount_offer_in_cart' );
			$this->loader->add_action( 'wp_ajax_nopriv_add_cart_discount_offer_in_cart', $plugin_public, 'wps_add_cart_discount_offer_in_cart' );
			$this->loader->add_action( 'woocommerce_before_calculate_totals', $plugin_public, 'wps_order_cart_custom_price_refresh' );

			$this->loader->add_action( 'woocommerce_blocks_enqueue_checkout_block_scripts_after', $plugin_public, 'wps_show_bump_on_checkout_block_callback' );

			$this->loader->add_action( 'woocommerce_blocks_enqueue_cart_block_scripts_after', $plugin_public, 'wps_show_bump_on_cart_block_callback' );

			// Ajax to add to cart the recommendation.
			$this->loader->add_action( 'wp_ajax_add_to_cart_fbt_product', $plugin_public, 'wps_add_to_cart_fbt_product_callback' );
			$this->loader->add_action( 'wp_ajax_nopriv_add_to_cart_fbt_product', $plugin_public, 'wps_add_to_cart_fbt_product_callback' );


            //Hooks Related to one click upsell Start From Here.
			$this->loader->add_action( 'woocommerce_init', $plugin_public, 'check_compatibltiy_instance_cs' );

			// Set cron recurrence time for 'wps_wocuf_twenty_minutes' schedule.
			$this->loader->add_filter( 'cron_schedules', $plugin_public, 'set_cron_schedule_time' );

			// Redirect upsell offer pages if not admin or upsell nonce expired.
			$this->loader->add_action( 'template_redirect', $plugin_public, 'upsell_offer_page_redirect' );

			// Hide upsell offer pages from nav menu front-end.
			$this->loader->add_filter( 'wp_page_menu_args', $plugin_public, 'exclude_pages_from_front_end', 99 );

			// Hide upsell offer pages from added menu list in customizer and admin panel.
			$this->loader->add_filter( 'wp_get_nav_menu_items', $plugin_public, 'exclude_pages_from_menu_list', 10, 3 );


			$wps_upsell_global_settings = get_option( 'wps_upsell_lite_global_options', array() );

			$remove_all_styles = ! empty( $wps_upsell_global_settings['remove_all_styles'] ) ? $wps_upsell_global_settings['remove_all_styles'] : 'yes';
	
			if ( 'yes' === $remove_all_styles && wps_upsell_lite_elementor_plugin_active() ) {
	
				// Remove styles from offer pages.
				$this->loader->add_action( 'wp_print_styles', $plugin_public, 'remove_styles_offer_pages' );
			}

			$this->loader->add_action( 'init', $plugin_public, 'upsell_shortcodes' );

			// Hide currency switcher on any page.
			$this->loader->add_filter( 'wps_currency_switcher_side_switcher_after_html', $plugin_public, 'hide_switcher_on_upsell_page' );

           	// Remove http and https from Upsell Action shortcodes added by Page Builders.
			$this->loader->add_filter( 'the_content', $plugin_public, 'filter_upsell_shortcodes_content' );

			$this->loader->add_filter( 'wp_kses_allowed_html', $plugin_public, 'wocuf_lite_allow_script_tags' );


			// Initiate Upsell Orders before processing payment.
			$this->loader->add_action( 'woocommerce_checkout_order_processed', $plugin_public, 'wps_wocuf_initate_upsell_orders_shortcode_checkout_org' );

			// Initiate Upsell Orders before processing payment.
			$this->loader->add_action( 'woocommerce_store_api_checkout_order_processed', $plugin_public, 'wps_wocuf_initate_upsell_orders_api_checkout_org', 90 );

			// When user clicks on No thanks for Upsell offer.
			! is_admin() && $this->loader->add_action( 'wp_loaded', $plugin_public, 'wps_wocuf_pro_process_the_funnel' );

			// When user clicks on Add upsell product to my Order.
			! is_admin() && $this->loader->add_action( 'wp_loaded', $plugin_public, 'wps_wocuf_pro_charge_the_offer' );

			// Define Cron schedule fire Event for Order payment process.
			$this->loader->add_action( 'wps_wocuf_lite_order_cron_schedule', $plugin_public, 'order_payment_cron_fire_event' );

			// Global Custom CSS.
			$this->loader->add_action( 'wp_head', $plugin_public, 'post_global_custom_css' );

			// Global custom JS.
			$this->loader->add_action( 'wp_footer', $plugin_public, 'post_global_custom_js' );

			// Reset Timer session for Timer shortcode.
			$this->loader->add_action( 'wp_footer', $plugin_public, 'reset_timer_session_data' );

			// Hide the upsell meta for Upsell order item for Customers.
			! is_admin() && $this->loader->add_filter( 'woocommerce_order_item_get_formatted_meta_data', $plugin_public, 'hide_order_item_formatted_meta_data' );

			// Handle Upsell Orders on Thankyou for Success Rate and Stats.
			$this->loader->add_action( 'woocommerce_thankyou', $plugin_public, 'upsell_sales_by_funnel_handling' );

			// Google Analytics and Facebook Pixel Tracking - Start.

			// GA and FB Pixel Base Code.
			$this->loader->add_action( 'wp_head', $plugin_public, 'add_ga_and_fb_pixel_base_code' );

			// GA and FB Pixel Purchase Event - Track Parent Order on 1st Upsell Offer Page.
			$this->loader->add_action( 'wp_head', $plugin_public, 'ga_and_fb_pixel_purchase_event_for_parent_order', 100 );

			// GA and FB Pixel Purchase Event - Track Order on Thankyou page.
			$this->loader->add_action( 'woocommerce_thankyou', $plugin_public, 'ga_and_fb_pixel_purchase_event' );

			/**
			 * Compatibility for Enhanced Ecommerce Google Analytics Plugin by Tatvic.
			 * Remove plugin's Purchase Event from Thankyou page when
			 * Upsell Purchase is enabled.
			 */
			$this->loader->add_action( 'wp_loaded', $plugin_public, 'upsell_ga_compatibility_for_eega' );

			/**
			 * Compatibility for Facebook for WooCommerce plugin.
			 * Remove plugin's Purchase Event from Thankyou page when
			 * Upsell Purchase is enabled.
			 */
			$this->loader->add_action( 'woocommerce_init', $plugin_public, 'upsell_fbp_compatibility_for_ffw' );

			// Google Analytics and Facebook Pixel Tracking - End.
			$this->loader->add_action( 'woocommerce_after_checkout_billing_form', $plugin_public, 'wps_upsell_add_nonce_field_at_checkout' );

		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		 $this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		 return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Upsell_Order_Bump_Offer_For_Woocommerce_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		 return $this->version;
	}

	/**
	 * Public static variable to be accessed in this plugin.
	 *
	 * @var     callback string
	 * @since   1.0.0
	 */
	public static $wps_upsell_bump_list_callback_function = 'wps_upsell_bump_list_callback_return';

	/**
	 * Validate the use of bump lists at org and pro version.
	 *
	 * @since    1.0.0
	 */
	public static function wps_upsell_bump_list_callback_return() {
		 $wps_ubo_offer_array_collection = get_option( 'wps_ubo_bump_list', array() );

		if ( wps_ubo_lite_if_pro_exists() && class_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro' ) ) {

			$wps_upsell_bump_callname_lic = Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$wps_upsell_bump_lic_callback_function;

			$wps_upsell_bump_callname_lic_initial = Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$wps_upsell_bump_lic_ini_callback_function;

			$day_count = Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$wps_upsell_bump_callname_lic_initial();

			if ( Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$wps_upsell_bump_callname_lic() || 0 <= $day_count ) {

				return $wps_ubo_offer_array_collection;
			} else {

				return array();
			}
		} else {

			$single_first_bump = array( key( $wps_ubo_offer_array_collection ) => $wps_ubo_offer_array_collection[ key( $wps_ubo_offer_array_collection ) ] );

			// Unset Smart Offer Upgrade in case as it's a pro feature.
			$single_first_bump[ key( $wps_ubo_offer_array_collection ) ]['wps_ubo_offer_replace_target'] = 'no';

			return $single_first_bump;
		}
	}
} // End of class.
