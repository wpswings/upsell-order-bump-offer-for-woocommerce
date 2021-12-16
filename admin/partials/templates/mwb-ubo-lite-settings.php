<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to set global settings for the plugin.
 *
 * @link       https://makewebbetter.com/?utm_source=MWB-orderbump-backend&utm_medium=MWB-Site-backend&utm_campaign=MWB-backend
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

	$mwb_bump_upsell_global_options['mwb_bump_skip_offer'] = ! empty( $_POST['mwb_bump_skip_offer'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_bump_skip_offer'] ) ) : 'yes';

	$mwb_bump_upsell_global_options['mwb_bump_order_bump_limit'] = ! empty( $_POST['mwb_bump_order_bump_limit'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_bump_order_bump_limit'] ) ) : '1';

	$mwb_bump_upsell_global_options['mwb_ubo_offer_location'] = ! empty( $_POST['mwb_ubo_offer_location'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_ubo_offer_location'] ) ) : '_after_payment_gateways';

	$mwb_bump_upsell_global_options['mwb_ubo_temp_adaption'] = ! empty( $_POST['mwb_ubo_temp_adaption'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_ubo_temp_adaption'] ) ) : 'yes';

	$mwb_bump_upsell_global_options['mwb_ubo_offer_removal'] = ! empty( $_POST['mwb_ubo_offer_removal'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_ubo_offer_removal'] ) ) : 'yes';

	// After version v1.0.2.
	$mwb_bump_upsell_global_options['mwb_ubo_offer_global_css'] = ! empty( $_POST['mwb_ubo_offer_global_css'] ) ? sanitize_textarea_field( wp_unslash( $_POST['mwb_ubo_offer_global_css'] ) ) : '';

	$mwb_bump_upsell_global_options['mwb_ubo_offer_global_js'] = ! empty( $_POST['mwb_ubo_offer_global_js'] ) ? sanitize_textarea_field( wp_unslash( $_POST['mwb_ubo_offer_global_js'] ) ) : '';

	$mwb_bump_upsell_global_options['mwb_ubo_offer_price_html'] = ! empty( $_POST['mwb_ubo_offer_price_html'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_ubo_offer_price_html'] ) ) : '';

	$mwb_bump_upsell_global_options['mwb_ubo_offer_purchased_earlier'] = ! empty( $_POST['mwb_ubo_offer_purchased_earlier'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_ubo_offer_purchased_earlier'] ) ) : 'no';

	$mwb_bump_upsell_global_options['mwb_ubo_offer_restrict_coupons'] = ! empty( $_POST['mwb_ubo_offer_restrict_coupons'] ) ? 'yes' : 'no';

	// After version v2.0.1.
	$mwb_bump_upsell_global_options['mwb_delete_all_on_uninstall'] = ! empty( $_POST['mwb_delete_all_on_uninstall'] ) ? 'on' : 'off';

	$mwb_bump_upsell_global_options['mwb_bump_enable_permalink'] = ! empty( $_POST['mwb_bump_enable_permalink'] ) ? 'on' : 'off';

	$mwb_bump_upsell_global_options['mwb_bump_target_link_attr_val'] = ! empty( $_POST['mwb_bump_target_attr'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_bump_target_attr'] ) ) : 'no';

	// After version v2.0.2.
	$mwb_bump_upsell_global_options['mwb_ubo_exclusive_offer'] = ! empty( $_POST['mwb_ubo_exclusive_offer'] ) ? 'yes' : 'no';

	$mwb_bump_upsell_global_options['mwb_ubo_orderbump_popup'] = ! empty( $_POST['mwb_ubo_orderbump_popup'] ) ? 'yes' : 'no';

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

	// Enable permalink setting.
	$mwb_bump_enable_permalink = ! empty( $mwb_ubo_global_options['mwb_bump_enable_permalink'] ) ? $mwb_ubo_global_options['mwb_bump_enable_permalink'] : '';

	// Selected target attribute value.
	$mwb_bump_target_link_attr_val = ! empty( $mwb_ubo_global_options['mwb_bump_target_link_attr_val'] ) ? $mwb_ubo_global_options['mwb_bump_target_link_attr_val'] : '';

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

	$bump_offer_coupon_restriction = ! empty( $mwb_ubo_global_options['mwb_ubo_offer_restrict_coupons'] ) ? $mwb_ubo_global_options['mwb_ubo_offer_restrict_coupons'] : 'no';

	// Exclusive offer.
	$bump_exclusive_offer = ! empty( $mwb_ubo_global_options['mwb_ubo_exclusive_offer'] ) ? $mwb_ubo_global_options['mwb_ubo_exclusive_offer'] : 'no';

	// Orderbump in popup.
	$mwb_ubo_orderbump_popup = ! empty( $mwb_ubo_global_options['mwb_ubo_orderbump_popup'] ) ? $mwb_ubo_global_options['mwb_ubo_orderbump_popup'] : 'no';

	// Bump Offer limit.
	$mwb_bump_order_bump_limit = ! empty( $mwb_ubo_global_options['mwb_bump_order_bump_limit'] ) ? $mwb_ubo_global_options['mwb_bump_order_bump_limit'] : '1';

	// Delete all data on uninstall.
	$mwb_delete_all_on_uninstall = ! empty( $mwb_ubo_global_options['mwb_delete_all_on_uninstall'] ) ? $mwb_ubo_global_options['mwb_delete_all_on_uninstall'] : 'no';

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

							<input id="mwb_ubo_enable_switch" class="mwb_upsell_bump_enable_plugin_input" type="checkbox" <?php echo ( 'on' === $mwb_bump_enable_plugin ) ? "checked='checked'" : ''; ?> name="mwb_bump_enable_plugin" >	
							<span class="mwb_upsell_bump_enable_plugin_span"></span>

						</label>
					</td>
				</tr>
				<!-- Enable Plugin end. -->

				<!-- Enable Product page link on title and image start. -->
				<tr valign="top">

					<th scope="row" class="titledesc">
						<label for="mwb_bump_enable_permalink  "><?php esc_html_e( 'Enable permalink on product title and image', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">
						<?php
							$attribute_description = esc_html__( 'Enable permalink on product title and image.', 'upsell-order-bump-offer-for-woocommerce' );

							mwb_ubo_lite_help_tip( $attribute_description );
						?>

						<label for="mwb_ubo_enable_permalink_switch" class="mwb_upsell_bump_enable_permalink_label mwb_bump_enable_permalink_support">

							<input id="mwb_ubo_enable_permalink_switch" class="mwb_upsell_bump_enable_permalink_input" type="checkbox" <?php echo ( 'on' === $mwb_bump_enable_permalink ) ? "checked='checked'" : ''; ?> name="mwb_bump_enable_permalink" >	
							<span class="mwb_upsell_bump_enable_permalink_span"></span>

						</label>
					</td>
				</tr>
				<!-- Enable Product page link on title and image end. -->

				<!-- Permalink target location start. -->
				<tr valign="top">

						<th scope="row" class="titledesc">
							<label for="mwb_ubo_permalink_target_attr"><?php esc_html_e( 'Offer product permalink target attribute', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
						</th>

						<td class="forminp forminp-text">
							<?php
								$attribute_description = esc_html__( 'On clicking on the offer product title or image the product page opens on the same page or new page.', 'upsell-order-bump-offer-for-woocommerce' );
								mwb_ubo_lite_help_tip( $attribute_description );
							?>

							<!-- Select options for skipping. -->
							<select id="mwb_ubo_permalink_target_attr" name="mwb_bump_target_attr">

								<option value="_self" <?php selected( $mwb_bump_target_link_attr_val, '_self' ); ?> ><?php esc_html_e( 'Opens in the same frame as it was clicked', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>

								<option value="_blank" <?php selected( $mwb_bump_target_link_attr_val, '_blank' ); ?> ><?php esc_html_e( 'Opens in a new window or tab', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>

							</select>
						</td>

				</tr>
				<!--Permalink target location end. -->

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
						'no'  => esc_html__( 'Keep Offer even When Target is Removed', 'upsell-order-bump-offer-for-woocommerce' ),
					);

					?>

					<td class="forminp forminp-text">

						<?php
							$attribute_description = sprintf(
								'<p><span class="mwb_ubo_description">%s</span> <span class="mwb_ubo_tip_tip">%s</span></p><p><span class="mwb_ubo_description">%s</span> <span class="mwb_ubo_tip_tip">%s</span></p>',
								esc_html__( 'Remove offer when target product is removed:', 'upsell-order-bump-offer-for-woocommerce' ),
								esc_html__( 'As the Target product is removed the offer product associated with the target will also be removed from your customer\'s cart.', 'upsell-order-bump-offer-for-woocommerce' ),
								esc_html__( 'Keep offer when target product is removed:', 'upsell-order-bump-offer-for-woocommerce' ),
								esc_html__( 'As the Target product is removed the offer product associated with the target will be converted into a normal product in your customer\'s cart.', 'upsell-order-bump-offer-for-woocommerce' )
							);
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
						'yes' => esc_html__( 'Free Width', 'upsell-order-bump-offer-for-woocommerce' ),
						'no'  => esc_html__( 'Fixed Width', 'upsell-order-bump-offer-for-woocommerce' ),
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
						'_before_order_summary'      => esc_html__( 'Before Order Summary', 'upsell-order-bump-offer-for-woocommerce' ),
						'_before_payment_gateways'   => esc_html__( 'Before Payment Gateways', 'upsell-order-bump-offer-for-woocommerce' ),
						'_after_payment_gateways'    => esc_html__( 'After Payment Gateways', 'upsell-order-bump-offer-for-woocommerce' ),
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
				if ( mwb_ubo_lite_if_pro_exists() && version_compare( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_VERSION, '1.2.0' ) < 0 ) {

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
						'sale_to_offer'    => sprintf( '%s&nbsp;&nbsp;%s', esc_html__( ' ̶S̶a̶l̶e̶ ̶P̶r̶i̶c̶e̶', 'upsell-order-bump-offer-for-woocommerce' ), esc_html__( 'Offer Price', 'upsell-order-bump-offer-for-woocommerce' ) ),
					);

					?>

					<td class="forminp forminp-text">

						<?php
							$attribute_description = esc_html__( 'Select the way to display Offer Price in Order Bumps.', 'upsell-order-bump-offer-for-woocommerce' );
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

						<label for="mwb_ubo_offer_purchased_earlier"><?php esc_html_e( 'Smart Skip if Already Purchased', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php
							$attribute_description = esc_html__( 'Skip the Order Bump offer for those customers who have already purchased the offer product anytime before in previous orders.', 'upsell-order-bump-offer-for-woocommerce' );
							mwb_ubo_lite_help_tip( $attribute_description );
						?>
						<label class="mwb-upsell-smart-pre-order-skip" for="mwb_ubo_offer_purchased_earlier">
						<input class="mwb-upsell-smart-pre-order-skip-wrap" type='checkbox' <?php echo mwb_ubo_lite_if_pro_exists() && ! empty( $bump_offer_preorder_skip ) && 'yes' === $bump_offer_preorder_skip ? 'checked' : ''; ?> id='mwb_ubo_offer_purchased_earlier' value='yes' name='mwb_ubo_offer_purchased_earlier'>
						<span class="upsell-smart-pre-order-skip-btn"></span>
						</label>
					</td>
				</tr>
				<!-- Pre-order feature skip end. -->

				<!-- Restrict external coupons feature skip start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">

						<span class="mwb_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>

						<label for="mwb_ubo_offer_restrict_coupons"><?php esc_html_e( 'Restrict Woo coupons', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php
							$attribute_description = esc_html__( 'This features removes extra coupon discounts from cart item.', 'upsell-order-bump-offer-for-woocommerce' );
							mwb_ubo_lite_help_tip( $attribute_description );
						?>
						<label class="mwb-upsell-smart-pre-order-skip" for="mwb_ubo_offer_restrict_coupons">
						<input class="mwb-upsell-smart-pre-order-skip-wrap" type='checkbox' <?php echo mwb_ubo_lite_if_pro_exists() && ! empty( $bump_offer_coupon_restriction ) && 'yes' === $bump_offer_coupon_restriction ? 'checked' : ''; ?> id='mwb_ubo_offer_restrict_coupons' value='yes' name='mwb_ubo_offer_restrict_coupons'>
						<span class="upsell-smart-pre-order-skip-btn"></span>
						</label>
					</td>
				</tr>
				<!-- Restrict external coupons feature skip end. -->

				<!-- Exclusive offer start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">

						<span class="mwb_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>

						<label for="mwb_ubo_exclusive_offer"><?php esc_html_e( 'Exclusive offer', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php
							$attribute_description = esc_html__( 'This features removes order bump for the email id which already purchased bump offer.', 'upsell-order-bump-offer-for-woocommerce' );
							mwb_ubo_lite_help_tip( $attribute_description );
						?>
						<label class="mwb-upsell-smart-pre-order-skip" for="mwb_ubo_exclusive_offer">
						<input class="mwb-upsell-smart-pre-order-skip-wrap" type='checkbox' <?php echo mwb_ubo_lite_if_pro_exists() && ! empty( $bump_exclusive_offer ) && 'yes' === $bump_exclusive_offer ? 'checked' : ''; ?> id='mwb_ubo_exclusive_offer' value='yes' name='mwb_ubo_exclusive_offer'>
						<span class="upsell-smart-pre-order-skip-btn"></span>
						</label>
					</td>
				</tr>
				<!-- Exclusive offer end. -->




























				<!-- Exclusive offer start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">

						<span class="mwb_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>

						<label for="mwb_ubo_orderbump_popup"><?php esc_html_e( 'Orderbump in Popup', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php
							$attribute_description = esc_html__( 'Enable to show orderbump in popup on time of checkout.', 'upsell-order-bump-offer-for-woocommerce' );
							mwb_ubo_lite_help_tip( $attribute_description );
						?>
						<label class="mwb-upsell-smart-pre-order-skip" for="mwb_ubo_orderbump_popup">
						<input class="mwb-upsell-smart-pre-order-skip-wrap" type='checkbox' <?php echo mwb_ubo_lite_if_pro_exists() && ! empty( $mwb_ubo_orderbump_popup ) && 'yes' === $mwb_ubo_orderbump_popup ? 'checked' : ''; ?> id='mwb_ubo_orderbump_popup' value='yes' name='mwb_ubo_orderbump_popup'>
						<span class="upsell-smart-pre-order-skip-btn"></span>
						</label>
					</td>
				</tr>
				<!-- Exclusive offer end. -->






















				<!-- Delete all the data on uninstall button html start. -->
				<tr valign="top">

					<th scope="row" class="titledesc">
						<label for="mwb_delete_all_on_uninstall  "><?php esc_html_e( 'Enable Delete all data on uninstall.', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">
						<?php
							$attribute_description = esc_html__( 'Enable Delete all data on uninstall.', 'upsell-order-bump-offer-for-woocommerce' );

							mwb_ubo_lite_help_tip( $attribute_description );
						?>

						<label for="mwb_ubo_enable_switch_delete_data" class="mwb_upsell_bump_enable_deletedata_label mwb_bump_enable_plugin_support">

							<input id="mwb_ubo_enable_switch_delete_data" class="mwb_upsell_bump_enable_plugin_input" type="checkbox" <?php echo ( 'on' === $mwb_delete_all_on_uninstall ) ? "checked='checked'" : ''; ?> name="mwb_delete_all_on_uninstall" >	
							<span class="mwb_upsell_bump_enable_plugin_span"></span>

						</label>
					</td>
				</tr>
				<!-- Delete all the data on uninstall button html end. -->

				<!-- Order Bump Limit start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_bump_order_bump_limit"><?php esc_html_e( 'Multiple Order Bumps Count', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php
							$attribute_description = esc_html__( 'Enter the Multiple Order Bumps count that you want to display on the checkout page.', 'upsell-order-bump-offer-for-woocommerce' );
							mwb_ubo_lite_help_tip( $attribute_description );

						?>

						<input type="number" min="1" id="mwb_bump_order_bump_limit" name="mwb_bump_order_bump_limit" value="<?php echo esc_html( $mwb_bump_order_bump_limit ); ?>">

					</td>
				</tr>
				<!-- Order Bump Limit end. -->

				<!-- Custom CSS start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_ubo_offer_global_css"><?php esc_html_e( 'Global Custom CSS', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php
							$attribute_description = esc_html__( 'Add your Custom CSS without style tags.', 'upsell-order-bump-offer-for-woocommerce' );
							mwb_ubo_lite_help_tip( $attribute_description );
						?>

						<textarea id="mwb_ubo_offer_global_css" name="mwb_ubo_offer_global_css" rows="4" cols="50"><?php echo esc_html( $mwb_ubo_offer_global_css ); ?></textarea>

					</td>
				</tr>
				<!-- Custom CSS end. -->

				<!-- Custom JS start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="mwb_ubo_offer_global_js"><?php esc_html_e( 'Global Custom JS', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php
							$attribute_description = esc_html__( 'Add your Custom JS without script tags.', 'upsell-order-bump-offer-for-woocommerce' );
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
			<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/icons/warning.png' ); ?>">
		</div>
		<!-- Popup body. -->
		<div class="mwb_ubo_update_popup_content">
			<div class="mwb_ubo_update_popup_ques">
				<h5><?php esc_html_e( 'Update Required!', 'upsell-order-bump-offer-for-woocommerce' ); ?></h5>
				<p><?php esc_html_e( "Please Update 'Upsell Order Bump Offer For Woocommerce Pro' to use this feature.", 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
			</div>
			<div class="mwb_ubo_update_popup_option">

				<!-- Update Button button. -->
				<a target="_blank" href="https://makewebbetter.com/my-account/downloads" class="mwb_ubo_update_yes"><?php esc_html_e( 'Update Now', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
				<a href="javascript:void(0);" class="mwb_ubo_update_no"><?php esc_html_e( "Don't update", 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
			</div>
		</div>
	</div>
</div>
