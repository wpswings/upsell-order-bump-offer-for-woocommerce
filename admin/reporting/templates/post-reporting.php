<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used for Upsell Reports and Upsell Sales by Funnel - Stats.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=upsell-org-backend&utm_campaign=official
 * @since      1.0.0
 *
 * @package     woo_one_click_upsell_funnel
 * @subpackage  woo_one_click_upsell_funnel/admin/partials/templates
 */

/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get all funnels.
if ( wps_is_plugin_active_with_version( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php', '3.0.0' ) ) {
	$funnels_list = get_option( 'wps_wocuf_pro_funnels_list' );
} else {
	$funnels_list = get_option( 'wps_wocuf_funnels_list' );
}
?>

<div class="wps_wocuf_pro_funnels_list">
	
	<div class="wps_uspell_stats_heading" ><h2><?php esc_html_e( 'Upsell - Behavioral Analytics', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2></div>

	<?php if ( empty( $funnels_list ) ) : ?>

		<p class="wps_wocuf_pro_no_funnel"><?php esc_html_e( 'No Upsell Data found', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>

	<?php endif; ?>

	<?php if ( ! empty( $funnels_list ) ) : ?>
	<div class="wps-bump-offer-container">
			<!-- Foreach Funnel start -->
			<?php foreach ( $funnels_list as $key => $value ) : ?>
			<?php 
					if(empty($value['offers_view_count']) && empty($value['funnel_success_count']) && empty($value['offers_accept_count']) && empty($value['funnel_total_sales'])){
						return;
					}
			?>
			<div class="bump-offer">
							<button
							id="wps-post-toggleButton<?php echo esc_attr( $key ); ?>"
							class="wps-post-toggle-button"
							data-bump="<?php echo esc_attr( $key ); ?>">
							Hide Chart
							</button>

							<div
							id="wps-post-chartContainer<?php echo esc_attr( $key ); ?>"
							class="wps-post-chart-container collapsed">
							<canvas
								id="wps-post-myPieChart<?php echo esc_attr( $key ); ?>"></canvas>
							</div>
						</div>
			<?php endforeach; ?>
			</div>
			<!-- Foreach Funnel end -->
	<?php endif; ?>
</div>
