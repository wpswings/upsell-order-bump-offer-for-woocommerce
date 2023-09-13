jQuery(document).ready(function ($) {


    $('.wps_product_discount').on('click', function () {


        jQuery(document).trigger('wc_fragment_refresh');
        // Get the div element by its class name
        var parent_element = document.querySelector(".wps_product_discount");
    
        // Get the value attribute from the div
        var parent_product_id = parent_element.getAttribute("value");
    
        //Get the quanity of the cart offer.
        var wps_cart_offer_quantity = document.querySelector('#wps_cart_offer_quantity');
        var wps_cart_offer_quantity_value = wps_cart_offer_quantity.value;

        //Get the discount type of the cart offer.
        var wps_cart_offer_discount_type = document.querySelector('#wps_cart_offer_discount_type');
        var wps_cart_offer_discount_type_value = wps_cart_offer_discount_type.value;

        //Get the product price  for the cart offer.
        var wps_cart_offer_product_price = document.querySelector('#wps_cart_offer_product_price');
        var wps_cart_offer_product_price = wps_cart_offer_product_price.value;
    
        // Get the select element by its ID
        var child_variation_id_element = document.querySelector("#wps-order-bump-child-id");
            
        if (null != child_variation_id_element) {
            // Get the currently selected value
            var child_variation_id = child_variation_id_element.value;
        }
    
        console.log(child_variation_id_element);
    
        jQuery.ajax({
            type: 'post',
            dataType: 'json',
            url: wps_ubo_lite_public_cart.ajaxurl,
            data: {
                nonce: wps_ubo_lite_public_cart.auth_nonce,
                action: 'add_cart_discount_offer_in_cart',
                parent_product_id: parent_product_id,
                child_product_id: child_variation_id,
                wps_cart_offer_quantity_value: wps_cart_offer_quantity_value,
                wps_cart_offer_discount_type_value: wps_cart_offer_discount_type_value,
                wps_cart_offer_product_price: wps_cart_offer_product_price
            },
            success: function (msg) {
                console.log(msg);
                $(document.body).trigger('added_to_cart', {});
                $(document.body).trigger('update_checkout');
                if (msg.message == 'remove') {
                    setTimeout(function () {
                        $(".wps_main_class_order").remove();
                    }, 1100);
                }
            }
        });
            
    });
});


jQuery(document).ready(function($) {
   var width_of_main = jQuery('.wps_main_class_order').width();
   if( width_of_main < 767 ){
    jQuery('.wps_main_class_order').addClass('wps_mobile-width');
   }
});