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
if ( isset( $_POST['wps_upsell_bump_common_settings_save_pre_global'] ) ) {

	// Nonce verification.
	check_admin_referer( 'wps_upsell_bump_settings_nonce', 'wps_upsell_bump_nonce' );

	$wps_bump_upsell_global_options = get_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );

	$wps_bump_upsell_global_options['wps_bump_order_bump_limit'] = ! empty( $_POST['wps_bump_order_bump_limit'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_bump_order_bump_limit'] ) ) : '1';

	$wps_bump_upsell_global_options['wps_custom_order_success_page'] = ! empty( $_POST['wps_custom_order_success_page'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_custom_order_success_page'] ) ) : '';

	$wps_bump_upsell_global_options['wps_ubo_offer_location'] = ! empty( $_POST['wps_ubo_offer_location'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_ubo_offer_location'] ) ) : '_after_payment_gateways';

	$wps_bump_upsell_global_options['wps_ubo_temp_adaption'] = ! empty( $_POST['wps_ubo_temp_adaption'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_ubo_temp_adaption'] ) ) : 'yes';

	$wps_bump_upsell_global_options['wps_ubo_offer_removal'] = ! empty( $_POST['wps_ubo_offer_removal'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_ubo_offer_removal'] ) ) : 'yes';

	// After version v1.0.2.
	$wps_bump_upsell_global_options['wps_ubo_offer_global_css'] = ! empty( $_POST['wps_ubo_offer_global_css'] ) ? sanitize_textarea_field( wp_unslash( $_POST['wps_ubo_offer_global_css'] ) ) : '';

	$wps_bump_upsell_global_options['wps_ubo_offer_global_js'] = ! empty( $_POST['wps_ubo_offer_global_js'] ) ? sanitize_textarea_field( wp_unslash( $_POST['wps_ubo_offer_global_js'] ) ) : '';

	$wps_bump_upsell_global_options['wps_ubo_offer_fbt_location_set'] = ! empty( $_POST['wps_ubo_offer_fbt_location_set'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_ubo_offer_fbt_location_set'] ) ) : '';

	$wps_bump_upsell_global_options['wps_ubo_offer_restrict_coupons'] = ! empty( $_POST['wps_ubo_offer_restrict_coupons'] ) ? 'yes' : 'no';

	// After version v2.0.1.
	$wps_bump_upsell_global_options['wps_delete_all_on_uninstall'] = ! empty( $_POST['wps_delete_all_on_uninstall'] ) ? 'on' : 'off';

	$wps_bump_upsell_global_options['wps_bump_enable_permalink'] = ! empty( $_POST['wps_bump_enable_permalink'] ) ? 'on' : 'off';

	// In Version 2.1.9.
	$wps_bump_upsell_global_options['wps_bump_target_link_attr_val'] = ! empty( $_POST['wps_bump_target_attr'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_bump_target_attr'] ) ) : 'no';

	// Enable fluent crm integration.
	$wps_bump_upsell_global_options['wps_ubo_enable_fluentcrm'] = ! empty( $_POST['wps_ubo_enable_fluentcrm'] ) ? 'on' : 'off';


	$wps_bump_upsell_global_options['wps_bump_popup_bump_offer'] = ! empty( $_POST['wps_bump_popup_bump_offer'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_bump_popup_bump_offer'] ) ) : 'no';
	// After v2.1.2.
	$wps_bump_upsell_global_options['wps_enable_red_arrow_feature'] = ! empty( $_POST['wps_enable_red_arrow_feature'] ) ? 'on' : 'off';

	// After v2.2.7 In PRO.
	$wps_bump_upsell_global_options['wps_enable_cart_upsell_feature'] = ! empty( $_POST['wps_enable_cart_upsell_feature'] ) ? 'on' : 'off';
	$wps_bump_upsell_global_options['wps_enable_fbt_upsell_feature'] = ! empty( $_POST['wps_enable_fbt_upsell_feature'] ) ? 'on' : 'off';
	$wps_bump_upsell_global_options['wps_enable_cart_upsell_location'] = ! empty( $_POST['wps_enable_cart_upsell_location'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_enable_cart_upsell_location'] ) ) : 'woocommerce_before_cart_totals';
	$wps_bump_upsell_global_options['wps_ubo_enable_popup_exit_intent'] = ! empty( $_POST['wps_ubo_enable_popup_exit_intent'] ) ? 'on' : 'off';

	$wps_bump_upsell_global_options['wps_ubo_offer_ab_method'] = ! empty( $_POST['wps_ubo_offer_ab_method'] ) ? 'on' : 'off';

	$wps_bump_upsell_global_options['wps_upsell_bump_target_ids_popup'] = ! empty( $_POST['wps_upsell_bump_target_ids_popup'] ) ? map_deep( wp_unslash( $_POST['wps_upsell_bump_target_ids_popup'] ), 'sanitize_text_field' ) : array();

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


// Bump offer showing setting.
$wps_upsell_bump_target_ids_popup = ! empty( $wps_ubo_global_options['wps_upsell_bump_target_ids_popup'] ) ? $wps_ubo_global_options['wps_upsell_bump_target_ids_popup'] : '';

// Enable permalink setting.
$wps_bump_enable_permalink = ! empty( $wps_ubo_global_options['wps_bump_enable_permalink'] ) ? $wps_ubo_global_options['wps_bump_enable_permalink'] : '';

// Selected target attribute value.
$wps_bump_target_link_attr_val = ! empty( $wps_ubo_global_options['wps_bump_target_link_attr_val'] ) ? $wps_ubo_global_options['wps_bump_target_link_attr_val'] : '';

// Selected bump offer appearance with or without pop-up.
$wps_bump_target_popup_bump = ! empty( $wps_ubo_global_options['wps_bump_popup_bump_offer'] ) ? $wps_ubo_global_options['wps_bump_popup_bump_offer'] : 'without_popup';

// Bump Offer remove.
$wps_ubo_offer_removal = ! empty( $wps_ubo_global_options['wps_ubo_offer_removal'] ) ? $wps_ubo_global_options['wps_ubo_offer_removal'] : '';

$wps_ubo_temp_adaption = ! empty( $wps_ubo_global_options['wps_ubo_temp_adaption'] ) ? $wps_ubo_global_options['wps_ubo_temp_adaption'] : 'yes';

// Bump Offer location.
$bump_offer_location = ! empty( $wps_ubo_global_options['wps_ubo_offer_location'] ) ? $wps_ubo_global_options['wps_ubo_offer_location'] : '';

$wps_ubo_offer_global_css = ! empty( $wps_ubo_global_options['wps_ubo_offer_global_css'] ) ? $wps_ubo_global_options['wps_ubo_offer_global_css'] : '';

$wps_ubo_offer_global_js = ! empty( $wps_ubo_global_options['wps_ubo_offer_global_js'] ) ? $wps_ubo_global_options['wps_ubo_offer_global_js'] : '';

$bump_offer_preorder_skip = ! empty( $wps_ubo_global_options['wps_ubo_offer_purchased_earlier'] ) ? $wps_ubo_global_options['wps_ubo_offer_purchased_earlier'] : 'no';

$bump_offer_coupon_restriction = ! empty( $wps_ubo_global_options['wps_ubo_offer_restrict_coupons'] ) ? $wps_ubo_global_options['wps_ubo_offer_restrict_coupons'] : 'no';

// FBT Location Select type.
$wps_ubo_offer_fbt_location_set = ! empty( $wps_ubo_global_options['wps_ubo_offer_fbt_location_set'] ) ? $wps_ubo_global_options['wps_ubo_offer_fbt_location_set'] : 'woocommerce_product_meta_end';

// Bump Offer limit.
$wps_bump_order_bump_limit = ! empty( $wps_ubo_global_options['wps_bump_order_bump_limit'] ) ? $wps_ubo_global_options['wps_bump_order_bump_limit'] : '1';

$wps_custom_order_success_page = ! empty( $wps_ubo_global_options['wps_custom_order_success_page'] ) ? $wps_ubo_global_options['wps_custom_order_success_page'] : '';

// Delete all data on uninstall.
$wps_delete_all_on_uninstall = ! empty( $wps_ubo_global_options['wps_delete_all_on_uninstall'] ) ? $wps_ubo_global_options['wps_delete_all_on_uninstall'] : 'no';

// Cart Upsell Functionality.
$wps_enable_cart_upsell_feature = ! empty( $wps_ubo_global_options['wps_enable_cart_upsell_feature'] ) ? $wps_ubo_global_options['wps_enable_cart_upsell_feature'] : 'no';

// FBT Location Enable.
$wps_enable_fbt_upsell_feature = ! empty( $wps_ubo_global_options['wps_enable_fbt_upsell_feature'] ) ? $wps_ubo_global_options['wps_enable_fbt_upsell_feature'] : 'no';

// Cart Upsell Offer Location.
$bump_cart_offer_location = ! empty( $wps_ubo_global_options['wps_enable_cart_upsell_location'] ) ? $wps_ubo_global_options['wps_enable_cart_upsell_location'] : '';

// Enable popup exit intent setting.
$wps_ubo_enable_popup_exit_intent = ! empty( $wps_ubo_global_options['wps_ubo_enable_popup_exit_intent'] ) ? $wps_ubo_global_options['wps_ubo_enable_popup_exit_intent'] : '';

$wps_ubo_enable_fluentcrm_integration = ! empty( $wps_ubo_global_options['wps_ubo_enable_fluentcrm'] ) ? $wps_ubo_global_options['wps_ubo_enable_fluentcrm'] : '';

// After v2.1.2.
$wps_enable_red_arrow_feature = ! empty( $wps_ubo_global_options['wps_enable_red_arrow_feature'] ) ? $wps_ubo_global_options['wps_enable_red_arrow_feature'] : 'no';

$bump_offer_ab_method  = ! empty( $wps_ubo_global_options['wps_ubo_offer_ab_method'] ) ? $wps_ubo_global_options['wps_ubo_offer_ab_method'] : 'no';
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


				<!-- Enable Product page link on title and image start. -->
				<tr valign="top">

					<th scope="row" class="titledesc">
						<label for="wps_bump_enable_permalink  "><?php esc_html_e( 'Enable permalink on product title and image', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">
						<?php
						$attribute_description = esc_html__( 'Enable permalink on product title and image.', 'upsell-order-bump-offer-for-woocommerce' );

						wps_ubo_lite_help_tip( $attribute_description );
						?>

						<label for="wps_ubo_enable_permalink_switch" class="wps_upsell_bump_enable_permalink_label wps_bump_enable_permalink_support">

							<input id="wps_ubo_enable_permalink_switch" class="wps_upsell_bump_enable_permalink_input" type="checkbox" <?php echo ( 'on' === $wps_bump_enable_permalink ) ? "checked='checked'" : ''; ?> name="wps_bump_enable_permalink">
							<span class="wps_upsell_bump_enable_permalink_span"></span>

						</label>
					</td>
				</tr>
				<!-- Enable Product page link on title and image end. -->

				<!-- Enable order bump shortcode start. -->

				<tr valign="top">

					<th scope="row" class="titledesc">
						<label for="wps_bump_enable_permalink  "><?php esc_html_e( 'Shortcode Order Bump', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">
						<?php
						$attribute_description = esc_html__( 'Use this shortcode to show bump offer on page.', 'upsell-order-bump-offer-for-woocommerce' );

						wps_ubo_lite_help_tip( $attribute_description );
						?>

						<label for="wps_ubo_enable_shortcode_switch" class="wps_upsell_bump_enable_permalink_label wps_bump_enable_permalink_support">

							
							<span>[wps_bump_offer_shortcode]</span>

						</label>
					</td>
				</tr>

				<!-- Enable order bump shortcode start end. -->
				
				<!-- Enable/disable Popup exit intent feature start -->
				<tr valign="top">
					<th scope="row" class="titledesc">
						<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<label for="wps_ubo_enable_popup_exit_intent_switch"><?php esc_html_e( 'Enable Popup Exit-Intent', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">
						<?php
						$attribute_description = esc_html__( 'Triggered the popup on leaving the checkout or cart page.', 'upsell-order-bump-offer-for-woocommerce' );

						wps_ubo_lite_help_tip( $attribute_description );
						?>

						<label for="wps_ubo_enable_popup_exit_intent_switch" class="wps_upsell_bump_enable_permalink_label wps_bump_enable_permalink_support">

							<input id="wps_ubo_enable_popup_exit_intent_switch" class="wps_upsell_bump_enable_permalink_input" type="checkbox" <?php echo ( 'on' === $wps_ubo_enable_popup_exit_intent ) ? "checked='checked'" : ''; ?> name="wps_ubo_enable_popup_exit_intent">
							<span class="wps_upsell_bump_enable_permalink_span"></span>

						</label>
					</td>
				</tr>
				<!-- Enable/disable Popup exit intent feature end -->


				<!-- Enable/disable FluentCRM feature start -->
				<tr valign="top">
					<th scope="row" class="titledesc">
						<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<label for="wps_ubo_enable_fluentcrm_switch"><?php esc_html_e( 'Enable Email Marketing Automation', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">
						<?php
						$attribute_description = esc_html__( 'Integrate with FluentCRM for advanced marketing automation for pre bump offers.', 'upsell-order-bump-offer-for-woocommerce' );

						wps_ubo_lite_help_tip( $attribute_description );
						?>

						<label for="wps_ubo_enable_fluentcrm_switch" class="wps_upsell_bump_enable_permalink_label wps_bump_enable_permalink_support">

							<input id="wps_ubo_enable_fluentcrm_switch" class="wps_upsell_bump_enable_permalink_input" type="checkbox" <?php echo ( 'on' === $wps_ubo_enable_fluentcrm_integration ) ? "checked='checked'" : ''; ?> name="wps_ubo_enable_fluentcrm">
							<span class="wps_upsell_bump_enable_permalink_span"></span>

						</label>
					</td>
				</tr>
				<!-- Enable/disable FluentCRM feature end -->

				<!-- Enable the Pop Up for bump Offer start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">
						<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<label for="wps_ubo_enable_popup_exit_intent_switch"><?php esc_html_e( 'Bump Offer Appearance', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="wps_bump_offer_popup_cases" class="forminp forminp-text">
						<?php
						$attribute_description = esc_html__( 'Show the order bump in popup and without popup form.', 'upsell-order-bump-offer-for-woocommerce' );
						wps_ubo_lite_help_tip( $attribute_description );
						?>

						<div>
							<label class="form-control">
								<?php if ( wps_ubo_lite_if_pro_exists() ) { ?>
									<input type="radio" id="wps_Offer_With_Pop_Up_id_pro_1" name="wps_bump_popup_bump_offer" value="with_popup" <?php checked( $wps_bump_target_popup_bump, 'with_popup' ); ?> /><?php echo esc_html_e( 'Offer With Pop-Up', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>

							<label class="form-control">
								<input type="radio" name="wps_bump_popup_bump_offer" id="wps_Offer_Without_Pop_Up_id_pro_1" value="without_popup" <?php checked( $wps_bump_target_popup_bump, 'without_popup' ); ?> /><?php echo esc_html_e( 'Offer Without Pop-Up', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
						<?php } else { ?>
							<input type="radio" id="wps_Offer_With_Pop_Up_id_org_2" class="wps_bump_offer_popup_case" name="wps_check_bump" value="" <?php checked( $wps_bump_target_popup_bump, 'with_popup' ); ?> /><?php echo esc_html_e( 'Offer With Pop-Up', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
							<label class="form-control">
								<input type="radio" name="wps_bump_popup_bump_offer" id="wps_Offer_Without_Pop_Up_id_org_2" value="without_popup" <?php checked( $wps_bump_target_popup_bump, 'without_popup' ); ?> /><?php echo esc_html_e( 'Offer Without Pop-Up', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
						<?php } ?>
						</div>

						<!-- Select options for skipping. -->

					</td>
				</tr>
				<!--Enable the Pop Up for bump Offer end. -->

				<!-- Select bump id to apply popup start here -->
				<tr valign="top" class="wps_target_bump_for_popup">

					<th scope="row" class="titledesc">
						<label for="wps_upsell_bump_target_ids_search"><?php esc_html_e( 'Select target bumps(s)', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">
						<?php

						$description = esc_html__( 'In these the order bump that are selected in these will show in popup.', 'upsell-order-bump-offer-for-woocommerce' );
						wps_ubo_lite_help_tip( $description );
						?>

						<select id="wps_upsell_bump_target_ids_search" class="wc-bump-offer-search" multiple="multiple" name="wps_upsell_bump_target_ids_popup[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'upsell-order-bump-offer-for-woocommerce' ); ?>">
							<?php
							if ( ! empty( $wps_upsell_bump_target_ids_popup ) ) {

								if ( $wps_upsell_bump_target_ids_popup ) {

									foreach ( $wps_upsell_bump_target_ids_popup as $wps_upsell_bump_single_target_products_ids ) {

										$product_name = wps_ubo_lite_get_bump_title( $wps_upsell_bump_single_target_products_ids );
										?>

										<option value="<?php echo esc_html( $wps_upsell_bump_single_target_products_ids ); ?>" selected="selected"><?php echo( esc_html( $product_name ) . '(#' . esc_html( $wps_upsell_bump_single_target_products_ids ) . ')' ); ?></option>';

										<?php
									}
								}
							}

							?>
						</select>		
					</td>	
				</tr>
				<!-- Select bump id to apply popup ends here -->

				<!-- Permalink target location start. -->
				<tr valign="top">

					<th scope="row" class="titledesc">
						<label for="wps_ubo_permalink_target_attr"><?php esc_html_e( 'Offer product permalink target attribute', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">
						<?php
						$attribute_description = esc_html__( 'On clicking on the offer product title or image the product page opens on the same page or new page.', 'upsell-order-bump-offer-for-woocommerce' );
						wps_ubo_lite_help_tip( $attribute_description );
						?>

						<!-- Select options for skipping. -->
						<select id="wps_ubo_permalink_target_attr" name="wps_bump_target_attr">

							<option value="_self" <?php selected( $wps_bump_target_link_attr_val, '_self' ); ?>><?php esc_html_e( 'Opens in the same frame as it was clicked', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>

							<option value="_blank" <?php selected( $wps_bump_target_link_attr_val, '_blank' ); ?>><?php esc_html_e( 'Opens in a new window or tab', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>

						</select>
					</td>

				</tr>
				<!--Permalink target location end. -->


				<!-- Offer removal on target removal starts. -->
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="wps_ubo_offer_removal_select"><?php esc_html_e( 'Offer Target Dependency', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<?php

					$wps_ubo_offer_removal_options = array(
						'yes' => esc_html__( 'Remove Offer When Target Product is Removed', 'upsell-order-bump-offer-for-woocommerce' ),
						'no'  => esc_html__( 'Keep Offer even When Target is Removed', 'upsell-order-bump-offer-for-woocommerce' ),
					);

					?>

					<td class="forminp forminp-text">

						<?php
						$attribute_description = sprintf(
							'<p><span class="wps_ubo_description">%s</span> <span class="wps_ubo_tip_tip">%s</span></p><p><span class="wps_ubo_description">%s</span> <span class="wps_ubo_tip_tip">%s</span></p>',
							esc_html__( 'Remove offer when target product is removed:', 'upsell-order-bump-offer-for-woocommerce' ),
							esc_html__( 'As the Target product is removed the offer product associated with the target will also be removed from your customer\'s cart.', 'upsell-order-bump-offer-for-woocommerce' ),
							esc_html__( 'Keep offer when target product is removed:', 'upsell-order-bump-offer-for-woocommerce' ),
							esc_html__( 'As the Target product is removed the offer product associated with the target will be converted into a normal product in your customer\'s cart.', 'upsell-order-bump-offer-for-woocommerce' )
						);
						wps_ubo_lite_help_tip( $attribute_description );
						?>

						<select id="wps_ubo_offer_removal_select" name="wps_ubo_offer_removal">

							<?php foreach ( $wps_ubo_offer_removal_options as $key => $value ) : ?>

								<option <?php selected( $wps_ubo_offer_removal, $key ); ?> value="<?php echo esc_html( $key ); ?>"><?php echo esc_html( $value ); ?></option>

							<?php endforeach; ?>

						</select>

					</td>
				</tr>
				<!-- Offer removal on target removal ends. -->

				<!-- Template adaption starts. -->
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="wps_ubo_temp_adaption_select"><?php esc_html_e( 'Offer Adaption settings', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<?php

					$wps_ubo_temp_adaptions_options = array(
						'yes' => esc_html__( 'Free Width', 'upsell-order-bump-offer-for-woocommerce' ),
						'no'  => esc_html__( 'Fixed Width', 'upsell-order-bump-offer-for-woocommerce' ),
					);

					?>

					<td class="forminp forminp-text">

						<?php
						$attribute_description = esc_html__( 'If Free Width, the Order Bump Offer will adapt to the complete width of it\'s parent location area else it will be fixed.', 'upsell-order-bump-offer-for-woocommerce' );
						wps_ubo_lite_help_tip( $attribute_description );
						?>

						<select id="wps_ubo_temp_adaption_select" name="wps_ubo_temp_adaption">

							<?php foreach ( $wps_ubo_temp_adaptions_options as $key => $value ) : ?>

								<option <?php selected( $wps_ubo_temp_adaption, $key ); ?> value="<?php echo esc_html( $key ); ?>"><?php echo esc_html( $value ); ?></option>

							<?php endforeach; ?>

						</select>

					</td>
				</tr>
				<!-- Template adaption ends. -->

				<!-- Offer location start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="wps_ubo_offer_location"><?php esc_html_e( 'Offer Location', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<?php
					$wps_plugin_list = get_option( 'active_plugins' );
					$wps_is_pro_active = false;
					$wps_traditional_checkout = true;
					$wps_plugin = 'checkout-for-woocommerce/checkout-for-woocommerce.php';

					// Get the ID of the selected checkout page from WooCommerce settings.
					$checkout_page_id = get_option( 'woocommerce_checkout_page_id' );

					// Get the content of the checkout page.
					$checkout_page_content = get_post_field( 'post_content', $checkout_page_id );

					// Check if the content contains a class associated with the block editor.
					if ( strpos( $checkout_page_content, 'wp-block-woocommerce-checkout' ) !== false ) {
						$wps_traditional_checkout = false;
					} else {
						$wps_traditional_checkout = true;
					}

					if ( in_array( $wps_plugin, $wps_plugin_list ) ) {
						$wps_is_pro_active = true;
					}

					if ( ! $wps_is_pro_active && $wps_traditional_checkout ) {
						$offer_locations_array = array(
							'_before_order_summary'      => esc_html__( 'Before Order Summary', 'upsell-order-bump-offer-for-woocommerce' ),
							'_before_payment_gateways'   => esc_html__( 'Before Payment Gateways', 'upsell-order-bump-offer-for-woocommerce' ),
							'_after_payment_gateways'    => esc_html__( 'After Payment Gateways', 'upsell-order-bump-offer-for-woocommerce' ),
							'_before_place_order_button' => esc_html__( 'Before Place Order Button', 'upsell-order-bump-offer-for-woocommerce' ),
						);
					} elseif ( ! $wps_is_pro_active && ! $wps_traditional_checkout ) { // Code For Comapatibility With Checkout Block plugin.
						$offer_locations_array = array(
							'_before_order_summary'      => esc_html__( 'After Coupon Box', 'upsell-order-bump-offer-for-woocommerce' ),
							'_before_payment_gateways'    => esc_html__( 'Before Checkout Summary', 'upsell-order-bump-offer-for-woocommerce' ),
							'_after_payment_gateways'    => esc_html__( 'After Payment Gateways', 'upsell-order-bump-offer-for-woocommerce' ),
							'_before_place_order_button' => esc_html__( 'After Checkout Total', 'upsell-order-bump-offer-for-woocommerce' ),
						);
					} elseif ( $wps_is_pro_active ) {  // Code For Comapatibility With CheckoutWC plugin.
						$offer_locations_array = array(
							'_before_order_summary'      => esc_html__( 'Before Order Summary', 'upsell-order-bump-offer-for-woocommerce' ),
							'_before_payment_gateways'   => esc_html__( 'Before Payment Gateways', 'upsell-order-bump-offer-for-woocommerce' ),
							'_after_payment_gateways'    => esc_html__( 'After Payment Gateways', 'upsell-order-bump-offer-for-woocommerce' ),
							'_before_place_order_button' => esc_html__( 'Before Place Order Button', 'upsell-order-bump-offer-for-woocommerce' ),
						);
					}
					?>

					<td class="forminp forminp-text">

						<?php
						$attribute_description = esc_html__( 'Choose the location where the Bump Offer will be displayed on the Checkout page.', 'upsell-order-bump-offer-for-woocommerce' );
						wps_ubo_lite_help_tip( $attribute_description );
						?>

						<select id="wps_ubo_offer_location" name="wps_ubo_offer_location">

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
				if ( wps_ubo_lite_if_pro_exists() && version_compare( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_VERSION, '1.2.0' ) < 0 ) {

					$is_update_needed = 'true';
				}
				?>
				<input type="hidden" id="is_pro_update_needed" value="<?php echo esc_html( $is_update_needed ); ?>">


				<!-- FBT Location Change enable / disable Start. -->
				<tr valign="top">

					<th scope="row" class="titledesc">

						<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<label for="wps_enable_fbt_upsell_feature"><?php esc_html_e( 'Enable FBT Location', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">
						<?php
						$attribute_description = esc_html__( 'Enable this to specify the location of the Frequently Bought Together products on the product page.', 'upsell-order-bump-offer-for-woocommerce' );

						wps_ubo_lite_help_tip( $attribute_description );
						?>

						<label for="wps_enable_fbt_upsell_feature" class="wps_enable_fbt_upsell_feature wps_upsell_bump_enable_deletedata_label wps_bump_enable_plugin_support">

							<input id="wps_enable_fbt_upsell_feature" class="wps_upsell_bump_enable_plugin_input" type="checkbox" <?php echo ( 'on' === $wps_enable_fbt_upsell_feature ) ? "checked='checked'" : ''; ?> name="wps_enable_fbt_upsell_feature">
							<span class="wps_upsell_bump_enable_plugin_span"></span>

						</label>
					</td>
				</tr>
				<!-- FBT Location Change enable / disable End Here. -->


				<!-- FBT Location Change Option Start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">
						<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<label for="wps_ubo_offer_purchased_earlier"><?php esc_html_e( 'Frequent Bought Together Location ', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<?php

					$offer_locations_array = array(
						'woocommerce_after_single_product' => esc_html__( 'After Single Product', 'upsell-order-bump-offer-for-woocommerce' ),
						'woocommerce_after_add_to_cart_form' => esc_html__( 'After Add To Cart Form', 'upsell-order-bump-offer-for-woocommerce' ),
						'woocommerce_after_single_product_summary' => esc_html__( 'After Single Product Summary', 'upsell-order-bump-offer-for-woocommerce' ),
						'woocommerce_product_meta_end' => esc_html__( 'Product Meta End', 'upsell-order-bump-offer-for-woocommerce' ),
					);

					?>

					<td class="forminp forminp-text">

						<?php
						$attribute_description = esc_html__( 'Select the way to display Frequently bought toegther on the product page..', 'upsell-order-bump-offer-for-woocommerce' );
						wps_ubo_lite_help_tip( $attribute_description );
						?>
						<label for="wps_ubo_offer_fbt_location_set">
						<select id="wps_ubo_offer_fbt_location_set" name="wps_ubo_offer_fbt_location_set">

							<?php foreach ( $offer_locations_array as $key => $value ) : ?>

								<option <?php selected( $wps_ubo_offer_fbt_location_set, $key ); ?> value="<?php echo esc_html( $key ); ?>"><?php echo esc_html( $value ); ?></option>

							<?php endforeach; ?>

						</select>
					</label>
					</td>
				</tr>
				<!-- FBT Location Change Option End Here. -->

				<!-- AB feature skip start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">

						<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>

						<label for="wps_ubo_offer_ab_method"><?php esc_html_e( 'AB Method', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php
						$attribute_description = esc_html__( 'Enable this feature to have the facility of AB method to show the bump with data.', 'upsell-order-bump-offer-for-woocommerce' );
						wps_ubo_lite_help_tip( $attribute_description );
						?>
						<label class="wps-upsell-smart-pre-order-skip" for="wps_ubo_offer_ab_method">
							<input class="wps-upsell-smart-pre-order-skip-wrap" type='checkbox' <?php echo wps_ubo_lite_if_pro_exists() && ! empty( $bump_offer_ab_method ) && 'on' === $bump_offer_ab_method ? 'checked' : ''; ?> id='wps_ubo_offer_ab_method' value='yes' name='wps_ubo_offer_ab_method'>
							<span class="upsell-smart-pre-order-skip-btn"></span>
						</label>
					</td>
				</tr>
				<!-- AB feature skip end. -->

				<!-- Restrict external coupons feature skip start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">

						<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>

						<label for="wps_ubo_offer_restrict_coupons"><?php esc_html_e( 'Restrict Woo coupons', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php
						$attribute_description = esc_html__( 'This features removes extra coupon discounts from cart item.', 'upsell-order-bump-offer-for-woocommerce' );
						wps_ubo_lite_help_tip( $attribute_description );
						?>
						<label class="wps-upsell-smart-pre-order-skip" for="wps_ubo_offer_restrict_coupons">
							<input class="wps-upsell-smart-pre-order-skip-wrap" type='checkbox' <?php echo wps_ubo_lite_if_pro_exists() && ! empty( $bump_offer_coupon_restriction ) && 'yes' === $bump_offer_coupon_restriction ? 'checked' : ''; ?> id='wps_ubo_offer_restrict_coupons' value='yes' name='wps_ubo_offer_restrict_coupons'>
							<span class="upsell-smart-pre-order-skip-btn"></span>
						</label>
					</td>
				</tr>
				<!-- Restrict external coupons feature skip end. -->


				<!-- Enable/Disable red arrow feature start. -->
				<tr valign="top">

					<th scope="row" class="titledesc">

						<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<label for="wps_enable_red_arrow_feature"><?php esc_html_e( 'Enable Arrow feature.', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">
						<?php
						$attribute_description = esc_html__( 'Enable arrow pointing checkbox to add the bump offer.', 'upsell-order-bump-offer-for-woocommerce' );

						wps_ubo_lite_help_tip( $attribute_description );
						?>

						<label for="wps_enable_red_arrow_feature" class="wps_enable_red_arrow_feature wps_upsell_bump_enable_deletedata_label wps_bump_enable_plugin_support">

							<input id="wps_enable_red_arrow_feature" class="wps_upsell_bump_enable_plugin_input" type="checkbox" <?php echo ( 'on' === $wps_enable_red_arrow_feature ) ? "checked='checked'" : ''; ?> id="wps_enable_red_arrow_feature" name="wps_enable_red_arrow_feature">
							<span class="wps_upsell_bump_enable_plugin_span"></span>

						</label>
					</td>
				</tr>
				<!-- Enable/Disable red arrow feature end. -->

				<!-- Delete all the data on uninstall button html start. -->
				<tr valign="top">

					<th scope="row" class="titledesc">
						<label for="wps_delete_all_on_uninstall  "><?php esc_html_e( 'Enable Delete all data on uninstall.', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">
						<?php
						$attribute_description = esc_html__( 'Enable Delete all data on uninstall.', 'upsell-order-bump-offer-for-woocommerce' );

						wps_ubo_lite_help_tip( $attribute_description );
						?>

						<label for="wps_ubo_enable_switch_delete_data" class="wps_upsell_bump_enable_deletedata_label wps_bump_enable_plugin_support">

							<input id="wps_ubo_enable_switch_delete_data" class="wps_upsell_bump_enable_plugin_input" type="checkbox" <?php echo ( 'on' === $wps_delete_all_on_uninstall ) ? "checked='checked'" : ''; ?> name="wps_delete_all_on_uninstall">
							<span class="wps_upsell_bump_enable_plugin_span"></span>

						</label>
					</td>
				</tr>
				<!-- Delete all the data on uninstall button html end. -->
				<!-- Enable/Disable the Cart upsell functionality Start-->
				<tr valign="top">

					<th scope="row" class="titledesc">

						<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<label for="wps_enable_cart_upsell"><?php esc_html_e( 'Enable Cart Upsell.', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">
						<?php
						$attribute_description = esc_html__( 'Enable cart upsell checkbox to add the bump offer on cart page.', 'upsell-order-bump-offer-for-woocommerce' );

						wps_ubo_lite_help_tip( $attribute_description );
						?>

						<label for="wps_enable_cart_upsell" class="wps_enable_cart_upsell wps_upsell_bump_enable_deletedata_label wps_bump_enable_plugin_support">

							<input id="wps_enable_cart_upsell" class="wps_upsell_bump_enable_plugin_input" type="checkbox" <?php echo ( 'on' === $wps_enable_cart_upsell_feature ) ? "checked='checked'" : ''; ?> name="wps_enable_cart_upsell_feature">
							<span class="wps_upsell_bump_enable_plugin_span"></span>

						</label>
					</td>
				</tr>
				<!-- Enable/Disable the Cart upsell functionality End -->
				<!-- Select option for location of the bump on cart page Start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">

						<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<label for="wps_enable_cart_upsell_location"><?php esc_html_e( 'Cart Upsell Offer Location.', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<?php
					$wps_traditional_cart = true;

					// Get the ID of the selected cart page from WooCommerce settings.
					$cart_page_id = get_option( 'woocommerce_cart_page_id' );

					// Get the content of the checkout page.
					$cart_page_content = get_post_field( 'post_content', $cart_page_id );

					// Check if the content contains a class associated with the block editor.
					if ( strpos( $cart_page_content, 'wp-block-woocommerce-cart' ) !== false ) {
						$wps_traditional_cart = false;
					} else {
						$wps_traditional_cart = true;
					}

					if ( $wps_traditional_cart ) {
						$offer_locations_array = array(
							'woocommerce_after_cart_totals' => esc_html__( 'After Cart Totals', 'upsell-order-bump-offer-for-woocommerce' ),
							'woocommerce_cart_collaterals'  => esc_html__( 'After Cart Section', 'upsell-order-bump-offer-for-woocommerce' ),
							'woocommerce_before_cart_totals' => esc_html__( 'Before Cart Total', 'upsell-order-bump-offer-for-woocommerce' ),
						);
					} else {
						$offer_locations_array = array(
							'woocommerce_after_cart_totals' => esc_html__( 'After Checkout Button', 'upsell-order-bump-offer-for-woocommerce' ),
							'woocommerce_cart_collaterals'  => esc_html__( 'Before Checkout Button', 'upsell-order-bump-offer-for-woocommerce' ),
							'woocommerce_before_cart_totals'  => esc_html__( 'After Cart Line Item', 'upsell-order-bump-offer-for-woocommerce' ),
						);
					}
					?>

					<td class="forminp forminp-text">

						<?php
						$attribute_description = esc_html__( 'Choose the location where the Bump Offer will be displayed on the Cart page.', 'upsell-order-bump-offer-for-woocommerce' );
						wps_ubo_lite_help_tip( $attribute_description );
						?>
						<label for="wps_enable_cart_upsell_location" class="wps_enable_cart_upsell_location wps_upsell_bump_enable_deletedata_label wps_bump_enable_plugin_support">
							<select id="wps_enable_cart_upsell_location" name="wps_enable_cart_upsell_location">

								<?php foreach ( $offer_locations_array as $key => $value ) : ?>

									<option <?php selected( $bump_cart_offer_location, $key ); ?> value="<?php echo esc_html( $key ); ?>"><?php echo esc_html( $value ); ?></option>

								<?php endforeach; ?>

							</select>
						</label>
					</td>
				</tr>
				<!-- Select option for location of the bump on cart page End. -->
				<!-- Order Bump Limit start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">
						<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<label for="wps_bump_order_bump_limit"><?php esc_html_e( 'Multiple Order Bumps Count', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php
						$attribute_description = esc_html__( 'Enter the Multiple Order Bumps count that you want to display on the checkout page.', 'upsell-order-bump-offer-for-woocommerce' );
						wps_ubo_lite_help_tip( $attribute_description );

						?>

						<input type="number" min="1" id="wps_bump_order_bump_limit" name="wps_bump_order_bump_limit" value="<?php echo esc_html( $wps_bump_order_bump_limit ); ?>">

					</td>
				</tr>
				<!-- Order Bump Limit end. -->
				<!-- Set Order Success Page Start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="wps_bump_order_bump_limit"><?php esc_html_e( 'Set Custom Order Success Page', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php
						$attribute_description = esc_html__( 'Set Any Page As The Order Success Page By Placing The Page Slug.', 'upsell-order-bump-offer-for-woocommerce' );
						wps_ubo_lite_help_tip( $attribute_description );

						?>

						<input type="text" min="1" id="wps_bump_order_bump_limit" name="wps_custom_order_success_page" value="<?php echo esc_html( $wps_custom_order_success_page ); ?>">
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>" style="text-decoration: none;"><i><?php esc_html_e( 'From here ,create custom order success page', 'upsell-order-bump-offer-for-woocommerce' ); ?></i></a>
					</td>
				</tr>
				<!-- Set Order Success Page End. -->

				<!-- Custom CSS start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="wps_ubo_offer_global_css"><?php esc_html_e( 'Global Custom CSS', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php
						$attribute_description = esc_html__( 'Add your Custom CSS without style tags.', 'upsell-order-bump-offer-for-woocommerce' );
						wps_ubo_lite_help_tip( $attribute_description );
						?>

						<textarea id="wps_ubo_offer_global_css" name="wps_ubo_offer_global_css" rows="4" cols="50"><?php echo esc_html( $wps_ubo_offer_global_css ); ?></textarea>

					</td>
				</tr>
				<!-- Custom CSS end. -->

				<!-- Custom JS start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="wps_ubo_offer_global_js"><?php esc_html_e( 'Global Custom JS', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php
						$attribute_description = esc_html__( 'Add your Custom JS without script tags.', 'upsell-order-bump-offer-for-woocommerce' );
						wps_ubo_lite_help_tip( $attribute_description );
						?>

						<textarea id="wps_ubo_offer_global_js" name="wps_ubo_offer_global_js" rows="4" cols="50"><?php echo esc_html( $wps_ubo_offer_global_js ); ?></textarea>

					</td>
				</tr>
				<!-- Custom JS end. -->

			</tbody>
		</table>
	</div>
	<!-- Settings ends -->

	<!-- Save Settings -->
	<p class="submit">
		<input type="submit" value="<?php esc_html_e( 'Save Changes', 'upsell-order-bump-offer-for-woocommerce' ); ?>" class="button-primary woocommerce-save-button" name="wps_upsell_bump_common_settings_save_pre_global" id="wps_upsell_bump_creation_setting_save">
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
