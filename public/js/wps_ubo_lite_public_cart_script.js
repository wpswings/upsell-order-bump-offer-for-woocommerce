jQuery( document ).ready(
	function ($) {

		$( document ).on(
			'click',
			'.wps_product_discount',
			function () {

				jQuery( document ).trigger( 'wc_fragment_refresh' );
				// Get the value attribute from the div
				var parent_product_id = jQuery( this ).closest( '.wps_main_class_order' ).find( '.wps_offered_product_id' ).val();

				// Get the quanity of the cart offer.
				var wps_cart_offer_quantity = document.querySelector( '#wps_cart_offer_quantity' );
				var wps_cart_offer_quantity_value = wps_cart_offer_quantity.value;

				// Get the product id  of the cart offer.
				var wps_cart_offer_product_id = document.querySelector( '#wps_cart_offer_product_id_' + parent_product_id );
				var wps_cart_offer_product_id_value = wps_cart_offer_product_id.value;

				// Get the product price  for the cart offer.
				var wps_cart_offer_product_price = document.querySelector( '#wps_cart_offer_product_price_' + parent_product_id );
				var wps_cart_offer_product_price = wps_cart_offer_product_price.value;

				// Get the select element by its ID
				var child_variation_id_element = document.querySelector( "#wps-order-bump-child-id_" + parent_product_id );

				if (null != child_variation_id_element) {
					// Get the currently selected value
					var child_variation_id = child_variation_id_element.value;
				} else {
					var child_variation_id = '';
				}

				jQuery.ajax(
					{
						type: 'post',
						dataType: 'json',
						url: wps_ubo_lite_public_cart.ajaxurl,
						data: {
							nonce: wps_ubo_lite_public_cart.auth_nonce,
							action: 'add_cart_discount_offer_in_cart',
							parent_product_id: parent_product_id,
							child_product_id: child_variation_id,
							wps_cart_offer_quantity_value: wps_cart_offer_quantity_value,
							wps_cart_offer_product_id_value: wps_cart_offer_product_id_value,
							wps_cart_offer_product_price: wps_cart_offer_product_price
						},
						success: function (msg) {
							console.log( msg );
							$( document.body ).trigger( 'added_to_cart', {} );
							$( document.body ).trigger( 'update_checkout' );
							if (msg.message == 'remove') {
								// Below code is to hide the offer section on adding to cart.
							}
						}
					}
				);

			}
		);
	}
);

