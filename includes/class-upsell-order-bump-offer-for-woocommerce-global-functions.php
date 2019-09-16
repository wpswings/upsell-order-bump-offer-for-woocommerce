<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/admin
 */


/**
 * Bump offer template 1.
 *
 * ( Default Template ).
 *
 * @since    1.0.0
 */
function mwb_ubo_lite_offer_template_1() {

	// Template 1.
	$mwb_bump_upsell_global_css['parent_border_type'] = 'dashed'; 
	$mwb_bump_upsell_global_css['parent_border_color'] = '#000000'; 
	$mwb_bump_upsell_global_css['top_vertical_spacing'] = '10';
	$mwb_bump_upsell_global_css['bottom_vertical_spacing'] = '10';

	// DISCOUNT SECTION( discount_section ).
	$mwb_bump_upsell_global_css['discount_section_background_color'] = '#ffffff'; 
	$mwb_bump_upsell_global_css['discount_section_text_color'] = '#616060';
	$mwb_bump_upsell_global_css['discount_section_text_size'] = '35';


	// PRODUCT SECTION(product_section).
	$mwb_bump_upsell_global_css['product_section_text_color'] = '#0a0a0a';
	$mwb_bump_upsell_global_css['product_section_text_size'] = '14';

	// Accept Offer Section(primary_section).
	$mwb_bump_upsell_global_css['primary_section_background_color'] = '#73cc12'; 
	$mwb_bump_upsell_global_css['primary_section_text_color'] = '#ffffff';
	$mwb_bump_upsell_global_css['primary_section_text_size'] = '18';

	// SECONDARY SECTION(secondary_section).
	$mwb_bump_upsell_global_css['secondary_section_background_color'] = '#ffdd2f'; 
	$mwb_bump_upsell_global_css['secondary_section_text_color'] = '#292626';
	$mwb_bump_upsell_global_css['secondary_section_text_size'] = '20';

	return $mwb_bump_upsell_global_css;
}



/**
 * Bump offer template 2.
 *
 * @since    1.0.0
 */
function mwb_ubo_lite_offer_template_2() {

	// Template 2.
	$mwb_bump_upsell_global_css['parent_border_type'] = 'dashed'; 
	$mwb_bump_upsell_global_css['parent_border_color'] = '#000000'; 
	$mwb_bump_upsell_global_css['top_vertical_spacing'] = '10';
	$mwb_bump_upsell_global_css['bottom_vertical_spacing'] = '10';

	// DISCOUNT SECTION( discount_section ).
	$mwb_bump_upsell_global_css['discount_section_background_color'] = '#ffffff'; 
	$mwb_bump_upsell_global_css['discount_section_text_color'] = '#485f75';
	$mwb_bump_upsell_global_css['discount_section_text_size'] = '35';


	// PRODUCT SECTION(product_section).
	$mwb_bump_upsell_global_css['product_section_text_color'] = '#0a0a0a';
	$mwb_bump_upsell_global_css['product_section_text_size'] = '14';

	// Accept Offer Section(primary_section).
	$mwb_bump_upsell_global_css['primary_section_background_color'] = '#c1f0db'; 
	$mwb_bump_upsell_global_css['primary_section_text_color'] = '#485f75';
	$mwb_bump_upsell_global_css['primary_section_text_size'] = '18';

	// SECONDARY SECTION(secondary_section).
	$mwb_bump_upsell_global_css['secondary_section_background_color'] = '#c1f0db'; 
	$mwb_bump_upsell_global_css['secondary_section_text_color'] = '#485f75';
	$mwb_bump_upsell_global_css['secondary_section_text_size'] = '20';

	return $mwb_bump_upsell_global_css;
}



/**
 * Bump offer template 3.
 *
 * @since    1.0.0
 */
function mwb_ubo_lite_offer_template_3() {

	// Template 3.
	$mwb_bump_upsell_global_css['parent_border_type'] = 'dashed'; 
	$mwb_bump_upsell_global_css['parent_border_color'] = '#000000'; 
	$mwb_bump_upsell_global_css['top_vertical_spacing'] = '10';
	$mwb_bump_upsell_global_css['bottom_vertical_spacing'] = '10';

	// DISCOUNT SECTION( discount_section ).
	$mwb_bump_upsell_global_css['discount_section_background_color'] = '#ffffff'; 
	$mwb_bump_upsell_global_css['discount_section_text_color'] = '#584c4c';
	$mwb_bump_upsell_global_css['discount_section_text_size'] = '35';


	// PRODUCT SECTION( product_section ).
	$mwb_bump_upsell_global_css['product_section_text_color'] = '#0a0a0a';
	$mwb_bump_upsell_global_css['product_section_text_size'] = '14';

	// Accept Offer Section( primary_section ).
	$mwb_bump_upsell_global_css['primary_section_background_color'] = '#feb800'; 
	$mwb_bump_upsell_global_css['primary_section_text_color'] = '#ffffff';
	$mwb_bump_upsell_global_css['primary_section_text_size'] = '18';

	// SECONDARY SECTION( secondary_section ).
	$mwb_bump_upsell_global_css['secondary_section_background_color'] = '#feb800'; 
	$mwb_bump_upsell_global_css['secondary_section_text_color'] = '#ffffff';
	$mwb_bump_upsell_global_css['secondary_section_text_size'] = '20';

	return $mwb_bump_upsell_global_css;
}



/**
* Bump offer default global options.
*
* @since 1.0.0
*/
function mwb_ubo_lite_default_global_options() {

	$default_global_options = array(

		'mwb_bump_enable_plugin' 	=> 'on', // By default plugin will be enabled.
		'mwb_bump_skip_offer' 		=> 'yes',
		'mwb_ubo_offer_location' 	=> '_after_payment_gateways',
		'mwb_ubo_offer_removal'		=> 'yes',
		'mwb_ubo_temp_adaption'		=> 'yes',
	);

	return $default_global_options;
}

/**
 * Bump offer default text fields.
 *
 * @since    1.0.0
 */
function mwb_ubo_lite_offer_default_text() {

	$default_default_text = array(

		'mwb_ubo_discount_title_for_fixed'		=>	sprintf( '%s %s %s', esc_html__( 'AT JUST', 'upsell-order-bump-offer-for-woocommerce' ), '{dc_price}' , esc_html__( '!!', 'upsell-order-bump-offer-for-woocommerce' ) ),

		'mwb_ubo_discount_title_for_percent'	=> sprintf( '%s %s', '{dc_%}' , esc_html__( 'off only for you !!', 'upsell-order-bump-offer-for-woocommerce' ) ),

		'mwb_bump_offer_decsription_text'		=>	esc_html__( 'A unique and handy product that perfectly fits your personality.', 'upsell-order-bump-offer-for-woocommerce' ),

		'mwb_upsell_offer_title'				=>	esc_html__( 'Hurry up. Get this one time offer !!', 'upsell-order-bump-offer-for-woocommerce' ),

		'mwb_upsell_bump_offer_description'		=>	esc_html__( 'Hey fella, you can get access to the above offer by just clicking the checkbox over there. Add this offer to your order, you will never get such a discount on any other place on this site.', 'upsell-order-bump-offer-for-woocommerce' ),
	);

	return $default_default_text;
}


/**
 * Bump Offer Html.
 *
 * @since    1.0.0
 */
function mwb_ubo_lite_bump_offer_html( $bump ) {

	/**
 	 * Text fields.
 	 */
	$title = ! empty( $bump['design_text'][ 'mwb_upsell_offer_title' ] ) ? $bump['design_text'][ 'mwb_upsell_offer_title' ] : "";

	$description = $bump['design_text'][ 'mwb_upsell_bump_offer_description' ];

	$product_description_text = $bump['design_text']['mwb_bump_offer_decsription_text'];

	$discount_title_fixed = ! empty( $bump['design_text']['mwb_ubo_discount_title_for_fixed'] ) ? $bump['design_text']['mwb_ubo_discount_title_for_fixed'] : "";

	$discount_title_percent = ! empty( $bump['design_text']['mwb_ubo_discount_title_for_percent'] ) ? $bump['design_text']['mwb_ubo_discount_title_for_percent'] : "";

	if( ! empty( $bump['bump_price_html'] ) ) {
		
		$discount_title_fixed = str_replace("{dc_price}", $bump['bump_price_html'], $discount_title_fixed );
		$discount_title_percent = str_replace("{dc_%}", $bump['bump_price_html'], $discount_title_percent );
	
	}

	if( ! empty( $bump[ 'price_type' ] ) && $bump[ 'price_type' ] == 'fixed' ) {
		$bump_price_html = $discount_title_fixed;
	}
	else {
		$bump_price_html = $discount_title_percent;
	}

	?>

	<?php

	// Template adaption
	$mwb_ubo_global_options = get_option( 'mwb_ubo_global_options', mwb_ubo_lite_default_global_options() );
	$mwb_ubo_template_adaption = ! empty( $mwb_ubo_global_options[ 'mwb_ubo_temp_adaption' ] ) ? $mwb_ubo_global_options[ 'mwb_ubo_temp_adaption' ] : '';

	// PARENT WRAPPER DIV CSS( parent_wrapper_div ).
	$parent_border_type = ! empty( $bump['design_css'][ 'parent_border_type' ] ) ? $bump['design_css'][ 'parent_border_type' ] : "";
	$parent_border_color = ! empty( $bump['design_css'][ 'parent_border_color' ] ) ? $bump['design_css'][ 'parent_border_color' ] : "";
	$parent_top_vertical_spacing = ! empty( $bump['design_css'][ 'top_vertical_spacing' ] ) ? $bump['design_css'][ 'top_vertical_spacing' ] : "";
	$parent_bottom_vertical_spacing = ! empty( $bump['design_css'][ 'bottom_vertical_spacing' ] ) ? $bump['design_css'][ 'bottom_vertical_spacing' ] : "0";

	// DISCOUNT SECTION( discount_section ).
	$discount_section_background_color = ! empty( $bump['design_css'][ 'discount_section_background_color' ] ) ? $bump['design_css'][ 'discount_section_background_color' ] : ""; 
	$discount_section_text_color = ! empty( $bump['design_css'][ 'discount_section_text_color' ] ) ? $bump['design_css'][ 'discount_section_text_color' ] : "";
	$discount_section_text_size = ! empty( $bump['design_css'][ 'discount_section_text_size' ] ) ? $bump['design_css'][ 'discount_section_text_size' ] : "";

	// PRODUCT SECTION( product_section ). 
	$product_section_text_color = ! empty( $bump['design_css'][ 'product_section_text_color' ] ) ? $bump['design_css'][ 'product_section_text_color' ] : "";
	$product_section_text_size = ! empty( $bump['design_css'][ 'product_section_text_size' ] ) ? $bump['design_css'][ 'product_section_text_size' ] : "";

	// PRIMARY SECTION(primary_section ).
	$primary_section_background_color = ! empty( $bump['design_css'][ 'primary_section_background_color' ] ) ? $bump['design_css'][ 'primary_section_background_color' ] : ""; 
	$primary_section_text_color = ! empty( $bump['design_css'][ 'primary_section_text_color' ] ) ? $bump['design_css'][ 'primary_section_text_color' ] : "";
	$primary_section_text_size = ! empty( $bump['design_css'][ 'primary_section_text_size' ] ) ? $bump['design_css'][ 'primary_section_text_size' ] : "";

	// SECONDARY SECTION( secondary_section ).
	$secondary_section_background_color = ! empty( $bump['design_css'][ 'secondary_section_background_color' ] ) ? $bump['design_css'][ 'secondary_section_background_color' ] : "";
	$secondary_section_text_color = ! empty( $bump['design_css'][ 'secondary_section_text_color' ] ) ? $bump['design_css'][ 'secondary_section_text_color' ] : "";
	$secondary_section_text_size = ! empty( $bump['design_css'][ 'secondary_section_text_size' ] ) ? $bump['design_css'][ 'secondary_section_text_size' ] : "";

	?>

	<?php $parent_border_width = '2px' ; ?>
	<?php if( $parent_border_type == 'double' ) : $parent_border_width = '4px'; endif; ?>

	<!--  HTML goes down here --> 
	<style type="text/css">
		/**
		* All of the CSS for your public-facing functionality should be
		* included in this file.
		*/
		.mwb_upsell_offer_main_wrapper {
			display: block;
			width: 100%;
			padding-top:  <?php echo $parent_top_vertical_spacing."px"; ?> ;
			padding-bottom:  <?php echo $parent_bottom_vertical_spacing."px"; ?> ;
		}
		.mwb_upsell_offer_parent_wrapper {
			border: <?php echo $parent_border_type." ".$parent_border_color." ".$parent_border_width ?> ;
			<?php if( 'no' == $mwb_ubo_template_adaption ) : ?>
				max-width: 400px;
			<?php endif; ?>
			margin: 0 auto;
		}

		.mwb_upsell_offer_parent_wrapper {
			font-family: 'Source Sans Pro', sans-serif;
		}

		.mwb_upsell_offer_wrapper {
			background-color: #ffffff;
			width: 100%;
			padding: 20px;
		}

		.mwb_upsell_offer_discount_section {
			margin: 0;
			text-align: center;
			background-color: <?php echo $discount_section_background_color ?>;
			line-height: 1.68;
		}

		.mwb_upsell_offer_discount_section h3 {
			color: <?php echo $discount_section_text_color ?>;
			margin: 0px;
			padding: 1px;
			font-size: <?php echo $discount_section_text_size ?>px;
		}

		.mwb_upsell_offer_product_section {
			display: -webkit-flex;
			display: -moz-flex;
			display: -ms-flex;
			display: -o-flex;
			display: flex;
			font-size: 16px;
			align-items: start;
		}

		.mwb_upsell_offer_product_section p{
			margin: 0;
			color: <?php echo $product_section_text_color ?>;
			font-size: <?php echo $product_section_text_size ?>px;

		}

		.mwb_upsell_offer_product_content h4 {
			display: inline-block;
			vertical-align: middle;
			font-weight: 500;
		}

		.mwb_upsell_offer_product_content p {
			white-space: pre-line;
		}

		p.mwb_upsell_offer_product_price {
		    font-size: <?php echo $product_section_text_size ?>px;
		    font-weight: 700;
		}

		p.mwb_upsell_offer_product_price del{
			font-size: <?php echo $product_section_text_size ?>px;
		}

		.mwb_upsell_offer_product_section h4 {
			margin: 0;
			color: <?php echo $product_section_text_color ?>;
			font-size: <?php echo $product_section_text_size+=10 ?>px;
			font-weight: 300;
		}

		.mwb_upsell_offer_product_content {
		    width: calc(100% - 90px);
		}

		.mwb_upsell_offer_primary_section {
			align-items: center;
			background-color: <?php echo $primary_section_background_color ?>;
			display: flex;
			margin: 14px auto;
			padding: 10px;
		}

		.mwb_upsell_offer_primary_section h5 {
			color: <?php echo $primary_section_text_color ?>;
			font-size: <?php echo $primary_section_text_size ?>px;
			margin: 0 0 0 10px;
			font-weight: 600;
		}

		.mwb_upsell_offer_secondary_section {
			padding: 8px;
			background-color: <?php echo $secondary_section_background_color ?>;
			text-align: center;
			white-space: pre-line;

		}

		.mwb_upsell_offer_secondary_section p {
			color: <?php echo $secondary_section_text_color ?>;
			margin: 0;
			font-size:<?php echo $secondary_section_text_size ?>px;
			
		}

		/*
		* Css for checkbox
		*
		*/

		/* Custom checkbox container */
		.mwb_upsell_offer_primary_section .mwb_upsell_bump_checkbox_container {
			cursor: pointer;
			width: auto !important;
			font-size: 22px;
			height: 23px;
			margin-bottom: 6px;
			padding-left: 35px;
			position: relative;
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
		}
		
		/* Hide the browser's default checkbox. */
		.mwb_upsell_bump_checkbox_container input {
			position: absolute;
			opacity: 0;
			cursor: pointer;
			height: 0;
			width: 0;
		}

		/* Create a custom checkbox. */
		.checkmark {
			position: absolute;
			top: 0;
			margin: 3px;
			left: 0;
			height: 25px;
			width: 25px;
			background-color: #eeeeee;
			animation: shadow-pulse 1.5s infinite;
		}

		@keyframes shadow-pulse
		{
			0% {
				box-shadow: 0 0 0 1px #ffffff;
			}
			100% {
				box-shadow: 0 0 0 7px transparent;
			}
		}
		
		/* On mouse-over, add a grey background color. */
		.mwb_upsell_bump_checkbox_container:hover input ~ .checkmark {
			background-color: #ccc;
		}

		/* When the checkbox is checked, add a blue background. */
		.mwb_upsell_bump_checkbox_container input:checked ~ .checkmark {
			background-color: #ffffff;
		}

		/* Create the checkmark/indicator (hidden when not checked). */
		.checkmark:after {
			content: "";
			position: absolute;
			display: none;
		}

		/* Show the checkmark when checked. */
		.mwb_upsell_bump_checkbox_container input:checked ~ .checkmark:after {
			display: block;
		}

		/* Style the checkmark/indicator. */
		.mwb_upsell_bump_checkbox_container .checkmark:after {
			left: 9px;
			top: 5px;
			width: 5px;
			height: 10px;
			border: solid #333;
			border-width: 0 3px 3px 0;
			-webkit-transform: rotate(45deg);
			-ms-transform: rotate(45deg);
			transform: rotate(45deg);
		}
		.mwb_upsell_offer_image {
			width: 90px;
			margin-right: 10px;
		}
		@media screen and (max-width: 480px) {
			.mwb_upsell_offer_product_content {
				margin-left: 0;
			}
		}

	</style>

	<?php 

	// Incase no offer is added return
	$bump['id'] = ! empty( $bump['id'] ) ? sanitize_text_field( $bump['id'] ) : "";
	if( empty( $bump['id'] ) ) : return; endif;

	$bump['name'] = ! empty( $bump['name'] ) ? sanitize_text_field( $bump['name'] )  : "";

	$bump['discount_price'] = ! empty( $bump['discount_price'] ) ? sanitize_text_field( $bump['discount_price'] ) : "0";

	$image = wp_get_attachment_image_src( get_post_thumbnail_id( $bump['id'] ), 'single-post-thumbnail' )[0];

	if( empty ( $image ) ) {

		$bump_parent_id = wc_get_product( $bump['id'] )->get_parent_id();

		if( ! empty( $bump_parent_id ) ) {

			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $bump_parent_id ), 'single-post-thumbnail' )[0];
		} else {

			$image = wc_placeholder_img_src();
		}
	}

	if( ! session_id() ) {

		session_start();
	}

	$check = '';

	if( ! empty( $_SESSION[ 'bump_offer_status' ] ) && 'added' == $_SESSION[ 'bump_offer_status' ] && ! is_admin() ) {

		$check = 'checked';
	}

	/*
	* Get price html.
	*/
	$bump_offer_price = mwb_ubo_lite_custom_price_html( $bump['id'], $bump['discount_price'] );

	/**
	 * Html for bump offer.
	 */
	$bumphtml = '';
	$bumphtml .= '<input type="hidden" id ="offer_shown_id" value="'. $bump['id'].'">';
	$bumphtml .= '<input type="hidden" id ="offer_shown_discount" value="'. $bump['discount_price'] .'">';
	$bumphtml .= '<input type="hidden" id ="target_id_cart_key" value="'. $bump['target_key'] .'">';

	if( ! empty( $bump['bump_price_at_zero'] ) ) :
		$bumphtml .= '<input type="hidden" id ="bump_price_at_zero" value='.$bump['bump_price_at_zero'].'>';
	endif;
	// parent wrapper start.
	$bumphtml .= '<div class = "mwb_upsell_offer_main_wrapper" >';
	$bumphtml .= '<div class = "mwb_upsell_offer_parent_wrapper" >';

	// discount section start.
	$bumphtml .= '<div class = "mwb_upsell_offer_discount_section" >';
	$bumphtml .= '<h3><b>'.$bump_price_html.'</b></h3>';
	$bumphtml .= '</div>';
	// discount section end.

	// wrapper div start.
	$bumphtml .= '<div class = "mwb_upsell_offer_wrapper" >';

	// product section start
	$bumphtml .= '<div class = "mwb_upsell_offer_product_section" >';
	$bumphtml .= '<div class = "mwb_upsell_offer_image" >';
	$bumphtml .= '<img src="'.$image.'" style =" max-height: 120px; width: 90px; max-width: 100px; " data-id="'.$bump["id"].'">';
	$bumphtml .= '</div>';
	$bumphtml .= '<div class="mwb_upsell_offer_product_content"> <h4>'.$bump["name"].'</h4><br>';
	$bumphtml .= '<p class="mwb_upsell_offer_product_price">'.$bump_offer_price.'</p>';
	$bumphtml .= '<p class="mwb_upsell_offer_product_description">'.$product_description_text.'</p></div></div>';
	// Product section ends.

	// Primary section start.
	$bumphtml .= '<div class = "mwb_upsell_offer_primary_section" >';
	$bumphtml .= '<label class="mwb_upsell_bump_checkbox_container">';
	$bumphtml .= '<input type="checkbox" '.  $check .' name="add_offer_in_cart_checkbox" id ="add_offer_in_cart">';
	$bumphtml .= '<span class="checkmark"></span>';
	$bumphtml .= '</label>';
	$bumphtml .= '<h5>'.$title.'</h5>';
	$bumphtml .= '</div>';
	// Primary section end.

	// Secondary section start.
	$bumphtml .= '<div class = "mwb_upsell_offer_secondary_section" ><p>'.$description.'</p></div>';
	// Secondary section end.

	// Wrapper div end.
	$bumphtml .= '</div>';

	// Parent wrapper end.
	$bumphtml .= '</div></div>';

	return $bumphtml;
}



/**
 * Fetch Bump Offer details for offer html.
 *
 * @since    1.0.0
 */
function mwb_ubo_lite_fetch_bump_offer_details( $encountered_bump_array_index, $mwb_upsell_bump_target_key = '' ) {

	$mwb_ubo_offer_array_collection = get_option( 'mwb_ubo_bump_list' );

	if( empty( $mwb_ubo_offer_array_collection ) ) {

		return;
	}

	$encountered_bump_array = $mwb_ubo_offer_array_collection[$encountered_bump_array_index];

	$offer_id = ! empty( $encountered_bump_array['mwb_upsell_bump_products_in_offer'] ) ? sanitize_text_field( $encountered_bump_array['mwb_upsell_bump_products_in_offer'] ) : "";

	$discount_price = ! empty( $encountered_bump_array['mwb_upsell_bump_offer_discount_price'] ) ? sanitize_text_field( $encountered_bump_array['mwb_upsell_bump_offer_discount_price'] ) : "";

	$_product = wc_get_product( $offer_id );

	$price = $_product->get_price();
	$price_type = $encountered_bump_array['mwb_upsell_offer_price_type'];

	$bump = ! empty( $bump )? $bump : array();

	// Check if price or discount % is given.
	if( $price_type == '%' && ! empty( $discount_price ) ){

		// Discount % is given( no negatives, not more than 100, if 100% then price zero ).
		$discount_price = sanitize_text_field( $discount_price );
		if( $discount_price > 100 ) : $discount_price = 100; endif;
		if( $discount_price < 0 ) : $discount_price = 0; endif;
		$bump['discount_price'] = $price - ( $price * $discount_price/100 );

	}

	// Discount % is given with null value or zero.
	if( $price_type == '%' && empty( $discount_price ) ) {

		// Discount % is given( null or zero )
		$bump['discount_price'] = $price ;

	}

	// Discount fixed is given with null value or zero.
	if ( $price_type == 'fixed' && ! empty( $discount_price  ) ) {

		// When fixed price is given.
		if( $discount_price < 0 ) : $discount_price = 0; endif;
		$bump['discount_price'] = sanitize_text_field( $discount_price );

	}

	// Discount is given with null value or zero.
	if ( $price_type == 'fixed' && empty( $discount_price ) ) {

		if( $discount_price == 0 || $discount_price == '' ) : $discount_price = 0; endif;
		$bump['discount_price'] = sanitize_text_field( $price );
	}

	$_product->set_price( $bump['discount_price'] );
	$price_excl_tax = wc_get_price_excluding_tax( $_product );  // Price without tax.
	$price_incl_tax = wc_get_price_including_tax( $_product );  // Price with tax.

	// Got all details.
	$bump['id'] = $offer_id;
	$bump['offer_type'] = $offer_id;
	$bump['target_key'] = $mwb_upsell_bump_target_key;
	$bump['name'] = get_the_title( $bump['id'] );
	$bump['discount_price_without_tax'] = $price_excl_tax;
	$bump['discount_price_with_tax'] = $price_incl_tax;
	$bump['price_type'] = $price_type;

	$bump[ 'design_text' ] = ! empty( $encountered_bump_array['design_text'] ) ? $encountered_bump_array['design_text'] : array();

	$mwb_bump_upsell_selected_template = ! empty( $encountered_bump_array[ 'mwb_bump_upsell_selected_template' ] ) ? sanitize_text_field( $encountered_bump_array[ 'mwb_bump_upsell_selected_template' ] ) : '';

	if( ! empty( $mwb_bump_upsell_selected_template ) ) {

		// Load the css of selected template
		$template_callb_func = 'mwb_ubo_lite_offer_template_' . $mwb_bump_upsell_selected_template;

		$mwb_bump_enable_available_design = $template_callb_func();

		$bump['design_css'] = $mwb_bump_enable_available_design;
	} else {

		$bump['design_css'] = $encountered_bump_array[ 'design_css' ];
	}

	if ( $price_type == '%' ) {

		if( empty( $discount_price ) ) : $discount_price = 0; endif;
		$bump['bump_price_html'] = $discount_price.'%';

	} else {

		if( get_option( 'woocommerce_tax_display_cart' ) == 'incl' ) {

			$inclusive = true;
		}

		if( ! empty( $inclusive ) ) {

			$bump['bump_price_html'] =  wc_price( $bump['discount_price_with_tax'] );
			$bump['bump_price_at_zero'] =  $bump['discount_price_with_tax'];


		} else {

			$bump['bump_price_html'] = wc_price( $bump['discount_price_without_tax'] );
			$bump['bump_price_at_zero'] =  $bump['discount_price_without_tax'];
		}
	}

	// Get the price criteria to set.
	$bump['discount_price'] = $discount_price . '+' . $price_type;

	return $bump;

}

/**
 * Retrieve Bump Offer location details.
 *
 * @since   1.0.0 
 */
function mwb_ubo_lite_retrieve_bump_location_details( $key = '_after_payment_gateways' ) {

	$location_details = array( 
		'_before_order_summary'   => [
			'hook'     => 'woocommerce_checkout_order_review',
			'priority' => 9,
			'name'     => esc_html__( 'Before Order Summary', 'upsell-order-bump-offer-for-woocommerce' ),
		],
		'_before_payment_gateways'   => [
			'hook'     => 'woocommerce_checkout_order_review',
			'priority' => 11,
			'name'     => esc_html__( 'Before Payment Gateways', 'upsell-order-bump-offer-for-woocommerce' ),
		],
		'_after_payment_gateways' => [
			'hook'     => 'mwb_ubo_after_pg_before_terms',
			'priority' => 19,
			'name'     => esc_html__( 'After Payment Gateways', 'upsell-order-bump-offer-for-woocommerce' ),
		],
		'_before_place_order_button' => [
			'hook'     => 'woocommerce_review_order_before_submit',
			'priority' => 21,
			'name'     => esc_html__( 'Before Place order button', 'upsell-order-bump-offer-for-woocommerce' ),
		],
	);

	return $location_details[$key];

}

/**
 * Function for search through category.
 *
 * @since   1.0.0 
 */
function mwb_ubo_lite_check_category_in_cart( $category_target_id ='' ) {

	// First get required category is parent or child.
	$target_parent_id = mwb_ubo_lite_category_has_parent( $category_target_id );

    // Start of the loop that fetches the cart items.
	foreach ( WC()->cart->get_cart() as $cart_key => $cart_item_object ) {

		$product = $cart_item_object['data'];
		$product_id = ! empty( $product->get_parent_id() ) ? $product->get_parent_id() : $product->get_id();
		$terms = get_the_terms( $product_id, 'product_cat' );

		if( ! empty( $terms ) )	{

			// Second level loop search, in case some items have several categories.
			foreach ( $terms as $term ) {

				// Category id is the product category.
				$category_id = $term->term_id;
				$category_parent_id = $term->parent;

				if( 'is_parent' == $target_parent_id && $category_target_id == $category_parent_id ) {

					// Child Category is in cart!
					return $cart_key;
				}

				// Case to trigger category for child or parent with no child category itself.
				if ( ! empty( $category_target_id ) && ( $category_id == $category_target_id ) ) {

					// Category is in cart!
					return $cart_key;
				}
			}
		}
	}	
}

/**
 * Function for search through category.
 *
 * @since   1.0.0 
 */
function mwb_ubo_lite_category_has_parent( $cat_id ) {

	/**
	 * Custom category are not handled as default taxonomies (category). 
	 * They are under product_cat terms so we get all the category and fetch the 
	 * required one.
	 * After that check if it is parent of not.
	 */
	$args = array(
	    'taxonomy'   => 'product_cat',
	);

	$product_categories = get_terms( $args );
	$category_parent = 'is_parent';

	foreach ( $product_categories as $key => $single_category ) {

		if( $cat_id == $single_category->term_id ) {

			// Required category!
			if( ! empty( $single_category->parent ) ) {

				$category_parent = $single_category->parent;

			}

			return $category_parent;
		}
	}
}

/**
 * Function for prepare bump data in a single array.
 *
 * @since   1.0.0
 */
function mwb_ubo_lite_check_if_in_cart( $product_id ='' ) {

	$product = wc_get_product( $product_id ); 	    //Offer product to be checked.
	$target_product = array();

	if ( $product->is_type( 'variable' ) ) {

		$available_variations = $product->get_available_variations();

		foreach ( $available_variations as $key => $value ) {
			
			foreach ($value as $k => $v) {
				
				if( $k == 'variation_id' ) {

					foreach( WC()->cart->get_cart() as $key => $val ) {

						$_product = $val['data'];

						if( $v == $_product->get_id() && empty( $val[ 'mwb_discounted_price' ] ) ) {

							return $key;
						}
					}
				}
			}
		}
	}

	if( ! empty( $product_id ) ) {

		// When a single variation or simple product are present in bumps array.
		foreach( WC()->cart->get_cart() as $key => $val ) {

			$_product = $val['data'];

			if( $product_id == $_product->get_id() && empty( $val[ 'mwb_discounted_price' ] ) ) {
				return $key;
			}
		}
	}
}



/**
 * Function to check similiar offer already present in cart.
 *
 * @since   1.0.0 
 */
function mwb_ubo_lite_already_in_cart( $offer_id ) {

	$offer_product = wc_get_product( $offer_id );
	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

		// If offer product is a variable product.
		if( $offer_product->has_child() ) {

			// If any variation of this parent is present return present.
			if( ( $cart_item[ 'product_id' ] == $offer_id ) && empty( $cart_item['mwb_discounted_price'] ) ) {

				return true;
			}

		} else {

			// If offer id is variation or simple product.
			if ( ( ( $cart_item[ 'product_id' ] == $offer_id ) || ( $cart_item[ 'variation_id' ] == $offer_id ) ) && empty( $cart_item['mwb_discounted_price'] ) ) {

	            //return true on same offer.
				return true;
			}
		}
	}

	return false;
}


/**
 * Adding html for the popup to show variations.
 *
 * @since   1.0.0 
 */
function mwb_ubo_lite_show_variation_popup( $product = '' ) {

	?>
	<!-- HTML for loader starts. -->
	<div class= "mwb_bump_popup_loader">
		<img src= <?php echo UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'public/resources/icons/loader.svg'; ?> >
	</div>
	<!-- HTML for loader ends. -->

	<!-- HTML for popup wrapper starts. -->	
	<div class="mwb_bump_popup_wrapper">

		<!-- HTML for popup content wrapper starts. -->	
		<div class="mwb_bump_popup_content">

			<!-- Inner custom wrapper starts. -->
			<div class="mwb_bump_popup_inner_content">

				<!-- Close button starts. -->
				<span class="mwb_bump_popup_close">
					<img src= <?php echo UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'public/resources/icons/close.png'; ?> > 
				</span>
				<!-- Close button ends. -->

				<!-- Product Image starts. -->
				<div class="mwb_bump_popup_image" >
					<?php
						echo mwb_ubo_lite_get_bump_image( $product->get_id() );
					?>
				</div>
				<!-- Product Image ends. -->

				<!-- Variation Select starts. -->
				<div class="mwb_bump_popup_select">
					<p class="bump_variable_product_title">
						<?php echo( $product->get_title() ); ?>
					</p>

					<?php
						$attributes = $product->get_variation_attributes();

						// Return if no attributes are present.
						if( empty( $attributes ) ){
							return;
						}
					?>

					<!-- Selected variation by selected fields. -->
					<input type="hidden" id="variation_id_selected" value="" />		

					<!-- Notification arena. -->
					<span id="mwb_ubo_err_waring_for_variation" ></span>
					<span id="mwb_ubo_price_html_for_variation" ></span>

					<!-- Printing all the variation dropdown. -->
					<?php foreach( $attributes as $attribute_name => $options ) : ?>

						<div class="mwb_ubo_input_row">
							<p class="mwb_ubo_bump_attributes_name">

								<!-- In case slug is encountered. -->
								<?php $show_title = str_replace('pa_', '', $attribute_name );  ?>
								<?php echo( ucfirst( $show_title ) ); ?>
								
							</p>

						 	<?php
							 	// Function to return variations select html.
								echo mwb_ubo_lite_show_variation_dropdown( 
									array( 
										'options' => $options, 
										'attribute' => $attribute_name, 
										'product' => $product, 
										'selected' => '',
										'id'	=> 'attribute_'.strtolower( $attribute_name ),
										'class' => 'mwb_upsell_offer_variation_select ',
									)
								);
							?>
						</div>

					<?php endforeach; ?>

					<!-- Add to cart button starts. -->
					<button name="add-to-cart" class="single_add_to_cart_button button alt" id="mwb_ubo_bump_add_to_cart_button">
						<?php _e( 'Add this offer to cart','upsell-order-bump-offer-for-woocommerce' ); ?>
					</button>
					<!-- Add to cart button ends. -->
					
				</div>
				<!-- Variation Select ends. -->

			</div> <!-- Inner custom wrapper ends -->

		</div>
		<!-- HTML for popup content wrapper ends. -->	

	</div>
	<!-- HTML for popup wrapper ends. -->

	<?php
}


/**
 * Check if Product Image is present or not and show.
 *
 * @since   1.0.0 
 */
function mwb_ubo_lite_get_bump_image( $product_id = '', $parent = '' ) {
	
	// Get product thumbnail id.
	$image_id = get_post_thumbnail_id( $product_id );
	$variation_product = wc_get_product( $product_id );

	if( ! empty( $image_id ) ) {

		// Get image with complete html for adding zooming effect( Variation ).
		$bump_var_image = wc_get_gallery_image_html( $image_id, true );

	} else {

		$image_id = get_post_thumbnail_id( $variation_product->get_parent_id() );

		if( ! empty( $image_id ) ) {

			// Get image with complete html for adding zooming effect( Parent image ).
			$bump_var_image = wc_get_gallery_image_html( $image_id, true );

		} else {

			// If no image is present return the woocommerce place holder image.
			$bump_var_image = wc_placeholder_img();
		}

	}

	return $bump_var_image;
}



/**
 * Adding all html for the attributes with a dropdown.
 *
 * @since   1.0.0 
 */
function mwb_ubo_lite_show_variation_dropdown( $args = array() ) {

	$args = wp_parse_args( apply_filters( 'woocommerce_dropdown_variation_attribute_options_args', $args ), array(
		'options'          => false,
		'attribute'        => false,
		'product'          => false,
		'selected' 	       => false,
		'name'             => '',
		'id'               => '',
		'class'            => '',
		'show_option_none' => false,
	) );

	$options               = $args['options'];
	$product               = $args['product'];
	$attribute             = $args['attribute'];
	$name                  = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
	$id                    = $args['id'] ? $args['id'] : sanitize_title( $attribute );
	$class                 = $args['class'];
	$show_option_none      = $args['show_option_none'] ? true : false;
	$show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __( 'Choose an option', 'upsell-order-bump-offer-for-woocommerce' ); 

	if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
		$attributes = $product->get_variation_attributes();
		$options    = $attributes[ $attribute ];
	}

	$html = '<select id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '">';
	$html .= '<option value="">' . esc_html( $show_option_none_text ) . '</option>';

	if ( ! empty( $options ) ) {
		if ( $product && taxonomy_exists( $attribute ) ) {
			// Get terms if this is a taxonomy - ordered. We need the names too.
			$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );

			foreach ( $terms as $term ) {
				if ( in_array( $term->slug, $options ) ) {
					$html .= '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args['selected'] ), $term->slug, false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) ) . '</option>';
				}
			}
		} else {
			foreach ( $options as $option ) {
				// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
				$selected = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false );
				$html .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
			}
		}
	}

	$html .= '</select>';

	return $html;
}


/**
 * Get price html with bump offer discount
 *
 * @since    1.0.0
 */

function mwb_ubo_lite_custom_price_html( $product_id='', $bump_discount= '', $get = '' ) {

	$product = wc_get_product( $product_id );
	$orginal_price = $product->get_price();
	$sale_price = $product->get_sale_price();
	$regular_price = $product->get_regular_price();

	// Case of variable parent product.
	if( empty( $sale_price ) && empty( $regular_price ) ) {

		if (  get_option( 'woocommerce_tax_display_cart' ) == 'incl' ) {

			$default_price = wc_get_price_including_tax( $product );
		} else {

			$default_price = wc_get_price_excluding_tax( $product );
		}
	}
	
	if( ! empty( $bump_discount ) ) {

    	$price_array = explode( '+', $bump_discount );
    	$price_type = $price_array[1];
    	$price_discount = $price_array[0];
    	
    	if( $price_type == '%' ) {

    		$price_discount = sanitize_text_field( $price_discount );
			if( $price_discount > 100 ) : $price_discount = 100; endif;
			if( $price_discount < 0 ) : $price_discount = 0; endif;

			$bump_price = $orginal_price - ( $orginal_price * $price_discount/100  );

			$product->set_price( $bump_price );

    	} else {

        	// Just add the price with discount, tax will be added automatically.
        	if( empty( $price_discount ) ) {

        		// If zero or empty default amount will be taken.
        		$price_discount = $product->get_price();
        		$bump_price = $price_discount;

        	} else {

            	$product->set_price( $price_discount );
            	$bump_price = $price_discount;
        	}
    	}
    }

    // If only bump offer price is needed.
    if( 'price' == $get ) {

    	return $bump_price;
    }

    // Case of variable parent product.
	if( ! empty( $default_price ) ) {

		if (  get_option( 'woocommerce_tax_display_cart' ) == 'incl' ) {

			$bump_price = wc_get_price_including_tax( $product );

		} else {

			$bump_price = wc_get_price_excluding_tax( $product );
		}

		return wc_format_sale_price( $default_price, $bump_price );
	}

	// Check woocommerce settings for tax display at cart.
	if( get_option( 'woocommerce_tax_display_cart' ) == 'incl' ) { 

		// If sale price is not present.
		if ( empty( $sale_price ) ) {

			$regular_price = wc_get_price_including_tax( $product, array('price' => $regular_price ) );

			$bump_price = wc_get_price_including_tax( $product );

			return wc_format_sale_price( $regular_price, $bump_price );

		} else {

			$sale_price = wc_get_price_including_tax( $product, array('price' => $sale_price ) );
			$bump_price = wc_get_price_including_tax( $product );

			return wc_format_sale_price( $sale_price, $bump_price );
		}

	} else {

		// If sale price is present.
		if ( empty( $sale_price ) ) {
			
			return wc_format_sale_price( $regular_price, $bump_price );

		} else {

			return wc_format_sale_price( $sale_price, $bump_price );
		}
	}

}