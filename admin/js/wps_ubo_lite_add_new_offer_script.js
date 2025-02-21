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


    //Js Of the one click funner search.
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
});