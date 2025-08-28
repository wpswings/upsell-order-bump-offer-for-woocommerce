<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used for Order Bump Reports and Order Bump Sales by Funnel - Stats.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      1.4.0
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/admin/reporting/templates
 */

/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get all Order Bumps.
$order_bumps = get_option( 'wps_ubo_bump_list' );

?>

<div class="wps_upsell_bumps_list" >

	<div class="wps_ubo_stats_heading" ><h2><?php esc_html_e( 'Order Bump - Behavioral Analytics', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2></div>

	<?php if ( empty( $order_bumps ) ) : ?>

		<p class="wps_upsell_bump_no_bump"><?php esc_html_e( 'No Order Bumps added', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>

		<?php endif; ?>

		<?php if ( ! empty( $order_bumps ) ) : ?>
		<div class="wps-bump-offer-container">
			<?php foreach ( $order_bumps as $key => $value ) : ?>
            <?php 
             if(empty($value['offer_view_count'])){
				return 'No Data Found';
			 }
			?>

			<div class="bump-offer">
				<button
				id="toggleButton<?php echo esc_attr( $key ); ?>"
				class="toggle-button"
				data-bump="<?php echo esc_attr( $key ); ?>">
				Hide Chart
				</button>

				<div
				id="chartContainer<?php echo esc_attr( $key ); ?>"
				class="chart-container collapsed">
				<canvas
					id="myPieChart<?php echo esc_attr( $key ); ?>"></canvas>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

</div>
