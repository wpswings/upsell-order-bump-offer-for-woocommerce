<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://makewebbetter.com/?utm_source=MWB-orderbump-backend&utm_medium=MWB-Site-backend&utm_campaign=MWB-backend
 * @since      1.0.0
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/includes
 * @author     Make Web Better <webmaster@makewebbetter.com>
 */
class Upsell_Order_Bump_Offer_For_Woocommerce_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		$mwb_delete_all_data = get_option( 'mwb_ubo_global_options' );
		if ( 'on' === $mwb_delete_all_data['mwb_delete_all_on_uninstall'] ) {
			delete_option( 'mwb_ubo_global_options' );
			delete_option( 'mwb_ubo_bump_list' );
		}
	}

}
