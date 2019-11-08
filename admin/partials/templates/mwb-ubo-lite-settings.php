<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to set global settings for the plugin.
 *
 * @link       https://makewebbetter.com/
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
if ( isset( $_POST['mwb_upsell_bump_common_settings_save'] ) ) {

	// Nonce verification.
	check_admin_referer( 'mwb_upsell_bump_settings_nonce', 'mwb_upsell_bump_nonce' );

	$mwb_bump_upsell_global_options = array();

	// Enable Plugin.
	$mwb_bump_upsell_global_options['mwb_bump_enable_plugin'] = ! empty( $_POST['mwb_bump_enable_plugin'] ) ? 'on' : 'off';

	$mwb_bump_upsell_global_options['mwb_bump_skip_offer'] = ! empty( $_POST['mwb_bump_skip_offer'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_bump_skip_offer'] ) ) : esc_html__( 'yes', 'upsell-order-bump-offer-for-woocommerce' );

	$mwb_bump_upsell_global_options['mwb_ubo_offer_location'] = ! empty( $_POST['mwb_ubo_offer_location'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_ubo_offer_location'] ) ) : esc_html__( '_after_payment_gateways', 'upsell-order-bump-offer-for-woocommerce' );

	$mwb_bump_upsell_global_options['mwb_ubo_temp_adaption'] = ! empty( $_POST['mwb_ubo_temp_adaption'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_ubo_temp_adaption'] ) ) : esc_html__( 'yes', 'upsell-order-bump-offer-for-woocommerce' );

	$mwb_bump_upsell_global_options['mwb_ubo_offer_removal'] = ! empty( $_POST['mwb_ubo_offer_removal'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_ubo_offer_removal'] ) ) : esc_html__( 'yes', 'upsell-order-bump-offer-for-woocommerce' );

	// After version v1.0.2.
	$mwb_bump_upsell_global_options['mwb_ubo_offer_global_css'] = ! empty( $_POST['mwb_ubo_offer_global_css'] ) ? sanitize_textarea_field( wp_unslash( $_POST['mwb_ubo_offer_global_css'] ) ) : '';

	$mwb_bump_upsell_global_options['mwb_ubo_offer_global_js'] = ! empty( $_POST['mwb_ubo_offer_global_js'] ) ? sanitize_textarea_field( wp_unslash( $_POST['mwb_ubo_offer_global_js'] ) ) : '';

	$mwb_bump_upsell_global_options['mwb_ubo_offer_price_html'] = ! empty( $_POST['mwb_ubo_offer_price_html'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_ubo_offer_price_html'] ) ) : '';

	$mwb_bump_upsell_global_options['mwb_ubo_offer_purchased_earlier'] = ! empty( $_POST['mwb_ubo_offer_purchased_earlier'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_ubo_offer_purchased_earlier'] ) ) : esc_html__( 'no', 'upsell-order-bump-offer-for-woocommerce' );

	// SAVE GLOBAL OPTIONS.
	update_option( 'mwb_ubo_global_options', $mwb_bump_upsell_global_options );

	?>
	<!-- Settings saved notice. -->
	<div class="notice notice-success is-dismissible mwb-notice"> 
		<p><strong><?php esc_html_e( 'Settings saved', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong></p>
	</div>

	<?php
}

	// Saved Global Options.
	$mwb_ubo_global_options = get_option( 'mwb_ubo_global_options', mwb_ubo_lite_default_global_options() );

	// By default plugin will be enabled.
	$mwb_bump_enable_plugin = ! empty( $mwb_ubo_global_options['mwb_bump_enable_plugin'] ) ? $mwb_ubo_global_options['mwb_bump_enable_plugin'] : '';

	// Bump Offer skip.
	$mwb_bump_enable_skip = ! empty( $mwb_ubo_global_options['mwb_bump_skip_offer'] ) ? $mwb_ubo_global_options['mwb_bump_skip_offer'] : '';

	// Bump Offer remove.
	$mwb_ubo_offer_removal = ! empty( $mwb_ubo_global_options['mwb_ubo_offer_removal'] ) ? $mwb_ubo_global_options['mwb_ubo_offer_removal'] : '';

	$mwb_ubo_temp_adaption = ! empty( $mwb_ubo_global_options['mwb_ubo_temp_adaption'] ) ? $mwb_ubo_global_options['mwb_ubo_temp_adaption'] : 'yes';

	// Bump Offer location.
	$bump_offer_location = ! empty( $mwb_ubo_global_options['mwb_ubo_offer_location'] ) ? $mwb_ubo_global_options['mwb_ubo_offer_location'] : '';

	$mwb_ubo_offer_global_css = ! empty( $mwb_ubo_global_options['mwb_ubo_offer_global_css'] ) ? $mwb_ubo_global_options['mwb_ubo_offer_global_css'] : '';

	$mwb_ubo_offer_global_js = ! empty( $mwb_ubo_global_options['mwb_ubo_offer_global_js'] ) ? $mwb_ubo_global_options['mwb_ubo_offer_global_js'] : '';

	$bump_offer_price_html = ! empty( $mwb_ubo_global_options['mwb_ubo_offer_price_html'] ) ? $mwb_ubo_global_options['mwb_ubo_offer_price_html'] : 'regular_to_offer';

	$bump_offer_preorder_skip = ! empty( $mwb_ubo_global_options['mwb_ubo_offer_purchased_earlier'] ) ? $mwb_ubo_global_options['mwb_ubo_offer_purchased_earlier'] : 'no';

?>

<form action="" method="POST">

	<!-- Settings starts -->
	<div class="mwb_upsell_table mwb_upsell_table--border">
		<table class="form-table mwb_upsell_bump_creation_setting">
			<tbody>

				<!-- Nonce field here. -->
				<?php wp_nonce_field( 'mwb_upsell_bump_settings_nonce', 'mwb_upsell_bump_nonce' ); ?>

				<?php if ( ! mwb_ubo_lite_if_pro_exists() ) : ?>
					<input type='hidden' id='mwb_ubo_pro_status' value='inactive'>
				<?php endif; ?>

				<!-- Enable Plugin start. -->
				<tr valign="top">

					<th scope="row" class="titledesc">
						<label for="mwb_bump_enable_plugin  "><?php esc_html_e( 'Enable Upsell Order Bumps', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">
						<?php
							$attribute_description = esc_html__( 'Enable Upsell Order Bump Offer plugin.', 'upsell-order-bump-offer-for-woocommerce' );

							mwb_ubo_lite_help_tip( $attribute_description );
						?>

						<label for="mwb_ubo_enable_switch" class="mwb_upsell_bump_enable_plugin_label mwb_bump_enable_plugin_support">

							<input id="mwb_ubo_enable_switch" class="mwb_upsell_bump_enable_plugin_input" type="checkbox" <?php echo ( 'on' == $mwb_bump_enable_plugin ) ? "checked='checked'" : ''; ?> name="mwb_bump_enable_plugin" >	
							<span class="mwb_upsell_bump_enable_plugin_span"></span>

						</label>
					</td>
				</tr>
				<!-- Enable Plugin end. -->

				<!-- Skip offer start. -->
				<tr valign="top">

					<th scope="row" class="titledesc">
						<label for="mwb_ubo_skip_offer"><?php esc_html_e( 'Skip for Same Offers', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">
						<?php
							$attribute_description = esc_html__( 'Skip Bump offer if offer product is already present in cart.', 'upsell-order-bump-offer-for-woocommerce' );
							mwb_ubo_lite_help_tip( $attribute_description );
						?>

						<!-- Select options for skipping. -->
						<select id="mwb_ubo_skip_offer" name="mwb_bump_skip_offer">

							<option value="yes" <?php selected( $mwb_bump_enable_skip, 'yes' ); ?> ><?php esc_html_e( 'Yes', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>

							<option value="no" <?php selected( $mwb_bump_enable_skip, 'no' ); ?> ><?php esc_html_e( 'No', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>

						</select>		
					</td>

				</tr>
				<!--Skip offer end. -->

				<!-- Offer removal on target removal starts. -->
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_ubo_offer_removal_select"><?php esc_html_e( 'Offer Target Dependency', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<?php

					$mwb_ubo_offer_removal_options = array(
						'yes' => esc_html__( 'Remove Offer When Target Product is Removed', 'upsell-order-bump-offer-for-woocommerce' ),
						'no' => esc_html__( 'Keep Offer even When Target is Removed', 'upsell-order-bump-offer-for-woocommerce' ),
					);

					?>

					<td class="forminp forminp-text">

						<?php
							$attribute_description = esc_html__( 'Choose if Bump Offer product should be removed if Target product is removed from Cart page.', 'upsell-order-bump-offer-for-woocommerce' );
							mwb_ubo_lite_help_tip( $attribute_description );
						?>

						<select id="mwb_ubo_offer_removal_select" name="mwb_ubo_offer_removal" >

							<?php foreach ( $mwb_ubo_offer_removal_options as $key => $value ) : ?>

								<option <?php selected( $mwb_ubo_offer_removal, $key ); ?> value="<?php echo esc_html( $key ); ?>"><?php echo esc_html( $value ); ?></option>
								
							<?php endforeach; ?>

						</select>

					</td>
				</tr>
				<!-- Offer removal on target removal ends. -->

				<!-- Template adaption starts. -->
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_ubo_temp_adaption_select"><?php esc_html_e( 'Offer Adaption settings', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<?php

					$mwb_ubo_temp_adaptions_options = array(
						'yes'   => esc_html__( 'Free Width', 'upsell-order-bump-offer-for-woocommerce' ),
						'no'    => esc_html__( 'Fixed Width', 'upsell-order-bump-offer-for-woocommerce' ),
					);

					?>

					<td class="forminp forminp-text">

						<?php
							$attribute_description = esc_html__( 'If Free Width, the Order Bump Offer will adapt to the complete width of it\'s parent location area else it will be fixed.', 'upsell-order-bump-offer-for-woocommerce' );
							mwb_ubo_lite_help_tip( $attribute_description );
						?>

						<select id="mwb_ubo_temp_adaption_select" name="mwb_ubo_temp_adaption" >

							<?php foreach ( $mwb_ubo_temp_adaptions_options as $key => $value ) : ?>

								<option <?php selected( $mwb_ubo_temp_adaption, $key ); ?> value="<?php echo esc_html( $key ); ?>"><?php echo esc_html( $value ); ?></option>
								
							<?php endforeach; ?>

						</select>

					</td>
				</tr>
				<!-- Template adaption ends. -->

				<!-- Offer location start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_ubo_offer_location"><?php esc_html_e( 'Offer Location', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<?php

					$offer_locations_array = array(
						'_before_order_summary' => esc_html__( 'Before Order Summary', 'upsell-order-bump-offer-for-woocommerce' ),
						'_before_payment_gateways' => esc_html__( 'Before Payment Gateways', 'upsell-order-bump-offer-for-woocommerce' ),
						'_after_payment_gateways' => esc_html__( 'After Payment Gateways', 'upsell-order-bump-offer-for-woocommerce' ),
						'_before_place_order_button' => esc_html__( 'Before Place Order Button', 'upsell-order-bump-offer-for-woocommerce' ),
					);

					?>

					<td class="forminp forminp-text">

						<?php
							$attribute_description = esc_html__( 'Choose the location where the Bump Offer will be displayed on the Checkout page.', 'upsell-order-bump-offer-for-woocommerce' );
							mwb_ubo_lite_help_tip( $attribute_description );
						?>

						<select id="mwb_ubo_offer_location" name="mwb_ubo_offer_location" >

							<?php foreach ( $offer_locations_array as $key => $value ) : ?>

								<option <?php selected( $bump_offer_location, $key ); ?> value="<?php echo esc_html( $key ); ?>"><?php echo esc_html( $value ); ?></option>	
								
							<?php endforeach; ?>

						</select>

					</td>
				</tr>
				<!-- Offer location end. -->

				<!-- Features after v1.0.2 -->

				<!-- Add version compare to dependent plugin -->
				<?php
					$is_update_needed = 'false';
					if( mwb_ubo_lite_if_pro_exists() && version_compare( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_VERSION, '1.2.0' ) < 0 ) {

						$is_update_needed = 'true';
					}
				?>
				<input type="hidden" id="is_pro_update_needed" value="<?php echo esc_html( $is_update_needed ); ?>">

				<!-- Price html start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_ubo_offer_price_html"><?php esc_html_e( 'Offer Price Format', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<?php

					$offer_locations_array = array(
						'regular_to_offer' => sprintf( '%s&nbsp;&nbsp;%s', esc_html__( '̶R̶e̶g̶u̶l̶a̶r̶ ̶P̶r̶i̶c̶e̶', 'upsell-order-bump-offer-for-woocommerce' ), esc_html__( 'Offer Price', 'upsell-order-bump-offer-for-woocommerce' ) ),
						'sale_to_offer' => sprintf( '%s&nbsp;&nbsp;%s', esc_html__( ' ̶S̶a̶l̶e̶ ̶P̶r̶i̶c̶e̶', 'upsell-order-bump-offer-for-woocommerce' ), esc_html__( 'Offer Price', 'upsell-order-bump-offer-for-woocommerce' ) ),
					);

					?>

					<td class="forminp forminp-text">

						<?php
							$attribute_description = esc_html__( 'Select the format to show the offer price in order bump.', 'upsell-order-bump-offer-for-woocommerce' );
							mwb_ubo_lite_help_tip( $attribute_description );
						?>

						<select id="mwb_ubo_offer_price_html" name="mwb_ubo_offer_price_html">

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

						<span class="mwb_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>

						<label for="mwb_ubo_offer_purchased_earlier"><?php esc_html_e( 'Smart Pre-order Skip', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php
							$attribute_description = esc_html__( 'This feature allows you to skip the order bump if the offer product is already purchased in previous orders.' );
							mwb_ubo_lite_help_tip( $attribute_description );
						?>
						<label class="mwb-upsell-smart-pre-order-skip" for="mwb_ubo_offer_purchased_earlier">
						<input class="mwb-upsell-smart-pre-order-skip-wrap" type='checkbox' <?php echo mwb_ubo_lite_if_pro_exists() && ! empty( $bump_offer_preorder_skip ) && 'yes' == $bump_offer_preorder_skip ? 'checked' : ''; ?> id='mwb_ubo_offer_purchased_earlier' value='yes' name='mwb_ubo_offer_purchased_earlier'>
						<span class="upsell-smart-pre-order-skip-btn"></span>
						</label>
					</td>
				</tr>
				<!-- Pre-order feature skip end. -->

				<!-- Custom CSS start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_ubo_offer_global_css"><?php esc_html_e( 'Custom CSS', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php
							$attribute_description = esc_html__( 'Add your custom CSS here. <br> Do not write style tags.', 'upsell-order-bump-offer-for-woocommerce' );
							mwb_ubo_lite_help_tip( $attribute_description );
						?>

						<textarea id="mwb_ubo_offer_global_css" name="mwb_ubo_offer_global_css" rows="4" cols="50"><?php echo esc_html( $mwb_ubo_offer_global_css ); ?></textarea>

					</td>
				</tr>
				<!-- Custom CSS end. -->

				<!-- Custom JS start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_ubo_offer_global_js"><?php esc_html_e( 'Custom JS', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php
							$attribute_description = esc_html__( 'Add your custom JS here. <br> Do not write scripts tags.', 'upsell-order-bump-offer-for-woocommerce' );
							mwb_ubo_lite_help_tip( $attribute_description );
						?>

						<textarea id="mwb_ubo_offer_global_js" name="mwb_ubo_offer_global_js" rows="4" cols="50"><?php echo esc_html( $mwb_ubo_offer_global_js ); ?></textarea>

					</td>
				</tr>
				<!-- Custom JS end. -->

			</tbody>
		</table>
	</div>
	<!-- Settings ends -->

	<!-- Save Settings -->
	<p class="submit">
		<input type="submit" value="<?php esc_html_e( 'Save Changes', 'upsell-order-bump-offer-for-woocommerce' ); ?>" class="button-primary woocommerce-save-button" name="mwb_upsell_bump_common_settings_save" id="mwb_upsell_bump_creation_setting_save" >
	</p>
</form>

<!-- After v1.0.2 -->
<!-- Adding go pro popup here. -->

<?php mwb_ubo_go_pro( 'pro' ); ?>

<!-- Update required Popup -->
<div class="mwb_ubo_update_popup_wrapper">
	<div class="mwb_ubo_update_popup_inner">
		<!-- Popup icon -->
		<div class="mwb_ubo_update_popup_head">
			<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/Icons/warning.png' ); ?>">
		</div>
		<!-- Popup body. -->
		<div class="mwb_ubo_update_popup_content">
			<div class="mwb_ubo_update_popup_ques">
				<h5><?php esc_html_e( 'This feature is Unavailable!', 'upsell-order-bump-offer-for-woocommerce-pro' ); ?></h5>
				<p><?php esc_html_e( 'Currrent version of UPSELL ORDER BUMP OFFER FOR WOOCOMMERCE PRO is out of date. Please update to the new version to use this feature.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
			</div>
			<div class="mwb_ubo_update_popup_option">

				<!-- Update Button button. -->
				<a target="_blank" href="https://makewebbetter.com/my-account/downloads" class="mwb_ubo_update_yes"><?php esc_html_e( 'Update Now', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
				<a href="javascript:void(0);" class="mwb_ubo_update_no"><?php esc_html_e( "Don't update", 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
			</div>
		</div>
	</div>
</div>