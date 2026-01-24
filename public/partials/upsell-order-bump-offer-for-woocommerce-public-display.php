<?php
/**
 * Provide a public-facing view for the plugin.
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      1.0.0
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/public/partials
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wps_ubo_global_options = get_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );

// Check enability of the plugin at settings page. By default plugin will be enabled.
$wps_bump_enable_plugin = ! empty( $wps_ubo_global_options['wps_bump_enable_plugin'] ) ? $wps_ubo_global_options['wps_bump_enable_plugin'] : 'on';

// Enable the bump offer with or without pop-up.
$wps_bump_target_popup_bump = ! empty( $wps_ubo_global_options['wps_bump_popup_bump_offer'] ) ? $wps_ubo_global_options['wps_bump_popup_bump_offer'] : 'on';
// Get all saved bumps.
$wps_ubo_bump_callback          = Upsell_Order_Bump_Offer_For_Woocommerce::$wps_upsell_bump_list_callback_function;
$wps_ubo_offer_array_collection = Upsell_Order_Bump_Offer_For_Woocommerce::$wps_ubo_bump_callback();

if ( 'on' !== $wps_bump_enable_plugin || empty( $wps_ubo_offer_array_collection ) ) {
	return;
}

// Token to fetch order bumps again.
$fetch_order_bumps = true;

$encountered_bump_ids_array        = array();
$encountered_bump_tarket_key_array = array();

// WIW-CC : First check from session and perform validations.
$session = WC()->session;

if ( $session ) {
	if ( null !== WC()->session->get( 'encountered_bump_array' ) && is_array( WC()->session->get( 'encountered_bump_array' ) ) ) {

		$fetch_order_bumps = false;

		$encountered_bump_ids_array        = WC()->session->get( 'encountered_bump_array' );
		$encountered_bump_tarket_key_array = WC()->session->get( 'encountered_bump_tarket_key_array' );

		// For Each Order Bump Ids array from session.
		foreach ( $encountered_bump_ids_array as $key => $value ) {

			$encountered_order_bump_id = $value;

			$session_validations = wps_ubo_order_bump_session_validations( $wps_ubo_offer_array_collection, $wps_ubo_global_options, $encountered_order_bump_id );

			// When session validations fail.
			if ( false === $session_validations ) {

				$fetch_order_bumps = true;

				// In case offer is already added then remove the offer product.
				if ( null !== WC()->session->get( "bump_offer_status_index_$key" ) ) {

					WC()->cart->remove_cart_item( WC()->session->get( "bump_offer_status_index_$key" ) );

					WC()->session->__unset( "bump_offer_status_index_$key" );
				}

				// Destroy encountered bump array session.
				WC()->session->__unset( 'encountered_bump_array' );

				break;
			}
		}
	}
}

if ( $fetch_order_bumps && method_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Public', 'fetch_order_bump_from_collection' ) ) {

	$encountered_bump_result = Upsell_Order_Bump_Offer_For_Woocommerce_Public::fetch_order_bump_from_collection( $wps_ubo_offer_array_collection, $wps_ubo_global_options );

	// WIW-CC : Will return array of Order Bumps Ids.
	$encountered_bump_ids_array = ! empty( $encountered_bump_result['encountered_bump_array'] ) ? $encountered_bump_result['encountered_bump_array'] : array();

	// WIW-CC : Will return array of Order Bumps Target Ids.
	$encountered_bump_tarket_key_array = ! empty( $encountered_bump_result['wps_upsell_bump_target_key'] ) ? $encountered_bump_result['wps_upsell_bump_target_key'] : array();
}

// When empty or not array return.
if ( empty( $encountered_bump_ids_array ) || ! is_array( $encountered_bump_ids_array ) ) {

	return;
}

// Set Session whenever Order Bump Ids are fetched from collection.
	WC()->session->set( 'encountered_bump_array', $encountered_bump_ids_array );
	WC()->session->set( 'encountered_bump_tarket_key_array', $encountered_bump_tarket_key_array );

	// Add Order Bump Offer View Count for the respective Order Bump.
foreach ( $encountered_bump_ids_array as $order_bump_id ) {

	$sales_by_bump = new Wps_Upsell_Order_Bump_Report_Sales_By_Bump( $order_bump_id );
	$sales_by_bump->add_offer_view_count();
}



/**===========================================
		Order bump html section start
===========================================*/
?><div class="wrapup_order_bump">
<?php
$order_bump_collections   = get_option( 'wps_ubo_bump_list', array() );
$bump_priority_collection = array();

foreach ( $encountered_bump_ids_array as $key => $order_bump_id ) {
	$bump_priority  = ! empty( $order_bump_collections[ $order_bump_id ]['wps_upsell_bump_priority'] ) ? $order_bump_collections[ $order_bump_id ]['wps_upsell_bump_priority'] : '';

	// Bump Priority.
	if ( ! empty( $bump_priority ) ) {
		$bump_priority_collection[ $bump_priority ] = $order_bump_id;
		unset( $encountered_bump_ids_array[ $key ] );
	}
}
ksort( $bump_priority_collection );

// Bump offer showing setting.
$wps_upsell_bump_target_ids_popup = ! empty( $wps_ubo_global_options['wps_upsell_bump_target_ids_popup'] ) ? $wps_ubo_global_options['wps_upsell_bump_target_ids_popup'] : array();

// Merge the array.
$encountered_bump_ids_array = array_merge( $bump_priority_collection, $encountered_bump_ids_array );

$encountered_bump_ids_array = array_unique( $encountered_bump_ids_array );
// Initialize arrays for separation.
$data_for_popup = array();
$data_without_popup = array();

foreach ( $encountered_bump_ids_array as $wps ) {
	if ( in_array( $wps, $wps_upsell_bump_target_ids_popup ) ) {
		$data_for_popup[] = $wps;
	} else {
		$data_without_popup[] = $wps;
	}
}

// Merge the unique elements.
$t = '';
?>
<div class="wps_order_bump_without_popup_wrap" >
<?php
if ( 'without_popup' == $wps_bump_target_popup_bump || ( isset( $wps_upsell_bump_target_ids_popup ) && 'with_popup' == $wps_bump_target_popup_bump ) ) {

	if ( ( isset( $wps_upsell_bump_target_ids_popup ) && 'with_popup' == $wps_bump_target_popup_bump ) ) {
		$t = $data_without_popup;
	} else {
		$t = $encountered_bump_ids_array;
	}
	$wps_current_user = wp_get_current_user();
	$current_user_email = $wps_current_user->user_email;
	$wc_dynamic_rules = get_option( 'wc_dynamic_discount_rules', array() );
	// Bump offer html section without popup function.
	foreach ( $t as $key => $order_bump_id ) {

		if ( true === is_valid_user_role( $order_bump_id ) ) {
			continue;
		}

		$wps_ubo_condition_show = ! empty( $order_bump_collections[ $order_bump_id ]['wps_ubo_condition_show'] ) ? $order_bump_collections[ $order_bump_id ]['wps_ubo_condition_show'] : 0;
		if ( 'yes' === $wps_ubo_condition_show ) {
			{
			$device_country_ok = true;
			if ( isset( $wc_dynamic_rules['wps_bump_one'][ $order_bump_id ] ) ) {
				foreach ( $wc_dynamic_rules['wps_bump_one'][ $order_bump_id ] as $rule ) {
					if ( in_array( $rule['field'], array( 'device_type', 'country' ), true ) ) {
						$target = 'device_type' === $rule['field'] ? wc_detect_device_type() : wc_get_user_country();
						if ( ! wc_compare_rule_value( $rule['field'], $rule['operator'], $target, $rule['value'] ) ) {
							$device_country_ok = false;
							break;
						}
					}
				}
			}
			if ( ! $device_country_ok || ! wc_dynamic_discount_conditions_pass( 'wps_bump_one', $order_bump_id ) ) {
				continue;
			}

			}}


		$min_cart_value_wps = ! empty( $order_bump_collections[ $order_bump_id ]['wps_upsell_bump_min_cart'] ) ? $order_bump_collections[ $order_bump_id ]['wps_upsell_bump_min_cart'] : 0;
		if ( ! empty( $min_cart_value_wps ) ) {
			$cart_total = WC()->cart->get_cart_contents_total();
			if ( (int) $cart_total < (int) $min_cart_value_wps ) {
				continue;
			}
		}
		$encountered_order_bump_id = $order_bump_id;

		if ( ! empty( $encountered_bump_tarket_key_array ) ) {
			$encountered_respective_target_key = ! empty( $encountered_bump_tarket_key_array[ $key ] ) ? $encountered_bump_tarket_key_array[ $key ] : '';
		}

		$wps_offer_product = ! empty( $order_bump_collections[ $order_bump_id ]['wps_upsell_bump_products_in_offer'] ) ? $order_bump_collections[ $order_bump_id ]['wps_upsell_bump_products_in_offer'] : '';
		$offer_product = wc_get_product( $wps_offer_product );

		if (
			isset( $order_bump_collections[ $order_bump_id ]['wps_is_abandoned_bump'], $order_bump_collections[ $order_bump_id ]['wps_abandoned_checkout_useremail'] ) &&
			'yes' === $order_bump_collections[ $order_bump_id ]['wps_is_abandoned_bump'] &&
		strtolower( $current_user_email ) === strtolower( $order_bump_collections[ $order_bump_id ]['wps_abandoned_checkout_useremail'] )
		) {


			wps_ubo_analyse_and_display_order_bump( $encountered_order_bump_id, $encountered_respective_target_key, $encountered_order_bump_id );

		} elseif ( isset( $order_bump_collections[ $order_bump_id ]['wps_is_abandoned_bump'] ) && 'yes' !=
			$order_bump_collections[ $order_bump_id ]['wps_is_abandoned_bump'] ) {
				wps_ubo_analyse_and_display_order_bump( $encountered_order_bump_id, $encountered_respective_target_key, $encountered_order_bump_id );

		} elseif ( ! isset( $order_bump_collections[ $order_bump_id ]['wps_is_abandoned_bump'] ) ) {
			wps_ubo_analyse_and_display_order_bump( $encountered_order_bump_id, $encountered_respective_target_key, $encountered_order_bump_id );
		}
	}
}
?>
</div>
<?php

// By default plugin will be enabled.
$wps_bump_enable_plugin = ! empty( $wps_ubo_global_options['wps_bump_enable_plugin'] ) ? $wps_ubo_global_options['wps_bump_enable_plugin'] : '';

// Bump offer html section with popup function.
if ( 'with_popup' == $wps_bump_target_popup_bump ) {

	// Only Varaible Bump offer to be show normal even in popup function enable.
	foreach ( $encountered_bump_ids_array as $key => $order_bump_id ) {

		if ( true === is_valid_user_role( $order_bump_id ) ) {
			continue;
		}


		$wps_ubo_condition_show = ! empty( $order_bump_collections[ $order_bump_id ]['wps_ubo_condition_show'] ) ? $order_bump_collections[ $order_bump_id ]['wps_ubo_condition_show'] : 0;
		if ( 'yes' === $wps_ubo_condition_show ) {
			{
			if ( ! wc_dynamic_discount_conditions_pass( 'wps_bump_one', $order_bump_id ) ) {
				continue;
			}

			}}


		$min_cart_value_wps = ! empty( $order_bump_collections[ $order_bump_id ]['wps_upsell_bump_min_cart'] ) ? $order_bump_collections[ $order_bump_id ]['wps_upsell_bump_min_cart'] : 0;
		if ( ! empty( $min_cart_value_wps ) ) {
			$cart_total = WC()->cart->get_cart_contents_total();
			if ( (int) $cart_total < (int) $min_cart_value_wps ) {
				continue;
			}
		}
		$encountered_order_bump_id = $order_bump_id;

		if ( ! empty( $encountered_bump_tarket_key_array ) ) {
			$encountered_respective_target_key = ! empty( $encountered_bump_tarket_key_array[ $key ] ) ? $encountered_bump_tarket_key_array[ $key ] : '';
		}

		$wps_offer_product = ! empty( $order_bump_collections[ $order_bump_id ]['wps_upsell_bump_products_in_offer'] ) ? $order_bump_collections[ $order_bump_id ]['wps_upsell_bump_products_in_offer'] : '';
		$offer_product = wc_get_product( $wps_offer_product );

		if ( $offer_product->is_type( 'variable' ) ) {
			wps_ubo_analyse_and_display_order_bump( $encountered_order_bump_id, $encountered_respective_target_key, $encountered_order_bump_id );
		}
	}

	// Below is bump offer in pop-up except variable.
	if ( $data_for_popup ) {
		// Filter out variable products.
		foreach ( $data_for_popup as $key => $order_bump_id ) {
			$wps_offer_id = ! empty( $order_bump_collections[ $order_bump_id ]['wps_upsell_bump_products_in_offer'] ) ? $order_bump_collections[ $order_bump_id ]['wps_upsell_bump_products_in_offer'] : '';
			$offer_product = wc_get_product( $wps_offer_id );

			$wps_ubo_condition_show = ! empty( $order_bump_collections[ $order_bump_id ]['wps_ubo_condition_show'] ) ? $order_bump_collections[ $order_bump_id ]['wps_ubo_condition_show'] : 0;
			if ( 'yes' === $wps_ubo_condition_show ) {
				$device_country_ok = true;
				if ( isset( $wc_dynamic_rules['wps_bump_one'][ $order_bump_id ] ) ) {
					foreach ( $wc_dynamic_rules['wps_bump_one'][ $order_bump_id ] as $rule ) {
						if ( in_array( $rule['field'], array( 'device_type', 'country' ), true ) ) {
							$target = 'device_type' === $rule['field'] ? wc_detect_device_type() : wc_get_user_country();
							if ( ! wc_compare_rule_value( $rule['field'], $rule['operator'], $target, $rule['value'] ) ) {
								$device_country_ok = false;
								break;
							}
						}
					}
				}
				if ( ! $device_country_ok || ! wc_dynamic_discount_conditions_pass( 'wps_bump_one', $order_bump_id ) ) {
					unset( $data_for_popup[ $key ] );
					continue;
				}
			}

			// Check if product exists and is a variable product.
			if ( $offer_product && $offer_product->is_type( 'variable' ) ) {
				// Remove this key from the array.
				unset( $data_for_popup[ $key ] );
			}
		}

		// Re-index the array after unsetting keys.
		$data_for_popup = array_values( $data_for_popup );

		// Only show the popup button and wrapper if there are non-variable products.
		if ( ! empty( $data_for_popup ) ) {
			?>
		<a class="open-button" id="wps_open_modal" popup-open="popup-1" href="javascript:void(0)">click</a>

		<div class="popup wps_uobo_product_popup" id="wps_slider" popup-name="popup-1">
				<div class="wps-popup-content">
				<div class="wps-popup-content-in">
					<?php
					// For Each Order Bump Ids array.
					foreach ( $data_for_popup as $key => $order_bump_id ) {

						if ( true === is_valid_user_role( $order_bump_id ) ) {
							continue;
						}

						$min_cart_value_wps = ! empty( $order_bump_collections[ $order_bump_id ]['wps_upsell_bump_min_cart'] ) ? $order_bump_collections[ $order_bump_id ]['wps_upsell_bump_min_cart'] : 0;
						if ( ! empty( $min_cart_value_wps ) ) {
							$cart_total = WC()->cart->get_cart_contents_total();
							if ( (int) $cart_total < (int) $min_cart_value_wps ) {
								continue;
							}
						}

						// Respect conditional visibility (device type, country, etc.).
						$wps_ubo_condition_show = ! empty( $order_bump_collections[ $order_bump_id ]['wps_ubo_condition_show'] ) ? $order_bump_collections[ $order_bump_id ]['wps_ubo_condition_show'] : 0;
						if ( 'yes' === $wps_ubo_condition_show ) {
							$device_country_ok = true;
							if ( isset( $wc_dynamic_rules['wps_bump_one'][ $order_bump_id ] ) ) {
								foreach ( $wc_dynamic_rules['wps_bump_one'][ $order_bump_id ] as $rule ) {
									if ( in_array( $rule['field'], array( 'device_type', 'country' ), true ) ) {
										$target = 'device_type' === $rule['field'] ? wc_detect_device_type() : wc_get_user_country();
										if ( ! wc_compare_rule_value( $rule['field'], $rule['operator'], $target, $rule['value'] ) ) {
											$device_country_ok = false;
											break;
										}
									}
								}
							}
							if ( ! $device_country_ok || ! wc_dynamic_discount_conditions_pass( 'wps_bump_one', $order_bump_id ) ) {
								continue;
							}
						}

						$encountered_order_bump_id = $order_bump_id;

						if ( ! empty( $encountered_bump_tarket_key_array ) ) {
							$encountered_respective_target_key = ! empty( $encountered_bump_tarket_key_array[ $key ] ) ? $encountered_bump_tarket_key_array[ $key ] : '';
						}

						// No need to check product type again since we already filtered variable products.
						?>
						<div class="wps_bump_offer_modal_wrapper">
							<?php
							wps_ubo_analyse_and_display_order_bump( $encountered_order_bump_id, $encountered_respective_target_key, $encountered_order_bump_id );
							?>
						</div>
						<?php
					}
					?>
					</div>
					<div class="wps_close_modal">
						<a class="close-button" popup-close="popup-1" href="javascript:void(0)"></a>
					</div>
				</div>
		</div>
			<?php
		}
	}
}
?>
  </div>
<?php
/**===========================================
		Order bump html section ends
===========================================*/
