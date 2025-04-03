<?php
/**
 * Fired during plugin activation.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      1.0.0
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/includes
 * @author     WP Swings <webmaster@wpswings.com>
 */
class Upsell_Order_Bump_Offer_For_Woocommerce_Activator {

	/**
	 * Just set a transient to get tabs operative.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		set_transient( 'wps_ubo_lite_default_settings_tab', 'overview', 300 );
		// Check if the option already exists; if not, add it with an empty array.
		if ( ! get_option( 'wps_ubo_global_options' ) ) {
		add_option( 'wps_ubo_global_options', array() );
		}

		$wps_run_code_once = get_option('wps_code_run_once',''); // Default to false if not set.
		if ('yes' != $wps_run_code_once) {

		$wps_upsell_global_options = get_option('wps_upsell_lite_global_options', array());
		$wps_bump_upsell_global_options = get_option('wps_ubo_global_options', array());
		$wps_bump_upsell_global_options['wps_bump_enable_plugin'] = $wps_upsell_global_options['wps_wocuf_enable_plugin'];
		$wps_bump_upsell_global_options['wps_bump_skip_offer'] =$wps_upsell_global_options['skip_similar_offer'] ;
		$wps_bump_upsell_global_options['wps_ubo_offer_price_html'] =$wps_upsell_global_options['offer_price_html_type'] ;
		if ( $wps_upsell_global_options['offer_price_html_type'] == 'sale'){
		$wps_bump_upsell_global_options['wps_ubo_offer_price_html'] = 'sale_to_offer';
		} else{
		$wps_bump_upsell_global_options['wps_ubo_offer_price_html'] = 'regular_to_offer';
		}
		update_option( 'wps_ubo_global_options', $wps_bump_upsell_global_options );
		update_option('wps_code_run_once', 'yes');
	}
}

}
