<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to create new/update bump offers.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      1.0.0
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/admin/partials/templates
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {

	exit;
}
/**
 * Bump Creation Template.
 *
 * This template is used for creating new bump as well
 * as viewing/editing previous bump.
 */

// Get all Bump if already some funnels are present.
$wps_upsell_bumps_list = get_option( 'wps_ubo_bump_list', array() );


if ( ! empty( $wps_upsell_bumps_list ) ) {

	reset( $wps_upsell_bumps_list );
	$wps_upsell_bump_id = key( $wps_upsell_bumps_list );
} else {

	// New Bump id.
	$wps_upsell_bump_id = 1;
}


// When save changes is clicked.
if ( isset( $_POST['wps_upsell_bump_creation_setting_save'] ) ) {

	unset( $_POST['wps_upsell_bump_creation_setting_save'] );

	// Nonce verification.
	check_admin_referer( 'wps_upsell_bump_creation_nonce', 'wps_upsell_bump_nonce' );

	// Saved bump id.
	$wps_upsell_bump_id = ! empty( $_POST['wps_upsell_bump_id'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_upsell_bump_id'] ) ) : 1;

	if ( empty( $_POST['wps_upsell_bump_target_categories'] ) ) {

		$_POST['wps_upsell_bump_target_categories'] = array();
	}

	if ( empty( $_POST['wps_upsell_bump_target_ids'] ) ) {

		$_POST['wps_upsell_bump_target_ids'] = array();
	}

	if ( empty( $_POST['wps_upsell_bump_status'] ) ) {

		$_POST['wps_upsell_bump_status'] = 'no';
	}

	// When price is saved.
	if ( empty( $_POST['wps_upsell_bump_offer_discount_price'] ) ) {

		if ( '' === $_POST['wps_upsell_bump_offer_discount_price'] ) {

			$_POST['wps_upsell_bump_offer_discount_price'] = '20';
		} else {

			$_POST['wps_upsell_bump_offer_discount_price'] = '0';
		}
	}

	// From these versions we will be having multiselect for schedules.
	if ( empty( $_POST['wps_upsell_bump_schedule'] ) ) {

		$_POST['wps_upsell_bump_schedule'] = array( '0' );
	}

	// New bump to be made.
	$wps_upsell_new_bump = array();

	// Sanitize and strip slashes for Texts.
	$wps_upsell_new_bump['wps_upsell_bump_status'] = ! empty( $_POST['wps_upsell_bump_status'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_upsell_bump_status'] ) ) : '';

	$wps_upsell_new_bump['wps_upsell_bump_name'] = ! empty( $_POST['wps_upsell_bump_name'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_upsell_bump_name'] ) ) : '';


	// Updated after v1.2.0.
	$wps_upsell_new_bump['wps_upsell_bump_schedule'] = ! empty( $_POST['wps_upsell_bump_schedule'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['wps_upsell_bump_schedule'] ) ) : array( '0' );

	$wps_upsell_new_bump['wps_upsell_bump_offer_discount_price'] = ! empty( $_POST['wps_upsell_bump_offer_discount_price'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_upsell_bump_offer_discount_price'] ) ) : '';

	$wps_upsell_new_bump['wps_upsell_bump_products_in_offer'] = ! empty( $_POST['wps_upsell_bump_products_in_offer'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_upsell_bump_products_in_offer'] ) ) : '';

	$wps_upsell_new_bump['wps_upsell_offer_price_type'] = ! empty( $_POST['wps_upsell_offer_price_type'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_upsell_offer_price_type'] ) ) : '';

	$wps_upsell_new_bump['wps_ubo_discount_title_for_percent'] = ! empty( $_POST['wps_ubo_discount_title_for_percent'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_ubo_discount_title_for_percent'] ) ) : '';

	$wps_upsell_new_bump['wps_bump_offer_decsription_text'] = ! empty( $_POST['wps_bump_offer_decsription_text'] ) ? sanitize_textarea_field( wp_unslash( $_POST['wps_bump_offer_decsription_text'] ) ) : '';

	$wps_upsell_new_bump['wps_upsell_bump_offer_description'] = ! empty( $_POST['wps_upsell_bump_offer_description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['wps_upsell_bump_offer_description'] ) ) : '';

	$wps_upsell_new_bump['wps_bump_upsell_selected_template'] = ! empty( $_POST['wps_bump_upsell_selected_template'] ) ? sanitize_textarea_field( wp_unslash( $_POST['wps_bump_upsell_selected_template'] ) ) : '';

	$wps_upsell_new_bump['wps_ubo_selected_template'] = ! empty( $_POST['wps_ubo_selected_template'] ) ? sanitize_textarea_field( wp_unslash( $_POST['wps_ubo_selected_template'] ) ) : '';

	// Sanitize and stripe slashes all the arrays.
	$wps_upsell_new_bump['wps_upsell_bump_target_categories'] = ! empty( $_POST['wps_upsell_bump_target_categories'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['wps_upsell_bump_target_categories'] ) ) : '';

	$wps_upsell_new_bump['wps_upsell_bump_target_ids'] = ! empty( $_POST['wps_upsell_bump_target_ids'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['wps_upsell_bump_target_ids'] ) ) : '';

	// After v2.0.1!
	$wps_upsell_new_bump['wps_upsell_offer_image']        = ! empty( $_POST['wps_upsell_offer_image'] ) ? absint( sanitize_text_field( wp_unslash( $_POST['wps_upsell_offer_image'] ) ) ) : '';
	$wps_upsell_new_bump['wps_upsell_bump_priority']      = ! empty( $_POST['wps_upsell_bump_priority'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_upsell_bump_priority'] ) ) : '';
	$wps_upsell_new_bump['wps_upsell_bump_exclude_roles'] = ! empty( $_POST['wps_upsell_bump_exclude_roles'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['wps_upsell_bump_exclude_roles'] ) ) : '';
	$wps_upsell_new_bump['wps_bump_label_campaign']        = ! empty( $_POST['wps_bump_label_campaign'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_bump_label_campaign'] ) ) : '';
	$wps_upsell_new_bump['wps_ubo_condition_show']        = ! empty( $_POST['wps_ubo_condition_show'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_ubo_condition_show'] ) ) : '';
	// When Bump is saved for the first time so load default Design Settings.
	if ( empty( $_POST['parent_border_type'] ) ) {

		$design_settings = wps_ubo_lite_offer_template_1();

		$wps_upsell_new_bump['design_css'] = $design_settings;

		$wps_upsell_new_bump['design_text'] = wps_ubo_lite_offer_default_text();
	} else {    // When design Settings is saved from Post.

		// PARENT WRAPPER DIV CSS( parent_wrapper_div ).
		$design_settings_post['parent_border_type']      = ! empty( $_POST['parent_border_type'] ) ? sanitize_text_field( wp_unslash( $_POST['parent_border_type'] ) ) : '';
		$design_settings_post['parent_border_color']     = ! empty( $_POST['parent_border_color'] ) ? sanitize_text_field( wp_unslash( $_POST['parent_border_color'] ) ) : '';
		$design_settings_post['top_vertical_spacing']    = ! empty( $_POST['top_vertical_spacing'] ) ? sanitize_text_field( wp_unslash( $_POST['top_vertical_spacing'] ) ) : '';
		$design_settings_post['bottom_vertical_spacing'] = ! empty( $_POST['bottom_vertical_spacing'] ) ? sanitize_text_field( wp_unslash( $_POST['bottom_vertical_spacing'] ) ) : '';
		// v2.1.2 version.
		$design_settings_post['parent_background_color']     = ! empty( $_POST['parent_background_color'] ) ? sanitize_text_field( wp_unslash( $_POST['parent_background_color'] ) ) : '';

		unset( $_POST['parent_background_color'] );
		unset( $_POST['parent_border_type'] );
		unset( $_POST['parent_border_color'] );
		unset( $_POST['top_vertical_spacing'] );
		unset( $_POST['bottom_vertical_spacing'] );

		// DISCOUNT SECTION( discount_section ).
		$design_settings_post['discount_section_background_color'] = ! empty( $_POST['discount_section_background_color'] ) ? sanitize_text_field( wp_unslash( $_POST['discount_section_background_color'] ) ) : '';
		$design_settings_post['discount_section_text_color']       = ! empty( $_POST['discount_section_text_color'] ) ? sanitize_text_field( wp_unslash( $_POST['discount_section_text_color'] ) ) : '';
		$design_settings_post['discount_section_text_size']        = ! empty( $_POST['discount_section_text_size'] ) ? sanitize_text_field( wp_unslash( $_POST['discount_section_text_size'] ) ) : '';

		unset( $_POST['discount_section_background_color'] );
		unset( $_POST['discount_section_text_color'] );
		unset( $_POST['discount_section_text_size'] );


		// PRODUCT SECTION(product_section).
		$design_settings_post['product_section_text_color'] = ! empty( $_POST['product_section_text_color'] ) ? sanitize_text_field( wp_unslash( $_POST['product_section_text_color'] ) ) : '';
		$design_settings_post['product_section_text_size']  = ! empty( $_POST['product_section_text_size'] ) ? sanitize_text_field( wp_unslash( $_POST['product_section_text_size'] ) ) : '';
		$design_settings_post['product_section_price_text_size']  = ! empty( $_POST['product_section_price_text_size'] ) ? sanitize_text_field( wp_unslash( $_POST['product_section_price_text_size'] ) ) : '';
		$design_settings_post['product_section_price_text_color'] = ! empty( $_POST['product_section_price_text_color'] ) ? sanitize_text_field( wp_unslash( $_POST['product_section_price_text_color'] ) ) : '';

		unset( $_POST['product_section_text_color'] );
		unset( $_POST['product_section_text_size'] );
		unset( $_POST['product_section_price_text_size'] );
		unset( $_POST['product_section_price_text_color'] );

		// Accept Offer Section(primary_section).
		$design_settings_post['primary_section_background_color'] = ! empty( $_POST['primary_section_background_color'] ) ? sanitize_text_field( wp_unslash( $_POST['primary_section_background_color'] ) ) : '';
		$design_settings_post['primary_section_text_color']       = ! empty( $_POST['primary_section_text_color'] ) ? sanitize_text_field( wp_unslash( $_POST['primary_section_text_color'] ) ) : '';
		$design_settings_post['primary_section_text_size']        = ! empty( $_POST['primary_section_text_size'] ) ? sanitize_text_field( wp_unslash( $_POST['primary_section_text_size'] ) ) : '';

		unset( $_POST['primary_section_background_color'] );
		unset( $_POST['primary_section_text_color'] );
		unset( $_POST['primary_section_text_size'] );

		// SECONDARY SECTION(secondary_section).
		$design_settings_post['secondary_section_background_color'] = ! empty( $_POST['secondary_section_background_color'] ) ? sanitize_text_field( wp_unslash( $_POST['secondary_section_background_color'] ) ) : '';
		$design_settings_post['secondary_section_text_color']       = ! empty( $_POST['secondary_section_text_color'] ) ? sanitize_text_field( wp_unslash( $_POST['secondary_section_text_color'] ) ) : '';
		$design_settings_post['secondary_section_text_size']        = ! empty( $_POST['secondary_section_text_size'] ) ? sanitize_text_field( wp_unslash( $_POST['secondary_section_text_size'] ) ) : '';

		unset( $_POST['secondary_section_background_color'] );
		unset( $_POST['secondary_section_text_color'] );
		unset( $_POST['secondary_section_text_size'] );

		$wps_upsell_new_bump['design_css'] = $design_settings_post;

		$text_settings_post = array(

			'wps_ubo_discount_title_for_fixed'   => ! empty( $_POST['wps_ubo_discount_title_for_fixed'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_ubo_discount_title_for_fixed'] ) ) : '',

			'wps_ubo_discount_title_for_percent' => ! empty( $_POST['wps_ubo_discount_title_for_percent'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_ubo_discount_title_for_percent'] ) ) : '',

			'wps_bump_offer_decsription_text'    => ! empty( $_POST['wps_bump_offer_decsription_text'] ) ? sanitize_textarea_field( wp_unslash( $_POST['wps_bump_offer_decsription_text'] ) ) : '',

			'wps_upsell_offer_title'             => ! empty( $_POST['wps_upsell_offer_title'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_upsell_offer_title'] ) ) : '',

			'wps_upsell_bump_offer_description'  => ! empty( $_POST['wps_upsell_bump_offer_description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['wps_upsell_bump_offer_description'] ) ) : '',
		);

		unset( $_POST['wps_ubo_discount_title_for_fixed'] );
		unset( $_POST['wps_ubo_discount_title_for_percent'] );
		unset( $_POST['wps_bump_offer_decsription_text'] );
		unset( $_POST['wps_upsell_offer_title'] );
		unset( $_POST['wps_upsell_bump_offer_description'] );
		$wps_upsell_new_bump['design_text'] = $text_settings_post;
	}

	// If Order Bump already exists then save Sales By Bump - Stats if present.
	if ( ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['offer_view_count'] ) ) {

		$sales_stats_bump = $wps_upsell_bumps_list[ $wps_upsell_bump_id ];

		// Not Post data, so no need to Sanitize and Strip slashes.

		// Empty for this already checked above.
		$wps_upsell_new_bump['offer_view_count'] = $sales_stats_bump['offer_view_count'];

		$wps_upsell_new_bump['offer_accept_count'] = ! empty( $sales_stats_bump['offer_accept_count'] ) ? $sales_stats_bump['offer_accept_count'] : 0;

		$wps_upsell_new_bump['offer_remove_count'] = ! empty( $sales_stats_bump['offer_remove_count'] ) ? $sales_stats_bump['offer_remove_count'] : 0;

		$wps_upsell_new_bump['bump_success_count'] = ! empty( $sales_stats_bump['bump_success_count'] ) ? $sales_stats_bump['bump_success_count'] : 0;

		$wps_upsell_new_bump['bump_total_sales'] = ! empty( $sales_stats_bump['bump_total_sales'] ) ? $sales_stats_bump['bump_total_sales'] : 0;

		$wps_upsell_new_bump['bump_orders_count'] = ! empty( $sales_stats_bump['bump_orders_count'] ) ? $sales_stats_bump['bump_orders_count'] : array();

		$wps_upsell_new_bump['offer_accept_count_pro'] = ! empty( $sales_stats_bump['offer_accept_count_pro'] ) ? $sales_stats_bump['offer_accept_count_pro'] : 0;
	}

	// When Bump is saved for the first time so load default text Settings.
	$wps_upsell_bump_series = array();

	// POST bump as array at bump id key.
	$wps_upsell_bump_series[ $wps_upsell_bump_id ] = $wps_upsell_new_bump;

	// Save the bump.
	update_option( 'wps_ubo_bump_list', $wps_upsell_bump_series );

	?>

	<!-- Settings saved notice. -->
	<div class="notice notice-success is-dismissible wps-notice">
		<p><strong><?php esc_html_e( 'Settings saved', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong></p>
	</div>

	<?php
}

// Get all Bump.
$wps_upsell_bumps_list = get_option( 'wps_ubo_bump_list', array() );
$wps_bump_offer_type   = ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_offer_price_type'] ) ? $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_offer_price_type'] : '';

$wps_ubo_template_param              = isset( $_GET['template'] ) ? sanitize_text_field( wp_unslash( $_GET['template'] ) ) : '';
// Accept both correct and commonly mistyped slug from query string.
$is_conditional_display_template     = in_array(
	$wps_ubo_template_param,
	array( 'conditional-display', 'conditonal-display' ),
	true
);

$wps_upsell_bump_schedule_options = array(
	'0' => __( 'Daily', 'upsell-order-bump-offer-for-woocommerce' ),
	'1' => __( 'Monday', 'upsell-order-bump-offer-for-woocommerce' ),
	'2' => __( 'Tuesday', 'upsell-order-bump-offer-for-woocommerce' ),
	'3' => __( 'Wednesday', 'upsell-order-bump-offer-for-woocommerce' ),
	'4' => __( 'Thursday', 'upsell-order-bump-offer-for-woocommerce' ),
	'5' => __( 'Friday', 'upsell-order-bump-offer-for-woocommerce' ),
	'6' => __( 'Saturday', 'upsell-order-bump-offer-for-woocommerce' ),
	'7' => __( 'Sunday', 'upsell-order-bump-offer-for-woocommerce' ),
);

global $wp_roles;

$all_roles = $wp_roles->roles;
$all_roles['guest'] = array(
	'name' => esc_html__( 'Guest/Logged Out User', 'upsell-order-bump-offer-for-woocommerce' ),
);


$editable_roles = apply_filters( 'wps_upsell_order_bump_editable_roles', $all_roles );

?>

<!-- For Single Bump. -->
<form action="" method="POST">
	<div class="wps_upsell_table">
		<?php if ( $is_conditional_display_template ) : ?>
			<div class="notice notice-info is-dismissible wps-notice wps-ubo-conditional-notice">
				<div class="wps-ubo-conditional-notice__content">
					<h3 class="wps-ubo-conditional-notice__title"><?php esc_html_e( 'Bump Conditional Display walkthrough', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
					<ol class="wps-ubo-conditional-notice__list">
						<li><?php esc_html_e( 'Set targets/categories for this funnel.', 'upsell-order-bump-offer-for-woocommerce' ); ?></li>
						<li><?php esc_html_e( 'Enable conditions and set cart/user/coupon rules.', 'upsell-order-bump-offer-for-woocommerce' ); ?></li>
						<li><?php esc_html_e( 'Configure offers and design.', 'upsell-order-bump-offer-for-woocommerce' ); ?></li>
						<li><?php esc_html_e( 'Save and preview the flow.', 'upsell-order-bump-offer-for-woocommerce' ); ?></li>
					</ol>
				</div>
			</div>
		<?php endif; ?>
		<table class="form-table wps_upsell_bump_creation_setting">
			<tbody>

				<!-- Nonce field here. -->
				<?php wp_nonce_field( 'wps_upsell_bump_creation_nonce', 'wps_upsell_bump_nonce' ); ?>

				<input type="hidden" name="wps_upsell_bump_id" value="<?php echo esc_html( $wps_upsell_bump_id ); ?>">
				<input type='hidden' id='wps_ubo_pro_status' value='inactive'>

				<?php

				$bump_name = ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_name'] ) ? sanitize_text_field( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_name'] ) : esc_html__( 'Order Bump', 'upsell-order-bump-offer-for-woocommerce' ) . " #$wps_upsell_bump_id";

				$bump_status = ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_status'] ) ? sanitize_text_field( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_status'] ) : 'no';

				// Order bump priority v2.0.1.
				$bump_priority = ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_priority'] ) ? sanitize_text_field( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_priority'] ) : '';

				$min_cart_value = ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_min_cart'] ) ? sanitize_text_field( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_min_cart'] ) : 0;
				?>

				<!-- Bump Header start.-->
				<div id="wps_upsell_bump_name_heading">
					<h2><?php echo esc_html( $bump_name ); ?></h2>
					<div id="wps_upsell_bump_status">
						<?php
						$description = esc_html__( 'Bump Offer will be displayed : OFF Mode -> For Bump Off. Live Mode -> Live For All.', 'upsell-order-bump-offer-for-woocommerce' );
						wps_ubo_lite_help_tip( $description );
						?>
						<label>
							<input type="checkbox" id="wps_upsell_bump_status_input" name="wps_upsell_bump_status" value="yes" <?php checked( 'yes', $bump_status ); ?>>
							<span class="wps_upsell_bump_span"></span>
						</label>
						<span class="wps_upsell_bump_status_on <?php echo 'yes' === $bump_status ? 'active' : ''; ?>"><?php esc_html_e( 'Live', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<span class="wps_upsell_bump_status_off <?php echo 'no' === $bump_status ? 'active' : ''; ?>"><?php esc_html_e( 'OFF', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
					</div>
				</div>

				<!-- Bump Name start.-->
				<tr valign="top">

					<th scope="row" class="titledesc">
						<label for="wps_upsell_bump_name"><?php esc_html_e( 'Name of the Order Bump', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php

						$description = esc_html__( 'Provide the name of your Order Bump.', 'upsell-order-bump-offer-for-woocommerce' );

						wps_ubo_lite_help_tip( $description );

						?>

						<input type="text" id="wps_upsell_bump_name" name="wps_upsell_bump_name" value="<?php echo esc_attr( $bump_name ); ?>" class="input-text wps_upsell_bump_commone_class" required="" maxlength="30">
					</td>
				</tr>
				<!-- Bump Name end.-->

				<!-- Bump Priority HTML start.-->
				<tr valign="top">

					<th scope="row" class="titledesc">
						<span class="wps_ubo_premium_strip">Pro</span>
						<label for="wps_upsell_bump_priority"><?php esc_html_e( 'Priority of the Order Bump', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php

						$description = esc_html__( 'Priortize Order Bump. Do not use same priority for multiple order bumps, this will override triggered order bump.', 'upsell-order-bump-offer-for-woocommerce' );

						wps_ubo_lite_help_tip( $description );

						?>

						<input type="number" id="wps_upsell_bump_priority" name="wps_upsell_bump_priority" value="<?php echo esc_attr( $bump_priority ); ?>" class="input-text wps_upsell_bump_commone_class" max="100000">
					</td>
				</tr>
				<!-- Bump Priority HTML end.-->

				<!-- Bump Min Cart HTML start.-->
				<tr valign="top">

					<th scope="row" class="titledesc">
						<span class="wps_ubo_premium_strip">Pro</span>
						<label for="wps_upsell_bump_min_cart"><?php esc_html_e( 'Minimum Cart Value for the Order Bump', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php

						$description = esc_html__( 'Set Minimum cart value to show the Order Bump.', 'upsell-order-bump-offer-for-woocommerce' );

						wps_ubo_lite_help_tip( $description );

						?>

						<input type="number" id="wps_upsell_bump_min_cart" name="wps_upsell_bump_min_cart" value="<?php echo esc_attr( $min_cart_value ); ?>" class="input-text wps_upsell_bump_commone_class" min="0">
					</td>
				</tr>
				<!-- Bump Min Cart end.-->

				<!-- Select Target product start. -->
				<tr valign="top">

					<th scope="row" class="titledesc">
						<label for="wps_upsell_bump_target_ids_search"><?php esc_html_e( 'Select target product(s)', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php

						$description = esc_html__( 'If any one of these Target Products is checked out then this Order Bump will be triggered and the below offer will be shown.', 'upsell-order-bump-offer-for-woocommerce' );

						wps_ubo_lite_help_tip( $description );

						?>

						<select id="wps_upsell_bump_target_ids_search" class="wc-bump-product-search" multiple="multiple" name="wps_upsell_bump_target_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'upsell-order-bump-offer-for-woocommerce' ); ?>">

							<?php

							if ( ! empty( $wps_upsell_bumps_list ) ) {

								$wps_upsell_bump_target_products = isset( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_target_ids'] ) ? $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_target_ids'] : array();

								// Array_map with absint converts negative array values to positive, so that we dont get negative ids.
								$wps_upsell_bump_target_products_ids = ! empty( $wps_upsell_bump_target_products ) ? array_map( 'absint', $wps_upsell_bump_target_products ) : null;

								if ( $wps_upsell_bump_target_products_ids ) {

									foreach ( $wps_upsell_bump_target_products_ids as $wps_upsell_bump_single_target_products_ids ) {

										$product_name = wps_ubo_lite_get_title( $wps_upsell_bump_single_target_products_ids );
										?>

										<option value="<?php echo esc_html( $wps_upsell_bump_single_target_products_ids ); ?>" selected="selected"><?php echo ( esc_html( $product_name ) . '(#' . esc_html( $wps_upsell_bump_single_target_products_ids ) . ')' ); ?></option>';

										<?php
									}
								}
							}

							?>
						</select>
					</td>
				</tr>

				<!-- Target category starts. -->
				<tr valign="top">

					<th scope="row" class="titledesc">
						<label for="wps_upsell_bump_target_categories_search"><?php esc_html_e( 'Select target categories', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php

						$description = esc_html__( 'If any one of these Target categories is checked out then this Order Bump will be triggered and the below offer will be shown.', 'upsell-order-bump-offer-for-woocommerce' );

						wps_ubo_lite_help_tip( $description );

						?>

						<select id="wps_upsell_bump_target_categories_search" class="wc-bump-product-category-search" multiple="multiple" name="wps_upsell_bump_target_categories[]" data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'upsell-order-bump-offer-for-woocommerce' ); ?>">

							<?php

							if ( ! empty( $wps_upsell_bumps_list ) ) {

								$wps_upsell_bump_target_categories = isset( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_target_categories'] ) ? $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_target_categories'] : array();

								// Array_map with absint converts negative array values to positive, so that we dont get negative ids.
								$wps_upsell_bump_target_categories = ! empty( $wps_upsell_bump_target_categories ) ? array_map( 'absint', $wps_upsell_bump_target_categories ) : null;

								if ( $wps_upsell_bump_target_categories ) {

									foreach ( $wps_upsell_bump_target_categories as $single_target_category_id ) {

										$single_category_name = wps_ubo_lite_getcat_title( $single_target_category_id );

										?>
										<option value="<?php echo esc_html( $single_target_category_id ); ?>" selected="selected"><?php echo ( esc_html( $single_category_name ) . '(#' . esc_html( $single_target_category_id ) . ')' ); ?></option>';
										<?php
									}
								}
							}

							?>
						</select>
					</td>
				</tr>
				<!-- Target category ends. -->

				<!-- Exclude roles starts. -->
				<tr valign="top">

					<th scope="row" class="titledesc">
						<label for="wps_upsell_bump_exclude_roles"><?php esc_html_e( 'Select roles to exclude', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php

						$description = esc_html__( 'Order Bumps will not be shown to these roles.', 'upsell-order-bump-offer-for-woocommerce' );

						wps_ubo_lite_help_tip( $description );

						?>

						<select id="wps_upsell_bump_exclude_roles" class="wc-bump-exclude-roles-search" multiple="multiple" name="wps_upsell_bump_exclude_roles[]" data-placeholder="<?php esc_attr_e( 'Search for a role&hellip;', 'upsell-order-bump-offer-for-woocommerce' ); ?>">

							<?php

							$wps_upsell_bump_exclude_roles = isset( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_exclude_roles'] ) ? $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_exclude_roles'] : array();

							if ( ! empty( $editable_roles ) && is_array( $editable_roles ) ) {

								foreach ( $editable_roles as $key => $value ) {

									?>
									<option <?php echo in_array( (string) $key, (array) $wps_upsell_bump_exclude_roles, true ) ? 'selected' : ''; ?> value="<?php echo esc_html( $key ); ?>"><?php echo esc_html( $value['name'] ); ?></option>
									<?php
								}
							}

							?>
						</select>
					</td>
				</tr>
				<!-- Exclude roles ends. -->


				<?php $wps_ubo_global_options = get_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() ); ?>
				<?php $wps_bump_enable_campaign_labels = ! empty( $wps_ubo_global_options['wps_bump_enable_campaign_labels'] ) ? $wps_ubo_global_options['wps_bump_enable_campaign_labels'] : ''; ?>
				<?php if ( 'on' === $wps_bump_enable_campaign_labels ) { ?>
					<tr valign="top">
						<th scope="row" class="titledesc">

							<label for="wps_ubo_offer_replace_target"><?php esc_html_e( 'Set Campaign label', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
						</th>

						<td class="forminp forminp-text">

							<?php
							$attribute_description = esc_html__( 'This feature allows you to set the campaign label for the order bump offer.', 'upsell-order-bump-offer-for-woocommerce' );
							wps_ubo_lite_help_tip( $attribute_description );

							// Retrieve the global options. Use an empty array as a default fallback.
							$wps_bump_upsell_global_options = get_option( 'wps_ubo_global_options', array() );

							// if the key doesn't exist.
							$labels = isset( $wps_bump_upsell_global_options['wps_bump_label'] ) ? (array) $wps_bump_upsell_global_options['wps_bump_label'] : array();
							$wps_bump_label_campaign = ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_bump_label_campaign'] ) ? sanitize_text_field( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_bump_label_campaign'] ) : '';
							wps_render_campaign_label_select(
								array(
									'id'          => 'wps_bump_label_campaign_select',
									'name'        => 'wps_bump_label_campaign',
									'options'     => $labels,
									'value'       => $wps_bump_label_campaign,
									'placeholder' => 'Select a campaign label',
									'width'       => '320px',
								)
							);
							?>


						</td>
					</tr>
				<?php } ?>


				<!-- Schedule your Bump start. -->
				<tr valign="top">

					<th scope="row" class="titledesc">
						<label for="wps_upsell_bump_schedule_select"><?php esc_html_e( 'Order Bump Schedule', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php

						$description = __( 'Schedule your Order Bump for specific weekdays.', 'upsell-order-bump-offer-for-woocommerce' );

						wps_ubo_lite_help_tip( $description );

						?>

						<?php

						// For earlier versions we will get a string over here.
						if ( ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_schedule'] ) && ! is_array( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_schedule'] ) ) {

							// Whatever was the selected day, add as an array.
							$wps_ubo_selected_schedule = array( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_schedule'] );
						} else {

							$wps_ubo_selected_schedule = ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_schedule'] ) ? ( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_schedule'] ) : array( '0' );
						}

						?>

						<select id="wps_upsell_bump_schedule_select" class="wc-bump-schedule-search wps_upsell_bump_schedule" multiple="multiple" name="wps_upsell_bump_schedule[]" data-placeholder="<?php esc_attr_e( 'Search for a specific days&hellip;', 'upsell-order-bump-offer-for-woocommerce' ); ?>">

							<?php foreach ( $wps_upsell_bump_schedule_options as $key => $day ) : ?>

								<option <?php echo in_array( (string) $key, $wps_ubo_selected_schedule, true ) ? 'selected' : ''; ?> value="<?php echo esc_html( $key ); ?>"><?php echo esc_html( $day ); ?></option>

							<?php endforeach; ?>

						</select>
					</td>
				</tr>
				<!-- Schedule your Bump end. -->

				<!-- Conditional Display start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="wps_ubo_condition_show"><?php esc_html_e( 'Bump Conditional Display', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">
						<?php
						$attribute_description = esc_html__( 'Enable dynamic conditions to control when this offer is displayed based on cart total, user role, coupons, and other criteria.', 'upsell-order-bump-offer-for-woocommerce' );
						wps_ubo_lite_help_tip( $attribute_description );
						$wps_ubo_condition_show = ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_ubo_condition_show'] ) ? $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_ubo_condition_show'] : 'no';
						if ( $is_conditional_display_template ) {
							$wps_ubo_condition_show = 'yes';
						}
						$wps_ubo_show_rules_button = ( 'yes' === $wps_ubo_condition_show );

						?>

						<label class="wps-upsell-smart-offer-upgrade" for="wps_ubo_condition_show">
							<input class="wps-upsell-smart-offer-upgrade-wrap" type='checkbox' id='wps_ubo_condition_show' name='wps_ubo_condition_show' value='yes' <?php echo ! empty( $wps_ubo_condition_show ) && 'yes' === $wps_ubo_condition_show ? 'checked' : ''; ?>>
							<span class="upsell-smart-offer-upgrade-btn"></span>
						</label>

						<label>
							<!-- Discount Condition Button, initially hidden -->
							<button id="show-discount-conditions" class="button button-primary" style="<?php echo $wps_ubo_show_rules_button ? '' : 'display:none;'; ?>">Add Conditional Rules</button>
						</label>
					</td>
				</tr>


				<!-- Conditional Display end. -->

				<!-- Replace with target start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">

						<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>

						<label for="wps_ubo_offer_replace_target"><?php esc_html_e( 'Smart Offer Upgrade', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php
						$attribute_description = esc_html__( 'This feature allows you to replace offer product to target product when added via order bump.', 'upsell-order-bump-offer-for-woocommerce' );
						wps_ubo_lite_help_tip( $attribute_description );
						?>

						<label class="wps-upsell-smart-offer-upgrade" for="wps_ubo_offer_replace_target">
							<input class="wps-upsell-smart-offer-upgrade-wrap" type='checkbox' id='wps_ubo_offer_replace_target' value=''>
							<span class="upsell-smart-offer-upgrade-btn"></span>
						</label>

					</td>
				</tr>
				<!-- Replace with target end. -->

				<!-- Order Bump Limit start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">

						<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>

						<label for="wps_ubo_offer_exclusive_limit"><?php esc_html_e( 'Exclusive Limits', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php
						$attribute_description = esc_html__( 'This feature allows you to limit Order bump for some exclusive sales/order counts.', 'upsell-order-bump-offer-for-woocommerce' );
						wps_ubo_lite_help_tip( $attribute_description );
						?>

						<label class="wps-upsell-smart-offer-upgrade" for="wps_ubo_offer_exclusive_limit">
							<input class="wps-upsell-smart-offer-upgrade-wrap" type='checkbox' id='wps_ubo_offer_exclusive_limit' value=''>
							<span class="upsell-smart-offer-upgrade-btn"></span>
						</label>

					</td>
				</tr>
				<!-- Order Bump Limit end. -->

				<!-- Meta Forms start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">

						<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>

						<label for="wps_ubo_offer_meta_forms"><?php esc_html_e( 'Meta Forms', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php
						$attribute_description = esc_html__( 'This feature allows you to add a custom form to receive answers before adding Offer product.', 'upsell-order-bump-offer-for-woocommerce' );
						wps_ubo_lite_help_tip( $attribute_description );
						?>

						<label class="wps-upsell-smart-offer-upgrade" for="wps_ubo_offer_meta_forms">
							<input class="wps-upsell-smart-offer-upgrade-wrap" type='checkbox' id='wps_ubo_offer_meta_forms' value=''>
							<span class="upsell-smart-offer-upgrade-btn"></span>
						</label>

					</td>
				</tr>
				<!-- Meta Forms end. -->

				<!-- V2.0.1 with global funnel start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">

						<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<label for="wps_ubo_offer_global_funnel"><?php esc_html_e( 'Global Order Bump', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php
						$attribute_description = esc_html__( 'This feature allows you to trigger this specific order bump, no matter target product is present or not.', 'upsell-order-bump-offer-for-woocommerce' );
						wps_ubo_lite_help_tip( $attribute_description );
						?>

						<label class="wps-upsell-smart-offer-upgrade" for="wps_ubo_offer_global_funnel">
							<input class="wps-upsell-smart-offer-upgrade-wrap" type='checkbox' id='wps_ubo_offer_global_funnel' value=''>
							<span class="upsell-smart-offer-upgrade-btn"></span>
						</label>

					</td>
				</tr>
				<!-- V2.0.1 with global funnel end. -->

				<!--v2.1.3 Timer Enable/Disable Start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">

						<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<label for="wps_ubo_offer_timer"><?php esc_html_e( 'Countdown Timer', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php
						$attribute_description = esc_html__( 'This feature allows you to add countdown timer at  specific order bump , when timer end it restrict offer product to add.', 'upsell-order-bump-offer-for-woocommerce' );
						wps_ubo_lite_help_tip( $attribute_description );
						?>

						<label class="wps-upsell-smart-offer-upgrade" for="wps_ubo_offer_timer">
							<input class="wps-upsell-smart-offer-upgrade-wrap" type='checkbox' id='wps_ubo_offer_timer' value=''>
							<span class="upsell-smart-offer-upgrade-btn"></span>
						</label>

					</td>
				</tr>
				<!--v2.1.3 Timer Enable/Disable End. -->

				<!--v2.2.0 Evergreen Timer Start Here. -->
				<tr valign="top">
					<th scope="row" class="titledesc">

						<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<label for="wps_ubo_offer_timer"><?php esc_html_e( 'Evergreen Timer', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php
						$attribute_description = esc_html__( 'This feature allows you to add evergreen countdown timer at  specific order bump  , timer start on every reload and when timer end it restrict offer product to add.Its priority is less than countdown timer.', 'upsell-order-bump-offer-for-woocommerce' );
						wps_ubo_lite_help_tip( $attribute_description );
						?>

						<label class="wps-upsell-smart-offer-upgrade" for="wps_ubo_offer_timer">
							<input class="wps-upsell-smart-offer-upgrade-wrap" type='checkbox' id='wps_evergreen_timer_switch' value=''>
							<span class="upsell-smart-offer-upgrade-btn"></span>
						</label>

					</td>
				</tr>

				<!--v2.2.0 Evergreen Timer End Her. -->

				<!--v2.1.7 Product Image Slider Show Enable/Disable Start. -->
				<tr valign="top">
					<th scope="row" class="titledesc">

						<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<label for="wps_ubo_offer_product_image_slider"><?php esc_html_e( 'Product Gallery Image Slider', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php
						$attribute_description = esc_html__( 'This feature allows you to add Product Gallery Image With Slider  at  specific order bump ', 'upsell-order-bump-offer-for-woocommerce' );
						wps_ubo_lite_help_tip( $attribute_description );
						?>

						<label class="wps-upsell-smart-offer-upgrade" for="wps_ubo_offer_product_image_slider">
							<input class="wps-upsell-smart-offer-upgrade-wrap" type='checkbox' id='wps_ubo_offer_product_image_slider' value=''>
							<span class="upsell-smart-offer-upgrade-btn"></span>
						</label>

					</td>
				</tr>
				<!--v2.1.3 Timer Enable/Disable End. -->

			</tbody>
		</table>

		<div class="wps_upsell_bump_offers">
			<h1><?php esc_html_e( 'Order Bump Offer', 'upsell-order-bump-offer-for-woocommerce' ); ?></h1>
		</div>

		<?php

		// Offers with discount.
		$wps_upsell_bump_product_in_offer = ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_products_in_offer'] ) ? sanitize_text_field( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_products_in_offer'] ) : '';

		// Offers with discount.
		$wps_upsell_bump_products_discount = ( ! empty( $wps_upsell_bumps_list ) && '' !== $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_offer_discount_price'] ) ? $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_offer_discount_price'] : '20';

		?>
		<!-- Loader for template generation starts. -->
		<div class="wps_ubo_animation_loader">
			<img src="images/spinner-2x.gif">
		</div>
		<!-- Loader for template generation ends. -->

		<!-- Bump Offers Start.-->
		<div class="new_offers">

			<!-- Single offer html start. -->
			<div class="new_created_offers wps_upsell_single_offer" data-scroll-id="#offer-section-1">

				<h2 class="wps_upsell_offer_title">
					<?php esc_html_e( 'Offer Section', 'upsell-order-bump-offer-for-woocommerce' ); ?>
				</h2>

				<table>
					<!-- Offer product start. -->
					<tr>

						<th scope="row" class="titledesc">
							<label for="wps_upsell_offer_product_select"><?php esc_html_e( 'Offer Product', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
						</th>

						<td class="forminp forminp-text">

							<select class="wc-offer-product-search wps_upsell_offer_product" id="wps_upsell_offer_product_select" name="wps_upsell_bump_products_in_offer" data-placeholder="<?php esc_html_e( 'Search for a product&hellip;', 'upsell-order-bump-offer-for-woocommerce' ); ?>">

								<?php
								$current_offer_product_id = ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_products_in_offer'] ) ? sanitize_text_field( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_products_in_offer'] ) : '';

								if ( ! empty( $current_offer_product_id ) ) {

									$product_title = wps_ubo_lite_get_title( $current_offer_product_id );

									?>

									<option value="<?php echo esc_html( $current_offer_product_id ); ?>" selected="selected"><?php echo esc_html( $product_title ) . '( #' . esc_html( $current_offer_product_id ) . ' )'; ?>
									</option>

									<?php

								}
								?>
							</select>

							<span class="wps_upsell_offer_description wps_upsell_offer_desc_text"><?php esc_html_e( 'Select the product you want to show as offer.', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						</td>
					</tr>
					<!-- Offer product end. -->

					<!-- Offer price start. -->
					<tr>
						<th scope="row" class="titledesc">
							<label for="wps_upsell_offer_price_type_id"><?php esc_html_e( 'Offer Price/Discount', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
						</th>

						<td class="forminp forminp-text">
							<select name="wps_upsell_offer_price_type" id='wps_upsell_offer_price_type_id'>

								<option <?php echo esc_html( '%' === $wps_bump_offer_type ? 'selected' : '' ); ?> value="%"><?php esc_html_e( 'Discount %', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>
								<option <?php echo esc_html( 'fixed' === $wps_bump_offer_type ? 'selected' : '' ); ?> value="fixed"><?php esc_html_e( 'Fixed price', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>
								<option <?php echo esc_html( 'no_disc' === $wps_bump_offer_type ? 'selected' : '' ); ?> value="no_disc"><?php esc_html_e( 'No Discount', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>

							</select>
							<input type="number" min="0" step="any" class="wps_upsell_offer_input_type" class="wps_upsell_offer_price" name="wps_upsell_bump_offer_discount_price" value="<?php echo esc_html( $wps_upsell_bump_products_discount ); ?>">
							<span class="wps_upsell_offer_description"><?php esc_html_e( 'Specify new offer price or discount %', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>

						</td>
					</tr>
					<!-- Offer price end. -->
					<!-- Offer image start. -->
					<tr>
						<th scope="row" class="titledesc">
							<label for="wps_upsell_offer_custom_image"><?php esc_html_e( 'Offer Image', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
						</th>

						<td class="forminp forminp-text">
							<?php

							$image_post_id = ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_offer_image'] ) ? $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_offer_image'] : '';

							Upsell_Order_Bump_Offer_For_Woocommerce_Admin::wps_ubo_image_uploader_field( $image_post_id );
							?>
						</td>
					</tr>
					<!-- Offer image end. -->
				</table>

			</div>
			<!-- Single offer html end. -->
		</div>

		<!-- Appearance Section starts.	-->
		<?php

		// If Offer product is Saved only then show Appearance section.
		if ( ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_upsell_bump_products_in_offer'] ) ) :

			?>

			<div class="wps_upsell_offer_templates"><?php esc_html_e( 'Appearance', 'upsell-order-bump-offer-for-woocommerce' ); ?></div>

			<!-- Nav starts. -->
			<nav class="nav-tab-wrapper wps-ubo-appearance-nav-tab">
				<a class="nav-tab wps-ubo-appearance-template nav-tab-active" href="javascript:void(0);"><?php esc_html_e( 'Template', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
				<a class="nav-tab wps-ubo-appearance-design" href="javascript:void(0);"><?php esc_html_e( 'Design', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
				<a class="nav-tab wps-ubo-appearance-text" href="javascript:void(0);"><?php esc_html_e( 'Content', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
			</nav>
			<!-- Nav ends. -->

			<!-- Appearance Starts. -->
			<div class="wps_upsell_template_div_wrapper">
				<!-- Template start -->
				<div class="wps-ubo-template-section">
					<?php

					$wps_bump_upsell_selected_template = ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_bump_upsell_selected_template'] ) ? $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_bump_upsell_selected_template'] : '';

					$wps_ubo_selected_template = ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_ubo_selected_template'] ) ? $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['wps_ubo_selected_template'] : '1';
					?>

					<!-- Image wrapper -->
					<div id="available_tab" class="wps_ubo_temp_class wps_upsell_template_select-wrapper">

						<!-- Template one. -->
						<div class="wps_upsell_template_select <?php echo esc_html( 1 === (int) $wps_ubo_selected_template ? 'wps_ubo_selected_class' : '' ); ?>">

							<input type="hidden" class="wps_ubo_template" name="wps_bump_upsell_selected_template" value="<?php echo esc_html( $wps_bump_upsell_selected_template ); ?>">

							<input type="hidden" class="wps_ubo_selected_template" name="wps_ubo_selected_template" value="<?php echo esc_html( $wps_ubo_selected_template ); ?>">

							<p class="wps_ubo_template_name"><?php esc_html_e( 'Dazzling Bliss', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
							<a href="javascript:void" class="wps_ubo_template_link" data_link='1'>
								<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/offer-templates/template-1.png' ); ?>">
							</a>
						</div>

						<!-- Template two. -->
						<div class="wps_upsell_template_select <?php echo esc_html( 2 === (int) $wps_ubo_selected_template ? 'wps_ubo_selected_class' : '' ); ?> ">

							<p class="wps_ubo_template_name"><?php esc_html_e( 'Alluring Lakeside', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
							<a href="javascript:void" class="wps_ubo_template_link" data_link='2'>
								<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/offer-templates/template-2.png' ); ?>">
							</a>
						</div>

						<!-- Template ten. -->
						<div class="wps_upsell_template_select <?php echo esc_html( 10 === (int) $wps_ubo_selected_template ? 'wps_ubo_selected_class' : '' ); ?> ">

							<p class="wps_ubo_template_name"><?php esc_html_e( 'Flexie Template', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
							<a href="javascript:void" class="wps_ubo_template_link" data_link='10'>
								<?php if ( file_exists( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_DIR_PATH . 'admin/resources/offer-templates/template-1.png' ) ) : ?>
									<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/offer-templates/template-10.png' ); ?>">
								<?php else : ?>
									<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/Offer templates/Template2.png' ); ?>">
								<?php endif; ?>
							</a>
						</div>

						<!-- Template eleven. -->
						<div class="wps_upsell_template_select <?php echo esc_html( 11 === (int) $wps_ubo_selected_template ? 'wps_ubo_selected_class' : '' ); ?> ">
							<p class="wps_ubo_template_name"><?php esc_html_e( 'Compactive Template', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
							<a href="javascript:void" class="wps_ubo_template_link" data_link='11'>
								<?php if ( file_exists( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_DIR_PATH . 'admin/resources/offer-templates/template-1.png' ) ) : ?>
									<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/offer-templates/template-11.png' ); ?>">
								<?php else : ?>
									<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/Offer templates/Template2.png' ); ?>">
								<?php endif; ?>
							</a>
						</div>

						<!-- Template three. -->
						<div id="wps_ubo_premium_popup_3_template" class="wps_upsell_template_select <?php echo esc_html( 3 === (int) $wps_ubo_selected_template ? 'wps_ubo_selected_class' : '' ); ?> ">

							<p class="wps_ubo_template_name"><?php esc_html_e( 'Elegant Summers', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
							<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<a href="javascript:void" data_link='3'>
								<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/offer-templates/template-3.png' ); ?>">
							</a>
						</div>

						<!-- Template four. -->
						<div id="wps_ubo_premium_popup_4_template" class="wps_upsell_template_select  <?php echo esc_html( 4 === (int) $wps_ubo_selected_template ? 'wps_ubo_selected_class' : '' ); ?> ">

							<p class="wps_ubo_template_name"><?php esc_html_e( 'Winner Jazz ', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
							<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<a href="javascript:void" data_link='4'>
								<?php if ( file_exists( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_DIR_PATH . 'admin/resources/offer-templates/template-1.png' ) ) : ?>
									<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/offer-templates/template-4.png' ); ?>">
								<?php else : ?>
									<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/Offer templates/Template4.png' ); ?>">
								<?php endif; ?>
							</a>
						</div>

						<!-- Template five. -->
						<div id="wps_ubo_premium_popup_5_template" class="wps_upsell_template_select  <?php echo esc_html( 5 === (int) $wps_ubo_selected_template ? 'wps_ubo_selected_class' : '' ); ?> ">

							<p class="wps_ubo_template_name"><?php esc_html_e( 'Summer Cool ', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
							<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<a href="javascript:void" data_link='5'>
								<?php if ( file_exists( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_DIR_PATH . 'admin/resources/offer-templates/template-1.png' ) ) : ?>
									<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/offer-templates/template-5.png' ); ?>">
								<?php else : ?>
									<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/Offer templates/Template-5.png' ); ?>">
								<?php endif; ?>
							</a>
						</div>
						<!-- Template six. -->
						<div id="wps_ubo_premium_popup_6_template" class="wps_upsell_template_select  <?php echo esc_html( 6 === (int) $wps_ubo_selected_template ? 'wps_ubo_selected_class' : '' ); ?> ">

							<p class="wps_ubo_template_name"><?php esc_html_e( 'Hybrid', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
							<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<a href="javascript:void" data_link='6'>
								<?php if ( file_exists( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_DIR_PATH . 'admin/resources/offer-templates/template-1.png' ) ) : ?>
									<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/offer-templates/template-6.png' ); ?>">
								<?php else : ?>
									<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/Offer templates/Template-6.png' ); ?>">
								<?php endif; ?>
							</a>
						</div>
						<!-- Template seven. -->
						<div id="wps_ubo_premium_popup_7_template" class="wps_upsell_template_select  <?php echo esc_html( 7 === (int) $wps_ubo_selected_template ? 'wps_ubo_selected_class' : '' ); ?> ">

							<p class="wps_ubo_template_name"><?php esc_html_e( 'Left To Right', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
							<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<a href="javascript:void" data_link='7'>
								<?php if ( file_exists( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_DIR_PATH . 'admin/resources/offer-templates/template-7.png' ) ) : ?>
									<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/offer-templates/template-7.png' ); ?>">
								<?php else : ?>
									<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/Offer templates/Template-7.png' ); ?>">
								<?php endif; ?>
							</a>
						</div>
						<!-- Template eight. -->
						<div id="wps_ubo_premium_popup_8_template" class="wps_upsell_template_select  <?php echo esc_html( 8 === (int) $wps_ubo_selected_template ? 'wps_ubo_selected_class' : '' ); ?> ">

							<p class="wps_ubo_template_name"><?php esc_html_e( 'Right To Left', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
							<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<a href="javascript:void" data_link='8'>
								<?php if ( file_exists( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_DIR_PATH . 'admin/resources/offer-templates/template-1.png' ) ) : ?>
									<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/offer-templates/template-8.png' ); ?>">
								<?php else : ?>
									<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/Offer templates/Template-8.png' ); ?>">
								<?php endif; ?>
							</a>
						</div>
						<!-- Template Nine. -->
						<div id="wps_ubo_premium_popup_9_template" class="wps_upsell_template_select  <?php echo esc_html( 9 === (int) $wps_ubo_selected_template ? 'wps_ubo_selected_class' : '' ); ?> ">

							<p class="wps_ubo_template_name"><?php esc_html_e( 'Vertical', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
							<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<a href="javascript:void" data_link='9'>
								<?php if ( file_exists( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_DIR_PATH . 'admin/resources/offer-templates/template-9.png' ) ) : ?>
									<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/offer-templates/template-9.png' ); ?>">
								<?php else : ?>
									<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/Offer templates/Template-9.png' ); ?>">
								<?php endif; ?>
							</a>
						</div>
						<!-- Template Winxy -->
						<div id="wps_ubo_premium_popup_9_template" class="wps_upsell_template_select  <?php echo esc_html( 9 === (int) $wps_ubo_selected_template ? 'wps_ubo_selected_class' : '' ); ?> ">

							<p class="wps_ubo_template_name"><?php esc_html_e( 'Vertical', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
							<span class="wps_ubo_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<a href="javascript:void" data_link='12'>
								<?php if ( file_exists( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_DIR_PATH . 'admin/resources/offer-templates/template-12.png' ) ) : ?>
									<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/offer-templates/template-12.png' ); ?>">
								<?php else : ?>
									<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/Offer templates/Template-12.png' ); ?>">
								<?php endif; ?>
							</a>
						</div>
					</div>
				</div>
				<!-- Template end -->

				<!-- Design start -->
				<div class="wps_upsell_table_column_wrapper wps-ubo-appearance-section-hidden">

					<div class="wps_upsell_table wps_upsell_table--border wps_upsell_custom_template_settings ">

						<div class="wps_upsell_offer_sections"><?php esc_html_e( 'Bump Offer Box', 'upsell-order-bump-offer-for-woocommerce' ); ?></div>
						<table class="form-table wps_upsell_bump_creation_setting">
							<tbody>

								<!-- Populate rest fields with available templates if not custom is checked. -->
								<?php

								if ( ! empty( $wps_bump_upsell_selected_template ) ) {

									// Load the css of selected template.
									$template_callb_func = 'wps_ubo_lite_offer_template_' . $wps_bump_upsell_selected_template;

									$wps_bump_enable_available_design = $template_callb_func();

									$wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css'] = $wps_bump_enable_available_design;
								}

								?>
								<!-- Border style start. -->
								<tr valign="top">

									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Border type', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
									</th>

									<td class="forminp forminp-text">
										<?php
										$attribute_description = esc_html__( 'Select among different border types for Bump Offer.', 'upsell-order-bump-offer-for-woocommerce' );

										wps_ubo_lite_help_tip( $attribute_description );

										?>

										<label>

											<!-- Select options for border. -->
											<select name="parent_border_type" class="wps_ubo_preview_select_border_type">

												<?php

												$border_type_array = array(
													'none' => esc_html__( 'No Border', 'upsell-order-bump-offer-for-woocommerce' ),
													'solid' => esc_html__( 'Solid', 'upsell-order-bump-offer-for-woocommerce' ),
													'dashed' => esc_html__( 'Dashed', 'upsell-order-bump-offer-for-woocommerce' ),
													'double' => esc_html__( 'Double', 'upsell-order-bump-offer-for-woocommerce' ),
													'dotted' => esc_html__( 'Dotted', 'upsell-order-bump-offer-for-woocommerce' ),

												);

												?>
												<option value=""><?php esc_html_e( '----Select Border Type----', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>

												<?php
												foreach ( $border_type_array as $value => $name ) :
													?>
													<option <?php echo esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['parent_border_type'] === $value ? 'selected' : '' ); ?> value="<?php echo esc_html( $value ); ?>"><?php echo esc_html( $name ); ?></option>
												<?php endforeach; ?>
											</select>

										</label>
									</td>
								</tr>
								<!-- Border style end. -->

								<!-- Border color start. -->
								<tr valign="top">

									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Border Color', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
									</th>

									<td class="forminp forminp-text">
										<?php
										$attribute_description = esc_html__( 'Select border color for Bump Offer.', 'upsell-order-bump-offer-for-woocommerce' );
										wps_ubo_lite_help_tip( $attribute_description );
										?>
										<label>
											<!-- Color picker for description background. -->
											<input type="text" name="parent_border_color" class="wps_ubo_colorpicker wps_ubo_preview_select_border_color" value="<?php echo ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['parent_border_color'] ) ? esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['parent_border_color'] ) : ''; ?>">
										</label>
									</td>

								</tr>
								<!-- Border color end. -->
								<?php if ( '11' != $wps_ubo_selected_template ) { ?>
									<!-- Background color start. -->
									<tr valign="top">

										<th scope="row" class="titledesc">
											<label><?php esc_html_e( 'Select Background Color', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
										</th>

										<td class="forminp forminp-text">
											<?php
											$attribute_description = esc_html__( 'Select Background Color for Bump Offer.', 'upsell-order-bump-offer-for-woocommerce' );
											wps_ubo_lite_help_tip( $attribute_description );
											?>
											<label>
												<!-- Color picker for description background. -->
												<input type="text" name="parent_background_color" class="wps_ubo_colorpicker wps_ubo_preview_select_background_color" value="<?php echo ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['parent_background_color'] ) ? esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['parent_background_color'] ) : ''; ?>">
											</label>
										</td>

									</tr>
								<?php } ?>
								<!-- Background color end. -->
								<!-- Top Vertical Spacing control start. -->
								<tr valign="top">

									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Top Vertical Spacing', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
									</th>

									<td class="forminp forminp-text">
										<?php
										$attribute_description = esc_html__( 'Add top spacing to the Bump Offer Box.', 'upsell-order-bump-offer-for-woocommerce' );
										wps_ubo_lite_help_tip( $attribute_description );
										?>

										<label>
											<!-- Slider for spacing. -->
											<input type="range" min="10" value="<?php echo esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['top_vertical_spacing'] ); ?>" max="40" value="" name='top_vertical_spacing' class="wps_ubo_top_vertical_spacing_slider" />
											<span class="wps_ubo_top_spacing_slider_size"><?php echo esc_html( ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['top_vertical_spacing'] ) ? esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['top_vertical_spacing'] . 'px' ) : '0px' ); ?></span>
										</label>
									</td>
								</tr>
								<!-- Top Vertical Spacing control ends. -->

								<!-- Bottom Vertical Spacing control start. -->
								<tr valign="top">
									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Bottom Vertical Spacing', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
									</th>

									<td class="forminp forminp-text">
										<?php
										$attribute_description = esc_html__( 'Add bottom spacing to the Bump Offer Box.', 'upsell-order-bump-offer-for-woocommerce' );
										wps_ubo_lite_help_tip( $attribute_description );
										?>
										<label>
											<!-- Slider for spacing. -->
											<input type="range" value="<?php echo esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['bottom_vertical_spacing'] ); ?>" min="10" max="40" value="" name='bottom_vertical_spacing' class="wps_ubo_bottom_vertical_spacing_slider" />
											<span class="wps_ubo_bottom_spacing_slider_size">
												<?php echo esc_html( ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['bottom_vertical_spacing'] ) ? esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['bottom_vertical_spacing'] . 'px' ) : '0px' ); ?>
											</span>
										</label>
									</td>
								</tr>
								<!-- Bottom Vertical Spacing control ends. -->

							</tbody>
						</table>
					</div>
					<!-- Global wrapper section. -->

					<!-- Discount_section section. -->
					<div class="wps_upsell_table wps_upsell_table--border wps_upsell_custom_template_settings ">
						<div class="wps_upsell_offer_sections"><?php esc_html_e( 'Discount Section', 'upsell-order-bump-offer-for-woocommerce' ); ?></div>
						<table class="form-table wps_upsell_bump_creation_setting">
							<tbody>
								<!-- Background color start. -->
								<tr valign="top">
									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Background Color', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
									</th>

									<td class="forminp forminp-text">
										<?php
										$attribute_description = esc_html__( 'Select background color for Discount section.', 'upsell-order-bump-offer-for-woocommerce' );
										wps_ubo_lite_help_tip( $attribute_description );
										?>
										<label>
											<!-- Color picker for description background. -->
											<input type="text" name="discount_section_background_color" class="wps_ubo_colorpicker wps_ubo_select_discount_bcolor" value="<?php echo ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['discount_section_background_color'] ) ? esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['discount_section_background_color'] ) : ''; ?>">

										</label>
									</td>
								</tr>
								<!-- Background color end. -->

								<!-- Text color start. -->
								<tr valign="top">
									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Text Color', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
									</th>

									<td class="forminp forminp-text">
										<?php
										$attribute_description = esc_html__( 'Select text color for Discount section.', 'upsell-order-bump-offer-for-woocommerce' );
										wps_ubo_lite_help_tip( $attribute_description );
										?>
										<label>
											<!-- Color picker for description text. -->
											<input type="text" name="discount_section_text_color" class="wps_ubo_colorpicker wps_ubo_select_discount_tcolor" value="<?php echo ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['discount_section_text_color'] ) ? esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['discount_section_text_color'] ) : ''; ?>">
										</label>
									</td>

								</tr>
								<!-- Text color end. -->

								<!-- Text size control start. -->
								<tr valign="top">
									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Text Size', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
									</th>

									<td class="forminp forminp-text">
										<?php
										$attribute_description = esc_html__( 'Select font size for Discount section.', 'upsell-order-bump-offer-for-woocommerce' );
										wps_ubo_lite_help_tip( $attribute_description );
										?>
										<label>
											<!-- Slider for spacing. -->
											<input type="range" min="20" value="<?php echo esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['discount_section_text_size'] ); ?>" max="50" value="" name='discount_section_text_size' class="wps_ubo_text_slider wps_ubo_discount_slider" />

											<span class="wps_ubo_slider_size wps_ubo_discount_slider_size"><?php echo esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['discount_section_text_size'] . 'px' ); ?></span>
										</label>
									</td>
								</tr>
								<!-- Text size control ends. -->
							</tbody>
						</table>
					</div>
					<!-- Discount_section section. -->

					<!-- Product_section section -->
					<div class="wps_upsell_table wps_upsell_table--border wps_upsell_custom_template_settings ">
						<div class="wps_upsell_offer_sections"><?php esc_html_e( 'Product Section', 'upsell-order-bump-offer-for-woocommerce' ); ?></div>
						<table class="form-table wps_upsell_bump_creation_setting">
							<tbody>

								<!-- Text color start. -->
								<tr valign="top">
									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Text Color', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
									</th>

									<td class="forminp forminp-text">
										<?php
										$attribute_description = esc_html__( 'Select text color for Product section.', 'upsell-order-bump-offer-for-woocommerce' );
										wps_ubo_lite_help_tip( $attribute_description );
										?>
										<label>
											<!-- Color picker for description text. -->
											<input type="text" name="product_section_text_color" class="wps_ubo_colorpicker wps_ubo_select_product_tcolor" value="<?php echo ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['product_section_text_color'] ) ? esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['product_section_text_color'] ) : ''; ?>">
										</label>
									</td>
								</tr>
								<!-- Text color end. -->

								<!-- Price Text color start. -->
								<tr valign="top">
									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Price Text Color', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
									</th>

									<td class="forminp forminp-text">
										<?php
										$attribute_description = esc_html__( 'Select text color for Product Price.', 'upsell-order-bump-offer-for-woocommerce' );
										wps_ubo_lite_help_tip( $attribute_description );
										?>
										<label>
											<!-- Color picker for description text. -->
											<input type="text" name="product_section_price_text_color" class="wps_ubo_colorpicker wps_ubo_select_product_price_tcolor" value="<?php echo ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['product_section_price_text_color'] ) ? esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['product_section_price_text_color'] ) : ''; ?>">
										</label>
									</td>
								</tr>
								<!-- Price Text color end. -->

								<!-- Text size control start. -->
								<tr valign="top">

									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Text Size', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
									</th>

									<td class="forminp forminp-text">
										<?php
										$attribute_description = esc_html__( 'Select font size for Product section.', 'upsell-order-bump-offer-for-woocommerce' );
										wps_ubo_lite_help_tip( $attribute_description );
										?>

										<label>

											<!-- Slider for spacing. -->
											<input type="range" min="10" value="<?php echo esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['product_section_text_size'] ); ?>" max="30" value="" name='product_section_text_size' class="wps_ubo_text_slider wps_ubo_product_slider" />

											<span class="wps_ubo_slider_size wps_ubo_product_slider_size"><?php echo esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['product_section_text_size'] . 'px' ); ?> </span>
										</label>
									</td>

								</tr>
								<!-- Text size control ends. -->

								<!-- Price size control start. -->
								<tr valign="top">
									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Product Price Size', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
									</th>

									<td class="forminp forminp-text">
										<?php
										$attribute_description = esc_html__( 'Select font size for Product Price.', 'upsell-order-bump-offer-for-woocommerce' );
										wps_ubo_lite_help_tip( $attribute_description );
										?>
										<label>
											<!-- Slider for spacing. -->
											<input type="range" min="10" value="<?php echo esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['product_section_price_text_size'] ); ?>" max="30" value="" name='product_section_price_text_size' class="wps_ubo_text_slider wps_ubo_product_price_slider" />

											<span class="wps_ubo_slider_size wps_ubo_product_price_slider_size"><?php echo esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['product_section_price_text_size'] . 'px' ); ?></span>
										</label>
									</td>
								</tr>
								<!-- Price size control end. -->

								<!-- Image width control start. -->
								<tr valign="top" id="wps_ubo_img_width_slider_pop_up">
									<th scope="row" class="titledesc">
										<span class="wps_ubo_premium_strip">Pro</span>
										<label><?php esc_html_e( 'Select Image Width', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
									</th>

									<td class="forminp forminp-text">
										<?php
										$attribute_description = esc_html__( 'Select Image width size for Product section.', 'upsell-order-bump-offer-for-woocommerce' );
										wps_ubo_lite_help_tip( $attribute_description );
										?>

										<label>
											<!-- Slider for spacing. -->
											<input type="range" min="10" value="10" max="125" name='product_section_img_width' class="wps_ubo_img_width_slider" />

											<span class="wps_ubo_slider_size wps_ubo_product_slider_width"><?php echo esc_html( '10px' ); ?> </span>
										</label>
									</td>
								</tr>
								<!-- Image width control ends. -->

								<!-- Image height control start. -->
								<tr valign="top" id="wps_ubo_img_height_slider_pop_up">
									<th scope="row" class="titledesc">
										<span class="wps_ubo_premium_strip">Pro</span>
										<label><?php esc_html_e( 'Select Image Height', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
									</th>

									<td class="forminp forminp-text">
										<?php
										$attribute_description = esc_html__( 'Select Image height for Product section.', 'upsell-order-bump-offer-for-woocommerce' );
										wps_ubo_lite_help_tip( $attribute_description );
										?>

										<label>

											<!-- Slider for spacing. -->
											<input type="range" min="10" value="0" max="150" name='product_section_img_height' class="wps_ubo_img_height_slider" />

											<span class="wps_ubo_slider_size wps_ubo_product_slider_height"><?php echo esc_html( '10px' ); ?> </span>
										</label>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<!-- Product_section section. -->

					<!-- Primary_section section. -->
					<div class="wps_upsell_table wps_upsell_table--border wps_upsell_custom_template_settings ">
						<div class="wps_upsell_offer_sections"><?php esc_html_e( 'Accept Offer Section', 'upsell-order-bump-offer-for-woocommerce' ); ?></div>

						<table class="form-table wps_upsell_bump_creation_setting">
							<tbody>
								<!-- Background color start. -->
								<tr valign="top">
									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Background Color', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
									</th>

									<td class="forminp forminp-text">
										<?php
										$attribute_description = esc_html__( 'Select background color for Accept Offer section.', 'upsell-order-bump-offer-for-woocommerce' );
										wps_ubo_lite_help_tip( $attribute_description );
										?>
										<label>
											<!-- Color picker for description background. -->
											<input type="text" name="primary_section_background_color" class="wps_ubo_colorpicker wps_ubo_select_accept_offer_bcolor" value="<?php echo ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['primary_section_background_color'] ) ? esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['primary_section_background_color'] ) : ''; ?>">
										</label>
									</td>
								</tr>
								<!-- Background color end. -->

								<!-- Text color start. -->
								<tr valign="top">
									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Text Color', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
									</th>

									<td class="forminp forminp-text">
										<?php
										$attribute_description = esc_html__( 'Select text color for Accept Offer section.', 'upsell-order-bump-offer-for-woocommerce' );
										wps_ubo_lite_help_tip( $attribute_description );
										?>
										<label>
											<!-- Color picker for description text. -->
											<input type="text" name="primary_section_text_color" class="wps_ubo_colorpicker wps_ubo_select_accept_offer_tcolor" value="<?php echo ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['primary_section_text_color'] ) ? esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['primary_section_text_color'] ) : ''; ?>">
										</label>
									</td>
								</tr>
								<!-- Text color end. -->

								<!-- Arrow color start. -->
								<?php if ( '12' != $wps_ubo_selected_template ) { ?>
									<tr valign="top" id="wps_ubo_select_accept_offer_acolor_pop_up">
										<th scope="row" class="titledesc">
											<span class="wps_ubo_premium_strip">Pro</span>
											<label><?php esc_html_e( 'Select Arrow Color', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
										</th>

										<td class="forminp forminp-text">
											<?php
											$attribute_description = esc_html__( 'Select Arrow color for Accept Offer section.', 'upsell-order-bump-offer-for-woocommerce' );
											wps_ubo_lite_help_tip( $attribute_description );
											?>
											<label>
												<!-- Color picker for description text. -->
												<input type="text" name="primary_section_arrow_color" class="wps_ubo_colorpicker" value="">
											</label>
										</td>
									</tr>
								<?php } ?>
								<!-- Arrow color end. -->

								<!-- Text size control start. -->
								<tr valign="top">
									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Text Size', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
									</th>
									<td class="forminp forminp-text">
										<?php
										$attribute_description = esc_html__( 'Select font size for Accept Offer section.', 'upsell-order-bump-offer-for-woocommerce' );
										wps_ubo_lite_help_tip( $attribute_description );
										?>
										<label>
											<!-- Slider for spacing. -->
											<input type="range" min="10" value="<?php echo esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['primary_section_text_size'] ); ?>" max="30" value="" name='primary_section_text_size' class="wps_ubo_text_slider wps_ubo_accept_offer_slider" />
											<span class="wps_ubo_slider_size wps_ubo_accept_offer_slider_size"><?php echo esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['primary_section_text_size'] . 'px' ); ?></span>
										</label>
									</td>
								</tr>
								<!-- Text size control ends. -->
							</tbody>
						</table>
					</div>
					<!-- Primary_section section. -->

					<!-- Secondary_section section. -->
					<?php if ( '10' != $wps_ubo_selected_template && '11' != $wps_ubo_selected_template ) { ?>
						<div class="wps_upsell_table wps_upsell_table--border wps_upsell_custom_template_settings ">
							<div class="wps_upsell_offer_sections"><?php esc_html_e( 'Offer Description Section', 'upsell-order-bump-offer-for-woocommerce' ); ?></div>
							<table class="form-table wps_upsell_bump_creation_setting">
								<tbody>
									<!-- Background color start. -->
									<?php if ( '12' != $wps_ubo_selected_template ) { ?>
										<tr valign="top">
											<th scope="row" class="titledesc">
												<label><?php esc_html_e( 'Select Background Color', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
											</th>
											<td class="forminp forminp-text">
												<?php
												$attribute_description = esc_html__( 'Select background color for Offer Description section.', 'upsell-order-bump-offer-for-woocommerce' );
												wps_ubo_lite_help_tip( $attribute_description );
												?>
												<label>
													<!-- Color picker for description background. -->
													<input type="text" name="secondary_section_background_color" class="wps_ubo_colorpicker wps_ubo_select_offer_description_bcolor" value="<?php echo ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['secondary_section_background_color'] ) ? esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['secondary_section_background_color'] ) : ''; ?>">
												</label>
											</td>
										</tr>
									<?php } ?>
									<!-- Background color end. -->

									<!-- Text color start. -->
									<tr valign="top">
										<th scope="row" class="titledesc">
											<label><?php esc_html_e( 'Select Text Color', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
										</th>
										<td class="forminp forminp-text">
											<?php
											$attribute_description = esc_html__( 'Select text color for Offer Description section.', 'upsell-order-bump-offer-for-woocommerce' );
											wps_ubo_lite_help_tip( $attribute_description );
											?>
											<!-- Color picker for description text. -->
											<input type="text" name="secondary_section_text_color" class="wps_ubo_colorpicker wps_ubo_select_offer_description_tcolor" value="<?php echo ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['secondary_section_text_color'] ) ? esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['secondary_section_text_color'] ) : ''; ?>">
										</td>
									</tr>
									<!-- Text color end. -->

									<!-- Text size control start -->
									<tr valign="top">
										<th scope="row" class="titledesc">
											<label><?php esc_html_e( 'Select Text Size', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
										</th>
										<td class="forminp forminp-text">
											<?php
											$attribute_description = esc_html__( 'Select font size for Offer Description section.', 'upsell-order-bump-offer-for-woocommerce' );
											wps_ubo_lite_help_tip( $attribute_description );
											?>
											<!-- Slider for spacing. -->
											<input type="range" min="10" value="<?php echo esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['secondary_section_text_size'] ); ?>" max="30" value="" name='secondary_section_text_size' class="wps_ubo_text_slider wps_ubo_offer_description_slider" />

											<span class="wps_ubo_slider_size wps_ubo_offer_description_slider_size"><?php echo esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_css']['secondary_section_text_size'] . 'px' ); ?></span>
										</td>
									</tr>
									<!-- Text size control ends. -->
								</tbody>
							</table>
						</div>
					<?php } ?>
					<!-- Secondary_section section ends. -->
				</div>
				<!-- Design end -->

				<!-- Text start -->
				<div class="wps-ubo-text-section wps_upsell_table--border wps-ubo-appearance-section-hidden wps_upsell_table">
					<table>
						<!-- Discount Title start. -->
						<tr valign="top">
							<th scope="row" class="titledesc">
								<p class='wps_ubo_row_heads'><?php esc_html_e( 'Discount Title', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
							</th>

							<td class="forminp forminp-text">
								<?php
								$attribute_description = sprintf(
									'%s<br>%s %s<br>%s %s',
									esc_html__( 'Discount title content. Please use at respective places :', 'upsell-order-bump-offer-for-woocommerce' ),
									'&rarr; {dc_%}',
									esc_html__( 'for % discount.', 'upsell-order-bump-offer-for-woocommerce' ),
									'&rarr; {dc_price}',
									esc_html__( 'for fixed discount price.', 'upsell-order-bump-offer-for-woocommerce' )
								);

								wps_ubo_lite_help_tip( $attribute_description );

								$wps_ubo_discount_title_for_fixed = ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_text']['wps_ubo_discount_title_for_fixed'] ) ? $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_text']['wps_ubo_discount_title_for_fixed'] : '';

								$wps_ubo_discount_title_for_percent = ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_text']['wps_ubo_discount_title_for_percent'] ) ? $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_text']['wps_ubo_discount_title_for_percent'] : '';

								?>
								<div class="d-inline-block">
									<p><?php esc_html_e( 'For Discount %', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>

									<input class="wps_upsell_offer_input_type" type="text" text_id="percent" name="wps_ubo_discount_title_for_percent" value="<?php echo esc_attr( $wps_ubo_discount_title_for_percent ); ?>">

									<p>
										<?php esc_html_e( 'For Fixed Price', 'upsell-order-bump-offer-for-woocommerce' ); ?>
									</p>

									<input class="wps_upsell_offer_input_type" type="text" name="wps_ubo_discount_title_for_fixed" text_id="fixed" value="<?php echo esc_html( $wps_ubo_discount_title_for_fixed ); ?>">
								</div>
							</td>
						</tr>
						<!--Discount Title end. -->

						<!-- Product Description start. -->
						<?php if ( '10' != $wps_ubo_selected_template ) { ?>
							<tr valign="top">
								<th scope="row" class="titledesc">
									<p class='wps_ubo_row_heads'><?php esc_html_e( 'Product Description', 'upsell-order-bump-offer-for-woocommerce' ); ?>
									</p>
								</th>

								<td class="forminp forminp-text" colspan="2">

									<?php
									$attribute_description = esc_html__( 'Bump Offer Product description content.', 'upsell-order-bump-offer-for-woocommerce' );

									wps_ubo_lite_help_tip( $attribute_description );

									$wps_bump_offer_decsription_text = ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_text']['wps_bump_offer_decsription_text'] ) ? $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_text']['wps_bump_offer_decsription_text'] : '';
									?>

									<textarea class="wps_textarea_class" text_id="pro_desc" rows="4" cols="50" name="wps_bump_offer_decsription_text"><?php echo esc_html( $wps_bump_offer_decsription_text ); ?></textarea>

								</td>
							</tr>
						<?php } ?>
						<!-- Product Description end. -->

						<!-- Lead Title start. -->
						<tr valign="top">
							<th scope="row" class="titledesc">
								<p class='wps_ubo_row_heads'><?php esc_html_e( ' Lead Title ', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
							</th>

							<td class="forminp forminp-text">
								<?php
								$attribute_description = esc_html__( 'Bump offer Lead title content.', 'upsell-order-bump-offer-for-woocommerce' );
								wps_ubo_lite_help_tip( $attribute_description );
								?>

								<?php

								$offer_lead_title = ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_text']['wps_upsell_offer_title'] ) ? $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_text']['wps_upsell_offer_title'] : '';

								?>

								<input type="text" class="wps_upsell_offer_input_type" name="wps_upsell_offer_title" text_id="lead" value="<?php echo esc_html( $offer_lead_title ); ?>">

							</td>
						</tr>
						<!--Lead Title ends.-->

						<!-- Offer Description start. -->
						<tr valign="top">
							<th scope="row" class="titledesc">
								<p class='wps_ubo_row_heads'><?php esc_html_e( 'Offer Description ', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
							</th>

							<td class="forminp forminp-text" colspan="2">
								<?php
								$attribute_description = esc_html__( 'Bump Offer description content.', 'upsell-order-bump-offer-for-woocommerce' );
								wps_ubo_lite_help_tip( $attribute_description );

								$wps_upsell_bump_offer_description = ! empty( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_text']['wps_upsell_bump_offer_description'] ) ? esc_html( $wps_upsell_bumps_list[ $wps_upsell_bump_id ]['design_text']['wps_upsell_bump_offer_description'] ) : '';
								?>
								<textarea class="wps_textarea_class" name="wps_upsell_bump_offer_description" text_id="off_desc" rows="5" cols="50"><?php echo esc_html( $wps_upsell_bump_offer_description ); ?></textarea>
							</td>
						</tr>
						<!-- Offer Description end. -->
					</table>
				</div>
				<!-- Text end -->

				<!-- Preview start -->
				<div class="wps_ubo_bump_offer_preview">

					<?php

					// Send current Bump Offer id.
					$bump = wps_ubo_lite_fetch_bump_offer_details( $wps_upsell_bump_id, '' );
					$wps_ubo_offer_array_collection = get_option( 'wps_ubo_bump_list' );
					$encountered_bump_array = $wps_ubo_offer_array_collection[ $wps_upsell_bump_id ];
					$wps_bump_upsell_selected_template = ! empty( $encountered_bump_array['wps_ubo_selected_template'] ) ? sanitize_text_field( $encountered_bump_array['wps_ubo_selected_template'] ) : '';

					if ( '10' == $wps_bump_upsell_selected_template ) {

						$bumphtml = wps_ubo_lite_bump_offer_html_10( $bump );
					} elseif ( '11' == $wps_bump_upsell_selected_template ) {

						$bumphtml = wps_ubo_lite_bump_offer_html_11( $bump );
					} else {
						$bumphtml = wps_ubo_lite_bump_offer_html( $bump );
					}

					?>
					<h3 class="wps_ubo_offer_preview_heading"><?php esc_html_e( 'Offer Preview', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>

					<!-- Generate a live preview. -->
					<?php
					$allowed_html = wps_ubo_lite_allowed_html();
					echo wp_kses( $bumphtml, $allowed_html );
					?>
				</div>
				<!-- Preview end -->

			</div>
			<!-- Appearance Ends. -->

			<?php
		endif;

		?>

		<!-- Save Changes for whole Bump. -->
		<p class="submit">
			<input type="submit" value="<?php esc_html_e( 'Save Changes', 'upsell-order-bump-offer-for-woocommerce' ); ?>" class="button-primary woocommerce-save-button" name="wps_upsell_bump_creation_setting_save" id="wps_upsell_bump_creation_setting_save">
		</p>
	</div>
</form>

<!-- Skin Change Popup -->
<div class="wps_ubo_skin_popup_wrapper">
	<div class="wps_ubo_skin_popup_inner">
		<!-- Popup icon -->
		<div class="wps_ubo_skin_popup_head">
			<img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/icons/warning.png' ); ?>">
		</div>
		<!-- Popup body. -->
		<div class="wps_ubo_skin_popup_content">
			<div class="wps_ubo_skin_popup_ques">
				<h5><?php esc_html_e( 'Do you really want to change template layout ?', 'upsell-order-bump-offer-for-woocommerce' ); ?></h5>
				<p><?php esc_html_e( 'Changing layout will reset Design settings back to default.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
			</div>
			<div class="wps_ubo_skin_popup_option">
				<!-- Yes button. -->
				<a href="javascript:void(0);" class="wps_ubo_template_layout_yes"><?php esc_html_e( 'Yes', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
				<!-- No button. -->
				<a href="javascript:void(0);" class="wps_ubo_template_layout_no"><?php esc_html_e( 'No', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
			</div>
		</div>
	</div>
</div>

<!-- After v1.0.2 -->
<!-- Adding go pro popup here. -->

<!-- Add Go pro popup. -->
<?php wps_ubo_go_pro( 'pro' ); ?>
<input type="hidden" id="wps_funnel_type" value="wps_bump_one" />
<?php
// In your template or page.
$wps_funnel_type = 'wps_bump_one';
wc_render_discount_conditions_popup( $wps_funnel_type, $wps_upsell_bump_id )
?>
<script>
	jQuery(document).ready(function($) {
		$('#show-discount-conditions').on('click', function(e) {
			e.preventDefault();
			$('#wc-discount-popup').addClass('ubo_show');
		});
		// Ensure conditional display notice is visible even if hidden by default styles.
		var $conditionalNotice = $('.wps-ubo-conditional-notice');
		if ($conditionalNotice.length) {
			$conditionalNotice.show().removeClass('hidden keep-hidden');
		}
	});
</script>
