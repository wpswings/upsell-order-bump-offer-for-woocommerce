(function( $ ) {
	'use strict';
	$(document).ready(function(){

		var myDiv = document.getElementById("wps_ubo_lite_save_changes_bump");

		let isHidden = false; // Flag to track if the div is hidden

		// Function to check if the user has reached the bottom of the page
		function isBottomOfPage() {
		const windowHeight = window.innerHeight || document.documentElement.clientHeight;
		const documentHeight = Math.max(
			document.body.scrollHeight,
			document.body.offsetHeight,
			document.documentElement.clientHeight,
			document.documentElement.scrollHeight,
			document.documentElement.offsetHeight
		);

		const scrollPosition = window.scrollY || window.pageYOffset || document.documentElement.scrollTop;

		return documentHeight - (scrollPosition + windowHeight) < 50; // Adjust the value as needed
		}

		// Function to hide the div
		function hideDiv() {
		myDiv.style.display = "none";
		isHidden = true;
		}

		// Function to show the div
		function showDiv() {
		myDiv.style.display = "inline-flex";
		isHidden = false;
		}

		// Listen for the scroll event
		window.addEventListener("scroll", function() {
		if (isBottomOfPage() && !isHidden) {
			hideDiv();
		} else if (!isBottomOfPage() && isHidden) {
			showDiv();
		}
		});

		var wps_is_pro_active = woocommerce_admin.is_pro_active;
		if(1 == wps_is_pro_active){
			$('.wps_ubo_premium_strip').hide()
		}

		$(document).on('click','.wps_product_info',function(){
            $(this).toggleClass('accordian--active');
            $(this).next('p').slideToggle();
        })

		$(document).on('click','.wps-save-changes-ubo',function(){
		document.getElementById("wps_upsell_bump_creation_setting_save").click();
        })

		// Create new offer bump.
		$('.wps_ubo_lite_bump_create_button').on( 'click', function (e) {

			var present_offers = $('.wps_ubo_lite_saved_funnel').val();
			if( present_offers >= '1' ) {

				e.preventDefault();
				$( '.wps_ubo_lite_go_pro_popup_wrap' ).addClass( 'wps_ubo_lite_go_pro_popup_show' );
				$( 'body' ).addClass( 'wps_ubo_lite_go_pro_popup_body' );
			}
		});

		$('.wps_ubo_lite_go_pro_popup_close').on( 'click', function (e) {

			// Hide Go pro popup.
			e.preventDefault();
			$( '.wps_ubo_lite_go_pro_popup_wrap' ).removeClass('wps_ubo_lite_go_pro_popup_show' );
			$( '.wps_ubo_lite_go_pro_popup_wrap_template' ).removeClass('wps_ubo_lite_go_pro_popup_show' );
			$( 'body' ).removeClass( 'wps_ubo_lite_go_pro_popup_body' );
		});
		$('.wps_ubo_lite_skype_setting').on( 'click', function () {
			$( '#wps_ubo_lite_skype_connect_with_us' ).toggleClass('show');
		});
	    // Onclick outside the div close for Go Pro popup.
	    $('body').click
	    (
	      function(e)
	      { 
	        if( e.target.className == 'wps_ubo_lite_go_pro_popup_wrap wps_ubo_lite_go_pro_popup_show' || e.target.className == 'wps_ubo_lite_go_pro_popup_wrap_template wps_ubo_lite_go_pro_popup_show' )
	        {   
	            $( '.wps_ubo_lite_go_pro_popup_wrap' ).removeClass( 'wps_ubo_lite_go_pro_popup_show' );
				$( '.wps_ubo_lite_go_pro_popup_wrap_template' ).removeClass( 'wps_ubo_lite_go_pro_popup_show' );
	            $( 'body' ).removeClass( 'wps_ubo_lite_go_pro_popup_body' );
	        }
	      }
	    );
		
		// Sticky Offer Preview.
		$(".wps_upsell_offer_main_wrapper").stick_in_parent({offset_top: 50});

		$('.wps_ubo_colorpicker').wpColorPicker();

		$('#wps_upsell_bump_target_ids').select2();

		$( '.wps_ubo_template').val( 0 );

		// Reflect the saved in Template selection settings.
		if( $('#wps_bump_template_type_select').val() == 'Yes' ) {

			$(".wps_upsell_template_div").css('display','block');
			$(".wps_upsell_custom_template_settings").css('display','none');

		} else {

			$(".wps_upsell_custom_template_settings").css('display','block');
			$(".wps_upsell_template_div").css('display','none');
		}

		// Reflect the change in Template selection.
		$('#wps_bump_template_type_select').on('change', function () {
			var v = $(this).val();

			if( v == 'Yes' ) {
				$(".wps_upsell_template_div").css('display','block');
				$(".wps_upsell_custom_template_settings").css('display','none');

			} else {

				$(".wps_upsell_custom_template_settings").css('display','block');
				$(".wps_upsell_template_div").css('display','none');
			}	
		});

		// Reflect the change in horizontal_slider.
		$('.wps_ubo_bottom_vertical_spacing_slider').on('change', function () {
		    var v = $(this).val();
		    $('.wps_ubo_bottom_spacing_slider_size').html( v + 'px' );
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
		if( typeof wps_ubo_lite_offer_section_obj !== 'undefined' ) {

			$('html, body').animate({
			    scrollTop: $('[data-scroll-id="#' + wps_ubo_lite_offer_section_obj.value.value + '"]').offset().top - 50
			}, 'slow');

			// After scrolling remove offer section parameter from url.
			var after_scroll_href = window.location.href;

			if ( after_scroll_href.indexOf( '&wps-bump-offer-section=' ) >= 0 ) {

				var after_scroll_newUrl = after_scroll_href.substring( 0, after_scroll_href.indexOf( '&wps-bump-offer-section=' ) );

	   			window.history.replaceState( {}, '', after_scroll_newUrl );

			}
		}

		// Scroll to respective Template section after clicking Yes for Template change.
		if( typeof wps_ubo_lite_template_section_obj !== 'undefined' ) {

			$('html, body').animate({
			    scrollTop: $('.wps_ubo_template_link[data_link="' + wps_ubo_lite_template_section_obj.value.value + '"]').offset().top - 500
			}, 'slow');

			// After scrolling remove offer section parameter from url.
			var after_scroll_href = window.location.href;

			if ( after_scroll_href.indexOf( '&wps-bump-template-section=' ) >= 0 ) {

				var after_scroll_newUrl = after_scroll_href.substring( 0, after_scroll_href.indexOf( '&wps-bump-template-section=' ) );

	   			window.history.replaceState( {}, '', after_scroll_newUrl );

			}
		}

		// Appearance Section JS - start.
		$('.wps-ubo-appearance-template').on( 'click', function(e) {

			e.preventDefault();

			$(this).addClass( 'nav-tab-active' );
			$('.wps-ubo-appearance-design').removeClass( 'nav-tab-active' );
			$('.wps-ubo-appearance-text').removeClass( 'nav-tab-active' );

			$('.wps-ubo-template-section').removeClass( 'wps-ubo-appearance-section-hidden' );
			$('.wps_upsell_table_column_wrapper').addClass( 'wps-ubo-appearance-section-hidden' );
			$('.wps-ubo-text-section').addClass( 'wps-ubo-appearance-section-hidden' );
		});

		$('.wps-ubo-appearance-design').on( 'click', function(e) {

			e.preventDefault();

			$(this).addClass( 'nav-tab-active' );
			$('.wps-ubo-appearance-text').removeClass( 'nav-tab-active' );
			$('.wps-ubo-appearance-template').removeClass( 'nav-tab-active' );

			$('.wps-ubo-template-section').addClass( 'wps-ubo-appearance-section-hidden' );
			$('.wps_upsell_table_column_wrapper').removeClass( 'wps-ubo-appearance-section-hidden' );
			$('.wps-ubo-text-section').addClass( 'wps-ubo-appearance-section-hidden' );
		});

		$('.wps-ubo-appearance-text').on( 'click', function(e) {

			e.preventDefault();

			$(this).addClass( 'nav-tab-active' );
			$('.wps-ubo-appearance-design').removeClass( 'nav-tab-active' );
			$('.wps-ubo-appearance-template').removeClass( 'nav-tab-active' );

			$('.wps-ubo-template-section').addClass( 'wps-ubo-appearance-section-hidden' );
			$('.wps_upsell_table_column_wrapper').addClass( 'wps-ubo-appearance-section-hidden' );
			$('.wps-ubo-text-section').removeClass( 'wps-ubo-appearance-section-hidden' );
		});
		// Appearance Section JS - End.

		// Available Template preview - Start.

		var temp_id;

		$('.wps_ubo_template_link').on( 'click', function(e) {

			e.preventDefault();
			temp_id = $(this).attr('data_link' );
			$('.wps_ubo_skin_popup_wrapper').css( 'display', 'flex' );

		});

		// On yes, reset the css
		$('.wps_ubo_template_layout_yes').on( 'click', function(e) { //Template chnage css and design. 

			e.preventDefault();
			$( '.wps_ubo_template').val( temp_id );   // Select temp id
			$( '.wps_ubo_selected_template').val( temp_id );   // Select temp id
			$( '.wps_ubo_animation_loader' ).css('display', 'flex'); // Loader

			// For Scroll back.
			var href_bump_current_url = window.location.href;
			href_bump_current_url += '&wps-bump-template-section=' + temp_id; 
			window.history.replaceState( {}, '', href_bump_current_url );

			$( '#wps_upsell_bump_creation_setting_save' ).click(); // Save bump
			$('.wps_ubo_skin_popup_wrapper').css( 'display', 'none' );



		});

		// On No, do nothing.
		$('.wps_ubo_template_layout_no').on( 'click', function(e) {

			e.preventDefault();
			$('.wps_ubo_skin_popup_wrapper').css( 'display', 'none' );

		});

		// Onclick outside the popup close the popup.
		$('body').click
		(
		  function(e)
		  {
		    if( e.target.className == 'wps_ubo_skin_popup_wrapper' )
		    {
		        $('.wps_ubo_skin_popup_wrapper').hide();
		    }
		  }
		);

		// Available Template preview - Ends.

		// Text Fields Preview.

		$(".wps_upsell_offer_input_type").on("change paste keyup", function() {
            // console.log('typingg......');
			var text_id = $(this).attr('text_id');
			var msg = '';			// Check which field in changed
			var price = $('.offer_shown_discount').val().split("+");

			if( text_id == 'fixed' ) {			// Fixed Price Text.

				if( price[1] == 'fixed' ) {

					var fixed = '$'+price[0];
					var string = $(this).val();
					if( price[0] == 0 ){

						fixed = '$'+$('.bump_price_at_zero').val();
					}

					msg = string.replace( "{dc_price}", fixed );
					$(".wps_upsell_offer_discount_section h3").html( msg );
					// $(".wps-ubo__temp-head h3").html( msg );
					$(".wps-ubo__temp-prod-offer").html( msg );
				}
			}

			if( text_id == 'percent' ) {		// % Price Text.

				if( price[1] == '%' ) {		

					var percent = price[0]+'%';
					var string = $(this).val();
					msg = string.replace( "{dc_%}", percent );
					$(".wps_upsell_offer_discount_section h3").html( msg );
					// $(".wps-ubo__temp-head h3").html( msg );
					$(".wps-ubo__temp-prod-offer").html( msg );
				}		
			}

			if( text_id == 'lead' ) {			// Lead Title Text.

				msg = $(this).val();
				$( ".wps_upsell_offer_primary_section h5" ).html( msg );
				$(".wps-ubo__temp-head h3").html( msg );	
			} 

		});

		// Textarea Fields Preview.
		$(".wps_textarea_class").on("change paste keyup", function() {

			var text_id = $(this).attr('text_id');
			var msg = "";

			if( text_id == 'off_desc' ) {			// Offer Description.

				msg = $(this).val();
				$(".wps_upsell_offer_secondary_section").show();
				$(".wps_upsell_offer_secondary_section p").html( msg );
				$(".wps-ubo__temp-desc").html(msg);
			}

			if( text_id == 'pro_desc' ) {			// Product Description.

				msg = $(this).val();
				$(".wps_upsell_offer_product_description").html( msg );
				$(".wps-ubo__temp-prod-desc").html(msg);
			}

		});

		// Live Preview JS start.

		// Border Styling.
		var BumpOfferBox = $('.wps_upsell_offer_parent_wrapper');
		var border_type = '';
		var border_color = '';
		var border_size = '';
		var background_color = '';

		$('.wps_ubo_preview_select_border_type').on('change', function () {

			wps_ubo_apply_border_styling();
			
		});

		var Bordercolorpicker = $('.wps_ubo_preview_select_border_color');

		Bordercolorpicker.wpColorPicker({
            change: (event, ui) => {

            	border_color = ui.color.toString();

            	wps_ubo_apply_border_styling( border_color );

            }
        });

		// Background color.
		var Backgroundcolorpicker = $('.wps_ubo_preview_select_background_color');

		Backgroundcolorpicker.wpColorPicker({
            change: (event, ui) => {

            	background_color = ui.color.toString();

            	wps_ubo_apply_background_color_styling( background_color );

            }
        });

        // Vertical spacing stylings.
        var BumpOfferBoxMain = $('.wps_upsell_offer_main_wrapper');
        var VerticalSpacingTop = '';
        var VerticalSpacingBottom = '';

        $('.wps_ubo_top_vertical_spacing_slider').on('change', function () {

		    VerticalSpacingTop = $(this).val();
		    BumpOfferBoxMain.css( 'padding-top', VerticalSpacingTop + 'px' );

		    $('.wps_ubo_top_spacing_slider_size').html( VerticalSpacingTop + 'px' );
		});

		$('.wps_ubo_bottom_vertical_spacing_slider').on('change', function () {

		    VerticalSpacingBottom = $(this).val();
		    BumpOfferBoxMain.css( 'padding-bottom', VerticalSpacingBottom + 'px' );

		    $('.wps_ubo_bottom_spacing_slider_size').html( VerticalSpacingBottom + 'px' );
		});


        // Discount Section stylings.
        var DiscountSection = $('.wps_upsell_offer_discount_section');
        var DiscountSectionH3 = $('.wps_upsell_offer_discount_section h3');
		var discount_bcolor = '';
		var discount_tcolor = '';
		var discount_tsize = '';

		var DiscountBcolorpicker = $('.wps_ubo_select_discount_bcolor');

		DiscountBcolorpicker.wpColorPicker({
            change: (event, ui) => {

            	discount_bcolor = ui.color.toString();

            	DiscountSection.css( 'background-color', discount_bcolor );
				$('.wps-ubo__temp-prod-offer').css( 'background-color', discount_bcolor );

            }
        });

        var DiscountTcolorpicker = $('.wps_ubo_select_discount_tcolor');

		DiscountTcolorpicker.wpColorPicker({
            change: (event, ui) => {

            	discount_tcolor = ui.color.toString();

            	DiscountSectionH3.css( 'color', discount_tcolor );
				$('.wps-ubo__temp-prod-offer').css(  'color', discount_tcolor );
				

            }
        });

        $('.wps_ubo_discount_slider').on('change', function () {

		    discount_tsize = $(this).val();
		    DiscountSectionH3.css( 'font-size', discount_tsize + 'px' );

		    $('.wps_ubo_discount_slider_size').html( discount_tsize + 'px' );
		});

        // Product Section stylings.
        var ProductSectionP = $('.wps_upsell_offer_product_section .wps_upsell_offer_product_description'); //2.1.7.
		var ProductDesSectionP = $('.upsell-product-info .upsell-product-desc p');//2.1.7.
		var ProductPriceColor = $('.wps_upsell_offer_product_price');//2.1.7.
		var ProductQty = $('.upsell-offer-template-2 .quantity')//2.1.7.
        var ProductpriceP = $('.wps_upsell_offer_product_price p');
        var Productpricedel = $('.wps_upsell_offer_product_price del');
		var Productpriceins = $('.wps_upsell_offer_product_price ins');
        var ProductSectionH4 = $('.wps_upsell_offer_product_section h4');
		var ProductSectionImg = $( '.wps_upsell_offer_img' );
		var product_tcolor = '';
		var product_tsize = '';
		var product_img_width = '';
		var product_img_height = '';

		var ProductTcolorpicker = $('.wps_ubo_select_product_tcolor');

		var ProductTcolorPricepicker = $('.wps_ubo_select_product_price_tcolor');//2.1.7.
		var ProductHeadingH4 = $('.upsell-product-info h4');//2.1.7.

		ProductTcolorpicker.wpColorPicker({
            change: (event, ui) => {

            	product_tcolor = ui.color.toString();

            	ProductSectionP.css( 'color', product_tcolor );
            	ProductSectionH4.css( 'color', product_tcolor );
            	ProductpriceP.css( 'color', product_tcolor );
				ProductDesSectionP.css( 'color', product_tcolor );//2.1.7.
				ProductHeadingH4.css( 'color', product_tcolor );//2.1.7.

				$('.wps-ubo__temp-prod-desc').css( 'color', product_tcolor );
            }
        });

		ProductTcolorPricepicker.wpColorPicker({ //2.1.7.
            change: (event, ui) => {

            	product_tcolor = ui.color.toString();
				ProductPriceColor.css( 'color', product_tcolor );
				ProductQty.css( 'color', product_tcolor );
				$('.wps-ubo__temp-prod-price-new').css( 'color', product_tcolor );
            }
        });

		$('.wps_ubo_product_price_slider').on('change', function () {     //2.1.7
			product_tsize = $(this).val();
			ProductpriceP.css('font-size', product_tsize + 'px');
		    Productpricedel.css('font-size', product_tsize + 'px');
			Productpriceins.css('font-size', product_tsize + 'px');
			ProductQty.css('font-size', product_tsize + 'px');
			$('.wps_ubo_product_price_slider_size').html( product_tsize + 'px' );//2.1.7.
		});

        $('.wps_ubo_product_slider').on('change', function () {
			product_tsize = $(this).val();
			ProductDesSectionP.css( 'font-size', product_tsize + 'px' );//2.1.7.
			ProductHeadingH4.css( 'font-size', product_tsize + 'px' );//2.1.7.
		    ProductSectionP.css('font-size', product_tsize + 'px');

		    $('.wps_ubo_product_slider_size').html( product_tsize + 'px' );
			product_tsize = parseInt( product_tsize ) + 10;
		    ProductSectionH4.css('font-size', product_tsize + 'px');
		});

		$('.wps_ubo_product_price_slider').on('change', function () { //2.1.7.
			product_tsize = $(this).val();
			$('.wps_ubo_product_price_slider_size').html( product_tsize + 'px' );
		});

		$( '.wps_ubo_product_img_height_slider' ).on( 'change', function() {
			product_img_height = $(this).val();
			ProductSectionImg.css( 'height', product_img_height + 'px' );

			$( '.wps_ubo_product_slider_height' ).html( product_img_height + 'px' );
		});

		$( '.wps_ubo_product_img_width_slider' ).on( 'change', function() {
			product_img_width = $(this).val();
			ProductSectionImg.css( 'width', product_img_width + 'px' );

			$( '.wps_ubo_product_slider_width' ).html( product_img_width + 'px' );
		});

		// Accept Offer Section stylings.
		var AcceptOfferSection = $('.wps_upsell_offer_primary_section');
        var AcceptOfferSectionH5 = $('.wps_upsell_offer_primary_section h5');
        var AcceptOfferSectionA = $('.wps_upsell_offer_primary_section svg');
		var AcceptOffer_tcolor = '';
		var AcceptOffer_tsize = '';
		var AcceptOffer_bcolor = '';

		var AcceptOfferBcolorpicker = $('.wps_ubo_select_accept_offer_bcolor');
		var AcceptOfferTcolorpicker = $('.wps_ubo_select_accept_offer_tcolor');
		var AcceptOfferAcolorpicker = $('.wps_ubo_select_accept_offer_acolor');

		AcceptOfferBcolorpicker.wpColorPicker({
            change: (event, ui) => {

            	AcceptOffer_bcolor = ui.color.toString();

            	AcceptOfferSection.css( 'background-color', AcceptOffer_bcolor );
				$('.wps-ubo__temp-title').css( 'background-color', AcceptOffer_bcolor );

            }
        });

        AcceptOfferTcolorpicker.wpColorPicker({
            change: (event, ui) => {

            	AcceptOffer_tcolor = ui.color.toString();

            	AcceptOfferSectionH5.css( 'color', AcceptOffer_tcolor );
				$('.wps-ubo__temp-title').css( 'color', AcceptOffer_tcolor);
            }
        });

		AcceptOfferAcolorpicker.wpColorPicker({
            change: (event, ui) => {

            	AcceptOffer_tcolor = ui.color.toString();

            	AcceptOfferSectionA.css( 'fill', AcceptOffer_tcolor );

            }
        });

        $('.wps_ubo_accept_offer_slider').on('change', function () {

		    AcceptOffer_tsize = $(this).val();
		    AcceptOfferSectionH5.css( 'font-size', AcceptOffer_tsize + 'px' );
			$('.wps-ubo__temp-title').css('font-size', AcceptOffer_tsize + 'px');
		    $('.wps_ubo_accept_offer_slider_size').html( AcceptOffer_tsize + 'px' );
		});

		// Offer Description Section stylings.
		var OfferDescriptionSection = $('.wps_upsell_offer_secondary_section');
        var OfferDescriptionSectionP = $('.wps_upsell_offer_secondary_section p');
		var OfferDescription_tcolor = '';
		var OfferDescription_tsize = '';
		var OfferDescription_bcolor = '';

		var OfferDescriptionBcolorpicker = $('.wps_ubo_select_offer_description_bcolor');
		var OfferDescriptionTcolorpicker = $('.wps_ubo_select_offer_description_tcolor');

		OfferDescriptionBcolorpicker.wpColorPicker({
            change: (event, ui) => {

            	OfferDescription_bcolor = ui.color.toString();

            	OfferDescriptionSection.css( 'background-color', OfferDescription_bcolor );
				$('.wps-ubo__temp-desc').css( 'background-color', OfferDescription_bcolor );

            }
        });

		OfferDescriptionTcolorpicker.wpColorPicker({
            change: (event, ui) => {

            	OfferDescription_tcolor = ui.color.toString();

            	OfferDescriptionSectionP.css( 'color', OfferDescription_tcolor );

				$('.wps-ubo__temp-desc').css(  'color', OfferDescription_tcolor );

            }
        });

        $('.wps_ubo_offer_description_slider').on('change', function () {

		    OfferDescription_tsize = $(this).val();
		    OfferDescriptionSectionP.css( 'font-size', OfferDescription_tsize + 'px' );

			$('.wps-ubo__temp-desc').css(  'font-size', OfferDescription_tsize + 'px');

		    $('.wps_ubo_offer_description_slider_size').html( OfferDescription_tsize + 'px' );
		});

		/* Local defined functions */

		// Apply Border stylings.
		function wps_ubo_apply_border_styling( border_color = '' ) {

			border_type = $('.wps_ubo_preview_select_border_type').val();

			if( border_color == '' ) {

				border_color = $('.wps_ubo_preview_select_border_color').val();
			}
			

			if( 'double' == border_type ) {

				border_size = '4px';
			}

			else {

				border_size = '2px';
			}

			BumpOfferBox.css( 'border', border_type + ' ' + border_color + ' ' + border_size );
		}

		// Apply Background color stylings.
		function wps_ubo_apply_background_color_styling( background_color = '' ) {

			background_color = $('.wps_ubo_preview_select_background_color').val();


			$('.wps_upsell_offer_wrapper').css( 'background-color', background_color );
		}

		// Live Preview JS end.
		
	});

})( jQuery );

// Basic JS.
jQuery(document).ready( function($) {

	// Reflect bump name input value.
	$("#wps_upsell_bump_name").on("change paste keyup", function() {
	   
	    $("#wps_upsell_bump_name_heading h2").text( $(this).val() );
	});
	
	// Bump status Live <->  Sandbox.
	$('#wps_upsell_bump_status_input').click( function() {

	    if( true === this.checked ) {

	    	$('.wps_upsell_bump_status_on').addClass('active');
			$('.wps_upsell_bump_status_off').removeClass('active');
	    }

	    else {

	    	$('.wps_upsell_bump_status_on').removeClass('active');
			$('.wps_upsell_bump_status_off').addClass('active');
	    }
	});

	// Save Bump Offer product as soon as Offer product is added.
	$( ".wps_upsell_offer_product" ).on("change", function() {

		// Show loading icon.
		$( '.wps_ubo_animation_loader' ).css("display", "flex");

		/**
		 * Append offer section parameter in current url, so after form submit we can scroll back to
		 * the current respective offer section.
		 */
		var href_bump_current_url = window.location.href;
		href_bump_current_url += '&wps-bump-offer-section=offer-section-1';
		window.history.replaceState( {}, '', href_bump_current_url );

		// Click button to save the bump.
	    $("#wps_upsell_bump_creation_setting_save").click();
	});

	// Reflect bump name input value.
	$(".wps_upsell_offer_input_type").on("change paste keyup", function() {

	    if( '%' == $("#wps_upsell_offer_price_type_id").val() ) {

	    	if( $(this).val() > 100 ) {

	    		$(this).val( 100 );
	    	}
	    }
	});

	/**
	 * Scripts after v1.0.2
	 */
	$('#wps_ubo_offer_purchased_earlier,#wps_ubo_offer_timer,#wps_ubo_offer_product_image_slider, #wps_ubo_offer_replace_target, #wps_ubo_offer_global_funnel, #wps_ubo_offer_exclusive_limit, #wps_ubo_offer_meta_forms, #wps_enable_red_arrow_feature,.wps_bump_offer_popup_case ,#wps_ubo_offer_restrict_coupons, #wps_upsell_bump_priority, #wps_upsell_bump_min_cart,#wps_ubo_img_width_slider_pop_up,#wps_ubo_img_height_slider_pop_up,#wps_ubo_select_accept_offer_acolor_pop_up').on( 'click', function (e) {

		// Add popup to unlock pro features.
		var pro_status = document.getElementById( 'wps_ubo_pro_status' );
		if( null != pro_status ) {
			
			// Add a popup over here.
			$(this).prop("checked", false);
			$( '.wps_ubo_lite_go_pro_popup_wrap' ).addClass( 'wps_ubo_lite_go_pro_popup_show' );
			$( 'body' ).addClass( 'wps_ubo_lite_go_pro_popup_body' );
		}
	});

	$('#wps_ubo_premium_popup_4_template,#wps_ubo_premium_popup_3_template,#wps_ubo_premium_popup_5_template,#wps_ubo_premium_popup_6_template,#wps_ubo_premium_popup_7_template,#wps_ubo_premium_popup_8_template,#wps_ubo_premium_popup_9_template').on( 'click', function (e) {

		// Add popup to unlock pro features.
		var pro_status = document.getElementById( 'wps_ubo_pro_status' );
		if( null != pro_status ) {
			
			// Add a popup over here.
			$(this).prop("checked", false);
			$( '.wps_ubo_lite_go_pro_popup_wrap_template' ).addClass( 'wps_ubo_lite_go_pro_popup_show' );
			$( 'body' ).addClass( 'wps_ubo_lite_go_pro_popup_body' );
		}
	});

	// If org is updated but pro is not.
	jQuery( '#wps_ubo_offer_purchased_earlier' ).on( 'click',function(e){

		var is_update_needed = jQuery( '#is_pro_update_needed' ).val();

		if( 'true' == is_update_needed ) {

			jQuery(this).prop( 'checked', false );
			jQuery('.wps_ubo_update_popup_wrapper').addClass( 'wps_ubo_lite_update_popup_show' );
			jQuery('body').addClass( 'wps_ubo_lite_go_pro_popup_body' );
		}
	});

	// If org is updated but pro is not.
	jQuery( '#wps_ubo_offer_exclusive_limit' ).on( 'change',function(e){

		var show_limit = jQuery( this ).prop( 'checked' );

		if( true == show_limit ) {
			jQuery( '.wps-ubo-offer-exclusive-limit-wrap' ).removeClass( 'keep-hidden' );
		} else {
			jQuery( '.wps-ubo-offer-exclusive-limit-wrap' ).addClass( 'keep-hidden' );
		}
	});
	
	// Onclick outside the div close for Update popup.
	jQuery('body').click( function(e) {

		if( e.target.className == 'wps_ubo_update_popup_wrapper wps_ubo_lite_update_popup_show' ) {

			jQuery( '.wps_ubo_update_popup_wrapper' ).removeClass( 'wps_ubo_lite_update_popup_show' );
			jQuery( 'body' ).removeClass( 'wps_ubo_lite_go_pro_popup_body' );
		}
	});

	// Close popup on clicking buttons.
	jQuery('.wps_ubo_update_yes, .wps_ubo_update_no').click( function(e) {

		jQuery( '.wps_ubo_update_popup_wrapper' ).removeClass( 'wps_ubo_lite_update_popup_show' );
		jQuery( 'body' ).removeClass( 'wps_ubo_lite_go_pro_popup_body' );

	});

	// If org is new and pro is old. Then folder will be unavailable at that time.
	var new_attr = '';
	var template_data_link = '';
	var template_data_link_id = '';


	wps_ubo_change_template_img_src( '1' );
	wps_ubo_change_template_img_src( '2' );
	wps_ubo_change_template_img_src( '3' );

	// For update popup at creation file.
	jQuery( '.wps_ubo_skin_popup_head img' ).attr( 'src',function( index,attr ) {

		new_attr = attr.replace( 'Icons','icons' );
		return new_attr;
	});

	function wps_ubo_change_template_img_src( template_data_link_id = false ) {

		if( false == template_data_link_id ) {

			return;
		}

		template_data_link = $("a.wps_ubo_template_link[data_link=" + template_data_link_id + "] img");

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
	exisiting_text = $(".wps_upsell_offer_secondary_section").text();

	if( '' == exisiting_text ) {

		// Hide if no content is present.
		$(".wps_upsell_offer_secondary_section").hide();
	}

	/**
	 * Custom Image setup.
	 * Wordpress image upload.
	 */
	 jQuery(function($){
		/*
		 * Select/Upload image(s) event.
		 */
		jQuery('body').on('click', '.wps_ubo_upload_image_button', function(e){

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
		jQuery('body').on('click', '.wps_ubo_remove_image_button', function(e){
			e.preventDefault();
			jQuery(this).hide().prev().val('').prev().addClass('button').html('Upload image');
			return false;
		});
	});

	var pro_status = document.getElementById( 'wps_ubo_pro_status' );
	if (null != pro_status) {
		document.getElementById("wps_Offer_Without_Pop_Up_id_org_2").checked = true;
		document.getElementById("wps_Offer_With_Pop_Up_id_org_2").checked = false;
	}

	var wps_selected_template_id = $('#wps_templete_select_id').val();
	if(6 == wps_selected_template_id || (7 == wps_selected_template_id) || (8 == wps_selected_template_id) || (9 == wps_selected_template_id)){
		$('.wps_new_template_pro').hide();
	}
	if(6 == wps_selected_template_id){
		$('#wps_upsell_offer_main_id_').addClass('wps-hybrid');
		$('#wps_upsell_offer_main_id_').removeClass('wps-horizontal-ltr');
		$('#wps_upsell_offer_main_id_').removeClass('wps-horizontal-rtl');
		$('#wps_upsell_offer_main_id_').removeClass('wps-verticle');
	} if(7 == wps_selected_template_id) {
		$('#wps_upsell_offer_main_id_').removeClass('hybrid');
		$('#wps_upsell_offer_main_id_').addClass('wps-horizontal-ltr');
		$('#wps_upsell_offer_main_id_').removeClass('wps-horizontal-rtl');
		$('#wps_upsell_offer_main_id_').removeClass('wps-verticle');	
	} if(8 == wps_selected_template_id) {
		$('#wps_upsell_offer_main_id_').removeClass('hybrid');
		$('#wps_upsell_offer_main_id_').removeClass('wps-horizontal-ltr');
		$('#wps_upsell_offer_main_id_').addClass('wps-horizontal-rtl');
		$('#wps_upsell_offer_main_id_').removeClass('wps-verticle');	
	} if(9 == wps_selected_template_id) {
		$('#wps_upsell_offer_main_id_').removeClass('hybrid');
		$('#wps_upsell_offer_main_id_').removeClass('wps-horizontal-ltr');
		$('#wps_upsell_offer_main_id_').removeClass('wps-horizontal-rtl');
		$('#wps_upsell_offer_main_id_').addClass('wps-verticle');	
	}
	$(".wps_hide_checkbox").hide();
// End of js.
});
