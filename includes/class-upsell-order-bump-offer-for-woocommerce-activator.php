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

		// Set default settings tab to Overview for five minutes.
		set_transient( 'wps_ubo_lite_default_settings_tab', 'overview', 300 );
	}

}
