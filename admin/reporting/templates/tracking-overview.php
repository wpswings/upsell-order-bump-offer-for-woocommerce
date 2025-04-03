<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=upsell-org-backend&utm_campaign=official
 * @since      1.0.0
 *
 * @package     woo_one_click_upsell_funnel
 * @subpackage  woo_one_click_upsell_funnel/tracking/templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$nonce = wp_create_nonce( 'view_upsell_tracking' ); // Create nonce.
$nonce_facebook = wp_create_nonce( 'view_upsell_tracking_fb' ); // Create nonce.

?>

<div id="wps_upsell_lite_overview">
	<div id="wps_upsell_lite_overview_pro_version">

		<h2><?php esc_html_e( 'eCommerce Analytics & Tracking', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2>
		<h3><?php esc_html_e( 'Supported Analytics Tools', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>

		<div class="wps_upsell_overview_supported_product">
			<div class="wps_upsell_overview_product_icon simple">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=upsell-order-bump-offer-for-woocommerce-post-reporting&tab=ga-setting&nonce=' . $nonce ) ); ?>">
					<img class="wps_upsell_lite_tool_ga" src="<?php echo esc_url( WPS_WOCUF_URL_FUNNEL_BUILDER . 'admin/reporting/resources/icons/google-analytics.svg' ); ?>">
				</a>
			</div>
			<div class="wps_upsell_overview_product_icon simple">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=upsell-order-bump-offer-for-woocommerce-post-reporting&tab=pixel-setting&nonce=' . $nonce_facebook ) ); ?>">
					<img class="wps_upsell_lite_tool_fb" src="<?php echo esc_url( WPS_WOCUF_URL_FUNNEL_BUILDER . 'admin/reporting/resources/icons/facebook-pixel.png' ); ?>">
				</a>
			</div>
		</div>
	</div>
</div>