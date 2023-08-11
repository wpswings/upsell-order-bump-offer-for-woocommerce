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
		<h2><?php esc_html_e( 'What is Upsell Order Bump Offer for WooCommerce?', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2>
		<div class="wps_ubo_lite_cont_vid">
			<div class="wps_ubo_lite_overview_cont">
				<p><?php esc_html_e( "Upsell Order Bump Offer for WooCommerce allows its users to show exclusive special offers known as Order Bumps on the checkout page. These Order Bumps can be accepted in just a single click which is added instantly to customer's existing Order.", 'upsell-order-bump-offer-for-woocommerce' ); ?></p>

				<p><?php esc_html_e( 'The Store owner can set the offers specifically for Target Products or Categories so that relevant Order Bumps can be offered which are hard to resist.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>

				<ul class='wps_ubo_lite_feature_list'>
					<li><span class='wps_ubo_lite_feature_content'><?php esc_html_e( 'Capture Customer’s attention by displaying appealing offers on the checkout page.', 'upsell-order-bump-offer-for-woocommerce' ); ?></span></li>
					<li><span class='wps_ubo_lite_feature_content'><?php esc_html_e( 'Develop Interest for the offer by mentioning how the offer will benefit them on their existing purchase.', 'upsell-order-bump-offer-for-woocommerce' ); ?></span></li>
					<li><span class='wps_ubo_lite_feature_content'><?php esc_html_e( 'Convince them that this is an Exclusive offer only available here at this Discount.', 'upsell-order-bump-offer-for-woocommerce' ); ?></span></li>
					<li><span class='wps_ubo_lite_feature_content'><?php esc_html_e( 'Ask them to Act and Accept the offer.', 'upsell-order-bump-offer-for-woocommerce' ); ?></span></li>
				</ul>
			</div>
			<div id="wps_ubo_lite_overview_video" class="wps_ubo_lite_overview_video">
				<iframe width="100%" height="600px" src="https://www.youtube.com/embed/p9KIQyXauY4" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
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
					<div class="wps_ubo_lite_go_pro_feature_image"><img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/icons/bumps.png' ); ?> "></div>
					<div class="wps_ubo_lite_go_pro_features_wrap">
						<!-- Pro feature heading. -->
						<h3><?php esc_html_e( 'Unlock the power to create unlimited Order Bumps', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
						<!-- Pro feature Content. -->
						<p><?php esc_html_e( 'With our Free version, you have limited access to create just one Order Bump.', 'upsell-order-bump-offer-for-woocommerce' ); ?><br>
						<?php esc_html_e( 'So, get our Premium version today to create multiple Order Bumps. Introduce your customers to the most relevant offers which will benefit them on their existing purchase. Give your sales a great boost ahead.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
					</div>
				</div>

				<!-- Feature - Right Intent -->
				<div class="wps_ubo_lite_go_pro_features wps_ubo_lite_go_pro_multiple_order_bumps">
					<div class="wps_ubo_lite_go_pro_feature_image"><img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/icons/multiple-order-bumps.png' ); ?> "></div>
					<div class="wps_ubo_lite_go_pro_features_wrap">
						<!-- Pro feature heading. -->
						<h3><?php esc_html_e( 'Multiple Order Bumps', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
						<!-- Pro feature Content. -->
						<p><?php esc_html_e( 'Show more than one Order Bump on the checkout page. This plugin now supports Multiple Order Bumps.', 'upsell-order-bump-offer-for-woocommerce' ); ?><br>
						<?php esc_html_e( 'Yes, now you can show Multiple Order Bumps simultaneously on the checkout page so that your customers can have more options to choose from.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
					</div>
				</div>

				<!-- Feature - Right Intent -->
				<div class="wps_ubo_lite_go_pro_features wps_ubo_lite_go_pro_upgrade_offer">

					<div class="wps_ubo_lite_go_pro_feature_image"><img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/icons/smart-upgrade.png' ); ?> "></div>
					<div class="wps_ubo_lite_go_pro_features_wrap">
						<!-- Pro feature Content. -->
						<h3><?php esc_html_e( 'Smart Offer Upgrade', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
						<!-- Pro feature heading. -->
						<p>
						<?php
						esc_html_e(
							'Upgrade the Existing purchase to your customers.',
							'upsell-order-bump-offer-for-woocommerce'
						);
						?>
						<br>
						<?php esc_html_e( ' The Smart Upgrade offer feature will replace the existing product with the offer product as soon as they accept the Order Bump offer. If the customer accepts the offer, the initial product will be replaced with the upgraded product.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
					</div>
				</div>

				<!-- Feature - Right Intent -->
				<div class="wps_ubo_lite_go_pro_features wps_ubo_lite_go_pro_smart_skip">
					<div class="wps_ubo_lite_go_pro_feature_image"><img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/icons/past-order.png' ); ?> "></div>
					<div class="wps_ubo_lite_go_pro_features_wrap">
						<!-- Pro feature heading. -->
						<h3><?php esc_html_e( 'Smart Skip if Already Purchased', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
						<!-- Pro feature Content. -->
						<p><?php esc_html_e( 'This feature will allow you to Skip the Order Bump offer for those customers who have already purchased the offer product anytime before in previous orders.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
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
						<?php esc_html_e( "Our Support is ready to assist you regarding any query, issue, or feature request and if that doesn't help our Technical team will connect with you personally and have your query resolved.", 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
					</div>
				</div>
			</div>
			<!-- Premium feature section ends. -->
			<!-- Upcoming feature section starts. -->
			<div class="wps_ubo_lite_overview_pro_version wps_ubo_lite_overview_wrap_upcom">
				<h2><?php esc_html_e( 'Upcoming Plugin Features', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2>
				<div class="wps_ubo_lite_overview_go_pro wps_ubo_lite_overview_go_upcom">

				<!-- Feature 01 -->
					<div class="wps_ubo_lite_go_pro_features wps_ubo_lite_go_pro_unlimited_offers">
						<div class="wps_ubo_lite_go_pro_feature_image"><img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/icons/bumps-1.png' ); ?> "></div>
						<div class="wps_ubo_lite_go_pro_features_wrap">
							<!-- Pro feature heading. -->
							<h3><?php esc_html_e( 'Display Product Bump On Cart', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
							<!-- Pro feature Content. -->
							<p><?php esc_html_e( 'Merchants can show offers as product order bumps on a cart page. They can create WooCommerce bump offer funnels for every product on their WooCommerce store by displaying various WooCommerce order bumps.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
						</div>
					</div>

				<!-- Feature 02 -->
					<div class="wps_ubo_lite_go_pro_features wps_ubo_lite_go_pro_unlimited_offers">
						<div class="wps_ubo_lite_go_pro_feature_image"><img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/icons/bumps-2.png' ); ?> "></div>
						<div class="wps_ubo_lite_go_pro_features_wrap">
							<!-- Pro feature heading. -->
							<h3><?php esc_html_e( 'Popup-alert on Exit Intent', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
							<!-- Pro feature Content. -->
							<p><?php esc_html_e( 'This feature gives, merchants the option to display the product order bump on the pop-up screen of the WooCommerce upsell products image or title as per your choice at the checkout page', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
						</div>
					</div>

				<!-- Feature 03 -->
					<div class="wps_ubo_lite_go_pro_features wps_ubo_lite_go_pro_unlimited_offers">
						<div class="wps_ubo_lite_go_pro_feature_image"><img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/icons/bumps-3.png' ); ?> "></div>
						<div class="wps_ubo_lite_go_pro_features_wrap">
							<!-- Pro feature heading. -->
							<h3><?php esc_html_e( 'Multiple Checkout Plugin Compatibility', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
							<!-- Pro feature Content. -->
							<p><?php esc_html_e( 'Our Upsell Order Bump plugin will also be fully compatible with different custom checkout plugins or templates of various eCommerce or WooCommerce stores. So, that plugin runs smoothly without any difficulties.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
						</div>
					</div>
				</div>
			</div>
			<!-- Upcoming feature section ends. -->

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
