<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to give an overview of pro version and features.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/admin/partials/templates
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<!-- Plugin Overview Template. -->
<div id="mwb_ubo_lite_overview">
	<div class="mwb_ubo_lite_intro_section">
		<div id="mwb_ubo_lite_video_title">
			<h2><?php esc_html_e( 'How Order Bump Offer Works and How to Set it Up', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2>
		</div>
	</div>

	<hr>

	<div id="mwb_ubo_lite_overview_video">
		<iframe width="100%" height="500" src="https://www.youtube.com/embed/7K4BFDqX-dw" frameborder="0" allowfullscreen=""></iframe>
	</div>

	<hr>

	<div class="mwb_ubo_lite_overview_pro_version mwb_ubo_lite_go_pro_feature_first">
		<h2><?php esc_html_e( 'What is Upsell Order Bump Offer for WooCommerce?', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2>

		<p><?php esc_html_e( "Upsell Order Bump Offer for WooCommerce allows its users to show exclusive special one time offers known as Order Bumps on the checkout page. These Order Bumps can be accepted in just a single click which is added instantly to customer's existing Order.", 'upsell-order-bump-offer-for-woocommerce' ); ?></p>

		<p><?php esc_html_e( 'The Store owner can set the offers specifically for Target Products or Categories so that relevant Order Bumps can be offered which are hard to resist.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>

		<ul class='mwb_ubo_lite_feature_list'>
			<li><span class='mwb_ubo_lite_feature_content'><?php esc_html_e( 'Capture Customer’s attention by displaying appealing offers on the checkout page.', 'upsell-order-bump-offer-for-woocommerce' ); ?></span></li>
			<li><span class='mwb_ubo_lite_feature_content'><?php esc_html_e( 'Develop Interest for the offer by mentioning how the offer will benefit them on their existing purchase.', 'upsell-order-bump-offer-for-woocommerce' ); ?></span></li>
			<li><span class='mwb_ubo_lite_feature_content'><?php esc_html_e( 'Convince them that this is an Exclusive offer only available here at this Discount.', 'upsell-order-bump-offer-for-woocommerce' ); ?></span></li>
			<li><span class='mwb_ubo_lite_feature_content'><?php esc_html_e( 'Ask them to Act and Accept the offer.', 'upsell-order-bump-offer-for-woocommerce' ); ?></span></li>
		</ul>
	</div>

	<?php if ( ! mwb_ubo_lite_is_plugin_active( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php' ) ) : ?>

		<div class="mwb_ubo_lite_overview_pro_version">
			<h2><?php esc_html_e( 'Premium Plugin Additional Features', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2>

			<!-- Premium feature section starts. -->
			<div class="mwb_ubo_lite_overview_go_pro">
				<div class="mwb_ubo_lite_go_pro_features">
					<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/Icons/bumps.png' ); ?> ">
					<div class="mwb_ubo_lite_go_pro_features_wrap">
						<!-- Pro feature heading. -->
						<h3><?php esc_html_e( 'Unlock the power to create unlimited Order Bumps', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
						<!-- Pro feature Content. -->
						<p><?php esc_html_e( 'With our Free version, you have limited access to create just one Order Bump.', 'upsell-order-bump-offer-for-woocommerce' ); ?><br>
						<?php esc_html_e( 'So, get our Premium version today to create multiple Order Bumps. Introduce your customers to the most relevant offers which will benefit them on their existing purchase. Give your sales a great boost ahead.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
					</div>
				</div>

				<div class="mwb_ubo_lite_go_pro_features">
					<div class="mwb_ubo_lite_go_pro_features_wrap mwb_ubo_lite_premium_content">
						<!-- Pro feature Content. -->
						<h3><?php esc_html_e( 'Get Premium Support', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
						<!-- Pro feature heading. -->
						<p>
						<?php
						esc_html_e(
							'Receive dedicated 24*7 Phone, Email & Skype support.',
							'upsell-order-bump-offer-for-woocommerce'
						);
						?>
						<br>
						<?php esc_html_e( "Our Support is ready to assist you regarding any query, issue or feature request and if that doesn't help our Technical team will connect with you personally and have your query resolved.", 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
					</div>
					<img class="mwb_ubo_lite_premium_support" src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/Icons/customer_support.png' ); ?> ">
				</div>
			</div>
			<!-- Premium feature section ends. -->

			<!-- Go pro section starts. -->
			<div class="mwb_ubo_lite_go_pro_popup_button">
				<a class="button mwb_ubo_lite_overview_go_pro_button" target="_blank" href="https://makewebbetter.com/product/woocommerce-upsell-order-bump-offer-pro/?utm_source=mwb-ubo-lite-org&utm_medium=Overview&utm_campaign=ORG">
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
