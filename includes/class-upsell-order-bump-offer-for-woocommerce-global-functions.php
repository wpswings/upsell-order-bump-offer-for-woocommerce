<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      1.0.0
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/includes
 */

/**
 * If pro Add-on is present and activated/valid.
 *
 * @since    1.0.0
 */
function wps_ubo_lite_if_pro_exists() {

	// Check if pro plugin exists.
	if ( wps_ubo_lite_is_plugin_active( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php' ) ) {

		return true;
	}

	return false;
}

/**
 * Checks Whether if Pro version is incompatible.
 *
 * @since    1.0.0
 */
function wps_ubo_lite_pro_version_incompatible() {

	if ( wps_ubo_lite_if_pro_exists() ) {

		// When Pro plugin is outdated.
		if ( defined( 'UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_VERSION' ) && version_compare( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_VERSION, '1.3.0' ) < 0 ) {

			return 'incompatible';
		} else {

			return 'compatible';
		}
	}

	return false;
}

/**
 * If pro Add-on is present and activated/valid.
 *
 * @param   string $description        Tooltip message.
 *
 * @since    1.0.0
 */
function wps_ubo_lite_help_tip( $description = '' ) {

	// Run only if description message is present.
	if ( ! empty( $description ) ) {

		$allowed_html = array(
			'span' => array(
				'class'    => array(),
				'data-tip' => array(),
			),
		);

		echo wp_kses( wc_help_tip( $description ), $allowed_html );
	}
}

/**
 * This function returns just allowed html for order bump.
 *
 * @since    1.0.0
 */
function wps_ubo_lite_allowed_html() {

	// Return the complete html elements defined by us.
	$allowed_html = array(
		'input'   => array(
			'class'       => array(
				'add_offer_in_cart',
				'offer_shown_id',
				'offer_shown_discount',
			),
			'id'          => array(
				'target_id_cart_key',
				array(),
			),
			'name'        => array(),
			'placeholder' => array(),
			'value'       => array(),
			'type'        => array( 'hidden', 'checkbox' ),
			'checked'     => array(),
			'min'         => array(),
			'max'         => array(),
		),
		'label'   => array(
			'class' => array( 'wps_upsell_bump_checkbox_container', 'wps_upsell_bump_checkbox_container', 'add_offer_in_cart' ),
			'id'    => array(),
			'value' => array(),
			'for'   => 'wps-ob-st__head-check',
		),
		'span'    => array(
			'class' => array(
				'wps_upsell_offer_arrow',
				'woocommerce-Price-amount',
				'amount',
				'woocommerce-Price-currencySymbol',
				'checkmark',
				'woocommerce-help-tip',
			),
			'id'    => array(),
			'value' => array(),
			'tabindex' => array(),
			'aria-label' => array(),
			'data-tip' => array(),
		),
		'br'      => '',
		'ins'     => '',
		'del'     => '',
		'h2'      => '',
		'h3' => array(),
		'strike' => array(),
		'h5'      => array(
			'class' => array(
				'add_offer_in_cart_text',
			),
		),
		'div'     => array(
			'class'                              => array(
				'wps_upsell_offer_main_wrapper',
				'wps-ob-st',
				'wps_upsell_offer_parent_wrapper',
				'wps_upsell_offer_discount_section',
				'wps_upsell_offer_wrapper',
				'wps_upsell_offer_product_section',
				'wps_upsell_offer_image',
				'wps_upsell_offer_arrow',
				'wps_upsell_offer_product_content',
				'wps_accept_offer_cla',
				'wps_upsell_offer_primary_section' => array(
					'div' => array(
						'img' => array(
							'src',
						),
					),
				),
				'wps_upsell_offer_secondary_section',
				'woocommerce-product-gallery__image',
				'wps_ubo_lite_go_pro_popup_wrap',
				'wps_ubo_lite_go_pro_popup',
				'wps_ubo_lite_go_pro_popup_head',
				'wps_ubo_lite_go_pro_popup_content',
				'wps_ubo_lite_go_pro_popup_button',
			),
			'id'                                 => array(
				'wps-ubo__temp-sec',
			),
			'value'                              => array(),
			'data-thumb'                         => array(),
			'data-thumb-alt'                     => array(),
			'woocommerce-product-gallery__image' => array(),
		),
		'svg'     => array(
			'xmlns'   => array(),
			'viewbox' => array(),
			'file' => '',
		),
		'path' => array(
			'fill-rule' => array(),
			'clip-rule' => array(),
			'd' => array(),
			'fill' => array(),
		),
		'defs'    => array(),
		'style'   => array(),
		'g'       => array(
			'id' => array(),
		),
		'polygon' => array(
			'class'  => array(),
			'points' => array(),
		),
		'p'        => array(
			'class' => array(
				'wps_upsell_offer_product_price',
				'wps_upsell_offer_product_description',
				'wps_ubo_lite_go_pro_popup_text',
			),
			'id'    => array(),
			'value' => array(),
		),
		'b'       => '',
		'img'     => array(
			'class'                   => array( 'wp-post-image' ),
			'id'                      => array(),
			'src'                     => array(),
			'style'                   => array(),
			'data-id'                 => array(),
			'width'                   => array(),
			'height'                  => array(),
			'alt'                     => array(),
			'data-caption'            => array(),
			'data-src'                => array(),
			'data-large_image'        => array(),
			'data-large_image_width'  => array(),
			'data-large_image_height' => array(),
			'srcset'                  => array(),
			'sizes'                   => array(),
		),
		'a'       => array(
			'href'   => array(),
			'class'  => array(
				'wps_ubo_lite_go_pro_popup_close',
				'button',
				'wps_ubo_lite_overview_go_pro_button',
			),
			'target' => '_blank',
		),
		'select'  => array(
			'id'                    => array(),
			'class'                 => array(),
			'name'                  => array(),
			'data-attribute_name'   => array(),
			'data-show_option_none' => array(),
			'order_bump_index'      => array(),
			'order'                 => array(),
			'attribute_pa_color'    => array(),
		),
		'h4'      => array(
			'data-wps_qty'          => array(),
			'data-wps_is_fixed_qty' => array(),
			'data-qty_allowed'      => array(),
			'class'                 => array(),
		),
		'option'  => array(
			'value'    => array(),
			'selected' => array(),
		),
		'section' => array(
			'id' => array(),
			'class' => array(),
		),
		'article' => array(
			'class' => array(),
		),
		'button' => array(
			'type' => array(),
			'class' => array(),
		),
		'i' => array(
			'class' => array(),
		),
	);
	?>
	<?php
	return $allowed_html;
}

/**
 * Bump offer template 1.
 *
 * ( Default Template ).
 *
 * @since    1.0.0
 */
function wps_ubo_lite_offer_template_1() {

	// Template 1.
	$wps_bump_upsell_global_css['parent_border_type']      = 'dashed';
	$wps_bump_upsell_global_css['parent_border_color']     = '#000000';
	$wps_bump_upsell_global_css['parent_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['top_vertical_spacing']    = '10';
	$wps_bump_upsell_global_css['bottom_vertical_spacing'] = '10';

	// DISCOUNT SECTION( discount_section ).
	$wps_bump_upsell_global_css['discount_section_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['discount_section_text_color']       = '#616060';
	$wps_bump_upsell_global_css['discount_section_text_size']        = '35';

	// PRODUCT SECTION(product_section).
	$wps_bump_upsell_global_css['product_section_text_color'] = '#0a0a0a';
	$wps_bump_upsell_global_css['product_section_text_size']  = '14';
	$wps_bump_upsell_global_css['product_section_img_width']  = '75';
	$wps_bump_upsell_global_css['product_section_img_height'] = '75';
	$wps_bump_upsell_global_css['product_section_price_text_size'] = '13';
	$wps_bump_upsell_global_css['product_section_price_text_color'] = '#000000';

	// Accept Offer Section(primary_section).
	$wps_bump_upsell_global_css['primary_section_background_color'] = '#73cc12';
	$wps_bump_upsell_global_css['primary_section_text_color']       = '#ffffff';
	$wps_bump_upsell_global_css['primary_section_arrow_color']      = '#FF0000';
	$wps_bump_upsell_global_css['primary_section_text_size']        = '18';

	// SECONDARY SECTION(secondary_section).
	$wps_bump_upsell_global_css['secondary_section_background_color'] = '#ffdd2f';
	$wps_bump_upsell_global_css['secondary_section_text_color']       = '#292626';
	$wps_bump_upsell_global_css['secondary_section_text_size']        = '20';

	return $wps_bump_upsell_global_css;
}

/**
 * Bump offer template 2.
 *
 * @since    1.0.0
 */
function wps_ubo_lite_offer_template_2() {

	// Template 2.
	$wps_bump_upsell_global_css['parent_border_type']      = 'dashed';
	$wps_bump_upsell_global_css['parent_border_color']     = '#000000';
	$wps_bump_upsell_global_css['parent_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['top_vertical_spacing']    = '10';
	$wps_bump_upsell_global_css['bottom_vertical_spacing'] = '10';

	// DISCOUNT SECTION( discount_section ).
	$wps_bump_upsell_global_css['discount_section_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['discount_section_text_color']       = '#485f75';
	$wps_bump_upsell_global_css['discount_section_text_size']        = '35';

	// PRODUCT SECTION(product_section).
	$wps_bump_upsell_global_css['product_section_text_color'] = '#0a0a0a';
	$wps_bump_upsell_global_css['product_section_text_size']  = '14';
	$wps_bump_upsell_global_css['product_section_img_width']  = '75';
	$wps_bump_upsell_global_css['product_section_img_height'] = '75';
	$wps_bump_upsell_global_css['product_section_price_text_size'] = '13';
	$wps_bump_upsell_global_css['product_section_price_text_color'] = '#000000';

	// Accept Offer Section(primary_section).
	$wps_bump_upsell_global_css['primary_section_background_color'] = '#c1f0db';
	$wps_bump_upsell_global_css['primary_section_arrow_color']      = '#FF0000';
	$wps_bump_upsell_global_css['primary_section_text_color']       = '#485f75';
	$wps_bump_upsell_global_css['primary_section_text_size']        = '18';

	// SECONDARY SECTION(secondary_section).
	$wps_bump_upsell_global_css['secondary_section_background_color'] = '#c1f0db';
	$wps_bump_upsell_global_css['secondary_section_text_color']       = '#485f75';
	$wps_bump_upsell_global_css['secondary_section_text_size']        = '20';

	return $wps_bump_upsell_global_css;
}

/**
 * Bump offer template 3.
 *
 * @since    1.0.0
 */
function wps_ubo_lite_offer_template_3() {

	// Template 3.
	$wps_bump_upsell_global_css['parent_border_type']      = 'dashed';
	$wps_bump_upsell_global_css['parent_border_color']     = '#000000';
	$wps_bump_upsell_global_css['parent_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['top_vertical_spacing']    = '10';
	$wps_bump_upsell_global_css['bottom_vertical_spacing'] = '10';

	// DISCOUNT SECTION( discount_section ).
	$wps_bump_upsell_global_css['discount_section_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['discount_section_text_color']       = '#584c4c';
	$wps_bump_upsell_global_css['discount_section_text_size']        = '35';

	// PRODUCT SECTION( product_section ).
	$wps_bump_upsell_global_css['product_section_text_color'] = '#0a0a0a';
	$wps_bump_upsell_global_css['product_section_text_size']  = '14';
	$wps_bump_upsell_global_css['product_section_img_width']  = '75';
	$wps_bump_upsell_global_css['product_section_img_height'] = '75';
	$wps_bump_upsell_global_css['product_section_price_text_size'] = '13';
	$wps_bump_upsell_global_css['product_section_price_text_color'] = '#000000';

	// Accept Offer Section( primary_section ).
	$wps_bump_upsell_global_css['primary_section_background_color'] = '#feb800';
	$wps_bump_upsell_global_css['primary_section_arrow_color']      = '#FF0000';
	$wps_bump_upsell_global_css['primary_section_text_color']       = '#ffffff';
	$wps_bump_upsell_global_css['primary_section_text_size']        = '18';

	// SECONDARY SECTION( secondary_section ).
	$wps_bump_upsell_global_css['secondary_section_background_color'] = '#feb800';
	$wps_bump_upsell_global_css['secondary_section_text_color']       = '#ffffff';
	$wps_bump_upsell_global_css['secondary_section_text_size']        = '20';

	return $wps_bump_upsell_global_css;
}

/**
 * Bump offer template 4.
 *
 * @since    1.0.0
 */
function wps_ubo_lite_offer_template_4() {

	// Template 4.
	$wps_bump_upsell_global_css['parent_border_type']      = 'dashed';
	$wps_bump_upsell_global_css['parent_border_color']     = '#000000';
	$wps_bump_upsell_global_css['parent_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['top_vertical_spacing']    = '10';
	$wps_bump_upsell_global_css['bottom_vertical_spacing'] = '10';

	// DISCOUNT SECTION( discount_section ).
	$wps_bump_upsell_global_css['discount_section_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['discount_section_text_color']       = '#584c4c';
	$wps_bump_upsell_global_css['discount_section_text_size']        = '35';

	// PRODUCT SECTION( product_section ).
	$wps_bump_upsell_global_css['product_section_text_color'] = '#0a0a0a';
	$wps_bump_upsell_global_css['product_section_text_size']  = '14';
	$wps_bump_upsell_global_css['product_section_img_width']  = '125';
	$wps_bump_upsell_global_css['product_section_img_height'] = '125';
	$wps_bump_upsell_global_css['product_section_price_text_size'] = '13';
	$wps_bump_upsell_global_css['product_section_price_text_color'] = '#000000';

	// Accept Offer Section( primary_section ).
	$wps_bump_upsell_global_css['primary_section_background_color'] = '#8224e3';
	$wps_bump_upsell_global_css['primary_section_arrow_color']      = '#FF0000';
	$wps_bump_upsell_global_css['primary_section_text_color']       = '#ffffff';
	$wps_bump_upsell_global_css['primary_section_text_size']        = '18';

	// SECONDARY SECTION( secondary_section ).
	$wps_bump_upsell_global_css['secondary_section_background_color'] = '#8600c4';
	$wps_bump_upsell_global_css['secondary_section_text_color']       = '#ffffff';
	$wps_bump_upsell_global_css['secondary_section_text_size']        = '20';

	return $wps_bump_upsell_global_css;
}


/**
 * Bump offer template 5.
 *
 * @since    1.0.0
 */
function wps_ubo_lite_offer_template_5() {

	// Template 5.
	$wps_bump_upsell_global_css['parent_border_type']      = 'dashed';
	$wps_bump_upsell_global_css['parent_border_color']     = '#000000';
	$wps_bump_upsell_global_css['parent_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['top_vertical_spacing']    = '10';
	$wps_bump_upsell_global_css['bottom_vertical_spacing'] = '10';

	// DISCOUNT SECTION( discount_section ).
	$wps_bump_upsell_global_css['discount_section_background_color'] = '#81d742';
	$wps_bump_upsell_global_css['discount_section_text_color']       = '#584c4c';
	$wps_bump_upsell_global_css['discount_section_text_size']        = '35';

	// PRODUCT SECTION( product_section ).
	$wps_bump_upsell_global_css['product_section_text_color'] = '#0a0a0a';
	$wps_bump_upsell_global_css['product_section_text_size']  = '14';
	$wps_bump_upsell_global_css['product_section_img_width']  = '125';
	$wps_bump_upsell_global_css['product_section_img_height'] = '150';
	$wps_bump_upsell_global_css['product_section_price_text_size'] = '13';
	$wps_bump_upsell_global_css['product_section_price_text_color'] = '#000000';

	// Accept Offer Section( primary_section ).
	$wps_bump_upsell_global_css['primary_section_background_color'] = '#ee4e34';
	$wps_bump_upsell_global_css['primary_section_arrow_color']      = '#000000';
	$wps_bump_upsell_global_css['primary_section_text_color']       = '#ffffff';
	$wps_bump_upsell_global_css['primary_section_text_size']        = '18';

	// SECONDARY SECTION( secondary_section ).
	$wps_bump_upsell_global_css['secondary_section_background_color'] = '#ee4e34';
	$wps_bump_upsell_global_css['secondary_section_text_color']       = '#ffffff';
	$wps_bump_upsell_global_css['secondary_section_text_size']        = '20';

	return $wps_bump_upsell_global_css;
}

/**
 * Bump offer template 5.
 *
 * @since    1.0.0
 */
function wps_ubo_lite_offer_template_6() {

	// Template 6.
	$wps_bump_upsell_global_css['parent_border_type']      = 'dashed';
	$wps_bump_upsell_global_css['parent_border_color']     = '#000000';
	$wps_bump_upsell_global_css['parent_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['top_vertical_spacing']    = '10';
	$wps_bump_upsell_global_css['bottom_vertical_spacing'] = '10';

	// DISCOUNT SECTION( discount_section ).
	$wps_bump_upsell_global_css['discount_section_background_color'] = '#81d742';
	$wps_bump_upsell_global_css['discount_section_text_color']       = '#584c4c';
	$wps_bump_upsell_global_css['discount_section_text_size']        = '35';

	// PRODUCT SECTION( product_section ).
	$wps_bump_upsell_global_css['product_section_text_color'] = '#0a0a0a';
	$wps_bump_upsell_global_css['product_section_text_size']  = '14';
	$wps_bump_upsell_global_css['product_section_img_width']  = '125';
	$wps_bump_upsell_global_css['product_section_img_height'] = '150';
	$wps_bump_upsell_global_css['product_section_price_text_size'] = '13';
	$wps_bump_upsell_global_css['product_section_price_text_color'] = '#0a0a0a';

	// Accept Offer Section( primary_section ).
	$wps_bump_upsell_global_css['primary_section_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['primary_section_arrow_color']      = '#000000';
	$wps_bump_upsell_global_css['primary_section_text_color']       = '#000000';
	$wps_bump_upsell_global_css['primary_section_text_size']        = '18';

	// SECONDARY SECTION( secondary_section ).
	$wps_bump_upsell_global_css['secondary_section_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['secondary_section_text_color']       = '#000000';
	$wps_bump_upsell_global_css['secondary_section_text_size']        = '20';

	// SET THE CLASS FOR THE TEMPLATE.
	$wps_bump_upsell_global_css['wps_class_template_pro']  = 'wps-hybrid';

	return $wps_bump_upsell_global_css;
}

/**
 * Bump offer template 5.
 *
 * @since    1.0.0
 */
function wps_ubo_lite_offer_template_7() {

	// Template 6.
	$wps_bump_upsell_global_css['parent_border_type']      = 'dashed';
	$wps_bump_upsell_global_css['parent_border_color']     = '#000000';
	$wps_bump_upsell_global_css['parent_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['top_vertical_spacing']    = '10';
	$wps_bump_upsell_global_css['bottom_vertical_spacing'] = '10';

	// DISCOUNT SECTION( discount_section ).
	$wps_bump_upsell_global_css['discount_section_background_color'] = '#81d742';
	$wps_bump_upsell_global_css['discount_section_text_color']       = '#584c4c';
	$wps_bump_upsell_global_css['discount_section_text_size']        = '35';

	// PRODUCT SECTION( product_section ).
	$wps_bump_upsell_global_css['product_section_text_color'] = '#0a0a0a';
	$wps_bump_upsell_global_css['product_section_text_size']  = '14';
	$wps_bump_upsell_global_css['product_section_img_width']  = '125';
	$wps_bump_upsell_global_css['product_section_img_height'] = '150';
	$wps_bump_upsell_global_css['product_section_price_text_size'] = '13';
	$wps_bump_upsell_global_css['product_section_price_text_color'] = '#0a0a0a';

	// Accept Offer Section( primary_section ).
	$wps_bump_upsell_global_css['primary_section_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['primary_section_arrow_color']      = '#000000';
	$wps_bump_upsell_global_css['primary_section_text_color']       = '#000000';
	$wps_bump_upsell_global_css['primary_section_text_size']        = '18';

	// SECONDARY SECTION( secondary_section ).
	$wps_bump_upsell_global_css['secondary_section_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['secondary_section_text_color']       = '#000000';
	$wps_bump_upsell_global_css['secondary_section_text_size']        = '20';

	// SET THE CLASS FOR THE TEMPLATE.
	$wps_bump_upsell_global_css['wps_class_template_pro']  = 'wps-horizontal-ltr';

	return $wps_bump_upsell_global_css;
}

/**
 * Bump offer template 5.
 *
 * @since    1.0.0
 */
function wps_ubo_lite_offer_template_8() {

	// Template 6.
	$wps_bump_upsell_global_css['parent_border_type']      = 'dashed';
	$wps_bump_upsell_global_css['parent_border_color']     = '#000000';
	$wps_bump_upsell_global_css['parent_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['top_vertical_spacing']    = '10';
	$wps_bump_upsell_global_css['bottom_vertical_spacing'] = '10';

	// DISCOUNT SECTION( discount_section ).
	$wps_bump_upsell_global_css['discount_section_background_color'] = '#81d742';
	$wps_bump_upsell_global_css['discount_section_text_color']       = '#584c4c';
	$wps_bump_upsell_global_css['discount_section_text_size']        = '35';

	// PRODUCT SECTION( product_section ).
	$wps_bump_upsell_global_css['product_section_text_color'] = '#0a0a0a';
	$wps_bump_upsell_global_css['product_section_text_size']  = '14';
	$wps_bump_upsell_global_css['product_section_img_width']  = '125';
	$wps_bump_upsell_global_css['product_section_img_height'] = '150';
	$wps_bump_upsell_global_css['product_section_price_text_size'] = '13';
	$wps_bump_upsell_global_css['product_section_price_text_color'] = '#0a0a0a';

	// Accept Offer Section( primary_section ).
	$wps_bump_upsell_global_css['primary_section_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['primary_section_arrow_color']      = '#000000';
	$wps_bump_upsell_global_css['primary_section_text_color']       = '#000000';
	$wps_bump_upsell_global_css['primary_section_text_size']        = '18';

	// SECONDARY SECTION( secondary_section ).
	$wps_bump_upsell_global_css['secondary_section_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['secondary_section_text_color']       = '#000000';
	$wps_bump_upsell_global_css['secondary_section_text_size']        = '20';

	// SET THE CLASS FOR THE TEMPLATE.
	$wps_bump_upsell_global_css['wps_class_template_pro']  = 'wps-horizontal-rtl';

	return $wps_bump_upsell_global_css;
}

/**
 * Bump offer template 5.
 *
 * @since    1.0.0
 */
function wps_ubo_lite_offer_template_9() {

	// Template 6.
	$wps_bump_upsell_global_css['parent_border_type']      = 'dashed';
	$wps_bump_upsell_global_css['parent_border_color']     = '#000000';
	$wps_bump_upsell_global_css['parent_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['top_vertical_spacing']    = '10';
	$wps_bump_upsell_global_css['bottom_vertical_spacing'] = '10';

	// DISCOUNT SECTION( discount_section ).
	$wps_bump_upsell_global_css['discount_section_background_color'] = '#81d742';
	$wps_bump_upsell_global_css['discount_section_text_color']       = '#584c4c';
	$wps_bump_upsell_global_css['discount_section_text_size']        = '35';

	// PRODUCT SECTION( product_section ).
	$wps_bump_upsell_global_css['product_section_text_color'] = '#0a0a0a';
	$wps_bump_upsell_global_css['product_section_text_size']  = '14';
	$wps_bump_upsell_global_css['product_section_img_width']  = '125';
	$wps_bump_upsell_global_css['product_section_img_height'] = '150';
	$wps_bump_upsell_global_css['product_section_price_text_size'] = '13';
	$wps_bump_upsell_global_css['product_section_price_text_color'] = '#0a0a0a';

	// Accept Offer Section( primary_section ).
	$wps_bump_upsell_global_css['primary_section_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['primary_section_arrow_color']      = '#000000';
	$wps_bump_upsell_global_css['primary_section_text_color']       = '#000000';
	$wps_bump_upsell_global_css['primary_section_text_size']        = '18';

	// SECONDARY SECTION( secondary_section ).
	$wps_bump_upsell_global_css['secondary_section_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['secondary_section_text_color']       = '#000000';
	$wps_bump_upsell_global_css['secondary_section_text_size']        = '20';

	// SET THE CLASS FOR THE TEMPLATE.
	$wps_bump_upsell_global_css['wps_class_template_pro']  = 'wps-verticle';

	return $wps_bump_upsell_global_css;
}

/**
 * Bump offer template 5.
 *
 * @since    1.0.0
 */
function wps_ubo_lite_offer_template_10() {

	// Template 10.
	$wps_bump_upsell_global_css['parent_border_type']      = 'solid';
	$wps_bump_upsell_global_css['parent_border_color']     = '#000000';
	$wps_bump_upsell_global_css['parent_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['top_vertical_spacing']    = '10';
	$wps_bump_upsell_global_css['bottom_vertical_spacing'] = '10';

	// DISCOUNT SECTION( discount_section ).
	$wps_bump_upsell_global_css['discount_section_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['discount_section_text_color']       = '#584c4c';
	$wps_bump_upsell_global_css['discount_section_text_size']        = '14';

	// PRODUCT SECTION( product_section ).
	$wps_bump_upsell_global_css['product_section_text_color'] = '#0a0a0a';
	$wps_bump_upsell_global_css['product_section_text_size']  = '14';
	$wps_bump_upsell_global_css['product_section_img_width']  = '125';
	$wps_bump_upsell_global_css['product_section_img_height'] = '150';
	$wps_bump_upsell_global_css['product_section_price_text_size'] = '13';
	$wps_bump_upsell_global_css['product_section_price_text_color'] = '#0a0a0a';

	// Accept Offer Section( primary_section ).
	$wps_bump_upsell_global_css['primary_section_background_color'] = '#f5f5f5';
	$wps_bump_upsell_global_css['primary_section_arrow_color']      = '#000000';
	$wps_bump_upsell_global_css['primary_section_text_color']       = '#000000';
	$wps_bump_upsell_global_css['primary_section_text_size']        = '18';

	// SECONDARY SECTION( secondary_section ).
	$wps_bump_upsell_global_css['secondary_section_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['secondary_section_text_color']       = '#000000';
	$wps_bump_upsell_global_css['secondary_section_text_size']        = '20';

	// SET THE CLASS FOR THE TEMPLATE.
	$wps_bump_upsell_global_css['wps_class_template_pro']  = 'wps-verticle';

	return $wps_bump_upsell_global_css;
}


/**
 * Bump offer template 5.
 *
 * @since    1.0.0
 */
function wps_ubo_lite_offer_template_11() {

	// Template 10.
	$wps_bump_upsell_global_css['parent_border_type']      = 'solid';
	$wps_bump_upsell_global_css['parent_border_color']     = '#000000';
	$wps_bump_upsell_global_css['parent_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['top_vertical_spacing']    = '10';
	$wps_bump_upsell_global_css['bottom_vertical_spacing'] = '10';

	// DISCOUNT SECTION( discount_section ).
	$wps_bump_upsell_global_css['discount_section_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['discount_section_text_color']       = '#584c4c';
	$wps_bump_upsell_global_css['discount_section_text_size']        = '14';

	// PRODUCT SECTION( product_section ).
	$wps_bump_upsell_global_css['product_section_text_color'] = '#0a0a0a';
	$wps_bump_upsell_global_css['product_section_text_size']  = '14';
	$wps_bump_upsell_global_css['product_section_img_width']  = '125';
	$wps_bump_upsell_global_css['product_section_img_height'] = '150';
	$wps_bump_upsell_global_css['product_section_price_text_size'] = '13';
	$wps_bump_upsell_global_css['product_section_price_text_color'] = '#0a0a0a';

	// Accept Offer Section( primary_section ).
	$wps_bump_upsell_global_css['primary_section_background_color'] = '#f5f5f5';
	$wps_bump_upsell_global_css['primary_section_arrow_color']      = '#000000';
	$wps_bump_upsell_global_css['primary_section_text_color']       = '#000000';
	$wps_bump_upsell_global_css['primary_section_text_size']        = '18';

	// SECONDARY SECTION( secondary_section ).
	$wps_bump_upsell_global_css['secondary_section_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['secondary_section_text_color']       = '#000000';
	$wps_bump_upsell_global_css['secondary_section_text_size']        = '20';

	// SET THE CLASS FOR THE TEMPLATE.
	$wps_bump_upsell_global_css['wps_class_template_pro']  = 'wps-verticle';

	return $wps_bump_upsell_global_css;
}


/**
 * Bump offer template 5.
 *
 * @since    1.0.0
 */
function wps_ubo_lite_offer_template_12() {

	// Template 10.
	$wps_bump_upsell_global_css['parent_border_type']      = 'solid';
	$wps_bump_upsell_global_css['parent_border_color']     = '#000000';
	$wps_bump_upsell_global_css['parent_background_color'] = '#d6eab9';
	$wps_bump_upsell_global_css['top_vertical_spacing']    = '10';
	$wps_bump_upsell_global_css['bottom_vertical_spacing'] = '10';

	// DISCOUNT SECTION( discount_section ).
	$wps_bump_upsell_global_css['discount_section_background_color'] = '#81d742';
	$wps_bump_upsell_global_css['discount_section_text_color']       = '#ffffff';
	$wps_bump_upsell_global_css['discount_section_text_size']        = '25';

	// PRODUCT SECTION( product_section ).
	$wps_bump_upsell_global_css['product_section_text_color'] = '#0a0a0a';
	$wps_bump_upsell_global_css['product_section_text_size']  = '14';
	$wps_bump_upsell_global_css['product_section_img_width']  = '125';
	$wps_bump_upsell_global_css['product_section_img_height'] = '150';
	$wps_bump_upsell_global_css['product_section_price_text_size'] = '13';
	$wps_bump_upsell_global_css['product_section_price_text_color'] = '#0a0a0a';

	// Accept Offer Section( primary_section ).
	$wps_bump_upsell_global_css['primary_section_background_color'] = '#81d742';
	$wps_bump_upsell_global_css['primary_section_arrow_color']      = '#000000';
	$wps_bump_upsell_global_css['primary_section_text_color']       = '#ffffff';
	$wps_bump_upsell_global_css['primary_section_text_size']        = '25';

	// SECONDARY SECTION( secondary_section ).
	$wps_bump_upsell_global_css['secondary_section_background_color'] = '#ffffff';
	$wps_bump_upsell_global_css['secondary_section_text_color']       = '#000000';
	$wps_bump_upsell_global_css['secondary_section_text_size']        = '17';

	// SET THE CLASS FOR THE TEMPLATE.
	$wps_bump_upsell_global_css['wps_class_template_pro']  = 'wps-verticle';

	return $wps_bump_upsell_global_css;
}
/**
 * Bump offer default global options.
 *
 * @since 1.0.0
 */
function wps_ubo_lite_default_global_options() {

	$default_global_options = array(

		'wps_bump_enable_plugin'          => 'on', // By default plugin will be enabled.
		'wps_bump_skip_offer'             => 'yes',
		'wps_ubo_offer_location'          => '_after_payment_gateways',
		'wps_ubo_offer_removal'           => 'yes',
		'wps_ubo_temp_adaption'           => 'yes',
		'wps_ubo_offer_global_css'        => '',
		'wps_ubo_offer_global_js'         => '',
		'wps_ubo_offer_price_html'        => 'regular_to_offer',
		'wps_ubo_offer_purchased_earlier' => 'no',
		'wps_bump_order_bump_limit'       => '1',
	);

	return $default_global_options;
}

/**
 * Bump offer default text fields.
 *
 * @since    1.0.0
 */
function wps_ubo_lite_offer_default_text() {

	$default_default_text = array(

		'wps_ubo_discount_title_for_fixed'   => sprintf( '%s %s %s', esc_html__( 'AT JUST', 'upsell-order-bump-offer-for-woocommerce' ), '{dc_price}', esc_html__( '!!', 'upsell-order-bump-offer-for-woocommerce' ) ),

		'wps_ubo_discount_title_for_percent' => sprintf( '%s %s', '{dc_%}', esc_html__( 'off only for you !!', 'upsell-order-bump-offer-for-woocommerce' ) ),

		'wps_bump_offer_decsription_text'    => esc_html__( 'A unique and handy product that will benefit you on your existing purchase.', 'upsell-order-bump-offer-for-woocommerce' ),

		'wps_upsell_offer_title'             => esc_html__( 'Get this exclusive offer now !!', 'upsell-order-bump-offer-for-woocommerce' ),

		'wps_upsell_bump_offer_description'  => esc_html__( 'Hey there, you can get access to this offer by just clicking the checkbox above. Add this offer to your order, you will never get such a discount on any other place on this site.', 'upsell-order-bump-offer-for-woocommerce' ),
	);

	return $default_default_text;
}

/**
 * Bump Offer Html.
 *
 * @param   string $bump        Consists all data about order bump.
 * @param   string $encountered_order_bump_id        Consists all data about order bump.
 * @param   string $order_bump_key        Consists all data about order bump.
 * @since   1.0.0
 */
function wps_ubo_lite_bump_offer_html( $bump, $encountered_order_bump_id = '', $order_bump_key = '' ) {

	/**
	 * Text fields.
	 */
	$title = ! empty( $bump['design_text']['wps_upsell_offer_title'] ) ? $bump['design_text']['wps_upsell_offer_title'] : '';

	$description = $bump['design_text']['wps_upsell_bump_offer_description'];

	$product_description_text = $bump['design_text']['wps_bump_offer_decsription_text'];

	$discount_title_fixed = ! empty( $bump['design_text']['wps_ubo_discount_title_for_fixed'] ) ? $bump['design_text']['wps_ubo_discount_title_for_fixed'] : '';

	$discount_title_percent = ! empty( $bump['design_text']['wps_ubo_discount_title_for_percent'] ) ? $bump['design_text']['wps_ubo_discount_title_for_percent'] : '';

	if ( ! empty( $bump['bump_price_html'] ) ) {

		$discount_title_fixed   = str_replace( '{dc_price}', $bump['bump_price_html'], $discount_title_fixed );
		$discount_title_percent = str_replace( '{dc_%}', $bump['bump_price_html'], $discount_title_percent );

	}

	if ( ! empty( $bump['price_type'] ) && 'fixed' === $bump['price_type'] ) {

		$bump_price_html = $discount_title_fixed;
	} else {

		$bump_price_html = $discount_title_percent;
	}

	// Template adaption.
	$wps_ubo_global_options    = get_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );
	$wps_ubo_template_adaption = ! empty( $wps_ubo_global_options['wps_ubo_temp_adaption'] ) ? $wps_ubo_global_options['wps_ubo_temp_adaption'] : '';

	// Setting to enable disable permalink.
	$wps_bump_enable_permalink = ! empty( $wps_ubo_global_options['wps_bump_enable_permalink'] ) ? $wps_ubo_global_options['wps_bump_enable_permalink'] : '';
	// Red arrow setting.
	$wps_enable_red_arrow_feature = ! empty( $wps_ubo_global_options['wps_enable_red_arrow_feature'] ) ? $wps_ubo_global_options['wps_enable_red_arrow_feature'] : '';
	// Setting for the offer Quantity.
	$wps_upsell_enable_quantity              = ! empty( $bump['wps_upsell_enable_quantity'] ) ? $bump['wps_upsell_enable_quantity'] : '';
	$wps_upsell_bump_products_fixed_quantity = ! empty( $bump['wps_upsell_bump_products_fixed_quantity'] ) ? $bump['wps_upsell_bump_products_fixed_quantity'] : '';
	$wps_upsell_bump_products_min_quantity   = ! empty( $bump['wps_upsell_bump_products_min_quantity'] ) ? $bump['wps_upsell_bump_products_min_quantity'] : '';
	$wps_upsell_bump_products_max_quantity   = ! empty( $bump['wps_upsell_bump_products_max_quantity'] ) ? $bump['wps_upsell_bump_products_max_quantity'] : '';
	$wps_upsell_offer_quantity_type          = ! empty( $bump['wps_upsell_offer_quantity_type'] ) ? $bump['wps_upsell_offer_quantity_type'] : '';

	// PARENT WRAPPER DIV CSS( parent_wrapper_div ).
	$parent_border_type             = ! empty( $bump['design_css']['parent_border_type'] ) ? $bump['design_css']['parent_border_type'] : '';
	$parent_border_color            = ! empty( $bump['design_css']['parent_border_color'] ) ? $bump['design_css']['parent_border_color'] : '';
	$parent_background_color        = ! empty( $bump['design_css']['parent_background_color'] ) ? $bump['design_css']['parent_background_color'] : '';
	$parent_top_vertical_spacing    = ! empty( $bump['design_css']['top_vertical_spacing'] ) ? $bump['design_css']['top_vertical_spacing'] : '';
	$parent_bottom_vertical_spacing = ! empty( $bump['design_css']['bottom_vertical_spacing'] ) ? $bump['design_css']['bottom_vertical_spacing'] : '0';

	// DISCOUNT SECTION( discount_section ).
	$discount_section_background_color = ! empty( $bump['design_css']['discount_section_background_color'] ) ? $bump['design_css']['discount_section_background_color'] : '';
	$discount_section_text_color       = ! empty( $bump['design_css']['discount_section_text_color'] ) ? $bump['design_css']['discount_section_text_color'] : '';
	$discount_section_text_size        = ! empty( $bump['design_css']['discount_section_text_size'] ) ? $bump['design_css']['discount_section_text_size'] : '';

	// PRODUCT SECTION( product_section ).
	$product_section_text_color = ! empty( $bump['design_css']['product_section_text_color'] ) ? $bump['design_css']['product_section_text_color'] : '';
	$product_section_text_size  = ! empty( $bump['design_css']['product_section_text_size'] ) ? $bump['design_css']['product_section_text_size'] : '';
	$product_section_text_price_size  = ! empty( $bump['design_css']['product_section_price_text_size'] ) ? $bump['design_css']['product_section_price_text_size'] : '';
	$product_section_price_text_color = ! empty( $bump['design_css']['product_section_price_text_color'] ) ? $bump['design_css']['product_section_price_text_color'] : '';

	$product_section_img_width  = ! empty( $bump['design_css']['product_section_img_width'] ) ? $bump['design_css']['product_section_img_width'] : '';
	$product_section_img_height = ! empty( $bump['design_css']['product_section_img_height'] ) ? $bump['design_css']['product_section_img_height'] : '';

	// PRIMARY SECTION(primary_section ).
	$primary_section_background_color = ! empty( $bump['design_css']['primary_section_background_color'] ) ? $bump['design_css']['primary_section_background_color'] : '';
	$primary_section_text_color       = ! empty( $bump['design_css']['primary_section_text_color'] ) ? $bump['design_css']['primary_section_text_color'] : '';
	$primary_section_arrow_color      = ! empty( $bump['design_css']['primary_section_arrow_color'] ) ? $bump['design_css']['primary_section_arrow_color'] : '';
	$primary_section_text_size        = ! empty( $bump['design_css']['primary_section_text_size'] ) ? $bump['design_css']['primary_section_text_size'] : '';

	// SECONDARY SECTION( secondary_section ).
	$secondary_section_background_color = ! empty( $bump['design_css']['secondary_section_background_color'] ) ? $bump['design_css']['secondary_section_background_color'] : '';
	$secondary_section_text_color       = ! empty( $bump['design_css']['secondary_section_text_color'] ) ? $bump['design_css']['secondary_section_text_color'] : '';
	$secondary_section_text_size        = ! empty( $bump['design_css']['secondary_section_text_size'] ) ? $bump['design_css']['secondary_section_text_size'] : '';

	$wps_bump_target_attr = ! empty( $wps_ubo_global_options['wps_bump_target_link_attr_val'] ) ? $wps_ubo_global_options['wps_bump_target_link_attr_val'] : '';
	?>

	<?php $parent_border_width = 'double' === $parent_border_type ? '4px' : '2px'; ?>
	<?php
	$important = is_admin() ? '' : '!important';

	$order_bump_div_id = '#wps_upsell_offer_main_id_' . $encountered_order_bump_id;

	?>

	<!--  HTML goes down here. --> 
	<style type="text/css">
		/**
		* All of the CSS for your public-facing functionality should be
		* included in this file.
		*/
		<?php echo esc_html( $order_bump_div_id ); ?> {
			display: block;
			width: 100%;
			padding-top:  <?php echo esc_html( $parent_top_vertical_spacing ) . esc_html( 'px' ); ?>;
			padding-bottom:  <?php echo esc_html( $parent_bottom_vertical_spacing ) . esc_html( 'px' ); ?>;
			clear: both;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_parent_wrapper {
			border: <?php echo esc_html( $parent_border_type . ' ' . $parent_border_color . ' ' . $parent_border_width ); ?>;
			margin: 0 auto;
			<?php if ( 'no' === $wps_ubo_template_adaption ) : ?>
			max-width: 400px;
			<?php endif; ?>
		}
		.wps_upsell_offer_parent_wrapper {
			font-family: 'Source Sans Pro', sans-serif;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_wrapper {
			background-color:<?php echo esc_html( $parent_background_color ); ?>;
			padding : 20px;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_discount_section {
			margin: 0;
			text-align: center;
			background-color: <?php echo esc_html( $discount_section_background_color ); ?>;
			line-height: 1.68;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_discount_section h3 {
			color: <?php echo esc_html( $discount_section_text_color ); ?>;
			margin: 2px;
			padding: 1px;
			font-size: <?php echo esc_html( $discount_section_text_size ) . esc_html( 'px' ); ?>;
			border: none;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_discount_section h3 .amount {
			font-size: inherit;
			color: inherit;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_product_section {
			text-align :left;
			display: -webkit-flex;
			display: -moz-flex;
			display: -ms-flex;
			display: -o-flex;
			display: flex;
			font-size: 16px;
			align-items: start;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_product_section .wps_upsell_offer_product_description {
			margin: 0;
			color: <?php echo esc_html( $product_section_text_color ); ?>;
			font-size: <?php echo esc_html( $product_section_text_size ) . esc_html( 'px' ); ?>;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_product_content h4 {
			display: inline-block;
			vertical-align: middle;
			font-weight: 500;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_product_content p {
			white-space: pre-line;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> p.wps_upsell_offer_product_price {
			font-size: <?php echo esc_html( $product_section_text_price_size ) . esc_html( 'px' ); ?>;
			font-weight: 700;
			color: <?php echo esc_html( $product_section_price_text_color ); ?>;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> p.wps_upsell_offer_product_price del{
			font-size: <?php echo esc_html( $product_section_text_price_size ) . esc_html( 'px' ); ?>;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_product_section h4 {
			margin: 0;
			color: <?php echo esc_html( $product_section_text_color ); ?>;
			font-size: <?php echo esc_html( $product_section_text_size += 10 ) . esc_html( 'px' ); ?>;
			font-weight: 300;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_product_content {
			word-break: break-word;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_primary_section {
			align-items: center;
			background-color: <?php echo esc_html( $primary_section_background_color ); ?>;
			display: flex;
			margin: 14px auto;
			padding: 10px;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_primary_section .add_offer_in_cart_text {
			color: <?php echo esc_html( $primary_section_text_color ); ?>;
			font-size: <?php echo esc_html( $primary_section_text_size ) . esc_html( 'px' ); ?>;
			margin: 0 0 0 5px;
			font-weight: 600;
			padding: 0;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_secondary_section {
			padding: 8px;
			background-color: <?php echo esc_html( $secondary_section_background_color ); ?>;
			text-align: center;
			white-space: pre-line;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_secondary_section p {
			color: <?php echo esc_html( $secondary_section_text_color ); ?>;
			margin: 0;
			font-size:<?php echo esc_html( $secondary_section_text_size ) . esc_html( 'px' ); ?>;
		}
		/* Custom checkbox container. */
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_primary_section .wps_upsell_bump_checkbox_container {
			cursor: pointer;
			width: auto;
			font-size: 22px;
			height: 23px;
			margin: 0 0 6px 0;
			padding-left: 35px;
			position: relative;
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
		}
		/* Hide the browser's default checkbox. */
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container input {
			position: absolute;
			opacity: 0;
			cursor: pointer;
			height: 0;
			width: 0;
		}
		/* Create a custom checkbox. */
		<?php echo esc_html( $order_bump_div_id ); ?> .checkmark {
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
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_arrow {
			width: 40px;
			margin-right: 4px;
			transform: scaleX(-1);
			animation: leftright 0.4s infinite ease;
			padding: 0 2px;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_arrow svg {
			width: 100%;
			height: auto;
			/* fill: #eb483f; */
			fill: <?php echo esc_html( $primary_section_arrow_color ); ?>
		}
		@keyframes leftright {
			0% {
				transform: translateX(-5px)scaleX(-1);
			}
			60% {
				transform: translateX(-2px)scaleX(-1);
			}
			100% {
				transform: translateX(-5px)scaleX(-1);
			}
		}
		/* On mouse-over, add a grey background color. */
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container:hover input ~ .checkmark {
			background-color: #ccc;
		}
		/* When the checkbox is checked, add a blue background. */
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container input:checked ~ .checkmark {
			background-color: #ffffff;
		}
		/* Create the checkmark/indicator (hidden when not checked). */
		<?php echo esc_html( $order_bump_div_id ); ?> .checkmark:after {
			content: "";
			position: absolute;
			display: none;
		}
		/* Show the checkmark when checked. */
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container input:checked ~ .checkmark:after {
			display: block;
		}
		/* Style the checkmark/indicator. */
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container .checkmark:after {
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
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_image {
			margin-right: 10px;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_img {
			width: <?php echo esc_html( $product_section_img_width . 'px' ); ?>;
			height: <?php echo esc_html( $product_section_img_height . 'px' ); ?>;
			max-width: 200px;
			max-height: 200px;
		}

		@media only screen and (min-width : 768px) and (max-width: 1100px) {
			.wps_upsell_offer_wrapper .wps_upsell_offer_product_section {
				flex-wrap: wrap;
				justify-content: center;
			}
			.wps_upsell_offer_wrapper .wps_upsell_offer_product_content {
				width: 100%!important;
			}
		}

		@media screen and (max-width: 480px) {
			<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_product_content {
				margin-left: 0;
			}
		}

	</style>

	<?php

	// Incase no offer is added return.
	$bump['id']   = ! empty( $bump['id'] ) ? sanitize_text_field( $bump['id'] ) : '';
	$bump_product = wc_get_product( $bump['id'] );

	// If offer not found return.
	if ( empty( $bump['id'] ) || empty( $bump_product ) ) {

		return;
	}

	$bump['name'] = ! empty( $bump['name'] ) ? sanitize_text_field( $bump['name'] ) : '';

	$bump['discount_price'] = ! empty( $bump['discount_price'] ) ? sanitize_text_field( $bump['discount_price'] ) : '0';

	// After v2.0.1!
	if ( ! empty( $bump['offer_image'] ) ) {
		$image = wp_get_attachment_image_src( $bump['offer_image'], 'single-post-thumbnail' )[0];
	}

			// If still not found.
	if ( empty( $image ) ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $bump['id'] ), 'single-post-thumbnail' )[0];
	}

	if ( empty( $image ) ) {

		$bump_parent_id = $bump_product->get_parent_id();

		if ( ! empty( $bump_parent_id ) ) {

			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $bump_parent_id ), 'single-post-thumbnail' )[0];

		} else {

			$image = wc_placeholder_img_src();
		}
	}

	// Add url of the offer product in the bump info.
	$bump_offer_product_permalink = esc_url_raw( get_permalink( $bump['id'] ) );

	$check = '';

	// Retain Checked if offer is added except for admin.
	if ( ! is_admin() && function_exists( 'WC' ) && ! empty( WC()->session ) ) {

		if ( null !== WC()->session->get( "bump_offer_status_index_$order_bump_key" ) ) {
			$check = 'checked';
		}
	}

	/*
	* Get price html.
	*/
	$bump_offer_price = wps_ubo_lite_custom_price_html( $bump['id'], $bump['discount_price'] );

	/**
	 * Html for bump offer.
	 */
	$bumphtml = '';

	// parent wrapper start.
	$bumphtml .= '<div id="wps_upsell_offer_main_id_' . $encountered_order_bump_id . '" class = "wps_upsell_offer_main_wrapper wps_ubo_wrapper_index_' . $order_bump_key . '" >';

	$bumphtml .= '<input type="hidden" class ="offer_shown_id" value="' . $bump['id'] . '">';
	$bumphtml .= '<input type="hidden" class ="offer_shown_discount" value="' . $bump['discount_price'] . '">';
	$bumphtml .= '<input type="hidden" class ="target_id_cart_key" value="' . $bump['target_key'] . '">';
	$bumphtml .= '<input type="hidden" class ="order_bump_index" value="index_' . $order_bump_key . '">';
	$bumphtml .= '<input type="hidden" class ="order_bump_id" value="' . $encountered_order_bump_id . '">';

	$offer_product = wc_get_product( $bump['id'] );

	if ( ! empty( $offer_product ) && is_object( $offer_product ) && $offer_product->has_child() ) {

		$bumphtml .= '<input type="hidden" class ="offer_shown_id_type" value="variable">';
	}

	if ( ! empty( $bump['smart_offer_upgrade'] ) && 'yes' === $bump['smart_offer_upgrade'] ) {

		$bumphtml .= '<input type="hidden" class="order_bump_smo" value=' . $bump['smart_offer_upgrade'] . '>';
	}

	if ( is_admin() && ! empty( $bump['bump_price_at_zero'] ) ) :
		$bumphtml .= '<input type="hidden" class="bump_price_at_zero" value=' . $bump['bump_price_at_zero'] . '>';
	endif;

	$bumphtml                .= '<div class = "wps_upsell_offer_parent_wrapper" >';
	$bumphtml                .= '<div id = "wps_admin_timer"></div>';
	$wps_counter_timer_enable = isset( $bump['counter_timer'] ) ? $bump['counter_timer'] : '';
	$wps_evergreencounter_timer_enable = isset( $bump['evergreen_counter_timer'] ) ? $bump['evergreen_counter_timer'] : '';
	// Countdown Timer Section start.
	if ( ( 'yes' === $wps_counter_timer_enable || 'yes' == $wps_evergreencounter_timer_enable ) && wps_ubo_lite_if_pro_exists() ) {
		$bumphtml .= '<div class="expired_message_class" id = "expired_message' . esc_html( $order_bump_key ) . '"></div>';
		$bumphtml .= '<div class = "wps_timer_count wps_upsell_offer_discount_section" id ="wps_timer' . esc_html( $order_bump_key ) . '">
		<div class = "wps_day_timer_block wps-timer-wrap" >
		<div id ="wps_day_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_day_label">Days</div>
		</div>
		<div class ="wps_timer_sept">:</div>

		<div class = "wps_hour_timer_block wps-timer-wrap">
		<div id ="wps_hour_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_hour_label">Hour</div>
		</div>
		<div class ="wps_timer_sept">:</div>

		<div class = "wps_min_timer_block wps-timer-wrap">
		<div id ="wps_min_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_min_label">Min</div>
		</div>
		<div class ="wps_timer_sept">:</div>

		<div class = "wps_sec_timer_block wps-timer-wrap">
		<div id ="wps_sec_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_sec_label">Sec</div>
		</div>
		</div>';
	}
	// Countdown Timer Section End.

	// discount section start.
	$bumphtml .= '<div class = "wps_upsell_offer_discount_section" >';
	$bumphtml .= '<h3><b>' . $bump_price_html . '</b></h3>';
	$bumphtml .= '</div>';
	// discount section end.

	if ( 'fixed_q' === $wps_upsell_offer_quantity_type ) {
		$wps_is_fixed_qty = 'true';
	} else {
		$wps_is_fixed_qty = 'false';
	}
	// wrapper div start.
	$bumphtml .= '<div class = "wps_upsell_offer_wrapper" >';

	if ( 'on' === $wps_bump_enable_permalink ) {
		// product section start with permalink.
		$bumphtml .= '<div class = "wps_upsell_offer_product_section" >';
		$bumphtml .= '<div class="wps_upsell_offer_product_content"> <h4 class="wps_bump_name" data-qty_allowed="' . esc_html( $wps_upsell_enable_quantity ) . '" data-wps_is_fixed_qty="' . esc_html( $wps_is_fixed_qty ) . '" data-wps_qty="' . esc_html( $wps_upsell_bump_products_fixed_quantity ) . '"><a target="' . esc_html( $wps_bump_target_attr ) . '" class="wps_upsell_product_permalink" href="' . esc_url( $bump_offer_product_permalink ) . '">' . esc_html( $bump['name'] ) . '</a></h4><br>';
		$bumphtml .= '<div class = "wps_upsell_offer_image" >';
		$bumphtml .= '<a target="' . $wps_bump_target_attr . '" href="' . $bump_offer_product_permalink . '"><img class="wps_upsell_offer_img" src="' . esc_url( $image ) . '" data-id="' . $bump['id'] . '"></a>';
		$bumphtml .= '</div>';
		$bumphtml .= '<p class="wps_upsell_offer_product_price">' . $bump_offer_price . '</p>';
		if ( 'yes' === $wps_upsell_enable_quantity && 'variable_q' === $wps_upsell_offer_quantity_type && wps_ubo_lite_if_pro_exists() && ! $bump_product->is_type( 'variable' ) ) {
			$bumphtml .= '<label for="wps_quantity_offer">' . __( 'Quantity', 'upsell-order-bump-offer-for-woocommerce' ) . ':</label>';
			$bumphtml .= '<input class="wps_input_quantity wps_quantity_input" type="number" name="wps_quantity_offer" value="' . $wps_upsell_bump_products_min_quantity . '" min="' . $wps_upsell_bump_products_min_quantity . '" max="' . $wps_upsell_bump_products_max_quantity . '">';
		}
		$bumphtml .= '<p class="wps_upsell_offer_product_description">' . $product_description_text . '</p></div></div>';
		// Product section ends.
	} else {
		// product section start without permalink.
		$bumphtml .= '<div class = "wps_upsell_offer_product_section" >';
		$bumphtml .= '<div class="wps_upsell_offer_product_content"> <h4 class="wps_bump_name" data-qty_allowed="' . esc_html( $wps_upsell_enable_quantity ) . '" data-wps_is_fixed_qty="' . esc_html( $wps_is_fixed_qty ) . '" data-wps_qty="' . esc_html( $wps_upsell_bump_products_fixed_quantity ) . '">' . esc_html( $bump['name'] ) . '</h4><br>';
		$bumphtml .= '<div class = "wps_upsell_offer_image" >';
		$bumphtml .= '<img class="wps_upsell_offer_img" src="' . esc_url( $image ) . '" data-id="' . esc_html( $bump['id'] ) . '">';
		$bumphtml .= '</div>';
		$bumphtml .= '<p class="wps_upsell_offer_product_price">' . $bump_offer_price . '</p>';
		if ( 'yes' === $wps_upsell_enable_quantity && 'variable_q' === $wps_upsell_offer_quantity_type && wps_ubo_lite_if_pro_exists() && ! $bump_product->is_type( 'variable' ) ) {
			$bumphtml .= '<label for="wps_quantity_offer">' . __( 'Quantity', 'upsell-order-bump-offer-for-woocommerce' ) . ':</label>';
			$bumphtml .= '<input class="wps_input_quantity wps_quantity_input" type="number" name="wps_quantity_offer" value="' . $wps_upsell_bump_products_min_quantity . '" min="' . $wps_upsell_bump_products_min_quantity . '" max="' . $wps_upsell_bump_products_max_quantity . '">';
		}
		$bumphtml .= '<p class="wps_upsell_offer_product_description">' . $product_description_text . '</p></div></div>';
		// Product section ends.
	}

	// Image Product Gallery.
	$wps_product_image_slider = isset( $bump['wps_ubo_offer_product_image_slider'] ) ? $bump['wps_ubo_offer_product_image_slider'] : '';
	if ( 'yes' === $wps_product_image_slider && wps_ubo_lite_if_pro_exists() && ( ( is_cart() ) || ( is_checkout() ) ) ) {
		$bumphtml  .= wps_product_image_gallery_callback( $bump['id'] );
	}

	// Creating  red arrow html.
	if ( 'on' === $wps_enable_red_arrow_feature ) {
		$wps_ubo_red_arrow_svg  = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 198 111.52" demo="0 0 198 111.52"><defs></defs><g id="a"/><g id="b"><g id="c"><polygon class="d" points="198 25.35 198 86.17 96.62 86.17 96.62 111.52 48.36 83.64 0 55.76 48.36 27.88 96.62 0 96.62 25.35 198 25.35"/></g></g></svg>';
		$wps_ubo_red_arrow_html = '<div class="wps_upsell_offer_arrow">' . $wps_ubo_red_arrow_svg . '</div>';
	} else {
		$wps_ubo_red_arrow_html = '';
	}

	// Primary section start.
	$bumphtml .= '<div class = "wps_upsell_offer_primary_section" >';
	$bumphtml .= $wps_ubo_red_arrow_html;
	$bumphtml .= '<label class="wps_upsell_bump_checkbox_container">';
	$bumphtml .= '<input type="checkbox" ' . $check . ' name="add_offer_in_cart_checkbox" class ="add_offer_in_cart" id ="wps_checkbox_offer' . esc_html( $order_bump_key ) . '">';
	$bumphtml .= '<span class="checkmark"></span>';
	$bumphtml .= '</label>';
	$bumphtml .= '<h5 class="add_offer_in_cart_text">' . $title . '</h5>';
	$bumphtml .= '</div>';
	// Primary section end.

	// Secondary section start.
	// When don't show this when empty except for admin as it involves Live Preview.
	if ( ! empty( $description ) || is_admin() ) :
		$bumphtml .= '<div class = "wps_upsell_offer_secondary_section" ><p>' . $description . '</p></div>';
	endif;
	// Secondary section end.

	// Wrapper div end.
	$bumphtml .= '</div>';

	// Parent wrapper end.
	$bumphtml .= '</div></div>';

	// For simple product.
	if ( wps_ubo_lite_if_pro_exists() && ! empty( $offer_product ) && is_object( $offer_product ) && ! $offer_product->has_child() && ! is_admin() ) {

		$bumphtml .= apply_filters( 'wps_meta_forms_allowed_submission', $order_bump_div_id, $bump['meta_forms_allowed'], $bump['meta_form_fields'] );
	}

	return $bumphtml;
}

/**
 * Fetch Bump Offer details for offer html.
 *
 * @param      string $encountered_bump_array_index     Index of the order bump encountered.
 * @param      string $wps_upsell_bump_target_key       Cart key of target product.
 * @since    1.0.0
 */
function wps_ubo_lite_fetch_bump_offer_details( $encountered_bump_array_index, $wps_upsell_bump_target_key = '' ) {

	$wps_ubo_offer_array_collection = get_option( 'wps_ubo_bump_list' );

	if ( empty( $wps_ubo_offer_array_collection ) || empty( $wps_ubo_offer_array_collection[ $encountered_bump_array_index ] ) ) {

		return;
	}

	$encountered_bump_array = $wps_ubo_offer_array_collection[ $encountered_bump_array_index ];

	$wps_bump_upsell_selected_template = ! empty( $encountered_bump_array['wps_bump_upsell_selected_template'] ) ? sanitize_text_field( $encountered_bump_array['wps_bump_upsell_selected_template'] ) : '';
	$bump['template_check_select'] = $encountered_bump_array['wps_bump_upsell_selected_template'];

	// Countdown Timer.
	$counter_timer = ! empty( $encountered_bump_array['wps_ubo_offer_timer'] ) ? $encountered_bump_array['wps_ubo_offer_timer'] : '';

	// evergreen countdown timer.
	$evergreen_counter_timer = ! empty( $encountered_bump_array['wps_evergreen_timer_switch'] ) ? $encountered_bump_array['wps_evergreen_timer_switch'] : '';

	$product_image_gallery_slider = ! empty( $encountered_bump_array['wps_ubo_offer_product_image_slider'] ) ? $encountered_bump_array['wps_ubo_offer_product_image_slider'] : '';

	// Smart offer Upgrade.
	$smart_offer_upgrade = ! empty( $encountered_bump_array['wps_ubo_offer_replace_target'] ) ? $encountered_bump_array['wps_ubo_offer_replace_target'] : '';

	$offer_id = ! empty( $encountered_bump_array['wps_upsell_bump_products_in_offer'] ) ? sanitize_text_field( $encountered_bump_array['wps_upsell_bump_products_in_offer'] ) : '';

	$discount_price = ! empty( $encountered_bump_array['wps_upsell_bump_offer_discount_price'] ) ? sanitize_text_field( $encountered_bump_array['wps_upsell_bump_offer_discount_price'] ) : '';

	$_product = wc_get_product( $offer_id );

	if ( empty( $_product ) ) {

		return;
	}

	$price              = $_product->get_price();
	$price_type         = $encountered_bump_array['wps_upsell_offer_price_type'];
	$price_discount     = $encountered_bump_array['wps_upsell_bump_offer_discount_price'];
	$meta_forms_allowed = ! empty( $encountered_bump_array['wps_ubo_offer_meta_forms'] ) ? $encountered_bump_array['wps_ubo_offer_meta_forms'] : 'no';
	$meta_form_fields   = ! empty( $encountered_bump_array['meta_form_fields'] ) ? $encountered_bump_array['meta_form_fields'] : array();

	// after v2.0.1!
	$wps_upsell_offer_image = ! empty( $encountered_bump_array['wps_upsell_offer_image'] ) ? $encountered_bump_array['wps_upsell_offer_image'] : '';

	$bump = ! empty( $bump ) ? $bump : array();

	// Smart offer upgrade.
	if ( 'yes' === $smart_offer_upgrade ) {

		$bump['smart_offer_upgrade'] = 'yes';
	}

	// Countdown Timer.
	if ( 'yes' === $counter_timer ) {

		$bump['counter_timer'] = 'yes';
	}
	// Evergreen Timer.
	if ( 'yes' === $evergreen_counter_timer ) {

		$bump['evergreen_counter_timer'] = 'yes';
	}

	// Product Image Gallery.
	if ( 'yes' === $product_image_gallery_slider ) {

		$bump['wps_ubo_offer_product_image_slider'] = 'yes';
	}

	if ( is_wps_role_based_pricing_active() ) {
		if ( 'no_disc' !== $price_type ) {
			$bump_discount          = $price_discount . '+' . $price_type;
			$bump['discount_price'] = wps_ubo_lite_custom_price_html( $offer_id, $bump_discount, 'price' );
			$_product->set_price( $bump['discount_price'] );
		} else {
			$prod_obj               = wc_get_product( $offer_id );
			$prod_type              = $prod_obj->get_type();
			$bump['discount_price'] = wps_mrbpfw_role_based_price( $prod_obj->get_price(), $prod_obj, $prod_type );
			$bump['discount_price'] = wp_strip_all_tags( str_replace( get_woocommerce_currency_symbol(), '', $bump['discount_price'] ) );
			$_product->set_price( $bump['discount_price'] );
		}
	} else {
		if ( 'no_disc' !== $price_type ) {
			$bump_discount          = $price_discount . '+' . $price_type;
			$bump['discount_price'] = wps_ubo_lite_custom_price_html( $offer_id, $bump_discount, 'price' );
			$_product->set_price( $bump['discount_price'] );
		}
	}

	$price_excl_tax = wc_get_price_excluding_tax( $_product );  // Price without tax.
	$price_incl_tax = wc_get_price_including_tax( $_product );  // Price with tax.

	// Got all details.
	$bump['id']                         = $offer_id;
	$bump['offer_type']                 = $offer_id;
	$bump['offer_image']                = $wps_upsell_offer_image;
	$bump['target_key']                 = $wps_upsell_bump_target_key;
	$bump['name']                       = get_the_title( $bump['id'] );
	$bump['discount_price_without_tax'] = $price_excl_tax;
	$bump['discount_price_with_tax']    = $price_incl_tax;
	$bump['price_type']                 = $price_type;
	$bump['meta_forms_allowed']         = $meta_forms_allowed;
	$bump['meta_form_fields']           = $meta_form_fields;

	if ( isset( $wps_ubo_offer_array_collection[ $encountered_bump_array_index ]['wps_upsell_enable_quantity'] ) ) {
		$bump['wps_upsell_enable_quantity']              = $wps_ubo_offer_array_collection[ $encountered_bump_array_index ]['wps_upsell_enable_quantity'];
		$bump['wps_upsell_bump_products_fixed_quantity'] = $wps_ubo_offer_array_collection[ $encountered_bump_array_index ]['wps_upsell_bump_products_fixed_quantity'];
		$bump['wps_upsell_bump_products_min_quantity']   = $wps_ubo_offer_array_collection[ $encountered_bump_array_index ]['wps_upsell_bump_products_min_quantity'];
		$bump['wps_upsell_bump_products_max_quantity']   = $wps_ubo_offer_array_collection[ $encountered_bump_array_index ]['wps_upsell_bump_products_max_quantity'];
		$bump['wps_upsell_offer_quantity_type']          = $wps_ubo_offer_array_collection[ $encountered_bump_array_index ]['wps_upsell_offer_quantity_type'];
	}

	$bump['design_text'] = ! empty( $encountered_bump_array['design_text'] ) ? $encountered_bump_array['design_text'] : array();

	$wps_bump_upsell_selected_template = ! empty( $encountered_bump_array['wps_bump_upsell_selected_template'] ) ? sanitize_text_field( $encountered_bump_array['wps_bump_upsell_selected_template'] ) : '';

	if ( ! empty( $wps_bump_upsell_selected_template ) ) {

		// Load the css of selected template.
		$template_callb_func = 'wps_ubo_lite_offer_template_' . $wps_bump_upsell_selected_template;

		$wps_bump_enable_available_design = $template_callb_func( $wps_bump_upsell_selected_template );

		$bump['design_css'] = $wps_bump_enable_available_design;
	} else {

		$bump['design_css'] = $encountered_bump_array['design_css'];
	}

	if ( is_wps_role_based_pricing_active() && ( 'no_disc' === $price_type ) ) {
		$prod_obj       = wc_get_product( $offer_id );
		$prod_type      = $prod_obj->get_type();
		$discount_price = wps_mrbpfw_role_based_price( $prod_obj->get_price(), $prod_obj, $prod_type );
	}

	if ( '%' === $price_type ) {

		if ( empty( $discount_price ) ) {

			$discount_price = 0;
		}

		$bump['bump_price_html'] = $discount_price . '%';

	} else {

		if ( 'incl' === get_option( 'woocommerce_tax_display_cart' ) ) {

			$inclusive = true;
		}

		if ( ! empty( $inclusive ) ) {

			$bump['bump_price_html']    = wc_price( $bump['discount_price_with_tax'] );
			$bump['bump_price_at_zero'] = $bump['discount_price_with_tax'];

		} else {

			$bump['bump_price_html']    = wc_price( $bump['discount_price_without_tax'] );
			$bump['bump_price_at_zero'] = $bump['discount_price_without_tax'];
		}
	}

	// Get the price criteria to set.
	$bump['discount_price'] = $discount_price . '+' . $price_type;

	return $bump;

}

/**
 * Retrieve Bump Offer location details.
 *
 * @param   string $key         The keyword for hook where to implement the order bump.
 * @since   1.0.0.
 */
function wps_ubo_lite_retrieve_bump_location_details( $key = '_after_payment_gateways' ) {

	$wps_plugin_list = get_option( 'active_plugins' );
	$wps_is_pro_active = false;
	$wps_plugin = 'checkout-for-woocommerce/checkout-for-woocommerce.php';
	if ( in_array( $wps_plugin, $wps_plugin_list ) ) {
		$wps_is_pro_active = true;
	}

	if ( ! $wps_is_pro_active ) {

		$location_details = array(
			'_before_order_summary'      => array(
				'hook'     => 'woocommerce_checkout_order_review',
				'priority' => 9,
				'name'     => esc_html__( 'Before Order Summary', 'upsell-order-bump-offer-for-woocommerce' ),
			),
			'_before_payment_gateways'   => array(
				'hook'     => 'woocommerce_checkout_order_review',
				'priority' => 11,
				'name'     => esc_html__( 'Before Payment Gateways', 'upsell-order-bump-offer-for-woocommerce' ),
			),
			'_after_payment_gateways'    => array(
				'hook'     => 'wps_ubo_after_pg_before_terms',
				'priority' => 19,
				'name'     => esc_html__( 'After Payment Gateways', 'upsell-order-bump-offer-for-woocommerce' ),
			),
		);

		// Check if the Stripe plugin is active.
		if ( is_plugin_active( 'woocommerce-paypal-payments/woocommerce-paypal-payments.php' ) ) {
			// If active, add the hook for Paypal.
			$location_details['_before_place_order_button'] = array(
				'hook'     => 'woocommerce_review_order_after_payment',
				'priority' => 10,
				'name'     => esc_html__( 'Before Place order button', 'upsell-order-bump-offer-for-woocommerce' ),
			);

		} else {
			// If not active, add the default hook.
			$location_details['_before_place_order_button'] = array(
				'hook'     => 'woocommerce_review_order_before_submit',
				'priority' => 21,
				'name'     => esc_html__( 'Before Place order button', 'upsell-order-bump-offer-for-woocommerce' ),
			);
		}
	} else {   // Code For Comapatibility With CheckoutWC plugin.
		$location_details = array(
			'_before_order_summary'      => array(
				'hook'     => 'cfw_checkout_cart_summary',
				'priority' => 10,
				'name'     => esc_html__( 'Before Order Summary', 'upsell-order-bump-offer-for-woocommerce' ),
			),
			'_before_payment_gateways'   => array(
				'hook'     => 'cfw_before_payment_method_heading',
				'priority' => 11,
				'name'     => esc_html__( 'Before Payment Gateways', 'upsell-order-bump-offer-for-woocommerce' ),
			),
			'_after_payment_gateways'    => array(
				'hook'     => 'cfw_checkout_after_payment_methods',
				'priority' => 19,
				'name'     => esc_html__( 'After Payment Gateways', 'upsell-order-bump-offer-for-woocommerce' ),
			),
			'_before_place_order_button' => array(
				'hook'     => 'woocommerce_review_order_before_submit',
				'priority' => 21,
				'name'     => esc_html__( 'Before Place order button', 'upsell-order-bump-offer-for-woocommerce' ),
			),
		);
	}

	return $location_details[ $key ];

}

/**
 * Retrieve Bump Offer location details For the plugin Germanized for WooCommerce.
 *
 * @param   string $key         The keyword for hook where to implement the order bump.
 * @since   1.0.0.
 */
function wps_ubo_lite_retrieve_bump_location_details_for_wc_germanized_compatibility( $key = '_before_place_order_button' ) {

	$location_details = array(
		'_before_order_summary'      => array(
			'hook'     => 'woocommerce_checkout_order_review',
			'priority' => 10,
			'name'     => esc_html__( 'Before Order Summary', 'upsell-order-bump-offer-for-woocommerce' ),
		),
		'_before_payment_gateways'   => array(
			'hook'     => 'woocommerce_checkout_order_review',
			'priority' => 11,
			'name'     => esc_html__( 'Before Payment Gateways', 'upsell-order-bump-offer-for-woocommerce' ),
		),
		'_after_payment_gateways'    => array(
			'hook'     => 'woocommerce_review_order_after_payment', // here is the changes.
			'priority' => 19,
			'name'     => esc_html__( 'After Payment Gateways', 'upsell-order-bump-offer-for-woocommerce' ),
		),
		'_before_place_order_button' => array(
			'hook'     => 'woocommerce_gzd_review_order_before_submit',
			'priority' => 10,
			'name'     => esc_html__( 'Before Place order button', 'upsell-order-bump-offer-for-woocommerce' ),
		),
	);

	return $location_details[ $key ];
}

/**
 * Function for search through category.
 *
 * @param   string $category_target_id          The category id we are looking for order bump.
 * @since   1.0.0
 */
function wps_ubo_lite_check_category_in_cart( $category_target_id = '' ) {

	// First get required category is parent or child.
	$target_parent_id = wps_ubo_lite_category_has_parent( $category_target_id );

	// Start of the loop that fetches the cart items.
	foreach ( WC()->cart->get_cart() as $cart_key => $cart_item_object ) {

		$product    = $cart_item_object['data'];
		$product_id = ! empty( $product->get_parent_id() ) ? $product->get_parent_id() : $product->get_id();
		$terms      = get_the_terms( $product_id, 'product_cat' );

		if ( ! empty( $terms ) ) {

			// Second level loop search, in case some items have several categories.
			foreach ( $terms as $term ) {

				// Category id is the product category.
				$category_id        = $term->term_id;
				$category_parent_id = $term->parent;

				if ( 'is_parent' === $target_parent_id && (string) $category_target_id === (string) $category_parent_id ) {

					// Child Category is in cart!
					return $cart_key;
				}

				// Case to trigger category for child or parent with no child category itself.
				if ( ! empty( $category_target_id ) && ( (string) $category_id === (string) $category_target_id ) ) {

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
 * @param   string $cat_id      The category id we need to check.
 * @since   1.0.0
 */
function wps_ubo_lite_category_has_parent( $cat_id ) {

	/**
	 * Custom category are not handled as default taxonomies (category).
	 * They are under product_cat terms so we get all the category and fetch the
	 * required one.
	 * After that check if it is parent of not.
	 */
	$args = array(
		'taxonomy' => 'product_cat',
	);

	$product_categories = get_terms( $args );
	$category_parent    = 'is_parent';

	foreach ( $product_categories as $key => $single_category ) {

		if ( (string) $cat_id === (string) $single_category->term_id ) {

			// Required category!
			if ( ! empty( $single_category->parent ) ) {

				$category_parent = $single_category->parent;

			}

			return $category_parent;
		}
	}
}

/**
 * Function for prepare bump data in a single array.
 *
 * @param   string $product_id          The target product id to search in cart.
 * @since   1.0.0
 */
function wps_ubo_lite_check_if_in_cart( $product_id = '' ) {

	$product        = wc_get_product( $product_id );       // Offer product to be checked.
	$target_product = array();

	if ( ! empty( $product ) && $product->is_type( 'variable' ) ) {

		$available_variations = $product->get_available_variations();

		foreach ( $available_variations as $key => $value ) {

			foreach ( $value as $k => $v ) {

				if ( 'variation_id' === $k ) {

					foreach ( WC()->cart->get_cart() as $key => $val ) {

						$_product = $val['data'];

						if ( (string) $v === (string) $_product->get_id() && empty( $val['wps_discounted_price'] ) ) {

							return $key;
						}
					}
				}
			}
		}
	}

	if ( ! empty( $product_id ) ) {

		$session = WC()->session;

		if ( $session ) {
			// When a single variation or simple product are present in bumps array.
			foreach ( WC()->cart->get_cart() as $key => $val ) {

				$_product = $val['data'];

				if ( (string) $product_id === (string) $_product->get_id() && empty( $val['wps_discounted_price'] ) ) {
					return $key;
				}
			}
		}
	}
}

/**
 * Function to check similiar offer already present in cart.
 *
 * @param   string $offer_id        The offer product id if already present in cart.
 * @since   1.0.0
 */
function wps_ubo_lite_already_in_cart( $offer_id ) {

	$offer_product = wc_get_product( $offer_id );

	if ( empty( $offer_product ) ) {

		return false;
	}

	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

		// If offer product is a variable product.
		if ( $offer_product->has_child() ) {

			// If any variation of this parent is present return present.
			if ( ( (string) $cart_item['product_id'] === (string) $offer_id ) && empty( $cart_item['wps_discounted_price'] ) ) {

				return true;
			}
		} else if ( ! $offer_product->has_child() ) {

			// If offer id is variation or simple product.
			if ( ( ( (string) $cart_item['product_id'] === (string) $offer_id ) || ( (string) $cart_item['variation_id'] === (string) $offer_id ) ) && empty( $cart_item['wps_discounted_price'] ) ) {

				// return true on same offer.
				return true;
			}
		}
	}

	return false;
}

/**
 * Adding html for the popup to show variations.
 *
 * @param   object $product           The variable parent product.
 * @param   string $order_bump_index  The variable parent product.
 * @param   array  $meta_form_attr     The variable meta form.
 * @since   1.0.0
 */
function wps_ubo_lite_show_variation_popup( $product = '', $order_bump_index = '', $meta_form_attr = array() ) {

	$wps_all_bumps_to_check_quantity_setting = get_option( 'wps_ubo_bump_list', array() );

	// Setting for the offer Quantity.
	$wps_upsell_enable_quantity              = ! empty( $wps_all_bumps_to_check_quantity_setting[ $order_bump_index ]['wps_upsell_enable_quantity'] ) ? $wps_all_bumps_to_check_quantity_setting[ $order_bump_index ]['wps_upsell_enable_quantity'] : '';
	$wps_upsell_bump_products_fixed_quantity = ! empty( $wps_all_bumps_to_check_quantity_setting[ $order_bump_index ]['wps_upsell_bump_products_fixed_quantity'] ) ? $wps_all_bumps_to_check_quantity_setting[ $order_bump_index ]['wps_upsell_bump_products_fixed_quantity'] : '';
	$wps_upsell_bump_products_min_quantity   = ! empty( $wps_all_bumps_to_check_quantity_setting[ $order_bump_index ]['wps_upsell_bump_products_min_quantity'] ) ? $wps_all_bumps_to_check_quantity_setting[ $order_bump_index ]['wps_upsell_bump_products_min_quantity'] : '';
	$wps_upsell_bump_products_max_quantity   = ! empty( $wps_all_bumps_to_check_quantity_setting[ $order_bump_index ]['wps_upsell_bump_products_max_quantity'] ) ? $wps_all_bumps_to_check_quantity_setting[ $order_bump_index ]['wps_upsell_bump_products_max_quantity'] : '';
	$wps_upsell_offer_quantity_type          = ! empty( $wps_all_bumps_to_check_quantity_setting[ $order_bump_index ]['wps_upsell_offer_quantity_type'] ) ? $wps_all_bumps_to_check_quantity_setting[ $order_bump_index ]['wps_upsell_offer_quantity_type'] : '';

	if ( 'fixed_q' === $wps_upsell_offer_quantity_type ) {
		$wps_is_fixed_qty = 'true';
	} else {
		$wps_is_fixed_qty = 'false';
	}

	?>
	<!-- HTML for loader starts. -->
	<div class= "wps_bump_popup_loader">
		<img src= <?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'public/resources/icons/loader.svg' ); ?>>
	</div>
	<!-- HTML for loader ends. -->

	<!-- HTML for popup wrapper starts. -->	
	<div class="wps_bump_popup_wrapper wps_bump_popup_<?php echo esc_html( $order_bump_index ); ?>">

		<!-- HTML for popup content wrapper starts. -->	
		<div class="wps_bump_popup_content">

			<!-- Inner custom wrapper starts. -->
			<div class="wps_bump_popup_inner_content">

				<!-- Close button starts. -->
				<span class="wps_bump_popup_close" offer_bump_index = "<?php echo esc_html( $order_bump_index ); ?>" class="variation_id_selected" >
					<img src= <?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'public/resources/icons/close.png' ); ?>>
				</span>
				<!-- Close button ends. -->

				<!-- Product Image starts. -->
				<div class="wps_bump_popup_image" >
					<?php $allowed_html = wps_ubo_lite_allowed_html(); ?>
					<?php echo wp_kses( wps_ubo_lite_get_bump_image( $product->get_id() ), $allowed_html ); ?>
				</div>
				<!-- Product Image ends. -->

				<!-- Variation Select starts. -->
				<div class="wps_bump_popup_select">
					<p class="bump_variable_product_title wps_bump_name" data-qty_allowed="<?php echo esc_attr( $wps_upsell_enable_quantity ); ?>" data-wps_is_fixed_qty="<?php echo esc_attr( $wps_is_fixed_qty ); ?>" data-wps_qty="<?php echo esc_attr( $wps_upsell_bump_products_fixed_quantity ); ?>" >
						<?php echo esc_html( $product->get_title() ); ?>
					</p>

					<?php
						$attributes = $product->get_variation_attributes();
						// Return if no attributes are present.
					if ( empty( $attributes ) ) {
						return;
					}

					$default_attributes = $product->get_default_attributes();

					?>

					<!-- Selected variation by selected fields. -->
					<input type="hidden" offer_bump_index = "<?php echo esc_html( $order_bump_index ); ?>" class="variation_id_selected" value="" />

					<!-- Notification arena. -->
					<span class="wps_ubo_err_waring_for_variation" offer_bump_index = <?php echo esc_html( $order_bump_index ); ?> ></span>
					<span class="wps_ubo_price_html_for_variation" offer_bump_index = <?php echo esc_html( $order_bump_index ); ?> ></span>

					<!-- Printing all the variation dropdown. -->
					<?php foreach ( $attributes as $attribute_name => $options ) : ?>

						<div class="wps_ubo_input_row">
							<p class="wps_ubo_bump_attributes_name">

								<!-- In case slug is encountered. -->
								<?php $show_title = str_replace( 'pa_', '', $attribute_name ); ?>
								<?php $attribute_name = str_replace( ' ', '-', $attribute_name ); ?>
								<?php echo esc_html( ucfirst( $show_title ) ); ?>
								<?php $selected_attribute = ! empty( $default_attributes[ strtolower( $attribute_name ) ] ) ? $default_attributes[ strtolower( $attribute_name ) ] : ''; ?>
							</p>

							<?php
								// Function to return variations select html.
								$variation_dropdown = wps_ubo_lite_show_variation_dropdown(
									array(
										'options'          => $options,
										'attribute'        => $attribute_name,
										'product'          => $product,
										'selected'         => $selected_attribute,
										'id'               => 'attribute_' . strtolower( $attribute_name ),
										'class'            => 'wps_upsell_offer_variation_select',
										'order_bump_index' => $order_bump_index,
									)
								);

								// Added custom attribute hence restrict the.
								echo wp_kses( $variation_dropdown, $allowed_html );
							?>
						</div>

					<?php endforeach; ?>

					<?php apply_filters( 'wps_meta_variable_forms_allowed_submission', $order_bump_index, $meta_form_attr ); ?>

					<?php
					if ( 'yes' === $wps_upsell_enable_quantity && 'variable_q' === $wps_upsell_offer_quantity_type && wps_ubo_lite_if_pro_exists() ) {
						echo wp_kses( '<label for="wps_quantity_offer">' . __( 'Quantity', 'upsell-order-bump-offer-for-woocommerce' ) . ':</label>', $allowed_html );
						echo wp_kses( '<input class="wps_input_quantity wps_quantity_input" type="number" name="wps_quantity_offer" value="' . $wps_upsell_bump_products_min_quantity . '" min="' . $wps_upsell_bump_products_min_quantity . '" max="' . $wps_upsell_bump_products_max_quantity . '">', $allowed_html );
					}
					?>
					<!-- Add to cart button starts. -->
					<button name="add-to-cart" class="single_add_to_cart_button button alt wps_ubo_bump_add_to_cart_button" offer_bump_index = <?php echo esc_html( $order_bump_index ); ?> >
						<?php esc_html_e( 'Add this offer to cart', 'upsell-order-bump-offer-for-woocommerce' ); ?>
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
 * @param   object $product_id          Id of product for which image is to be shown.
 * @since   1.0.0
 */
function wps_ubo_lite_get_bump_image( $product_id = '' ) {

	// Get product thumbnail id.
	$image_id = get_post_thumbnail_id( $product_id );

	if ( ! empty( $image_id ) ) {

		// Get image with complete html for adding zooming effect( Variation ).
		$bump_var_image = wc_get_gallery_image_html( $image_id, true );

	} else {

		$variation_product = wc_get_product( $product_id );

		if ( ! empty( $variation_product ) ) {

			$image_id = get_post_thumbnail_id( $variation_product->get_parent_id() );

			if ( ! empty( $image_id ) ) {

				// Get image with complete html for adding zooming effect( Parent image ).
				$bump_var_image = wc_get_gallery_image_html( $image_id, true );

			} else {

				// If no image is present return the woocommerce place holder image.
				$bump_var_image = wc_placeholder_img();
			}
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
 * @param   array $args     Arguments according to which dropdown has to be made.
 * @since   1.0.0
 */
function wps_ubo_lite_show_variation_dropdown( $args = array() ) {

	$args = wp_parse_args(
		apply_filters( 'woocommerce_dropdown_variation_attribute_options_args', $args ),
		array(
			'options'          => false,
			'attribute'        => false,
			'order_bump_index' => false,
			'selected'         => false,
			'name'             => '',
			'id'               => '',
			'class'            => '',
			'show_option_none' => false,
		)
	);

	$order_bump_index      = ! empty( $args['order_bump_index'] ) ? $args['order_bump_index'] : '0';
	$options               = $args['options'];
	$product               = $args['product'];
	$attribute             = $args['attribute'];
	$name                  = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
	$id                    = $args['id'] ? sanitize_title( $args['id'] ) : sanitize_title( $attribute );
	$class                 = $args['class'];
	$show_option_none      = $args['show_option_none'] ? true : false;
	$show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : esc_html__( 'Choose an option', 'upsell-order-bump-offer-for-woocommerce' );

	if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
		$attributes = $product->get_variation_attributes();
		$options    = $attributes[ $attribute ];
	}

	$html = '<select order_bump_index="' . $order_bump_index . '" order="' . esc_attr( $id ) . '" id="' . esc_attr( $id ) . '" class="' . esc_attr( sanitize_title( $class ) ) . '" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '">';

	$html .= '<option value="">' . esc_html( $show_option_none_text ) . '</option>';

	if ( ! empty( $options ) ) {

		if ( $product && taxonomy_exists( $attribute ) ) {

			// Get terms if this is a taxonomy - ordered. We need the names too.
			$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );

			foreach ( $terms as $term ) {

				if ( in_array( $term->slug, $options, true ) ) {
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

/*
============================================================================
							// compatibility with role based plugin.
============================================================================
*/


/**
 * Function to get the all rule ids depending upon the current user role.
 */
function wps_mrbpfw_get_all_rule_ids_for_current_role() {
	// Logic for calculating the new price here.
	if ( ! is_user_logged_in() ) {
		$current_role = array( 'guest' );
	} else {
		$get_current_user_role = wp_get_current_user();
		$current_role          = $get_current_user_role->roles;
	}
	$current_role_val = 0;
	$all_rules_ids = get_posts(
		array(
			'fields'         => 'ids',
			'posts_per_page' => 10,
			'post_type'      => 'mrbpfw_price_rules',
			'meta_query'     => array(
				array(
					'key'     => 'wps_mrbpfw_role',
					'value'   => $current_role[ $current_role_val ],
					'compare' => '==',
				),
				array(
					'key'     => 'wps_mrbpfw_enable_rule',
					'value'   => 'on',
					'compare' => '==',
				),
			),
		)
	);
	return $all_rules_ids;
}


/**
 * Get Total discount for the products
 *
 * @param object $product .
 * @param string $new_price .
 */
function wps_mrbpfw_get_discount_price( $product, $new_price ) {
	$all_rules_ids         = wps_mrbpfw_get_all_rule_ids_for_current_role();
	$priority_wise_rule_id = array();
	$total_discount        = '';
	if ( isset( $all_rules_ids ) && ! empty( $all_rules_ids ) && ! empty( $new_price ) ) {
		foreach ( $all_rules_ids as $key => $id ) {
			$priority                           = get_post_meta( $id, 'wps_mrbpfw_priority', true );
			$priority_wise_rule_id[ $priority ] = $id;
		}
		ksort( $priority_wise_rule_id );
		$count = 0;
		foreach ( $priority_wise_rule_id as $key => $id ) {
			++$count;
			$rule_type       = get_post_meta( $id, 'wps_mrbpfw_rule_type', true );
			$discount_type   = get_post_meta( $id, 'wps_mrbpfw_discount_type', true );
			$price           = get_post_meta( $id, 'wps_mrbpfw_price', true );
			$all_product_ids = get_post_meta( $id, 'wps_mrbpfw_all_products', true );
			floatval( $price );
			if ( $product->is_type( 'variable' ) ) {
				$product_id = $product->get_parent_id();
			} elseif ( $product->is_type( 'variation' ) ) {
				$product_id = $product->get_parent_id();
			} elseif ( $product->is_type( 'simple' ) ) {
				$product_id = $product->get_id();
			}
			if ( isset( $rule_type ) && ! empty( $rule_type ) && 'all_products' === $rule_type && ( empty( $all_product_ids ) || ( ! empty( $all_product_ids ) && in_array( $product_id, $all_product_ids, false ) ) ) ) {
				if ( isset( $price ) && ! empty( $price ) ) {
					if ( 'fixed' === $discount_type ) {
						$total_discount = $price;
					} elseif ( 'percentage' === $discount_type ) {
						$per_price = ( $new_price * $price ) / 100;
						round( $per_price, 2 );
						$total_discount = $per_price;
					}
				}
			} elseif ( isset( $rule_type ) && ! empty( $rule_type ) && 'categories' === $rule_type ) {
				$categories = get_post_meta( $id, 'wps_mrbpfw_categories', true );
				if ( isset( $categories ) && ! empty( $categories ) ) {
					if ( has_term( $categories, 'product_cat', $product->get_parent_id() ) ) {
						if ( isset( $price ) && ! empty( $price ) ) {
							if ( 'fixed' === $discount_type ) {
								$total_discount = $price;
							} elseif ( 'percentage' === $discount_type ) {
								$per_price = ( $new_price * $price ) / 100;
								round( $per_price, 2 );
								$total_discount = $per_price;
							}
						}
					}
				}
			} elseif ( isset( $rule_type ) && ! empty( $rule_type ) && 'tags' === $rule_type ) {
				$tags = get_post_meta( $id, 'wps_mrbpfw_tags', true );
				if ( isset( $tags ) && ! empty( $tags ) ) {
					if ( has_term( $tags, 'product_tag', $product->get_parent_id() ) ) {
						if ( isset( $price ) && ! empty( $price ) ) {
							if ( 'fixed' === $discount_type ) {
								$total_discount = $price;
							} elseif ( 'percentage' === $discount_type ) {
								$per_price = ( $new_price * $price ) / 100;
								round( $per_price, 2 );
								$total_discount = $per_price;
							}
						}
					}
				}
			}
			if ( 1 === $count ) {
				break;
			}
		}
	}
	return $total_discount;
}

/**
 * Function to change the product price for the product
 *
 * @param string $original_price is current product product price.
 * @param object $product is the current product object.
 * @param object $type .
 * @return $new_price which can be modify price or original price based the conditions
 */
function wps_mrbpfw_role_based_price( $original_price, $product, $type ) {
	if ( ! is_user_logged_in() ) {
		$current_role = array( 'guest' );
	} else {
		$get_current_user_role = wp_get_current_user();
		$current_role          = $get_current_user_role->roles;
	}
	if ( class_exists( 'WC_Subscriptions_Product' ) && $product->is_subscription() ) {
		return $original_price;
	}
	$current_role_val = 0;
	$rule_apply  = get_option( 'wps_mrbpfw_for_price_rule', false );
	$get_options = get_option( 'user_setting_' . $current_role[ $current_role_val ], false );
	if ( 'simple' === $product->get_type() ) {
		if ( 'r_price' === $rule_apply ) {
			$new_price = $product->get_regular_price();
		} elseif ( 's_price' === $rule_apply ) {
			$new_price = $product->get_sale_price();
		}
	} elseif ( 'variable' === $product->get_type() ) {
		if ( 'r_price' === $rule_apply ) {
			$new_price = $product->get_regular_price();
		} elseif ( 's_price' === $rule_apply ) {
			$new_price = $product->get_sale_price();
		}
	} elseif ( 'variation' === $product->get_type() ) {
		if ( 'r_price' === $rule_apply ) {
			$new_price = $product->get_regular_price();
		} elseif ( 's_price' === $rule_apply ) {
			$new_price = $product->get_sale_price();
		}
	} else {
		return $original_price;
	}
	if ( empty( $new_price ) ) {
		return $original_price;
	}
	// Logic for calculating the new price here.
	$total_discount = wps_mrbpfw_get_discount_price( $product, $new_price );
	if ( empty( $new_price ) && $new_price < 0 ) {
		return wc_price( 0 );
	}
	$args_saleprice = wp_parse_args(
		array(
			'qty'   => '',
			'price' => $product->get_sale_price(),
		)
	);
	$args_regularprice = wp_parse_args(
		array(
			'qty'   => '',
			'price' => $product->get_regular_price(),
		)
	);
	if ( isset( $rule_apply ) && 's_price' === $rule_apply && $product->is_on_sale() ) {
		$sale_price_excl_tax    = wc_get_price_excluding_tax( $product, $args_saleprice );
		$sale_price_incl_tax    = wc_get_price_including_tax( $product, $args_saleprice );
		$regular_price_excl_tax = wc_get_price_excluding_tax( $product, $args_regularprice );
		$regular_price_incl_tax = wc_get_price_including_tax( $product, $args_regularprice );
	} elseif ( isset( $rule_apply ) && 'r_price' === $rule_apply && ( $product->is_on_sale() || ! $product->is_on_sale() ) ) {
		if ( $product->is_on_sale() ) {
			$sale_price_excl_tax = wc_get_price_excluding_tax( $product, $args_saleprice );
			$sale_price_incl_tax = wc_get_price_including_tax( $product, $args_saleprice );
		}
		$regular_price_excl_tax = wc_get_price_excluding_tax( $product, $args_regularprice );
		$regular_price_incl_tax = wc_get_price_including_tax( $product, $args_regularprice );
	}
	$current_role_val = 0;
	if ( isset( $get_options ) && ! empty( $get_options ) && in_array( 'show_tax_' . $current_role[ $current_role_val ], $get_options, true ) ) {
		if ( isset( $rule_apply ) && 's_price' === $rule_apply ) {
			if ( $product->is_on_sale() ) {
				if ( isset( $total_discount ) && ! empty( $total_discount ) ) {
					$role_based_pricing = $sale_price_incl_tax - $total_discount;
				}
				$sale_price    = $sale_price_incl_tax;
				$regular_price = $regular_price_incl_tax;
			}
		} elseif ( isset( $rule_apply ) && 'r_price' === $rule_apply ) {
			if ( $product->is_on_sale() || ! $product->is_on_sale() ) {
				if ( isset( $total_discount ) && ! empty( $total_discount ) ) {
					$role_based_pricing = $regular_price_incl_tax - $total_discount;
				}
				if ( $product->is_on_sale() ) {
					$sale_price = $sale_price_incl_tax;
				}
				$regular_price = $regular_price_incl_tax;
			}
		}
	} else {
		if ( isset( $rule_apply ) && 's_price' === $rule_apply ) {
			if ( $product->is_on_sale() ) {
				if ( isset( $total_discount ) && ! empty( $total_discount ) ) {
					$role_based_pricing = $sale_price_excl_tax - $total_discount;
				}
				$sale_price    = $sale_price_excl_tax;
				$regular_price = $regular_price_excl_tax;
			}
		} elseif ( isset( $rule_apply ) && 'r_price' === $rule_apply ) {
			if ( $product->is_on_sale() || ! $product->is_on_sale() ) {
				if ( isset( $total_discount ) && ! empty( $total_discount ) ) {
					$role_based_pricing = $regular_price_excl_tax - $total_discount;
				}
				if ( $product->is_on_sale() ) {
					$sale_price = $sale_price_excl_tax;
				}
				$regular_price = $regular_price_excl_tax;
			}
		}
	}
	$tax_enable = get_option( 'woocommerce_calc_taxes', false );
	$tax_label  = '';
	if ( 'yes' === $tax_enable ) {
		if ( isset( $get_options ) && ! empty( $get_options ) && in_array( 'show_tax_' . $current_role[ $current_role_val ], $get_options, true ) ) {
			$tax_label = apply_filters( 'wps_mrbpfw_tax_lable', esc_html__( 'incl.VAT', 'upsell-order-bump-offer-for-woocommerce' ) );
		} else {
			$tax_label = apply_filters( 'wps_mrbpfw_tax_lable', esc_html__( 'excl.VAT', 'upsell-order-bump-offer-for-woocommerce' ) );
		}
	}
	if ( isset( $role_based_pricing ) && $role_based_pricing < 0 ) {
		return wc_price( 0 );
	}
	if ( function_exists( 'wps_mmcsfw_admin_fetch_currency_rates_from_base_currency' ) && ! is_admin() ) {
		if ( isset( $role_based_pricing ) ) {
			$role_based_pricing = wps_mmcsfw_admin_fetch_currency_rates_from_base_currency( '', $role_based_pricing );
		}
		if ( isset( $sale_price ) ) {
			$sale_price = wps_mmcsfw_admin_fetch_currency_rates_from_base_currency( '', $sale_price );
		}
		if ( isset( $regular_price ) ) {
			$regular_price = wps_mmcsfw_admin_fetch_currency_rates_from_base_currency( '', $regular_price );
		}
	}
	$current_role_val = 0;
	if ( isset( $total_discount ) && ! empty( $total_discount ) && ! empty( $get_options ) && in_array( 'role_based_price_' . $current_role[ $current_role_val ], $get_options, true ) ) {
		if ( isset( $sale_price ) && $role_based_pricing > $sale_price && ! empty( $type ) && 'simple' === $type ) {
			return wc_price( $sale_price ) . ' ' . $tax_label;
		} else {
			return wc_price( $role_based_pricing ) . ' ' . $tax_label;
		}
	} elseif ( $product->is_on_sale() && ! empty( $get_options ) && in_array( 'on_sale_price_' . $current_role[ $current_role_val ], $get_options, true ) ) {
		return wc_price( $sale_price ) . ' ' . $tax_label;
	} elseif ( $product->is_on_sale() && ! empty( $get_options ) && in_array( 'regular_price_' . $current_role[ $current_role_val ], $get_options, true ) ) {
		return wc_price( $regular_price ) . ' ' . $tax_label;
	} elseif ( ! $product->is_on_sale() && ! empty( $get_options ) && in_array( 'regular_price_' . $current_role[ $current_role_val ], $get_options, true ) ) {
		return wc_price( $regular_price ) . ' ' . $tax_label;
	} else {
		if ( isset( $get_options ) && ! empty( $get_options ) && in_array( 'show_tax_' . $current_role[ $current_role_val ], $get_options, true ) ) {
			return wc_price( wc_get_price_including_tax( $product ) );
		} else {
			return wc_price( wc_get_price_excluding_tax( $product ) );
		}
	}
}



/**
 * Function to check whether plugin is active or not.
 *
 * @return boolean
 */
function is_wps_role_based_pricing_active() {
	$active_plugin = get_option( 'active_plugins', false );
	if ( in_array( 'wps-role-based-pricing-for-woocommerce/wps-role-based-pricing-for-woocommerce.php', $active_plugin, true ) && wps_ubo_lite_if_pro_exists() ) {
		return true;
	} else {
		return false;
	}
}



/**
 * Get price html with bump offer discount
 *
 * @param   string $product_id          The offer product id.
 * @param   string $bump_discount       The bump offer discount string.
 * @param   string $get                 What to get price or price html.
 * @since   1.0.0
 */
function wps_ubo_lite_custom_price_html( $product_id = '', $bump_discount = '', $get = '' ) {

	$product = wc_get_product( $product_id );

	if ( empty( $product ) ) {

		return;
	}

	if ( is_wps_role_based_pricing_active() ) {
		$prod_obj = wc_get_product( $product_id );
		$prod_type = $prod_obj->get_type();
		$wps_price_role_based = wps_mrbpfw_role_based_price( $product->get_price(), $prod_obj, $prod_type );
		$wps_price_role_based = wp_strip_all_tags( str_replace( get_woocommerce_currency_symbol(), '', $wps_price_role_based ) );
		$orginal_price = floatval( $wps_price_role_based );
		$sale_price    = floatval( $wps_price_role_based );
		$regular_price = floatval( $wps_price_role_based );
	} else {
		$orginal_price = $product->get_price();
		$sale_price    = $product->get_sale_price();
		$regular_price = $product->get_regular_price();
	}

	// Case of variable parent product.

	if ( empty( $sale_price ) && empty( $regular_price ) ) {

		if ( 'incl' === get_option( 'woocommerce_tax_display_cart', false ) ) {

			$default_price = wc_get_price_including_tax( $product );
		} else {

			$default_price = wc_get_price_excluding_tax( $product );
		}
	}

	if ( ! empty( $bump_discount ) ) {

		$price_array    = explode( '+', $bump_discount );
		$price_type     = $price_array[1];
		$price_discount = $price_array[0];
		if ( '%' === $price_type ) {

			$price_discount = ( $price_discount > 100 ) ? 100 : $price_discount;
			$price_discount = ( $price_discount < 0 ) ? 0 : $price_discount;

			$price_discount = floatval( sanitize_text_field( $price_discount ) );

			$bump_price = (float) $orginal_price - ( (float) $orginal_price * (float) $price_discount / 100 );

			$product->set_price( $bump_price );

		} elseif ( 'fixed' === $price_type ) {

			// Just add the price with discount, tax will be added automatically.
			if ( empty( $price_discount ) ) {

				$bump_price = '0';
				$product->set_price( $bump_price );

			} else {

				$product->set_price( $price_discount );
				$bump_price = $price_discount;
			}
		} else {
			if ( is_wps_role_based_pricing_active() ) {
				$prod_obj = wc_get_product( $product_id );
				$prod_type = $prod_obj->get_type();
				$bump_price = wps_mrbpfw_role_based_price( $product->get_price(), $prod_obj, $prod_type );
			} else {
				$bump_price = $product->get_price();
			}
		}
	}

	// If only bump offer price is needed.
	if ( 'price' === $get ) {

		return $bump_price;
	}

	/**
	 * After v1.2.0 We have option to select the price html format.
	 * So before returning price html in wc_format_sale_price check the settings and create html accordingly.
	 */
	$wps_ubo_global_options = get_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );

	$price_formatting = ! empty( $wps_ubo_global_options['wps_ubo_offer_price_html'] ) ? $wps_ubo_global_options['wps_ubo_offer_price_html'] : 'regular_to_offer';

	$is_subscription = false;

	// Check if Subscription product.
	if ( class_exists( 'WC_Subscriptions_Product' ) && WC_Subscriptions_Product::is_subscription( $product ) ) {

		$is_subscription = true;
	}

	// Case of variable parent product.
	if ( ! empty( $default_price ) ) {

		if ( get_option( 'woocommerce_tax_display_cart', false ) === 'incl' ) {

			$bump_price = wc_get_price_including_tax( $product );

		} else {

			$bump_price = wc_get_price_excluding_tax( $product );
		}

		if ( true === $is_subscription ) {

			return $product->get_price_html();
		}
		if ( is_wps_role_based_pricing_active() ) {
			$prod_obj             = wc_get_product( $product_id );
			$prod_type            = $prod_obj->get_type();
			$wps_price_role_based = wps_mrbpfw_role_based_price( $default_price, $prod_obj, $prod_type );
		} else {
			$wps_price_role_based = 'not_active';
		}
		if ( 'no_disc' == $price_type ) {
			$bump_price = $product->get_price_html();
			return $bump_price;
		} else {
			return wc_format_sale_price( ( 'not_active' === $wps_price_role_based ) ? $default_price : $wps_price_role_based, $bump_price );
		}
	}

	// Check woocommerce settings for tax display at cart.
	if ( 'incl' === get_option( 'woocommerce_tax_display_cart', false ) ) {

		$regular_price = wc_get_price_including_tax( $product, array( 'price' => $regular_price ) );

		$sale_price = ! empty( $sale_price ) ? wc_get_price_including_tax( $product, array( 'price' => $sale_price ) ) : $regular_price;

		$bump_price = wc_get_price_including_tax( $product );
	}

	/**
	 * Here the price has been updated in case the tax must be included.
	 */

	// If regular price is to be shown.
	if ( 'regular_to_offer' === $price_formatting ) {

		if ( true === $is_subscription ) {

			return $product->get_price_html();
		}
		if ( is_wps_role_based_pricing_active() ) {
			$prod_obj             = wc_get_product( $product_id );
			$prod_type            = $prod_obj->get_type();
			$wps_price_role_based = wps_mrbpfw_role_based_price( $regular_price, $prod_obj, $prod_type );
		} else {
			$wps_price_role_based = 'not_active';
		}
		if ( 'no_disc' == $price_type ) {
			$bump_price = $product->get_price_html();
			return $bump_price;
		} else {
			return wc_format_sale_price( ( 'not_active' === $wps_price_role_based ) ? $regular_price : $wps_price_role_based, $bump_price );
		}
	} elseif ( 'sale_to_offer' === $price_formatting ) {

		if ( ! empty( $sale_price ) ) {

			if ( true === $is_subscription ) {

				$updated_html = explode( '<span class="subscription-details">', $product->get_price_html() );

				$subscription_details = ! empty( $updated_html['1'] ) ? '<span class="subscription-details">' . $updated_html['1'] : '';

				if ( is_wps_role_based_pricing_active() ) {
					$prod_obj             = wc_get_product( $product_id );
					$prod_type            = $prod_obj->get_type();
					$wps_price_role_based = wps_mrbpfw_role_based_price( $sale_price, $prod_obj, $prod_type );
				} else {
					$wps_price_role_based = 'not_active';
				}
				if ( 'no_disc' == $price_type ) {
					$bump_price = $product->get_price_html();
					return $bump_price;
				} else {
					return wc_format_sale_price( ( 'not_active' === $wps_price_role_based ) ? $sale_price : $wps_price_role_based, $bump_price ) . $subscription_details;
				}
			}

			if ( is_wps_role_based_pricing_active() ) {
				$prod_obj             = wc_get_product( $product_id );
				$prod_type            = $prod_obj->get_type();
				$wps_price_role_based = wps_mrbpfw_role_based_price( $sale_price, $prod_obj, $prod_type );
			} else {
				$wps_price_role_based = 'not_active';
			}
			if ( 'no_disc' == $price_type ) {
				$bump_price = $product->get_price_html();
				return $bump_price;
			} else {
				return wc_format_sale_price( ( 'not_active' === $wps_price_role_based ) ? $sale_price : $wps_price_role_based, $bump_price );
			}
		} else {

			if ( true === $is_subscription ) {

				return $product->get_price_html();
			}

			if ( is_wps_role_based_pricing_active() ) {
				$prod_obj             = wc_get_product( $product_id );
				$prod_type            = $prod_obj->get_type();
				$wps_price_role_based = wps_mrbpfw_role_based_price( $regular_price, $prod_obj, $prod_type );
			} else {
				$wps_price_role_based = 'not_active';
			}
			if ( 'no_disc' == $price_type ) {
				$bump_price = $product->get_price_html();
				return $bump_price;
			} else {
				return wc_format_sale_price( ( 'not_active' === $wps_price_role_based ) ? $regular_price : $wps_price_role_based, $bump_price );
			}
		}
	}
}

/**
 * Unset order bump encountered session.
 * In which Order Bump ids are saved
 * which are displayed from session.
 *
 * @since   1.4.0
 */
function wps_ubo_destroy_encountered_session() {

	// WC Session not accessible so return.
	if ( empty( WC()->session ) ) {

		return;
	}

	// Encountered session key.
	$session_keys = array(
		'encountered_bump_array',
	);

	foreach ( $session_keys as $key => $key_name ) {

		if ( null !== WC()->session->get( $key_name ) ) {

			WC()->session->__unset( $key_name );
		}
	}
}

/**
 * Destroy the only data added in session by orderbump.
 *
 * @since   1.2.0
 */
function wps_ubo_session_destroy() {

	// WC Session not accessible so return.
	if ( empty( WC()->session ) ) {

		return;
	}

	$session_keys = array(
		'encountered_bump_array',
		'encountered_bump_tarket_key_array',
		'bump_offer_status',
		'encountered_bump_array_display',
	);

	// Add respective bump status index session keys for removal.
	$encountered_bump_array = null !== WC()->session->get( 'encountered_bump_array' ) ? WC()->session->get( 'encountered_bump_array' ) : array();

	if ( ! empty( $encountered_bump_array ) && is_array( $encountered_bump_array ) ) {

		foreach ( $encountered_bump_array as $bump_id ) {

			$session_keys[] = "bump_offer_status_index_$bump_id";
		}
	}

	foreach ( $session_keys as $key => $key_name ) {

		if ( null !== WC()->session->get( $key_name ) ) {

			WC()->session->__unset( $key_name );
		}
	}
}

/**
 * Add Go pro popup.
 *
 * @param   string $location        Location of page where you want to show popup.
 * @since   1.2.0
 */
function wps_ubo_go_pro( $location = 'pro' ) {

	if ( 'pro' === $location ) {

		$message = esc_html__( 'Want some more super cool features? Unlock your power to explore more.', 'upsell-order-bump-offer-for-woocommerce' );

	} else {

		$message = esc_html__( 'Stucked to just one order bump? Unlock your power to explore more.', 'upsell-order-bump-offer-for-woocommerce' );
	}

	ob_start();
	?>
	<!-- Go pro popup wrap start. -->
	<div class="wps_ubo_lite_go_pro_popup_wrap">
		<!-- Go pro popup main start. -->
		<div class="wps_ubo_lite_go_pro_popup">
			<!-- Main heading. -->
			<div class="wps_ubo_lite_go_pro_popup_head">
				<h2><?php esc_html_e( 'Want More? Go Pro !!', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2>
				<!-- Close button. -->
				<a href="" class="wps_ubo_lite_go_pro_popup_close">
					<span>&times;</span>
				</a>
			</div>  

			<!-- Notice icon. -->
			<div class="wps_ubo_lite_go_pro_popup_head"><img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/icons/pro.png' ); ?> ">
			</div>

			<!-- Notice. -->
			<div class="wps_ubo_lite_go_pro_popup_content">
				<p class="wps_ubo_lite_go_pro_popup_text">
					<?php echo esc_html( $message ); ?>
				</p>
				<p class="wps_ubo_lite_go_pro_popup_text">
					<?php esc_html_e( 'Go with our premium version and make unlimited numbers of order bumps. Get more smart features and make the most attractive offers with all of your products. Set Relevant offers for specific targets which will ensure customer satisfaction and higher conversion rates.', 'upsell-order-bump-offer-for-woocommerce' ); ?>
				</p>
			</div>

			<!-- Go pro button. -->
			<div class="wps_ubo_lite_go_pro_popup_button">
				<a class="button wps_ubo_lite_overview_go_pro_button" target="_blank" href="https://wpswings.com/product/upsell-order-bump-offer-for-woocommerce-pro/?utm_source=wpswings-order-bump-pro&utm_medium=order-bump-org-backend&utm_campaign=WPS-order-bump-pro"><?php echo esc_html__( 'Upgrade to Premium', 'upsell-order-bump-offer-for-woocommerce' ) . ' <span class="dashicons dashicons-arrow-right-alt"></span>'; ?></a>
			</div>
		</div>
		<!-- Go pro popup main end. -->
	</div>
	<!-- Go pro popup wrap end. -->

		<!-- Go pro popup wrap start. -->
		<div class="wps_ubo_lite_go_pro_popup_wrap_template">
		<!-- Go pro popup main start. -->
		<div class="wps_ubo_lite_go_pro_popup">
			<!-- Main heading. -->
			<div class="wps_ubo_lite_go_pro_popup_head">
				<h2><?php esc_html_e( 'Want More Templates? Go Pro !!', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2>
				<!-- Close button. -->
				<a href="" class="wps_ubo_lite_go_pro_popup_close">
					<span>&times;</span>
				</a>
			</div>  

			<!-- Notice icon. -->
			<div class="wps_ubo_lite_go_pro_popup_head"><img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/icons/pro.png' ); ?> ">
			</div>

			<!-- Notice. -->
			<div class="wps_ubo_lite_go_pro_popup_content">
				<p class="wps_ubo_lite_go_pro_popup_text">
					<?php echo esc_html( $message ); ?>
				</p>
				<p class="wps_ubo_lite_go_pro_popup_text">
					<?php esc_html_e( 'Go with our premium version and make unlimited numbers of order bumps. Get more smart features and make the most attractive offers with all of your products. Set Relevant offers for specific targets which will ensure customer satisfaction and higher conversion rates.', 'upsell-order-bump-offer-for-woocommerce' ); ?>
				</p>
			</div>

			<!-- Go pro button. -->
			<div class="wps_ubo_lite_go_pro_popup_button">
				<a class="button wps_ubo_lite_overview_go_pro_button" target="_blank" href="https://wpswings.com/product/upsell-order-bump-offer-for-woocommerce-pro/?utm_source=wpswings-order-bump-pro&utm_medium=order-bump-org-backend&utm_campaign=WPS-order-bump-pro"><?php echo esc_html__( 'Upgrade to Premium', 'upsell-order-bump-offer-for-woocommerce' ) . ' <span class="dashicons dashicons-arrow-right-alt"></span>'; ?></a>
			</div>
		</div>
		<!-- Go pro popup main end. -->
	</div>
	<!-- Go pro popup wrap end. -->
	<?php
	$popup_html = ob_get_contents();
	ob_end_clean();
	$allowed_html = wps_ubo_lite_allowed_html();
	echo wp_kses( $popup_html, $allowed_html );
}

/**
 *  Returns product name and status.
 *
 * @param   string $product_id        Product id.
 * @since   1.2.0
 */
function wps_ubo_lite_get_title( $product_id = '' ) {

	if ( ! empty( $product_id ) ) {

		$result = esc_html__( 'Product not found', 'upsell-order-bump-offer-for-woocommerce' );

		$product = wc_get_product( $product_id );

		if ( ! empty( $product ) ) {

			if ( 'publish' !== $product->get_status() ) {

				$result = esc_html__( 'Product Unavailable', 'upsell-order-bump-offer-for-woocommerce' );

			} else {

				$result = get_the_title( $product_id );
			}
		}

		return $result;
	}
}

/**
 *  Returns bump name and id.
 *
 * @param   string $bump_id      Bump id.
 * @since   1.2.0
 */
function wps_ubo_lite_get_bump_title( $bump_id = '' ) {

	if ( ! empty( $bump_id ) ) {

		$wps_upsell_bumps_list = get_option( 'wps_ubo_bump_list' );
		;

		if ( ! empty( $wps_upsell_bumps_list ) ) {

			if ( 'yes' != $wps_upsell_bumps_list[ $bump_id ]['wps_upsell_bump_status'] ) {

				$result = esc_html__( 'Bump Unavailable / Bump Not Live', 'upsell-order-bump-offer-for-woocommerce' );
			} else {
				$result = $wps_upsell_bumps_list[ $bump_id ]['wps_upsell_bump_name'];
			}
		}

		return $result;
	} else {

		$result = esc_html__( 'Bump not found', 'upsell-order-bump-offer-for-woocommerce' );
	}
}

/**
 *  Returns product name and status.
 *
 * @param   string $coupon_id        Coupon id.
 * @since   1.2.0
 */
function wps_ubo_lite_get_coupon_title( $coupon_id = '' ) {

	if ( ! empty( $coupon_id ) ) {

		$result = esc_html__( 'Coupon not found', 'upsell-order-bump-offer-for-woocommerce' );

		$coupon = new WC_Coupon( $coupon_id );

		if ( ! empty( $coupon ) && 'shop_coupon' === get_post_type( $coupon_id ) ) {

			if ( 'publish' !== get_post_status( $coupon_id ) ) {

				$result = esc_html__( 'Coupon Unavailable', 'upsell-order-bump-offer-for-woocommerce' );

			} else {

				$result = get_the_title( $coupon_id );
			}
		}

		return $result;
	}
}

/**
 *  Returns category name and existance.
 *
 * @param   string $cat_id        Category id.
 * @since   1.2.0
 */
function wps_ubo_lite_getcat_title( $cat_id = '' ) {

	if ( ! empty( $cat_id ) ) {

		$result = esc_html__( 'Category not found', 'upsell-order-bump-offer-for-woocommerce' );

		$category_name = get_the_category_by_ID( $cat_id );

		if ( ! empty( $category_name ) ) {

			$result = $category_name;
		}

		return $result;
	}
}

/**=================================================================================================
								Customised functions for next update.
								Have been called in public files only.
===================================================================================================*/

/**
 *  Displays Order bump and its variation popup.
 *
 * @param   string $key                                        Key of encountered order bump array.
 * @param   string $encountered_respective_target_key          Target product key for same order bump.
 * @param   int    $encountered_order_bump_id                  Single order bump id.
 * @since   1.4.0
 */
function wps_ubo_analyse_and_display_order_bump( $key, $encountered_respective_target_key, int $encountered_order_bump_id = null ) {

	if ( empty( $encountered_order_bump_id ) ) {

		return;
	}

	// Fetch Order Bump Details from Order Bump ID.
	$wps_ubo_offer_array_collection = get_option( 'wps_ubo_bump_list' );
	$bump = wps_ubo_lite_fetch_bump_offer_details( $encountered_order_bump_id, $encountered_respective_target_key );// details of the all template chnage is here.
	$encountered_bump_array = $wps_ubo_offer_array_collection[ $encountered_order_bump_id ];
	if ( empty( $bump ) ) {

		return;
	}

	// As just going to display the Order Bump.
	if ( null === WC()->session->get( 'encountered_bump_array_display' ) ) {

		WC()->session->set( 'encountered_bump_array_display', 'true' );
	}

	// Fetch Order Bump HTML from Order Bump Details.
	// Key for handling Mulitple Order Bumps.
	$encountered_bump_array = $wps_ubo_offer_array_collection[ $encountered_order_bump_id ];
	$wps_bump_upsell_selected_template = ! empty( $encountered_bump_array['wps_ubo_selected_template'] ) ? sanitize_text_field( $encountered_bump_array['wps_ubo_selected_template'] ) : '';

	if ( '3' == $wps_bump_upsell_selected_template ) {

		$bumphtml = wps_ubo_lite_bump_offer_html_3( $bump, $encountered_order_bump_id, $key );

	} elseif ( '4' == $wps_bump_upsell_selected_template ) {

		$bumphtml = wps_ubo_lite_bump_offer_html_4( $bump, $encountered_order_bump_id, $key );

	} elseif ( '5' == $wps_bump_upsell_selected_template ) {

		$bumphtml = wps_ubo_lite_bump_offer_html_5( $bump, $encountered_order_bump_id, $key );

	} elseif ( '6' == $wps_bump_upsell_selected_template ) {

		$bumphtml = wps_ubo_lite_bump_offer_html_pro_6( $bump, $encountered_order_bump_id, $key );

	} elseif ( '7' == $wps_bump_upsell_selected_template ) {

		$bumphtml = wps_ubo_lite_bump_offer_html_pro_6( $bump, $encountered_order_bump_id, $key );

	} elseif ( '8' == $wps_bump_upsell_selected_template ) {

		$bumphtml = wps_ubo_lite_bump_offer_html_pro_6( $bump, $encountered_order_bump_id, $key );

	} elseif ( '9' == $wps_bump_upsell_selected_template ) {

		$bumphtml = wps_ubo_lite_bump_offer_html_pro_6( $bump, $encountered_order_bump_id, $key );

	} elseif ( '10' == $wps_bump_upsell_selected_template ) {

		$bumphtml = wps_ubo_lite_bump_offer_html_10( $bump, $encountered_order_bump_id, $key );

	} elseif ( '11' == $wps_bump_upsell_selected_template ) {

		$bumphtml = wps_ubo_lite_bump_offer_html_11( $bump, $encountered_order_bump_id, $key );

	} elseif ( '12' == $wps_bump_upsell_selected_template ) {

		$bumphtml = wps_ubo_lite_bump_offer_html_12( $bump, $encountered_order_bump_id, $key );

	} else {

		$bumphtml = wps_ubo_lite_bump_offer_html( $bump, $encountered_order_bump_id, $key );

	}

	$allowed_html = wps_ubo_lite_allowed_html();

	if ( '11' != $wps_bump_upsell_selected_template ) {
		echo wp_kses( $bumphtml, $allowed_html );
	} else {
		// Suppress PHPCS warning about escaping output.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason for ignoring the escaping rule.
		echo wp_kses( $bumphtml, $allowed_html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped.
	}

	$offer_product = wc_get_product( $bump['id'] );

	$meta_form_attr = array(
		'meta_forms_allowed' => ! empty( $bump['meta_forms_allowed'] ) ? $bump['meta_forms_allowed'] : 'no',
		'meta_form_fields'   => ! empty( $bump['meta_form_fields'] ) ? $bump['meta_form_fields'] : array(),
	);

	// For variable offer products.
	if ( $offer_product->has_child() ) {
		wps_ubo_lite_show_variation_popup( $offer_product, $key, $meta_form_attr );
	}
}


/**
 *  Live availability Check for order bump offer products.
 *
 * @param   array $wps_ubo_offer_array_collection     Array of all order bumps collection.
 * @param   array $wps_ubo_global_options             Array of global settings.
 * @param   int   $encountered_order_bump_id          Single order bump id.
 * @since   1.4.0
 */
function wps_ubo_order_bump_session_validations( $wps_ubo_offer_array_collection, $wps_ubo_global_options, int $encountered_order_bump_id = null ) {

	if ( empty( $encountered_order_bump_id ) || empty( $wps_ubo_offer_array_collection[ $encountered_order_bump_id ] ) ) {

		return false;
	}

	$selected_order_bump = $wps_ubo_offer_array_collection[ $encountered_order_bump_id ];

	// Check if still live.
	if ( ! empty( $selected_order_bump['wps_upsell_bump_status'] ) && 'yes' !== $selected_order_bump['wps_upsell_bump_status'] ) {

		return false;
	}

	$offer_id = ! empty( $selected_order_bump['wps_upsell_bump_products_in_offer'] ) ? sanitize_text_field( $selected_order_bump['wps_upsell_bump_products_in_offer'] ) : '';

	if ( empty( $offer_id ) ) {

		return false;
	}

	$wps_upsell_bump_global_skip_settings = ! empty( $wps_ubo_global_options['wps_bump_skip_offer'] ) ? $wps_ubo_global_options['wps_bump_skip_offer'] : 'yes';

	// Check if offer product is already in cart.
	if ( wps_ubo_lite_already_in_cart( $offer_id ) && 'yes' === $wps_upsell_bump_global_skip_settings ) {

		return false;
	}

	$offer_product = wc_get_product( $offer_id );

	// Offer Product Validations.
	if ( empty( $offer_product ) || 'publish' !== $offer_product->get_status() || ! $offer_product->is_in_stock() ) {

		return false;
	}
}

/**
 * If page reload is required when subscription offer is added
 * and according to conditions.
 *
 * @param object $product Product.
 *
 * @since    1.4.0
 */
function wps_ubo_lite_reload_required_after_adding_offer( $product = '' ) {

	if ( ! empty( $product ) && ! is_user_logged_in() && class_exists( 'WC_Subscriptions_Product' ) && WC_Subscriptions_Product::is_subscription( $product ) && 'yes' !== get_option( 'woocommerce_enable_signup_and_login_from_checkout', false ) ) {

		return true;
	} else {

		return false;
	}
}

/**
 * Function to validate user roles.
 *
 * @param int $bump_id single bump id.
 * @return boolean
 */
function is_valid_user_role( $bump_id = '' ) {
	$all_bumps_to_get = get_option( 'wps_ubo_bump_list', array() );

	$wps_bump_unsupported_roles = ! empty( $all_bumps_to_get[ $bump_id ]['wps_upsell_bump_exclude_roles'] ) ? $all_bumps_to_get[ $bump_id ]['wps_upsell_bump_exclude_roles'] : array();
	$user                       = wp_get_current_user();
	$user_role                  = ! empty( $user->roles ) ? $user->roles : array( 'guest' );
	$user_role                  = ! empty( $user_role[0] ) ? $user_role[0] : '';

	if ( in_array( $user_role, $wps_bump_unsupported_roles, true ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Bump Offer Html For Elegant Summers.
 *
 * @param   string $bump        Consists all data about order bump.
 * @param   string $encountered_order_bump_id        Consists all data about order bump.
 * @param   string $order_bump_key        Consists all data about order bump.
 * @since   1.0.0
 */
function wps_ubo_lite_bump_offer_html_3( $bump, $encountered_order_bump_id = '', $order_bump_key = '' ) {

	/**
	 * Text fields.
	 */
	$title = ! empty( $bump['design_text']['wps_upsell_offer_title'] ) ? $bump['design_text']['wps_upsell_offer_title'] : '';

	$description = $bump['design_text']['wps_upsell_bump_offer_description'];

	$product_description_text = $bump['design_text']['wps_bump_offer_decsription_text'];

	$discount_title_fixed = ! empty( $bump['design_text']['wps_ubo_discount_title_for_fixed'] ) ? $bump['design_text']['wps_ubo_discount_title_for_fixed'] : '';

	$discount_title_percent = ! empty( $bump['design_text']['wps_ubo_discount_title_for_percent'] ) ? $bump['design_text']['wps_ubo_discount_title_for_percent'] : '';

	if ( ! empty( $bump['bump_price_html'] ) ) {

		$discount_title_fixed   = str_replace( '{dc_price}', $bump['bump_price_html'], $discount_title_fixed );
		$discount_title_percent = str_replace( '{dc_%}', $bump['bump_price_html'], $discount_title_percent );

	}

	if ( ! empty( $bump['price_type'] ) && 'fixed' === $bump['price_type'] ) {

		$bump_price_html = $discount_title_fixed;
	} else {

		$bump_price_html = $discount_title_percent;
	}

	// Template adaption.
	$wps_ubo_global_options    = get_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );
	$wps_ubo_template_adaption = ! empty( $wps_ubo_global_options['wps_ubo_temp_adaption'] ) ? $wps_ubo_global_options['wps_ubo_temp_adaption'] : '';

	// Setting to enable disable permalink.
	$wps_bump_enable_permalink = ! empty( $wps_ubo_global_options['wps_bump_enable_permalink'] ) ? $wps_ubo_global_options['wps_bump_enable_permalink'] : '';
	// Red arrow setting.
	$wps_enable_red_arrow_feature = ! empty( $wps_ubo_global_options['wps_enable_red_arrow_feature'] ) ? $wps_ubo_global_options['wps_enable_red_arrow_feature'] : '';
	// Setting for the offer Quantity.
	$wps_upsell_enable_quantity              = ! empty( $bump['wps_upsell_enable_quantity'] ) ? $bump['wps_upsell_enable_quantity'] : '';
	$wps_upsell_bump_products_fixed_quantity = ! empty( $bump['wps_upsell_bump_products_fixed_quantity'] ) ? $bump['wps_upsell_bump_products_fixed_quantity'] : '';
	$wps_upsell_bump_products_min_quantity   = ! empty( $bump['wps_upsell_bump_products_min_quantity'] ) ? $bump['wps_upsell_bump_products_min_quantity'] : '';
	$wps_upsell_bump_products_max_quantity   = ! empty( $bump['wps_upsell_bump_products_max_quantity'] ) ? $bump['wps_upsell_bump_products_max_quantity'] : '';
	$wps_upsell_offer_quantity_type          = ! empty( $bump['wps_upsell_offer_quantity_type'] ) ? $bump['wps_upsell_offer_quantity_type'] : '';

	// PARENT WRAPPER DIV CSS( parent_wrapper_div ).
	$parent_border_type             = ! empty( $bump['design_css']['parent_border_type'] ) ? $bump['design_css']['parent_border_type'] : '';
	$parent_border_color            = ! empty( $bump['design_css']['parent_border_color'] ) ? $bump['design_css']['parent_border_color'] : '';
	$parent_background_color        = ! empty( $bump['design_css']['parent_background_color'] ) ? $bump['design_css']['parent_background_color'] : '';
	$parent_top_vertical_spacing    = ! empty( $bump['design_css']['top_vertical_spacing'] ) ? $bump['design_css']['top_vertical_spacing'] : '';
	$parent_bottom_vertical_spacing = ! empty( $bump['design_css']['bottom_vertical_spacing'] ) ? $bump['design_css']['bottom_vertical_spacing'] : '0';

	// DISCOUNT SECTION( discount_section ).
	$discount_section_background_color = ! empty( $bump['design_css']['discount_section_background_color'] ) ? $bump['design_css']['discount_section_background_color'] : '';
	$discount_section_text_color       = ! empty( $bump['design_css']['discount_section_text_color'] ) ? $bump['design_css']['discount_section_text_color'] : '';
	$discount_section_text_size        = ! empty( $bump['design_css']['discount_section_text_size'] ) ? $bump['design_css']['discount_section_text_size'] : '';

	// PRODUCT SECTION( product_section ).
	$product_section_text_color = ! empty( $bump['design_css']['product_section_text_color'] ) ? $bump['design_css']['product_section_text_color'] : '';
	$product_section_text_size  = ! empty( $bump['design_css']['product_section_text_size'] ) ? $bump['design_css']['product_section_text_size'] : '';
	$product_section_text_price_size  = ! empty( $bump['design_css']['product_section_price_text_size'] ) ? $bump['design_css']['product_section_price_text_size'] : '';
	$product_section_price_text_color = ! empty( $bump['design_css']['product_section_price_text_color'] ) ? $bump['design_css']['product_section_price_text_color'] : '';

	$product_section_img_width  = ! empty( $bump['design_css']['product_section_img_width'] ) ? $bump['design_css']['product_section_img_width'] : '';
	$product_section_img_height = ! empty( $bump['design_css']['product_section_img_height'] ) ? $bump['design_css']['product_section_img_height'] : '';

	// PRIMARY SECTION(primary_section ).
	$primary_section_background_color = ! empty( $bump['design_css']['primary_section_background_color'] ) ? $bump['design_css']['primary_section_background_color'] : '';
	$primary_section_text_color       = ! empty( $bump['design_css']['primary_section_text_color'] ) ? $bump['design_css']['primary_section_text_color'] : '';
	$primary_section_arrow_color      = ! empty( $bump['design_css']['primary_section_arrow_color'] ) ? $bump['design_css']['primary_section_arrow_color'] : '';
	$primary_section_text_size        = ! empty( $bump['design_css']['primary_section_text_size'] ) ? $bump['design_css']['primary_section_text_size'] : '';

	// SECONDARY SECTION( secondary_section ).
	$secondary_section_background_color = ! empty( $bump['design_css']['secondary_section_background_color'] ) ? $bump['design_css']['secondary_section_background_color'] : '';
	$secondary_section_text_color       = ! empty( $bump['design_css']['secondary_section_text_color'] ) ? $bump['design_css']['secondary_section_text_color'] : '';
	$secondary_section_text_size        = ! empty( $bump['design_css']['secondary_section_text_size'] ) ? $bump['design_css']['secondary_section_text_size'] : '';

	$wps_bump_target_attr = ! empty( $wps_ubo_global_options['wps_bump_target_link_attr_val'] ) ? $wps_ubo_global_options['wps_bump_target_link_attr_val'] : '';
	?>

	<?php $parent_border_width = 'double' === $parent_border_type ? '4px' : '2px'; ?>
	<?php
	$important = is_admin() ? '' : '!important';

	$order_bump_div_id = '#wps_upsell_offer_main_id_' . $encountered_order_bump_id;

	?>

	<!--  HTML goes down here. --> 
	<style type="text/css">
		/**
		* All of the CSS for your public-facing functionality should be
		* included in this file.
		*/
		<?php echo esc_html( $order_bump_div_id ); ?> {
			display: block;
			width: 100%;
			padding-top:  <?php echo esc_html( $parent_top_vertical_spacing ) . esc_html( 'px' ); ?>;
			padding-bottom:  <?php echo esc_html( $parent_bottom_vertical_spacing ) . esc_html( 'px' ); ?>;
			clear: both;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_parent_wrapper {
			border: <?php echo esc_html( $parent_border_type . ' ' . $parent_border_color . ' ' . $parent_border_width ); ?>;
			margin: 0 auto;
			<?php if ( 'no' === $wps_ubo_template_adaption ) : ?>
			max-width: 400px;
			<?php endif; ?>
		}
		.wps_upsell_offer_parent_wrapper {
			font-family: 'Source Sans Pro', sans-serif;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_wrapper {
			background-color:<?php echo esc_html( $parent_background_color ); ?>;
			padding : 20px;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_discount_section {
			margin: 0;
			text-align: center;
			background-color: <?php echo esc_html( $discount_section_background_color ); ?>;
			line-height: 1.68;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_discount_section h3 {
			color: <?php echo esc_html( $discount_section_text_color ); ?>;
			margin: 2px;
			padding: 1px;
			font-size: <?php echo esc_html( $discount_section_text_size ) . esc_html( 'px' ); ?>;
			border: none;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_discount_section h3 .amount {
			font-size: inherit;
			color: inherit;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_product_section {
			text-align :left;
			display: -webkit-flex;
			display: -moz-flex;
			display: -ms-flex;
			display: -o-flex;
			display: flex;
			font-size: 16px;
			align-items: start;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .upsell-offer-template-1 .wps_bump_name  {
			margin: 0;
			color: <?php echo esc_html( $product_section_text_color ); ?>;
			font-size: <?php echo esc_html( $product_section_text_size ) . esc_html( 'px' ); ?>;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_product_content h4 {
			display: inline-block;
			vertical-align: middle;
			font-weight: 500;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_product_content p {
			white-space: pre-line;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> p.wps_upsell_offer_product_price {
			font-size: <?php echo esc_html( $product_section_text_price_size ) . esc_html( 'px' ); ?>;
			/* font-weight: 700; */
			color: <?php echo esc_html( $product_section_price_text_color ); ?>;
		}

		<?php echo esc_html( $order_bump_div_id ); ?> .upsell-offer-template-1 p.wps_upsell_offer_product_price del{
			font-size: <?php echo esc_html( $product_section_text_price_size ) . esc_html( 'px' ); ?>;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .upsell-offer-template-1 p.wps_upsell_offer_product_price ins{
			font-size: <?php echo esc_html( $product_section_text_price_size ) . esc_html( 'px' ); ?>;
		}

		<?php echo esc_html( $order_bump_div_id ); ?> .upsell-offer-template-1 .upsell-product-desc p{
			font-size: <?php echo esc_html( $product_section_text_size ) . esc_html( 'px' ); ?>;
			color: <?php echo esc_html( $product_section_text_color ); ?>;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_product_section h4 {
			margin: 0;
			color: <?php echo esc_html( $product_section_text_color ); ?>;
			font-size: <?php echo esc_html( $product_section_text_size += 10 ) . esc_html( 'px' ); ?>;
			font-weight: 300;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_product_content {
			/* width: calc(100% - 90px); */
			word-break: break-word;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_primary_section {
			align-items: center;
			background-color: <?php echo esc_html( $primary_section_background_color ); ?>;
			display: flex;
			margin: 14px auto;
			padding: 10px;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_primary_section .add_offer_in_cart_text {
			color: <?php echo esc_html( $primary_section_text_color ); ?>;
			font-size: <?php echo esc_html( $primary_section_text_size ) . esc_html( 'px' ); ?>;
			margin: 0 0 0 5px;
			font-weight: 600;
			padding: 0;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_secondary_section {
			padding: 8px;
			background-color: <?php echo esc_html( $secondary_section_background_color ); ?>;
			text-align: center;
			white-space: pre-line;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_secondary_section p {
			color: <?php echo esc_html( $secondary_section_text_color ); ?>;
			margin: 0;
			font-size:<?php echo esc_html( $secondary_section_text_size ) . esc_html( 'px' ); ?>;
		}
		/* Custom checkbox container. */
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_primary_section .wps_upsell_bump_checkbox_container {
			cursor: pointer;
			width: auto;
			font-size: 22px;
			height: 23px;
			margin: 0 0 6px 0;
			padding-left: 35px;
			position: relative;
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
		}
		/* Hide the browser's default checkbox. */
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container input {
			position: absolute;
			opacity: 0;
			cursor: pointer;
			height: 0;
			width: 0;
		}
		/* Create a custom checkbox. */
		<?php echo esc_html( $order_bump_div_id ); ?> .checkmark {
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
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_arrow {
			width: 40px;
			margin-right: 4px;
			transform: scaleX(-1);
			animation: leftright 0.4s infinite ease;
			padding: 0 2px;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_arrow svg {
			width: 100%;
			height: auto;
			fill: <?php echo esc_html( $primary_section_arrow_color ); ?>
		}
		@keyframes leftright {
			0% {
				transform: translateX(-5px)scaleX(-1);
			}
			60% {
				transform: translateX(-2px)scaleX(-1);
			}
			100% {
				transform: translateX(-5px)scaleX(-1);
			}
		}
		/* On mouse-over, add a grey background color. */
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container:hover input ~ .checkmark {
			background-color: #ccc;
		}
		/* When the checkbox is checked, add a blue background. */
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container input:checked ~ .checkmark {
			background-color: #ffffff;
		}
		/* Create the checkmark/indicator (hidden when not checked). */
		<?php echo esc_html( $order_bump_div_id ); ?> .checkmark:after {
			content: "";
			position: absolute;
			display: none;
		}
		/* Show the checkmark when checked. */
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container input:checked ~ .checkmark:after {
			display: block;
		}
		/* Style the checkmark/indicator. */
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container .checkmark:after {
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
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_image {
			margin-right: 10px;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_img {
			width: <?php echo esc_html( $product_section_img_width . 'px' ); ?>;
			height: <?php echo esc_html( $product_section_img_height . 'px' ); ?>;
			max-width: 90px;
			max-height: 100px;
		}

		@media only screen and (min-width : 768px) and (max-width: 1100px) {
			.wps_upsell_offer_wrapper .wps_upsell_offer_product_section {
				flex-wrap: wrap;
				justify-content: center;
			}
			.wps_upsell_offer_wrapper .wps_upsell_offer_product_content {
				width: 100%!important;
			}
		}

		@media screen and (max-width: 480px) {
			<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_product_content {
				margin-left: 0;
			}
		}

	</style>

	<?php

	// Incase no offer is added return.
	$bump['id']   = ! empty( $bump['id'] ) ? sanitize_text_field( $bump['id'] ) : '';
	$bump_product = wc_get_product( $bump['id'] );

	// If offer not found return.
	if ( empty( $bump['id'] ) || empty( $bump_product ) ) {

		return;
	}

	$bump['name'] = ! empty( $bump['name'] ) ? sanitize_text_field( $bump['name'] ) : '';

	$bump['discount_price'] = ! empty( $bump['discount_price'] ) ? sanitize_text_field( $bump['discount_price'] ) : '0';

	// After v2.0.1!
	if ( ! empty( $bump['offer_image'] ) ) {
		$image = wp_get_attachment_image_src( $bump['offer_image'], 'single-post-thumbnail' )[0];
	}

	// If still not found.
	if ( empty( $image ) ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $bump['id'] ), 'single-post-thumbnail' )[0];
	}

	if ( empty( $image ) ) {

		$bump_parent_id = $bump_product->get_parent_id();

		if ( ! empty( $bump_parent_id ) ) {

			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $bump_parent_id ), 'single-post-thumbnail' )[0];

		} else {

			$image = wc_placeholder_img_src();
		}
	}

	// Add url of the offer product in the bump info.
	$bump_offer_product_permalink = esc_url_raw( get_permalink( $bump['id'] ) );

	$check = '';

	// Retain Checked if offer is added except for admin.
	if ( ! is_admin() && function_exists( 'WC' ) && ! empty( WC()->session ) ) {

		if ( null !== WC()->session->get( "bump_offer_status_index_$order_bump_key" ) ) {

			$check = 'checked';
		}
	}

	/*
	* Get price html.
	*/
	$bump_offer_price = wps_ubo_lite_custom_price_html( $bump['id'], $bump['discount_price'] );

	/**
	 * Html for bump offer.
	 */
	$bumphtml = '';

	// parent wrapper start.
	$bumphtml .= '<div id="wps_upsell_offer_main_id_' . $encountered_order_bump_id . '" class = "wps_upsell_offer_main_wrapper wps_ubo_wrapper_index_' . $order_bump_key . '" >';

	$bumphtml .= '<input type="hidden" class ="offer_shown_id" value="' . $bump['id'] . '">';
	$bumphtml .= '<input type="hidden" class ="offer_shown_discount" value="' . $bump['discount_price'] . '">';
	$bumphtml .= '<input type="hidden" class ="target_id_cart_key" value="' . $bump['target_key'] . '">';
	$bumphtml .= '<input type="hidden" class ="order_bump_index" value="index_' . $order_bump_key . '">';
	$bumphtml .= '<input type="hidden" class ="order_bump_id" value="' . $encountered_order_bump_id . '">';

	$offer_product = wc_get_product( $bump['id'] );

	if ( ! empty( $offer_product ) && is_object( $offer_product ) && $offer_product->has_child() ) {

		$bumphtml .= '<input type="hidden" class ="offer_shown_id_type" value="variable">';
	}

	if ( ! empty( $bump['smart_offer_upgrade'] ) && 'yes' === $bump['smart_offer_upgrade'] ) {

		$bumphtml .= '<input type="hidden" class="order_bump_smo" value=' . $bump['smart_offer_upgrade'] . '>';
	}

	if ( is_admin() && ! empty( $bump['bump_price_at_zero'] ) ) :
		$bumphtml .= '<input type="hidden" class="bump_price_at_zero" value=' . $bump['bump_price_at_zero'] . '>';
	endif;

	$bumphtml                .= '<div class = "wps_upsell_offer_parent_wrapper upsell-offer-template upsell-offer-template-1" >';
	$bumphtml                .= '<div class="upsell-offer-header">';
	$bumphtml                .= '<div class="upsell-offer-timer-section">';
	$bumphtml                .= '<div class="upsell-offer-time">';
	$bumphtml                .= '<div id = "wps_admin_timer"></div>';
	$wps_counter_timer_enable = isset( $bump['counter_timer'] ) ? $bump['counter_timer'] : '';
	$wps_evergreencounter_timer_enable = isset( $bump['evergreen_counter_timer'] ) ? $bump['evergreen_counter_timer'] : '';
	// Countdown Timer Section start.
	if ( ( 'yes' === $wps_counter_timer_enable || 'yes' == $wps_evergreencounter_timer_enable ) && wps_ubo_lite_if_pro_exists() ) {
		$bumphtml .= '<div class="expired_message_class" id = "expired_message' . esc_html( $order_bump_key ) . '"></div>';
		$bumphtml .= '<div class = "wps_timer_count wps_upsell_offer_discount_section" id ="wps_timer' . esc_html( $order_bump_key ) . '">
		<div class = "wps_day_timer_block wps-timer-wrap" >
		<div id ="wps_day_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_day_label">Days</div>
		</div>
		<div class ="wps_timer_sept">:</div>

		<div class = "wps_hour_timer_block wps-timer-wrap">
		<div id ="wps_hour_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_hour_label">Hour</div>
		</div>
		<div class ="wps_timer_sept">:</div>

		<div class = "wps_min_timer_block wps-timer-wrap">
		<div id ="wps_min_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_min_label">Min</div>
		</div>
		<div class ="wps_timer_sept">:</div>

		<div class = "wps_sec_timer_block wps-timer-wrap">
		<div id ="wps_sec_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_sec_label">Sec</div>
		</div>
		</div>';
	}
	// Countdown Timer Section End.

	$bumphtml .= '</div></div><div class="uspell-offer-discount">';
	// discount section start.
	$bumphtml .= '<div class = "wps_upsell_offer_discount_section" >';
	$bumphtml .= '<h3><b>' . $bump_price_html . '</b></h3>';
	$bumphtml .= '</div></div></div>';
	// discount section end.

	if ( 'fixed_q' === $wps_upsell_offer_quantity_type ) {
		$wps_is_fixed_qty = 'true';
	} else {
		$wps_is_fixed_qty = 'false';
	}
	// wrapper div start.

	$bumphtml .= '<div class = "wps_upsell_offer_wrapper" >';
	$bumphtml .= '<div class="upsell-offer-body">';
	$bumphtml .= '<div class="upsell-product">';

	if ( 'on' === $wps_bump_enable_permalink ) {
		// product section start with permalink.
		$bumphtml .= '<div class = "upsell-product-img" >';
		$bumphtml .= '<a target="' . $wps_bump_target_attr . '" href="' . $bump_offer_product_permalink . '"><img class="wps_upsell_offer_img" src="' . esc_url( $image ) . '" data-id="' . $bump['id'] . '"></a>';
		$bumphtml .= '</div>';
		$bumphtml .= '<div class="upsell-product-info">';
		$bumphtml .= '<h4 class="wps_bump_name" data-qty_allowed="' . esc_html( $wps_upsell_enable_quantity ) . '" data-wps_is_fixed_qty="' . esc_html( $wps_is_fixed_qty ) . '" data-wps_qty="' . esc_html( $wps_upsell_bump_products_fixed_quantity ) . '"><a target="' . esc_html( $wps_bump_target_attr ) . '" class="wps_upsell_product_permalink" href="' . esc_url( $bump_offer_product_permalink ) . '">' . esc_html( $bump['name'] ) . '</a></h4>';
		$bumphtml .= '<div class="product-price"><p class="wps_upsell_offer_product_price">' . $bump_offer_price . '</p></div>';
		if ( 'yes' === $wps_upsell_enable_quantity && 'variable_q' === $wps_upsell_offer_quantity_type && wps_ubo_lite_if_pro_exists() && ! $bump_product->is_type( 'variable' ) ) {
			$bumphtml .= '<div class="quantity">';
			$bumphtml .= '<label for="wps_quantity_offer quantity">' . __( 'Quantity', 'upsell-order-bump-offer-for-woocommerce' ) . ':</label>';
			$bumphtml .= '<input class="wps_input_quantity wps_quantity_input quantity-no" type="number" name="wps_quantity_offer" value="' . $wps_upsell_bump_products_min_quantity . '" min="' . $wps_upsell_bump_products_min_quantity . '" max="' . $wps_upsell_bump_products_max_quantity . '"></div>';
		}
		$bumphtml .= '<div class="upsell-product-desc">';
		$bumphtml .= '<p class="wps_upsell_offer_product_description">' . $product_description_text . '</p></div>';
		$bumphtml .= '</div>';
		// Product section ends.
	} else {
		$bumphtml .= '<div class = "upsell-product-img" >';
		$bumphtml .= '<img class="wps_upsell_offer_img" src="' . esc_url( $image ) . '" data-id="' . esc_html( $bump['id'] ) . '">';
		$bumphtml .= '</div>';
		$bumphtml .= '<div class="upsell-product-info">';
		$bumphtml .= '<h4 class="wps_bump_name" data-qty_allowed="' . esc_html( $wps_upsell_enable_quantity ) . '" data-wps_is_fixed_qty="' . esc_html( $wps_is_fixed_qty ) . '" data-wps_qty="' . esc_html( $wps_upsell_bump_products_fixed_quantity ) . '">' . esc_html( $bump['name'] ) . '</h4><br>';
		$bumphtml .= '<div class="product-price"><p class="wps_upsell_offer_product_price">' . $bump_offer_price . '</p></div>';
		if ( 'yes' === $wps_upsell_enable_quantity && 'variable_q' === $wps_upsell_offer_quantity_type && wps_ubo_lite_if_pro_exists() && ! $bump_product->is_type( 'variable' ) ) {
			$bumphtml .= '<div class="quantity">';
			$bumphtml .= '<label for="wps_quantity_offer quantity">' . __( 'Quantity', 'upsell-order-bump-offer-for-woocommerce' ) . ':</label>';
			$bumphtml .= '<input class="wps_input_quantity wps_quantity_input quantity-no" type="number" name="wps_quantity_offer" value="' . $wps_upsell_bump_products_min_quantity . '" min="' . $wps_upsell_bump_products_min_quantity . '" max="' . $wps_upsell_bump_products_max_quantity . '"></div>';
		}
		$bumphtml .= '<div class="upsell-product-desc">';
		$bumphtml .= '<p class="wps_upsell_offer_product_description">' . $product_description_text . '</p></div>';
		$bumphtml .= '</div>';
		// Product section ends.
	}

	$bumphtml    .= '</div>';

	// Image Product Gallery.
	$wps_product_image_slider = isset( $bump['wps_ubo_offer_product_image_slider'] ) ? $bump['wps_ubo_offer_product_image_slider'] : '';
	if ( 'yes' === $wps_product_image_slider && wps_ubo_lite_if_pro_exists() && ( ( is_cart() ) || ( is_checkout() ) ) ) {
		$bumphtml  .= wps_product_image_gallery_callback( $bump['id'] );
	}

	if ( ! empty( $description ) || is_admin() ) :
		$bumphtml .= '<div class = "wps_upsell_offer_secondary_section upsell-offer-desc" ><p>' . $description . '</p></div>';
	endif;

	// Creating  red arrow html.
	if ( 'on' === $wps_enable_red_arrow_feature ) {
		$wps_ubo_red_arrow_svg  = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 198 111.52" demo="0 0 198 111.52"><defs></defs><g id="a"/><g id="b"><g id="c"><polygon class="d" points="198 25.35 198 86.17 96.62 86.17 96.62 111.52 48.36 83.64 0 55.76 48.36 27.88 96.62 0 96.62 25.35 198 25.35"/></g></g></svg>';
		$wps_ubo_red_arrow_html = '<div class="wps_upsell_offer_arrow">' . $wps_ubo_red_arrow_svg . '</div>';
	} else {
		$wps_ubo_red_arrow_html = '';
	}

	// Wrapper div end.
	$bumphtml .= '</div>';

	// Parent wrapper end.
	$bumphtml  .= '<div class="upsell-order-footer">';
	$bumphtml .= '<div class = "wps_upsell_offer_primary_section" >';
	$bumphtml .= '<div class="upsell-order-check">';
	$bumphtml .= $wps_ubo_red_arrow_html;
	$bumphtml .= '<label class="wps_upsell_bump_checkbox_container">';
	$bumphtml .= '<input type="checkbox" ' . $check . ' name="add_offer_in_cart_checkbox" class ="add_offer_in_cart" id ="wps_checkbox_offer' . esc_html( $order_bump_key ) . '">';
	$bumphtml .= '<span class="checkmark"></span>';
	$bumphtml .= '</label>';
	$bumphtml .= '<h5 class="add_offer_in_cart_text">' . $title . '</h5>';
	$bumphtml .= '</div></div>';
	$bumphtml .= '</div></div></div></div>';

	// For simple product.
	if ( wps_ubo_lite_if_pro_exists() && ! empty( $offer_product ) && is_object( $offer_product ) && ! $offer_product->has_child() && ! is_admin() ) {

		$bumphtml .= apply_filters( 'wps_meta_forms_allowed_submission', $order_bump_div_id, $bump['meta_forms_allowed'], $bump['meta_form_fields'] );
	}
	return $bumphtml;
}

/**
 * Bump Offer Html For Winner Jazz.
 *
 * @param   string $bump        Consists all data about order bump.
 * @param   string $encountered_order_bump_id        Consists all data about order bump.
 * @param   string $order_bump_key        Consists all data about order bump.
 * @since   1.0.0
 */
function wps_ubo_lite_bump_offer_html_4( $bump, $encountered_order_bump_id = '', $order_bump_key = '' ) {

	/**
	 * Text fields.
	 */
	$title = ! empty( $bump['design_text']['wps_upsell_offer_title'] ) ? $bump['design_text']['wps_upsell_offer_title'] : '';

	$description = $bump['design_text']['wps_upsell_bump_offer_description'];

	$product_description_text = $bump['design_text']['wps_bump_offer_decsription_text'];

	$discount_title_fixed = ! empty( $bump['design_text']['wps_ubo_discount_title_for_fixed'] ) ? $bump['design_text']['wps_ubo_discount_title_for_fixed'] : '';

	$discount_title_percent = ! empty( $bump['design_text']['wps_ubo_discount_title_for_percent'] ) ? $bump['design_text']['wps_ubo_discount_title_for_percent'] : '';

	if ( ! empty( $bump['bump_price_html'] ) ) {

		$discount_title_fixed   = str_replace( '{dc_price}', $bump['bump_price_html'], $discount_title_fixed );
		$discount_title_percent = str_replace( '{dc_%}', $bump['bump_price_html'], $discount_title_percent );

	}

	if ( ! empty( $bump['price_type'] ) && 'fixed' === $bump['price_type'] ) {

		$bump_price_html = $discount_title_fixed;
	} else {

		$bump_price_html = $discount_title_percent;
	}

	// Template adaption.
	$wps_ubo_global_options    = get_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );
	$wps_ubo_template_adaption = ! empty( $wps_ubo_global_options['wps_ubo_temp_adaption'] ) ? $wps_ubo_global_options['wps_ubo_temp_adaption'] : '';

	// Setting to enable disable permalink.
	$wps_bump_enable_permalink = ! empty( $wps_ubo_global_options['wps_bump_enable_permalink'] ) ? $wps_ubo_global_options['wps_bump_enable_permalink'] : '';
	// Red arrow setting.
	$wps_enable_red_arrow_feature = ! empty( $wps_ubo_global_options['wps_enable_red_arrow_feature'] ) ? $wps_ubo_global_options['wps_enable_red_arrow_feature'] : '';
	// Setting for the offer Quantity.
	$wps_upsell_enable_quantity              = ! empty( $bump['wps_upsell_enable_quantity'] ) ? $bump['wps_upsell_enable_quantity'] : '';
	$wps_upsell_bump_products_fixed_quantity = ! empty( $bump['wps_upsell_bump_products_fixed_quantity'] ) ? $bump['wps_upsell_bump_products_fixed_quantity'] : '';
	$wps_upsell_bump_products_min_quantity   = ! empty( $bump['wps_upsell_bump_products_min_quantity'] ) ? $bump['wps_upsell_bump_products_min_quantity'] : '';
	$wps_upsell_bump_products_max_quantity   = ! empty( $bump['wps_upsell_bump_products_max_quantity'] ) ? $bump['wps_upsell_bump_products_max_quantity'] : '';
	$wps_upsell_offer_quantity_type          = ! empty( $bump['wps_upsell_offer_quantity_type'] ) ? $bump['wps_upsell_offer_quantity_type'] : '';

	// PARENT WRAPPER DIV CSS( parent_wrapper_div ).
	$parent_border_type             = ! empty( $bump['design_css']['parent_border_type'] ) ? $bump['design_css']['parent_border_type'] : '';
	$parent_border_color            = ! empty( $bump['design_css']['parent_border_color'] ) ? $bump['design_css']['parent_border_color'] : '';
	$parent_background_color        = ! empty( $bump['design_css']['parent_background_color'] ) ? $bump['design_css']['parent_background_color'] : '';
	$parent_top_vertical_spacing    = ! empty( $bump['design_css']['top_vertical_spacing'] ) ? $bump['design_css']['top_vertical_spacing'] : '';
	$parent_bottom_vertical_spacing = ! empty( $bump['design_css']['bottom_vertical_spacing'] ) ? $bump['design_css']['bottom_vertical_spacing'] : '0';

	// DISCOUNT SECTION( discount_section ).
	$discount_section_background_color = ! empty( $bump['design_css']['discount_section_background_color'] ) ? $bump['design_css']['discount_section_background_color'] : '';
	$discount_section_text_color       = ! empty( $bump['design_css']['discount_section_text_color'] ) ? $bump['design_css']['discount_section_text_color'] : '';
	$discount_section_text_size        = ! empty( $bump['design_css']['discount_section_text_size'] ) ? $bump['design_css']['discount_section_text_size'] : '';

	// PRODUCT SECTION( product_section ).
	$product_section_text_color = ! empty( $bump['design_css']['product_section_text_color'] ) ? $bump['design_css']['product_section_text_color'] : '';
	$product_section_text_size  = ! empty( $bump['design_css']['product_section_text_size'] ) ? $bump['design_css']['product_section_text_size'] : '';
	$product_section_text_price_size  = ! empty( $bump['design_css']['product_section_price_text_size'] ) ? $bump['design_css']['product_section_price_text_size'] : '';
	$product_section_price_text_color = ! empty( $bump['design_css']['product_section_price_text_color'] ) ? $bump['design_css']['product_section_price_text_color'] : '';

	$product_section_img_width  = ! empty( $bump['design_css']['product_section_img_width'] ) ? $bump['design_css']['product_section_img_width'] : '';
	$product_section_img_height = ! empty( $bump['design_css']['product_section_img_height'] ) ? $bump['design_css']['product_section_img_height'] : '';

	// PRIMARY SECTION(primary_section ).
	$primary_section_background_color = ! empty( $bump['design_css']['primary_section_background_color'] ) ? $bump['design_css']['primary_section_background_color'] : '';
	$primary_section_text_color       = ! empty( $bump['design_css']['primary_section_text_color'] ) ? $bump['design_css']['primary_section_text_color'] : '';
	$primary_section_arrow_color      = ! empty( $bump['design_css']['primary_section_arrow_color'] ) ? $bump['design_css']['primary_section_arrow_color'] : '';
	$primary_section_text_size        = ! empty( $bump['design_css']['primary_section_text_size'] ) ? $bump['design_css']['primary_section_text_size'] : '';

	// SECONDARY SECTION( secondary_section ).
	$secondary_section_background_color = ! empty( $bump['design_css']['secondary_section_background_color'] ) ? $bump['design_css']['secondary_section_background_color'] : '';
	$secondary_section_text_color       = ! empty( $bump['design_css']['secondary_section_text_color'] ) ? $bump['design_css']['secondary_section_text_color'] : '';
	$secondary_section_text_size        = ! empty( $bump['design_css']['secondary_section_text_size'] ) ? $bump['design_css']['secondary_section_text_size'] : '';

	$wps_bump_target_attr = ! empty( $wps_ubo_global_options['wps_bump_target_link_attr_val'] ) ? $wps_ubo_global_options['wps_bump_target_link_attr_val'] : '';
	?>

	<?php $parent_border_width = 'double' === $parent_border_type ? '4px' : '2px'; ?>
	<?php
	$important = is_admin() ? '' : '!important';

	$order_bump_div_id = '#wps_upsell_offer_main_id_' . $encountered_order_bump_id;

	?>

	<!--  HTML goes down here. --> 
	<style type="text/css">
		/**
		* All of the CSS for your public-facing functionality should be
		* included in this file.
		*/
		<?php echo esc_html( $order_bump_div_id ); ?> {
			display: block;
			width: 100%;
			padding-top:  <?php echo esc_html( $parent_top_vertical_spacing ) . esc_html( 'px' ); ?>;
			padding-bottom:  <?php echo esc_html( $parent_bottom_vertical_spacing ) . esc_html( 'px' ); ?>;
			clear: both;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_parent_wrapper {
			border: <?php echo esc_html( $parent_border_type . ' ' . $parent_border_color . ' ' . $parent_border_width ); ?>;
			margin: 0 auto;
			<?php if ( 'no' === $wps_ubo_template_adaption ) : ?>
			max-width: 400px;
			<?php endif; ?>
		}
		.wps_upsell_offer_parent_wrapper {
			font-family: 'Source Sans Pro', sans-serif;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_wrapper {
			background-color:<?php echo esc_html( $parent_background_color ); ?>;
			padding : 20px;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_discount_section {
			margin: 0;
			text-align: center;
			background-color: <?php echo esc_html( $discount_section_background_color ); ?>;
			line-height: 1.68;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_discount_section h3 {
			color: <?php echo esc_html( $discount_section_text_color ); ?>;
			margin: 2px;
			padding: 1px;
			font-size: <?php echo esc_html( $discount_section_text_size ) . esc_html( 'px' ); ?>;
			border: none;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_discount_section h3 .amount {
			font-size: inherit;
			color: inherit;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_product_section {
			text-align :left;
			display: -webkit-flex;
			display: -moz-flex;
			display: -ms-flex;
			display: -o-flex;
			display: flex;
			font-size: 16px;
			align-items: start;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_product_section p {
			margin: 0;
			color: <?php echo esc_html( $product_section_text_color ); ?>;
			font-size: <?php echo esc_html( $product_section_text_size ) . esc_html( 'px' ); ?>;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_product_content h4 {
			display: inline-block;
			vertical-align: middle;
			font-weight: 500;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_product_content p {
			white-space: pre-line;
		}
		/* v2.1.7 Start.*/
		<?php echo esc_html( $order_bump_div_id ); ?> p.wps_upsell_offer_product_price {
			font-size: <?php echo esc_html( $product_section_text_price_size ) . esc_html( 'px' ); ?>;
			color: <?php echo esc_html( $product_section_price_text_color ); ?>;
		}

		<?php echo esc_html( $order_bump_div_id ); ?> .upsell-offer-template-2 .quantity{
			font-size: <?php echo esc_html( $product_section_text_price_size ) . esc_html( 'px' ); ?>;
			color: <?php echo esc_html( $product_section_price_text_color ); ?>;
		}

		<?php echo esc_html( $order_bump_div_id ); ?> .upsell-offer-template-2 .wps_upsell_offer_product_description {
			font-size: <?php echo esc_html( $product_section_text_size ) . esc_html( 'px' ); ?>;
			color: <?php echo esc_html( $product_section_text_color ); ?>;
		}

		<?php echo esc_html( $order_bump_div_id ); ?> .upsell-offer-template-2 h4{
			font-size: <?php echo esc_html( $product_section_text_size ) . esc_html( 'px' ); ?>;
			color: <?php echo esc_html( $product_section_text_color ); ?>;
		}

		/* v2.1.7 End.*/

		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_product_section h4 {
			margin: 0;
			color: <?php echo esc_html( $product_section_text_color ); ?>;
			font-size: <?php echo esc_html( $product_section_text_size += 10 ) . esc_html( 'px' ); ?>;
			font-weight: 300;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_product_content {
			/* width: calc(100% - 90px); */
			word-break: break-word;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_primary_section {
			align-items: center;
			background-color: <?php echo esc_html( $primary_section_background_color ); ?>;
			display: flex;
			margin: 14px auto;
			padding: 10px;
		}

		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_primary_section .add_offer_in_cart_text {
			color: <?php echo esc_html( $primary_section_text_color ); ?>;
			font-size: <?php echo esc_html( $primary_section_text_size ) . esc_html( 'px' ); ?>;
			margin: 0 0 0 5px;
			font-weight: 600;
			padding: 0;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_secondary_section {
			padding: 8px;
			background-color: <?php echo esc_html( $secondary_section_background_color ); ?>;
			text-align: center;
			white-space: pre-line;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_secondary_section p {
			color: <?php echo esc_html( $secondary_section_text_color ); ?>;
			margin: 0;
			font-size:<?php echo esc_html( $secondary_section_text_size ) . esc_html( 'px' ); ?>;
		}
		/* Custom checkbox container. */
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_primary_section .wps_upsell_bump_checkbox_container {
			cursor: pointer;
			width: auto;
			font-size: 22px;
			height: 23px;
			margin: 0 0 6px 0;
			padding-left: 35px;
			position: relative;
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
		}
		/* Hide the browser's default checkbox. */
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container input {
			position: absolute;
			opacity: 0;
			cursor: pointer;
			height: 0;
			width: 0;
		}
		/* Create a custom checkbox. */
		<?php echo esc_html( $order_bump_div_id ); ?> .checkmark {
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
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_arrow {
			width: 40px;
			margin-right: 4px;
			transform: scaleX(-1);
			animation: leftright 0.4s infinite ease;
			padding: 0 2px;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_arrow svg {
			width: 100%;
			height: auto;
			fill: <?php echo esc_html( $primary_section_arrow_color ); ?>
		}
		@keyframes leftright {
			0% {
				transform: translateX(-5px)scaleX(-1);
			}
			60% {
				transform: translateX(-2px)scaleX(-1);
			}
			100% {
				transform: translateX(-5px)scaleX(-1);
			}
		}
		/* On mouse-over, add a grey background color. */
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container:hover input ~ .checkmark {
			background-color: #ccc;
		}
		/* When the checkbox is checked, add a blue background. */
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container input:checked ~ .checkmark {
			background-color: #ffffff;
		}
		/* Create the checkmark/indicator (hidden when not checked). */
		<?php echo esc_html( $order_bump_div_id ); ?> .checkmark:after {
			content: "";
			position: absolute;
			display: none;
		}
		/* Show the checkmark when checked. */
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container input:checked ~ .checkmark:after {
			display: block;
		}
		/* Style the checkmark/indicator. */
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container .checkmark:after {
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
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_image {
			/* width: 90px; */
			margin-right: 10px;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_img {
			width: <?php echo esc_html( $product_section_img_width . 'px' ); ?>;
			height: <?php echo esc_html( $product_section_img_height . 'px' ); ?>;
			max-width: 125px;
			max-height: 150px;
		}

		@media only screen and (min-width : 768px) and (max-width: 1100px) {
			.wps_upsell_offer_wrapper .wps_upsell_offer_product_section {
				flex-wrap: wrap;
				justify-content: center;
			}
			.wps_upsell_offer_wrapper .wps_upsell_offer_product_content {
				width: 100%!important;
			}
		}

		@media screen and (max-width: 480px) {
			<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_product_content {
				margin-left: 0;
			}
		}

	</style>

	<?php

	// Incase no offer is added return.
	$bump['id']   = ! empty( $bump['id'] ) ? sanitize_text_field( $bump['id'] ) : '';
	$bump_product = wc_get_product( $bump['id'] );

	// If offer not found return.
	if ( empty( $bump['id'] ) || empty( $bump_product ) ) {

		return;
	}

	$bump['name'] = ! empty( $bump['name'] ) ? sanitize_text_field( $bump['name'] ) : '';

	$bump['discount_price'] = ! empty( $bump['discount_price'] ) ? sanitize_text_field( $bump['discount_price'] ) : '0';

	// After v2.0.1!
	if ( ! empty( $bump['offer_image'] ) ) {
		$image = wp_get_attachment_image_src( $bump['offer_image'], 'single-post-thumbnail' )[0];
	}

	// If still not found.
	if ( empty( $image ) ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $bump['id'] ), 'single-post-thumbnail' )[0];
	}

	if ( empty( $image ) ) {

		$bump_parent_id = $bump_product->get_parent_id();

		if ( ! empty( $bump_parent_id ) ) {

			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $bump_parent_id ), 'single-post-thumbnail' )[0];

		} else {

			$image = wc_placeholder_img_src();
		}
	}

	// Add url of the offer product in the bump info.
	$bump_offer_product_permalink = esc_url_raw( get_permalink( $bump['id'] ) );

	$check = '';

	// Retain Checked if offer is added except for admin.
	if ( ! is_admin() && function_exists( 'WC' ) && ! empty( WC()->session ) ) {

		if ( null !== WC()->session->get( "bump_offer_status_index_$order_bump_key" ) ) {

			$check = 'checked';
		}
	}

	/*
	* Get price html.
	*/
	$bump_offer_price = wps_ubo_lite_custom_price_html( $bump['id'], $bump['discount_price'] );

	/**
	 * Html for bump offer.
	 */
	$bumphtml = '';

	// parent wrapper start.
	$bumphtml .= '<div id="wps_upsell_offer_main_id_' . $encountered_order_bump_id . '" class = "wps_upsell_offer_main_wrapper wps_ubo_wrapper_index_' . $order_bump_key . '" >';

	$bumphtml .= '<input type="hidden" class ="offer_shown_id" value="' . $bump['id'] . '">';
	$bumphtml .= '<input type="hidden" class ="offer_shown_discount" value="' . $bump['discount_price'] . '">';
	$bumphtml .= '<input type="hidden" class ="target_id_cart_key" value="' . $bump['target_key'] . '">';
	$bumphtml .= '<input type="hidden" class ="order_bump_index" value="index_' . $order_bump_key . '">';
	$bumphtml .= '<input type="hidden" class ="order_bump_id" value="' . $encountered_order_bump_id . '">';

	$offer_product = wc_get_product( $bump['id'] );

	if ( ! empty( $offer_product ) && is_object( $offer_product ) && $offer_product->has_child() ) {

		$bumphtml .= '<input type="hidden" class ="offer_shown_id_type" value="variable">';
	}

	if ( ! empty( $bump['smart_offer_upgrade'] ) && 'yes' === $bump['smart_offer_upgrade'] ) {

		$bumphtml .= '<input type="hidden" class="order_bump_smo" value=' . $bump['smart_offer_upgrade'] . '>';
	}

	if ( is_admin() && ! empty( $bump['bump_price_at_zero'] ) ) :
		$bumphtml .= '<input type="hidden" class="bump_price_at_zero" value=' . $bump['bump_price_at_zero'] . '>';
	endif;

	$bumphtml .= '<div class = "wps_upsell_offer_parent_wrapper" >';

	$bumphtml .= '<div class="upsell-offer-template upsell-offer-template-2">';

	$bumphtml .= '<div class="upsell-offer-header">';

	$bumphtml .= '<div class="upsell-offer-timer-section"><div class="upsell-offer-time">';

	$bumphtml .= '<div id = "wps_admin_timer"></div>';
	$wps_counter_timer_enable = isset( $bump['counter_timer'] ) ? $bump['counter_timer'] : '';
	$wps_evergreencounter_timer_enable = isset( $bump['evergreen_counter_timer'] ) ? $bump['evergreen_counter_timer'] : '';
	// Countdown Timer Section start.
	if ( ( 'yes' === $wps_counter_timer_enable || 'yes' == $wps_evergreencounter_timer_enable ) && wps_ubo_lite_if_pro_exists() ) {
		$bumphtml .= '<div class="expired_message_class" id = "expired_message' . esc_html( $order_bump_key ) . '"></div>';
		$bumphtml .= '<div class = "wps_timer_count wps_upsell_offer_discount_section" id ="wps_timer' . esc_html( $order_bump_key ) . '">
		<div class = "wps_day_timer_block wps-timer-wrap" >
		<div id ="wps_day_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_day_label">Days</div>
		</div>
		<div class ="wps_timer_sept">:</div>

		<div class = "wps_hour_timer_block wps-timer-wrap">
		<div id ="wps_hour_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_hour_label">Hour</div>
		</div>
		<div class ="wps_timer_sept">:</div>

		<div class = "wps_min_timer_block wps-timer-wrap">
		<div id ="wps_min_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_min_label">Min</div>
		</div>
		<div class ="wps_timer_sept">:</div>

		<div class = "wps_sec_timer_block wps-timer-wrap">
		<div id ="wps_sec_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_sec_label">Sec</div>
		</div>
		</div>';
	}

	$bumphtml .= '</div></div>';
	// Countdown Timer Section End.

	// discount section start.
	$bumphtml .= '<div class="uspell-offer-discount">';
	$bumphtml .= '<div class = "wps_upsell_offer_discount_section" >';
	$bumphtml .= '<h3><b>' . $bump_price_html . '</b></h3>';
	$bumphtml .= '</div>';
	$bumphtml .= '</div></div>';
	// discount section end.

	if ( 'fixed_q' === $wps_upsell_offer_quantity_type ) {
		$wps_is_fixed_qty = 'true';
	} else {
		$wps_is_fixed_qty = 'false';
	}
	// wrapper div start.
	$bumphtml .= '<div class = "wps_upsell_offer_wrapper" >';

	if ( 'on' === $wps_bump_enable_permalink ) {
		// product section start with permalink.
		$bumphtml .= '<div class = "wps_upsell_offer_product_section1" >';
		$bumphtml .= '<div class="upsell-offer-body">';
		$bumphtml .= '<div class="upsell-product">';
		$bumphtml .= '<div class="upsell-product-img">';
		$bumphtml .= '<div class = "wps_upsell_offer_image" >';
		$bumphtml .= '<a target="' . $wps_bump_target_attr . '" href="' . $bump_offer_product_permalink . '"><img class="wps_upsell_offer_img" src="' . esc_url( $image ) . '" data-id="' . $bump['id'] . '"></a>';
		$bumphtml .= '</div>';
		$bumphtml .= '</div>';

		$bumphtml .= '<div class="upsell-product-info">';
		$bumphtml .= '<div class="wps_upsell_offer_product_content"> <h4 class="wps_bump_name" data-qty_allowed="' . esc_html( $wps_upsell_enable_quantity ) . '" data-wps_is_fixed_qty="' . esc_html( $wps_is_fixed_qty ) . '" data-wps_qty="' . esc_html( $wps_upsell_bump_products_fixed_quantity ) . '"><a target="' . esc_html( $wps_bump_target_attr ) . '" class="wps_upsell_product_permalink" href="' . esc_url( $bump_offer_product_permalink ) . '">' . esc_html( $bump['name'] ) . '</a></h4>';
		$bumphtml .= '<div class="product-price">';
		$bumphtml .= '<p class="wps_upsell_offer_product_price">' . $bump_offer_price . '</p></div>';

		$bumphtml .= '<div class="quantity">';
		if ( 'yes' === $wps_upsell_enable_quantity && 'variable_q' === $wps_upsell_offer_quantity_type && wps_ubo_lite_if_pro_exists() && ! $bump_product->is_type( 'variable' ) ) {
			$bumphtml .= '<label for="wps_quantity_offer">' . __( 'Quantity', 'upsell-order-bump-offer-for-woocommerce' ) . ':</label>';
			$bumphtml .= '<input class="wps_input_quantity wps_quantity_input" type="number" name="wps_quantity_offer" value="' . $wps_upsell_bump_products_min_quantity . '" min="' . $wps_upsell_bump_products_min_quantity . '" max="' . $wps_upsell_bump_products_max_quantity . '">';
		} else {
			$bumphtml .= '<label for="wps_quantity_offer">' . __( 'Quantity', 'upsell-order-bump-offer-for-woocommerce' ) . ':1</label>';
		}
		$bumphtml .= '</div>';
		$bumphtml .= '<div class="upsell-product-desc">';
		$bumphtml .= '<p class="wps_upsell_offer_product_description">' . $product_description_text . '</p></div></div></div>';
		$bumphtml .= '</div>';
		$bumphtml .= '</div>';
		// Product section ends.
	} else {
		// product section start without permalink.
		$bumphtml .= '<div class = "wps_upsell_offer_product_section1" >';
		$bumphtml .= '<div class="upsell-offer-body">';
		$bumphtml .= '<div class="upsell-product">';
		$bumphtml .= '<div class="upsell-product-img">';
		$bumphtml .= '<div class = "wps_upsell_offer_image" >';
		$bumphtml .= '<img class="wps_upsell_offer_img" src="' . esc_url( $image ) . '" data-id="' . esc_html( $bump['id'] ) . '">';
		$bumphtml .= '</div>';
		$bumphtml .= '</div>';

		$bumphtml .= '<div class="upsell-product-info">';
		$bumphtml .= '<div class="wps_upsell_offer_product_content"> <h4 class="wps_bump_name" data-qty_allowed="' . esc_html( $wps_upsell_enable_quantity ) . '" data-wps_is_fixed_qty="' . esc_html( $wps_is_fixed_qty ) . '" data-wps_qty="' . esc_html( $wps_upsell_bump_products_fixed_quantity ) . '">' . esc_html( $bump['name'] ) . '</h4>';
		$bumphtml .= '<div class="product-price">';
		$bumphtml .= '<p class="wps_upsell_offer_product_price">' . $bump_offer_price . '</p></div>';

		$bumphtml .= '<div class="quantity">';
		if ( 'yes' === $wps_upsell_enable_quantity && 'variable_q' === $wps_upsell_offer_quantity_type && wps_ubo_lite_if_pro_exists() && ! $bump_product->is_type( 'variable' ) ) {
			$bumphtml .= '<label for="wps_quantity_offer">' . __( 'Quantity', 'upsell-order-bump-offer-for-woocommerce' ) . ':</label>';
			$bumphtml .= '<input class="wps_input_quantity wps_quantity_input" type="number" name="wps_quantity_offer" value="' . $wps_upsell_bump_products_min_quantity . '" min="' . $wps_upsell_bump_products_min_quantity . '" max="' . $wps_upsell_bump_products_max_quantity . '">';
		} else {
			$bumphtml .= '<label for="wps_quantity_offer">' . __( 'Quantity', 'upsell-order-bump-offer-for-woocommerce' ) . ':1</label>';
		}
		$bumphtml .= '</div>';

		$bumphtml .= '<div class="upsell-product-desc">';
		$bumphtml .= '<p class="wps_upsell_offer_product_description">' . $product_description_text . '</p></div></div></div>';
		$bumphtml .= '</div>';
		$bumphtml .= '</div>';
	}

	// Image Product Gallery.
	$wps_product_image_slider = isset( $bump['wps_ubo_offer_product_image_slider'] ) ? $bump['wps_ubo_offer_product_image_slider'] : '';
	if ( 'yes' === $wps_product_image_slider && wps_ubo_lite_if_pro_exists() && ( ( is_cart() ) || ( is_checkout() ) ) ) {
		$bumphtml  .= wps_product_image_gallery_callback( $bump['id'] );
	}

	// Secondary section start.
	// When don't show this when empty except for admin as it involves Live Preview.
	if ( ! empty( $description ) || is_admin() ) :
		$bumphtml .= '<div class = "wps_upsell_offer_secondary_section" ><p>' . $description . '</p></div>';
	endif;

	$bumphtml .= '</div>';

	$bumphtml .= '<div class="upsell-order-footer">';
	// Creating  red arrow html.
	if ( 'on' === $wps_enable_red_arrow_feature ) {
		$wps_ubo_red_arrow_svg  = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 198 111.52" demo="0 0 198 111.52"><defs></defs><g id="a"/><g id="b"><g id="c"><polygon class="d" points="198 25.35 198 86.17 96.62 86.17 96.62 111.52 48.36 83.64 0 55.76 48.36 27.88 96.62 0 96.62 25.35 198 25.35"/></g></g></svg>';
		$wps_ubo_red_arrow_html = '<div class="wps_upsell_offer_arrow">' . $wps_ubo_red_arrow_svg . '</div>';
	} else {
		$wps_ubo_red_arrow_html = '';
	}

	// Primary section start.
	$bumphtml .= '<div class = "wps_upsell_offer_primary_section" >';
	$bumphtml .= $wps_ubo_red_arrow_html;
	$bumphtml .= '<div class="upsell-order-check">';
	$bumphtml .= '<label class="wps_upsell_bump_checkbox_container">';
	$bumphtml .= '<input type="checkbox" ' . $check . ' name="add_offer_in_cart_checkbox" class ="add_offer_in_cart" id ="wps_checkbox_offer' . esc_html( $order_bump_key ) . '">';
	$bumphtml .= '<span class="checkmark"></span>';
	$bumphtml .= '</label>';
	$bumphtml .= '</div>';
	$bumphtml .= '<h5 class="add_offer_in_cart_text">' . $title . '</h5>';
	$bumphtml .= '</div>';
	$bumphtml .= '</div>';
	// Primary section end.

	// Secondary section end.

	$bumphtml .= '</div>';
	// Wrapper div end.
	$bumphtml .= '</div>';

	// Parent wrapper end.
	$bumphtml .= '</div></div>';

	// For simple product.
	if ( wps_ubo_lite_if_pro_exists() && ! empty( $offer_product ) && is_object( $offer_product ) && ! $offer_product->has_child() && ! is_admin() ) {

		$bumphtml .= apply_filters( 'wps_meta_forms_allowed_submission', $order_bump_div_id, $bump['meta_forms_allowed'], $bump['meta_form_fields'] );
	}

	return $bumphtml;
}


/**
 * Bump Offer Html For Summer Cool.
 *
 * @param   string $bump        Consists all data about order bump.
 * @param   string $encountered_order_bump_id        Consists all data about order bump.
 * @param   string $order_bump_key        Consists all data about order bump.
 * @since   1.0.0
 */
function wps_ubo_lite_bump_offer_html_5( $bump, $encountered_order_bump_id = '', $order_bump_key = '' ) {

	/**
	 * Text fields.
	 */
	$title = ! empty( $bump['design_text']['wps_upsell_offer_title'] ) ? $bump['design_text']['wps_upsell_offer_title'] : '';

	$description = $bump['design_text']['wps_upsell_bump_offer_description'];

	$product_description_text = $bump['design_text']['wps_bump_offer_decsription_text'];

	$discount_title_fixed = ! empty( $bump['design_text']['wps_ubo_discount_title_for_fixed'] ) ? $bump['design_text']['wps_ubo_discount_title_for_fixed'] : '';

	$discount_title_percent = ! empty( $bump['design_text']['wps_ubo_discount_title_for_percent'] ) ? $bump['design_text']['wps_ubo_discount_title_for_percent'] : '';

	if ( ! empty( $bump['bump_price_html'] ) ) {

		$discount_title_fixed   = str_replace( '{dc_price}', $bump['bump_price_html'], $discount_title_fixed );
		$discount_title_percent = str_replace( '{dc_%}', $bump['bump_price_html'], $discount_title_percent );

	}

	if ( ! empty( $bump['price_type'] ) && 'fixed' === $bump['price_type'] ) {

		$bump_price_html = $discount_title_fixed;
	} else {

		$bump_price_html = $discount_title_percent;
	}

	// Template adaption.
	$wps_ubo_global_options    = get_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );
	$wps_ubo_template_adaption = ! empty( $wps_ubo_global_options['wps_ubo_temp_adaption'] ) ? $wps_ubo_global_options['wps_ubo_temp_adaption'] : '';

	// Setting to enable disable permalink.
	$wps_bump_enable_permalink = ! empty( $wps_ubo_global_options['wps_bump_enable_permalink'] ) ? $wps_ubo_global_options['wps_bump_enable_permalink'] : '';
	// Red arrow setting.
	$wps_enable_red_arrow_feature = ! empty( $wps_ubo_global_options['wps_enable_red_arrow_feature'] ) ? $wps_ubo_global_options['wps_enable_red_arrow_feature'] : '';
	// Setting for the offer Quantity.
	$wps_upsell_enable_quantity              = ! empty( $bump['wps_upsell_enable_quantity'] ) ? $bump['wps_upsell_enable_quantity'] : '';
	$wps_upsell_bump_products_fixed_quantity = ! empty( $bump['wps_upsell_bump_products_fixed_quantity'] ) ? $bump['wps_upsell_bump_products_fixed_quantity'] : '';
	$wps_upsell_bump_products_min_quantity   = ! empty( $bump['wps_upsell_bump_products_min_quantity'] ) ? $bump['wps_upsell_bump_products_min_quantity'] : '';
	$wps_upsell_bump_products_max_quantity   = ! empty( $bump['wps_upsell_bump_products_max_quantity'] ) ? $bump['wps_upsell_bump_products_max_quantity'] : '';
	$wps_upsell_offer_quantity_type          = ! empty( $bump['wps_upsell_offer_quantity_type'] ) ? $bump['wps_upsell_offer_quantity_type'] : '';

	// PARENT WRAPPER DIV CSS( parent_wrapper_div ).
	$parent_border_type             = ! empty( $bump['design_css']['parent_border_type'] ) ? $bump['design_css']['parent_border_type'] : '';
	$parent_border_color            = ! empty( $bump['design_css']['parent_border_color'] ) ? $bump['design_css']['parent_border_color'] : '';
	$parent_background_color        = ! empty( $bump['design_css']['parent_background_color'] ) ? $bump['design_css']['parent_background_color'] : '';
	$parent_top_vertical_spacing    = ! empty( $bump['design_css']['top_vertical_spacing'] ) ? $bump['design_css']['top_vertical_spacing'] : '';
	$parent_bottom_vertical_spacing = ! empty( $bump['design_css']['bottom_vertical_spacing'] ) ? $bump['design_css']['bottom_vertical_spacing'] : '0';

	// DISCOUNT SECTION( discount_section ).
	$discount_section_background_color = ! empty( $bump['design_css']['discount_section_background_color'] ) ? $bump['design_css']['discount_section_background_color'] : '';
	$discount_section_text_color       = ! empty( $bump['design_css']['discount_section_text_color'] ) ? $bump['design_css']['discount_section_text_color'] : '';
	$discount_section_text_size        = ! empty( $bump['design_css']['discount_section_text_size'] ) ? $bump['design_css']['discount_section_text_size'] : '';

	// PRODUCT SECTION( product_section ).
	$product_section_text_color = ! empty( $bump['design_css']['product_section_text_color'] ) ? $bump['design_css']['product_section_text_color'] : '';
	$product_section_text_size  = ! empty( $bump['design_css']['product_section_text_size'] ) ? $bump['design_css']['product_section_text_size'] : '';
	$product_section_text_price_size  = ! empty( $bump['design_css']['product_section_price_text_size'] ) ? $bump['design_css']['product_section_price_text_size'] : '';
	$product_section_price_text_color = ! empty( $bump['design_css']['product_section_price_text_color'] ) ? $bump['design_css']['product_section_price_text_color'] : '';

	$product_section_img_width  = ! empty( $bump['design_css']['product_section_img_width'] ) ? $bump['design_css']['product_section_img_width'] : '';
	$product_section_img_height = ! empty( $bump['design_css']['product_section_img_height'] ) ? $bump['design_css']['product_section_img_height'] : '';

	// PRIMARY SECTION(primary_section ).
	$primary_section_background_color = ! empty( $bump['design_css']['primary_section_background_color'] ) ? $bump['design_css']['primary_section_background_color'] : '';
	$primary_section_text_color       = ! empty( $bump['design_css']['primary_section_text_color'] ) ? $bump['design_css']['primary_section_text_color'] : '';
	$primary_section_arrow_color      = ! empty( $bump['design_css']['primary_section_arrow_color'] ) ? $bump['design_css']['primary_section_arrow_color'] : '';
	$primary_section_text_size        = ! empty( $bump['design_css']['primary_section_text_size'] ) ? $bump['design_css']['primary_section_text_size'] : '';

	// SECONDARY SECTION( secondary_section ).
	$secondary_section_background_color = ! empty( $bump['design_css']['secondary_section_background_color'] ) ? $bump['design_css']['secondary_section_background_color'] : '';
	$secondary_section_text_color       = ! empty( $bump['design_css']['secondary_section_text_color'] ) ? $bump['design_css']['secondary_section_text_color'] : '';
	$secondary_section_text_size        = ! empty( $bump['design_css']['secondary_section_text_size'] ) ? $bump['design_css']['secondary_section_text_size'] : '';

	$wps_bump_target_attr = ! empty( $wps_ubo_global_options['wps_bump_target_link_attr_val'] ) ? $wps_ubo_global_options['wps_bump_target_link_attr_val'] : '';
	?>

	<?php $parent_border_width = 'double' === $parent_border_type ? '4px' : '2px'; ?>
	<?php
	$important = is_admin() ? '' : '!important';

	$order_bump_div_id = '#wps_upsell_offer_main_id_' . $encountered_order_bump_id;

	?>

	<!--  HTML goes down here. --> 
	<style type="text/css">
		/**
		* All of the CSS for your public-facing functionality should be
		* included in this file.
		*/
		<?php echo esc_html( $order_bump_div_id ); ?> {
			display: block;
			width: 100%;
			padding-top:  <?php echo esc_html( $parent_top_vertical_spacing ) . esc_html( 'px' ); ?>;
			padding-bottom:  <?php echo esc_html( $parent_bottom_vertical_spacing ) . esc_html( 'px' ); ?>;
			clear: both;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_parent_wrapper {
			border: <?php echo esc_html( $parent_border_type . ' ' . $parent_border_color . ' ' . $parent_border_width ); ?>;
			margin: 0 auto;
			<?php if ( 'no' === $wps_ubo_template_adaption ) : ?>
			max-width: 400px;
			<?php endif; ?>
		}
		.wps_upsell_offer_parent_wrapper {
			font-family: 'Source Sans Pro', sans-serif;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_wrapper {
			background-color:<?php echo esc_html( $parent_background_color ); ?>;
			padding : 20px;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_discount_section {
			margin: 0;
			text-align: center;
			background-color: <?php echo esc_html( $discount_section_background_color ); ?>;
			line-height: 1.68;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_discount_section h3 {
			color: <?php echo esc_html( $discount_section_text_color ); ?>;
			margin: 2px;
			padding: 1px;
			font-size: <?php echo esc_html( $discount_section_text_size ) . esc_html( 'px' ); ?>;
			border: none;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_discount_section h3 .amount {
			font-size: inherit;
			color: inherit;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_product_section {
			text-align :left;
			display: -webkit-flex;
			display: -moz-flex;
			display: -ms-flex;
			display: -o-flex;
			display: flex;
			font-size: 16px;
			align-items: start;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .upsell-offer-template-3 .wps_upsell_offer_product_section .wps_upsell_offer_product_description {
			margin: 0;
			color: <?php echo esc_html( $product_section_text_color ); ?>;
			font-size: <?php echo esc_html( $product_section_text_size ) . esc_html( 'px' ); ?>;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_product_content h4 {
			display: inline-block;
			vertical-align: middle;
			font-weight: 500;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_product_content p {
			white-space: pre-line;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .upsell-offer-template-3 p.wps_upsell_offer_product_price {
			color: <?php echo esc_html( $product_section_price_text_color ); ?>;
			font-size: <?php echo esc_html( $product_section_text_price_size ) . esc_html( 'px' ); ?>;
			font-weight: 700;
		}
		<?php echo esc_html( $order_bump_div_id ); ?>.upsell-offer-template-3 p.wps_upsell_offer_product_price del{
			color: <?php echo esc_html( $product_section_price_text_color ); ?>;
			font-size: <?php echo esc_html( $product_section_text_price_size ) . esc_html( 'px' ); ?>;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .upsell-offer-template-3 .wps_upsell_offer_product_section h4 {
			margin: 0;
			color: <?php echo esc_html( $product_section_text_color ); ?>;
			font-size: <?php echo esc_html( $product_section_text_size += 10 ) . esc_html( 'px' ); ?>;
			font-weight: 300;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_product_content {
			word-break: break-word;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_primary_section {
			align-items: center;
			background-color: <?php echo esc_html( $primary_section_background_color ); ?>;
			display: flex;
			margin: 14px auto;
			padding: 10px;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_primary_section .add_offer_in_cart_text {
			color: <?php echo esc_html( $primary_section_text_color ); ?>;
			font-size: <?php echo esc_html( $primary_section_text_size ) . esc_html( 'px' ); ?>;
			margin: 0 0 0 5px;
			font-weight: 600;
			padding: 0;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_secondary_section {
			padding: 8px;
			background-color: <?php echo esc_html( $secondary_section_background_color ); ?>;
			text-align: center;
			white-space: pre-line;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_secondary_section p {
			color: <?php echo esc_html( $secondary_section_text_color ); ?>;
			margin: 0;
			font-size:<?php echo esc_html( $secondary_section_text_size ) . esc_html( 'px' ); ?>;
		}
		/* Custom checkbox container. */
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_primary_section .wps_upsell_bump_checkbox_container {
			cursor: pointer;
			width: auto;
			font-size: 22px;
			height: 23px;
			margin: 0 0 6px 0;
			padding-left: 35px;
			position: relative;
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
		}
		/* Hide the browser's default checkbox. */
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container input {
			position: absolute;
			opacity: 0;
			cursor: pointer;
			height: 0;
			width: 0;
		}
		/* Create a custom checkbox. */
		<?php echo esc_html( $order_bump_div_id ); ?> .checkmark {
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
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_arrow {
			width: 40px;
			margin-right: 4px;
			transform: scaleX(-1);
			animation: leftright 0.4s infinite ease;
			padding: 0 2px;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_arrow svg {
			width: 100%;
			height: auto;
			/* fill: #eb483f; */
			fill: <?php echo esc_html( $primary_section_arrow_color ); ?>
		}
		@keyframes leftright {
			0% {
				transform: translateX(-5px)scaleX(-1);
			}
			60% {
				transform: translateX(-2px)scaleX(-1);
			}
			100% {
				transform: translateX(-5px)scaleX(-1);
			}
		}
		/* On mouse-over, add a grey background color. */
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container:hover input ~ .checkmark {
			background-color: #ccc;
		}
		/* When the checkbox is checked, add a blue background. */
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container input:checked ~ .checkmark {
			background-color: #ffffff;
		}
		/* Create the checkmark/indicator (hidden when not checked). */
		<?php echo esc_html( $order_bump_div_id ); ?> .checkmark:after {
			content: "";
			position: absolute;
			display: none;
		}
		/* Show the checkmark when checked. */
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container input:checked ~ .checkmark:after {
			display: block;
		}
		/* Style the checkmark/indicator. */
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container .checkmark:after {
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
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_image {
			margin-right: 10px;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_img {
			width: <?php echo esc_html( $product_section_img_width . 'px' ); ?>;
			height: <?php echo esc_html( $product_section_img_height . 'px' ); ?>;
			max-width: 80px;
			max-height: 200px;
		}

		@media only screen and (min-width : 768px) and (max-width: 1100px) {
			.wps_upsell_offer_wrapper .wps_upsell_offer_product_section {
				flex-wrap: wrap;
				justify-content: center;
			}
			.wps_upsell_offer_wrapper .wps_upsell_offer_product_content {
				width: 100%!important;
			}
		}

		@media screen and (max-width: 480px) {
			<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_product_content {
				margin-left: 0;
			}
		}

	</style>

	<?php

	// Incase no offer is added return.
	$bump['id']   = ! empty( $bump['id'] ) ? sanitize_text_field( $bump['id'] ) : '';
	$bump_product = wc_get_product( $bump['id'] );

	// If offer not found return.
	if ( empty( $bump['id'] ) || empty( $bump_product ) ) {

		return;
	}

	$bump['name'] = ! empty( $bump['name'] ) ? sanitize_text_field( $bump['name'] ) : '';

	$bump['discount_price'] = ! empty( $bump['discount_price'] ) ? sanitize_text_field( $bump['discount_price'] ) : '0';

	// After v2.0.1!
	if ( ! empty( $bump['offer_image'] ) ) {
		$image = wp_get_attachment_image_src( $bump['offer_image'], 'single-post-thumbnail' )[0];
	}

	// If still not found.
	if ( empty( $image ) ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $bump['id'] ), 'single-post-thumbnail' )[0];
	}

	if ( empty( $image ) ) {

		$bump_parent_id = $bump_product->get_parent_id();

		if ( ! empty( $bump_parent_id ) ) {

			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $bump_parent_id ), 'single-post-thumbnail' )[0];

		} else {

			$image = wc_placeholder_img_src();
		}
	}

	// Add url of the offer product in the bump info.
	$bump_offer_product_permalink = esc_url_raw( get_permalink( $bump['id'] ) );

	$check = '';

	// Retain Checked if offer is added except for admin.
	if ( ! is_admin() && function_exists( 'WC' ) && ! empty( WC()->session ) ) {

		if ( null !== WC()->session->get( "bump_offer_status_index_$order_bump_key" ) ) {

			$check = 'checked';
		}
	}

	/*
	* Get price html.
	*/
	$bump_offer_price = wps_ubo_lite_custom_price_html( $bump['id'], $bump['discount_price'] );

	/**
	 * Html for bump offer.
	 */
	$bumphtml = '';

	// parent wrapper start.
	$bumphtml .= '<div id="wps_upsell_offer_main_id_' . $encountered_order_bump_id . '" class = "wps_upsell_offer_main_wrapper wps_ubo_wrapper_index_' . $order_bump_key . '" >';

	$bumphtml .= '<input type="hidden" class ="offer_shown_id" value="' . $bump['id'] . '">';
	$bumphtml .= '<input type="hidden" class ="offer_shown_discount" value="' . $bump['discount_price'] . '">';
	$bumphtml .= '<input type="hidden" class ="target_id_cart_key" value="' . $bump['target_key'] . '">';
	$bumphtml .= '<input type="hidden" class ="order_bump_index" value="index_' . $order_bump_key . '">';
	$bumphtml .= '<input type="hidden" class ="order_bump_id" value="' . $encountered_order_bump_id . '">';

	$offer_product = wc_get_product( $bump['id'] );

	if ( ! empty( $offer_product ) && is_object( $offer_product ) && $offer_product->has_child() ) {

		$bumphtml .= '<input type="hidden" class ="offer_shown_id_type" value="variable">';
	}

	if ( ! empty( $bump['smart_offer_upgrade'] ) && 'yes' === $bump['smart_offer_upgrade'] ) {

		$bumphtml .= '<input type="hidden" class="order_bump_smo" value=' . $bump['smart_offer_upgrade'] . '>';
	}

	if ( is_admin() && ! empty( $bump['bump_price_at_zero'] ) ) :
		$bumphtml .= '<input type="hidden" class="bump_price_at_zero" value=' . $bump['bump_price_at_zero'] . '>';
	endif;

	$bumphtml                .= '<div class = "wps_upsell_offer_parent_wrapper" >';

	$bumphtml                .= '<div class="upsell-offer-template upsell-offer-template-3">';

	$bumphtml                .= ' <div class="upsell-offer-header">';

	$bumphtml .= '<div class="upsell-offer-timer-section">';
	$bumphtml .= '<div class="upsell-offer-time">';
	$bumphtml                .= '<div id = "wps_admin_timer"></div>';
	$wps_counter_timer_enable = isset( $bump['counter_timer'] ) ? $bump['counter_timer'] : '';
	$wps_evergreencounter_timer_enable = isset( $bump['evergreen_counter_timer'] ) ? $bump['evergreen_counter_timer'] : '';
	// Countdown Timer Section start.
	if ( ( 'yes' === $wps_counter_timer_enable || 'yes' == $wps_evergreencounter_timer_enable ) && wps_ubo_lite_if_pro_exists() ) {
		$bumphtml .= '<div class="expired_message_class" id = "expired_message' . esc_html( $order_bump_key ) . '"></div>';
		$bumphtml .= '<div class = "wps_timer_count wps_upsell_offer_discount_section" id ="wps_timer' . esc_html( $order_bump_key ) . '">
		<div class = "wps_day_timer_block wps-timer-wrap" >
		<div id ="wps_day_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_day_label">Days</div>
		</div>
		<div class ="wps_timer_sept">:</div>

		<div class = "wps_hour_timer_block wps-timer-wrap">
		<div id ="wps_hour_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_hour_label">Hour</div>
		</div>
		<div class ="wps_timer_sept">:</div>

		<div class = "wps_min_timer_block wps-timer-wrap">
		<div id ="wps_min_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_min_label">Min</div>
		</div>
		<div class ="wps_timer_sept">:</div>

		<div class = "wps_sec_timer_block wps-timer-wrap">
		<div id ="wps_sec_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_sec_label">Sec</div>
		</div>
		</div>';
	}
	// Countdown Timer Section End.

	$bumphtml .= '</div>';
	$bumphtml .= '</div>';

	// discount section start.
	$bumphtml .= '<div class="uspell-offer-discount">';
	$bumphtml .= '<div class = "wps_upsell_offer_discount_section" >';
	$bumphtml .= '<h3><b>' . $bump_price_html . '</b></h3>';
	$bumphtml .= '</div>';
	$bumphtml .= '</div>';
	$bumphtml .= '</div>';// E.
	// discount section end.

	if ( 'fixed_q' === $wps_upsell_offer_quantity_type ) {
		$wps_is_fixed_qty = 'true';
	} else {
		$wps_is_fixed_qty = 'false';
	}
	// wrapper div start.
	$bumphtml .= '<div class = "wps_upsell_offer_wrapper" >';

	if ( 'on' === $wps_bump_enable_permalink ) {
		// product section start with permalink.
		$bumphtml .= '<div class="upsell-offer-body">';// c.
		$bumphtml .= '<div class="upsell-product">';// B.

		$bumphtml .= '<div class="upsell-product-img">';
		$bumphtml .= '<div class = "wps_upsell_offer_image" >';
		$bumphtml .= '<a target="' . $wps_bump_target_attr . '" href="' . $bump_offer_product_permalink . '"><img class="wps_upsell_offer_img" src="' . esc_url( $image ) . '" data-id="' . $bump['id'] . '"></a>';
		$bumphtml .= '</div>';
		$bumphtml .= '</div>';

		$bumphtml .= '<div class="upsell-product-info">';// A.
		$bumphtml .= '<div class = "wps_upsell_offer_product_section" >';
		$bumphtml .= '<div class="wps_upsell_offer_product_content"> <h4 class="wps_bump_name" data-qty_allowed="' . esc_html( $wps_upsell_enable_quantity ) . '" data-wps_is_fixed_qty="' . esc_html( $wps_is_fixed_qty ) . '" data-wps_qty="' . esc_html( $wps_upsell_bump_products_fixed_quantity ) . '"><a target="' . esc_html( $wps_bump_target_attr ) . '" class="wps_upsell_product_permalink" href="' . esc_url( $bump_offer_product_permalink ) . '">' . esc_html( $bump['name'] ) . '</a></h4><br>';

		$bumphtml .= '<div class="product-price">';
		$bumphtml .= '<p class="wps_upsell_offer_product_price">' . $bump_offer_price . '</p></div>';

		$bumphtml .= '<div class="quantity">';

		if ( 'yes' === $wps_upsell_enable_quantity && 'variable_q' === $wps_upsell_offer_quantity_type && wps_ubo_lite_if_pro_exists() && ! $bump_product->is_type( 'variable' ) ) {
			$bumphtml .= '<label for="wps_quantity_offer">' . __( 'Quantity', 'upsell-order-bump-offer-for-woocommerce' ) . ':</label>';
			$bumphtml .= '<input class="wps_input_quantity wps_quantity_input" type="number" name="wps_quantity_offer" value="' . $wps_upsell_bump_products_min_quantity . '" min="' . $wps_upsell_bump_products_min_quantity . '" max="' . $wps_upsell_bump_products_max_quantity . '">';
		} else {
			$bumphtml .= '<label for="wps_quantity_offer">' . __( 'Quantity', 'upsell-order-bump-offer-for-woocommerce' ) . ':1</label>';
		}
		$bumphtml .= '</div>';

		$bumphtml .= '<div class="upsell-product-desc">';
		$bumphtml .= '<h5 class ="wps_product_info">Product Description </h5><p class="wps_upsell_offer_product_description">' . $product_description_text . '</p></div></div></div>';

		// Product section ends.
	} else {
		// product section start without permalink.
		$bumphtml .= '<div class="upsell-offer-body">';
		$bumphtml .= '<div class="upsell-product">';

		$bumphtml .= '<div class="upsell-product-img">';
		$bumphtml .= '<div class = "wps_upsell_offer_image" >';
		$bumphtml .= '<img class="wps_upsell_offer_img" src="' . esc_url( $image ) . '" data-id="' . esc_html( $bump['id'] ) . '">';
		$bumphtml .= '</div>';
		$bumphtml .= '</div>';

		$bumphtml .= '<div class="upsell-product-info">';
		$bumphtml .= '<div class = "wps_upsell_offer_product_section" >';
		$bumphtml .= '<div class="wps_upsell_offer_product_content"> <h4 class="wps_bump_name" data-qty_allowed="' . esc_html( $wps_upsell_enable_quantity ) . '" data-wps_is_fixed_qty="' . esc_html( $wps_is_fixed_qty ) . '" data-wps_qty="' . esc_html( $wps_upsell_bump_products_fixed_quantity ) . '">' . esc_html( $bump['name'] ) . '</h4><br>';

		$bumphtml .= '<div class="product-price">';
		$bumphtml .= '<p class="wps_upsell_offer_product_price">' . $bump_offer_price . '</p></div>';

		$bumphtml .= '<div class="quantity">';

		if ( 'yes' === $wps_upsell_enable_quantity && 'variable_q' === $wps_upsell_offer_quantity_type && wps_ubo_lite_if_pro_exists() && ! $bump_product->is_type( 'variable' ) ) {
			$bumphtml .= '<label for="wps_quantity_offer">' . __( 'Quantity', 'upsell-order-bump-offer-for-woocommerce' ) . ':</label>';
			$bumphtml .= '<input class="wps_input_quantity wps_quantity_input" type="number" name="wps_quantity_offer" value="' . $wps_upsell_bump_products_min_quantity . '" min="' . $wps_upsell_bump_products_min_quantity . '" max="' . $wps_upsell_bump_products_max_quantity . '">';
		} else {
			$bumphtml .= '<label for="wps_quantity_offer">' . __( 'Quantity', 'upsell-order-bump-offer-for-woocommerce' ) . ':1</label>';
		}
		$bumphtml .= '</div>';

		$bumphtml .= '<div class="upsell-product-desc">';
		$bumphtml .= '<h5 class ="wps_product_info">Product Description </h5><p class="wps_upsell_offer_product_description">' . $product_description_text . '</p></div></div></div>';
	}

	$bumphtml .= '</div>';
	$bumphtml .= '</div>';

	// Image Product Gallery.
	$wps_product_image_slider = isset( $bump['wps_ubo_offer_product_image_slider'] ) ? $bump['wps_ubo_offer_product_image_slider'] : '';
	if ( 'yes' === $wps_product_image_slider && wps_ubo_lite_if_pro_exists() && ( ( is_cart() ) || ( is_checkout() ) ) ) {
		$bumphtml  .= wps_product_image_gallery_callback( $bump['id'] );
	}

	// Secondary section start.
	// When don't show this when empty except for admin as it involves Live Preview.
	if ( ! empty( $description ) || is_admin() ) :
		$bumphtml .= '<div class="upsell-offer-desc">';
		$bumphtml .= '<div class = "wps_upsell_offer_secondary_section" ><p>' . $description . '</p></div>';
		$bumphtml .= '</div>';

		$bumphtml .= '</div>';// C.
	endif;
	// Secondary section end.

	// Creating  red arrow html.
	if ( 'on' === $wps_enable_red_arrow_feature ) {
		$wps_ubo_red_arrow_svg  = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 198 111.52" demo="0 0 198 111.52"><defs></defs><g id="a"/><g id="b"><g id="c"><polygon class="d" points="198 25.35 198 86.17 96.62 86.17 96.62 111.52 48.36 83.64 0 55.76 48.36 27.88 96.62 0 96.62 25.35 198 25.35"/></g></g></svg>';
		$wps_ubo_red_arrow_html = '<div class="wps_upsell_offer_arrow">' . $wps_ubo_red_arrow_svg . '</div>';
	} else {
		$wps_ubo_red_arrow_html = '';
	}

	// Primary section start.
	$bumphtml .= '<div class="upsell-order-footer">';
	$bumphtml .= '<div class = "wps_upsell_offer_primary_section" >';
	$bumphtml .= $wps_ubo_red_arrow_html;
	$bumphtml .= '<div class="upsell-order-check">';
	$bumphtml .= '<label class="wps_upsell_bump_checkbox_container">';
	$bumphtml .= '<input type="checkbox" ' . $check . ' name="add_offer_in_cart_checkbox" class ="add_offer_in_cart" id ="wps_checkbox_offer' . esc_html( $order_bump_key ) . '">';
	$bumphtml .= '<span class="checkmark"></span>';
	$bumphtml .= '</label>';
	$bumphtml .= '</div>';
	$bumphtml .= '<h5 class="add_offer_in_cart_text">' . $title . '</h5>';
	$bumphtml .= '</div>';
	$bumphtml .= '</div>';
	// Primary section end.

	// Wrapper div end.
	$bumphtml .= '</div>';

	$bumphtml .= '</div>';// D.

	// Parent wrapper end.
	$bumphtml .= '</div></div>';

	// For simple product.
	if ( wps_ubo_lite_if_pro_exists() && ! empty( $offer_product ) && is_object( $offer_product ) && ! $offer_product->has_child() && ! is_admin() ) {

		$bumphtml .= apply_filters( 'wps_meta_forms_allowed_submission', $order_bump_div_id, $bump['meta_forms_allowed'], $bump['meta_form_fields'] );
	}

	return $bumphtml;
}
/**
 * Bump Offer Html For Summer Cool.
 *
 * @param   string $bump        Consists all data about order bump.
 * @param   string $encountered_order_bump_id        Consists all data about order bump.
 * @param   string $order_bump_key        Consists all data about order bump.
 * @since   1.0.0
 */
function wps_ubo_lite_bump_offer_html_pro_6( $bump, $encountered_order_bump_id = '', $order_bump_key = '' ) {

	$discount_title_fixed = ! empty( $bump['design_text']['wps_ubo_discount_title_for_fixed'] ) ? $bump['design_text']['wps_ubo_discount_title_for_fixed'] : '';

	$discount_title_percent = ! empty( $bump['design_text']['wps_ubo_discount_title_for_percent'] ) ? $bump['design_text']['wps_ubo_discount_title_for_percent'] : '';

	$title = ! empty( $bump['design_text']['wps_upsell_offer_title'] ) ? $bump['design_text']['wps_upsell_offer_title'] : '';

	$product_description_text = $bump['design_text']['wps_bump_offer_decsription_text'];

	// Setting to enable disable permalink.
	$wps_ubo_global_options    = get_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );
	$wps_bump_enable_permalink = ! empty( $wps_ubo_global_options['wps_bump_enable_permalink'] ) ? $wps_ubo_global_options['wps_bump_enable_permalink'] : '';

	$description = $bump['design_text']['wps_upsell_bump_offer_description'];

	// DISCOUNT SECTION( discount_section ).
	$discount_section_background_color = ! empty( $bump['design_css']['discount_section_background_color'] ) ? $bump['design_css']['discount_section_background_color'] : '';
	$discount_section_text_color       = ! empty( $bump['design_css']['discount_section_text_color'] ) ? $bump['design_css']['discount_section_text_color'] : '';

	// PRODUCT SECTION( product_section ).
	$product_section_text_color = ! empty( $bump['design_css']['product_section_text_color'] ) ? $bump['design_css']['product_section_text_color'] : '';
	$product_section_price_text_color = ! empty( $bump['design_css']['product_section_price_text_color'] ) ? $bump['design_css']['product_section_price_text_color'] : '';

	// Accept Offer Section.
	$primary_section_background_color = ! empty( $bump['design_css']['primary_section_background_color'] ) ? $bump['design_css']['primary_section_background_color'] : '';
	$primary_section_text_color       = ! empty( $bump['design_css']['primary_section_text_color'] ) ? $bump['design_css']['primary_section_text_color'] : '';
	$primary_section_text_size        = ! empty( $bump['design_css']['primary_section_text_size'] ) ? $bump['design_css']['primary_section_text_size'] : '';

	// Offer Description Section.
	$secondary_section_background_color = ! empty( $bump['design_css']['secondary_section_background_color'] ) ? $bump['design_css']['secondary_section_background_color'] : '';
	$secondary_section_text_color       = ! empty( $bump['design_css']['secondary_section_text_color'] ) ? $bump['design_css']['secondary_section_text_color'] : '';
	$secondary_section_text_size        = ! empty( $bump['design_css']['secondary_section_text_size'] ) ? $bump['design_css']['secondary_section_text_size'] : '';

	// SET THE CLASS FOR THE TEMPLATE.
	$wps_class_template_pro = ! empty( $bump['design_css']['wps_class_template_pro'] ) ? esc_html( $bump['design_css']['wps_class_template_pro'] ) : '';

	$order_bump_div_id = '#wps_upsell_offer_main_id_' . $encountered_order_bump_id;

	$bump_offer_product_permalink = '';

	// Add url of the offer product in the bump info.
	if ( 'on' == $wps_bump_enable_permalink ) {
		$bump_offer_product_permalink = esc_url_raw( get_permalink( $bump['id'] ) );
	}
	?>
	<!--  CSS goes down here. --> 
	<style type="text/css">
		/* DISCOUNT SECTION( discount_section ). */
		<?php echo esc_html( $order_bump_div_id ); ?> .wps-ubo__temp-prod-offer {
		color : <?php echo esc_html( $discount_section_text_color ); ?>;
		background-color:<?php echo esc_html( $discount_section_background_color ); ?>;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps-ubo__temp-prod-offer {
		color : <?php echo esc_html( $discount_section_text_color ); ?>;
		background-color:<?php echo esc_html( $discount_section_background_color ); ?>;
		}
		/* PRODUCT SECTION( product_section ).*/
		<?php echo esc_html( $order_bump_div_id ); ?> .wps-ubo__temp-prod-desc {
		color : <?php echo esc_html( $product_section_text_color ); ?>;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps-ubo__temp-prod-price-new {
		color : <?php echo esc_html( $product_section_price_text_color ); ?>;
		}

		/* ACCEPT OFFER SECTION( product_section ).*/
		<?php echo esc_html( $order_bump_div_id ); ?> .wps-ubo__temp-title {
		background-color : <?php echo esc_html( $primary_section_background_color ); ?>;
		color : <?php echo esc_html( $primary_section_text_color ); ?>;
		font-size: <?php echo esc_html( $primary_section_text_size ); ?>;
		}

		/*OFFER DESCRIPTION SECTION( product_section ).*/
		<?php echo esc_html( $order_bump_div_id ); ?> .wps-ubo__temp-desc {
		background-color : <?php echo esc_html( $secondary_section_background_color ); ?>;
		color : <?php echo esc_html( $secondary_section_text_color ); ?>;
		font-size: <?php echo esc_html( $secondary_section_text_size ); ?>;
		}
		.wps_hide_checkbox {
			display: none;
		}
	</style>

	<?php
	// Template adaption.
	$wps_ubo_global_options    = get_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );

	$check = '';

	// Retain Checked if offer is added except for admin.
	if ( ! is_admin() && function_exists( 'WC' ) && ! empty( WC()->session ) ) {
		if ( null !== WC()->session->get( "bump_offer_status_index_$order_bump_key" ) ) {

			$check = 'checked';
		}
	}

	// Setting for the offer Quantity.
	$wps_upsell_enable_quantity              = ! empty( $bump['wps_upsell_enable_quantity'] ) ? $bump['wps_upsell_enable_quantity'] : '';
	$wps_upsell_bump_products_fixed_quantity = ! empty( $bump['wps_upsell_bump_products_fixed_quantity'] ) ? $bump['wps_upsell_bump_products_fixed_quantity'] : '';
	$wps_upsell_bump_products_min_quantity   = ! empty( $bump['wps_upsell_bump_products_min_quantity'] ) ? $bump['wps_upsell_bump_products_min_quantity'] : '';
	$wps_upsell_bump_products_max_quantity   = ! empty( $bump['wps_upsell_bump_products_max_quantity'] ) ? $bump['wps_upsell_bump_products_max_quantity'] : '';
	$wps_upsell_offer_quantity_type          = ! empty( $bump['wps_upsell_offer_quantity_type'] ) ? $bump['wps_upsell_offer_quantity_type'] : '';

	if ( ! empty( $bump['bump_price_html'] ) ) {

		$discount_title_fixed   = str_replace( '{dc_price}', $bump['bump_price_html'], $discount_title_fixed );
		$discount_title_percent = str_replace( '{dc_%}', $bump['bump_price_html'], $discount_title_percent );

	}

	$important = is_admin() ? '' : '!important';

	// Qty changed value.
	$wps_upsell_bump_products_min_quantity   = ! empty( $bump['wps_upsell_bump_products_min_quantity'] ) ? $bump['wps_upsell_bump_products_min_quantity'] : '';

	// Image Link purpose.
	$wps_bump_target_attr = ! empty( $wps_ubo_global_options['wps_bump_target_link_attr_val'] ) ? $wps_ubo_global_options['wps_bump_target_link_attr_val'] : '';

	// Incase no offer is added return.
	$bump['id']   = ! empty( $bump['id'] ) ? sanitize_text_field( $bump['id'] ) : '';
	$bump_product = wc_get_product( $bump['id'] );

	// If offer not found return.
	if ( empty( $bump['id'] ) || empty( $bump_product ) ) {

		return;
	}
	$bump['name'] = ! empty( $bump['name'] ) ? sanitize_text_field( $bump['name'] ) : '';

	$bump['discount_price'] = ! empty( $bump['discount_price'] ) ? sanitize_text_field( $bump['discount_price'] ) : '0';

	// After v2.0.1!
	if ( ! empty( $bump['offer_image'] ) ) {
		$image = wp_get_attachment_image_src( $bump['offer_image'], 'single-post-thumbnail' )[0];
	}

	// If still not found.
	if ( empty( $image ) ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $bump['id'] ), 'single-post-thumbnail' )[0];
	}
	if ( empty( $image ) ) {

		$bump_parent_id = $bump_product->get_parent_id();

		if ( ! empty( $bump_parent_id ) ) {

			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $bump_parent_id ), 'single-post-thumbnail' )[0];

		} else {
			$image = wc_placeholder_img_src();
		}
	}

	if ( ! empty( $bump['price_type'] ) && 'fixed' === $bump['price_type'] ) {

		$bump_price_html = $discount_title_fixed;
	} else {

		$bump_price_html = $discount_title_percent;
	}

	/*
	* Get price html.
	*/
	$bump_offer_price = wps_ubo_lite_custom_price_html( $bump['id'], $bump['discount_price'] );

	$product = wc_get_product( $bump['id'] );

	$bumphtml = '';

	$bumphtml .= '<div class="upsell-offer-timer-section">';
	$bumphtml .= '<div class="upsell-offer-time">';
	$bumphtml                .= '<div id = "wps_admin_timer"></div>';
	$wps_counter_timer_enable = isset( $bump['counter_timer'] ) ? $bump['counter_timer'] : '';
	$wps_evergreencounter_timer_enable = isset( $bump['evergreen_counter_timer'] ) ? $bump['evergreen_counter_timer'] : '';
	// Countdown Timer Section start.
	if ( ( 'yes' === $wps_counter_timer_enable || 'yes' == $wps_evergreencounter_timer_enable ) && wps_ubo_lite_if_pro_exists() ) {
		$bumphtml .= '<div class="expired_message_class" id = "expired_message' . esc_html( $order_bump_key ) . '"></div>';
		$bumphtml .= '<div class = "wps_timer_count wps_upsell_offer_discount_section" id ="wps_timer' . esc_html( $order_bump_key ) . '">
		<div class = "wps_day_timer_block wps-timer-wrap" >
		<div id ="wps_day_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_day_label">Days</div>
		</div>
		<div class ="wps_timer_sept">:</div>

		<div class = "wps_hour_timer_block wps-timer-wrap">
		<div id ="wps_hour_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_hour_label">Hour</div>
		</div>
		<div class ="wps_timer_sept">:</div>

		<div class = "wps_min_timer_block wps-timer-wrap">
		<div id ="wps_min_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_min_label">Min</div>
		</div>
		<div class ="wps_timer_sept">:</div>

		<div class = "wps_sec_timer_block wps-timer-wrap">
		<div id ="wps_sec_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_sec_label">Sec</div>
		</div>
		</div>';
	}
	// Countdown Timer Section End.

	$bumphtml .= '</div>';
	$bumphtml .= '</div>';

	$bumphtml .= '<div id="wps-ubo__temp-sec">';
	$bumphtml .= '<section id="wps_upsell_offer_main_id_' . $encountered_order_bump_id . '" class="wps-ubo__temp wps_upsell_offer_parent_wrapper ' . $wps_class_template_pro . ' wps_parent_wrapper_order_bump_' . $order_bump_key . '">'; // phpcs:ignore

	$bumphtml .= '<input type="hidden" class ="offer_shown_id" value="' . $bump['id'] . '">';
	$bumphtml .= '<input type="hidden" class ="offer_shown_discount" value="' . $bump['discount_price'] . '">';
	$bumphtml .= '<input type="hidden" class ="target_id_cart_key" value="' . $bump['target_key'] . '">';
	$bumphtml .= '<input type="hidden" class ="order_bump_index" value="index_' . $order_bump_key . '">';
	$bumphtml .= '<input type="hidden" class ="order_bump_id" value="' . $encountered_order_bump_id . '">';

	$offer_product = wc_get_product( $bump['id'] );

	if ( ! empty( $offer_product ) && is_object( $offer_product ) && $offer_product->has_child() ) {

		$bumphtml .= '<input type="hidden" class ="offer_shown_id_type" value="variable">';
	}

	$bumphtml .= '<input class="wps_hide_checkbox" type="checkbox" ' . $check . ' id ="wps_checkbox_tick_' . $order_bump_key . '" value="">';

	if ( ! empty( $bump['smart_offer_upgrade'] ) && 'yes' === $bump['smart_offer_upgrade'] ) {    // smart offer upgrade.

		$bumphtml .= '<input type="hidden" class="order_bump_smo" value=' . $bump['smart_offer_upgrade'] . '>';
	}

	$bumphtml .= '<article class="wps-ubo__temp-in">';
	$bumphtml .= '<div class="wps-ubo__temp-main">';
	$bumphtml .= '<div class="wps-ubo__temp-head">';// Template header start.
	$bumphtml .= '<h3 class="wps-ubo__temp-title">' . $title . '</h3>';

	$bumphtml .= '<p class="wps-ubo__temp-desc"> ' . $description . '</p>';

	$bumphtml .= '</div>';// Template header end.

	$bumphtml .= '<div class="wps-ubo__temp-prod-wrap">';
	$bumphtml .= '<div class="wps-ubo__temp-prod">';
	$bumphtml .= '<span class="wps-ubo__temp-prod-offer">' . $bump_price_html . '</span>';
	$bumphtml .= '<div class="wps-ubo__temp-prod-img">';
	$bumphtml .= '<a target="' . $wps_bump_target_attr . '" href="' . $bump_offer_product_permalink . '"><img src="' . esc_url( $image ) . '" alt="product-img"></a>';
	$bumphtml .= '</div>';

	$bumphtml .= '<div class="wps-ubo__temp-prod-content">';
	$bumphtml .= ' <h4 class="wps-ubo__temp-prod-title">' . esc_html( $bump['name'] ) . '</h4>';
	$average = $product->get_average_rating();
	if ( $average ) :    // HTML for product rating.
		$count   = $product->get_rating_count();
		$bumphtml .= '<div class="wps-ubo__temp-prod-rate">';
		$bumphtml .= '<span class="wps-ubo__temp-prod-rate-value">' . $average . ' <i class="dashicons dashicons-star-filled"></i></span>';
		$bumphtml .= '<span class="wps-ubo__temp-prod-rate-usr">(' . $count . ')</span>';
		$bumphtml .= '</div>';
	endif;

	$bumphtml .= '<p class="wps-ubo__temp-prod-desc">' . $product_description_text . '</p>'; // Product description.

	$bumphtml .= ' <div class="wps-ubo__temp-prod-price">';
	$bumphtml .= '<span class="wps-ubo__temp-prod-price-new">' . $bump_offer_price . '</span>';

	if ( 'yes' === $wps_upsell_enable_quantity && 'variable_q' === $wps_upsell_offer_quantity_type && wps_ubo_lite_if_pro_exists() && ! $bump_product->is_type( 'variable' ) ) {
		$bumphtml .= '<div class="wps-ubo__temp-prod-price-qty">';
		$bumphtml .= '<div class="wps-ubo__temp-prod-price-qty-change">';
		$bumphtml .= '<span class="wps-ubo__temp-prod-price-qty-sub wps-ubo__qty-btn">-</span>';
		$bumphtml .= '<input type="number" id="inputtag" class="wps-ubo_quantity_value" value="' . $wps_upsell_bump_products_min_quantity . '" placeholder="Qty" />';
		$bumphtml .= '<span class="wps-ubo__temp-prod-price-qty-add wps-ubo__qty-btn">+</span></div>';
		$bumphtml .= '</div>';
		$bumphtml .= '</div>';
	} else {
		$bumphtml .= '<label for="wps_quantity_offer">' . __( 'Quantity', 'upsell-order-bump-offer-for-woocommerce' ) . ':1</label>';
	}

	$bumphtml .= '</div>';
	$bumphtml .= '</div>';
	$bumphtml .= '</div>';
	$bumphtml .= '<div class="wps-ubo__temp-foot">';
	$bumphtml .= '<div class="wps-ubo__temp-btn-wrap wps-left" id="wps_button_id_' . esc_html( $order_bump_key ) . '">';
	$bumphtml .= '<input name="add_offer_in_cart_checkbox"  type="checkbox" id ="wps_checkbox_offer' . esc_html( $order_bump_key ) . '" class="wps-ubo__temp-prod-check add_offer_in_cart">';

	$bumphtml .= '<button type ="button" class="wps-ubo__temp-add-btn wps-ubo__btn wps-active wps-ubo_add_prod' . esc_html( $order_bump_key ) . '"><i class="dashicons dashicons-cart"></i> Add item to cart</button>';

	$bumphtml .= '<button type ="button" class="wps-ubo__temp-rmv-btn wps-ubo__btn wps-ubo_remove_prod' . esc_html( $order_bump_key ) . '"><i class="dashicons dashicons-trash"></i> Remove item from cart</button>';
	$bumphtml .= '<div class="wps-ubo__temp-btn-notice"><span class="wps-notice"></span></div>';
	$bumphtml .= '</div>';
	$bumphtml .= '</div>';
	$bumphtml .= '</div>';
	$bumphtml .= '</article>';
	$bumphtml .= '</section>';
	$bumphtml .= '</div>';

	// For simple product.
	if ( wps_ubo_lite_if_pro_exists() && ! empty( $offer_product ) && is_object( $offer_product ) && ! $offer_product->has_child() && ! is_admin() ) {

		$bumphtml .= apply_filters( 'wps_meta_forms_allowed_submission', $order_bump_div_id, $bump['meta_forms_allowed'], $bump['meta_form_fields'] );
	}

	return $bumphtml;

}

/**
 * Bump Offer Html For Summer Cool.
 *
 * @param   string $bump        Consists all data about order bump.
 * @param   string $encountered_order_bump_id        Consists all data about order bump.
 * @param   string $order_bump_key        Consists all data about order bump.
 * @since   1.0.0
 */
function wps_ubo_lite_bump_offer_html_10( $bump, $encountered_order_bump_id = '', $order_bump_key = '' ) {

	$discount_title_fixed = ! empty( $bump['design_text']['wps_ubo_discount_title_for_fixed'] ) ? $bump['design_text']['wps_ubo_discount_title_for_fixed'] : '';   // Discount Title. for fixes price.

	$discount_title_percent = ! empty( $bump['design_text']['wps_ubo_discount_title_for_percent'] ) ? $bump['design_text']['wps_ubo_discount_title_for_percent'] : ''; // Discount Title. for percentage price.

	$title = ! empty( $bump['design_text']['wps_upsell_offer_title'] ) ? $bump['design_text']['wps_upsell_offer_title'] : '';   // Lead Title.

	if ( ! empty( $bump['bump_price_html'] ) ) {

		$discount_title_fixed   = str_replace( '{dc_price}', $bump['bump_price_html'], $discount_title_fixed );
		$discount_title_percent = str_replace( '{dc_%}', $bump['bump_price_html'], $discount_title_percent );

	}

	if ( ! empty( $bump['price_type'] ) && 'fixed' === $bump['price_type'] ) {

		$bump_price_html = $discount_title_fixed;
	} else {

		$bump_price_html = $discount_title_percent;
	}

	// Setting to enable disable permalink.
	$wps_ubo_global_options    = get_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );
	$wps_bump_enable_permalink = ! empty( $wps_ubo_global_options['wps_bump_enable_permalink'] ) ? $wps_ubo_global_options['wps_bump_enable_permalink'] : '';

	$description = $bump['design_text']['wps_upsell_bump_offer_description'];   // Offer Description.
	// Red arrow setting.
	$wps_enable_red_arrow_feature = ! empty( $wps_ubo_global_options['wps_enable_red_arrow_feature'] ) ? $wps_ubo_global_options['wps_enable_red_arrow_feature'] : '';

	// Setting for the offer Quantity.
	$wps_upsell_enable_quantity              = ! empty( $bump['wps_upsell_enable_quantity'] ) ? $bump['wps_upsell_enable_quantity'] : '';
	$wps_upsell_bump_products_fixed_quantity = ! empty( $bump['wps_upsell_bump_products_fixed_quantity'] ) ? $bump['wps_upsell_bump_products_fixed_quantity'] : '';
	$wps_upsell_bump_products_min_quantity   = ! empty( $bump['wps_upsell_bump_products_min_quantity'] ) ? $bump['wps_upsell_bump_products_min_quantity'] : '';
	$wps_upsell_bump_products_max_quantity   = ! empty( $bump['wps_upsell_bump_products_max_quantity'] ) ? $bump['wps_upsell_bump_products_max_quantity'] : '';
	$wps_upsell_offer_quantity_type          = ! empty( $bump['wps_upsell_offer_quantity_type'] ) ? $bump['wps_upsell_offer_quantity_type'] : '';

	// PARENT WRAPPER DIV CSS( parent_wrapper_div ).
	$parent_border_type             = ! empty( $bump['design_css']['parent_border_type'] ) ? $bump['design_css']['parent_border_type'] : '';
	$parent_border_color            = ! empty( $bump['design_css']['parent_border_color'] ) ? $bump['design_css']['parent_border_color'] : '';
	$parent_background_color        = ! empty( $bump['design_css']['parent_background_color'] ) ? $bump['design_css']['parent_background_color'] : '';
	$parent_top_vertical_spacing    = ! empty( $bump['design_css']['top_vertical_spacing'] ) ? $bump['design_css']['top_vertical_spacing'] : '';
	$parent_bottom_vertical_spacing = ! empty( $bump['design_css']['bottom_vertical_spacing'] ) ? $bump['design_css']['bottom_vertical_spacing'] : '0';

	// DISCOUNT SECTION( discount_section ).
	$discount_section_background_color = ! empty( $bump['design_css']['discount_section_background_color'] ) ? $bump['design_css']['discount_section_background_color'] : '';
	$discount_section_text_color       = ! empty( $bump['design_css']['discount_section_text_color'] ) ? $bump['design_css']['discount_section_text_color'] : '';
	$discount_section_text_size        = ! empty( $bump['design_css']['discount_section_text_size'] ) ? $bump['design_css']['discount_section_text_size'] : '';

	// PRODUCT SECTION( product_section ).
	$product_section_text_color = ! empty( $bump['design_css']['product_section_text_color'] ) ? $bump['design_css']['product_section_text_color'] : '';
	$product_section_text_size  = ! empty( $bump['design_css']['product_section_text_size'] ) ? $bump['design_css']['product_section_text_size'] : '';
	$product_section_text_price_size  = ! empty( $bump['design_css']['product_section_price_text_size'] ) ? $bump['design_css']['product_section_price_text_size'] : '';
	$product_section_price_text_color = ! empty( $bump['design_css']['product_section_price_text_color'] ) ? $bump['design_css']['product_section_price_text_color'] : '';

	$product_section_img_width  = ! empty( $bump['design_css']['product_section_img_width'] ) ? $bump['design_css']['product_section_img_width'] : '';
	$product_section_img_height = ! empty( $bump['design_css']['product_section_img_height'] ) ? $bump['design_css']['product_section_img_height'] : '';

	// PRIMARY SECTION(primary_section ).
	$primary_section_background_color = ! empty( $bump['design_css']['primary_section_background_color'] ) ? $bump['design_css']['primary_section_background_color'] : '';
	$primary_section_text_color       = ! empty( $bump['design_css']['primary_section_text_color'] ) ? $bump['design_css']['primary_section_text_color'] : '';
	$primary_section_arrow_color      = ! empty( $bump['design_css']['primary_section_arrow_color'] ) ? $bump['design_css']['primary_section_arrow_color'] : '';
	$primary_section_text_size        = ! empty( $bump['design_css']['primary_section_text_size'] ) ? $bump['design_css']['primary_section_text_size'] : '';

	// SECONDARY SECTION( secondary_section ).
	$secondary_section_background_color = ! empty( $bump['design_css']['secondary_section_background_color'] ) ? $bump['design_css']['secondary_section_background_color'] : '';
	$secondary_section_text_color       = ! empty( $bump['design_css']['secondary_section_text_color'] ) ? $bump['design_css']['secondary_section_text_color'] : '';
	$secondary_section_text_size        = ! empty( $bump['design_css']['secondary_section_text_size'] ) ? $bump['design_css']['secondary_section_text_size'] : '';

	$wps_bump_target_attr = ! empty( $wps_ubo_global_options['wps_bump_target_link_attr_val'] ) ? $wps_ubo_global_options['wps_bump_target_link_attr_val'] : '';

	$order_bump_div_id = '#wps_upsell_offer_main_id_' . $encountered_order_bump_id;

	$bump_offer_product_permalink = '';

	// Template adaption.
	$wps_ubo_global_options    = get_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );
	$wps_ubo_template_adaption = ! empty( $wps_ubo_global_options['wps_ubo_temp_adaption'] ) ? $wps_ubo_global_options['wps_ubo_temp_adaption'] : '';

	// Add url of the offer product in the bump info.
	if ( 'on' == $wps_bump_enable_permalink ) {
		$bump_offer_product_permalink = esc_url_raw( get_permalink( $bump['id'] ) );
	}
	?>
	<?php $parent_border_width = 'double' === $parent_border_type ? '4px' : '2px'; ?>
	<!--  HTML goes down here. --> 
	<style type="text/css">
		/**
		* All of the CSS for your public-facing functionality should be
		* included in this file.
		*/
		<?php echo esc_html( $order_bump_div_id ); ?> {
			display: block;
			width: 100%;
			padding-top:  <?php echo esc_html( $parent_top_vertical_spacing ) . esc_html( 'px' ); ?>;
			padding-bottom:  <?php echo esc_html( $parent_bottom_vertical_spacing ) . esc_html( 'px' ); ?>;
			clear: both;
		}

		<?php echo esc_html( $order_bump_div_id ); ?> .wps-ob-st {
			border: <?php echo esc_html( $parent_border_type . ' ' . $parent_border_color . ' ' . $parent_border_width ); ?>;
			margin: 0 auto;
			<?php if ( 'no' === $wps_ubo_template_adaption ) : ?>
			max-width: 400px;
			<?php endif; ?>
		}

		.wps-ob-st {
			font-family: 'Source Sans Pro', sans-serif;
		}

		<?php echo esc_html( $order_bump_div_id ); ?> .wps-ob-st {
			background-color:<?php echo esc_html( $parent_background_color ); ?>;
			padding : 20px;
		}

		<?php echo esc_html( $order_bump_div_id ); ?> .wps-ob-st__m-title {
			padding: 10px;
			background: #f5f5f5;
			border-radius: 5px;
			margin: 0 0 15px;
			background-color: <?php echo esc_html( $discount_section_background_color ); ?>;
			font-size: <?php echo esc_html( $discount_section_text_size ) . esc_html( 'px' ); ?>;
			color: <?php echo esc_html( $discount_section_text_color ); ?>;
		}

		<?php echo esc_html( $order_bump_div_id ); ?> .wps-ob-st__m-c-p { 
			font-size: 14px;
			line-height: 1.5;
			margin: 0 0 10px;
			color: <?php echo esc_html( $product_section_text_color ); ?>;
			font-size: <?php echo esc_html( $product_section_text_size ) . esc_html( 'px' ); ?>;
		}	
	
		<?php echo esc_html( $order_bump_div_id ); ?> .wps-ob-st__m-c-price { 
			color: <?php echo esc_html( $product_section_price_text_color ); ?>;
			font-size: <?php echo esc_html( $product_section_text_price_size ) . esc_html( 'px' ); ?>;
			font-weight: 700;
			line-height: 1.25;
		}

		<?php echo esc_html( $order_bump_div_id ); ?> .wps-ob-st__head { 
			font-size: <?php echo esc_html( $primary_section_text_size ) . esc_html( 'px' ); ?>;
			background-color: <?php echo esc_html( $primary_section_background_color ); ?>;
			border-radius: 5px;
			margin: 0 0 15px;
			padding: 10px;
			color: <?php echo esc_html( $primary_section_text_color ); ?>
		}


		@keyframes leftright {
			0% {
				transform: translateX(-5px)scaleX(-1);
			}
			60% {
				transform: translateX(-2px)scaleX(-1);
			}
			100% {
				transform: translateX(-5px)scaleX(-1);
			}
		}

		<?php echo esc_html( $order_bump_div_id ); ?> span.wps_upsell_offer_arrow {
			display: inline-block;
			transform: rotate(180deg);
			 animation: leftright 0.4s infinite ease;
			width: 20px;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_arrow svg {
			/* fill: #eb483f; */
			fill: <?php echo esc_html( $primary_section_arrow_color ); ?>
		}

		.wps_upsell_product_permalink {
			text-decoration: none !important;
		}

 .wps-ob-st {
		padding: 15px;
		border: 1px solid #dcdcdc;
		margin: 20px 0;
	}

	.wps-ob-st__m-in {
		display: flex;
		align-items: flex-start;
		gap: 20px;
	}

	#wps-ob-st .wps-ob-st__m-in img {
		max-width: 140px;
		width: 100%;
		aspect-ratio: 1;
		object-fit: cover;
		border-radius: 5px;
	}

	.wps-ob-st__m-title {
		font-size: 24px;
		font-weight: 700;
		margin: 0 0 15px;
		line-height: 1.25;
	}

	.wps-ob-st__m-c-h2{
		font-size: 18px;
		font-weight: 700;
		line-height: 1.25;
		margin: 0 0 10px;
		text-decoration: none !important;
	}

	.wps-ob-st__m-c-h2.wps_upsell_product_permalink{
		text-decoration: none !important;
	}

	.wps-ob-st__m-c-p {
		font-size: 14px;
		line-height: 1.5;
		margin: 0 0 10px;
	}

	.wps-ob-st__head {
		padding: 10px;
		background: #f5f5f5;
		border-radius: 5px;
		margin: 0 0 15px;
	}

	.wps-ob-st__m-c-price del {
		font-weight: 400;
		color: #dcdcdc;
	}

	.wps-ob-st__head label {
		display: block;
		line-height: 1.25;
		cursor: pointer;
		margin: 0;
	}

	.wps-ob-st__head input[type=checkbox] {
		margin: 0 5px 0 0;
	}

	#wps-ob-st.ob_cont-full .wps-ob-st__m-in img {
		max-width: 380px;
	}

	.ob_cont-full .wps-ob-st__m-in {
		flex-direction: column;
	}

	@media screen and (max-width: 520px) {
		#wps-ob-st .wps-ob-st__m-in img {
			max-width: 80px;
		}
	}

	@media screen and (max-width: 380px) {
		#wps-ob-st .wps-ob-st__m-in img {
			max-width: 380px;
		}

		.wps-ob-st__m-in {
			flex-direction: column;
		}
	}

	</style>

	<?php

	// Template adaption.
	$wps_ubo_global_options    = get_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );

	$check = '';

	// Retain Checked if offer is added except for admin.
	if ( ! is_admin() && function_exists( 'WC' ) && ! empty( WC()->session ) ) {
		if ( null !== WC()->session->get( "bump_offer_status_index_$order_bump_key" ) ) {

			$check = 'checked';
		}
	}

	if ( ! empty( $bump['bump_price_html'] ) ) {

		$discount_title_fixed   = str_replace( '{dc_price}', $bump['bump_price_html'], $discount_title_fixed );
		$discount_title_percent = str_replace( '{dc_%}', $bump['bump_price_html'], $discount_title_percent );

	}

	$important = is_admin() ? '' : '!important';

	// Qty changed value.
	$wps_upsell_bump_products_min_quantity   = ! empty( $bump['wps_upsell_bump_products_min_quantity'] ) ? $bump['wps_upsell_bump_products_min_quantity'] : '';

	// Image Link purpose.
	$wps_bump_target_attr = ! empty( $wps_ubo_global_options['wps_bump_target_link_attr_val'] ) ? $wps_ubo_global_options['wps_bump_target_link_attr_val'] : '';

	// Incase no offer is added return.
	$bump['id']   = ! empty( $bump['id'] ) ? sanitize_text_field( $bump['id'] ) : '';
	$bump_product = wc_get_product( $bump['id'] );

	// If offer not found return.
	if ( empty( $bump['id'] ) || empty( $bump_product ) ) {

		return;
	}
	$bump['name'] = ! empty( $bump['name'] ) ? sanitize_text_field( $bump['name'] ) : '';

	$bump['discount_price'] = ! empty( $bump['discount_price'] ) ? sanitize_text_field( $bump['discount_price'] ) : '0';

	// After v2.0.1!
	if ( ! empty( $bump['offer_image'] ) ) {
		$image = wp_get_attachment_image_src( $bump['offer_image'], 'single-post-thumbnail' )[0];
	}

	// If still not found.
	if ( empty( $image ) ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $bump['id'] ), 'single-post-thumbnail' )[0];
	}
	if ( empty( $image ) ) {

		$bump_parent_id = $bump_product->get_parent_id();

		if ( ! empty( $bump_parent_id ) ) {

			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $bump_parent_id ), 'single-post-thumbnail' )[0];

		} else {
			$image = wc_placeholder_img_src();
		}
	}

	if ( ! empty( $bump['price_type'] ) && 'fixed' === $bump['price_type'] ) {

		$bump_price_html = $discount_title_fixed;
	} else {

		$bump_price_html = $discount_title_percent;
	}

	/*
	* Get price html.
	*/
	$bump_offer_price = wps_ubo_lite_custom_price_html( $bump['id'], $bump['discount_price'] );

	$product = wc_get_product( $bump['id'] );
	$bumphtml  = '';

	$bumphtml .= '<div id="wps_upsell_offer_main_id_' . $encountered_order_bump_id . '" class = "wps_upsell_offer_main_wrapper wps_ubo_wrapper_index_' . $order_bump_key . '" >';

	$bumphtml .= '<input type="hidden" class ="offer_shown_id" value="' . $bump['id'] . '">';
	$bumphtml .= '<input type="hidden" class ="offer_shown_discount" value="' . $bump['discount_price'] . '">';
	$bumphtml .= '<input type="hidden" class ="target_id_cart_key" value="' . $bump['target_key'] . '">';
	$bumphtml .= '<input type="hidden" class ="order_bump_index" value="index_' . $order_bump_key . '">';
	$bumphtml .= '<input type="hidden" class ="order_bump_id" value="' . $encountered_order_bump_id . '">';

	$offer_product = wc_get_product( $bump['id'] );

	if ( ! empty( $offer_product ) && is_object( $offer_product ) && $offer_product->has_child() ) {

		$bumphtml .= '<input type="hidden" class ="offer_shown_id_type" value="variable">';
	}

	if ( ! empty( $bump['smart_offer_upgrade'] ) && 'yes' === $bump['smart_offer_upgrade'] ) {

		$bumphtml .= '<input type="hidden" class="order_bump_smo" value=' . $bump['smart_offer_upgrade'] . '>';
	}

	if ( is_admin() && ! empty( $bump['bump_price_at_zero'] ) ) :
		$bumphtml .= '<input type="hidden" class="bump_price_at_zero" value=' . $bump['bump_price_at_zero'] . '>';
	endif;

	$bumphtml                .= '<div class = "wps_upsell_offer_parent_wrapper" >';
	$bumphtml                .= '<div id = "wps_admin_timer"></div>';
	$wps_counter_timer_enable = isset( $bump['counter_timer'] ) ? $bump['counter_timer'] : '';
	$wps_evergreencounter_timer_enable = isset( $bump['evergreen_counter_timer'] ) ? $bump['evergreen_counter_timer'] : '';
	// Countdown Timer Section start.
	if ( ( 'yes' === $wps_counter_timer_enable || 'yes' == $wps_evergreencounter_timer_enable ) && wps_ubo_lite_if_pro_exists() ) {
		$bumphtml .= '<div class="expired_message_class" id = "expired_message' . esc_html( $order_bump_key ) . '"></div>';
		$bumphtml .= '<div class = "wps_timer_count wps_upsell_offer_discount_section" id ="wps_timer' . esc_html( $order_bump_key ) . '">
		<div class = "wps_day_timer_block wps-timer-wrap" >
		<div id ="wps_day_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_day_label">Days</div>
		</div>
		<div class ="wps_timer_sept">:</div>

		<div class = "wps_hour_timer_block wps-timer-wrap">
		<div id ="wps_hour_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_hour_label">Hour</div>
		</div>
		<div class ="wps_timer_sept">:</div>

		<div class = "wps_min_timer_block wps-timer-wrap">
		<div id ="wps_min_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_min_label">Min</div>
		</div>
		<div class ="wps_timer_sept">:</div>

		<div class = "wps_sec_timer_block wps-timer-wrap">
		<div id ="wps_sec_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_sec_label">Sec</div>
		</div>
		</div>';
	}
	// Countdown Timer Section End.

	if ( 'fixed_q' === $wps_upsell_offer_quantity_type ) {
		$wps_is_fixed_qty = 'true';
	} else {
		$wps_is_fixed_qty = 'false';
	}

	// Creating  red arrow html.
	if ( 'on' === $wps_enable_red_arrow_feature ) {
		$wps_ubo_red_arrow_svg  = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 198 111.52" demo="0 0 198 111.52"><defs></defs><g id="a"/><g id="b"><g id="c"><polygon class="d" points="198 25.35 198 86.17 96.62 86.17 96.62 111.52 48.36 83.64 0 55.76 48.36 27.88 96.62 0 96.62 25.35 198 25.35"/></g></g></svg>';
		$wps_ubo_red_arrow_html = '<span class="wps_upsell_offer_arrow">' . $wps_ubo_red_arrow_svg . '</span>';
	} else {
		$wps_ubo_red_arrow_html = '';
	}

	$bumphtml .= '<div id="wps-ob-st" class="wps-ob-st">';
	$bumphtml .= '<div class="wps-ob-st__head">';
	$bumphtml .= '<label for="wps-ob-st__head-check" class = "wps_head_check_ubo">';
	$bumphtml .= $wps_ubo_red_arrow_html;
	$bumphtml .= '<input type="checkbox" ' . $check . ' class="add_offer_in_cart" id="wps_checkbox_offer' . esc_html( $order_bump_key ) . '" />';
	$bumphtml .= '<span>' . $title . '</span>';

	$bumphtml .= '</label>';

	$bumphtml .= '</div>';

	$bumphtml .= '<div class="wps-ob-st__main">';
	$bumphtml .= ' <div class="wps-ob-st__m-title">' . $bump_price_html . '</div>';
	$bumphtml .= '<div class="wps-ob-st__m-in">';

	if ( 'on' === $wps_bump_enable_permalink ) {      // Permalinks Fro The Product Image.
		$bumphtml .= '<a target="' . $wps_bump_target_attr . '" href="' . $bump_offer_product_permalink . '"><img src="' . esc_url( $image ) . '" alt="test"></a>';
	} else {
		$bumphtml .= '<img src="' . esc_url( $image ) . '" alt="test">';
	}

	// Image Product Gallery.
	$wps_product_image_slider = isset( $bump['wps_ubo_offer_product_image_slider'] ) ? $bump['wps_ubo_offer_product_image_slider'] : '';
	if ( 'yes' === $wps_product_image_slider && wps_ubo_lite_if_pro_exists() && ( ( is_cart() ) || ( is_checkout() ) ) ) {
		$bumphtml  .= wps_product_image_gallery_callback( $bump['id'] );
	}

	$bumphtml .= '<div class="wps-ob-st__m-c">';
	$bumphtml .= ' <div class="wps-ob-st__m-c-h2">';  // Permalinks For Product Name.
	if ( 'on' === $wps_bump_enable_permalink ) {
		$bumphtml .= '<h4 class="wps_bump_name" data-qty_allowed="' . esc_html( $wps_upsell_enable_quantity ) . '" data-wps_is_fixed_qty="' . esc_html( $wps_is_fixed_qty ) . '" data-wps_qty="' . esc_html( $wps_upsell_bump_products_fixed_quantity ) . '"><a target="' . esc_html( $wps_bump_target_attr ) . '" class="wps_upsell_product_permalink" href="' . esc_url( $bump_offer_product_permalink ) . '">' . $bump['name'] . '</a></h4>';
	} else {
		$bumphtml .= '<h4 class="wps_bump_name" data-qty_allowed="' . esc_html( $wps_upsell_enable_quantity ) . '" data-wps_is_fixed_qty="' . esc_html( $wps_is_fixed_qty ) . '" data-wps_qty="' . esc_html( $wps_upsell_bump_products_fixed_quantity ) . '">' . $bump['name'] . '</h4>';
	}

	$bumphtml .= '</div>';

	$bumphtml .= ' <div class="wps-ob-st__m-c-p">' . $description . '</div>';
	$bumphtml .= ' <div class="wps-ob-st__m-c-price">';
	$bumphtml .= $bump_offer_price;
	$bumphtml .= '</div>';

	$bumphtml .= ' <div class="wps-ob-st__m-c-price">';
	if ( 'yes' === $wps_upsell_enable_quantity && 'variable_q' === $wps_upsell_offer_quantity_type && wps_ubo_lite_if_pro_exists() && ! $bump_product->is_type( 'variable' ) ) {
		$bumphtml .= '<label for="wps_quantity_offer">' . __( 'Quantity', 'upsell-order-bump-offer-for-woocommerce' ) . ':</label>';
		$bumphtml .= '<input class="wps_input_quantity wps_quantity_input" type="number" name="wps_quantity_offer" value="' . $wps_upsell_bump_products_min_quantity . '" min="' . $wps_upsell_bump_products_min_quantity . '" max="' . $wps_upsell_bump_products_max_quantity . '">';
	}

	$bumphtml .= '</div>';

	$bumphtml .= '</div>';
	$bumphtml .= '</div>';
	$bumphtml .= '</div>';

	$bumphtml .= '</div>';

	$bumphtml .= '</div>';
	$bumphtml .= '</div>';

	// For simple product.
	if ( wps_ubo_lite_if_pro_exists() && ! empty( $offer_product ) && is_object( $offer_product ) && ! $offer_product->has_child() && ! is_admin() ) {

		$bumphtml .= apply_filters( 'wps_meta_forms_allowed_submission', $order_bump_div_id, $bump['meta_forms_allowed'], $bump['meta_form_fields'] );
	}

	return $bumphtml;

}


/**
 * Bump Offer Html For Summer Cool.
 *
 * @param   string $bump        Consists all data about order bump.
 * @param   string $encountered_order_bump_id        Consists all data about order bump.
 * @param   string $order_bump_key        Consists all data about order bump.
 * @since   1.0.0
 */
function wps_ubo_lite_bump_offer_html_11( $bump, $encountered_order_bump_id = '', $order_bump_key = '' ) {

	$discount_title_fixed = ! empty( $bump['design_text']['wps_ubo_discount_title_for_fixed'] ) ? $bump['design_text']['wps_ubo_discount_title_for_fixed'] : '';   // Discount Title. for fixes price.

	$discount_title_percent = ! empty( $bump['design_text']['wps_ubo_discount_title_for_percent'] ) ? $bump['design_text']['wps_ubo_discount_title_for_percent'] : ''; // Discount Title. for percentage price.

	$title = ! empty( $bump['design_text']['wps_upsell_offer_title'] ) ? $bump['design_text']['wps_upsell_offer_title'] : '';   // Lead Title.

	// Template adaption.
	$wps_ubo_global_options    = get_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );
	// Red arrow setting.
	$wps_enable_red_arrow_feature = ! empty( $wps_ubo_global_options['wps_enable_red_arrow_feature'] ) ? $wps_ubo_global_options['wps_enable_red_arrow_feature'] : '';
	$wps_upsell_offer_quantity_type          = ! empty( $bump['wps_upsell_offer_quantity_type'] ) ? $bump['wps_upsell_offer_quantity_type'] : '';

	// Setting for the offer Quantity.
	$wps_upsell_enable_quantity              = ! empty( $bump['wps_upsell_enable_quantity'] ) ? $bump['wps_upsell_enable_quantity'] : '';
	$wps_upsell_bump_products_fixed_quantity = ! empty( $bump['wps_upsell_bump_products_fixed_quantity'] ) ? $bump['wps_upsell_bump_products_fixed_quantity'] : '';
	$wps_upsell_bump_products_min_quantity   = ! empty( $bump['wps_upsell_bump_products_min_quantity'] ) ? $bump['wps_upsell_bump_products_min_quantity'] : '';
	$wps_upsell_bump_products_max_quantity   = ! empty( $bump['wps_upsell_bump_products_max_quantity'] ) ? $bump['wps_upsell_bump_products_max_quantity'] : '';
	$wps_upsell_offer_quantity_type          = ! empty( $bump['wps_upsell_offer_quantity_type'] ) ? $bump['wps_upsell_offer_quantity_type'] : '';

	// PARENT WRAPPER DIV CSS( parent_wrapper_div ).
	$parent_border_type             = ! empty( $bump['design_css']['parent_border_type'] ) ? $bump['design_css']['parent_border_type'] : '';
	$parent_border_color            = ! empty( $bump['design_css']['parent_border_color'] ) ? $bump['design_css']['parent_border_color'] : '';
	$parent_background_color        = ! empty( $bump['design_css']['parent_background_color'] ) ? $bump['design_css']['parent_background_color'] : '';
	$parent_top_vertical_spacing    = ! empty( $bump['design_css']['top_vertical_spacing'] ) ? $bump['design_css']['top_vertical_spacing'] : '';
	$parent_bottom_vertical_spacing = ! empty( $bump['design_css']['bottom_vertical_spacing'] ) ? $bump['design_css']['bottom_vertical_spacing'] : '0';

	// DISCOUNT SECTION( discount_section ).
	$discount_section_background_color = ! empty( $bump['design_css']['discount_section_background_color'] ) ? $bump['design_css']['discount_section_background_color'] : '';
	$discount_section_text_color       = ! empty( $bump['design_css']['discount_section_text_color'] ) ? $bump['design_css']['discount_section_text_color'] : '';
	$discount_section_text_size        = ! empty( $bump['design_css']['discount_section_text_size'] ) ? $bump['design_css']['discount_section_text_size'] : '';

	// PRODUCT SECTION( product_section ).
	$product_section_text_color = ! empty( $bump['design_css']['product_section_text_color'] ) ? $bump['design_css']['product_section_text_color'] : '';
	$product_section_text_size  = ! empty( $bump['design_css']['product_section_text_size'] ) ? $bump['design_css']['product_section_text_size'] : '';
	$product_section_text_price_size  = ! empty( $bump['design_css']['product_section_price_text_size'] ) ? $bump['design_css']['product_section_price_text_size'] : '';
	$product_section_price_text_color = ! empty( $bump['design_css']['product_section_price_text_color'] ) ? $bump['design_css']['product_section_price_text_color'] : '';

	$product_section_img_width  = ! empty( $bump['design_css']['product_section_img_width'] ) ? $bump['design_css']['product_section_img_width'] : '';
	$product_section_img_height = ! empty( $bump['design_css']['product_section_img_height'] ) ? $bump['design_css']['product_section_img_height'] : '';

	// PRIMARY SECTION(primary_section ).
	$primary_section_background_color = ! empty( $bump['design_css']['primary_section_background_color'] ) ? $bump['design_css']['primary_section_background_color'] : '';
	$primary_section_text_color       = ! empty( $bump['design_css']['primary_section_text_color'] ) ? $bump['design_css']['primary_section_text_color'] : '';
	$primary_section_arrow_color      = ! empty( $bump['design_css']['primary_section_arrow_color'] ) ? $bump['design_css']['primary_section_arrow_color'] : '';
	$primary_section_text_size        = ! empty( $bump['design_css']['primary_section_text_size'] ) ? $bump['design_css']['primary_section_text_size'] : '';

	$order_bump_div_id = '#wps_upsell_offer_main_id_' . $encountered_order_bump_id;

	if ( ! empty( $bump['bump_price_html'] ) ) {

		$discount_title_fixed   = str_replace( '{dc_price}', $bump['bump_price_html'], $discount_title_fixed );
		$discount_title_percent = str_replace( '{dc_%}', $bump['bump_price_html'], $discount_title_percent );

	}

	// Incase no offer is added return.
	$bump['id']   = ! empty( $bump['id'] ) ? sanitize_text_field( $bump['id'] ) : '';
	$bump_product = wc_get_product( $bump['id'] );

	// If offer not found return.
	if ( empty( $bump['id'] ) || empty( $bump_product ) ) {

		return;
	}

	if ( ! empty( $bump['price_type'] ) && 'fixed' === $bump['price_type'] ) {

		$bump_price_html = $discount_title_fixed;
	} else {

		$bump_price_html = $discount_title_percent;
	}

	$parent_border_width = 'double' === $parent_border_type ? '4px' : '2px';

	// After v2.0.1!
	if ( ! empty( $bump['offer_image'] ) ) {
		$image = wp_get_attachment_image_src( $bump['offer_image'], 'single-post-thumbnail' )[0];
	}

	// If still not found.
	if ( empty( $image ) ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $bump['id'] ), 'single-post-thumbnail' )[0];
	}
	if ( empty( $image ) ) {

		$bump_parent_id = $bump_product->get_parent_id();

		if ( ! empty( $bump_parent_id ) ) {

			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $bump_parent_id ), 'single-post-thumbnail' )[0];

		} else {
			$image = wc_placeholder_img_src();
		}
	}

	$check = '';

	// Retain Checked if offer is added except for admin.
	if ( ! is_admin() && function_exists( 'WC' ) && ! empty( WC()->session ) ) {
		if ( null !== WC()->session->get( "bump_offer_status_index_$order_bump_key" ) ) {

			$check = 'checked';
		}
	}

	if ( ! empty( $bump['bump_price_html'] ) ) {

		$discount_title_fixed   = str_replace( '{dc_price}', $bump['bump_price_html'], $discount_title_fixed );
		$discount_title_percent = str_replace( '{dc_%}', $bump['bump_price_html'], $discount_title_percent );

	}

	if ( ! empty( $bump['price_type'] ) && 'fixed' === $bump['price_type'] ) {

		$bump_price_html = $discount_title_fixed;
	} else {

		$bump_price_html = $discount_title_percent;
	}

	$wps_ubo_global_options    = get_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );
	$wps_ubo_template_adaption = ! empty( $wps_ubo_global_options['wps_ubo_temp_adaption'] ) ? $wps_ubo_global_options['wps_ubo_temp_adaption'] : '';

		/*
	* Get price html.
	*/
	$bump_offer_price = wps_ubo_lite_custom_price_html( $bump['id'], $bump['discount_price'] );

	$description = $bump['design_text']['wps_upsell_bump_offer_description'];   // Offer Description.
	$product_description_text = $bump['design_text']['wps_bump_offer_decsription_text'];

	$bumphtml = '';

	$bumphtml .= '<div id="wps_upsell_offer_main_id_' . $encountered_order_bump_id . '" class = "wps_upsell_offer_main_wrapper wps_ubo_wrapper_index_' . $order_bump_key . '" >';

	$bumphtml .= '<input type="hidden" class ="offer_shown_id" value="' . $bump['id'] . '">';
	$bumphtml .= '<input type="hidden" class ="offer_shown_discount" value="' . $bump['discount_price'] . '">';
	$bumphtml .= '<input type="hidden" class ="target_id_cart_key" value="' . $bump['target_key'] . '">';
	$bumphtml .= '<input type="hidden" class ="order_bump_index" value="index_' . $order_bump_key . '">';
	$bumphtml .= '<input type="hidden" class ="order_bump_id" value="' . $encountered_order_bump_id . '">';

	$offer_product = wc_get_product( $bump['id'] );

	if ( ! empty( $offer_product ) && is_object( $offer_product ) && $offer_product->has_child() ) {

		$bumphtml .= '<input type="hidden" class ="offer_shown_id_type" value="variable">';
	}

	if ( ! empty( $bump['smart_offer_upgrade'] ) && 'yes' === $bump['smart_offer_upgrade'] ) {

		$bumphtml .= '<input type="hidden" class="order_bump_smo" value=' . $bump['smart_offer_upgrade'] . '>';
	}

	if ( is_admin() && ! empty( $bump['bump_price_at_zero'] ) ) :
		$bumphtml .= '<input type="hidden" class="bump_price_at_zero" value=' . $bump['bump_price_at_zero'] . '>';
	endif;

	$bumphtml                .= '<div class = "wps_upsell_offer_parent_wrapper" >';
	$bumphtml                .= '<div id = "wps_admin_timer"></div>';
	$wps_counter_timer_enable = isset( $bump['counter_timer'] ) ? $bump['counter_timer'] : '';
	$wps_evergreencounter_timer_enable = isset( $bump['evergreen_counter_timer'] ) ? $bump['evergreen_counter_timer'] : '';
	// Countdown Timer Section start.
	if ( ( 'yes' === $wps_counter_timer_enable || 'yes' == $wps_evergreencounter_timer_enable ) && wps_ubo_lite_if_pro_exists() ) {
		$bumphtml .= '<div class="expired_message_class" id = "expired_message' . esc_html( $order_bump_key ) . '"></div>';
		$bumphtml .= '<div class = "wps_timer_count wps_upsell_offer_discount_section" id ="wps_timer' . esc_html( $order_bump_key ) . '">
		<div class = "wps_day_timer_block wps-timer-wrap" >
		<div id ="wps_day_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_day_label">Days</div>
		</div>
		<div class ="wps_timer_sept">:</div>

		<div class = "wps_hour_timer_block wps-timer-wrap">
		<div id ="wps_hour_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_hour_label">Hour</div>
		</div>
		<div class ="wps_timer_sept">:</div>

		<div class = "wps_min_timer_block wps-timer-wrap">
		<div id ="wps_min_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_min_label">Min</div>
		</div>
		<div class ="wps_timer_sept">:</div>

		<div class = "wps_sec_timer_block wps-timer-wrap">
		<div id ="wps_sec_time_' . esc_html( $order_bump_key ) . '">0</div>
		<div id = "wps_sec_label">Sec</div>
		</div>
		</div>';
	}

		// Countdown Timer Section End.
	if ( 'fixed_q' === $wps_upsell_offer_quantity_type ) {
		$wps_is_fixed_qty = 'true';
	} else {
		$wps_is_fixed_qty = 'false';
	}

	?>
 <style>
		.bump-offer-container {
			max-width: 650px;
			margin: 0 auto;
			border: 2px solid #007BFF;
			border-radius: 15px;
			background: linear-gradient(145deg, #ffffff, #f0f0f0);
			padding: 20px;
			box-shadow: 5px 5px 10px #aaaaaa, -5px -5px 10px #ffffff;
			transition: transform 0.3s ease-in-out;
		}

		.bump-offer-container:hover {
			transform: scale(1.02);
		}

		.bump-offer-title {
			font-size: 28px;
			font-weight: 700;
			margin-bottom: 20px;
			text-align: center;
			color: #007BFF;
		}

		.bump-offer-product {
			display: flex;
			align-items: center;
			margin-bottom: 20px;
		}

		.bump-offer-product img {
			max-width: 120px;
			border-radius: 10px;
			margin-right: 20px;
			box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.1);
		}

		.bump-offer-product-details {
			flex: 1;
		}

		.bump-offer-product-name {
			font-size: 20px;
			font-weight: 600;
			margin-bottom: 10px;
			color: #333;
		}

		.bump-offer-product-description {
			font-size: 16px;
			color: #555;
			margin-bottom: 10px;
		}

		.bump-offer-product-price {
			font-size: 18px;
			font-weight: 700;
			color: #28a745;
		}

		.bump-offer-checkbox {
			display: flex;
			flex-wrap: wrap;
			align-items: center;
			margin-top: 20px;
		}

		.bump-offer-checkbox input[type="checkbox"] {
			margin-right: 10px;
			transform: scale(1.5);
		}

		.bump-offer-checkbox label {
			font-size: 18px;
			color: #333;
		}

		@media (max-width: 650px) {
			.bump-offer-product {
				flex-direction: column;
				align-items: flex-start;
			}

			.bump-offer-product img {
				margin-right: 0;
				margin-bottom: 10px;
			}
		}



		@keyframes leftright {
			0% {
				transform: translateX(-5px)scaleX(-1);
			}
			60% {
				transform: translateX(-2px)scaleX(-1);
			}
			100% {
				transform: translateX(-5px)scaleX(-1);
			}
		}

		@keyframes shadow-pulse{

				0% {
					box-shadow: 0 0 0 1px #dcdcdc;
				}

				100% {
					box-shadow: 0 0 0 7px transparent;
				}
		}

		<?php echo esc_html( $order_bump_div_id ); ?> span.wps_upsell_offer_arrow {
			display: inline-block;
			transform: rotate(180deg);
			 animation: leftright 0.4s infinite ease;
			width: 20px;
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_arrow svg {
			/* fill: #eb483f; */
			fill: <?php echo esc_html( $primary_section_arrow_color ); ?>
		}

		.wps_upsell_product_permalink {
			text-decoration: none !important;
		}


		<?php echo esc_html( $order_bump_div_id ); ?> .bump-offer-container {
			border: <?php echo esc_html( $parent_border_type . ' ' . $parent_border_color . ' ' . $parent_border_width ); ?>;
			margin: 0 auto;
			<?php if ( 'no' === $wps_ubo_template_adaption ) : ?>
			max-width: 400px;
			<?php endif; ?>
		}

		<?php echo esc_html( $order_bump_div_id ); ?> .bump-offer-container {
			background-color:<?php echo esc_html( $parent_background_color ); ?>;
			/* padding : 20px; */
		}
		<?php echo esc_html( $order_bump_div_id ); ?> .bump-offer-title {
			background-color:<?php echo esc_html( $discount_section_background_color ); ?>;
			font-size: <?php echo esc_html( $discount_section_text_size ) . esc_html( 'px' ); ?>;
			color: <?php echo esc_html( $discount_section_text_color ); ?>;
		}

		<?php echo esc_html( $order_bump_div_id ); ?> {
			display: block;
			width: 100%;
			padding-top:  <?php echo esc_html( $parent_top_vertical_spacing ) . esc_html( 'px' ); ?>;
			padding-bottom:  <?php echo esc_html( $parent_bottom_vertical_spacing ) . esc_html( 'px' ); ?>;
			clear: both;
		}

		<?php echo esc_html( $order_bump_div_id ); ?> .bump-offer-product-description {
			color: <?php echo esc_html( $product_section_text_color ); ?>;
			font-size: <?php echo esc_html( $product_section_text_size ) . esc_html( 'px' ); ?>;
		}


	<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container {
	cursor: pointer;
	width: auto;
	font-size: 22px;
	height: 23px;
	margin: 0 0 6px 0;
	padding-left: 35px;
	position: relative;
	-webkit-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;
}

	<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container input {
	position: absolute;
	opacity: 0;
	cursor: pointer;
	height: 0;
	width: 0;
}
	<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container input:checked ~ .checkmark {
	background-color: #ffffff;
}


	<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container .checkmark {
	position: absolute;
	top: 0;
	margin: 3px;
	left: 0;
	height: 25px;
	width: 25px;
	background-color: #ffffff;
	animation: shadow-pulse 1.5s infinite;
}

	<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container input:checked ~ .checkmark:after {
	display: block;
	content: '';
	position: absolute;
}
	<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_bump_checkbox_container .checkmark:after {
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
	<?php echo esc_html( $order_bump_div_id ); ?> .wps_upsell_offer_img {
width: <?php echo esc_html( $product_section_img_width . 'px' ); ?>;
height: <?php echo esc_html( $product_section_img_height . 'px' ); ?>;
max-width: 80px;
max-height: 200px;
}


	<?php echo esc_html( $order_bump_div_id ); ?> .wps_accept_offer_cla {
	font-size: <?php echo esc_html( $primary_section_text_size ) . esc_html( 'px' ); ?>;
	background-color: <?php echo esc_html( $primary_section_background_color ); ?>;
	color: <?php echo esc_html( $primary_section_text_color ); ?>
}

	</style>

	<?php

		// Creating  red arrow html.
	if ( 'on' === $wps_enable_red_arrow_feature ) {
		$wps_ubo_red_arrow_svg  = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 198 111.52" demo="0 0 198 111.52"><defs></defs><g id="a"/><g id="b"><g id="c"><polygon class="d" points="198 25.35 198 86.17 96.62 86.17 96.62 111.52 48.36 83.64 0 55.76 48.36 27.88 96.62 0 96.62 25.35 198 25.35"/></g></g></svg>';
		$wps_ubo_red_arrow_html = '<div class = "wps_accept_offer_cla"><span class="wps_upsell_offer_arrow">' . $wps_ubo_red_arrow_svg . '</span>';
	} else {
		$wps_ubo_red_arrow_html = '<div class = "wps_accept_offer_cla">';
	}

	$bumphtml .= '<div class="bump-offer-container">
        <div class="bump-offer-title">' . wp_kses_post( $bump_price_html ) . '</div>
        <div class="bump-offer-product">';
		$bumphtml .= '<img class = "wps_upsell_offer_img" src="' . esc_url( $image ) . '" alt="Product Image">
            <div class="bump-offer-product-details">
                <div class="wps_bump_name bump-offer-product-name" data-qty_allowed="' . esc_html( $wps_upsell_enable_quantity ) . '" data-wps_is_fixed_qty="' . esc_html( $wps_is_fixed_qty ) . '" data-wps_qty="' . esc_html( $wps_upsell_bump_products_fixed_quantity ) . '">' . $bump['name'] . '</div>
                <div class="bump-offer-product-description">' . $product_description_text . '</div>
                <div class="bump-offer-product-price wps-ubo__temp-prod-price-new">' . $bump_offer_price . '</div>
            </div>
        </div>
        <div class="bump-offer-checkbox">';

	if ( 'yes' === $wps_upsell_enable_quantity && 'variable_q' === $wps_upsell_offer_quantity_type && wps_ubo_lite_if_pro_exists() && ! $bump_product->is_type( 'variable' ) ) {
		$bumphtml .= '<label for="wps_quantity_offer">' . __( 'Quantity', 'upsell-order-bump-offer-for-woocommerce' ) . ':</label>';
		$bumphtml .= '<input class="wps_input_quantity wps_quantity_input" type="number" name="wps_quantity_offer" value="' . $wps_upsell_bump_products_min_quantity . '" min="' . $wps_upsell_bump_products_min_quantity . '" max="' . $wps_upsell_bump_products_max_quantity . '">';
	}

		$bumphtml .= $wps_ubo_red_arrow_html;

		$bumphtml .= '<label class="wps_upsell_bump_checkbox_container"><input type="checkbox"' . $check . ' class="add_offer_in_cart" id="wps_checkbox_offer' . esc_html( $order_bump_key ) . '">
			<span class="checkmark"></span></label>
            <span class = "wps_accetp_offer_title">' . $title . '</span></div>
        </div>
    </div>';

	$bumphtml .= '</div></div>';

		// For simple product.
	if ( wps_ubo_lite_if_pro_exists() && ! empty( $offer_product ) && is_object( $offer_product ) && ! $offer_product->has_child() && ! is_admin() ) {

		$bumphtml .= apply_filters( 'wps_meta_forms_allowed_submission', $order_bump_div_id, $bump['meta_forms_allowed'], $bump['meta_form_fields'] );
	}

	return $bumphtml;
}



/**
 * Bump Offer Html For Summer Cool.
 *
 * @param   string $bump        Consists all data about order bump.
 * @param   string $encountered_order_bump_id        Consists all data about order bump.
 * @param   string $order_bump_key        Consists all data about order bump.
 * @since   1.0.0
 */
function wps_ubo_lite_bump_offer_html_12( $bump, $encountered_order_bump_id = '', $order_bump_key = '' ) {

	$discount_title_fixed = ! empty( $bump['design_text']['wps_ubo_discount_title_for_fixed'] ) ? $bump['design_text']['wps_ubo_discount_title_for_fixed'] : '';   // Discount Title. for fixes price.

	$discount_title_percent = ! empty( $bump['design_text']['wps_ubo_discount_title_for_percent'] ) ? $bump['design_text']['wps_ubo_discount_title_for_percent'] : ''; // Discount Title. for percentage price.

	$title = ! empty( $bump['design_text']['wps_upsell_offer_title'] ) ? $bump['design_text']['wps_upsell_offer_title'] : '';   // Lead Title.

	if ( ! empty( $bump['bump_price_html'] ) ) {

		$discount_title_fixed   = str_replace( '{dc_price}', $bump['bump_price_html'], $discount_title_fixed );
		$discount_title_percent = str_replace( '{dc_%}', $bump['bump_price_html'], $discount_title_percent );

	}

	if ( ! empty( $bump['price_type'] ) && 'fixed' === $bump['price_type'] ) {

		$bump_price_html = $discount_title_fixed;
	} else {

		$bump_price_html = $discount_title_percent;
	}

	// Setting to enable disable permalink.
	$wps_ubo_global_options    = get_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );
	$wps_bump_enable_permalink = ! empty( $wps_ubo_global_options['wps_bump_enable_permalink'] ) ? $wps_ubo_global_options['wps_bump_enable_permalink'] : '';

	$description = $bump['design_text']['wps_upsell_bump_offer_description'];   // Offer Description.
	// Red arrow setting.
	$wps_enable_red_arrow_feature = ! empty( $wps_ubo_global_options['wps_enable_red_arrow_feature'] ) ? $wps_ubo_global_options['wps_enable_red_arrow_feature'] : '';

	// Setting for the offer Quantity.
	$wps_upsell_enable_quantity              = ! empty( $bump['wps_upsell_enable_quantity'] ) ? $bump['wps_upsell_enable_quantity'] : '';
	$wps_upsell_bump_products_fixed_quantity = ! empty( $bump['wps_upsell_bump_products_fixed_quantity'] ) ? $bump['wps_upsell_bump_products_fixed_quantity'] : '';
	$wps_upsell_bump_products_min_quantity   = ! empty( $bump['wps_upsell_bump_products_min_quantity'] ) ? $bump['wps_upsell_bump_products_min_quantity'] : '';
	$wps_upsell_bump_products_max_quantity   = ! empty( $bump['wps_upsell_bump_products_max_quantity'] ) ? $bump['wps_upsell_bump_products_max_quantity'] : '';
	$wps_upsell_offer_quantity_type          = ! empty( $bump['wps_upsell_offer_quantity_type'] ) ? $bump['wps_upsell_offer_quantity_type'] : '';

	// PARENT WRAPPER DIV CSS( parent_wrapper_div ).
	$parent_border_type             = ! empty( $bump['design_css']['parent_border_type'] ) ? $bump['design_css']['parent_border_type'] : '';
	$parent_border_color            = ! empty( $bump['design_css']['parent_border_color'] ) ? $bump['design_css']['parent_border_color'] : '';
	$parent_background_color        = ! empty( $bump['design_css']['parent_background_color'] ) ? $bump['design_css']['parent_background_color'] : '';
	$parent_top_vertical_spacing    = ! empty( $bump['design_css']['top_vertical_spacing'] ) ? $bump['design_css']['top_vertical_spacing'] : '';
	$parent_bottom_vertical_spacing = ! empty( $bump['design_css']['bottom_vertical_spacing'] ) ? $bump['design_css']['bottom_vertical_spacing'] : '0';

	// DISCOUNT SECTION( discount_section ).
	$discount_section_background_color = ! empty( $bump['design_css']['discount_section_background_color'] ) ? $bump['design_css']['discount_section_background_color'] : '';
	$discount_section_text_color       = ! empty( $bump['design_css']['discount_section_text_color'] ) ? $bump['design_css']['discount_section_text_color'] : '';
	$discount_section_text_size        = ! empty( $bump['design_css']['discount_section_text_size'] ) ? $bump['design_css']['discount_section_text_size'] : '';

	// PRODUCT SECTION( product_section ).
	$product_section_text_color = ! empty( $bump['design_css']['product_section_text_color'] ) ? $bump['design_css']['product_section_text_color'] : '';
	$product_section_text_size  = ! empty( $bump['design_css']['product_section_text_size'] ) ? $bump['design_css']['product_section_text_size'] : '';
	$product_section_text_price_size  = ! empty( $bump['design_css']['product_section_price_text_size'] ) ? $bump['design_css']['product_section_price_text_size'] : '';
	$product_section_price_text_color = ! empty( $bump['design_css']['product_section_price_text_color'] ) ? $bump['design_css']['product_section_price_text_color'] : '';

	$product_section_img_width  = ! empty( $bump['design_css']['product_section_img_width'] ) ? $bump['design_css']['product_section_img_width'] : '';
	$product_section_img_height = ! empty( $bump['design_css']['product_section_img_height'] ) ? $bump['design_css']['product_section_img_height'] : '';

	// PRIMARY SECTION(primary_section ).
	$primary_section_background_color = ! empty( $bump['design_css']['primary_section_background_color'] ) ? $bump['design_css']['primary_section_background_color'] : '';
	$primary_section_text_color       = ! empty( $bump['design_css']['primary_section_text_color'] ) ? $bump['design_css']['primary_section_text_color'] : '';
	$primary_section_arrow_color      = ! empty( $bump['design_css']['primary_section_arrow_color'] ) ? $bump['design_css']['primary_section_arrow_color'] : '';
	$primary_section_text_size        = ! empty( $bump['design_css']['primary_section_text_size'] ) ? $bump['design_css']['primary_section_text_size'] : '';

	// SECONDARY SECTION( secondary_section ).
	$secondary_section_background_color = ! empty( $bump['design_css']['secondary_section_background_color'] ) ? $bump['design_css']['secondary_section_background_color'] : '';
	$secondary_section_text_color       = ! empty( $bump['design_css']['secondary_section_text_color'] ) ? $bump['design_css']['secondary_section_text_color'] : '';
	$secondary_section_text_size        = ! empty( $bump['design_css']['secondary_section_text_size'] ) ? $bump['design_css']['secondary_section_text_size'] : '';

	$wps_bump_target_attr = ! empty( $wps_ubo_global_options['wps_bump_target_link_attr_val'] ) ? $wps_ubo_global_options['wps_bump_target_link_attr_val'] : '';

	$order_bump_div_id = '#wps_upsell_offer_main_id_' . $encountered_order_bump_id;

	$bump_offer_product_permalink = '';

	// Template adaption.
	$wps_ubo_global_options    = get_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );
	$wps_ubo_template_adaption = ! empty( $wps_ubo_global_options['wps_ubo_temp_adaption'] ) ? $wps_ubo_global_options['wps_ubo_temp_adaption'] : '';

	// Add url of the offer product in the bump info.
	if ( 'on' == $wps_bump_enable_permalink ) {
		$bump_offer_product_permalink = esc_url_raw( get_permalink( $bump['id'] ) );
	}

	$parent_border_width = 'double' === $parent_border_type ? '4px' : '2px';

		// Template adaption.
		$wps_ubo_global_options    = get_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );

		$check = '';

		// Retain Checked if offer is added except for admin.
	if ( ! is_admin() && function_exists( 'WC' ) && ! empty( WC()->session ) ) {
		if ( null !== WC()->session->get( "bump_offer_status_index_$order_bump_key" ) ) {

			$check = 'checked';
		}
	}

	if ( ! empty( $bump['bump_price_html'] ) ) {

		$discount_title_fixed   = str_replace( '{dc_price}', $bump['bump_price_html'], $discount_title_fixed );
		$discount_title_percent = str_replace( '{dc_%}', $bump['bump_price_html'], $discount_title_percent );

	}

		$important = is_admin() ? '' : '!important';

		// Qty changed value.
		$wps_upsell_bump_products_min_quantity   = ! empty( $bump['wps_upsell_bump_products_min_quantity'] ) ? $bump['wps_upsell_bump_products_min_quantity'] : '';

		// Image Link purpose.
		$wps_bump_target_attr = ! empty( $wps_ubo_global_options['wps_bump_target_link_attr_val'] ) ? $wps_ubo_global_options['wps_bump_target_link_attr_val'] : '';

		// Incase no offer is added return.
		$bump['id']   = ! empty( $bump['id'] ) ? sanitize_text_field( $bump['id'] ) : '';
		$bump_product = wc_get_product( $bump['id'] );

		// If offer not found return.
	if ( empty( $bump['id'] ) || empty( $bump_product ) ) {

		return;
	}
		$bump['name'] = ! empty( $bump['name'] ) ? sanitize_text_field( $bump['name'] ) : '';

		$bump['discount_price'] = ! empty( $bump['discount_price'] ) ? sanitize_text_field( $bump['discount_price'] ) : '0';

		// After v2.0.1!
	if ( ! empty( $bump['offer_image'] ) ) {
		$image = wp_get_attachment_image_src( $bump['offer_image'], 'single-post-thumbnail' )[0];
	}

		// If still not found.
	if ( empty( $image ) ) {
		$thumbnail_id = get_post_thumbnail_id( $bump['id'] );
		$image_src = $thumbnail_id ? wp_get_attachment_image_src( $thumbnail_id, 'single-post-thumbnail' ) : null;

		if ( $image_src && isset( $image_src[0] ) ) {
			$image = $image_src[0];
		}
	}
	if ( empty( $image ) ) {

		$bump_parent_id = $bump_product->get_parent_id();

		if ( ! empty( $bump_parent_id ) ) {

			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $bump_parent_id ), 'single-post-thumbnail' )[0];

		} else {
			$image = wc_placeholder_img_src();
		}
	}

	if ( ! empty( $bump['price_type'] ) && 'fixed' === $bump['price_type'] ) {

		$bump_price_html = $discount_title_fixed;
	} else {

		$bump_price_html = $discount_title_percent;
	}

	/*
	* Get price html.
	*/
	$bump_offer_price = wps_ubo_lite_custom_price_html( $bump['id'], $bump['discount_price'] );
	$description = $bump['design_text']['wps_upsell_bump_offer_description'];   // Offer Description.
	$product_description_text = $bump['design_text']['wps_bump_offer_decsription_text'];

		$bumphtml = '';

		/*
		* Get price html.
		*/
		$bumphtml .= '<div id="wps_upsell_offer_main_id_' . $encountered_order_bump_id . '" class = "wps_upsell_offer_main_wrapper wps_new_template_12 wps_ubo_wrapper_index_' . $order_bump_key . '" >';

		$bumphtml .= '<input type="hidden" class ="offer_shown_id" value="' . $bump['id'] . '">';
		$bumphtml .= '<input type="hidden" class ="offer_shown_discount" value="' . $bump['discount_price'] . '">';
		$bumphtml .= '<input type="hidden" class ="target_id_cart_key" value="' . $bump['target_key'] . '">';
		$bumphtml .= '<input type="hidden" class ="order_bump_index" value="index_' . $order_bump_key . '">';
		$bumphtml .= '<input type="hidden" class ="order_bump_id" value="' . $encountered_order_bump_id . '">';

		$offer_product = wc_get_product( $bump['id'] );

	if ( ! empty( $offer_product ) && is_object( $offer_product ) && $offer_product->has_child() ) {

		$bumphtml .= '<input type="hidden" class ="offer_shown_id_type" value="variable">';
	}

	if ( ! empty( $bump['smart_offer_upgrade'] ) && 'yes' === $bump['smart_offer_upgrade'] ) {

		$bumphtml .= '<input type="hidden" class="order_bump_smo" value=' . $bump['smart_offer_upgrade'] . '>';
	}

	if ( is_admin() && ! empty( $bump['bump_price_at_zero'] ) ) :
		$bumphtml .= '<input type="hidden" class="bump_price_at_zero" value=' . $bump['bump_price_at_zero'] . '>';
		endif;

		$bumphtml                .= '<div class = "wps_upsell_offer_parent_wrapper" >';
		$bumphtml                .= '<div id = "wps_admin_timer"></div>';
		$wps_counter_timer_enable = isset( $bump['counter_timer'] ) ? $bump['counter_timer'] : '';
		$wps_evergreencounter_timer_enable = isset( $bump['evergreen_counter_timer'] ) ? $bump['evergreen_counter_timer'] : '';
		// Countdown Timer Section start.
	if ( ( 'yes' === $wps_counter_timer_enable || 'yes' == $wps_evergreencounter_timer_enable ) && wps_ubo_lite_if_pro_exists() ) {
		$bumphtml .= '<div class="expired_message_class" id = "expired_message' . esc_html( $order_bump_key ) . '"></div>';
		$bumphtml .= '<div class = "wps_timer_count wps_upsell_offer_discount_section" id ="wps_timer' . esc_html( $order_bump_key ) . '">
			<div class = "wps_day_timer_block wps-timer-wrap" >
			<div id ="wps_day_time_' . esc_html( $order_bump_key ) . '">0</div>
			<div id = "wps_day_label">Days</div>
			</div>
			<div class ="wps_timer_sept">:</div>
	
			<div class = "wps_hour_timer_block wps-timer-wrap">
			<div id ="wps_hour_time_' . esc_html( $order_bump_key ) . '">0</div>
			<div id = "wps_hour_label">Hour</div>
			</div>
			<div class ="wps_timer_sept">:</div>
	
			<div class = "wps_min_timer_block wps-timer-wrap">
			<div id ="wps_min_time_' . esc_html( $order_bump_key ) . '">0</div>
			<div id = "wps_min_label">Min</div>
			</div>
			<div class ="wps_timer_sept">:</div>
	
			<div class = "wps_sec_timer_block wps-timer-wrap">
			<div id ="wps_sec_time_' . esc_html( $order_bump_key ) . '">0</div>
			<div id = "wps_sec_label">Sec</div>
			</div>
			</div>';
	}
		// Countdown Timer Section End.

	if ( 'fixed_q' === $wps_upsell_offer_quantity_type ) {
		$wps_is_fixed_qty = 'true';
	} else {
		$wps_is_fixed_qty = 'false';
	}

		$bumphtml .= '<div class="wps-ob_temp-alpha-main" id="wps-ob_temp-alpha-main">
		<div class="wps-ob_temp-alpha" id="wps-ob_temp-alpha">
			<div class="wps-ob_ta-offer">' . $bump_price_html . '</div>
			<div class="wps-ob_temp-alpha-wrap">
				<div class="wps-ob_temp-alpha-in">
					<input type="checkbox" id="wps-ob_temp-alpha-check">';

	if ( 'on' === $wps_bump_enable_permalink ) {
		$bumphtml .= '<a target="' . $wps_bump_target_attr . '" href="' . $bump_offer_product_permalink . '"><img src="' . esc_url( $image ) . '" alt="product image" class="wps-ob_ta-p-img"></a>';

		$bumphtml .= '<div class="wps-ob_ta-p-name wps_bump_name bump-offer-product-name" data-qty_allowed="' . esc_html( $wps_upsell_enable_quantity ) . '" data-wps_is_fixed_qty="' . esc_html( $wps_is_fixed_qty ) . '" data-wps_qty="' . esc_html( $wps_upsell_bump_products_fixed_quantity ) . '"><a target="' . esc_html( $wps_bump_target_attr ) . '" class="wps_upsell_product_permalink" href="' . esc_url( $bump_offer_product_permalink ) . '">' . $bump['name'] . '</a></div>';
	} else {

		$bumphtml .= '<img src="' . esc_url( $image ) . '" alt="product image" class="wps-ob_ta-p-img">';

		$bumphtml .= '<div class="wps-ob_ta-p-name wps_bump_name bump-offer-product-name" data-qty_allowed="' . esc_html( $wps_upsell_enable_quantity ) . '" data-wps_is_fixed_qty="' . esc_html( $wps_is_fixed_qty ) . '" data-wps_qty="' . esc_html( $wps_upsell_bump_products_fixed_quantity ) . '">' . $bump['name'] . '</div>';
	}

					$bumphtml .= '<div class="wps-ob_ta-p-price">' . $bump_offer_price . '</div>
					<div class="wps-ob_ta-p-desc">' . $product_description_text . '</div>
				</div>
				<div class="wps-ob_ta-o-desc">' . $description . '</div>
			</div>';
	if ( 'yes' === $wps_upsell_enable_quantity && 'variable_q' === $wps_upsell_offer_quantity_type && wps_ubo_lite_if_pro_exists() && ! $bump_product->is_type( 'variable' ) ) {
		$bumphtml .= '<div class = "wps_variable_qty_temp_12"><label for="wps_quantity_offer">' . __( 'Quantity', 'upsell-order-bump-offer-for-woocommerce' ) . ':</label>';
		$bumphtml .= '<input class="wps_input_quantity wps_quantity_input" type="number" name="wps_quantity_offer" value="' . $wps_upsell_bump_products_min_quantity . '" min="' . $wps_upsell_bump_products_min_quantity . '" max="' . $wps_upsell_bump_products_max_quantity . '"></div>';
	}
			$bumphtml .= '<div class="wps-ob_ta-o-title">' . $title . '</div>
		</div>
	</div>';

	$bumphtml .= '<input name="add_offer_in_cart_checkbox"  type="checkbox" id ="wps_checkbox_offer' . esc_html( $order_bump_key ) . '" class="wps-ubo__temp-prod-check add_offer_in_cart wps_checkbox_template_12">';

	$bumphtml .= '</div></div>'; // end div close.

	// For simple product.
	if ( wps_ubo_lite_if_pro_exists() && ! empty( $offer_product ) && is_object( $offer_product ) && ! $offer_product->has_child() && ! is_admin() ) {

		$bumphtml .= apply_filters( 'wps_meta_forms_allowed_submission', $order_bump_div_id, $bump['meta_forms_allowed'], $bump['meta_form_fields'] );
	}
	?>



<style>
	<?php echo esc_html( $order_bump_div_id ); ?> .wps_ubo_bump_offer_preview .wps-ob_temp-alpha-main {
		max-width: 420px;
		margin: auto;
	}

	<?php echo esc_html( $order_bump_div_id ); ?> .wps_variable_qty_temp_12 {
	text-align: center;
	padding: 10px;
	}

	<?php echo esc_html( $order_bump_div_id ); ?> .wps-ob_temp-alpha-main {
		container-type: inline-size;
		min-width: 220px;
	}

	<?php echo esc_html( $order_bump_div_id ); ?> #wps-ob_temp-alpha {
		margin: 0 0 15px;
		border: <?php echo esc_html( $parent_border_type . ' ' . $parent_border_color . ' ' . $parent_border_width ); ?>;
	}

	<?php echo esc_html( $order_bump_div_id ); ?> #wps-ob_temp-alpha.wps-ob_checked {
		border: 2px solid #78c900;
		border-top: none;
		border-bottom: none;
		border-radius: 8px;
	}

	<?php echo esc_html( $order_bump_div_id ); ?> #wps-ob_temp-alpha .wps-ob_temp-alpha-wrap {
		background: <?php echo esc_html( $parent_background_color ); ?>;
		padding: 75px 15px 15px;
	}

	<?php echo esc_html( $order_bump_div_id ); ?> #wps-ob_temp-alpha img.wps-ob_ta-p-img {
		width: <?php echo esc_html( $product_section_img_width . 'px' ); ?>;
		height: <?php echo esc_html( $product_section_img_height . 'px' ); ?>;
		max-width: 120px;
		max-height: 120px;
		border-radius: 50%;
		object-fit: cover;
		margin: -60px auto 0;
		border: 5px solid #fff;
		box-shadow: 0 0 0 1.5px #e2e2e2;
	}

	<?php echo esc_html( $order_bump_div_id ); ?> #wps-ob_temp-alpha .wps-ob_temp-alpha-in {
		position: relative;
		background: #fff;
		border-radius: 5px;
		border: 1px solid #E2E2E2;
		margin: 0 0 15px;
		text-align: center;
		padding: 0 15px 15px;
	}

	<?php echo esc_html( $order_bump_div_id ); ?> #wps-ob_temp-alpha #wps-ob_temp-alpha-check {
		display: none;
	}

	<?php echo esc_html( $order_bump_div_id ); ?> #wps-ob_temp-alpha .wps-ob_ta-p-name {
		font-size: 24px;
		margin: 10px 0;
		line-height: 1.25;
		font-weight: bold;
		color: #1e1e1e;
	}

	<?php echo esc_html( $order_bump_div_id ); ?> #wps-ob_temp-alpha .wps-ob_ta-p-price {
		font-size: <?php echo esc_html( $product_section_text_price_size ) . esc_html( 'px' ); ?>;
		font-weight: bold;
		color: <?php echo esc_html( $product_section_price_text_color ); ?>;
	}

	<?php echo esc_html( $order_bump_div_id ); ?> #wps-ob_temp-alpha .wps-ob_ta-p-price del {
		color: #6c6c6c;
		font-weight: normal;
		margin: 0 5px 0 0;
	}

	<?php echo esc_html( $order_bump_div_id ); ?> #wps-ob_temp-alpha .wps-ob_ta-p-desc,
	#wps-ob_temp-alpha .wps-ob_ta-o-desc {
		font-size: <?php echo esc_html( $product_section_text_size ) . esc_html( 'px' ); ?>;
		line-height: 1.5;
		text-align: center;
		font-weight: normal;
		color: <?php echo esc_html( $product_section_text_color ); ?>
	}


	<?php echo esc_html( $order_bump_div_id ); ?> #wps-ob_temp-alpha .wps-ob_ta-o-desc {
		font-size: <?php echo esc_html( $secondary_section_text_size ) . esc_html( 'px' ); ?>;
		line-height: 1.5;
		text-align: center;
		font-weight: normal;
		color: <?php echo esc_html( $secondary_section_text_color ); ?>
	}

	<?php echo esc_html( $order_bump_div_id ); ?> #wps-ob_temp-alpha .wps-ob_ta-offer {
		background: <?php echo esc_html( $discount_section_background_color ); ?>;
		text-align: center;
		padding: 15px;
		border-radius: 5px 5px 0 0;
		position: relative;
		line-height: 1.25;
		font-size: <?php echo esc_html( $discount_section_text_size ) . esc_html( 'px' ); ?>;
		color: <?php echo esc_html( $discount_section_text_color ); ?>;
		font-weight: bold;
		letter-spacing: 0.4px;
	}

	<?php echo esc_html( $order_bump_div_id ); ?> #wps-ob_temp-alpha .wps-ob_ta-o-title {
		padding: 15px;
		color: <?php echo esc_html( $primary_section_text_color ); ?>;;
		background: <?php echo esc_html( $primary_section_background_color ); ?>;
		font-size: <?php echo esc_html( $primary_section_text_size ) . esc_html( 'px' ); ?>;
		text-align: center;
		border-radius: 0 0 5px 5px;
		line-height: 1.25;
		cursor: pointer;
	}

	@container (min-width: 620px) {
		<?php echo esc_html( $order_bump_div_id ); ?> #wps-ob_temp-alpha {
			display: grid;
			grid-template-columns: 4fr 1fr;
			gap: 15px 0;
		}

		<?php echo esc_html( $order_bump_div_id ); ?> #wps-ob_temp-alpha .wps-ob_ta-offer {
			order: 1;
			grid-column-start: 1;
			grid-column-end: 3;
			border-radius: 5px;
		}

		<?php echo esc_html( $order_bump_div_id ); ?> #wps-ob_temp-alpha .wps-ob_temp-alpha-wrap {
			display: grid;
			gap: 15px;
			grid-template-columns: 1fr 1fr;
			padding: 15px 15px 15px 75px;
		}

		<?php echo esc_html( $order_bump_div_id ); ?> #wps-ob_temp-alpha .wps-ob_ta-o-title {
			border-radius: 0 5px 5px 0;
			display: flex;
			align-items: center;
			font-size: 32px;
			font-weight: normal;
		}

		<?php echo esc_html( $order_bump_div_id ); ?> #wps-ob_temp-alpha .wps-ob_temp-alpha-in {
			margin: 0;
			display: flex;
			flex-direction: column;
			padding: 15px 15px 15px 85px;
			text-align: left;
		}

		<?php echo esc_html( $order_bump_div_id ); ?>  #wps-ob_temp-alpha h3.wps-ob_ta-p-name {
			margin: 0 0 10px;
		}

		<?php echo esc_html( $order_bump_div_id ); ?> #wps-ob_temp-alpha img.wps-ob_ta-p-img {
			margin: 0;
			position: absolute;
			left: -60px;
			top: 50%;
			transform: translate(0, -50%);
		}

		<?php echo esc_html( $order_bump_div_id ); ?> #wps-ob_temp-alpha .wps-ob_ta-p-desc,
		#wps-ob_temp-alpha .wps-ob_ta-o-desc {
			text-align: left;
		}

		<?php echo esc_html( $order_bump_div_id ); ?> #wps-ob_temp-alpha .wps-ob_ta-o-desc {
			display: flex;
			align-items: center;
		}
	}
</style>
	<?php
	return $bumphtml;
}



/**
 * Bump Offer Product Image Gallery.
 *
 * @param   string $bump_id        Product Id.
 * @since   1.0.0
 */
function wps_product_image_gallery_callback( $bump_id ) {

	$product = new WC_product( $bump_id );
	$attachment_ids = $product->get_gallery_image_ids();

	$bumphtml = '';

	$bumphtml .= '<div class="wps_product_gallery_img_focus_wrapper"><div class="wps_product_gallery_img_focus_wrapper_box"></div><span class="close">+</span></div>';
	$bumphtml .= '<div class="wps_product_gallery_wrapper">';

	foreach ( $attachment_ids as $attachment_id ) {
		  $bumphtml .= '<div class="wps_upsell_offer_img_wrap" ><img class="wps_upsell_offer_img_gallery" src="' . esc_url( wp_get_attachment_url( $attachment_id ) ) . '" data-id="' . $bump_id . '"></div>';
	}
		$bumphtml .= '</div>';

	return $bumphtml;
}
