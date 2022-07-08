jQuery(document).ready(function ($) {

    if (window.location.href.indexOf('reload')==-1) {
        window.location.replace(window.location.href+'?reload');
   }

    var columns = wps_ubo_lite_public.check;
    var rows = wps_ubo_lite_public.timer;
    var result =  rows.reduce(function(result, field, index) {
  result[columns[index]] = field;
  return result;
}, {})

    var x = setInterval(function() {
    for (var key in result) {
    var deadline = new Date(result[key]).getTime();
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
    if (t < 0 ) {
        $("#wps_timer"+ key). css({display: "none"});
        document.getElementById("expired_message"+ key).innerHTML = "EXPIRED";
        document.getElementById("wps_checkbox_offer"+ key).disabled = true;
    }
        }
    }, 1000);

    function getNum(val) {
        if (isNaN(val) ) {
          return 0;
        }
        return val;
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
                $('.wps_ubo_wrapper_' + order_bump_index).css('pointer-events', 'all');
                $('.wps_ubo_wrapper_' + order_bump_index).css('opacity', '1');
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
                variation_selected = jQuery(this).val();
            }
        });

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
                $(parent_wrapper_class).css('pointer-events', 'all');
                $(parent_wrapper_class).css('opacity', '1');
                $('.wps_ubo_bump_add_to_cart_button').prop('disabled', false);

                // When Reload is required.
                if ('subs_reload' == msg) {

                    // Scroll Top and Reload.
                    $("html, body").scrollTop(300);
                    location.reload();
                }
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
            var wps_qty_variable = object.closest('.wps_upsell_offer_main_wrapper').find('.wps_bump_name').attr("data-wps_qty");
        } else if ( object.closest('.wps_upsell_offer_main_wrapper').find('.wps_quantity_input').val() != undefined && object.closest('.wps_upsell_offer_main_wrapper').find('.wps_bump_name').attr( "data-qty_allowed") == 'yes' && object.closest('.wps_upsell_offer_main_wrapper').find('.wps_bump_name').attr("data-wps_is_fixed_qty") == 'false' ) {
            var wps_qty_variable = object.closest('.wps_upsell_offer_main_wrapper').find('.wps_quantity_input').val();
        } else {
            var wps_qty_variable = 1;
        }

        $value_of_input_field_to_check = object.closest('.wps_upsell_offer_main_wrapper').find('.wps_quantity_input').val();
        $min_attr_value = object.closest('.wps_upsell_offer_main_wrapper').find('.wps_quantity_input').attr('min');
        $max_attr_value = object.closest('.wps_upsell_offer_main_wrapper').find('.wps_quantity_input').attr('max');

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

        order_bump_index = object.closest('.wps_upsell_offer_main_wrapper').find('.order_bump_index').val();
        parent_wrapper_class = '.wps_ubo_wrapper_' + order_bump_index;
        order_bump_id = object.closest('.wps_upsell_offer_main_wrapper').find('.order_bump_id').val();

        // Disable bump div.
        $('.wps_ubo_wrapper_' + order_bump_index).css('pointer-events', 'none');
        $('.wps_ubo_wrapper_' + order_bump_index).css('opacity', '0.4');

        if ($(parent_wrapper_class + ' .add_offer_in_cart').is(':checked')) {

            // Get Order Bump variation popup ready.
            handle_pre_selected_values();

            // Show loader for Variable offers.
            if ('variable' == object.closest('.wps_upsell_offer_main_wrapper').find('.offer_shown_id_type').val()) {
                $('.wps_bump_popup_loader').css('display', 'flex');
            }

            bump_id = object.closest('.wps_upsell_offer_main_wrapper').find('.offer_shown_id').val(); // offer product id.
            bump_discount = object.closest('.wps_upsell_offer_main_wrapper').find('.offer_shown_discount').val();
            bump_target_cart_key = object.closest('.wps_upsell_offer_main_wrapper').find('.target_id_cart_key').val();
            smart_offer_upgrade = object.closest('.wps_upsell_offer_main_wrapper').find('.order_bump_smo').val();

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
                        $('.wps_ubo_wrapper_' + order_bump_index).css('pointer-events', 'all');
                        $('.wps_ubo_wrapper_' + order_bump_index).css('opacity', '1');

                        // When Reload is required.
                        if ('subs_reload' == msg) {

                            // Scroll Top and Reload.
                            $("html, body").scrollTop(300);
                            location.reload();
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
                            index++;g                        } else {
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
    $(document).on('click', '.wps_ubo_bump_add_to_cart_button', function (e) {
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

    // END OF SCRIPT
});
