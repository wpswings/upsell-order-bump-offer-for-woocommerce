jQuery(document).ready(function() {
    jQuery('.wps-obop__open-pop').hide();
    
    jQuery(document).on('click', '.wps-obop__open-pop', function() {
        jQuery('.w-obop__bg').show();
        jQuery('.w-obop__wrap').addClass('w-obop__show-wrap');
    })
    jQuery(document).on('click', '.w-obop__head .w-close,.w-obop__bg,.w-obop__foot-link.w-shop-more', function() {
        jQuery('.w-obop__wrap').removeClass('w-obop__show-wrap');
        jQuery('.w-obop__bg').hide();
    })

    jQuery(document).on('click', '.single_add_to_cart_button ,.add_to_cart_button', function (e) {
        e.preventDefault();
        var wps_product_id = jQuery('.single_add_to_cart_button').val();
        var wps_product_id_shop = jQuery(this).attr('data-product_id');
        var wps_targeted_varaition_id = jQuery('.variation_id').val();

            // Add recommandated product to popup.
            jQuery.ajax({
                type: 'post',
                dataType: 'json',
                url: wps_ubo_lite_public_recommendated.ajaxurl,
                data: {
                    nonce: wps_ubo_lite_public_recommendated.auth_nonce,
                    action: 'add_recommendated_offer_in_popup',
                    target_product_id : wps_product_id,
                    wps_product_id_shop : wps_product_id_shop,
                    wps_targeted_varaition_id : wps_targeted_varaition_id,
                },
                success: function (msg) {

                    // Get the current page URL
                    var currentPageURL = window.location.href;
                    var wps_is_shop_page = currentPageURL.includes('/shop/');
    
                    if(true == msg.wps_show_recommend_product_in_popup && false == wps_is_shop_page && (! msg.count.includes('empty bump recommendation'))){

                    jQuery('.w-obop__bg').show();
                    jQuery('.w-obop__wrap').addClass('w-obop__show-wrap');
                    }

                    if(true == msg.wps_show_recommend_product_in_popup && true == wps_is_shop_page && (! msg.count.includes('empty bump recommendation'))){
                        jQuery('#w-obop__popup_' + msg.wps_target_product  + ' ' +'.w-obop__bg').show();
                        jQuery('#w-obop__popup_' + msg.wps_target_product  + ' ' +'.w-obop__wrap').addClass('w-obop__show-wrap');
                        } else {
                            console.log('Error in displaying the recommend in the pop up');
                        }

                        if(false == msg.wps_show_recommend_product_in_popup){
                            console.log('select option clicked');
                        }
                }
              
            });

        });

        jQuery('.single_add_to_cart_button').on('click', function(e){ 
            e.preventDefault();
            $thisbutton = jQuery(this),
                        $form = $thisbutton.closest('form.cart'),
                        id = $thisbutton.val(),
                        product_qty = $form.find('input[name=quantity]').val() || 1,
                        product_id = $form.find('input[name=product_id]').val() || id,
                        variation_id = $form.find('input[name=variation_id]').val() || 0;
            var data = {
                    action: 'ql_woocommerce_ajax_add_to_cart',
                    product_id: product_id,
                    product_sku: '',
                    quantity: product_qty,
                    variation_id: variation_id,
                    nonce: wps_ubo_lite_public_recommendated.auth_nonce,
                };
            jQuery.ajax({
                    type: 'post',
                    url: wps_ubo_lite_public_recommendated.ajaxurl,
                    data: data,
                    beforeSend: function (response) {

                        $thisbutton.removeClass('added').addClass('loading');
                    },
                    complete: function (response) {

                        $thisbutton.addClass('added').removeClass('loading');
                    }, 
                    success: function (response) { 

                        if (response.error & response.product_url) {
                            window.location = response.product_url;
                            return;
                        } else { 
                            jQuery(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $thisbutton]);
                        } 
                        // Get the <span> element by its ID
                        var wps_my_span = document.getElementById("wps_cart_content");

                        // Update the text content
                        wps_my_span.textContent = response;
                    }, 
                }); 
             });

             jQuery('.wps-add-to-cart-button').on('click', function(e){ 

                //For the other simple product type.
                var productId = this.getAttribute('data-product-id');
                var product_price_id = this.getAttribute('data-price');
                var wps_target_product_id = jQuery('.single_add_to_cart_button').val();
  
                //For variation product.
                 var wps_variation_product_id = jQuery('input[name="product_id"]').val();
  
                console.log(wps_variation_product_id + ' ' + wps_target_product_id + ' ' + productId + ' ' +product_price_id);
  
              jQuery.ajax({
                  type: 'post',
                  dataType: 'json',
                  url: wps_ubo_lite_public_recommendated.ajaxurl,
                  data: {
                      nonce: wps_ubo_lite_public_recommendated.auth_nonce,
                      action: 'add_to_cart_recommendation',
                      wps_product_id : productId,
                      wps_product_price : product_price_id,
                      wps_target_product_id : wps_target_product_id,
                  },
                  success: function (msg) {
                      
                        var loader = document.querySelector('.wps_loader_' + msg.product_id);
                        // Show the loader
                       loader.style.display = 'block';
                      
                        // Simulate a delay (remove this in your actual code)
                        setTimeout(function() {
                            // Hide the loader when the action is complete
                            loader.style.display = 'none';
                        }, 1100); 
                      
                        // Get the <span> element by its ID
                         var wps_my_span = document.getElementById("wps_cart_content");

                         // Update the text content
                         wps_my_span.textContent = msg.cart_count;       
                  }
                
              });
  
      });
});