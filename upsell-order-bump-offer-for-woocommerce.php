<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://makewebbetter.com/
 * @since             1.0.0
 * @package           Upsell_Order_Bump_Offer_For_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Upsell Order Bump Offer for WooCommerce
 * Plugin URI:        https://wordpress.org/plugins/upsell-order-bump-offer-for-woocommerce/
 * Description:       Show exclusive order bump offers on the checkout page to your customers. Offers that are relevant and benefits your customers on the existing purchase and so increase Average Order Value and your Revenue. <a target="_blank" href="https://makewebbetter.com/wordpress-plugins/?utm_source=MWB-orderbump-home&utm_medium=MWB-home-page&utm_campaign=MWB-orderbump-home" >Elevate your e-commerce store by exploring more on <strong>MakeWebBetter</strong></a>.
 *
 * Requires at least:       4.4
 * Tested up to:            5.5.1
 * WC requires at least:    3.0
 * WC tested up to:         4.5.2
 *
 * Version:           1.4.0
 * Author:            MakeWebBetter
 * Author URI:        https://makewebbetter.com/
 * License:           GPL-3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       upsell-order-bump-offer-for-woocommerce
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Plugin Active Detection.
 *
 * @since    1.0.0
 * @param    string $plugin_slug index file of plugin.
 */
function mwb_ubo_lite_is_plugin_active( $plugin_slug = '' ) {

	if ( empty( $plugin_slug ) ) {

		return false;
	}

	$active_plugins = (array) get_option( 'active_plugins', array() );

	if ( is_multisite() ) {

		$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );

	}

	return in_array( $plugin_slug, $active_plugins ) || array_key_exists( $plugin_slug, $active_plugins );

}

/**
 * Currently plugin version.
 */
define( 'UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_VERSION', '1.4.0' );

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
function mwb_ubo_lite_plugin_activation() {

	$activation['status'] = true;
	$activation['message'] = '';

	// Dependant plugin.
	if ( ! mwb_ubo_lite_is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

		$activation['status'] = false;
		$activation['message'] = 'woo_inactive';

	}

	return $activation;
}

$mwb_ubo_lite_plugin_activation = mwb_ubo_lite_plugin_activation();

if ( true === $mwb_ubo_lite_plugin_activation['status'] ) {

	// Define all the neccessary details.
	define( 'UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL', plugin_dir_url( __FILE__ ) );

	define( 'UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_DIR_PATH', plugin_dir_path( __FILE__ ) );

	// If pro version is inactive add setings link to org version.
	if ( ! mwb_ubo_lite_is_plugin_active( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php' ) ) {

		// Add settings links.
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'mwb_ubo_lite_plugin_action_links' );

		/**
		 * Add Settings link if premium version is not available.
		 *
		 * @since    1.0.0
		 * @param    string $links link to admin arena of plugin.
		 */
		function mwb_ubo_lite_plugin_action_links( $links ) {

			$plugin_links = array(
				'<a href="' . admin_url( 'admin.php?page=upsell-order-bump-offer-for-woocommerce-setting' ) .
									'">' . esc_html__( 'Settings', 'upsell-order-bump-offer-for-woocommerce' ) . '</a>',
				'<a class="mwb-ubo-lite-go-pro" style="background: #05d5d8; color: white; font-weight: 700; padding: 2px 5px; border: 1px solid #05d5d8; border-radius: 5px;" href="https://makewebbetter.com/product/woocommerce-upsell-order-bump-offer-pro/?utm_source=MWB-orderbump-home&utm_medium=MWB-home-page&utm_campaign=MWB-orderbump-home" target="_blank">' . esc_html__( 'GO PRO', 'upsell-order-bump-offer-for-woocommerce' ) . '</a>',
			);

			return array_merge( $plugin_links, $links );
		}
	}

	add_filter( 'plugin_row_meta', 'mwb_ubo_lite_add_important_links', 10, 2 );

	/**
	 * Add custom links for getting premium version.
	 *
	 * @param   string $links link to index file of plugin.
	 * @param   string $file index file of plugin.
	 *
	 * @since    1.0.0
	 */
	function mwb_ubo_lite_add_important_links( $links, $file ) {

		if ( strpos( $file, 'upsell-order-bump-offer-for-woocommerce.php' ) !== false ) {

			$row_meta = array(
				'demo' => '<a href="https://demo.makewebbetter.com/woocommerce-upsell-order-bump-offer/?utm_source=MWB-orderbump-home&utm_medium=MWB-home-page&utm_campaign=MWB-orderbump-home" target="_blank">' . esc_html__( 'Demo', 'upsell-order-bump-offer-for-woocommerce' ) . '</a>',
				'doc' => '<a href="https://docs.makewebbetter.com/woocommerce-upsell-order-bump-offer/?utm_source=MWB-orderbump-home&utm_medium=MWB-home-page&utm_campaign=MWB-orderbump-home" target="_blank">' . esc_html__( 'Documentation', 'upsell-order-bump-offer-for-woocommerce' ) . '</a>',
				'support' => '<a href="https://makewebbetter.com/submit-query/" target="_blank">' . esc_html__( 'Support', 'upsell-order-bump-offer-for-woocommerce' ) . '</a>',
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

	add_action( 'admin_init', 'mwb_ubo_lite_plugin_activation_failure' );

	/**
	 * Deactivate this plugin.
	 *
	 * @since    1.0.0
	 */
	function mwb_ubo_lite_plugin_activation_failure() {

		// To hide Plugin activated notice.
		if ( ! empty( $_GET['activate'] ) ) {

			unset( $_GET['activate'] );
		}

		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	// Add admin error notice.
	add_action( 'admin_notices', 'mwb_ubo_lite_activation_admin_notice' );

	/**
	 * This function is used to display plugin activation error notice.
	 *
	 * @since    1.0.0
	 */
	function mwb_ubo_lite_activation_admin_notice() {

		global $mwb_ubo_lite_plugin_activation;

		?>

		<?php if ( 'woo_inactive' == $mwb_ubo_lite_plugin_activation['message'] ) : ?>

			<div class="notice notice-error is-dismissible mwb-notice">
				<p><strong><?php esc_html_e( 'WooCommerce', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong><?php esc_html_e( ' is not activated, Please activate WooCommerce first to activate ', 'upsell-order-bump-offer-for-woocommerce' ); ?><strong><?php esc_html_e( 'Upsell Order Bump Offer for WooCommerce', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong><?php esc_html_e( '.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
			</div>

			<?php
		endif;
	}
}
