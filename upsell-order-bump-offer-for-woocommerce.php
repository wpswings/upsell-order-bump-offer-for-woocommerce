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
  * Description:       Increase your cart value by adding bumps that offer additional products or services to customers at checkout page.
 *
 * Requires at least: 		4.4
 * Tested up to: 			5.2.2
 * WC requires at least: 	3.0
 * WC tested up to: 		3.7.0
 *
 * Version:           1.0.0
 * Author:            Make Web Better
 * Author URI:        https://makewebbetter.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       upsell-order-bump-offer-for-woocommerce
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-upsell-order-bump-offer-for-woocommerce-activator.php
 */
function activate_upsell_order_bump_offer_for_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-upsell-order-bump-offer-for-woocommerce-activator.php';
	Upsell_Order_Bump_Offer_For_Woocommerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-upsell-order-bump-offer-for-woocommerce-deactivator.php
 */
function deactivate_upsell_order_bump_offer_for_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-upsell-order-bump-offer-for-woocommerce-deactivator.php';
	Upsell_Order_Bump_Offer_For_Woocommerce_Deactivator::deactivate();
}

/**
 * The code that runs during plugin validation.
 * This action is checks for WooCommerce Dependency.
 */

function mwb_ubo_lite_plugin_activation() {

	$activation['status'] = true;
	$activation['message'] = '';

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	// Dependant plugin.
	if( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

		$activation['status'] = false;
		$activation['message'] = 'woo_inactive';

	}

	return $activation;
}

$mwb_ubo_lite_plugin_activation =  mwb_ubo_lite_plugin_activation();

if( true === $mwb_ubo_lite_plugin_activation['status'] ) {

	// Define all the neccessary details.
	define( 'UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL', plugin_dir_url( __FILE__ ) );

	define('UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_DIR_PATH', plugin_dir_path( __FILE__ ) );

	add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'mwb_ubo_lite_plugin_settings_link' );

	function mwb_ubo_lite_plugin_settings_link( $links ) 
	{
		$plugin_links= array('<a href="' .
			admin_url( 'admin.php?page=upsell-order-bump-offer-for-woocommerce-setting' ) .
			'">' . __( "Settings",'upsell-order-bump-offer-for-woocommerce' ) .'</a>');
		return array_merge( $plugin_links,$links );
	}

	add_filter( 'plugin_row_meta', 'mwb_ubo_lite_add_doc_and_premium_link', 10, 2 );

	function mwb_ubo_lite_add_doc_and_premium_link( $links, $file ) {
		
		if ( strpos( $file, 'upsell-order-bump-offer-for-woocommerce.php' ) !== false ) {

			$row_meta = array(
				'docs'    => '<a target="_blank" style="color:#FFF;background:linear-gradient(to right,#7a28ff 0,#00a1ff 100%);padding:5px;border-radius:6px;" href="https://docs.makewebbetter.com/woocommerce-upsell-order-bump-offer-pro/?utm_source=mwb-site&utm_medium=doc-cta&utm_campaign=bump-offer-page">'.esc_html__('Go to Docs', 'upsell-order-bump-offer-for-woocommerce' ).'</a>',

				'goPro' => '<a target="_blank" style="color:#FFF;background:linear-gradient(to right,#45b649,#dce35b);padding:5px;border-radius:6px;" href="https://makewebbetter.com/product/woocommerce-upsell-order-bump-offer-pro/"><strong>'.esc_html__('Go Premium', 'upsell-order-bump-offer-for-woocommerce' ).'</strong></a>',
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

	// Deactivate this plugin.
	function mwb_ubo_lite_plugin_activation_failure() {

		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	// Add admin error notice.
	add_action( 'admin_notices', 'mwb_ubo_lite_activation_admin_notice' );

	// This function is used to display plugin activation error notice.
	function mwb_ubo_lite_activation_admin_notice() {

		global $mwb_ubo_lite_plugin_activation;
		
		// To hide Plugin activated notice.
		unset( $_GET['activate'] );

	    ?>

	    <?php if( 'woo_inactive' == $mwb_ubo_lite_plugin_activation['message'] ) : ?>

		    <div class="notice notice-error is-dismissible">
		        <p><strong><?php esc_html_e( 'WooCommerce', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong><?php esc_html_e( ' is not activated, Please activate WooCommerce first to activate ', 'upsell-order-bump-offer-for-woocommerce' ); ?><strong><?php esc_html_e( 'Upsell Order Bump Offer for WooCommerce' ); ?></strong><?php esc_html_e( '.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
		    </div>

	    <?php endif;
	}
}