<?php
/**
 * Provide a public-facing view for the plugin.
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/public/partials
 */

// Check enability of the plugin at settings page.
$mwb_ubo_global_options = get_option( 'mwb_ubo_global_options', mwb_ubo_lite_default_global_options() );

// By default plugin will be enabled.
$mwb_bump_enable_plugin = ! empty( $mwb_ubo_global_options['mwb_bump_enable_plugin'] ) ? $mwb_ubo_global_options['mwb_bump_enable_plugin'] : 'on';

// Get all saved bumps.
$mwb_ubo_bump_callback = Upsell_Order_Bump_Offer_For_Woocommerce::$mwb_upsell_bump_list_callback_function;
$mwb_ubo_offer_array_collection = Upsell_Order_Bump_Offer_For_Woocommerce::$mwb_ubo_bump_callback();

if ( 'on' != $mwb_bump_enable_plugin || empty( $mwb_ubo_offer_array_collection ) ) {

	return;

}

if ( method_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Public', 'fetch_order_bump_from_collection' ) ) {

	$encountered_bump_result = Upsell_Order_Bump_Offer_For_Woocommerce_Public::fetch_order_bump_from_collection( $mwb_ubo_offer_array_collection );

	$encountered_bump_array = ! empty( $encountered_bump_result['encountered_bump_array'] ) ? $encountered_bump_result['encountered_bump_array'] : '';

	$mwb_upsell_bump_target_key = ! empty( $encountered_bump_result['mwb_upsell_bump_target_key'] ) ? $encountered_bump_result['mwb_upsell_bump_target_key'] : '';
}

// When we didn't get a perfect index for bump offer to be shown.
if ( empty( $encountered_bump_array ) && null == WC()->session->get( 'encountered_bump_array' ) ) {

	return;
}

// When we didn't get a saved funnel at same index for bump offer to be shown.
if ( empty( $mwb_ubo_offer_array_collection[ $encountered_bump_array ] ) && empty( $mwb_ubo_offer_array_collection[ WC()->session->get( 'encountered_bump_array' ) ] ) ) {

	return;
}

$selected_order_bump = ! empty( $mwb_ubo_offer_array_collection[ $encountered_bump_array ] ) ? $mwb_ubo_offer_array_collection[ $encountered_bump_array ] : $mwb_ubo_offer_array_collection[ WC()->session->get( 'encountered_bump_array' ) ];

// After v1.2.0.
if ( ! empty( $selected_order_bump ) ) {

	$offer_id = ! empty( $selected_order_bump['mwb_upsell_bump_products_in_offer'] ) ? sanitize_text_field( $selected_order_bump['mwb_upsell_bump_products_in_offer'] ) : '';
	$offer_product = wc_get_product( $offer_id );

	// Check once again product avaibility if present of not.
	if ( empty( $offer_product ) || 'publish' != $offer_product->get_status() || ! $offer_product->is_in_stock() ) {

		// Search for the next order bump again.
		if ( method_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Public', 'fetch_order_bump_from_collection' ) ) {

			// Destroy session.
			mwb_ubo_session_destroy();

			$encountered_bump_result = Upsell_Order_Bump_Offer_For_Woocommerce_Public::fetch_order_bump_from_collection( $mwb_ubo_offer_array_collection );

			$encountered_bump_array = ! empty( $encountered_bump_result['encountered_bump_array'] ) ? $encountered_bump_result['encountered_bump_array'] : '';

			$mwb_upsell_bump_target_key = ! empty( $encountered_bump_result['mwb_upsell_bump_target_key'] ) ? $encountered_bump_result['mwb_upsell_bump_target_key'] : '';
		}
	}
}

$mwb_upsell_bump_target_key = ! empty( $mwb_upsell_bump_target_key ) ? $mwb_upsell_bump_target_key : '';

// Save bump index.
$encountered_bump_array = null != WC()->session->get( 'encountered_bump_array' ) ? WC()->session->get( 'encountered_bump_array' ) : $encountered_bump_array;
WC()->session->set( 'encountered_bump_array' , $encountered_bump_array );

// Save Target product cart key.
$mwb_upsell_bump_target_key = null != WC()->session->get( 'mwb_upsell_bump_target_key' ) ? WC()->session->get( 'mwb_upsell_bump_target_key' ) : $mwb_upsell_bump_target_key;
WC()->session->set( 'mwb_upsell_bump_target_key' , $mwb_upsell_bump_target_key );


$bump = mwb_ubo_lite_fetch_bump_offer_details( WC()->session->get( 'encountered_bump_array' ), WC()->session->get( 'mwb_upsell_bump_target_key' ) );

$bumphtml = mwb_ubo_lite_bump_offer_html( $bump );

$allowed_html = mwb_ubo_lite_allowed_html();

echo wp_kses( $bumphtml, $allowed_html );

/*
 * FOR VARIABLE PRODUCTS ONLY,
 * ADDING POPUP HTML,
 * FOR VARIATION SELECTION.
 */
if ( ! empty( $bump['id'] ) ) {

	$product = wc_get_product( $bump['id'] );

	// The product must be variable.
	if ( ! empty( $product ) && $product->has_child() ) {

		// Show variations popup Html.
		mwb_ubo_lite_show_variation_popup( $product );
	}
} else {

	return;
}
