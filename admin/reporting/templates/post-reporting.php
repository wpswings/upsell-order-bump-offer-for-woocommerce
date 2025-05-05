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
$funnels_list = get_option( 'wps_wocuf_pro_funnels_list' );
$org_upsell_funnels = get_option( 'wps_wocuf_funnels_list' );
$pro_upsell_funnels = get_option( 'wps_wocuf_pro_funnels_list' );

?>

<div class="wps_wocuf_pro_funnels_list">

	<div class="wps_uspell_reporting_heading" >
		<h2><?php esc_html_e( 'Upsell Sales - Reports', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2>
		<a target="_blank" href="<?php echo esc_url( admin_url( 'admin.php?page=wc-reports&tab=upsell' ) ); ?>"><?php esc_html_e( 'Visit here &rarr;', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
	</div>

	<hr class="wps_uspell_reporting_funnel_stats_hr">

	<div class="wps_uspell_stats_heading" ><h2><?php esc_html_e( 'Upsell - Behavioral Analytics', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2></div>

	<?php if ( empty( $funnels_list ) ) : ?>

		<p class="wps_wocuf_pro_no_funnel"><?php esc_html_e( 'No Upsell Data found', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>

	<?php endif; ?>

	<?php if ( ! empty( $funnels_list ) ) : ?>
		<table>
			<tr>
				<th><?php esc_html_e( 'Funnel Name', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Trigger Count', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Success Count', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Offers Viewed', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Offers Accepted', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Offers Rejected', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Offers Pending', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Conversion Rate', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Total Sales', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
			</tr>

			<!-- Foreach Funnel start -->
			<?php
			foreach ( $funnels_list as $key => $value ) :

				?>

				<tr>		
					<!-- Funnel Name -->
				<?php 
					if ( wps_is_plugin_active_with_version( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php', '4.0.0' ) ) {
					 ?>
					<td><a class="wps_upsell_funnel_list_name" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting-post&sub_tab=post-list-offer-section&funnel_id=<?php echo esc_html( $key ); ?>"><?php echo esc_html( $value['wps_wocuf_pro_funnel_name'] ); ?></a></td>
					<?php 
					} else {
					?>

<td><a class="wps_upsell_funnel_list_name" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting-post&sub_tab=post-list-offer-section&funnel_id=<?php echo esc_html( $key ); ?>"><?php echo esc_html( $value['wps_wocuf_funnel_name'] ); ?></a></td>

<?php
					}
					?>

					<!-- Trigger Count -->
					<td>

						<?php

						$funnel_triggered_count = ! empty( $value['funnel_triggered_count'] ) ? $value['funnel_triggered_count'] : 0;

						echo esc_html( $funnel_triggered_count );

						?>

					</td>

					<!-- Success Count -->
					<td>

						<?php

						$funnel_success_count = ! empty( $value['funnel_success_count'] ) ? $value['funnel_success_count'] : 0;

						echo esc_html( $funnel_success_count );

						?>

					</td>

					<!-- Offers Viewed -->
					<td>

						<?php

						$offers_view_count = ! empty( $value['offers_view_count'] ) ? $value['offers_view_count'] : 0;

						echo esc_html( $offers_view_count );

						?>

					</td>

					<!-- Offers Accepted -->
					<td>

						<?php

						$offers_accept_count = ! empty( $value['offers_accept_count'] ) ? $value['offers_accept_count'] : 0;

						echo esc_html( $offers_accept_count );

						?>

					</td>

					<!-- Offers Rejected -->
					<td>

						<?php

						$offers_reject_count = ! empty( $value['offers_reject_count'] ) ? $value['offers_reject_count'] : 0;

						echo esc_html( $offers_reject_count );

						?>

					</td>

					<!-- Offers Pending -->
					<td>

						<?php

						$offers_pending_count = $offers_view_count - $offers_accept_count - $offers_reject_count;

						echo esc_html( $offers_pending_count );

						?>

					</td>

					<!-- Conversion Rate -->
					<td>

						<?php

						if ( ! empty( $funnel_triggered_count ) ) {

							$conversion_rate = ( $funnel_success_count * 100 ) / $funnel_triggered_count;
						} else {

							$conversion_rate = 0;
						}

						$conversion_rate = number_format( (float) $conversion_rate, 2 );

						echo '<div class="wps_upsell_stats_conversion_rate"><p>' . esc_html( $conversion_rate ) . esc_html__( '%', 'upsell-order-bump-offer-for-woocommerce' ) . '</p><div>';

						?>

					</td>

					<!-- Total Sales -->
					<td>

						<?php

						$funnel_total_sales = ! empty( $value['funnel_total_sales'] ) ? $value['funnel_total_sales'] : 0;

						$funnel_total_sales = number_format( (float) $funnel_total_sales, 2 );

						echo '<div class="wps_upsell_stats_total_sales"><p>' . esc_html( get_woocommerce_currency_symbol() ) . esc_html( $funnel_total_sales ) . '</p><div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

						?>

					</td>

				</tr>
			<?php endforeach; ?>
			<!-- Foreach Funnel end -->
		</table>
	<?php endif; ?>
</div>
