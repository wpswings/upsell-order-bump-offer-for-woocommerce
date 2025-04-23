<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since             1.0.0
 * @package           Upsell_Order_Bump_Offer_For_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Upsell Funnel Builder for WooCommerce
 * Plugin URI:        https://wordpress.org/plugins/upsell-order-bump-offer-for-woocommerce/
 * Description:       <code><strong>Upsell Funnel Builder for WooCommerce</strong></code>helps merchants maximize sales and generate revenue by curating one-click upsell and bump offers!. <a target="_blank" href="https://wpswings.com/woocommerce-plugins/?utm_source=wpswings-orderbump-shop&utm_medium=orderbump-pro-backend&utm_campaign=shop-page" >Elevate your eCommerce store by exploring more on <strong>WP Swings</strong></a>.
 *
 * Requires at least:       5.5.0
 * Tested up to:            6.8.0
 * WC requires at least:    6.1.0
 * WC tested up to:         9.8.2
 *
 * Requires Plugins: woocommerce
 * Version:           3.0.1
 * Author:            WP Swings
 * Author URI:        https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * License:           GPL-3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       upsell-order-bump-offer-for-woocommerce
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

use Automattic\WooCommerce\Utilities\OrderUtil;

$activated      = false;
$active_plugins = get_option( 'active_plugins', array() );
if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	$active_network_wide = get_site_option( 'active_sitewide_plugins', array() );
	if ( ! empty( $active_network_wide ) ) {
		foreach ( $active_network_wide as $key => $value ) {
			$active_plugins[] = $key;
		}
	}
	$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	if ( file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) && in_array( 'woocommerce/woocommerce.php', $active_plugins, true ) ) {
		$activated = true;
	}
} elseif ( file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) && in_array( 'woocommerce/woocommerce.php', $active_plugins, true ) ) {
	$activated = true;
}

if ( $activated ) {


	// HPOS Compatibility and cart and checkout block.
	add_action(
		'before_woocommerce_init',
		function () {
			if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			}
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
			}
		}
	);

	require_once ABSPATH . 'wp-admin/includes/plugin.php';

	/**
	 * Plugin Active Detection.
	 *
	 * @since    1.0.0
	 * @param    string $plugin_slug index file of plugin.
	 */
	function wps_ubo_lite_is_plugin_active( $plugin_slug = '' ) {

		if ( empty( $plugin_slug ) ) {

			return false;
		}

		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {

			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		return in_array( $plugin_slug, $active_plugins, true ) || array_key_exists( $plugin_slug, $active_plugins );
	}


	/**
	 * Plugin Active Detection For One Click Upsell.
	 *
	 * @param mixed $plugin_slug plugin slug.
	 */
	if ( ! function_exists( 'wps_upsell_lite_is_plugin_active_funnel_builder' ) ) {

		/**
		 * Wps_upsell_lite_is_plugin_active_funnel_builder.
		 *
		 * Adds necessary URL parameters for the upsell live offer funnel.
		 *
		 * @since 2.0.0
		 * @param string $plugin_slug plugin slug.
		 * @return array
		 */
		function wps_upsell_lite_is_plugin_active_funnel_builder( $plugin_slug ) {

			if ( empty( $plugin_slug ) ) {

				return false;
			}

			$active_plugins = (array) get_option( 'active_plugins', array() );

			if ( is_multisite() ) {

				$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
			}

			return in_array( $plugin_slug, $active_plugins, true ) || array_key_exists( $plugin_slug, $active_plugins );
		}
	}

	/**
	 * Currently plugin version.
	 */
	define( 'UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_VERSION', '3.0.1' );
	if ( ! defined( 'WPS_WOCUF_URL_FUNNEL_BUILDER' ) ) {
		define( 'WPS_WOCUF_URL_FUNNEL_BUILDER', plugin_dir_url( __FILE__ ) );
	}

	if ( ! defined( 'WPS_WOCUF_DIRPATH_FUNNEL_BUILDER' ) ) {
		define( 'WPS_WOCUF_DIRPATH_FUNNEL_BUILDER', plugin_dir_path( __FILE__ ) );
	}


	add_filter( 'woocommerce_get_checkout_order_received_url', 'wps_wocuf_redirect_order_while_upsell_org_funnel_builder', 10, 2 );

	/**
	 * Function to save redirection.
	 *
	 * @param string $order_received_url is the order url.
	 * @param object $data is the order data.
	 * @return string
	 */
	if ( ! function_exists( 'wps_wocuf_redirect_order_while_upsell_org_funnel_builder' ) ) {

		/**
		 * Function to save redirection.
		 *
		 * @param string $order_received_url is the order url.
		 * @param object $data is the order data.
		 * @return string
		 */
		function wps_wocuf_redirect_order_while_upsell_org_funnel_builder( $order_received_url, $data ) {

			wps_wocfo_hpos_update_meta_data_funnel_builder( $data->get_id(), 'wps_wocuf_upsell_funnel_order_redirection_link', $order_received_url );

			$order_received_url_data = wps_wocfo_hpos_get_meta_data_funnel_builder( $data->get_id(), 'wps_wocfo_upsell_funnel_redirection_link_org', true );
			if ( ! empty( $order_received_url_data ) ) {
				$order_received_url = $order_received_url_data;
			}
			return $order_received_url;
		}
	}


	$old_pro_present   = false;
	$installed_plugins = get_plugins();

	if ( array_key_exists( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php', $installed_plugins ) ) {
		$pro_plugin = $installed_plugins['upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php'];
		if ( version_compare( $pro_plugin['Version'], '2.1.0', '<' ) ) {
			$old_pro_present = true;
		}
	}

	if ( true === $old_pro_present || 1 === $old_pro_present || '1' === $old_pro_present ) {

		add_action( 'admin_notices', 'wps_ubo_check_and_inform_update' );

		/**
		 * Check update if pro is old.
		 */
		function wps_ubo_check_and_inform_update() {
			$update_file = plugin_dir_path( __DIR__ ) . 'upsell-order-bump-offer-for-woocommerce-pro/class-mwb-upsell-bump-update.php';

			// If present but not active.
			if ( ! wps_ubo_lite_is_plugin_active( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php' ) ) {
				if ( file_exists( $update_file ) ) {

					$mwb_upsell_bump_purchase_code = get_option( 'wps_upsell_bump_license_key', '' );
					if ( empty( $mwb_upsell_bump_purchase_code ) ) {
						$mwb_upsell_bump_purchase_code = get_option( 'mwb_upsell_bump_license_key', '' );
					}
					! defined( 'UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_LICENSE_KEY' ) && define( 'UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_LICENSE_KEY', $mwb_upsell_bump_purchase_code );
					! defined( 'UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_BASE_FILE' ) && define( 'UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_BASE_FILE', 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php' );
				}
				require_once $update_file;
			} else {

				$mwb_upsell_bump_purchase_code = get_option( 'wps_upsell_bump_license_key', 'wpslicensetest' );
				if ( empty( $mwb_upsell_bump_purchase_code ) ) {
					$mwb_upsell_bump_purchase_code = get_option( 'mwb_upsell_bump_license_key', 'wpslicensetest' );
				}

				if ( ! empty( $mwb_upsell_bump_purchase_code ) ) {
					update_option( 'wps_upsell_bump_license_key', $mwb_upsell_bump_purchase_code );
					update_option( 'mwb_upsell_bump_license_key', $mwb_upsell_bump_purchase_code );
				}
			}

			if ( defined( 'UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_BASE_FILE' ) ) {
				do_action( 'mwb_upsell_bump_check_event' );
				$is_update_fetched = get_option( 'mwb_bump_plugin_update', 'false' );
				$plugin_transient  = get_site_transient( 'update_plugins' );
				$update_obj        = ! empty( $plugin_transient->response[ UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_BASE_FILE ] ) ? $plugin_transient->response[ UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_BASE_FILE ] : false;
				if ( ! empty( $update_obj ) ) :
					?>
					<div class="notice notice-error is-dismissible">
						<p><?php esc_html_e( 'Your Upsell Order Bump Offer for WooCommerce Pro plugin update is here! Please Update it now.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
					</div>
					<?php
				endif;
			}
		}
	}

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in :
	 * includes/class-upsell-order-bump-offer-for-woocommerce-activator.php
	 *
	 * @since    1.0.0
	 */
	function activate_upsell_order_bump_offer_for_woocommerce() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-upsell-order-bump-offer-for-woocommerce-activator.php';
		Upsell_Order_Bump_Offer_For_Woocommerce_Activator::activate();
	}


	/**
	 * The file responsible for Upsell Sales by Funnel - Data handling and Stats.
	 */
	if ( ! wps_ubo_lite_is_plugin_active( 'woocommerce-one-click-upsell-funnel-pro/woocommerce-one-click-upsell-funnel-pro.php' ) ) {
		require_once plugin_dir_path( __FILE__ ) . 'reporting/class-wps-upsell-report-sales-by-funnel-bump.php';
	}


	/**
	 * The file responsible for Upsell Widgets added within every page builder.
	 */
	require_once plugin_dir_path( __FILE__ ) . 'page-builders/class-wps-upsell-widget-loader-bump.php';
	if ( class_exists( 'WPS_UPSELL_WIDGET_LOADER_BUMP' ) ) {
		WPS_UPSELL_WIDGET_LOADER_BUMP::get_instance();
	}


	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in : includes/class-upsell-order-bump-offer-for-woocommerce-deactivator.php
	 *
	 * @since    1.0.0
	 */
	function deactivate_upsell_order_bump_offer_for_woocommerce() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-upsell-order-bump-offer-for-woocommerce-deactivator.php';
		Upsell_Order_Bump_Offer_For_Woocommerce_Deactivator::deactivate();
	}

	/**
	 * The code that runs during plugin validation.
	 * This action is checks for WooCommerce Dependency.
	 *
	 * @since    1.0.0
	 */
	function wps_ubo_lite_plugin_activation() {
		$activation['status']  = true;
		$activation['message'] = '';

		// Dependant plugin.
		if ( ! wps_ubo_lite_is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

			$activation['status']  = false;
			$activation['message'] = 'woo_inactive';
		}

		return $activation;
	}

	$wps_ubo_lite_plugin_activation = wps_ubo_lite_plugin_activation();

	if ( true === $wps_ubo_lite_plugin_activation['status'] ) {

		// Define all the neccessary details.
		define( 'UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL', plugin_dir_url( __FILE__ ) );

		define( 'UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_DIR_PATH', plugin_dir_path( __FILE__ ) );

		// If pro version is inactive add setings link to org version.
		if ( ! wps_ubo_lite_is_plugin_active( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php' ) && ! wps_ubo_lite_is_plugin_active( 'woocommerce-one-click-upsell-funnel-pro/woocommerce-one-click-upsell-funnel-pro.php' ) ) {

			// Add settings links.
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wps_ubo_lite_plugin_action_links' );

			/**
			 * Add Settings link if premium version is not available.
			 *
			 * @since    1.0.0
			 * @param    string $links link to admin arena of plugin.
			 */
			function wps_ubo_lite_plugin_action_links( $links ) {

				$plugin_links = array(
					'<a href="' . admin_url( 'admin.php?page=upsell-order-bump-offer-for-woocommerce-setting&tab=general-setting' ) .
						'">' . esc_html__( 'Settings', 'upsell-order-bump-offer-for-woocommerce' ) . '</a>',
					'<a class="wps-ubo-lite-go-pro" style="background: #05d5d8; color: white; font-weight: 700; padding: 2px 5px; border: 1px solid #05d5d8; border-radius: 5px;" href="https://wpswings.com/product/upsell-order-bump-offer-for-woocommerce-pro/?utm_source=wpswings-order-bump-pro&utm_medium=order-bump-org-backend&utm_campaign=order-bump-pro" target="_blank">' . esc_html__( 'GO PRO', 'upsell-order-bump-offer-for-woocommerce' ) . '</a>',
				);

				return array_merge( $plugin_links, $links );
			}
		}

		add_filter( 'plugin_row_meta', 'wps_ubo_lite_add_important_links', 10, 2 );

		/**
		 * Add custom links for getting premium version.
		 *
		 * @param   string $links link to index file of plugin.
		 * @param   string $file index file of plugin.
		 *
		 * @since    1.0.0
		 */
		function wps_ubo_lite_add_important_links( $links, $file ) {

			if ( strpos( $file, 'upsell-order-bump-offer-for-woocommerce.php' ) !== false ) {

				$row_meta = array(
					'demo'    => '<a href="https://demo.wpswings.com/upsell-order-bump-offer-for-woocommerce-pro/?utm_source=wpswings-order-bump-demo&utm_medium=order-bump-org-backend&utm_campaign=order-bump-demo" target="_blank"><img src="' . esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL ) . 'admin/resources/icons/Demo.svg" class="wps-info-img" alt="Demo image">' . esc_html__( 'Demo', 'upsell-order-bump-offer-for-woocommerce' ) . '</a>',
					'doc'     => '<a href="https://docs.wpswings.com/upsell-order-bump-offer-for-woocommerce/?utm_source=wpswings-order-bump-doc&utm_medium=order-bump-org-backend&utm_campaign=order-bump-doc" target="_blank"><img src="' . esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL ) . 'admin/resources/icons/Documentation.svg" class="wps-info-img" alt="Documentation image">' . esc_html__( 'Documentation', 'upsell-order-bump-offer-for-woocommerce' ) . '</a>',
					'video'     => '<a href="https://www.youtube.com/watch?v=p9KIQyXauY4&t=101s" target="_blank"><img src="' . esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL ) . 'admin/resources/icons/video.png" class="wps-info-img" alt="Documentation image">' . esc_html__( 'Video', 'upsell-order-bump-offer-for-woocommerce' ) . '</a>',
					'support' => '<a href="https://wpswings.com/submit-query/?utm_source=wpswings-orderbump-support&utm_medium=orderbump-org-backend&utm_campaign=support" target="_blank"><img src="' . esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL ) . 'admin/resources/icons/Support.svg" class="wps-info-img" alt="DeSupportmo image">' . esc_html__( 'Support', 'upsell-order-bump-offer-for-woocommerce' ) . '</a>',
					'services' => '<a href="https://wpswings.com/woocommerce-services/?utm_source=wpswings-orderbump-services&utm_medium=orderbump-pro-backend&utm_campaign=woocommerce-services" target="_blank"><img src="' . esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL ) . 'admin/resources/icons/Services.svg" class="wps-info-img" alt="DeSupportmo image">' . esc_html__( 'Services', 'upsell-order-bump-offer-for-woocommerce' ) . '</a>',
				);

				return array_merge( $links, $row_meta );
			}

			return (array) $links;
		}

		register_activation_hook( __FILE__, 'activate_upsell_order_bump_offer_for_woocommerce' );
		register_deactivation_hook( __FILE__, 'deactivate_upsell_order_bump_offer_for_woocommerce' );


		/**
		 * Wps_redirect_to_bump_list.
		 *
		 * @since 2.0.0
		 * @return void
		 */
		function wps_redirect_to_bump_list() {
			if ( isset( $_GET['tab'] ) && 'bump-list' == $_GET['tab'] ) {
				wp_redirect( admin_url( 'admin.php?page=upsell-order-bump-offer-for-woocommerce-setting&tab=order-bump-section&sub_tab=pre-list-offer-section' ) );
				exit;
			}
		}
		add_action( 'admin_init', 'wps_redirect_to_bump_list' );

		// Run this function only when the plugin is activated.
		register_activation_hook( __FILE__, 'wps_activate_plugin' );

		/**
		 * Handles plugin activation.
		 *
		 * This function checks if the "Woo One Click Upsell Funnel" plugin exists.
		 * If not, it creates the required plugin folder and file, then activates it.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		function wps_activate_plugin() {
			if ( ! wps_plugin_exists( 'woo-one-click-upsell-funnel/woocommerce-one-click-upsell-funnel.php' ) ) {
				update_option( 'wps_manual_create_upsell', 'done' );
				wps_create_plugin_folder(); // Create the plugin folder and file.
				wps_activate_created_plugin();
			}
		}


		/**
		 * Checks if a plugin exists in the plugins directory.
		 *
		 * @since 1.0.0
		 *
		 * @param string $plugin_path Relative path to the plugin file.
		 * @return bool True if the plugin exists, false otherwise.
		 */
		function wps_plugin_exists( $plugin_path ) {
			return file_exists( WP_PLUGIN_DIR . '/' . $plugin_path );
		}


		/**
		 * Creates the plugin folder and main plugin file if they do not exist.
		 *
		 * This function checks if the "woo-one-click-upsell-funnel" folder exists in
		 * the WordPress plugins directory. If not, it creates the folder and a main
		 * plugin file with basic metadata.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		function wps_create_plugin_folder() {
			$plugin_dir  = WP_PLUGIN_DIR . '/woo-one-click-upsell-funnel/';
			$plugin_file = $plugin_dir . 'woocommerce-one-click-upsell-funnel.php';

			// Initialize WP_Filesystem.
			if ( function_exists( 'WP_Filesystem' ) ) {
				WP_Filesystem();

				global $wp_filesystem;
			// Create the directory if it doesn't exist.
			if ( ! $wp_filesystem->is_dir( $plugin_dir ) ) {
				$wp_filesystem->mkdir( $plugin_dir, 0755 , true );
			}


			// Create the plugin file if it doesn't exist.
			if ( ! file_exists( $plugin_file ) ) {
				$plugin_content = '<?php
				/*
				Plugin Name: Woo One Click Upsell Funnel
				Plugin URI: https://example.com
				Description: Upsell Funnel Builder.
				Version: 4.2.11
				Author: WPSwings
				Author URI: https://wpswings.com/
				*/
				';
				file_put_contents( $plugin_file, $plugin_content );
			}
		}
		}


		add_filter( 'plugin_action_links', 'wps_remove_deactivate_option', 10, 4 );

		/**
		 * Removes the "Deactivate" option for a specific plugin.
		 *
		 * This function prevents users from deactivating the "Woo One Click Upsell Funnel" plugin.
		 *
		 * @since 1.0.0
		 *
		 * @param array  $actions      An array of plugin action links.
		 * @param string $plugin_file  Path to the plugin file relative to the plugins directory.
		 * @param array  $plugin_data  An array of plugin data.
		 * @param string $context      The plugin context.
		 *
		 * @return array Modified plugin action links.
		 */
		function wps_remove_deactivate_option( $actions, $plugin_file, $plugin_data, $context ) {
			$protected_plugin = 'woo-one-click-upsell-funnel/woocommerce-one-click-upsell-funnel.php';

			$already_existed = get_option( 'wps_manual_create_upsell' );
			if ( wps_ubo_lite_is_plugin_active( 'woo-one-click-upsell-funnel/woocommerce-one-click-upsell-funnel.php' ) ) {

				if ( 'done' != $already_existed ) {
					return $actions;
				}
			}

			if ( $plugin_file === $protected_plugin ) {
				unset( $actions['deactivate'] ); // Remove the "Deactivate" button.
			}

			return $actions;
		}


		/**
		 * Activates the created plugin if it exists.
		 *
		 * @since 1.0.0
		 */
		function wps_activate_created_plugin() {
			$plugin_path = 'woo-one-click-upsell-funnel/woocommerce-one-click-upsell-funnel.php';

			if ( wps_plugin_exists( $plugin_path ) ) {
				$active_plugins = get_option( 'active_plugins', array() );
				if ( ! in_array( $plugin_path, $active_plugins, true ) ) {
					$active_plugins[] = $plugin_path;
					update_option( 'active_plugins', $active_plugins );
				}
			}
		}



		/**
		 * The core plugin class that is used to define internationalization,
		 * admin-specific hooks, and public-facing site hooks.
		 */
		require plugin_dir_path( __FILE__ ) . 'includes/class-upsell-order-bump-offer-for-woocommerce.php';

		/**
		 * Begins execution of the plugin.
		 *
		 * Since everything within the plugin is registered via hooks,
		 * then kicking off the plugin from this point in the file does
		 * not affect the page life cycle.
		 *
		 * @since    1.0.0
		 */
		function run_upsell_order_bump_offer_for_woocommerce() {
			$plugin = new Upsell_Order_Bump_Offer_For_Woocommerce();
			$plugin->run();
		}

		run_upsell_order_bump_offer_for_woocommerce();
	} else {

		add_action( 'admin_init', 'wps_ubo_lite_plugin_activation_failure' );

		/**
		 * Deactivate this plugin.
		 *
		 * @since    1.0.0
		 */
		function wps_ubo_lite_plugin_activation_failure() {
			$secure_nonce      = wp_create_nonce( 'wps-upsell-auth-nonce' );
			$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-upsell-auth-nonce' );

			if ( ! $id_nonce_verified ) {
				wp_die( esc_html__( 'Nonce Not verified', 'upsell-order-bump-offer-for-woocommerce' ) );
			}

			// To hide Plugin activated notice.
			if ( ! empty( $_GET['activate'] ) ) {

				unset( $_GET['activate'] );
			}

			deactivate_plugins( plugin_basename( __FILE__ ) );
		}

		// Add admin error notice.
		add_action( 'admin_notices', 'wps_ubo_lite_activation_admin_notice' );

		/**
		 * This function is used to display plugin activation error notice.
		 *
		 * @since    1.0.0
		 */
		function wps_ubo_lite_activation_admin_notice() {
			global $wps_ubo_lite_plugin_activation;

			?>

			<?php if ( 'woo_inactive' === $wps_ubo_lite_plugin_activation['message'] ) : ?>

				<div class="notice notice-error is-dismissible wps-notice">
					<p><strong><?php esc_html_e( 'WooCommerce', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong><?php esc_html_e( ' is not activated, Please activate WooCommerce first to activate ', 'upsell-order-bump-offer-for-woocommerce' ); ?><strong><?php esc_html_e( 'Upsell Order Bump Offer for WooCommerce', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong><?php esc_html_e( '.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
				</div>

				<?php
			endif;
		}
	}


	add_action(
		'init',
		function () {
			add_action(
				'current_screen',
				function ( $screen ) {
					if ( $screen ) {
						$screen_id = $screen->id;
						$sub_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : ''; // Get 'tab' from URL.
						$sub_section = isset( $_GET['section'] ) ? sanitize_text_field( wp_unslash( $_GET['section'] ) ) : ''; // Get 'section' if exists.

						// Store both screen ID and sub-tab in options table.
						update_option(
							'wps_current_screen_data',
							array(
								'screen_id'   => $screen_id,
								'sub_tab'     => $sub_tab,
								'sub_section' => $sub_section,
							)
						);
					}
				}
			);
		}
	);


	add_action( 'admin_notices', 'wps_banner_notification_plugin_html' );
	if ( ! function_exists( 'wps_banner_notification_plugin_html' ) ) {
		/**
		 * Common Function To show banner image.
		 *
		 * @return void
		 */
		function wps_banner_notification_plugin_html() {
			$screen = get_current_screen();
			if ( isset( $screen->id ) ) {
				$pagescreen = $screen->id;
			}
			if ( 'toplevel_page_upsell-order-bump-offer-for-woocommerce-setting' === $pagescreen || 'plugins' === $pagescreen || 'order-bump_page_upsell-order-bump-offer-for-woocommerce-reporting' == $pagescreen ) {
				$banner_id = get_option( 'wps_wgm_notify_new_banner_id', false );
				if ( isset( $banner_id ) && '' !== $banner_id ) {
					$hidden_banner_id            = get_option( 'wps_wgm_notify_hide_baneer_notification', false );
					$banner_image = get_option( 'wps_wgm_notify_new_banner_image', '' );

					$banner_url = get_option( 'wps_wgm_notify_new_banner_url', '' );
					if ( isset( $hidden_banner_id ) && $hidden_banner_id < $banner_id ) {

						if ( '' !== $banner_image && '' !== $banner_url ) {

							?>
							<div class="wps-offer-notice wps-notice notice notice-warning is-dismissible">
								<div class="notice-container">
									<a href="<?php echo esc_url( $banner_url ); ?>" target="_blank"><img src="<?php echo esc_url( $banner_image ); ?>" alt="Subscription cards" /></a>
								</div>
								<button type="button" class="notice-dismiss dismiss_banner" id="dismiss-banner"><span class="screen-reader-text">Dismiss this notice.</span></button>
							</div>

							<?php
						}
					}
				}
			}
		}
	}


	/**
	 * This function is used to check hpos enable.
	 *
	 * @return boolean
	 */
	if ( ! function_exists( 'wps_wocfo_is_hpos_enabled_funnel_builder' ) ) {
		/**
		 * Wwps_wocfo_is_hpos_enabled_funnel_builder.
		 *
		 * @since 2.0.0
		 * @return string
		 */
		function wps_wocfo_is_hpos_enabled_funnel_builder() {
			$is_hpos_enable = false;
			if ( class_exists( 'Automattic\WooCommerce\Utilities\OrderUtil' ) && OrderUtil::custom_orders_table_usage_is_enabled() ) {

				$is_hpos_enable = true;
			}
			return $is_hpos_enable;
		}
	}


	add_filter(
		'admin_menu',
		function () {
			global $menu;

			// Loop through the menu and find the target menu item by its slug.
			foreach ( $menu as &$item ) {
				if ( 'upsell-order-bump-offer-for-woocommerce-setting' == $item[2] ) {
					// Add your custom class here.
					$item[4] .= ' wps-ufbo-menu-custom-class';
				}
			}
		}
	);


	/**
	 * This function is used to update meta data.
	 *
	 * @param string $id id.
	 * @param string $meta_key meta_key.
	 * @param string $meta_value meta_value.
	 * @return void
	 */
	if ( ! function_exists( 'wps_wocfo_hpos_update_meta_data_funnel_builder' ) ) {

		/**
		 * Wps_wocfo_hpos_update_meta_data_funnel_builder.
		 *
		 * @since 2.0.0
		 * @param int $id The ID of the product in the offer.
		 * @param int $meta_key The ID of the product in the offer.
		 * @param int $meta_value The ID of the product in the offer.
		 * @return void
		 */
		function wps_wocfo_hpos_update_meta_data_funnel_builder( $id, $meta_key, $meta_value ) {

			if ( 'shop_order' === OrderUtil::get_order_type( $id ) && wps_wocfo_is_hpos_enabled_funnel_builder() ) {

				$order = wc_get_order( $id );
				$order->update_meta_data( $meta_key, $meta_value );
				$order->save();
			} else {

				update_post_meta( $id, $meta_key, $meta_value );
			}
		}
	}


	/**
	 * This function is used delete meta data.
	 *
	 * @param string $id       id.
	 * @param string $meta_key meta_key.
	 * @return void
	 */
	if ( ! function_exists( 'wps_wocfo_hpos_delete_meta_data_funnel_builder' ) ) {

		/**
		 * Wps_wocfo_hpos_delete_meta_data_funnel_builder.
		 *
		 * @since 2.0.0
		 * @param int $id The ID of the product in the offer.
		 * @param int $meta_key The ID of the product in the offer.
		 * @return void
		 */
		function wps_wocfo_hpos_delete_meta_data_funnel_builder( $id, $meta_key ) {

			if ( 'shop_order' === OrderUtil::get_order_type( $id ) && wps_wocfo_is_hpos_enabled_funnel_builder() ) {

				$order = wc_get_order( $id );
				$order->delete_meta_data( $meta_key );
				$order->save();
			} else {

				delete_post_meta( $id, $meta_key );
			}
		}
	}



	/**
	 * This function is used to get post meta data.
	 *
	 * @param  string $id        id.
	 * @param  string $meta_key  meta key.
	 * @param  bool   $bool meta bool.
	 * @return string
	 */
	if ( ! function_exists( 'wps_wocfo_hpos_get_meta_data_funnel_builder' ) ) {
		/**
		 * Wwps_wocfo_hpos_get_meta_data_funnel_builder.
		 *
		 * @since 2.0.0
		 * @param int $id The ID of the product in the offer.
		 * @param int $meta_key The ID of the product in the offer.
		 * @param int $bool The ID of the product in the offer.
		 * @return string
		 */
		function wps_wocfo_hpos_get_meta_data_funnel_builder( $id, $meta_key, $bool ) {

			$meta_value = '';
			if ( 'shop_order' === OrderUtil::get_order_type( $id ) && wps_wocfo_is_hpos_enabled_funnel_builder() ) {

				$order      = wc_get_order( $id );
				$meta_value = $order->get_meta( $meta_key, $bool );
			} else {

				$meta_value = get_post_meta( $id, $meta_key, $bool );
			}
			return $meta_value;
		}
	}


	add_action( 'admin_notices', 'wps_ubo_banner_notification_html' );
	/**
	 * Function to show banner image based on subscription.
	 *
	 * @return void
	 */
	function wps_ubo_banner_notification_html() {
		$secure_nonce      = wp_create_nonce( 'wps-upsell-auth-nonce' );
		$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-upsell-auth-nonce' );

		if ( ! $id_nonce_verified ) {
			wp_die( esc_html__( 'Nonce Not verified', 'upsell-order-bump-offer-for-woocommerce' ) );
		}

		$screen = get_current_screen();
		if ( isset( $screen->id ) ) {
			$pagescreen = $screen->id;
		}
		if ( ( isset( $_GET['page'] ) && 'toplevel_page_upsell-order-bump-offer-for-woocommerce-setting' === $_GET['page'] ) ) {
			$banner_id = get_option( 'wps_wgm_notify_new_banner_id', false );
			if ( isset( $banner_id ) && '' !== $banner_id ) {
				$hidden_banner_id            = get_option( 'wps_wgm_notify_hide_baneer_notification', false );
				$banner_image = get_option( 'wps_wgm_notify_new_banner_image', '' );
				$banner_url = get_option( 'wps_wgm_notify_new_banner_url', '' );
				if ( isset( $hidden_banner_id ) && $hidden_banner_id < $banner_id ) {

					if ( '' !== $banner_image && '' !== $banner_url ) {

						?>
						<div class="wps-offer-notice notice notice-warning is-dismissible">
							<div class="notice-container">
								<a href="<?php echo esc_url( $banner_url ); ?>" target="_blank"><img src="<?php echo esc_url( $banner_image ); ?>" alt="Subscription cards" /></a>
							</div>
							<button type="button" class="notice-dismiss dismiss_banner" id="dismiss-banner"><span class="screen-reader-text">Dismiss this notice.</span></button>
						</div>

						<?php
					}
				}
			}
		}
	}
}
