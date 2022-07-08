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
}
