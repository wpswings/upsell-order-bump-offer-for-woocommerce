<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used for listing all the shortcodes of the plugin.
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
?>

<div class="wps_upsell_table wps_upsell_new_shortcodes">
	<table class="form-table wps_wocuf_pro_shortcodes">
		<tbody>
			<!-- Upsell Action shortcodes start-->
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label><?php esc_html_e( 'Upsell Action shortcodes', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
				</th>
				<td class="forminp forminp-text">
					<div class="wps_upsell_shortcode_div">
						<p class="wps_upsell_shortcode">
							<?php
							$attribute_description = sprintf( '<p class="wps_upsell_tip_tip">%s</p><p class="wps_upsell_tip_tip">%s</p>', esc_html__( 'Accept Offer.', 'upsell-order-bump-offer-for-woocommerce' ), esc_html__( 'This shortcode only returns the link so it has to be used in the link section. In html use it as href="[wps_upsell_yes]" of anchor tag.', 'upsell-order-bump-offer-for-woocommerce' ) );

							wps_upsell_lite_wc_help_tip( $attribute_description ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
							<span class="wps_upsell_shortcode_title"><?php esc_html_e( 'Buy Now &rarr;', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<span class="wps_upsell_shortcode_content"><?php echo esc_html__( '[wps_upsell_yes]', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						</p>
					</div>
					<div class="wps_upsell_shortcode_div" >
						<p class="wps_upsell_shortcode">
							<?php
							$attribute_description = sprintf( '<p class="wps_upsell_tip_tip">%s</p><p class="wps_upsell_tip_tip">%s</p>', esc_html__( 'Reject Offer.', 'upsell-order-bump-offer-for-woocommerce' ), esc_html__( 'This shortcode only returns the link so it has to be used in the link section. In html use it as href="[wps_upsell_no]" of anchor tag.', 'upsell-order-bump-offer-for-woocommerce' ) );

							wps_upsell_lite_wc_help_tip( $attribute_description ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
							<span class="wps_upsell_shortcode_title"><?php esc_html_e( 'No Thanks &rarr;', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<span class="wps_upsell_shortcode_content"><?php echo esc_html__( '[wps_upsell_no]', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						</p>
					</div>		
				</td>
			</tr>
			<!-- Upsell Action shortcodes end-->

			<!-- Product shortcodes start-->
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label><?php esc_html_e( 'Product shortcodes', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
				</th>
				<td class="forminp forminp-text">
					<div class="wps_upsell_shortcode_div">
						<p class="wps_upsell_shortcode">
							<?php
							$attribute_description = sprintf( '<p class="wps_upsell_tip_tip">%s</p>', esc_html__( 'This shortcode returns the product title.', 'upsell-order-bump-offer-for-woocommerce' ) );

							wps_upsell_lite_wc_help_tip( $attribute_description );
							?>
							<span class="wps_upsell_shortcode_title"><?php esc_html_e( 'Product Title &rarr;', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<span class="wps_upsell_shortcode_content"><?php echo esc_html__( '[wps_upsell_title]', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						</p>
					</div>
					<div class="wps_upsell_shortcode_div" >
						<p class="wps_upsell_shortcode">
							<?php
							$attribute_description = sprintf( '<p class="wps_upsell_tip_tip">%s</p>', esc_html__( 'This shortcode returns the product description.', 'upsell-order-bump-offer-for-woocommerce' ) );

							wps_upsell_lite_wc_help_tip( $attribute_description ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
							<span class="wps_upsell_shortcode_title"><?php esc_html_e( 'Product Description &rarr;', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<span class="wps_upsell_shortcode_content"><?php echo esc_html__( '[wps_upsell_desc]', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						</p>
					</div>	
					<div class="wps_upsell_shortcode_div" >
						<p class="wps_upsell_shortcode">
							<?php
							$attribute_description = sprintf( '<p class="wps_upsell_tip_tip">%s</p>', esc_html__( 'This shortcode returns the product short description.', 'upsell-order-bump-offer-for-woocommerce' ) );

							wps_upsell_lite_wc_help_tip( $attribute_description ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped.
							?>
							<span class="wps_upsell_shortcode_title"><?php esc_html_e( 'Product Short Description &rarr;', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<span class="wps_upsell_shortcode_content"><?php echo esc_html__( '[wps_upsell_desc_short]', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						</p>
					</div>
					<div class="wps_upsell_shortcode_div" >
						<p class="wps_upsell_shortcode">
							<?php
							$attribute_description = sprintf( '<p class="wps_upsell_tip_tip">%s</p>', esc_html__( 'This shortcode returns the product upsell shipping price.', 'upsell-order-bump-offer-for-woocommerce' ) );

							wps_upsell_lite_wc_help_tip( $attribute_description ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped.
							?>
							<span class="wps_upsell_shortcode_title"><?php esc_html_e( 'Product Upsell Shipping Price &rarr;', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<span class="wps_upsell_shortcode_content"><?php echo esc_html__( '[wps_upsell_product_shipping_price]', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						</p>
					</div>
					<hr class="wps_upsell_shortcodes_hr">
					<div class="wps_upsell_shortcode_div" >
						<p class="wps_upsell_shortcode">
							<?php
							$attribute_description = sprintf( '<p class="wps_upsell_tip_tip">%s</p>', esc_html__( 'This shortcode returns the product image.', 'upsell-order-bump-offer-for-woocommerce' ) );

							wps_upsell_lite_wc_help_tip( $attribute_description ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
							<span class="wps_upsell_shortcode_title"><?php esc_html_e( 'Product Image &rarr;', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<span class="wps_upsell_shortcode_content"><?php echo esc_html__( '[wps_upsell_image]', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						</p>
					</div>
					<div class="wps_upsell_shortcode_div" >
						<p class="wps_upsell_shortcode">
							<?php
							$attribute_description = sprintf( '<p class="wps_upsell_tip_tip">%s</p>', esc_html__( 'This shortcode returns the product price.', 'upsell-order-bump-offer-for-woocommerce' ) );

							wps_upsell_lite_wc_help_tip( $attribute_description ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
							<span class="wps_upsell_shortcode_title"><?php esc_html_e( 'Product Price &rarr;', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<span class="wps_upsell_shortcode_content"><?php echo esc_html__( '[wps_upsell_price]', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						</p>
					</div>
					<div class="wps_upsell_shortcode_div" >
						<p class="wps_upsell_shortcode">
							<?php
							$attribute_description = sprintf( '<p class="wps_upsell_tip_tip">%s</p>', esc_html__( '( Only for Pro ) This shortcode returns the product variations if offer product is a variable product.', 'upsell-order-bump-offer-for-woocommerce' ) );

							wps_upsell_lite_wc_help_tip( $attribute_description ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
							<span class="wps_upsell_shortcode_title"><?php esc_html_e( 'Product Variations &rarr;', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<span class="wps_upsell_shortcode_content"><?php echo esc_html__( '[wps_upsell_variations]', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						</p>
					</div>
				</td>
			</tr>
			<!-- Product shortcodes end-->

			<!-- Other shortcodes start-->
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label><?php esc_html_e( 'Other shortcodes', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
				</th>
				<td class="forminp forminp-text">
					<div class="wps_upsell_shortcode_div">
						<p class="wps_upsell_shortcode">
							<?php
							$attribute_description = sprintf( '<p class="wps_upsell_tip_tip">%s</p>', esc_html__( 'This shortcode returns Star ratings. You can specify the number of stars like [wps_upsell_star_review stars=4.5] .', 'upsell-order-bump-offer-for-woocommerce' ) );
							wps_upsell_lite_wc_help_tip( $attribute_description ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
							<span class="wps_upsell_shortcode_title"><?php esc_html_e( 'Star Ratings &rarr;', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<span class="wps_upsell_shortcode_content"><?php echo esc_html__( '[wps_upsell_star_review]', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						</p>
					</div>
					<div class="wps_upsell_shortcode_div">
						<p class="wps_upsell_shortcode">
							<?php

							$attribute_description = sprintf( '<p class="wps_upsell_tip_tip">%s</p>', esc_html__( 'This shortcode returns quantity field. You can restrict the customer to select the quantity offered. [wps_upsell_quantity max=4 min=1 ] .', 'upsell-order-bump-offer-for-woocommerce' ) );
							wps_upsell_lite_wc_help_tip( $attribute_description ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped.

							?>
							<span class="wps_upsell_shortcode_title"><?php esc_html_e( 'Offer Quantity &rarr;', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<span class="wps_upsell_shortcode_content"><?php echo esc_html__( '[wps_upsell_quantity]', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						</p>
					</div>
					<div class="wps_upsell_shortcode_div">
						<p class="wps_upsell_shortcode">
							<?php

							$attribute_description = sprintf( '<p class="wps_upsell_tip_tip">%s</p>', esc_html__( 'This shortcode returns urgency timer. You can specify the timer limit as [wps_upsell_timer minutes=5] .', 'upsell-order-bump-offer-for-woocommerce' ) );
							wps_upsell_lite_wc_help_tip( $attribute_description ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped.

							?>
							<span class="wps_upsell_shortcode_title"><?php esc_html_e( 'Urgency Timer &rarr;', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<span class="wps_upsell_shortcode_content"><?php echo esc_html__( '[wps_upsell_timer]', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						</p>
					</div>
				</td>
			</tr>
			<!-- Other shortcodes ends-->
		</tbody>
	</table>
</div>
