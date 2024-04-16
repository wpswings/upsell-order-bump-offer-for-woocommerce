jQuery( document ).ready(


			jQuery(document).on(
				'click','.wps-add-to-cart',
				function(e){
                    alert('click');
                    // Retrieve the hidden input element by its ID.
                    var wps_hidden_input = document.getElementById("wps_all_prod_id_fbt");
                    var wps_json_value = wps_hidden_input.value;
                    var wps_all_product_id = JSON.parse(wps_json_value);


                    var wps_fbt_discount_price = document.getElementById(" wps_fbt_discount_price");

					// // For the other simple product type.
					// var productId = this.getAttribute( 'data-product-id' );
					// var product_price_id = this.getAttribute( 'data-price' );
					// var wps_target_product_id = jQuery( '.single_add_to_cart_button' ).val();

					// // For variation product.
					// var wps_variation_product_id = jQuery( 'input[name="product_id"]' ).val();

					// console.log( wps_variation_product_id + ' ' + wps_target_product_id + ' ' + productId + ' ' + product_price_id );

					jQuery.ajax(
						{
							type: 'post',
							dataType: 'json',
							url: wps_ubo_lite_public_fbt.ajaxurl,
							data: {
								// nonce: wps_ubo_lite_public_fbt.auth_nonce,
								action: 'add_to_cart_fbt_product',
								wps_product_id : wps_all_product_id,
								wps_discount_price : wps_fbt_discount_price,
							},
                            success: function (msg) {
                                
                                alert('ajax run');

								// var loader = document.querySelector( '.wps_loader_' + msg.product_id );
								// // Show the loader.
								// loader.style.display = 'block';

								// // Simulate a delay (remove this in your actual code).
								// setTimeout(
								// 	function() {
								// 		// Hide the loader when the action is complete.
								// 		loader.style.display = 'none';
								// 	},
								// 	1100
								// );

								// // Get the <span> element by its ID.
								// var wps_my_span = document.getElementById( "wps_cart_content" );

								// // Update the text content.
								// wps_my_span.textContent = msg.cart_count;
							}

						}
					);
}
));
