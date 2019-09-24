<?php
/**
 * Provide a public-facing view for the plugin
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

$mwb_upsell_bump_global_skip_settings = ! empty( $mwb_ubo_global_options['mwb_bump_skip_offer'] ) ? $mwb_ubo_global_options['mwb_bump_skip_offer'] : 'yes';

/**
 * Get all bump lists,
 * Check for live ones and scheduled for today only,
 * Rest leave No need to check,
 * For live one check if target id is present and after this category check,
 * Save the array index that is encountered and target product key.
 */

if ( ! session_id() ) {

	session_start();
}

if ( empty( $_SESSION['encountered_bump_array'] ) ) {

	foreach ( $mwb_ubo_offer_array_collection as $single_bump_id => $single_bump_array ) {

		// Check Bump status.
		$single_bump_status = ! empty( $single_bump_array['mwb_upsell_bump_status'] ) ? $single_bump_array['mwb_upsell_bump_status'] : '';

		// Not live so continue.
		if ( 'yes' != $single_bump_status ) {

			continue;
		}

		// Check for Bump Schedule.
		$single_bump_schedule = ! empty( $single_bump_array['mwb_upsell_bump_schedule'] ) ? $single_bump_array['mwb_upsell_bump_schedule'] : '';

		if ( ( date( 'N' ) != $single_bump_array['mwb_upsell_bump_schedule'] ) && ( '0' != $single_bump_array['mwb_upsell_bump_schedule'] ) ) {

			continue;
		}

		// Check if target products or target categories are empty.
		$single_bump_target_ids = ! empty( $single_bump_array['mwb_upsell_bump_target_ids'] ) ? $single_bump_array['mwb_upsell_bump_target_ids'] : array();

		$single_bump_categories = ! empty( $single_bump_array['mwb_upsell_bump_target_categories'] ) ? $single_bump_array['mwb_upsell_bump_target_categories'] : array();

		// When both target products or target categories are empty, continue.
		if ( empty( $single_bump_target_ids ) && empty( $single_bump_categories ) ) {

			continue;

		}

		// Here we will have atleast a category or target id.
		// Lets check for offer be present.
		if ( ! empty( $single_bump_array['mwb_upsell_bump_products_in_offer'] ) ) {

			// Check if these product are present in cart one by one.
			foreach ( $single_bump_array['mwb_upsell_bump_target_ids'] as $key => $single_target_id ) {

				// Check if present in cart.
				$mwb_upsell_bump_target_key = mwb_ubo_lite_check_if_in_cart( $single_target_id );

				// If we product is present we get the cart key.
				if ( ! empty( $mwb_upsell_bump_target_key ) ) {

					// Check offer product must be in stock.
					$offer_product = wc_get_product( $single_bump_array['mwb_upsell_bump_products_in_offer'] );

					if ( ! $offer_product->is_in_stock() ) {

						continue;
					}

					// Check if offer product is already in cart.
					if ( mwb_ubo_lite_already_in_cart( $single_bump_array['mwb_upsell_bump_products_in_offer'] ) && 'yes' == $mwb_upsell_bump_global_skip_settings ) {

						continue;
					}

					// If everything is good just break !!
					$encountered_bump_array = $single_bump_id;
					break 2;

				}
			} // 2nd foreach end for product id.

			// If target key is still empty means no target category is found yet.
			if ( empty( $encountered_bump_array ) && ! empty( $single_bump_array['mwb_upsell_bump_target_categories'] ) ) {

				foreach ( $single_bump_array['mwb_upsell_bump_target_categories'] as $key => $single_category_id ) {

					// No target Id is found go for category,
					// Check if the category is in cart.
					$mwb_upsell_bump_target_key = mwb_ubo_lite_check_category_in_cart( $single_category_id );

					// If we product is present we get the cart key.
					if ( ! empty( $mwb_upsell_bump_target_key ) ) {

						// Check offer product must be in stock.
						$offer_product = wc_get_product( $single_bump_array['mwb_upsell_bump_products_in_offer'] );

						if ( ! $offer_product->is_in_stock() ) {
							continue;
						}

						// Check if offer product is already in cart.
						if ( mwb_ubo_lite_already_in_cart( $single_bump_array['mwb_upsell_bump_products_in_offer'] ) && 'yes' == $mwb_upsell_bump_global_skip_settings ) {

							continue;

						}

						// If everything is good just break !!
						$encountered_bump_array = $single_bump_id;
						break 2;

					}
				} // Second foreach for category search end.
			}
		} else {

			// If offer product is not saved, continue.
			continue;
		}
	} // First foreach end.
}

// When we didn't get a perfect data for bump offer to be shown.
if ( empty( $encountered_bump_array ) && empty( $_SESSION['encountered_bump_array'] ) ) {

	return;
}

$mwb_upsell_bump_target_key = ! empty( $mwb_upsell_bump_target_key ) ? $mwb_upsell_bump_target_key : '';

$_SESSION['encountered_bump_array'] = ! empty( $_SESSION['encountered_bump_array'] ) ? $_SESSION['encountered_bump_array'] : $encountered_bump_array;

$_SESSION['mwb_upsell_bump_target_key'] = ! empty( $_SESSION['mwb_upsell_bump_target_key'] ) ? $_SESSION['mwb_upsell_bump_target_key'] : $mwb_upsell_bump_target_key;

$bump = mwb_ubo_lite_fetch_bump_offer_details( $_SESSION['encountered_bump_array'], $_SESSION['mwb_upsell_bump_target_key'] );

$bumphtml = mwb_ubo_lite_bump_offer_html( $bump );

echo $bumphtml; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped Function returns the html so can't escape.

/*
 * FOR VARIABLE PRODUCTS ONLY,
 * ADDING POPUP HTML,
 * FOR VARIATION SELECTION.
 */
if ( ! empty( $bump['id'] ) ) {

	$product = wc_get_product( $bump['id'] );

	// The product must be variable.
	if ( $product->has_child() ) {

		// Show variations popup Html.
		mwb_ubo_lite_show_variation_popup( $product );
	}
} else {

	return;
}
