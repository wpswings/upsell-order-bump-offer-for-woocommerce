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
 * Plugin Name:       Upsell Order Bump Offer for WooCommerce
 * Plugin URI:        https://wordpress.org/plugins/upsell-order-bump-offer-for-woocommerce/
 * Description:       <code><strong>Upsell Order Bump Offer for WooCommerce</strong></code> makes special offers on checkout page, enabling to increase conversions & AOV in just a single click. <a target="_blank" href="https://wpswings.com/woocommerce-plugins/?utm_source=wpswings-orderbump-shop&utm_medium=orderbump-pro-backend&utm_campaign=shop-page" >Elevate your eCommerce store by exploring more on <strong>WP Swings</strong></a>.
 *
 * Requires at least:       4.4
 * Tested up to:            5.9.3
 * WC requires at least:    3.0.0
 * WC tested up to:         6.4.1
 *
 * Version:           2.1.3
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
 * Currently plugin version.
 */
define( 'UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_VERSION', '2.1.3' );

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
		$update_file = plugin_dir_path( dirname( __FILE__ ) ) . 'upsell-order-bump-offer-for-woocommerce-pro/class-mwb-upsell-bump-update.php';

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
	if ( ! wps_ubo_lite_is_plugin_active( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php' ) ) {

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
				'<a href="' . admin_url( 'admin.php?page=upsell-order-bump-offer-for-woocommerce-setting' ) .
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
				'support' => '<a href="https://wpswings.com/submit-query/?utm_source=wpswings-orderbump-support&utm_medium=orderbump-org-backend&utm_campaign=support" target="_blank"><img src="' . esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL ) . 'admin/resources/icons/Support.svg" class="wps-info-img" alt="DeSupportmo image">' . esc_html__( 'Support', 'upsell-order-bump-offer-for-woocommerce' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}

	register_activation_hook( __FILE__, 'activate_upsell_order_bump_offer_for_woocommerce' );
	register_deactivation_hook( __FILE__, 'deactivate_upsell_order_bump_offer_for_woocommerce' );

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
