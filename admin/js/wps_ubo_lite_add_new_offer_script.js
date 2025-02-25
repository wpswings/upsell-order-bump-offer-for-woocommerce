jQuery(document).ready(function ($) {
	

	// Target product search.
	jQuery('.wc-bump-coupon-search').select2({
		ajax:{
			  url: wps_ubo_lite_ajaxurl.ajaxurl,
			  dataType: 'json',
			  delay: 200,
			  data: function (params) {
					return {
					  q: params.term,
					  action: 'search_coupon_for_offers'
					};
			  },
			  processResults: function( data ) {
			  var options = [];
			  if ( data ) 
			  {
				  $.each( data, function( index, text )
				  {
					  text[1]+='( #'+text[0]+')';
					  options.push( { id: text[0], text: text[1]  } );
				  });
			  }
			  return {
				  results:options
			  };
		  },
		  cache: true
	  },
	  minimumInputLength: 3 // The minimum of symbols to input before perform a search.
  });



	// Target product search.
	jQuery('.wc-bump-product-search').select2({
  		ajax:{
    			url: wps_ubo_lite_ajaxurl.ajaxurl,
    			dataType: 'json',
    			delay: 200,
    			data: function (params) {
      				return {
        				q: params.term,
        				action: 'search_products_for_bump'
      				};
    			},
    			processResults: function( data ) {
				var options = [];
				if ( data ) 
				{
					$.each( data, function( index, text )
					{
						text[1]+='( #'+text[0]+')';
						options.push( { id: text[0], text: text[1]  } );
					});
				}
				return {
					results:options
				};
			},
			cache: true
		},
		minimumInputLength: 3 // The minimum of symbols to input before perform a search.
	});

		// Target bump search.
		jQuery('.wc-bump-offer-search').select2({
			ajax:{
				  url: wps_ubo_lite_ajaxurl.ajaxurl,
				  dataType: 'json',
				  delay: 200,
				  data: function (params) {
						return {
						  q: params.term,
						  action: 'search_products_for_bump_offer_id'
						};
				  },
				  processResults: function( data ) {
				  var options = [];
				  if ( data ) 
				  {
					  $.each( data, function( index, text )
					  {
						  text[1]+='( #'+text[0]+')';
						  options.push( { id: text[0], text: text[1]  } );
					  });
				  }
				  return {
					  results:options
				  };
			  },
			  cache: true
		  },
		  minimumInputLength: 3 // The minimum of symbols to input before perform a search.
		});
	

	// Offer product search.
	jQuery('.wc-offer-product-search').select2({
  		ajax:{
    			url: wps_ubo_lite_ajaxurl.ajaxurl,
    			dataType: 'json',
    			delay: 200,
    			data: function (params) {
      				return {
        				q: params.term,
        				action: 'search_products_for_offers'
      				};
    			},
			processResults: function (data) {
				var options = [];
				if ( data ) 
				{
					$.each( data, function( index, text )
					{
						text[1]+='( #'+text[0]+')';
						options.push( { id: text[0], text: text[1]  } );
					});
				}
				return {
					results:options
				};
			},
			cache: true
		},
		minimumInputLength: 3 // The minimum of symbols to input before perform a search.
	});

	// Target Categories Search.
	jQuery('.wc-bump-product-category-search').select2({
  		ajax:{
    			url: wps_ubo_lite_ajaxurl.ajaxurl,
    			dataType: 'json',
    			delay: 200,
    			data: function (params) {
      				return {
        				q: params.term,
        				action: 'search_product_categories_for_bump'
      				};
    			},
    			processResults: function( data ) {
				var options = [];
				if ( data ) 
				{
					$.each( data, function( index, text )
					{
						text[1]+='( #'+text[0]+')';
						options.push( { id: text[0], text: text[1]  } );
					});
				}
				return {
					results:options
				};
			},
			cache: true
		},
		minimumInputLength: 3 // The minimum of symbols to input before perform a search.
	});

	// Target date.
	jQuery('.wc-bump-schedule-search').select2();
	jQuery('.wc-bump-exclude-roles-search').select2();
	
	$(document).on(
		'click',
		'#wps_one_click_upsell',
		function (e) {
			e.preventDefault();

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'wps_install_and_redirect_upsell_plugin'
				},
				success: function (response) {
					if (response.success) {
						window.location.href = response.data.redirect_url;
					} else {
						alert('Error: ' + response.error);
					}
				},
				error: function(error) {
					console.log(error); // Log the error for debugging
				}
			});


		});
	
	
	//Product Search Script Of One CLick Upsell Funnel.
	jQuery('.wc-offer-product-search').select2({
		ajax:{
			  url :wps_ubo_lite_ajaxurl.ajaxurl,
			  dataType: 'json',
			  delay: 200,
			  data: function (params) {
					return {
					  q: params.term,
					  nonce : wps_ubo_lite_ajaxurl.one_click_funnel_nonce,
					  action: 'seach_products_for_offers'
					};
			  },
			  processResults: function( data ) {
			  var options = [];
			  if ( data ) 
			  {
				  $.each( data, function( index, text )
				  {
					  text[1]+='( #'+text[0]+')';
					  options.push( { id: text[0], text: text[1]  } );
				  });
			  }
			  return {
				  results:options
			  };
		  },
		  cache: true
	  },
	  minimumInputLength: 3 // the minimum of symbols to input before perform a search
	});
	

	// Show hide clear button.
	jQuery( '.wc-offer-product-search' ).on( 'change', function(e) {

		if ( jQuery( this ).val() ) {

			jQuery( '.wps-upsell-offer-product-clear' ).show();
		}

		else {

			jQuery( '.wps-upsell-offer-product-clear' ).hide();
		}
	});

	// Clear values.
	jQuery('.wps-upsell-offer-product-clear').on('click', function (e) {
	jQuery( this ).parent().find( '.wc-offer-product-search' ).empty();
	}); 


    //Js Of the one click funnel search.
	jQuery('.wc-funnel-product-search').select2({
		ajax:{
			  url :wps_ubo_lite_ajaxurl.ajaxurl,
			  dataType: 'json',
			  delay: 200,
			  data: function (params) {
					return {
					  q: params.term,
					  nonce : wps_ubo_lite_ajaxurl.one_click_funnel_nonce,
					  action: 'seach_products_for_funnel'
					};
			  },
			  processResults: function( data ) {
			  var options = [];
			  if ( data ) 
			  {
				  $.each( data, function( index, text )
				  {
					  text[1]+='( #'+text[0]+')';
					  options.push( { id: text[0], text: text[1]  } );
				  });
			  }
			  return {
				  results:options
			  };
		  },
		  cache: true
	  },
	  minimumInputLength: 3 // the minimum of symbols to input before perform a search
	});
	

	    // Dismiss Elementor inactive notice.
		$(document).on('click', '#wps_upsell_dismiss_elementor_inactive_notice', function(e) {

			e.preventDefault();
	
			$.ajax({
				type:'POST',
				url :wps_ubo_lite_ajaxurl.ajaxurl,
				data:{
					action: 'wps_upsell_dismiss_elementor_inactive_notice',
				},
	
				success:function() {
	
					window.location.reload();
				}
		   });		
		});
	
		// Create New Offer.
		jQuery( '#wps_upsell_create_new_offer' ).on( 'click', function(e) {
			// alert('click it');
			e.preventDefault();
	
			// Last offer id.
			var index = $('.new_created_offers:last').data('id');
	
			// Current funnel id.
			var funnel = $(this).data('id');
	
			// Show loading icon.
			$('#wps_wocuf_pro_loader').removeClass('hide');
			$('#wps_wocuf_pro_loader').addClass('show');
	
			upsell_create_new_offer_post_request( index, funnel );		
		});
	
	
		function upsell_create_new_offer_post_request( index, funnel ) {

			// Increase offer id.
			++index;
	
			$.ajax({
				type:'POST',
				url :wps_ubo_lite_ajaxurl.ajaxurl,
				data:{
					action: 'wps_wocuf_pro_return_offer_content',
					nonce : wps_ubo_lite_ajaxurl.one_click_funnel_nonce,
					wps_wocuf_pro_flag: index,
					wps_wocuf_pro_funnel: funnel
				},
	
				success:function( data ) {
	
					// Hide loading icon.
					jQuery('#wps_wocuf_pro_loader').removeClass('show');
					jQuery('#wps_wocuf_pro_loader').addClass('hide');
	
					jQuery('.new_offers').append(data);
	
					// Slidedown.
					jQuery('.new_created_offers').slideDown( 1500 );
	
					// Scrolldown Animate.
					var scroll_height = $(document).height() - 300;
					$('html, body').animate({ scrollTop: scroll_height }, 1000 );
	
	
					// Remove Added Offers.
					jQuery('.wps_wocuf_pro_delete_new_created_offers').on( 'click', function(e) {
						e.preventDefault();
						var btn_id = $(this).data( 'id' );
						jQuery("div.new_created_offers[data-id='" + btn_id + "']").slideUp( 'slow', function() { $(this).remove(); } );
					});
	
					// Reinitialize product search in new offer.
					jQuery('.wc-offer-product-search').select2({
						  ajax:{
								url :wps_ubo_lite_ajaxurl.ajaxurl,
								dataType: 'json',
								delay: 200,
								data: function (params) {
									  return {
										q: params.term,
										nonce : wps_ubo_lite_ajaxurl.one_click_funnel_nonce,
										action: 'seach_products_for_offers'
									  };
								},
								processResults: function( data ) {
								var options = [];
								if ( data ) 
								{
									$.each( data, function( index, text )
									{
										text[1]+='( #'+text[0]+')';
										options.push( { id: text[0], text: text[1]  } );
									});
								}
								return {
									results:options
								};
							},
							cache: true
						},
						minimumInputLength: 3 // the minimum of symbols to input before perform a search
					});
	
				}
		   });
	}
	

		/**
	 * Custom Image setup.
	 * Wordpress image upload.
	 */
		jQuery(function($){
			/*
			 * Select/Upload image(s) event.
			 */
			jQuery('body').on('click', '.wps_wocuf_pro_upload_image_button', function(e){
	
				e.preventDefault();
				var button = jQuery(this),
				custom_uploader = wp.media({
					title: 'Insert image',
					library : {
						type : 'image'
					},
					button: {
						text: 'Use this image' 
					},
					multiple: false
				}).on('select', function() {
					var attachment = custom_uploader.state().get('selection').first().toJSON();
					jQuery(button).removeClass('button').html('<img class="true_pre_image" src="' + attachment.url + '" style="max-width:150px;display:block;" />').next().val(attachment.id).next().show();
				}).open();
			});
		 
			/*
			 * Remove image event.
			 */
			jQuery('body').on('click', '.wps_wocuf_pro_remove_image_button', function(e){
				e.preventDefault();
				jQuery(this).hide().prev().val('').prev().addClass('button').html('Upload image');
				return false;
			});
		});
	
	
	
		// Reflect Funnel name input value.
		$("#wps_upsell_funnel_name").on("change paste keyup", function() {
	   
			$("#wps_upsell_funnel_name_heading h2").text( $(this).val() );
		}); 
	
		// Funnel status Live <->  Sandbox.
		$('#wps_upsell_funnel_status_input').click( function() {
	
			if( true === this.checked ) {
	
				$('.wps_upsell_funnel_status_on').addClass('active');
				$('.wps_upsell_funnel_status_off').removeClass('active');
			}
	
			else {
	
				$('.wps_upsell_funnel_status_on').removeClass('active');
				$('.wps_upsell_funnel_status_off').addClass('active');
			}
		});
	
		// Preview respective template.
		$(document).on('click', '.wps_upsell_view_offer_template', function(e) {
	
			// Current template id.
			var template_id = $(this).data( 'template-id' );
	
			$('.wps_upsell_offer_template_previews').show();
	
			$('.wps_upsell_offer_template_preview_' + template_id ).addClass('active');
	
			$('body').addClass('wps_upsell_preview_body');
	
		});
	
		// Close Preview of respective template.
		$(document).on('click', '.wps_upsell_offer_preview_close', function(e) {
	
			$('body').removeClass('wps_upsell_preview_body');
	
			$('.wps_upsell_offer_template_preview_one').removeClass('active');
			$('.wps_upsell_offer_template_preview_two').removeClass('active');
			$('.wps_upsell_offer_template_preview_three').removeClass('active');
	
			$('.wps_upsell_offer_template_previews').hide();
	
		});
	
		$('.wps_upsell_slide_down_link').click(function(e) {
	
			e.preventDefault();
	
			$('.wps_upsell_slide_down_content').slideToggle("fast");
	
		});
	
	
		(function( $ ) {
			'use strict';
			$(document).ready(function(){
				jQuery('.wps_ubo_lite_go_pro_popup_close').attr('href','javascript:void(0)');
			
				$('#wps_wocuf_pro_target_pro_ids').select2();
				$('#product_features_ubo_lite').hide();
				//  Add multiselect to Funnel Schedule since v3.0.0
				if ( $( '.wps-upsell-funnel-schedule-search' ).length ) {
		
					$( '.wps-upsell-funnel-schedule-search' ).select2();
		
				}
			});
	})(jQuery);
	


// Insert and Activate respective template.
$(document).on('click', '.wps_upsell_activate_offer_template', function(e) {

	e.preventDefault();

	// Current clicked button object.
	var current_button = $(this);

	// Current funnel id.
	var funnel_id = $(this).data( 'funnel-id' );
	// Current offer id.
	var offer_id = $(this).data( 'offer-id' );
	// Current template id.
	var template_id = $(this).data( 'template-id' );
	// Current offer post id.
	var offer_post_id = $(this).data( 'offer-post-id' );

	// Show loading icon.
	$('#wps_wocuf_pro_loader').removeClass('hide');
	$('#wps_wocuf_pro_loader').addClass('show');

	$.ajax({
		type:'POST',
		url :wps_ubo_lite_ajaxurl.ajaxurl,
		data:{
			action: 'wps_upsell_activate_offer_template_ajax',
			nonce : wps_ubo_lite_ajaxurl.one_click_funnel_nonce,
			funnel_id: funnel_id,
			offer_id: offer_id,
			template_id: template_id,
			offer_post_id: offer_post_id
		},

		success:function( data ) {

			data = JSON.parse( data );
			
			if( true === data.status ) {

				// Update Offer template value to current template id. 
				current_button.closest('.wps_upsell_offer_templates_parent').find('.wps_wocuf_pro_offer_template_input').val(template_id);

				/**
				 * Append offer section parameter in current url, so after form submit we can scroll back to
				 * the current respective offer section.
				 */
				var href_funnel_offer_url = window.location.href;
				href_funnel_offer_url += '&wps-upsell-offer-section=offer-section-' + offer_id; 
				window.history.replaceState( {}, '', href_funnel_offer_url );

				// Hide loading icon.
				$('#wps_wocuf_pro_loader').removeClass('show');
				$('#wps_wocuf_pro_loader').addClass('hide');

				// Submit form ( Save upsell funnel ).
				$("#wps_wocuf_pro_creation_setting_save").click();
			} 
		 }
   });		
});

	
	
});