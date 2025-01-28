<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to set global settings for the plugin.
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

// Save settings on Save changes.
if ( isset( $_POST['wps_upsell_bump_common_settings_save_general'] ) ) {

	// Nonce verification.
	check_admin_referer( 'wps_upsell_bump_settings_nonce', 'wps_upsell_bump_nonce' );

	$wps_bump_upsell_global_options = array();

	// Enable Plugin.
	$wps_bump_upsell_global_options['wps_bump_enable_plugin'] = ! empty( $_POST['wps_bump_enable_plugin'] ) ? 'on' : 'off';

	$wps_bump_upsell_global_options['wps_bump_skip_offer'] = ! empty( $_POST['wps_bump_skip_offer'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_bump_skip_offer'] ) ) : 'yes'; 


	$wps_bump_upsell_global_options['wps_ubo_offer_price_html'] = ! empty( $_POST['wps_ubo_offer_price_html'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_ubo_offer_price_html'] ) ) : ''; 


	$wps_bump_upsell_global_options['wps_ubo_offer_purchased_earlier'] = ! empty( $_POST['wps_ubo_offer_purchased_earlier'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_ubo_offer_purchased_earlier'] ) ) : 'no'; 

	
	// SAVE GLOBAL OPTIONS.
	update_option( 'wps_ubo_global_options', $wps_bump_upsell_global_options );

	?>
	<!-- Settings saved notice. -->
	<div class="notice notice-success is-dismissible wps-notice">
		<p><strong><?php esc_html_e( 'Settings saved', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong></p>
	</div>

	<?php
}

// Saved Global Options.
$wps_ubo_global_options = get_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );

// echo '<pre>';
// print_r($wps_ubo_global_options);
// echo '</pre>';

// By default plugin will be enabled.
$wps_bump_enable_plugin = ! empty( $wps_ubo_global_options['wps_bump_enable_plugin'] ) ? $wps_ubo_global_options['wps_bump_enable_plugin'] : ''; 


// Bump Offer skip.
$wps_bump_enable_skip = ! empty( $wps_ubo_global_options['wps_bump_skip_offer'] ) ? $wps_ubo_global_options['wps_bump_skip_offer'] : ''; 


$bump_offer_price_html = ! empty( $wps_ubo_global_options['wps_ubo_offer_price_html'] ) ? $wps_ubo_global_options['wps_ubo_offer_price_html'] : 'regular_to_offer'; 

$bump_offer_preorder_skip = ! empty( $wps_ubo_global_options['wps_ubo_offer_purchased_earlier'] ) ? $wps_ubo_global_options['wps_ubo_offer_purchased_earlier'] : 'no';
?>
<form action="" method="POST">

	<!-- Settings starts -->
	<div class="wps_upsell_table wps_upsell_table--border">
		<table class="form-table wps_upsell_bump_creation_setting">
			<tbody>

				<!-- Nonce field here. -->
				<?php wp_nonce_field( 'wps_upsell_bump_settings_nonce', 'wps_upsell_bump_nonce' ); ?>

				<?php if ( ! wps_ubo_lite_if_pro_exists() ) : ?>
					<input type='hidden' id='wps_ubo_pro_status' value='inactive'>
				<?php endif; ?>

				<!-- Enable Plugin start. -->
				<tr valign="top">

					<th scope="row" class="titledesc">
						<label for="wps_bump_enable_plugin  "><?php esc_html_e( 'Enable Upsell Order Bumps', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">
						<?php
						$attribute_description = esc_html__( 'Enable Upsell Order Bump Offer plugin.', 'upsell-order-bump-offer-for-woocommerce' );

						wps_ubo_lite_help_tip( $attribute_description );
						?>

						<label for="wps_ubo_enable_switch" class="wps_upsell_bump_enable_plugin_label wps_bump_enable_plugin_support">

							<input id="wps_ubo_enable_switch" class="wps_upsell_bump_enable_plugin_input" type="checkbox" <?php echo ( 'on' === $wps_bump_enable_plugin ) ? "checked='checked'" : ''; ?> name="wps_bump_enable_plugin">
							<span class="wps_upsell_bump_enable_plugin_span"></span>

						</label>
					</td>
				</tr>
				<!-- Enable Plugin end. -->

				<!-- Skip offer start. -->
				<tr valign="top">

					<th scope="row" class="titledesc">
						<label for="wps_ubo_skip_offer"><?php esc_html_e( 'Skip for Same Offers', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">
						<?php
						$attribute_description = esc_html__( 'Skip Bump offer if offer product is already present in cart.', 'upsell-order-bump-offer-for-woocommerce' );
						wps_ubo_lite_help_tip( $attribute_description );
						?>

						<!-- Select options for skipping. -->
						<select id="wps_ubo_skip_offer" name="wps_bump_skip_offer">

							<option value="yes" <?php selected( $wps_bump_enable_skip, 'yes' ); ?>><?php esc_html_e( 'Yes', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>

							<option value="no" <?php selected( $wps_bump_enable_skip, 'no' ); ?>><?php esc_html_e( 'No', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>

						</select>
					</td>

				</tr>
				<!--Skip offer end. -->

				<!-- Features after v1.0.2 -->

				<!-- Add version compare to dependent plugin -->
				<?php
				$is_update_needed = 'false';
				if ( wps_ubo_lite_if_pro_exists() && version_compare( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_VERSION, '1.2.0' ) < 0 ) {

					$is_update_needed = 'true';
				}
				?>
				<input type="hidden" id="is_pro_update_needed" value="<?php echo esc_html( $is_update_needed ); ?>">

				<!-- Price html start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="wps_ubo_offer_price_html"><?php esc_html_e( 'Offer Price Format', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<?php

					$offer_locations_array = array(
						'regular_to_offer' => sprintf( '%s&nbsp;&nbsp;%s', esc_html__( '̶R̶e̶g̶u̶l̶a̶r̶ ̶P̶r̶i̶c̶e̶', 'upsell-order-bump-offer-for-woocommerce' ), esc_html__( 'Offer Price', 'upsell-order-bump-offer-for-woocommerce' ) ),
						'sale_to_offer'    => sprintf( '%s&nbsp;&nbsp;%s', esc_html__( ' ̶S̶a̶l̶e̶ ̶P̶r̶i̶c̶e̶', 'upsell-order-bump-offer-for-woocommerce' ), esc_html__( 'Offer Price', 'upsell-order-bump-offer-for-woocommerce' ) ),
					);

					?>

					<td class="forminp forminp-text">

						<?php
						$attribute_description = esc_html__( 'Select the way to display Offer Price in Order Bumps.', 'upsell-order-bump-offer-for-woocommerce' );
						wps_ubo_lite_help_tip( $attribute_description );
						?>

						<select id="wps_ubo_offer_price_html" name="wps_ubo_offer_price_html">

							<?php foreach ( $offer_locations_array as $key => $value ) : ?>

								<option <?php selected( $bump_offer_price_html, $key ); ?> value="<?php echo esc_html( $key ); ?>"><?php echo esc_html( $value ); ?></option>

							<?php endforeach; ?>

						</select>
					</td>
				</tr>
				<!-- Price html end. -->

				<!-- Pre-order feature skip start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">

						<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>

						<label for="wps_ubo_offer_purchased_earlier"><?php esc_html_e( 'Smart Skip if Already Purchased', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php
						$attribute_description = esc_html__( 'Skip the Order Bump offer for those customers who have already purchased the offer product anytime before in previous orders.', 'upsell-order-bump-offer-for-woocommerce' );
						wps_ubo_lite_help_tip( $attribute_description );
						?>
						<label class="wps-upsell-smart-pre-order-skip" for="wps_ubo_offer_purchased_earlier">
							<input class="wps-upsell-smart-pre-order-skip-wrap" type='checkbox' <?php echo wps_ubo_lite_if_pro_exists() && ! empty( $bump_offer_preorder_skip ) && 'yes' === $bump_offer_preorder_skip ? 'checked' : ''; ?> id='wps_ubo_offer_purchased_earlier' value='yes' name='wps_ubo_offer_purchased_earlier'>
							<span class="upsell-smart-pre-order-skip-btn"></span>
						</label>
					</td>
				</tr>
				<!-- Pre-order feature skip end. -->
			</tbody>
		</table>
	</div>
	<!-- Settings ends -->

	<!-- Save Settings -->
	<p class="submit">
		<input type="submit" value="<?php esc_html_e( 'Save Changes', 'upsell-order-bump-offer-for-woocommerce' ); ?>" class="button-primary woocommerce-save-button" name="wps_upsell_bump_common_settings_save_general" id="wps_upsell_bump_creation_setting_save">
	</p>
</form>

<!-- After v1.0.2 -->
<!-- Adding go pro popup here. -->

<?php wps_ubo_go_pro( 'pro' ); ?>

<!-- Update required Popup -->
<div class="wps_ubo_update_popup_wrapper">
	<div class="wps_ubo_update_popup_inner">
		<!-- Popup icon -->
		<div class="wps_ubo_update_popup_head">
			<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/icons/warning.png' ); ?>">
		</div>
		<!-- Popup body. -->
		<div class="wps_ubo_update_popup_content">
			<div class="wps_ubo_update_popup_ques">
				<h5><?php esc_html_e( 'Update Required!', 'upsell-order-bump-offer-for-woocommerce' ); ?></h5>
				<p><?php esc_html_e( "Please Update 'Upsell Order Bump Offer For Woocommerce Pro' to use this feature.", 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
			</div>
			<div class="wps_ubo_update_popup_option">

				<!-- Update Button button. -->
				<a target="_blank" href="https://wpswings.com/my-account/downloads" class="wps_ubo_update_yes"><?php esc_html_e( 'Update Now', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
				<a href="javascript:void(0);" class="wps_ubo_update_no"><?php esc_html_e( "Don't update", 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
			</div>
		</div>
	</div>
</div>
