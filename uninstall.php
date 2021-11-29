<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress while unintalling the plugin.
 *
 * @link              https://makewebbetter.com/?utm_source=MWB-orderbump-backend&utm_medium=MWB-Site-backend&utm_campaign=MWB-backend
 * @since             1.0.0
 * @package           Upsell_Order_Bump_Offer_For_Woocommerce
 */

$mwb_global_options = get_option( 'mwb_ubo_global_options', array() );
if ( ! empty( $mwb_global_options['mwb_delete_all_on_uninstall'] ) && 'on' === $mwb_global_options['mwb_delete_all_on_uninstall'] ) {
	delete_option( 'mwb_ubo_global_options' );
	delete_option( 'mwb_ubo_bump_list' );
}
