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
 * Requires at least:       6.7.0
 * Tested up to:            6.8.3
 * WC requires at least:    6.5.0
 * WC tested up to:         10.2.2
 *
 * Requires Plugins: woocommerce
 * Version:           3.0.9
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
// To Suppress The Notices on text doman.
add_filter( 'doing_it_wrong_trigger_error', '__return_false' );

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

			// Check regular activation.
			if ( in_array( $plugin_slug, $active_plugins, true ) ) {
				return true;
			}

			// Check multisite network activation.
			if ( is_multisite() ) {
				$network_plugins = (array) get_site_option( 'active_sitewide_plugins', array() );
				if ( isset( $network_plugins[ $plugin_slug ] ) ) {
					return true;
				}
			}

			return false;
		}
	}

	/**
	 * Currently plugin version.
	 */
	define( 'UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_VERSION', '3.0.9' );
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
		ob_start();
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-upsell-order-bump-offer-for-woocommerce-activator.php';
		Upsell_Order_Bump_Offer_For_Woocommerce_Activator::activate();
		ob_end_clean();
	}


	/**
	 * The file responsible for Upsell Sales by Funnel - Data handling and Stats.
	 */
		require_once plugin_dir_path( __FILE__ ) . 'reporting/class-wps-upsell-report-sales-by-funnel-bump.php';


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
			 ob_start();
			if ( ! wps_plugin_exists( 'woo-one-click-upsell-funnel/woocommerce-one-click-upsell-funnel.php' ) ) {
				update_option( 'wps_manual_create_upsell', 'done' );
				wps_create_plugin_folder(); // Create the plugin folder and file.
				wps_activate_created_plugin();
			}
			ob_end_clean(); // Discard buffer content.
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
					$wp_filesystem->mkdir( $plugin_dir, 0755, true );
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
		 * This function is used to check PAR pro plugin is active or not.
		 *
		 * @return bool
		 */
		function wps_upsell_funnel_builder_is_pdf_pro_plugin_active() {

			$flag = false;
			if ( is_plugin_active( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php' ) ) {

				$flag = true;
			}
			return $flag;
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
			if ( 'toplevel_page_upsell-order-bump-offer-for-woocommerce-setting' === $pagescreen || 'plugins' === $pagescreen || 'order-bump_page_upsell-order-bump-offer-for-woocommerce-reporting' == $pagescreen || 'dashboard' == $pagescreen ) {
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


/**
 * ======================================================
 * WooCommerce Dynamic Discount Rules (with Select2)
 * ======================================================
 * Conditions supported:
 * - Cart Total
 * - Subtotal
 * - Payment Method (Select2 Multi)
 * - Coupon Applied (Select2 Multi)
 * ======================================================
 */

// --- 1. ADMIN PAGE ---
// Enqueue Select2 and styles globally
// add_action('wp_enqueue_scripts', function () {
// wp_enqueue_style('select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
// wp_enqueue_script('select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['jquery'], null, true);
// });

// // Enqueue for admin as well
// add_action('admin_enqueue_scripts', function () {
// wp_enqueue_style('select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
// wp_enqueue_script('select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['jquery'], null, true);
// });

/**
 * Display Dynamic Discount Conditions Popup Modal
 *
 * @param array $args Configuration array
 *  - 'modal_id' => unique ID for the modal (default: 'wc-discount-popup')
 *  - 'button_id' => ID of trigger button (default: 'show-discount-conditions')
 *  - 'callback' => callback function to execute on save
 *  - 'rules' => existing rules (default: get from option)
 */
function wc_render_discount_conditions_popup( $wps_funnel_type = '', $bump_id = '', $args = array() ) {
	$defaults = array(
		'modal_id'   => 'wc-discount-popup',
		'button_id'  => 'show-discount-conditions',
		'callback'   => null,
		'rules'      => get_option( 'wc_dynamic_discount_rules', array() ),
		'discount_amount' => get_option( 'wc_dynamic_discount_amount', 0 ),
	);
	$args = wp_parse_args( $args, $defaults );

	// Get rules for this funnel type and bump ID
	$rules = array();
	if ( ! empty( $wps_funnel_type ) && ! empty( $bump_id ) && isset( $args['rules'][$wps_funnel_type][$bump_id] ) ) {
		$rules = $args['rules'][$wps_funnel_type][$bump_id];
	}

	$coupons = get_posts(
		array(
			'post_type' => 'shop_coupon',
			'posts_per_page' => -1,
		)
	);
	?>

	<style>
		.wc-discount-modal {
			display: none !important;
			position: fixed !important;
			z-index: 999999 !important;
			left: 0 !important;
			top: 0 !important;
			width: 100% !important;
			height: 100% !important;
			background-color: rgba(0, 0, 0, 0.5) !important;
			margin: 0 !important;
			padding: 0 !important;
			border: none !important;
		}
		.wc-discount-modal.show {
			display: flex !important;
			align-items: center !important;
			justify-content: center !important;
		}
		.wc-discount-modal-content {
			background-color: #fefefe !important;
			padding: 30px !important;
			border: 1px solid #888 !important;
			border-radius: 8px !important;
			width: 90% !important;
			max-width: 900px !important;
			max-height: 80vh !important;
			overflow-y: auto !important;
			box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
			position: relative !important;
			margin: auto !important;
		}
		.wc-discount-modal-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 20px;
			border-bottom: 1px solid #ddd;
			padding-bottom: 10px;
		}
		.wc-discount-modal-header h2 {
			margin: 0;
			font-size: 24px;
		}
		.wc-discount-close {
			font-size: 28px;
			font-weight: bold;
			color: #aaa;
			cursor: pointer;
			border: none;
			background: none;
			padding: 0;
			line-height: 1;
		}
		.wc-discount-close:hover {
			color: #000;
		}
		.wc-discount-form {
			margin: 20px 0;
		}
		.wc-discount-rules-table {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 20px;
		}
		.wc-discount-rules-table thead {
			background-color: #f5f5f5;
		}
		.wc-discount-rules-table th,
		.wc-discount-rules-table td {
			border: 1px solid #ddd;
			padding: 12px;
			text-align: left;
		}
		.wc-discount-rules-table th {
			font-weight: 600;
			background-color: #f9f9f9;
		}
		.wc-discount-rules-table button {
			margin: 0;
		}
		.wc-discount-form-group {
			margin-bottom: 20px;
		}
		.wc-discount-form-group label {
			display: block;
			margin-bottom: 8px;
			font-weight: 600;
		}
		.wc-discount-form-group input[type="number"] {
			width: 200px;
			padding: 8px;
			border: 1px solid #ddd;
			border-radius: 4px;
		}
		.wc-discount-modal-footer {
			display: flex;
			gap: 10px;
			justify-content: flex-end;
			border-top: 1px solid #ddd;
			padding-top: 20px;
			margin-top: 20px;
		}
		.wc-discount-modal-footer button {
			padding: 10px 20px;
			font-size: 14px;
			cursor: pointer;
			border-radius: 4px;
			border: 1px solid #ddd;
		}
		.wc-discount-modal-footer .btn-primary {
			background-color: #0073aa;
			color: white;
			border-color: #0073aa;
		}
		/* .wc-discount-modal-footer .btn-primary:hover {
			background-color: #4a6cacff;
		} */
		.wc-discount-modal-footer .btn-secondary {
			background-color: #f3f3f3;
			color: #333;
		}
		/* .wc-discount-modal-footer .btn-secondary:hover {
			background-color: #e0e0e0;
		} */
		.wc-discount-add-btn {
			margin-bottom: 15px;
		}
		.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
			color: white !important;
			margin-right: 4px !important;
		}
		.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
			color: #fff !important;
		}
		.select2-dropdown {
			border: 1px solid #ddd !important;
			border-radius: 4px !important;
		}
		.select2-results__option {
			padding: 8px 12px !important;
		}
		.select2-results__option--highlighted {
			background-color: #0073aa !important;
		}
		.select2-container--default .select2-selection--single {
			border: 1px solid #ddd !important;
			border-radius: 4px !important;
			height: 38px !important;
			padding: 0 !important;
		}
		.select2-container--default .select2-selection--single .select2-selection__rendered {
			padding: 8px 12px !important;
			line-height: 22px !important;
		}
	</style>

	<!-- Modal -->
	<div id="<?php echo esc_attr( $args['modal_id'] ); ?>" class="wc-discount-modal">
		<div class="wc-discount-modal-content">
			<div class="wc-discount-modal-header">
				<h2>Visibility Conditions</h2>
				<button type="button" class="wc-discount-close">&times;</button>
			</div>

			<form method="post" class="wc-discount-form" id="wc-discount-condition-form">
				<?php wp_nonce_field( 'wc_save_dynamic_rules', 'wc_dynamic_rules_nonce' ); ?>

				<table class="wc-discount-rules-table" id="dynamic-rules-table">
					<thead>
						<tr>
							<th>Condition Field</th>
							<th>Operator</th>
							<th>Value</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php if ( ! empty( $rules ) ) : ?>
							<?php
							foreach ( $rules as $index => $rule ) :
								$field    = esc_attr( $rule['field'] );
								$operator = esc_attr( $rule['operator'] );
								$value    = is_array( $rule['value'] ) ? $rule['value'] : array( $rule['value'] );
								?>
								<tr>
									<td>
										<select class="rule-field" name="rules[<?php echo $index; ?>][field]">
											<option value="cart_total" <?php selected( $field, 'cart_total' ); ?>>Cart Total</option>
											<option value="subtotal" <?php selected( $field, 'subtotal' ); ?>>Subtotal</option>
											<option value="coupon_applied" <?php selected( $field, 'coupon_applied' ); ?>>Coupon Applied</option>
											<option value="user_status" <?php selected( $field, 'user_status' ); ?>>User Login Status</option>
											<option value="user_registered" <?php selected( $field, 'user_registered' ); ?>>Specific Registered User</option>
										</select>
									</td>
									<td>
										<select class="rule-operator" name="rules[<?php echo $index; ?>][operator]">
											<option value="greater_than" <?php selected( $operator, 'greater_than' ); ?>>Greater Than</option>
											<option value="less_than" <?php selected( $operator, 'less_than' ); ?>>Less Than</option>
											<option value="is" <?php selected( $operator, 'is' ); ?>>Is</option>
											<option value="is_not" <?php selected( $operator, 'is_not' ); ?>>Is Not</option>
										</select>
									</td>
									<td class="value-cell">
										<?php echo wc_render_value_input( $field, $index, $value, $coupons ); ?>
									</td>
									<td><button type="button" class="button remove-row">Remove</button></td>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<tr>
								<td>
									<select class="rule-field" name="rules[0][field]">
										<option value="cart_total">Cart Total</option>
										<option value="subtotal">Subtotal</option>
										<option value="coupon_applied">Coupon Applied</option>
										<option value="user_status">User Login Status</option>
										<option value="user_registered">Specific Registered User</option>
									</select>
								</td>
								<td>
									<select class="rule-operator" name="rules[0][operator]">
										<option value="greater_than">Greater Than</option>
										<option value="less_than">Less Than</option>
										<option value="is">Is</option>
										<option value="is_not">Is Not</option>
									</select>
								</td>
								<td class="value-cell"><input type="text" name="rules[0][value][]" value="" /></td>
								<td><button type="button" class="button remove-row">Remove</button></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>

				<p class="wc-discount-add-btn"><button type="button" id="add-rule" class="button button-primary">+ Add Condition</button></p>
			</form>

			<div class="wc-discount-modal-footer">
				<button type="button" class="button btn-secondary wc-discount-cancel">Cancel</button>
				<button type="button" class="button btn-primary wc-discount-save">Save Conditions</button>
			</div>
		</div>
	</div>

	<script type="text/javascript">
	jQuery(document).ready(function ($) {
		
		const modal_id = '<?php echo esc_js( $args['modal_id'] ); ?>';
		const $modal = $('#' + modal_id);
		const $form = $modal.find('#wc-discount-condition-form');

		function init_select2() {
			$modal.find('.select2-field').select2({
				width: '100%',
				placeholder: 'Select value(s)',
				allowClear: true,
				dropdownParent: $modal
			});
		}

		// Initialize
		init_select2();
		let row_index = $modal.find('#dynamic-rules-table tbody tr').length;

		// Open/Close Modal
		$('.wc-discount-close, .wc-discount-cancel').on('click', function () {
			$modal.removeClass('show');
		});

		$(window).on('click', function (event) {
			if (event.target === $modal[0]) {
				$modal.removeClass('show');
			}
		});

		// Add new condition
		$modal.find('#add-rule').on('click', function () {
			const new_row = `
				<tr>
					<td>
						<select class="rule-field" name="rules[${row_index}][field]">
							<option value="cart_total">Cart Total</option>
							<option value="subtotal">Subtotal</option>
							<option value="coupon_applied">Coupon Applied</option>
							<option value="user_status">User Login Status</option>
							<option value="user_registered">Specific Registered User</option>
						</select>
					</td>
					<td>
						<select class="rule-operator" name="rules[${row_index}][operator]">
							<option value="greater_than">Greater Than</option>
							<option value="less_than">Less Than</option>
							<option value="is">Is</option>
							<option value="is_not">Is Not</option>
						</select>
					</td>
					<td class="value-cell"><input type="text" name="rules[${row_index}][value][]" value="" /></td>
					<td><button type="button" class="button remove-row">Remove</button></td>
				</tr>`;
			$modal.find('#dynamic-rules-table tbody').append(new_row);
			
			// Get the newly added row and render the correct value input
			const $new_row = $modal.find('#dynamic-rules-table tbody tr').last();
			const field = $new_row.find('.rule-field').val();
			const $td = $new_row.find('.value-cell');
			
			$.ajax({
				url: ajaxurl,
				method: 'POST',
				data: { action: 'wc_get_dynamic_value_field', field, row_idx: row_index },
				success: function (html) {
					$td.html(html);
					init_select2();
					filter_operators($new_row);
				}
			});
			
			row_index++;
		});

		// Remove condition
		$modal.on('click', '.remove-row', function () {
			$(this).closest('tr').remove();
		});

		// Filter operators
		function filter_operators($row) {
			const field = $row.find('.rule-field').val();
			const $operator = $row.find('.rule-operator');
			$operator.find('option').show();

			if (['coupon_applied', 'user_status', 'user_registered'].includes(field)) {
				$operator.find('option[value="greater_than"], option[value="less_than"]').hide();
				if (['greater_than', 'less_than'].includes($operator.val())) {
					$operator.val('is');
				}
			}
		}

		$modal.on('change', '.rule-field', function () {
			const $row = $(this).closest('tr');
			const $td = $row.find('.value-cell');
			const field = $(this).val();
			const row_idx = $(this).attr('name').match(/\d+/)[0];
			filter_operators($row);

			$.ajax({
				url: ajaxurl,
				method: 'POST',
				data: { action: 'wc_get_dynamic_value_field', field, row_idx },
				success: function (html) {
					$td.html(html);
					init_select2();
				}
			});
		});

		$modal.find('#dynamic-rules-table tbody tr').each(function () {
			filter_operators($(this));
		});

		// Save conditions
		$modal.find('.wc-discount-save').on('click', function () {
			const form_data = $form.serialize();

			const bump_id_one = new URLSearchParams(window.location.search).get('bump_id');
			const funnel_id_one = new URLSearchParams(window.location.search).get('funnel_id');
			let bump_id = '';

			if (bump_id_one) {
			bump_id = bump_id_one;
			} else if (funnel_id_one) {
			bump_id = funnel_id_one;
			}


			const funnel_type = $('#wps_funnel_type').val();
			
			if ( !bump_id || !funnel_type ) {
				alert('Missing bump_id or funnel_type');
				return;
			}

			$.ajax({
				url: ajaxurl,
				method: 'POST',
				data: form_data + '&action=wc_save_dynamic_discount_rules&bump_id=' + encodeURIComponent(bump_id) + '&funnel_type=' + encodeURIComponent(funnel_type),
				
				success: function (response) {
					alert('Conditions saved successfully!');
					$modal.removeClass('show');
					// Trigger custom event or callback
					$(document).trigger('wc_discount_conditions_saved', response);
				},
				error: function () {
					alert('Error saving conditions. Please try again.');
				}
			});
		});
	});
	</script>
	<?php
}

// AJAX: Save conditions
add_action(
	'wp_ajax_wc_save_dynamic_discount_rules',
	function () {
		if ( ! isset( $_POST['wc_dynamic_rules_nonce'] ) || ! wp_verify_nonce( $_POST['wc_dynamic_rules_nonce'], 'wc_save_dynamic_rules' ) ) {
			wp_send_json_error( 'Nonce verification failed' );
		}

		// Get funnel type and bump ID first
		$funnel_type = isset( $_POST['funnel_type'] ) ? sanitize_text_field( $_POST['funnel_type'] ) : '';
		$bump_id = isset( $_POST['bump_id'] ) ? sanitize_text_field( $_POST['bump_id'] ) : '';

		// Debug logging
		error_log( 'Funnel Type: ' . $funnel_type );
		error_log( 'Bump ID: ' . $bump_id );
		error_log( 'Rules POST: ' . print_r( $_POST['rules'] ?? array(), true ) );

		// Validate that we have both funnel_type and bump_id
		if ( empty( $funnel_type ) || empty( $bump_id ) ) {
			wp_send_json_error( array( 'message' => 'Funnel type or bump ID is missing', 'funnel_type' => $funnel_type, 'bump_id' => $bump_id ) );
		}

		$rules = array();
		if ( ! empty( $_POST['rules'] ) ) {
			foreach ( $_POST['rules'] as $r ) {
				// Skip rules with empty field or operator
				if ( empty( $r['field'] ) || empty( $r['operator'] ) ) {
					continue;
				}

				// Skip rules with empty value
				$value = array_filter( array_map( 'sanitize_text_field', (array) $r['value'] ) );
				if ( empty( $value ) ) {
					continue;
				}

				$rules[] = array(
					'field'    => sanitize_text_field( $r['field'] ),
					'operator' => sanitize_text_field( $r['operator'] ),
					'value'    => $value,
				);
			}
		}

		error_log( 'Processed Rules: ' . print_r( $rules, true ) );

		// Get existing rules
		$wc_dynamic_discount_rules = get_option( 'wc_dynamic_discount_rules', array() );

		// Check if funnel type exists
		if ( ! isset( $wc_dynamic_discount_rules[$funnel_type] ) ) {
			$wc_dynamic_discount_rules[$funnel_type] = array();
		}

		// Replace rules for this funnel_type and bump_id
		$wc_dynamic_discount_rules[$funnel_type][$bump_id] = $rules;

		error_log( 'Final Data to Save: ' . print_r( $wc_dynamic_discount_rules, true ) );

		// Update options with all funnel types, bump IDs and their rules
		$updated = update_option( 'wc_dynamic_discount_rules', $wc_dynamic_discount_rules );
		update_option( 'wc_dynamic_discount_amount', floatval( $_POST['discount_amount'] ?? 0 ) );

		error_log( 'Update Result: ' . ( $updated ? 'true' : 'false' ) );

		wp_send_json_success( array( 'message' => 'Rules saved successfully', 'rules' => $rules, 'funnel_type' => $funnel_type, 'bump_id' => $bump_id ) );
	}
);

// --- RENDER VALUE INPUT ---
function wc_render_value_input( $field, $index, $values, $coupons ) {
	ob_start();
	switch ( $field ) {
		case 'coupon_applied':
			echo '<select multiple class="select2-field" name="rules[' . $index . '][value][]">';
			foreach ( $coupons as $coupon ) {
				$coupon_code = $coupon->post_name;
				$selected = in_array( $coupon_code, $values ) ? 'selected' : '';
				echo '<option value="' . esc_attr( $coupon_code ) . '" ' . $selected . '>' . esc_html( $coupon->post_title ) . '</option>';
			}
			echo '</select>';
			break;

		case 'user_status':
			echo '<select class="select2-field" name="rules[' . $index . '][value][]">';
			$statuses = array(
				'logged_in' => 'Logged In User',
				'guest' => 'Guest User',
			);
			foreach ( $statuses as $key => $label ) {
				$selected = in_array( $key, $values ) ? 'selected' : '';
				echo '<option value="' . esc_attr( $key ) . '" ' . $selected . '>' . esc_html( $label ) . '</option>';
			}
			echo '</select>';
			break;

		case 'user_registered':
			echo '<select multiple class="select2-field" name="rules[' . $index . '][value][]">';
			$users = get_users( array( 'fields' => array( 'ID', 'display_name', 'user_login' ) ) );
			foreach ( $users as $user ) {
				$selected = in_array( (string) $user->ID, $values ) ? 'selected' : '';
				echo '<option value="' . esc_attr( $user->ID ) . '" ' . $selected . '>'
					 . esc_html( $user->display_name . ' (' . $user->user_login . ')' )
					 . '</option>';
			}
			echo '</select>';
			break;

		default:
			echo '<input type="text" name="rules[' . $index . '][value][]" value="' . esc_attr( implode( ',', $values ) ) . '" />';
	}
	return ob_get_clean();
}

// --- AJAX dynamic field rendering ---
add_action(
	'wp_ajax_wc_get_dynamic_value_field',
	function () {
		$field = sanitize_text_field( $_POST['field'] ?? '' );
		$row_idx = sanitize_text_field( $_POST['row_idx'] ?? 0 );
		$coupons = get_posts(
			array(
				'post_type' => 'shop_coupon',
				'posts_per_page' => -1,
			)
		);
		echo wc_render_value_input( $field, $row_idx, array(), $coupons );
		wp_die();
	}
);

// --- EXISTING CONDITION CHECK FUNCTIONS ---
function wc_dynamic_discount_conditions_pass( $funnel_type = '', $bump_id = '' ) {
	$all_rules = get_option( 'wc_dynamic_discount_rules', array() );
	
	// If funnel type and bump ID provided, get specific rules
	if ( ! empty( $funnel_type ) && ! empty( $bump_id ) && isset( $all_rules[$funnel_type][$bump_id] ) ) {
		$rules = $all_rules[$funnel_type][$bump_id];
	} else {
		return false;
	}

	if ( empty( $rules ) ) {
		return false;
	}

	$cart = WC()->cart;
	if ( ! $cart || $cart->get_cart_contents_count() === 0 ) {
		$response = wp_remote_get( home_url( '/?rest_route=/wc/store/v1/cart' ) );
		if ( is_wp_error( $response ) ) {
			return false;
		}
		$cart_data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( empty( $cart_data ) ) {
			return false;
		}
		foreach ( $rules as $rule ) {
			if ( ! wc_evaluate_rule_condition_block( $rule, $cart_data ) ) {
				return false;
			}
		}
		return true;
	}

	foreach ( $rules as $rule ) {
		if ( ! wc_evaluate_rule_condition( $rule, $cart ) ) {
			return false;
		}
	}
	return true;
}

function wc_evaluate_rule_condition( $rule, $cart ) {
	$field = $rule['field'];
	$operator = $rule['operator'];
	$values = $rule['value'];

	switch ( $field ) {
		case 'cart_total':
			$target = floatval( $cart->get_cart_contents_total() + $cart->get_shipping_total() + $cart->get_taxes_total() );
			break;
		case 'subtotal':
			$target = floatval( $cart->get_subtotal() );
			break;
		case 'coupon_applied':
			$target = $cart->get_applied_coupons();
			break;
		case 'user_status':
			$target = is_user_logged_in() ? 'logged_in' : 'guest';
			break;
		case 'user_registered':
			if ( is_user_logged_in() ) {
				$user = wp_get_current_user();
				$target = (string) $user->ID;
			} else {
				$target = '';
			}
			break;
		default:
			return false;
	}

	return wc_compare_rule_value( $field, $operator, $target, $values );
}

function wc_evaluate_rule_condition_block( $rule, $cart_data ) {
	$field = $rule['field'];
	$operator = $rule['operator'];
	$values = $rule['value'];

	switch ( $field ) {
		case 'cart_total':
			$target = floatval( $cart_data['totals']['total_price'] ?? 0 );
			break;
		case 'subtotal':
			$target = floatval( $cart_data['totals']['subtotal'] ?? 0 );
			break;
		case 'coupon_applied':
			$target = wp_list_pluck( $cart_data['coupons'] ?? array(), 'code' );
			break;
		case 'user_status':
			$target = is_user_logged_in() ? 'logged_in' : 'guest';
			break;
		case 'user_registered':
			if ( is_user_logged_in() ) {
				$user = wp_get_current_user();
				$target = (string) $user->ID;
			} else {
				$target = '';
			}
			break;
		default:
			return false;
	}

	return wc_compare_rule_value( $field, $operator, $target, $values );
}

function wc_compare_rule_value( $field, $operator, $target, $values ) {
	switch ( $operator ) {
		case 'greater_than':
			return floatval( $target ) > floatval( $values[0] );
		case 'less_than':
			return floatval( $target ) < floatval( $values[0] );
		case 'is':
			$target = (array) $target;
			$values = (array) $values;
			$target = array_map( 'strtolower', $target );
			$values = array_map( 'strtolower', $values );
			return count( array_intersect( $target, $values ) ) > 0;
		case 'is_not':
			$target = (array) $target;
			$values = (array) $values;
			$target = array_map( 'strtolower', $target );
			$values = array_map( 'strtolower', $values );
			return count( array_intersect( $target, $values ) ) == 0;
		default:
			return false;
	}
}
