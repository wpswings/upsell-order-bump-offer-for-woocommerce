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
