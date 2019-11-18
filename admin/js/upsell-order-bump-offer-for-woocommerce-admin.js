(function( $ ) {
	'use strict';
	$(document).ready(function(){

		// Create new offer bump.
		$('.mwb_ubo_lite_bump_create_button').on( 'click', function (e) {

			var present_offers = $('.mwb_ubo_lite_saved_funnel').val();
			if( present_offers >= '1' ) {

				e.preventDefault();
				$( '.mwb_ubo_lite_go_pro_popup_wrap' ).addClass( 'mwb_ubo_lite_go_pro_popup_show' );
				$( 'body' ).addClass( 'mwb_ubo_lite_go_pro_popup_body' );
			}
		});

		$('.mwb_ubo_lite_go_pro_popup_close').on( 'click', function (e) {

			// Hide Go pro popup.
			e.preventDefault();
			$( '.mwb_ubo_lite_go_pro_popup_wrap' ).removeClass('mwb_ubo_lite_go_pro_popup_show' );
			$( 'body' ).removeClass( 'mwb_ubo_lite_go_pro_popup_body' );
		});
		$('.mwb_ubo_lite_skype_setting').on( 'click', function () {
			$( '#mwb_ubo_lite_skype_connect_with_us' ).toggleClass('show');
		});
	    // Onclick outside the div close for Go Pro popup.
	    $('body').click
	    (
	      function(e)
	      { 
	        if( e.target.className == 'mwb_ubo_lite_go_pro_popup_wrap mwb_ubo_lite_go_pro_popup_show' )
	        {   
	            $( '.mwb_ubo_lite_go_pro_popup_wrap' ).removeClass( 'mwb_ubo_lite_go_pro_popup_show' );
	            $( 'body' ).removeClass( 'mwb_ubo_lite_go_pro_popup_body' );
	        }
	      }
	    );
		
		// Sticky Offer Preview.
		$(".mwb_upsell_offer_main_wrapper").stick_in_parent({offset_top: 50});

		$('.mwb_ubo_colorpicker').wpColorPicker();

		$('#mwb_upsell_bump_target_ids').select2();

		$( '.mwb_ubo_template').val( 0 );

		// Reflect the saved in Template selection settings.
		if( $('#mwb_bump_template_type_select').val() == 'Yes' ) {

			$(".mwb_upsell_template_div").css('display','block');
			$(".mwb_upsell_custom_template_settings").css('display','none');

		} else {

			$(".mwb_upsell_custom_template_settings").css('display','block');
			$(".mwb_upsell_template_div").css('display','none');
		}

		// Reflect the change in Template selection.
		$('#mwb_bump_template_type_select').on('change', function () {
			var v = $(this).val();

			if( v == 'Yes' ) {
				$(".mwb_upsell_template_div").css('display','block');
				$(".mwb_upsell_custom_template_settings").css('display','none');

			} else {

				$(".mwb_upsell_custom_template_settings").css('display','block');
				$(".mwb_upsell_template_div").css('display','none');
			}	
		});

		// Reflect the change in horizontal_slider.
		$('.mwb_ubo_bottom_vertical_spacing_slider').on('change', function () {
		    var v = $(this).val();
		    $('.mwb_ubo_bottom_spacing_slider_size').html( v + 'px' );
		});

		// Reflect the change in font.
		$('#primary_section_slider').on('change', function () {
		    var v = $(this).val();
		    $('#primary_section_slider_html').css('font-size', v + 'px')
		});

		// Reflect the change in font.
		$('#secondary_section_slider').on('change', function () {
		    var v = $(this).val();
		    $('#secondary_section_slider_html').css('font-size', v + 'px')
		});

		// Scroll to bump offer section after Saving product.
		if( typeof offer_section_obj !== 'undefined' ) {

			$('html, body').animate({
			    scrollTop: $('[data-scroll-id="#' + offer_section_obj.value + '"]').offset().top - 50
			}, 'slow');

			// After scrolling remove offer section parameter from url.
			var after_scroll_href = window.location.href;

			if ( after_scroll_href.indexOf( '&mwb-bump-offer-section=' ) >= 0 ) {

				var after_scroll_newUrl = after_scroll_href.substring( 0, after_scroll_href.indexOf( '&mwb-bump-offer-section=' ) );

	   			window.history.replaceState( {}, '', after_scroll_newUrl );

			}
		}

		// Scroll to respective Template section after clicking Yes for Template change.
		if( typeof template_section_obj !== 'undefined' ) {

			$('html, body').animate({
			    scrollTop: $('.mwb_ubo_template_link[data_link="' + template_section_obj.value + '"]').offset().top - 500
			}, 'slow');

			// After scrolling remove offer section parameter from url.
			var after_scroll_href = window.location.href;

			if ( after_scroll_href.indexOf( '&mwb-bump-template-section=' ) >= 0 ) {

				var after_scroll_newUrl = after_scroll_href.substring( 0, after_scroll_href.indexOf( '&mwb-bump-template-section=' ) );

	   			window.history.replaceState( {}, '', after_scroll_newUrl );

			}
		}

		// Appearance Section JS - start.
		$('.mwb-ubo-appearance-template').on( 'click', function(e) {

			e.preventDefault();

			$(this).addClass( 'nav-tab-active' );
			$('.mwb-ubo-appearance-design').removeClass( 'nav-tab-active' );
			$('.mwb-ubo-appearance-text').removeClass( 'nav-tab-active' );

			$('.mwb-ubo-template-section').removeClass( 'mwb-ubo-appearance-section-hidden' );
			$('.mwb_upsell_table_column_wrapper').addClass( 'mwb-ubo-appearance-section-hidden' );
			$('.mwb-ubo-text-section').addClass( 'mwb-ubo-appearance-section-hidden' );
		});

		$('.mwb-ubo-appearance-design').on( 'click', function(e) {

			e.preventDefault();

			$(this).addClass( 'nav-tab-active' );
			$('.mwb-ubo-appearance-text').removeClass( 'nav-tab-active' );
			$('.mwb-ubo-appearance-template').removeClass( 'nav-tab-active' );

			$('.mwb-ubo-template-section').addClass( 'mwb-ubo-appearance-section-hidden' );
			$('.mwb_upsell_table_column_wrapper').removeClass( 'mwb-ubo-appearance-section-hidden' );
			$('.mwb-ubo-text-section').addClass( 'mwb-ubo-appearance-section-hidden' );
		});

		$('.mwb-ubo-appearance-text').on( 'click', function(e) {

			e.preventDefault();

			$(this).addClass( 'nav-tab-active' );
			$('.mwb-ubo-appearance-design').removeClass( 'nav-tab-active' );
			$('.mwb-ubo-appearance-template').removeClass( 'nav-tab-active' );

			$('.mwb-ubo-template-section').addClass( 'mwb-ubo-appearance-section-hidden' );
			$('.mwb_upsell_table_column_wrapper').addClass( 'mwb-ubo-appearance-section-hidden' );
			$('.mwb-ubo-text-section').removeClass( 'mwb-ubo-appearance-section-hidden' );
		});
		// Appearance Section JS - End.

		// Available Template preview - Start.

		var temp_id;

		$('.mwb_ubo_template_link').on( 'click', function(e) {

			e.preventDefault();
			temp_id = $(this).attr('data_link' );
			$('.mwb_ubo_skin_popup_wrapper').css( 'display', 'flex' );

		});

		// On yes, reset the css
		$('.mwb_ubo_template_layout_yes').on( 'click', function(e) {

			e.preventDefault();
			$( '.mwb_ubo_template').val( temp_id );   // Select temp id
			$( '.mwb_ubo_selected_template').val( temp_id );   // Select temp id
			$( '.mwb_ubo_animation_loader' ).css('display', 'flex'); // Loader

			// For Scroll back.
			var href_bump_current_url = window.location.href;
			href_bump_current_url += '&mwb-bump-template-section=' + temp_id; 
			window.history.replaceState( {}, '', href_bump_current_url );

			$( '#mwb_upsell_bump_creation_setting_save' ).click(); // Save bump
			$('.mwb_ubo_skin_popup_wrapper').css( 'display', 'none' );



		});

		// On No, do nothing.
		$('.mwb_ubo_template_layout_no').on( 'click', function(e) {

			e.preventDefault();
			$('.mwb_ubo_skin_popup_wrapper').css( 'display', 'none' );

		});

		// Onclick outside the popup close the popup.
		$('body').click
		(
		  function(e)
		  {
		    if( e.target.className == 'mwb_ubo_skin_popup_wrapper' )
		    {
		        $('.mwb_ubo_skin_popup_wrapper').hide();
		    }
		  }
		);

		// Available Template preview - Ends.

		// Text Fields Preview.

		$(".mwb_upsell_offer_input_type").on("change paste keyup", function() {

			var text_id = $(this).attr('text_id');
			var msg = '';			// Check which field in changed
			var price = $('#offer_shown_discount').val().split("+");

			if( text_id == 'fixed' ) {			// Fixed Price Text.

				if( price[1] == 'fixed' ) {

					var fixed = '$'+price[0];
					var string = $(this).val();
					if( price[0] == 0 ){

						fixed = '$'+$('#bump_price_at_zero').val();
					}

					msg = string.replace( "{dc_price}", fixed );
					$(".mwb_upsell_offer_discount_section h3").html( msg );
				}
			}

			if( text_id == 'percent' ) {		// % Price Text.

				if( price[1] == '%' ) {		

					var percent = price[0]+'%';
					var string = $(this).val();
					msg = string.replace( "{dc_%}", percent );
					$(".mwb_upsell_offer_discount_section h3").html( msg );
				}		
			}

			if( text_id == 'lead' ) {			// Lead Title Text.

				msg = $(this).val();
				$( ".mwb_upsell_offer_primary_section h5" ).html( msg );	
			} 

		});

		// Textarea Fields Preview.
		$(".mwb_textarea_class").on("change paste keyup", function() {

			var text_id = $(this).attr('text_id');
			var msg = "";

			if( text_id == 'off_desc' ) {			// Offer Description.

				msg = $(this).val();
				$(".mwb_upsell_offer_secondary_section").show();
				$(".mwb_upsell_offer_secondary_section p").html( msg );
			}

			if( text_id == 'pro_desc' ) {			// Product Description.

				msg = $(this).val();
				$(".mwb_upsell_offer_product_description").html( msg );
			}

		});

		// Live Preview JS start.

		// Border Styling.
		var BumpOfferBox = $('.mwb_upsell_offer_parent_wrapper');
		var border_type = '';
		var border_color = '';
		var border_size = '';

		$('.mwb_ubo_preview_select_border_type').on('change', function () {

			mwb_ubo_apply_border_styling();
			
		});

		var Bordercolorpicker = $('.mwb_ubo_preview_select_border_color');

		Bordercolorpicker.wpColorPicker({
            change: (event, ui) => {

            	border_color = ui.color.toString();

            	mwb_ubo_apply_border_styling( border_color );

            }
        });

        // Vertical spacing stylings.
        var BumpOfferBoxMain = $('.mwb_upsell_offer_main_wrapper');
        var VerticalSpacingTop = '';
        var VerticalSpacingBottom = '';

        $('.mwb_ubo_top_vertical_spacing_slider').on('change', function () {

		    VerticalSpacingTop = $(this).val();
		    BumpOfferBoxMain.css( 'padding-top', VerticalSpacingTop + 'px' );

		    $('.mwb_ubo_top_spacing_slider_size').html( VerticalSpacingTop + 'px' );
		});

		$('.mwb_ubo_bottom_vertical_spacing_slider').on('change', function () {

		    VerticalSpacingBottom = $(this).val();
		    BumpOfferBoxMain.css( 'padding-bottom', VerticalSpacingBottom + 'px' );

		    $('.mwb_ubo_bottom_spacing_slider_size').html( VerticalSpacingBottom + 'px' );
		});


        // Discount Section stylings.
        var DiscountSection = $('.mwb_upsell_offer_discount_section');
        var DiscountSectionH3 = $('.mwb_upsell_offer_discount_section h3');
		var discount_bcolor = '';
		var discount_tcolor = '';
		var discount_tsize = '';

		var DiscountBcolorpicker = $('.mwb_ubo_select_discount_bcolor');

		DiscountBcolorpicker.wpColorPicker({
            change: (event, ui) => {

            	discount_bcolor = ui.color.toString();

            	DiscountSection.css( 'background-color', discount_bcolor );

            }
        });

        var DiscountTcolorpicker = $('.mwb_ubo_select_discount_tcolor');

		DiscountTcolorpicker.wpColorPicker({
            change: (event, ui) => {

            	discount_tcolor = ui.color.toString();

            	DiscountSectionH3.css( 'color', discount_tcolor );

            }
        });

        $('.mwb_ubo_discount_slider').on('change', function () {

		    discount_tsize = $(this).val();
		    DiscountSectionH3.css( 'font-size', discount_tsize + 'px' );

		    $('.mwb_ubo_discount_slider_size').html( discount_tsize + 'px' );
		});

        // Product Section stylings.
        var ProductSectionP = $('.mwb_upsell_offer_product_section p');
        var ProductpriceP = $('.mwb_upsell_offer_product_price p');
        var Productpricedel = $('.mwb_upsell_offer_product_price del');
        var ProductSectionH4 = $('.mwb_upsell_offer_product_section h4');
		var product_tcolor = '';
		var product_tsize = '';

		var ProductTcolorpicker = $('.mwb_ubo_select_product_tcolor');

		ProductTcolorpicker.wpColorPicker({
            change: (event, ui) => {

            	product_tcolor = ui.color.toString();

            	ProductSectionP.css( 'color', product_tcolor );
            	ProductSectionH4.css( 'color', product_tcolor );
            	ProductpriceP.css( 'color', product_tcolor );

            }
        });

        $('.mwb_ubo_product_slider').on('change', function () {

		    product_tsize = $(this).val();
		    ProductSectionP.css('font-size', product_tsize + 'px');

		    $('.mwb_ubo_product_slider_size').html( product_tsize + 'px' );
		    ProductpriceP.css('font-size', product_tsize + 'px');
		    Productpricedel.css('font-size', product_tsize + 'px');
			product_tsize = parseInt( product_tsize ) + 10;
		    ProductSectionH4.css('font-size', product_tsize + 'px');
		});

		// Accept Offer Section stylings.
		var AcceptOfferSection = $('.mwb_upsell_offer_primary_section');
        var AcceptOfferSectionH5 = $('.mwb_upsell_offer_primary_section h5');
		var AcceptOffer_tcolor = '';
		var AcceptOffer_tsize = '';
		var AcceptOffer_bcolor = '';

		var AcceptOfferBcolorpicker = $('.mwb_ubo_select_accept_offer_bcolor');
		var AcceptOfferTcolorpicker = $('.mwb_ubo_select_accept_offer_tcolor');

		AcceptOfferBcolorpicker.wpColorPicker({
            change: (event, ui) => {

            	AcceptOffer_bcolor = ui.color.toString();

            	AcceptOfferSection.css( 'background-color', AcceptOffer_bcolor );

            }
        });

        AcceptOfferTcolorpicker.wpColorPicker({
            change: (event, ui) => {

            	AcceptOffer_tcolor = ui.color.toString();

            	AcceptOfferSectionH5.css( 'color', AcceptOffer_tcolor );

            }
        });

        $('.mwb_ubo_accept_offer_slider').on('change', function () {

		    AcceptOffer_tsize = $(this).val();
		    AcceptOfferSectionH5.css( 'font-size', AcceptOffer_tsize + 'px' );

		    $('.mwb_ubo_accept_offer_slider_size').html( AcceptOffer_tsize + 'px' );
		});

		// Offer Description Section stylings.
		var OfferDescriptionSection = $('.mwb_upsell_offer_secondary_section');
        var OfferDescriptionSectionP = $('.mwb_upsell_offer_secondary_section p');
		var OfferDescription_tcolor = '';
		var OfferDescription_tsize = '';
		var OfferDescription_bcolor = '';

		var OfferDescriptionBcolorpicker = $('.mwb_ubo_select_offer_description_bcolor');
		var OfferDescriptionTcolorpicker = $('.mwb_ubo_select_offer_description_tcolor');

		OfferDescriptionBcolorpicker.wpColorPicker({
            change: (event, ui) => {

            	OfferDescription_bcolor = ui.color.toString();

            	OfferDescriptionSection.css( 'background-color', OfferDescription_bcolor );

            }
        });

		OfferDescriptionTcolorpicker.wpColorPicker({
            change: (event, ui) => {

            	OfferDescription_tcolor = ui.color.toString();

            	OfferDescriptionSectionP.css( 'color', OfferDescription_tcolor );

            }
        });

        $('.mwb_ubo_offer_description_slider').on('change', function () {

		    OfferDescription_tsize = $(this).val();
		    OfferDescriptionSectionP.css( 'font-size', OfferDescription_tsize + 'px' );

		    $('.mwb_ubo_offer_description_slider_size').html( OfferDescription_tsize + 'px' );
		});

		/* Local defined functions */

		// Apply Border stylings.
		function mwb_ubo_apply_border_styling( border_color = '' ) {

			border_type = $('.mwb_ubo_preview_select_border_type').val();

			if( border_color == '' ) {

				border_color = $('.mwb_ubo_preview_select_border_color').val();
			}
			

			if( 'double' == border_type ) {

				border_size = '4px';
			}

			else {

				border_size = '2px';
			}

			BumpOfferBox.css( 'border', border_type + ' ' + border_color + ' ' + border_size );
		}

		// Live Preview JS end.
		
	});

})( jQuery );

// Basic JS.
jQuery(document).ready( function($) {

	// Reflect bump name input value.
	$("#mwb_upsell_bump_name").on("change paste keyup", function() {
	   
	    $("#mwb_upsell_bump_name_heading h2").text( $(this).val() );
	});
	
	// Bump status Live <->  Sandbox.
	$('#mwb_upsell_bump_status_input').click( function() {

	    if( true === this.checked ) {

	    	$('.mwb_upsell_bump_status_on').addClass('active');
			$('.mwb_upsell_bump_status_off').removeClass('active');
	    }

	    else {

	    	$('.mwb_upsell_bump_status_on').removeClass('active');
			$('.mwb_upsell_bump_status_off').addClass('active');
	    }
	});

	// Save Bump Offer product as soon as Offer product is added.
	$( ".mwb_upsell_offer_product" ).on("change", function() {

		// Show loading icon.
		$( '.mwb_ubo_animation_loader' ).css("display", "flex");

		/**
		 * Append offer section parameter in current url, so after form submit we can scroll back to
		 * the current respective offer section.
		 */
		var href_bump_current_url = window.location.href;
		href_bump_current_url += '&mwb-bump-offer-section=offer-section-1';
		window.history.replaceState( {}, '', href_bump_current_url );

		// Click button to save the bump.
	    $("#mwb_upsell_bump_creation_setting_save").click();
	});

	// Reflect bump name input value.
	$(".mwb_upsell_offer_input_type").on("change paste keyup", function() {

	    if( '%' == $("#mwb_upsell_offer_price_type_id").val() ) {

	    	if( $(this).val() > 100 ) {

	    		$(this).val( 100 );
	    	}
	    }
	});

	/**
	 * Scripts after v1.0.2
	 */
	$('#mwb_ubo_offer_purchased_earlier, #mwb_ubo_offer_replace_target').on( 'click', function (e) {

		// Add popup to unlock pro features.
		var pro_status = document.getElementById( 'mwb_ubo_pro_status' );
		if( null != pro_status ) {
			
			// Add a popup over here.
			$(this).prop("checked", false);
			$( '.mwb_ubo_lite_go_pro_popup_wrap' ).addClass( 'mwb_ubo_lite_go_pro_popup_show' );
			$( 'body' ).addClass( 'mwb_ubo_lite_go_pro_popup_body' );
		}
	});

	// If org is updated but pro is not.
	jQuery( '#mwb_ubo_offer_purchased_earlier' ).on( 'click',function(e){

		var is_update_needed = jQuery( '#is_pro_update_needed' ).val();

		if( 'true' == is_update_needed ) {

			jQuery(this).prop( 'checked', false );
			jQuery('.mwb_ubo_update_popup_wrapper').addClass( 'mwb_ubo_lite_update_popup_show' );
			jQuery('body').addClass( 'mwb_ubo_lite_go_pro_popup_body' );
		}
	});

	// Onclick outside the div close for Update popup.
	jQuery('body').click( function(e) {

		if( e.target.className == 'mwb_ubo_update_popup_wrapper mwb_ubo_lite_update_popup_show' ) {

			jQuery( '.mwb_ubo_update_popup_wrapper' ).removeClass( 'mwb_ubo_lite_update_popup_show' );
			jQuery( 'body' ).removeClass( 'mwb_ubo_lite_go_pro_popup_body' );
		}
	});

	// Close popup on clicking buttons.
	jQuery('.mwb_ubo_update_yes, .mwb_ubo_update_no').click( function(e) {

		jQuery( '.mwb_ubo_update_popup_wrapper' ).removeClass( 'mwb_ubo_lite_update_popup_show' );
		jQuery( 'body' ).removeClass( 'mwb_ubo_lite_go_pro_popup_body' );

	});

	// If org is new and pro is old. Then folder will be unavailable at that time.
	var new_attr = '';
	var template_data_link = '';
	var template_data_link_id = '';


	mwb_ubo_change_template_img_src( '1' );
	mwb_ubo_change_template_img_src( '2' );
	mwb_ubo_change_template_img_src( '3' );

	// For update popup at creation file.
	jQuery( '.mwb_ubo_skin_popup_head img' ).attr( 'src',function( index,attr ) {

		new_attr = attr.replace( 'Icons','icons' );
		return new_attr;
	});

	function mwb_ubo_change_template_img_src( template_data_link_id = false ) {

		if( false == template_data_link_id ) {

			return;
		}

		template_data_link = $("a.mwb_ubo_template_link[data_link=" + template_data_link_id + "] img");

		jQuery( template_data_link ).attr( 'src',function( index,attr ) {

			if( '1' == template_data_link_id ) {

				new_attr = attr.replace( 'Offer%20templates/Template' + template_data_link_id,'offer-templates/template-' + template_data_link_id );
			}

			else {

				new_attr = attr.replace( 'Offer templates/Template' + template_data_link_id,'offer-templates/template-' + template_data_link_id );
			}

			return new_attr;
		});
	}

	// No text is present in the container.
	var exisiting_text = '';
	exisiting_text = $(".mwb_upsell_offer_secondary_section").text();

	if( '' == exisiting_text ) {

		// Hide if no content is present.
		$(".mwb_upsell_offer_secondary_section").hide();
	}

// End of js.
});
