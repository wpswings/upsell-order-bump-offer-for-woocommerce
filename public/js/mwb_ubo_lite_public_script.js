jQuery(document).ready( function($) {

    // When bump is prepared we will get this data.
    var bump_id = '';
    var bump_discount = '';
    var bump_target_cart_key = '';
    var default_image = '';
    var default_price = '';

    /*
     * POP-UP JS.
     * To hide on click close.
     */
    $(document).on('click', '.mwb_bump_popup_close', function(e) {
        $( '.mwb_upsell_offer_main_wrapper' ).css( 'pointer-events', 'all' );
        $( '.mwb_upsell_offer_main_wrapper' ).css( 'opacity', '1' );
        $('body').removeClass( 'mwb_upsell_variation_pop_up_body' ); 
        $('.mwb_bump_popup_wrapper').css('display','none');
        $('#add_offer_in_cart').prop('checked', false);
    });

    /*
     * POP-UP Select change JS,
     * To add the price html and image of selected variation in popup.
     */

    $(document).on('change', '.mwb_upsell_offer_variation_select', function(e) {

        var selected_variations = $( '.mwb_upsell_offer_variation_select' );
        var attributes_selected = [];
        
        if( default_image == '' ) {
            default_image = $( '.woocommerce-product-gallery__image' );
        }

        for ( var i = selected_variations.length - 1; i >= 0; i-- ) {

            if( selected_variations[i].value == '' ) {

                // Default View on no selection.
                $( '.mwb_bump_popup_image' ).html( default_image );
                $('.woocommerce-product-gallery__image').zoom();
                $('#mwb_ubo_err_waring_for_variation').css( 'display','none' );
                $('#mwb_ubo_price_html_for_variation').css( 'display','block' );
                $('#mwb_ubo_bump_add_to_cart_button').css( 'display','none' );
                $('#mwb_ubo_price_html_for_variation').html( default_price );

               return;

            } else {
                attributes_selected[ selected_variations[i].id ] = selected_variations[i].value; 
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
        attributes_selected = Object.assign({}, attributes_selected );
        bump_id = $('#offer_shown_id').val();
        bump_discount = $('#offer_shown_discount').val();
        bump_target_cart_key = $('#target_id_cart_key').val();

        jQuery.ajax({

            type: 'post',
            dataType: 'json',
            url: mwb.ajaxurl,
            data: { 
                nonce : mwb.auth_nonce, 
                action: 'search_variation_id_by_select' ,  
                attributes_selected_options: attributes_selected , 
                id: bump_id,
                discount: bump_discount, 
                bump_target_cart_key :bump_target_cart_key  
            },

            success: function( msg ){
                if( msg['key'] == 'stock' ) {

                    $('#mwb_ubo_err_waring_for_variation').css('display','flex');
                    $('#mwb_ubo_price_html_for_variation').css('display','none');
                    $('#mwb_ubo_bump_add_to_cart_button').css('display','none');

                    $( '.mwb_bump_popup_image' ).html( msg['image'] );
                    $('.woocommerce-product-gallery__image').zoom();
                    $( '#mwb_ubo_err_waring_for_variation' ).html( msg['message'] );

                } else if ( msg['key'] == 'not_available' ) {

                    $('#mwb_ubo_err_waring_for_variation').css('display','flex');
                    $('#mwb_ubo_price_html_for_variation').css('display','none');
                    $('#mwb_ubo_bump_add_to_cart_button').css('display','none');

                    $( '.mwb_bump_popup_image' ).html( msg['image'] );
                    $('.woocommerce-product-gallery__image').zoom();
                    $( '#mwb_ubo_err_waring_for_variation' ).html( msg['message'] );

                } else if ( ! isNaN(msg['key']) ) {

                    $('#mwb_ubo_err_waring_for_variation').css('display','none');
                    $('#mwb_ubo_price_html_for_variation').css('display','block');
                    $('#mwb_ubo_bump_add_to_cart_button').css('display','flex');

                    $( '.mwb_bump_popup_image' ).html( msg['image'] );
                    $('.woocommerce-product-gallery__image').zoom();
                    $('#variation_id_selected').val( msg['key'] );
                    $( '#mwb_ubo_price_html_for_variation' ).html( msg['message'] );
                }
            }
        });
    });

    /*
     * POP-UP ADD TO CART BUTTON [ works with variable products].
     * To add the selected js.
     */
    $(document).on( 'click', '#mwb_ubo_bump_add_to_cart_button', function(e) {
        e.preventDefault();

        // Disable bump div.
        $( '.mwb_upsell_offer_main_wrapper' ).css( 'pointer-events', 'none' );
        $( '.mwb_upsell_offer_main_wrapper' ).css( 'opacity', '0.4' );
        
        var variation_selected = $( '#variation_id_selected' ).val();
        bump_id = $('#offer_shown_id').val();
        bump_discount = $('#offer_shown_discount').val();
        bump_target_cart_key = $('#target_id_cart_key').val();

        jQuery.ajax({

            type: 'post',
            dataType: 'json',
            url: mwb.ajaxurl,
            data: { 
                nonce : mwb.auth_nonce, 
                action: 'add_variation_offer_in_cart' ,
                id: variation_selected, 
                parent_id: bump_id, 
                discount: bump_discount, 
                bump_target_cart_key :bump_target_cart_key  
            },
            success: function( msg ){
                
                $('body').removeClass( 'mwb_upsell_variation_pop_up_body' );
                $( '.mwb_bump_popup_wrapper' ).css( 'display','none' );
                $( 'body' ).trigger( 'update_checkout' );
                $( '.mwb_upsell_offer_main_wrapper' ).css( 'pointer-events', 'all' );
                $( '.mwb_upsell_offer_main_wrapper' ).css( 'opacity', '1' );
            }
        });
    });

    /**
     * CHECKBOX ADD TO CART [ works with simple product and product variations ].
     */
    $(document).on('click', '#add_offer_in_cart', function(e) {

        // Disable bump div.
        $( '.mwb_upsell_offer_main_wrapper' ).css( 'pointer-events', 'none' );
        $( '.mwb_upsell_offer_main_wrapper' ).css( 'opacity', '0.4' );

        if ( $( '#add_offer_in_cart' ).is( ':checked' ) ) {

            $( '.mwb_bump_popup_loader' ).css( 'display','flex' );
            
            bump_id = $('#offer_shown_id').val();
            bump_discount = $('#offer_shown_discount').val();
            bump_target_cart_key = $('#target_id_cart_key').val();

            // Add product to cart.
            jQuery.ajax({

                type: 'post',
                dataType: 'json',
                url: mwb.ajaxurl,
                data: { 
                    nonce : mwb.auth_nonce,
                    action: 'add_offer_in_cart',
                    id: bump_id,
                    discount: bump_discount,
                    bump_target_cart_key :bump_target_cart_key
                },

                success: function( msg ) {

                    if( msg['key'] == 'true' ) {

                        $('#mwb_ubo_price_html_for_variation').html( msg['message'] );
                        $( '.mwb_bump_popup_loader' ).css('display','none');
                        $( '.mwb_bump_popup_wrapper' ).css('display','flex');
                        $('body').addClass( 'mwb_upsell_variation_pop_up_body' );

                        if( default_price == '' ) {
                            default_price = $( '#mwb_ubo_price_html_for_variation' ).html();
                        }

                    } else {
                            
                        $( '.mwb_bump_popup_loader' ).css('display','none');
                        $( 'body' ).trigger( 'update_checkout' );
                        $( '.mwb_upsell_offer_main_wrapper' ).css( 'pointer-events', 'all' );
                        $( '.mwb_upsell_offer_main_wrapper' ).css( 'opacity', '1' );
                    }
                }
            });
      
        } else {
            
            // Remove the same product from cart.
            jQuery.ajax({

                type: 'post',
                dataType: 'json',
                url: mwb.ajaxurl,
                data: { 
                    nonce : mwb.auth_nonce,
                    action: 'remove_offer_in_cart' 
                },
                
                success: function( msg ){

                    $( 'body' ).trigger( 'update_checkout' );
                    $( '.mwb_upsell_offer_main_wrapper' ).css( 'pointer-events', 'all' );
                    $( '.mwb_upsell_offer_main_wrapper' ).css( 'opacity', '1' );  
                }   
            });
        }
    });

    // Onclick outside the div close the popup.
    $('body').click
    (
      function(e)
      {
        if( e.target.className == 'mwb_bump_popup_wrapper' )
        {   
            $( '.mwb_upsell_offer_main_wrapper' ).css( 'pointer-events', 'all' );
            $( '.mwb_upsell_offer_main_wrapper' ).css( 'opacity', '1' );
            $('body').removeClass( 'mwb_upsell_variation_pop_up_body' ); 
            $('.mwb_bump_popup_wrapper').hide();
            $('#add_offer_in_cart').prop('checked', false);
        }
      }
    );

    if( mwb.mobile_view != 1 ) {

        // Function for zooming image( not for mobile view ).
        $(document).on('hover', '.mwb_bump_popup_image', function(e) {

            // Block opening image.
            e.preventDefault();
            $('.woocommerce-product-gallery__image').zoom({
                magnify : 1.0  // Magnify upto 120 %.
            });
        });
    } else {

        $(document).on('click', '.mwb_bump_popup_image', function(e) {

            // Block opening image.
            e.preventDefault();
        });
    }


// END OF SCRIPT
});
