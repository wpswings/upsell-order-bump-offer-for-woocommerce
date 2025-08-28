jQuery( document ).ready(
	jQuery( document ).on(
		'click',
		'.wps-add-to-cart',
		function(e){
			// Retrieve the hidden input element by its ID.
			var wps_hidden_input = document.getElementById( "wps_all_prod_id_fbt" );
			var wps_main_prod_id = document.getElementById( "wps_main_prod_id" ).value;
			var wps_json_value = wps_hidden_input.value;
			var wps_all_product_id = JSON.parse( wps_json_value );
			var wps_fbt_discount_price = document.getElementById( " wps_fbt_discount_price" );

			jQuery.ajax(
				{
					type: 'post',
					dataType: 'json',
					url: wps_ubo_lite_public_fbt.ajaxurl,
					data: {
						// nonce: wps_ubo_lite_public_fbt.auth_nonce,
						action: 'add_to_cart_fbt_product',
						wps_product_id : wps_all_product_id,
						wps_discount_price: wps_fbt_discount_price,
						wps_main_prod_id : wps_main_prod_id,
					},
					success: function (msg) {
						alert( 'Product Added!!' );
					}

				}
			);
		}
	)
);
