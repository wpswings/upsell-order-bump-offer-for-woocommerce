<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://makewebbetter.com/
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
 * @author     Make Web Better <webmaster@makewebbetter.com>
 */
class Upsell_Order_Bump_Offer_For_Woocommerce {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Upsell_Order_Bump_Offer_For_Woocommerce_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
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
	 * @access   private
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

		$this->loader = new Upsell_Order_Bump_Offer_For_Woocommerce_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Upsell_Order_Bump_Offer_For_Woocommerce_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Upsell_Order_Bump_Offer_For_Woocommerce_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Upsell_Order_Bump_Offer_For_Woocommerce_Admin( $this->get_plugin_name(), $this->get_version() );

		// Add admin arena.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'mwb_ubo_lite_admin_menu' );

		// Load scripts and styles.
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Rest functionality for admin side ajax.
		$this->loader->add_action( 'wp_ajax_search_products_for_bump', $plugin_admin, 'search_products_for_bump' );
		$this->loader->add_action( 'wp_ajax_search_product_categories_for_bump', $plugin_admin, 'search_product_categories_for_bump' );
		$this->loader->add_action( 'wp_ajax_search_products_for_offers', $plugin_admin, 'search_products_for_offers' );

		// Rest functionality for order table.
		$this->loader->add_action( 'manage_shop_order_posts_custom_column', $plugin_admin, 'show_bump_total_content', 20, 2 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		// Check enability of the plugin at settings page.
		$mwb_ubo_global_options = get_option( 'mwb_ubo_global_options', array() );

		// By default plugin will be enabled.
		$mwb_upsell_bump_enable_plugin = ! empty( $mwb_ubo_global_options['mwb_bump_enable_plugin'] ) ? $mwb_ubo_global_options['mwb_bump_enable_plugin'] : 'on';

		if ( 'on' == $mwb_upsell_bump_enable_plugin ) {

			$plugin_public = new Upsell_Order_Bump_Offer_For_Woocommerce_Public( $this->get_plugin_name(), $this->get_version() );

			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

			// Add custom hook to show offer bump after payment gateways but before terms as one is not provided by Woocommerce.
			$this->loader->add_action( 'woocommerce_before_template_part', $plugin_public, 'add_bump_offer_custom_hook', 10, 2 );

			// Bump Offer location.
			$bump_offer_location = ! empty( $mwb_ubo_global_options['mwb_ubo_offer_location'] ) ? $mwb_ubo_global_options['mwb_ubo_offer_location'] : '_after_payment_gateways';

			$offer_location_details = mwb_ubo_lite_retrieve_bump_location_details( $bump_offer_location );

			// Show bump offer.
			$this->loader->add_action( $offer_location_details['hook'], $plugin_public, 'show_offer_bump', $offer_location_details['priority'] );

			// Ajax to add bump offer.
			$this->loader->add_action( 'wp_ajax_add_offer_in_cart', $plugin_public, 'add_offer_in_cart' );
			$this->loader->add_action( 'wp_ajax_nopriv_add_offer_in_cart', $plugin_public, 'add_offer_in_cart' );

			// Ajax to add bump offer.
			$this->loader->add_action( 'wp_ajax_add_variation_offer_in_cart', $plugin_public, 'add_variation_offer_in_cart' );
			$this->loader->add_action( 'wp_ajax_nopriv_add_variation_offer_in_cart', $plugin_public, 'add_variation_offer_in_cart' );

			// Ajax to search variation.
			$this->loader->add_action( 'wp_ajax_search_variation_id_by_select', $plugin_public, 'search_variation_id_by_select' );
			$this->loader->add_action( 'wp_ajax_nopriv_search_variation_id_by_select', $plugin_public, 'search_variation_id_by_select' );

			// Ajax to remove bump offer.
			$this->loader->add_action( 'wp_ajax_remove_offer_in_cart', $plugin_public, 'remove_offer_in_cart' );
			$this->loader->add_action( 'wp_ajax_nopriv_remove_offer_in_cart', $plugin_public, 'remove_offer_in_cart' );

			// Start php session.
			if ( ! session_id() ) {

				session_start();
			}

			if ( ! empty( $_SESSION['mwb_upsell_bump_target_key'] ) ) {

				// Cost calculations only when the offer is added.
				$this->loader->add_action( 'woocommerce_before_calculate_totals', $plugin_public, 'woocommerce_custom_price_to_cart_item' );

				// Disable quantity field.
				$this->loader->add_filter( 'woocommerce_cart_item_quantity', $plugin_public, 'disable_quantity_bump_product_in_cart', 10, 2 );

				// Removing offer or target product manually by cart.
				$this->loader->add_action( 'woocommerce_remove_cart_item', $plugin_public, 'after_remove_product', 21, 2 );

				// Add meta data to order item for order review.
				$this->loader->add_action( 'woocommerce_checkout_create_order', $plugin_public, 'add_order_item_meta', 10 );

				// Reset custom session data.
				$this->loader->add_action( 'woocommerce_thankyou', $plugin_public, 'reset_session_variable', 10 );
			}
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
	public static $mwb_upsell_bump_list_callback_function = 'mwb_upsell_bump_list_callback_return';

	/**
	 * Validate the use of bump lists at org and pro version.
	 *
	 * @since    1.0.0
	 */
	public static function mwb_upsell_bump_list_callback_return() {

		$mwb_ubo_offer_array_collection = get_option( 'mwb_ubo_bump_list', array() );

		if ( mwb_ubo_lite_is_plugin_active( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php' ) && class_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro' ) ) {

			$mwb_upsell_bump_callname_lic = Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$mwb_upsell_bump_lic_callback_function;

			$mwb_upsell_bump_callname_lic_initial = Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$mwb_upsell_bump_lic_ini_callback_function;

			$day_count = Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$mwb_upsell_bump_callname_lic_initial();

			if ( Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$mwb_upsell_bump_callname_lic() || 0 <= $day_count ) {

				return $mwb_ubo_offer_array_collection;

			} else {

				return array();
			}
		} else {

			return array( key( $mwb_ubo_offer_array_collection ) => $mwb_ubo_offer_array_collection[ key( $mwb_ubo_offer_array_collection ) ] );
		}
	}

	// End of class.
}
