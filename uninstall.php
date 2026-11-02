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

$wps_global_options = get_option( 'wps_ubo_global_options', array() );
if ( ! empty( $wps_global_options['wps_delete_all_on_uninstall'] ) && 'on' === $wps_global_options['wps_delete_all_on_uninstall'] ) {
	delete_option( 'wps_ubo_global_options' );
	delete_option( 'wps_ubo_bump_list' );

	// Offer Heatmaps: drop events table, settings and cron.
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-upsell-order-bump-heatmap-db.php';
	Upsell_Order_Bump_Heatmap_Db::uninstall();

	// Smart Offer Recommendations: drop model tables, settings, schedules and order meta.
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-upsell-order-bump-reco-db.php';
	Upsell_Order_Bump_Reco_Db::uninstall();

	// Gamified bump offers: settings and statistics.
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-upsell-order-bump-gamified.php';
	Upsell_Order_Bump_Gamified::uninstall();

	// Native cart recovery: drop the saved-carts table, settings and schedule.
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-upsell-order-bump-acr-db.php';
	Upsell_Order_Bump_Acr_Db::uninstall();
}
