jQuery(document).ready(function ($) {

    $(document).ready(function () {
        var wps_current_theme = wps_ubo_lite_public.current_theme;
        if ('Twenty Twenty-Five' == wps_current_theme || 'Twenty Twenty-Three' == wps_current_theme || 'Twenty Twenty-Four' == wps_current_theme || 'Betheme' == wps_current_theme) {
            $("body > div.wrapup_order_bump").first().hide();
        }
    });

    if (document.querySelector('.woocommerce-order-received')) {
        // You are on the cart page.
        $('.wrapup_order_bump').hide();
    }


    $(document).on('click', '.wps-ob_ta-o-title', function(e) {
        // Check if the target element exists.
        var bumpButton = document.querySelector('.add_offer_in_cart');
        if (bumpButton) {
            // Trigger the click event on the target element
            bumpButton.click();
        } else {
            console.log('Element with class .add_offer_in_cart not found.');
        }
    });


    setTimeout(function () {

        var wps_is_checkout_block_use = wps_ubo_lite_public.wps_is_checkout_block_use;

        if (wps_is_checkout_block_use) {

            if (jQuery('.wrapup_order_bump').length > 0) {
                var data = jQuery('.wrapup_order_bump').html();

                if ('_before_place_order_button' == wps_ubo_lite_public.wps_order_bump_location_on_checkout) {
 
                    jQuery('.wp-block-woocommerce-checkout-order-summary-block').append('<div class = "wrapup_order_bump">' + data + '</div>');
                    $(".wp-block-woocommerce-checkout").siblings().remove();

                } else if ('_after_payment_gateways' == wps_ubo_lite_public.wps_order_bump_location_on_checkout) {

                    jQuery('.wc-block-checkout__payment-method').append('<div class = "wrapup_order_bump">' + data + '</div>');
                    $(".wp-block-woocommerce-checkout").siblings().remove();
                } else if ('_before_order_summary' == wps_ubo_lite_public.wps_order_bump_location_on_checkout) {
            
                    jQuery('.wp-block-woocommerce-checkout-order-summary-coupon-form-block').append('<div class = "wrapup_order_bump">' + data + '</div>');
                    $(".wp-block-woocommerce-checkout").siblings().remove();

                }
            }
        }

        var wps_is_cart_block_use = wps_ubo_lite_public.wps_is_cart_block_use;

        if ('on' == wps_ubo_lite_public.wps_enable_cart_upsell && wps_is_cart_block_use && (document.querySelector('.woocommerce-cart'))) {
            if (jQuery('.wrapup_order_bump').length > 0) {

                
                var data = jQuery('.wrapup_order_bump').html();

                if ('woocommerce_after_cart_totals' == wps_ubo_lite_public.wps_order_bump_location_on_cart) {

                    jQuery('.wp-block-woocommerce-cart-totals-block').append('<div class = "wrapup_order_bump">' + data + '</div>'); //after cart total.
                    $(".wp-block-woocommerce-cart").prev().remove();

                } else if ('woocommerce_cart_collaterals' == wps_ubo_lite_public.wps_order_bump_location_on_cart) {
                
                    jQuery('.wc-block-components-totals-footer-item').append('<div class = "wrapup_order_bump">' + data + '</div>'); //before cart total.
                    $(".wp-block-woocommerce-cart").prev().remove();

                } else if ('woocommerce_before_cart_totals' == wps_ubo_lite_public.wps_order_bump_location_on_cart) {
                    
                    jQuery(jQuery('.wp-block-woocommerce-cart-line-items-block').parent()).append('<div class = "wrapup_order_bump">' + data + '</div>'); //before cart total.
                    $(".wp-block-woocommerce-cart").prev().remove();
                }
  
            }
        }    
        //For the new template 10 js.
        var wps_ob_con = jQuery('.wps-ob-st');
                    if (wps_ob_con.width() < 396) {
                        wps_ob_con.addClass('ob_cont-full');
                }

    },1000)

    $(document).on('click', '.wc-block-cart-item__remove-link', function () {
        setTimeout(function() {
            location.reload();
        }, 2000);
    })

    if( 'yes' == wps_ubo_lite_public.wps_popup_body_class && (null == wps_ubo_lite_public.wps_popup_body_class)){
        var body = document.body;

        body.classList.remove("wps_body_class_popup");
    }

    setInterval(function() {
        $('.wps_product_gallery_wrapper').slick({
            dots: false,
            arrows: true,
            slidesToShow: 1,
            variableWidth: true,
            infinite:true,
            responsive: [{
                breakpoint: 1080,
                settings: {
                    arrows: false,
                }
            }]
        });

        var gallery_img = $('.wps_product_gallery_wrapper img');
        gallery_img.on('click', function() {
            var gallery_img_clone = $(this).clone();
            $('.wps_product_gallery_img_focus_wrapper').show();
            $('.wps_product_gallery_img_focus_wrapper_box').empty();
            $(gallery_img_clone).appendTo('.wps_product_gallery_img_focus_wrapper_box');
            $('.wps_product_gallery_img_focus_wrapper .close').on('click', function() {
                $('.wps_product_gallery_img_focus_wrapper').hide();
            });
        })
    }, 1000);

     
        $(document).on('click','.wps_product_info',function(){
            $(this).toggleClass('accordian--active');
            $(this).next('p').slideToggle();
        })

    function getNum(val) {
        if (isNaN(val) ) {
        return 0;
        }
        return val;
    }

    if ( wps_ubo_lite_public.hasOwnProperty( 'check_if_reload' ) ) {

        if ( 'reload' === wps_ubo_lite_public.check_if_reload ) {
        // Nothing to do.
        } else {
            if ( wps_ubo_lite_public.hasOwnProperty( 'timer' ) ) {
                var columns = wps_ubo_lite_public.check_if_reload;
                var rows = wps_ubo_lite_public.timer;
                var result = [];
                $.each( rows, function( index, value ) {
                    if ( columns.indexOf( parseInt( index ) ) >= 0 ) {
                      result[index] = value;
                    }
                } );

                setTimeout(function() { 
                    setInterval(function() {
                    for (var key in result) {

                        if ( 'yes' === result[key].enabled ) {

                            var deadline = new Date(result[key].counter).getTime();
                            var now = new Date().getTime();
                            var t = deadline - now;
                            var days = Math.floor(t / (1000 * 60 * 60 * 24));
                            var hours = Math.floor((t%(1000 * 60 * 60 * 24))/(1000 * 60 * 60));
                            var minutes = Math.floor((t % (1000 * 60 * 60)) / (1000 * 60));
                            var seconds = Math.floor((t % (1000 * 60)) / 1000);
                            document.getElementById("wps_day_time_" +  key ).innerHTML   =  getNum( days );
                            document.getElementById("wps_hour_time_"+  key).innerHTML    =  getNum( hours );
                            document.getElementById("wps_min_time_" +  key).innerHTML    =  getNum( minutes );
                            document.getElementById("wps_sec_time_" +  key).innerHTML    =  getNum( seconds );

                            const element = document.getElementById('wps_timer'+key);
                            const element_error = document.getElementById('expired_message'+ key);
                            element.style.backgroundColor = 'white';
                            element_error.style.backgroundColor = 'white';
                            if ( t < 0 ) {
                                $("#wps_timer"+ key). css({display: "none"});
                                document.getElementById("expired_message"+ key).innerHTML = "EXPIRED";
                                document.getElementById("wps_checkbox_offer" + key).disabled = true;
                                $("#wps_button_id_" + key).hide();
                                $("#expired_message" + key).parent().css("pointer-events", "none");
                            }
                        }
                    }
                }, 1000);
            },1000);
            }      
        }
    }
  
    // When bump is prepared we will get this data.
    var bump_id = '';
    var bump_discount = '';
    var bump_target_cart_key = '';
    var default_image = '';
    var default_price = '';
    var order_bump_index = '';
    var order_bump_id = '';
    var smart_offer_upgrade = '';
    var selected_order_bump = '';
    var selected_order_bump_popup = '';

    function wps_ubo_lite_intant_zoom_img(selected_order_bump_popup) {

        if (wps_ubo_lite_public.mobile_view != 1) {

            selected_order_bump_popup.find('.woocommerce-product-gallery__image').zoom();
        }
    }

    const triggerRemoveOffer = (order_bump_index, order_bump_id) => {

        //Getting Current theme Name.
        var wps_current_theme = wps_ubo_lite_public.current_theme;
        
        // Remove the same product from cart.
        jQuery.ajax({

            type: 'post',
            dataType: 'json',
            url: wps_ubo_lite_public.ajaxurl,
            data: {
                nonce: wps_ubo_lite_public.auth_nonce,
                action: 'remove_offer_in_cart',

                // Index : index_{ digit }
                bump_index: order_bump_index,
                order_bump_id: order_bump_id,
            },

            success: function (msg) {

                $('body').trigger('update_checkout');
                $(document.body).trigger('added_to_cart', {});
                $(document.body).trigger('update_checkout');

                //Mini-Cart Upadte on Checkout depending upon themes.
                wps_minicart_update(wps_current_theme);

                $('.wps_ubo_wrapper_' + order_bump_index).css('pointer-events', 'all');
                $('.wps_ubo_wrapper_' + order_bump_index).css('opacity', '1');
                var body = document.body;
                body.classList.remove("wps_body_class_popup");

                if (('Avada' == wps_current_theme || 'Divi' == wps_current_theme || 'Flatsome' == wps_current_theme) && (wps_ubo_lite_public.wps_silde_cart_plgin_active || 'Betheme' == wps_current_theme)) {
                    $("html, body").scrollTop(300);
                    location.reload(); //avada and side cart issue fixes.
                }

            }
        });
    }

    /**
     * Js to get variation product for "any variation" from dropdown in json format.
     */
    var wps_orderbump_any_variation = {};
    jQuery(document).on('change', 'select.wps_upsell_offer_variation_select', function () {
        wps_orderbump_any_variation[jQuery(this).attr('id')] = jQuery(this).val();
    });

    /**
     * Process orderbump for variations.
     * 
     * @param {object} object    Bump object
     * @param {array}  formdata  Custom form object.
     */
    function triggerAddOfferVariation(object, form_data) {

        // Prevent mulitple clicks on this button.
        object.prop('disabled', true);
        order_bump_index = object.attr('offer_bump_index');
        if (typeof order_bump_index === 'undefined') {
            console.log('order bump not found');
            return;
        }

        // Get product Quantity
        if ( object.closest('.wps_bump_popup_select').find('.wps_bump_name').attr("data-wps_is_fixed_qty") == 'true' && object.closest('.wps_bump_popup_select').find('.wps_bump_name').attr( "data-qty_allowed") == 'yes' ) {
            var wps_qty_variable = object.closest('.wps_bump_popup_select').find('.wps_bump_name').attr("data-wps_qty");
        } else if ( object.closest('.wps_bump_popup_select').find('.wps_quantity_input').val() != undefined && object.closest('.wps_bump_popup_select').find('.wps_bump_name').attr( "data-qty_allowed") == 'yes' && object.closest('.wps_bump_popup_select').find('.wps_bump_name').attr("data-wps_is_fixed_qty") == 'false' ) {
            var wps_qty_variable = object.closest('.wps_bump_popup_select').find('.wps_quantity_input').val();
        } else {
            var wps_qty_variable = 1;
        }

        $value_of_input_field_to_check = object.closest('.wps_bump_popup_select').find('.wps_quantity_input').val();
        $min_attr_value = object.closest('.wps_bump_popup_select').find('.wps_quantity_input').attr('min');
        $max_attr_value = object.closest('.wps_bump_popup_select').find('.wps_quantity_input').attr('max');

        if ( ( $min_attr_value != undefined && $min_attr_value != undefined ) ) {
            if ( (parseInt($value_of_input_field_to_check) >= parseInt($min_attr_value) && parseInt($value_of_input_field_to_check) <= parseInt($max_attr_value) ) ) {
                var inLimit = 'true';
            } else {
                alert( 'Max Limit ' + $max_attr_value + ' Min Limit ' + $min_attr_value );
                object.prop('checked', false);
                location.reload();
                return;
            }
        }

        // Order Bump Object.
        var parent_wrapper_class = '.wps_ubo_wrapper_index_' + order_bump_index;
        var selected_order_bump = jQuery(parent_wrapper_class);

        //Getting Current theme Name.
        var wps_current_theme = wps_ubo_lite_public.current_theme;


        // Disable bump div.
        $(parent_wrapper_class).css('pointer-events', 'none');
        $(parent_wrapper_class).css('opacity', '0.4');

        // Required Data.
        bump_id = selected_order_bump.find('.offer_shown_id').val();
        bump_discount = selected_order_bump.find('.offer_shown_discount').val();
        bump_target_cart_key = selected_order_bump.find('.target_id_cart_key').val();
        order_bump_id = selected_order_bump.find('.order_bump_id').val();
        smart_offer_upgrade = selected_order_bump.find('.order_bump_smo').val();

        var variation_selected = '';
        jQuery('body').find('.variation_id_selected').each(function () {
            if (object.attr('offer_bump_index') == order_bump_index) {
                variation_selected = jQuery('input.variation_id_selected').val();
            }
        });

        console.log(variation_selected); //No data is coming.);

        jQuery.ajax({
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

                $('body').removeClass('wps_upsell_variation_pop_up_body');
                $('.wps_bump_popup_wrapper').css('display', 'none');
                $('body').trigger('update_checkout');
                $(document.body).trigger('added_to_cart', {});
                $(document.body).trigger('update_checkout');

                //Mini-Cart Upadte on Checkout depending upon themes.
                wps_minicart_update(wps_current_theme);

                $(parent_wrapper_class).css('pointer-events', 'all');
                $(parent_wrapper_class).css('opacity', '1');
                $('.wps_ubo_bump_add_to_cart_button').prop('disabled', false);

                // When Reload is required.
                if ('subs_reload' == msg) {

                    // Scroll Top and Reload.
                    $("html, body").scrollTop(300);
                    location.reload();
                }

                var body = document.body;
                body.classList.remove("wps_body_class_popup");
            }
        });
    }

    /**
     * Process orderbump.
     * 
     * @param {object} object    Bump object
     * @param {array}  formdata  Custom form object.
     */
    function triggerAddOffer(object, formdata) {
        // Get product Quantity
        if ( object.closest('.wps_upsell_offer_main_wrapper').find('.wps_bump_name').attr("data-wps_is_fixed_qty") == 'true' && object.closest('.wps_upsell_offer_main_wrapper').find('.wps_bump_name').attr( "data-qty_allowed") == 'yes' ) {
            var wps_qty_variable = object.closest('.wps_upsell_offer_main_wrapper').find('.wps_bump_name').attr("data-wps_qty"); //check whether qty variable or not.
        } else if ( object.closest('.wps_upsell_offer_main_wrapper').find('.wps_quantity_input').val() != undefined && object.closest('.wps_upsell_offer_main_wrapper').find('.wps_bump_name').attr( "data-qty_allowed") == 'yes' && object.closest('.wps_upsell_offer_main_wrapper').find('.wps_bump_name').attr("data-wps_is_fixed_qty") == 'false' ) {
            var wps_qty_variable = object.closest('.wps_upsell_offer_main_wrapper').find('.wps_quantity_input').val(); //in these condition we check for min and max quantity.
        } else {
            var wps_qty_variable = 1;
        }

        $value_of_input_field_to_check = object.closest('.wps_upsell_offer_main_wrapper').find('.wps_quantity_input').val();
        $min_attr_value = object.closest('.wps_upsell_offer_main_wrapper').find('.wps_quantity_input').attr('min');
        $max_attr_value = object.closest('.wps_upsell_offer_main_wrapper').find('.wps_quantity_input').attr('max');
        //In Above get max , min and actual inside the input box.

        if ( ( $min_attr_value != undefined && $min_attr_value != undefined ) ) {
            if ( (parseInt($value_of_input_field_to_check) >= parseInt($min_attr_value) && parseInt($value_of_input_field_to_check) <= parseInt($max_attr_value) ) ) {
                var inLimit = 'true';
            } else {
                alert( 'Max Limit ' + $max_attr_value + ' Min Limit ' + $min_attr_value );
                object.prop('checked', false);
                location.reload();
                return;
            }
        }  //This will check valida value in the input box.

        order_bump_index = object.closest('.wps_upsell_offer_main_wrapper').find('.order_bump_index').val();
        parent_wrapper_class = '.wps_ubo_wrapper_' + order_bump_index;
        order_bump_id = object.closest('.wps_upsell_offer_main_wrapper').find('.order_bump_id').val();

        // Disable bump div.
        $('.wps_ubo_wrapper_' + order_bump_index).css('pointer-events', 'none');
        $('.wps_ubo_wrapper_' + order_bump_index).css('opacity', '0.4');
        if ($(parent_wrapper_class + ' .add_offer_in_cart').is(':checked')) {

            // Get Order Bump variation popup ready.
            handle_pre_selected_values();

            //Getting Current theme Name.
            var wps_current_theme = wps_ubo_lite_public.current_theme;

            // Show loader for Variable offers.
            if ('variable' == object.closest('.wps_upsell_offer_main_wrapper').find('.offer_shown_id_type').val()) {
                $('.wps_bump_popup_loader').css('display', 'flex');
            }

            bump_id = object.closest('.wps_upsell_offer_main_wrapper').find('.offer_shown_id').val(); // offer product id.
            bump_discount = object.closest('.wps_upsell_offer_main_wrapper').find('.offer_shown_discount').val();
            bump_target_cart_key = object.closest('.wps_upsell_offer_main_wrapper').find('.target_id_cart_key').val();
            smart_offer_upgrade = object.closest('.wps_upsell_offer_main_wrapper').find('.order_bump_smo').val();
            // alert('i am click');
            // console.log(formdata);
            
            // Add product to cart.
            jQuery.ajax({

                type: 'post',
                dataType: 'json',
                url: wps_ubo_lite_public.ajaxurl,
                data: {
                    nonce: wps_ubo_lite_public.auth_nonce,
                    action: 'add_offer_in_cart',
                    id: bump_id, // offer product id.
                    discount: bump_discount,
                    bump_target_cart_key: bump_target_cart_key,
                    order_bump_id: order_bump_id,
                    smart_offer_upgrade: smart_offer_upgrade,
                    wps_qty_variable: wps_qty_variable, // Quantity attr

                    // Index : index_{ digit }
                    bump_index: order_bump_index,
                    // Form data.
                    form_data: formdata,
                },

                success: function (msg) {
                    // For variable product.
                    if (msg['key'] == 'true') {
                        variation_popup_index = order_bump_index.replace('index_', '');
                        $('.wps_ubo_price_html_for_variation').html(msg['message']);
                        $('.wps_bump_popup_loader').css('display', 'none');
                        $('.wps_bump_popup_' + variation_popup_index).css('display', 'flex');
                        $('body').addClass('wps_upsell_variation_pop_up_body');

                        // Add zoom to defaut image.
                        selected_order_bump_popup = jQuery('.wps_bump_popup_' + variation_popup_index);
                        wps_ubo_lite_intant_zoom_img(selected_order_bump_popup);

                        if (default_price == '') {
                            default_price = $('.wps_ubo_price_html_for_variation').html();
                        }
                    }

                    // For simple Products and Variations.
                    else {

                        $('.wps_bump_popup_loader').css('display', 'none');
                        $('body').trigger('update_checkout');
                        $(document.body).trigger('added_to_cart', {});
                        $(document.body).trigger('update_checkout');

                        //Mini-Cart Upadte on Checkout depending upon themes.
                        wps_minicart_update(wps_current_theme);

                        $('.wps_ubo_wrapper_' + order_bump_index).css('pointer-events', 'all');
                        $('.wps_ubo_wrapper_' + order_bump_index).css('opacity', '1');

                        // When Reload is required.
                        if ('subs_reload' == msg) {

                            // Scroll Top and Reload.
                            $("html, body").scrollTop(300);
                            location.reload();
                        }

                        var body = document.body;
                        body.classList.remove("wps_body_class_popup");

                        if (('Avada' == wps_current_theme || 'Divi' == wps_current_theme || 'Flatsome' == wps_current_theme) && (wps_ubo_lite_public.wps_silde_cart_plgin_active || 'Betheme' == wps_current_theme)) {
                            $("html, body").scrollTop(300);
                            location.reload(); //avada and side cart issue fixes.
                        }

                    }
                }
            });
        }
    }

    // Prevent Enter Key Press for checkbox of Order Bump offers.
    $(document).on('keypress', '.add_offer_in_cart', function (e) {
        // The enter key code.
        if (e.which == 13) {
            e.preventDefault();
        }
    });

    /*==========================================================================
                            Add to cart checkbox click
    ============================================================================*/
    /**
     * CHECKBOX ADD TO CART [ works with simple product and product variations ].
     */
    jQuery(document).on('click', '.add_offer_in_cart', function (e) {
        order_bump_trigger_obj = jQuery(this);
        order_bump_index = order_bump_trigger_obj.closest('.wps_upsell_offer_main_wrapper').find('.order_bump_index').val();
        parent_wrapper_class = '.wps_ubo_wrapper_' + order_bump_index;
        order_bump_id = order_bump_trigger_obj.closest('.wps_upsell_offer_main_wrapper').find('.order_bump_id').val();

        // When offer is added.
        if (order_bump_trigger_obj.is(':checked')) {

            // Check if meta form present.
            let popup_obj = jQuery('#wps-meta-form-index-' + order_bump_id);

            let index = 0;

            // Meta form available.
            if (popup_obj.length > 0 && !popup_obj.hasClass('wps_bump_popup_variable_meta_form')) {

                open_custom_form(popup_obj, order_bump_trigger_obj);

                jQuery('.wps-meta-form-submit').on('click', function (e) {

                    e.preventDefault();
                    let data_arr = [];
                    jQuery('#wps-meta-form-index-' + order_bump_id).find('.wps_ubo_custom_meta_field').each(function () {

                        let field_obj = {};

                        if ('' == jQuery(this).val()) {
                            alert(jQuery(this).attr('name') + ' field cannot be empty');
                            return;
                        } else if ('checkbox' == jQuery(this).attr('type')) {

                            // Push the values in an array.
                            field_obj.name = jQuery(this).attr('name');
                            field_obj.value = jQuery(this).prop('checked');
                            data_arr[index] = field_obj;
                            index++;                        } else {
                            // Push the values in an array.
                            field_obj.name = jQuery(this).attr('name');
                            field_obj.value = jQuery(this).val();

                            data_arr[index] = field_obj;
                            index++;
                        }
                    });

                    data_arr = data_arr.filter(onlyUnique);

                    // All fields are saved!
                    if (data_arr.length == popup_obj.find('.wps_ubo_custom_meta_field').length) {

                        // Close popup and send add to cart request.
                        close_custom_form();
                        triggerAddOffer(order_bump_trigger_obj, data_arr);
                    }
                });

            } else {

                triggerAddOffer(jQuery(this), []);
            }

        } else {

            // Remove Offer.
            triggerRemoveOffer(order_bump_index, order_bump_id);
        }

    });

    function onlyUnique(value, index, self) {
        return self.indexOf(value) === index;
    }


    /*=======================================================================
                        Default selection trigger in variations popup.
    ========================================================================*/

    function handle_pre_selected_values() {
        var variations = jQuery('.wps_upsell_offer_variation_select');
        if (variations.length > 0) {
            var value_exists = false;
            variations.each(function () {
                if (jQuery(this).val() !== '') {
                    value_exists = true;
                    return;
                }
            });

            if (true == value_exists) {
                jQuery('.wps_upsell_offer_variation_select').trigger('change');
            }
        }
    }

    function open_custom_form(form_obj, order_bump_obj) {

        let form_wrap = form_obj.parent().parent().parent().parent();
        jQuery('body').css('overflow', 'hidden');
        if (jQuery('.wps-g-modal').hasClass('wps-modal--close')) {
            jQuery('.wps-g-modal').removeClass('wps-modal--close');
        }
        form_wrap.addClass('wps-modal--open');

        jQuery('.wps-g-modal__close').on('click', function () {
            order_bump_obj.prop('checked', false);
            close_custom_form();
        });
    }

    function close_custom_form() {
        jQuery('body').css('overflow', 'auto');
        jQuery('.wps-g-modal').addClass('wps-modal--close');
        setTimeout(function () {
            jQuery('.wps-g-modal').removeClass('wps-modal--open');
        }, 320);
    }

    /*=======================================================================
                        Select the variations in popup.
    ========================================================================*/
    /*
     * POP-UP Select change JS,
     * To add the price html and image of selected variation in popup.
     */

    $(document).on('change', '.wps_upsell_offer_variation_select', function (e) {

        var selected_order_bump_index = $(this).attr('order_bump_index');

        // Order Bump Object.
        var parent_wrapper_class = '.wps_ubo_wrapper_index_' + selected_order_bump_index;
        selected_order_bump = jQuery(parent_wrapper_class);

        // Order Bump Popup Object.
        var popup_wrapper_class = '.wps_bump_popup_' + selected_order_bump_index;
        selected_order_bump_popup = jQuery(popup_wrapper_class);

        // Fetch selected attributes.
        var selected_variations = selected_order_bump_popup.find('.wps_upsell_offer_variation_select');
        var attributes_selected = [];

        // Default image handle here.
        if (default_image == '') {

            if (selected_order_bump_popup.find('woocommerce-product-gallery__image')) {

                default_image = selected_order_bump_popup.find('.woocommerce-product-gallery__image');
            }

            else {

                default_image = selected_order_bump_popup.find('.woocommerce-placeholder');
            }
        }

        for (var i = selected_variations.length - 1; i >= 0; i--) {

            if (selected_variations[i].value == '') {

                // Default View on no selection.
                selected_order_bump_popup.find('.wps_bump_popup_image').html(default_image);
                wps_ubo_lite_intant_zoom_img(selected_order_bump_popup);
                selected_order_bump_popup.find('.wps_ubo_err_waring_for_variation').css('display', 'none');
                selected_order_bump_popup.find('.wps_ubo_price_html_for_variation').css('display', 'block');
                selected_order_bump_popup.find('.wps_ubo_bump_add_to_cart_button').css('display', 'none');
                selected_order_bump_popup.find('.wps_ubo_price_html_for_variation').html(default_price);

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
        attributes_selected = Object.assign({}, attributes_selected);

        // Required Data.
        bump_id = selected_order_bump.find('.offer_shown_id').val();
        bump_discount = selected_order_bump.find('.offer_shown_discount').val();
        wps_order_bump_id = $(this).attr('order_bump_index');

        jQuery.ajax({

            type: 'post',
            dataType: 'json',
            url: wps_ubo_lite_public.ajaxurl,
            data: {
                nonce: wps_ubo_lite_public.auth_nonce,
                action: 'search_variation_id_by_select',
                attributes_selected_options: attributes_selected,
                id: bump_id,
                discount: bump_discount,
                wps_order_bump_id,
            },

            success: function (msg) {
                if (msg['key'] == 'stock') {

                    selected_order_bump_popup.find('.wps_ubo_err_waring_for_variation').css('display', 'flex');
                    selected_order_bump_popup.find('.wps_ubo_price_html_for_variation').css('display', 'none');
                    selected_order_bump_popup.find('.wps_ubo_bump_add_to_cart_button').css('display', 'none');

                    selected_order_bump_popup.find('.wps_bump_popup_image').html(msg['image']);
                    wps_ubo_lite_intant_zoom_img(selected_order_bump_popup);
                    selected_order_bump_popup.find('.wps_ubo_err_waring_for_variation').html(msg['message']);

                } else if (msg['key'] == 'not_available') {

                    selected_order_bump_popup.find('.wps_ubo_err_waring_for_variation').css('display', 'flex');
                    selected_order_bump_popup.find('.wps_ubo_price_html_for_variation').css('display', 'none');
                    selected_order_bump_popup.find('.wps_ubo_bump_add_to_cart_button').css('display', 'none');

                    selected_order_bump_popup.find('.wps_bump_popup_image').html(msg['image']);
                    wps_ubo_lite_intant_zoom_img(selected_order_bump_popup);
                    selected_order_bump_popup.find('.wps_ubo_err_waring_for_variation').html(msg['message']);

                } else if (!isNaN(msg['key'])) {

                    selected_order_bump_popup.find('.wps_ubo_err_waring_for_variation').css('display', 'none');
                    selected_order_bump_popup.find('.wps_ubo_price_html_for_variation').css('display', 'block');
                    selected_order_bump_popup.find('.wps_ubo_bump_add_to_cart_button').css('display', 'flex');

                    selected_order_bump_popup.find('.wps_bump_popup_image').html(msg['image']);
                    wps_ubo_lite_intant_zoom_img(selected_order_bump_popup);
                    selected_order_bump_popup.find('.variation_id_selected').val(msg['key']);
                    selected_order_bump_popup.find('.wps_ubo_price_html_for_variation').html(msg['message']);
                }
            }
        });
    });

    /*==========================================================================
                            Variation popup add to cart
    ============================================================================*/
    /*
     * POP-UP ADD TO CART BUTTON [ works with variable products].
     * To add the selected js.
     */
    $(document).on('click', '.wps_ubo_bump_add_to_cart_button', function (e) {       //This for the add to cart on the varaition popup.
        e.preventDefault();
        order_bump_index = jQuery(this).attr('offer_bump_index');

        // Order Bump Object.
        var parent_wrapper_class = '.wps_bump_popup_' + order_bump_index;
        var popup_obj = jQuery(parent_wrapper_class);

        // Meta form exists.
        if (popup_obj.length > 0) {
            let data_arr = [];
            popup_obj.find('.wps_ubo_custom_meta_field').each(function (index) {

                let field_obj = {};
                if ('' == jQuery(this).val()) {
                    alert(jQuery(this).attr('name') + ' field cannot be empty');
                    return;
                } else if ('checkbox' == jQuery(this).attr('type')) {

                    // Push the values in an array.
                    field_obj.name = jQuery(this).attr('name');
                    field_obj.value = jQuery(this).prop('checked');
                    data_arr[index] = field_obj;
                    index++;
                } else {
                    // Push the values in an array.
                    field_obj.name = jQuery(this).attr('name');
                    field_obj.value = jQuery(this).val();

                    data_arr[index] = field_obj;

                }
            });

            // All fields are saved!
            if (data_arr.length == popup_obj.find('.wps_ubo_custom_meta_field').length) {

                // Close popup and send add to cart request.
                triggerAddOfferVariation(jQuery(this), data_arr);
            }
        } else { // Simple variable add to cart.
            triggerAddOfferVariation(jQuery(this), []);
        }
    });

    /*==========================================================================
                                Popup closing
    ============================================================================*/
    /*
     * POP-UP JS.
     * To hide on click close.
     */
    $(document).on('click', '.wps_bump_popup_close', function (e) {

        order_bump_index = $(this).attr('offer_bump_index');

        $('.wps_ubo_wrapper_index_' + order_bump_index).css('pointer-events', 'all');
        $('.wps_ubo_wrapper_index_' + order_bump_index).css('opacity', '1');
        $('body').removeClass('wps_upsell_variation_pop_up_body');
        $('.wps_bump_popup_' + order_bump_index).css('display', 'none');
        $('.wps_ubo_wrapper_index_' + order_bump_index).find('.add_offer_in_cart').prop('checked', false);
        $('.wps_bump_popup_meta_form_fields').css('display', 'none');
    });


    // Onclick outside the div close the popup.
    $('body').click(function (e) {

        if (e.target.className.search('wps_bump_popup_wrapper') == 0) {

            order_bump_index = e.target.className.replace('wps_bump_popup_wrapper wps_bump_popup_', '');

            $('.wps_ubo_wrapper_index_' + order_bump_index).css('pointer-events', 'all');
            $('.wps_ubo_wrapper_index_' + order_bump_index).css('opacity', '1');
            $('body').removeClass('wps_upsell_variation_pop_up_body');
            $('.wps_bump_popup_wrapper').hide();
            $('.wps_ubo_wrapper_index_' + order_bump_index).find('.add_offer_in_cart').prop('checked', false);
        }
    }
    );


    /*==========================================================================
                                Zooming Effect on mobile.
    ============================================================================*/
    if (wps_ubo_lite_public.mobile_view != 1) {

        // Function for zooming image( not for mobile view ).
        $(document).on('hover', '.wps_bump_popup_image', function (e) {

            // Block opening image.
            e.preventDefault();
            $('.woocommerce-product-gallery__image').zoom({
                magnify: 1.0  // Magnify upto 120 %.
            });
        });

    } else {

        $(document).on('click', '.wps_bump_popup_image', function (e) {

            // Block opening image.
            e.preventDefault();
        });
    }

    /*=====================================================================================
                                Mini Cart Update on Checkout on Adding Bump Offer.
    ======================================================================================*/
    /**
     * Update The Mini-Cart On Adding Bump Offer.
     * 
     * @param {string} wps_current_theme    Current Theme Name.
     */
    function wps_minicart_update(wps_current_theme){

        if(wps_current_theme == 'Storefront') {
            $( "#site-header-cart" ).load(window.location.href + " #site-header-cart" );//StoreFront.
            }
    }

        if ('on' == wps_ubo_lite_public.wps_popup_exit_intent) {
            $("html").bind("mouseleave", function () {
                $('.close-button').parent().show();
                wps_show_pop_up();
                $("html").unbind("mouseleave");
            });
        } else {
            $('.close-button').parent().show();
            setTimeout(function () { wps_show_pop_up(); }, 1000);
        }
        

    function wps_show_pop_up() {
         $('[popup-name="' + 'popup-1' + '"]').fadeIn(300);
         $(".fusion-header-wrapper").css("display","none");///Avada header hidden on popup.
         $('.wps-popup-content').slick({
            slidesToShow: 1,
            autoplay:false,
            autoplaySpeed:1500,
            lazyLoad: 'ondemand',
            prevArrow: '<span class="slide-arrow prev-arrow"></span>',
            nextArrow: '<span class="slide-arrow next-arrow"></span>',
            slidesToScroll:1,
            cssEase:'ease',
            useTransform:true,
            useCSS:true,
            responsive: [
                {
                  breakpoint: 768,
                  settings: {
                    arrows: false,
                  }
                }]
          });
          //Hide the header on popup open.
          if('Divi' == wps_ubo_lite_public.current_theme ){
          $("#main-header").css("display","none");
          }

          if('Avada' == wps_ubo_lite_public.current_theme ){
          $(".fusion-header-wrapper").css("display","none");
        }
    }
    
        // Open Popup  
        $(document).on('click', '.open-button', function (e) {
            var popup_name = $(this).attr('popup-open');
            $('[popup-name="' + popup_name + '"]').fadeIn(300);
            var body = document.body;
            body.classList.add("wps_body_class_popup");
            });
        
            // Close Popup. 
             $(document).on('click', '.close-button ', function (e) {
             $('.close-button').hide();
            $(".fusion-header-wrapper").css("display","block");///Avada header hidden on popup.
            //Hide the header on popup open.
             if('Divi' == wps_ubo_lite_public.current_theme ) {
            $("#main-header").css("display","none");
            }

            var popup_name = $(this).attr('popup-close');
            $('[popup-name="' + popup_name + '"]').fadeOut(300);
            var body = document.body;
            body.classList.remove("wps_body_class_popup");
            });
            
            // Close Popup When Click Outside
            $('.popup').on('click', function () {
                $(".fusion-header-wrapper").css("display", "block");
            });

            //Increase and descrease the quantity value on bump offer.
            $(document).on('click', '.wps-ubo__temp-prod-price-qty-add', function (e) {
            document.getElementById("inputtag").value++;;
            });
            $(document).on('click', '.wps-ubo__temp-prod-price-qty-sub', function (e) {
            document.getElementById("inputtag").value--;
            });
    // END OF SCRIPT.
});

// Evergreen timer Start here.
jQuery(document).ready(function($) {

    function startTimer(duration, display) {
        var timer = duration, minutes, seconds;
        setInterval(function () {
            hours = parseInt(timer / 3600, 10);
            minutes = parseInt((timer % 3600) / 60, 10);
            seconds = parseInt((timer % 3600) % 60, 10);
        
            hours = hours < 10 ? "0" + hours : hours;
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

          jQuery('#wps_hour_time_' + display).html(hours);
          jQuery('#wps_min_time_' + display).html(minutes);
          jQuery('#wps_sec_time_' + display).html(seconds);

          if (--timer < 0) {
            $("#wps_timer"+ display). css({display: "none"});
              document.getElementById("expired_message" + display).innerHTML = "EXPIRED";
              document.getElementById("wps_checkbox_offer" + display).disabled = true;
              $("#wps_button_id_" + display).hide();
              $("#expired_message" + display).parent().css("pointer-events", "none");
          }
        }, 1000);
      }
      
      setTimeout(function() {
        var columns = wps_ubo_lite_public.check_if_reload;
        var rows = wps_ubo_lite_public.evergreen_timer;
        var result = [];
        $.each( rows, function( index, value ) {
            if ( columns.indexOf( parseInt( index ) ) >= 0 ) {
              result[index] = value;
            }
        } );
        
        for (var key in result) {
            display = key ;
            var Minutes = result[key]['evegreen_counter'] * 60; //minutes to seconds.
            startTimer(Minutes, display);
        } 
      }, 1000);
});
// Evergreen timer end here.

//Script end here.