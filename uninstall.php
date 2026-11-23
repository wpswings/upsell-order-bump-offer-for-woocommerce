<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress while unintalling the plugin.
 *
 * @link              https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since             1.0.0
 * @package           Upsell_Order_Bump_Offer_For_Woocommerce
 */

// Only when WordPress is really uninstalling the plugin ( never when loaded directly ).
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-upsell-order-bump-heatmap-db.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-upsell-order-bump-reco-db.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-upsell-order-bump-gamified.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-upsell-order-bump-acr-db.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-upsell-order-bump-acr-engine.php'; // Needed by Acr_Db::uninstall() to delete acr-* coupons.

/**
 * Remove the plugin's data from the current site, when the site asked for it.
 *
 * @return void
 */
function wps_ubo_uninstall_current_site() {
	$wps_global_options = get_option( 'wps_ubo_global_options', array() );
	if ( empty( $wps_global_options['wps_delete_all_on_uninstall'] ) || 'on' !== $wps_global_options['wps_delete_all_on_uninstall'] ) {
		return;
	}
	delete_option( 'wps_ubo_global_options' );
	delete_option( 'wps_ubo_bump_list' );

	// Offer Heatmaps: drop events table, settings and cron.
	Upsell_Order_Bump_Heatmap_Db::uninstall();

	// Smart Offer Recommendations: drop model tables, settings, schedules and order meta.
	Upsell_Order_Bump_Reco_Db::uninstall();

	// Gamified bump offers: settings and statistics.
	Upsell_Order_Bump_Gamified::uninstall();

	// Native cart recovery: drop the saved-carts table, settings and schedule.
	Upsell_Order_Bump_Acr_Db::uninstall();
}

if ( is_multisite() ) {
	// Every site has its own tables and options ( each site's own "delete all" setting applies ).
	foreach ( get_sites( array( 'fields' => 'ids', 'number' => 0 ) ) as $wps_site_id ) {
		switch_to_blog( $wps_site_id );
		wps_ubo_uninstall_current_site();
		restore_current_blog();
	}
} else {
	wps_ubo_uninstall_current_site();
}
