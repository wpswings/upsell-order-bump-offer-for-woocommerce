<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to give an overview of pro version and features.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      1.0.0
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/admin/partials/templates
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<!-- Plugin Overview Template. -->
<div id="wps_ubo_lite_overview">
	<div class="wps_ubo_lite_overview_pro_version wps_ubo_lite_go_pro_feature_first">
		<div class="wps_ubo_lite_main_feature_image">
			<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/banner.jpg' ); ?> ">
		</div>
		<h2><?php esc_html_e( 'What Is Upsell Funnel Builder For WooCommerce?', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2>
		<div class="wps_ubo_lite_cont_vid">
			<div class="wps_ubo_lite_overview_cont">
			<p><?php esc_html_e( 'Upsell Funnel Builder for WooCommerce is a sales funnel builder that merchants can use to present their customers with One-Click Upsell, Cross-Sell, Product Recommendations, and order bump offers right where they’re most likely to buy!  This plugin allows merchants to create engaging sales funnels and bump offers that convert.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>

<p><?php esc_html_e( 'Store owners can enhance customers’ shopping experience and convert opportunities into transactions.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>

				<ul class='wps_ubo_lite_feature_list'>
					<li><span class='wps_ubo_lite_feature_content'><?php esc_html_e( 'Use pre-defined shortcodes to create offer page elements.', 'upsell-order-bump-offer-for-woocommerce' ); ?></span></li>
					<li><span class='wps_ubo_lite_feature_content'><?php esc_html_e( 'Get a comprehensive tracking report for every sales funnel.', 'upsell-order-bump-offer-for-woocommerce' ); ?></span></li>
					<li><span class='wps_ubo_lite_feature_content'><?php esc_html_e( 'Build responsive and product-specific offer pages.', 'upsell-order-bump-offer-for-woocommerce' ); ?></span></li>
					<li><span class='wps_ubo_lite_feature_content'><?php esc_html_e( 'Offer an upgrade of the existing product in the cart.', 'upsell-order-bump-offer-for-woocommerce' ); ?></span></li>
				</ul>
			</div>
		</div>
	</div>

	<?php if ( ! wps_ubo_lite_is_plugin_active( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php' ) ) : ?>

		<div class="wps_ubo_lite_overview_pro_version">
			<h2><?php esc_html_e( 'Premium Plugin Additional Features', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2>

			<!-- Premium feature section starts. -->
			<div class="wps_ubo_lite_overview_go_pro">

				<!-- Feature - Left Intent -->
				<div class="wps_ubo_lite_go_pro_features wps_ubo_lite_go_pro_unlimited_offers">
					<div class="wps_ubo_lite_go_pro_feature_image"><img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/icons/Customize-Checkout-and-Thank-You-Page.svg' ); ?> "></div>
					<div class="wps_ubo_lite_go_pro_features_wrap">
						<!-- Pro feature heading. -->
						<h3><?php esc_html_e( 'Customize Checkout And Thank You Page', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
						<!-- Pro feature Content. -->
						<p><?php esc_html_e( 'Customize your checkout and thank you pages however you like.', 'upsell-order-bump-offer-for-woocommerce' ); ?><br>
							<?php esc_html_e( 'Merchants can hide, disable, or rearrange fields, so only the ones you need are shown, making the process smoother. This helps simplify the checkout experience, which can reduce cart abandonment.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
					</div>
				</div>

				<!-- Feature - Right Intent -->
				<div class="wps_ubo_lite_go_pro_features wps_ubo_lite_go_pro_multiple_order_bumps">
					<div class="wps_ubo_lite_go_pro_feature_image"><img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/icons/Multiple-Payment-Gateways.svg' ); ?> "></div>
					<div class="wps_ubo_lite_go_pro_features_wrap">
						<!-- Pro feature heading. -->
						<h3><?php esc_html_e( 'Multiple Payment Gateways', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
						<!-- Pro feature Content. -->
						<p><?php esc_html_e( 'The compatible with multiple popular payment gateways like WooCommerce Stripe, WooCommerce Standard PayPal, Braintree, Square, Mollie, and more, which allow paying for upsells with just a click.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
					</div>
				</div>

				<!-- Feature - Right Intent -->
				<div class="wps_ubo_lite_go_pro_features wps_ubo_lite_go_pro_upgrade_offer">

					<div class="wps_ubo_lite_go_pro_feature_image"><img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/icons/A-B-Testing-Funnels.svg' ); ?> "></div>
					<div class="wps_ubo_lite_go_pro_features_wrap">
						<!-- Pro feature Content. -->
						<h3><?php esc_html_e( 'A/B Testing Funnels', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
						<!-- Pro feature heading. -->
						<p>
							<?php
							esc_html_e(
								'Using this feature the admin can easily compare two upsell offers and see which one works better.',
								'upsell-order-bump-offer-for-woocommerce'
							);
							?>
							<br>
							<?php esc_html_e( 'You can track important stats like how many times the offer was triggered, how many times it succeeded, how many offers were viewed, accepted, or rejected, and the total sales generated.', 'upsell-order-bump-offer-for-woocommerce' ); ?>
						</p>
					</div>
				</div>

				<!-- Feature - Right Intent -->
				<div class="wps_ubo_lite_go_pro_features wps_ubo_lite_go_pro_smart_skip">
					<div class="wps_ubo_lite_go_pro_feature_image"><img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/icons/Multiple-Bump-Recommendations.svg' ); ?> "></div>
					<div class="wps_ubo_lite_go_pro_features_wrap">
						<!-- Pro feature heading. -->
						<h3><?php esc_html_e( 'Multiple Bump Recommendations', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
						<!-- Pro feature Content. -->
						<p><?php esc_html_e( 'Merchants can set multiple bump recommendations for bump sales, and different products listed on their platforms will appear on the product and cart page when customers add a product to their cart.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
					</div>
				</div>

		<!-- Feature - Right Intent -->
		<div class="wps_ubo_lite_go_pro_features wps_ubo_lite_go_pro_smart_skip">
					<div class="wps_ubo_lite_go_pro_feature_image"><img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/icons/Popup-on-exit-intent.svg' ); ?> "></div>
					<div class="wps_ubo_lite_go_pro_features_wrap">
						<!-- Pro feature heading. -->
						<h3><?php esc_html_e( 'Popup On Exit Intent', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
						<!-- Pro feature Content. -->
						<p><?php esc_html_e( 'This is the ultimate upselling hack for your online store, as it gives you the option to display the sales pop-up screen of the WooCommerce upsell product image or title as per your choice when the customer is exiting your website.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
					</div>
				</div>


		<!-- Feature - Right Intent -->
		<div class="wps_ubo_lite_go_pro_features wps_ubo_lite_go_pro_smart_skip">
					<div class="wps_ubo_lite_go_pro_feature_image"><img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/icons/Frequently-Bought-Together.svg' ); ?> "></div>
					<div class="wps_ubo_lite_go_pro_features_wrap">
						<!-- Pro feature heading. -->
						<h3><?php esc_html_e( 'Frequently Bought Together', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
						<!-- Pro feature Content. -->
						<p><?php esc_html_e( 'With this WooCommerce order bump feature, merchants can showcase a list of products that are ‘frequently bought together’ so that their customers can consider buying those products, which increases revenue.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
					</div>
				</div>

				<!-- Feature - Left Intent -->
				<div class="wps_ubo_lite_go_pro_features wps_ubo_lite_go_pro_exclusive_support">
					<div class="wps_ubo_lite_go_pro_feature_image"><img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/icons/customer-support.png' ); ?> "></div>
					<div class="wps_ubo_lite_go_pro_features_wrap">
						<!-- Pro feature Content. -->
						<h3><?php esc_html_e( 'Exclusive Support', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
						<!-- Pro feature heading. -->
						<p>
							<?php
							esc_html_e(
								'Receive dedicated 24*7 Phone, Email & Skype support.',
								'upsell-order-bump-offer-for-woocommerce'
							);
							?>
							<br>
							<?php esc_html_e( "Our Support is ready to assist you regarding any query, issue, or feature request and if that doesn't help our Technical team will connect with you personally and have your query resolved.", 'upsell-order-bump-offer-for-woocommerce' ); ?>
						</p>
					</div>
				</div>
			</div>
			<!-- Premium feature section ends. -->
			<!-- Go pro section starts. -->
			<div class="wps_ubo_lite_go_pro_popup_button">
				<a class="button wps_ubo_lite_overview_go_pro_button" target="_blank" href="https://wpswings.com/product/upsell-order-bump-offer-for-woocommerce-pro/?utm_source=wpswings-order-bump-pro&utm_medium=order-bump-org-backend&utm_campaign=WPS-order-bump-pro">
					<?php
					esc_html_e( 'Upgrade to Premium', 'upsell-order-bump-offer-for-woocommerce' );
					echo ' <span class="dashicons dashicons-arrow-right-alt"></span>';
					?>
				</a>
			</div>
			<!-- Go pro section ends. -->
		</div>

	<?php endif; ?>
</div>
