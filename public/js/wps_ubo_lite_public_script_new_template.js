jQuery( document ).ready(
	function ($) {
		function addProduct() {
			$( '.wps_hide_checkbox' ).hide();
			$( document ).on(
				'click',
				'.wps-ubo__temp-add-btn',
				function() {

					console.log( $( this ).closest( '.wps-ubo__temp' ).find( '.wps-ubo__temp-prod-check' ) );

					$( this ).closest( '.wps-ubo__temp' ).find( '.wps-ubo__temp-prod-check' ).attr( 'checked', true );
					$( this ).closest( '.wps-ubo__temp' ).find( '.wps-ubo__temp-btn-notice' ).children( '.wps-notice' ).html( 'Product Added!' );

					$( this ).closest( '.wps-ubo__temp' ).find( '.wps-ubo__temp-btn-notice' ).addClass( 'wps-success' );
					setTimeout(
						function() {
							$( this ).closest( '.wps-ubo__temp' ).find( '.wps-ubo__temp-btn-notice' ).removeClass( 'wps-success' );
						},
						2000
					)
				}
			).on(
				'click',
				'.wps-ubo__temp-rmv-btn',
				function() {

					$( this ).closest( '.wps-ubo__temp' ).find( '.wps-ubo__temp-prod-check' ).attr( 'checked', false );

					$( this ).closest( '.wps-ubo__temp' ).find( '.wps-ubo__temp-rmv-btn' ).removeClass( 'wps-active' );
					$( this ).closest( '.wps-ubo__temp' ).find( '.wps-ubo__temp-add-btn' ).addClass( 'wps-active' );

					$( this ).closest( '.wps-ubo__temp' ).find( '.wps-ubo__temp-btn-notice' ).children( '.wps-notice' ).html( 'Product removed!' );
					$( this ).closest( '.wps-ubo__temp' ).find( '.wps-ubo__temp-btn-notice' ).addClass( 'failure' );
					setTimeout(
						function() {
							$( this ).closest( '.wps-ubo__temp' ).find( '.wps-ubo__temp-btn-notice' ).removeClass( 'failure' );
						},
						2000
					)
				}
			)
		}

		$( document ).ready(
			function() {
				addProduct();
			}
		);

		var selected_order_bump_popup = '';
		var default_image = '';
		var default_price = '';
		function wps_ubo_lite_intant_zoom_img(selected_order_bump_popup) {

			if (wps_ubo_lite_public.mobile_view != 1) {

				selected_order_bump_popup.find( '.woocommerce-product-gallery__image' ).zoom();
			}
		}

		jQuery( document ).on(
			'click',
			'.wps-ubo__temp-add-btn',
			function (e) {
				order_bump_trigger_obj = jQuery( this );
				var order_bump_index = order_bump_trigger_obj.closest( '.wps-ubo__temp' ).find( '.order_bump_index' ).val();
				var order_bump_id = order_bump_trigger_obj.closest( '.wps-ubo__temp' ).find( '.order_bump_id' ).val();

				$( "#wps_checkbox_offer" + order_bump_id ).prop( "checked", true );

				if ($( "#wps_checkbox_offer" + order_bump_id ).prop( 'checked' ) == true) {

					// Check if meta form present.
					let popup_obj = jQuery( '#wps-meta-form-index-' + order_bump_id );

					let index = 0;
					jQuery( '.wps-meta-form-submit' ).on(
						'click',
						function (e) {
							open_custom_form( popup_obj, order_bump_trigger_obj );
							e.preventDefault();
							let data_arr = [];
							jQuery( '#wps-meta-form-index-' + order_bump_id ).find( '.wps_ubo_custom_meta_field' ).each(
								function () {

									let field_obj = {};

									if ('' == jQuery( this ).val()) {
										alert( jQuery( this ).attr( 'name' ) + ' field cannot be empty' );
										return;
									} else if ('checkbox' == jQuery( this ).attr( 'type' )) {

										// Push the values in an array.
										field_obj.name = jQuery( this ).attr( 'name' );
										field_obj.value = jQuery( this ).prop( 'checked' );
										data_arr[index] = field_obj;
										index++;                        } else {
										// Push the values in an array.
										field_obj.name = jQuery( this ).attr( 'name' );
										field_obj.value = jQuery( this ).val();

										data_arr[index] = field_obj;
										index++;
										}
								}
							);

							data_arr = data_arr.filter( onlyUnique );

							// All fields are saved!
							if (data_arr.length == popup_obj.find( '.wps_ubo_custom_meta_field' ).length) {
								// Close popup and send add to cart request.
								close_custom_form();
								triggerAddOffer( order_bump_trigger_obj, data_arr );
							}
						}
					);
				}
				triggerAddOffer( order_bump_trigger_obj );
			}
		);

		function open_custom_form(form_obj, order_bump_obj) {

			jQuery( 'body' ).css( 'overflow', 'hidden' );
			if (jQuery( '.wps-g-modal' ).hasClass( 'wps-modal--close' )) {
				jQuery( '.wps-g-modal' ).removeClass( 'wps-modal--close' );
			}
			$( 'div.wps-g-modal.wps-modal--open' ).addClass( 'wps-modal--open' );

			jQuery( '.wps-g-modal__close' ).on(
				'click',
				function () {
					order_bump_obj.prop( 'checked', false );
					close_custom_form();
				}
			);
		}

		function close_custom_form() {
			jQuery( 'body' ).css( 'overflow', 'auto' );
			jQuery( '.wps-g-modal' ).addClass( 'wps-modal--close' );
			setTimeout(
				function () {
					jQuery( '.wps-g-modal' ).removeClass( 'wps-modal--open' );
				},
				320
			);
		}

		function triggerAddOffer(order_bump_trigger_obj){
					// Add product to cart.
					// Getting the Value From Html.
					var bump_id = order_bump_trigger_obj.closest( '.wps-ubo__temp' ).find( '.offer_shown_id' ).val()
					var bump_discount = order_bump_trigger_obj.closest( '.wps-ubo__temp' ).find( '.offer_shown_discount' ).val()
					var bump_target_cart_key = order_bump_trigger_obj.closest( '.wps-ubo__temp' ).find( '.target_id_cart_key' ).val()
					var order_bump_id = order_bump_trigger_obj.closest( '.wps-ubo__temp' ).find( '.order_bump_id' ).val()
					var smart_offer_upgrade = order_bump_trigger_obj.closest( '.wps-ubo__temp' ).find( '.order_bump_smo' ).val()

					var order_bump_index = order_bump_trigger_obj.closest( '.wps-ubo__temp' ).find( '.order_bump_index' ).val()
					var wps_qty_variable = 1;

					// Show loader for Variable offers.
			if ('variable' == order_bump_trigger_obj.closest( '.wps-ubo__temp' ).find( '.offer_shown_id_type' ).val()) { // decide the product type.
				$( '.wps_bump_popup_loader' ).css( 'display', 'flex' );
			}

					jQuery.ajax(
						{
							type: 'post',
							dataType: 'json',
							url: wps_ubo_lite_public_new.ajaxurl,
							data: {
								nonce: wps_ubo_lite_public_new.auth_nonce,
								action: 'wps_add_the_product',
								id: bump_id, // offer product id.
								discount: bump_discount,
								bump_target_cart_key: bump_target_cart_key,
								order_bump_id: order_bump_id,
								smart_offer_upgrade: smart_offer_upgrade,
								wps_qty_variable: wps_qty_variable, // Quantity attr

								bump_index: order_bump_index,
								// Form data.
								form_data: '',
							},

							success: function (msg) {
								let m1 = $( '#wps_checkbox_tick_2' ).is( ":checked" );

								localStorage.setItem( "checkbox1", m1 );
								wps_is_checkbox_checked();

								if (msg['key'] == 'true') {

									variation_popup_index = order_bump_index.replace( 'index_', '' );
									$( '.wps_ubo_price_html_for_variation' ).html( msg['message'] );
									$( '.wps_bump_popup_loader' ).css( 'display', 'none' );
									$( '.wps_bump_popup_' + variation_popup_index ).css( 'display', 'flex' );
									$( 'body' ).addClass( 'wps_upsell_variation_pop_up_body' );

									// Add zoom to defaut image.
									selected_order_bump_popup = jQuery( '.wps_bump_popup_' + variation_popup_index );
									wps_ubo_lite_intant_zoom_img( selected_order_bump_popup );

								} else {
									$( 'body' ).trigger( 'update_checkout' );
									$( document.body ).trigger( 'added_to_cart', {} );
									$( document.body ).trigger( 'update_checkout' );
									// When Reload is required.
									if ('subs_reload' == msg) {

										// Scroll Top and Reload.
										$( "html, body" ).scrollTop( 300 );
										location.reload();
									}

								}
							}
						}
					);

		}

		function wps_is_checkbox_checked() {
			console.log( localStorage.getItem( "checkbox1" ) );
		}

		jQuery( document ).on(
			'click',
			'.wps-ubo__temp-rmv-btn',
			function (e) {
				order_bump_trigger_obj = jQuery( this );
				var order_bump_index = order_bump_trigger_obj.closest( '.wps-ubo__temp' ).find( '.order_bump_index' ).val();
				var order_bump_id = order_bump_trigger_obj.closest( '.wps-ubo__temp' ).find( '.order_bump_id' ).val();

				triggerRemoveOffer( order_bump_index, order_bump_id );
			}
		);

		const triggerRemoveOffer = (order_bump_index, order_bump_id) => {

			// Getting Current theme Name.
			var wps_current_theme = wps_ubo_lite_public.current_theme;

			// Remove the same product from cart.
			jQuery.ajax(
				{

					type: 'post',
					dataType: 'json',
					url: wps_ubo_lite_public_new.ajaxurl,
					data: {
						nonce: wps_ubo_lite_public.auth_nonce,
						action: 'wps_remove_offer_product',
						bump_index: order_bump_index,
						order_bump_id: order_bump_id,
					},

					success: function (msg) {
						localStorage.removeItem( "checkbox1" );
						wps_is_checkbox_checked();
						$( 'body' ).trigger( 'update_checkout' );
						$( document.body ).trigger( 'added_to_cart', {} );
						$( document.body ).trigger( 'update_checkout' );
						// Mini-Cart Upadte on Checkout depending upon themes.

						$( '.wps_parent_wrapper_order_bump_' + order_bump_index ).css( 'pointer-events', 'all' );
						$( '.wps_parent_wrapper_order_bump_' + order_bump_index ).css( 'opacity', '1' );
						var body = document.body;
						body.classList.remove( "wps_body_class_popup" );
					}
				}
			);
		}

		/*=======================================================================
							Select the variations in popup.
		========================================================================*/
		/*
		 * POP-UP Select change JS,
		 * To add the price html and image of selected variation in popup.
		 */

		$( document ).on(
			'change',
			'.wps_upsell_offer_variation_select',
			function (e) {

				var selected_order_bump_index = $( this ).attr( 'order_bump_index' );

				// Order Bump Object.
				var parent_wrapper_class = '.wps_parent_wrapper_order_bump_' + selected_order_bump_index;
				selected_order_bump = jQuery( parent_wrapper_class );

				// Order Bump Popup Object.
				var popup_wrapper_class = '.wps_bump_popup_' + selected_order_bump_index;
				selected_order_bump_popup = jQuery( popup_wrapper_class );

				// Fetch selected attributes.
				var selected_variations = selected_order_bump_popup.find( '.wps_upsell_offer_variation_select' );
				var attributes_selected = [];

				// Default image handle here.
				if (default_image == '') {

					if (selected_order_bump_popup.find( 'woocommerce-product-gallery__image' )) {

						default_image = selected_order_bump_popup.find( '.woocommerce-product-gallery__image' );
					} else {

						default_image = selected_order_bump_popup.find( '.woocommerce-placeholder' );
					}
				}

				for (var i = selected_variations.length - 1; i >= 0; i--) {

					if (selected_variations[i].value == '') {

						// Default View on no selection.
						selected_order_bump_popup.find( '.wps_bump_popup_image' ).html( default_image );
						wps_ubo_lite_intant_zoom_img( selected_order_bump_popup );
						selected_order_bump_popup.find( '.wps_ubo_err_waring_for_variation' ).css( 'display', 'none' );
						selected_order_bump_popup.find( '.wps_ubo_price_html_for_variation' ).css( 'display', 'block' );
						selected_order_bump_popup.find( '.wps_ubo_bump_add_to_cart_button' ).css( 'display', 'none' );
						selected_order_bump_popup.find( '.wps_ubo_price_html_for_variation' ).html( default_price );

						return;

					} else {

						attributes_selected[selected_variations[i].id] = selected_variations[i].value;
					}
				}

				/*
				* Got an array of all selected attribute.
				* Will run an ajax for search of attributes.
				* Show price and image.
				* Will see for variation id and in stock.
				* Add to cart button will be shown.
				*/

				// Converts attributes array in object.
				attributes_selected = Object.assign( {}, attributes_selected );

				// Required Data.
				bump_id = selected_order_bump.find( '.offer_shown_id' ).val();
				bump_discount = selected_order_bump.find( '.offer_shown_discount' ).val();

				console.log( attributes_selected );
				console.log( bump_id );
				console.log( bump_discount );

				jQuery.ajax(
					{

						type: 'post',
						dataType: 'json',
						url: wps_ubo_lite_public_new.ajaxurl,
						data: {
							nonce: wps_ubo_lite_public.auth_nonce,
							action: 'wps_variation_select_added',
							attributes_selected_options: attributes_selected,
							id: bump_id,
							discount: bump_discount,
						},

						success: function (msg) {
							if (msg['key'] == 'stock') {

								selected_order_bump_popup.find( '.wps_ubo_err_waring_for_variation' ).css( 'display', 'flex' );
								selected_order_bump_popup.find( '.wps_ubo_price_html_for_variation' ).css( 'display', 'none' );
								selected_order_bump_popup.find( '.wps_ubo_bump_add_to_cart_button' ).css( 'display', 'none' );

								selected_order_bump_popup.find( '.wps_bump_popup_image' ).html( msg['image'] );
								wps_ubo_lite_intant_zoom_img( selected_order_bump_popup );
								selected_order_bump_popup.find( '.wps_ubo_err_waring_for_variation' ).html( msg['message'] );

							} else if (msg['key'] == 'not_available') {

								selected_order_bump_popup.find( '.wps_ubo_err_waring_for_variation' ).css( 'display', 'flex' );
								selected_order_bump_popup.find( '.wps_ubo_price_html_for_variation' ).css( 'display', 'none' );
								selected_order_bump_popup.find( '.wps_ubo_bump_add_to_cart_button' ).css( 'display', 'none' );

								selected_order_bump_popup.find( '.wps_bump_popup_image' ).html( msg['image'] );
								wps_ubo_lite_intant_zoom_img( selected_order_bump_popup );
								selected_order_bump_popup.find( '.wps_ubo_err_waring_for_variation' ).html( msg['message'] );

							} else if ( ! isNaN( msg['key'] )) {

								selected_order_bump_popup.find( '.wps_ubo_err_waring_for_variation' ).css( 'display', 'none' );
								selected_order_bump_popup.find( '.wps_ubo_price_html_for_variation' ).css( 'display', 'block' );
								selected_order_bump_popup.find( '.wps_ubo_bump_add_to_cart_button' ).css( 'display', 'flex' );

								selected_order_bump_popup.find( '.wps_bump_popup_image' ).html( msg['image'] );
								wps_ubo_lite_intant_zoom_img( selected_order_bump_popup );
								selected_order_bump_popup.find( '.variation_id_selected' ).val( msg['key'] );
								selected_order_bump_popup.find( '.wps_ubo_price_html_for_variation' ).html( msg['message'] );
							}
						}
					}
				);
			}
		);

		/*==========================================================================
								Variation popup add to cart
		============================================================================*/
		/*
		 * POP-UP ADD TO CART BUTTON [ works with variable products].
		 * To add the selected js.
		 */
		$( document ).on(
			'click',
			'.wps_ubo_bump_add_to_cart_button',
			function (e) {
				e.preventDefault();

				order_bump_index = jQuery( this ).attr( 'offer_bump_index' );

				// Order Bump Object.
				var parent_wrapper_class = '.wps_bump_popup_' + order_bump_index;
				var popup_obj = jQuery( parent_wrapper_class );

				// Meta form exists.
				if (popup_obj.length > 0) {
					let data_arr = [];
					popup_obj.find( '.wps_ubo_custom_meta_field' ).each(
						function (index) {

							let field_obj = {};
							if ('' == jQuery( this ).val()) {
								alert( jQuery( this ).attr( 'name' ) + ' field cannot be empty' );
								return;
							} else if ('checkbox' == jQuery( this ).attr( 'type' )) {

								// Push the values in an array.
								field_obj.name = jQuery( this ).attr( 'name' );
								field_obj.value = jQuery( this ).prop( 'checked' );
								data_arr[index] = field_obj;
								index++;
							} else {
								// Push the values in an array.
								field_obj.name = jQuery( this ).attr( 'name' );
								field_obj.value = jQuery( this ).val();

								data_arr[index] = field_obj;

							}
						}
					);

					// All fields are saved!
					if (data_arr.length == popup_obj.find( '.wps_ubo_custom_meta_field' ).length) {

						// Close popup and send add to cart request.
						triggerAddOfferVariation( jQuery( this ), data_arr );
					}
				} else { // Simple variable add to cart.
					triggerAddOfferVariation( jQuery( this ), [] );
				}
			}
		);

		/**
		 * Js to get variation product for "any variation" from dropdown in json format.
		 */
		var wps_orderbump_any_variation = {};
		jQuery( document ).on(
			'change',
			'select.wps_upsell_offer_variation_select',
			function () {
				wps_orderbump_any_variation[jQuery( this ).attr( 'id' )] = jQuery( this ).val();
			}
		);

			/**
			 * Process orderbump for variations.
			 *
			 * @param {object} object    Bump object
			 * @param {array}  formdata  Custom form object.
			 */
		function triggerAddOfferVariation(object, form_data) {
			// Prevent mulitple clicks on this button.
			object.prop( 'disabled', true );

			order_bump_index = object.attr( 'offer_bump_index' );
			console.log( order_bump_index );
			if (typeof order_bump_index === 'undefined') {
					console.log( 'order bump not found' );
					return;
			}

				 var wps_qty_variable = 1;

				 $value_of_input_field_to_check = object.closest( '.wps_bump_popup_select' ).find( '.wps_quantity_input' ).val();
				 $min_attr_value = object.closest( '.wps_bump_popup_select' ).find( '.wps_quantity_input' ).attr( 'min' );
				 $max_attr_value = object.closest( '.wps_bump_popup_select' ).find( '.wps_quantity_input' ).attr( 'max' );

				 // Order Bump Object.
				 var parent_wrapper_class = '.wps_parent_wrapper_order_bump_' + order_bump_index;
				 selected_order_bump = jQuery( parent_wrapper_class );

				 // Disable bump div.
				 $( parent_wrapper_class ).css( 'pointer-events', 'none' );
				 $( parent_wrapper_class ).css( 'opacity', '0.4' );

				 // Required Data.
				 bump_id = selected_order_bump.find( '.offer_shown_id' ).val();
				 bump_discount = selected_order_bump.find( '.offer_shown_discount' ).val();
				 bump_target_cart_key = selected_order_bump.find( '.target_id_cart_key' ).val();
				 order_bump_id = selected_order_bump.find( '.order_bump_id' ).val();
				 smart_offer_upgrade = selected_order_bump.find( '.order_bump_smo' ).val();

				 var variation_selected = '';
				jQuery( 'body' ).find( '.variation_id_selected' ).each(
					function () {
						if (object.attr( 'offer_bump_index' ) == order_bump_index) {
							variation_selected = jQuery( this ).val();
						}
					}
				);

			jQuery.ajax(
				{
					type: 'post',
					dataType: 'json',
					url: wps_ubo_lite_public.ajaxurl,
					data: {
						nonce: wps_ubo_lite_public.auth_nonce,
						action: 'add_variation_offer_in_cart',
						id: variation_selected, // variation offer product id.
						parent_id: bump_id, // variation offer parent product id.
						wps_orderbump_any_variation: wps_orderbump_any_variation, // variation data from dropdown
						discount: bump_discount,
						order_bump_id: order_bump_id,
						smart_offer_upgrade: smart_offer_upgrade,
						wps_qty_variable: wps_qty_variable, // Quantity attr

								  // Index : { digit }
						bump_index: order_bump_index,
						bump_target_cart_key: bump_target_cart_key,

								  // Form data if present.
						form_data: form_data
					},
					success: function (msg) {

								 $( 'body' ).removeClass( 'wps_upsell_variation_pop_up_body' );
								 $( '.wps_bump_popup_wrapper' ).css( 'display', 'none' );
								 $( 'body' ).trigger( 'update_checkout' );
								 $( document.body ).trigger( 'added_to_cart', {} );
								 $( document.body ).trigger( 'update_checkout' );

								 $( parent_wrapper_class ).css( 'pointer-events', 'all' );
								 $( parent_wrapper_class ).css( 'opacity', '1' );
								 $( '.wps_ubo_bump_add_to_cart_button' ).prop( 'disabled', false );

								 // When Reload is required.
						if ('subs_reload' == msg) {

							// Scroll Top and Reload.
							$( "html, body" ).scrollTop( 300 );
							location.reload();
						}

						var body = document.body;
						body.classList.remove( "wps_body_class_popup" );
					}
					}
			);
		}

		// Hde the checkbox from front end.
		 $( ".wps_hide_checkbox" ).hide();
	}
);
