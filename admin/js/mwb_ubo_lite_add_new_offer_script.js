jQuery(document).ready( function($) {

	// Target product search.
	jQuery('.wc-bump-product-search').select2({
  		ajax:{
    			url: ajaxurl,
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

	// Offer product search.
	jQuery('.wc-offer-product-search').select2({
  		ajax:{
    			url: ajaxurl,
    			dataType: 'json',
    			delay: 200,
    			data: function (params) {
      				return {
        				q: params.term,
        				action: 'search_products_for_offers'
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

	// Target Categories Search.
	jQuery('.wc-bump-product-category-search').select2({
  		ajax:{
    			url: ajaxurl,
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
	
});