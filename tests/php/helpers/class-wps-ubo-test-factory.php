<?php
/**
 * Small WooCommerce/plugin fixture factory (WC release builds ship no test helpers).
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce/Tests
 */

/**
 * Fixture factory.
 */
class WPS_UBO_Test_Factory {

	/**
	 * Create a published simple product.
	 *
	 * @param array $args name, regular_price, sale_price, stock_status, category_ids, virtual.
	 * @return WC_Product_Simple
	 */
	public static function simple_product( $args = array() ) {
		$args    = wp_parse_args(
			$args,
			array(
				'name'          => 'Product ' . wp_generate_password( 6, false ),
				'regular_price' => '20',
				'sale_price'    => '',
				'stock_status'  => 'instock',
				'category_ids'  => array(),
				'virtual'       => false,
			)
		);
		$product = new WC_Product_Simple();
		$product->set_name( $args['name'] );
		$product->set_status( 'publish' );
		$product->set_regular_price( $args['regular_price'] );
		$product->set_sale_price( $args['sale_price'] );
		$product->set_stock_status( $args['stock_status'] );
		$product->set_category_ids( $args['category_ids'] );
		$product->set_virtual( $args['virtual'] );
		$product->save();
		return wc_get_product( $product->get_id() );
	}

	/**
	 * Create a variable product with one attribute (size) and two variations.
	 *
	 * @param array $prices size => price.
	 * @return WC_Product_Variable
	 */
	public static function variable_product( $prices = array( 'small' => '10', 'large' => '15' ) ) {
		$product   = new WC_Product_Variable();
		$attribute = new WC_Product_Attribute();
		$attribute->set_name( 'size' );
		$attribute->set_options( array_keys( $prices ) );
		$attribute->set_visible( true );
		$attribute->set_variation( true );
		$product->set_name( 'Variable ' . wp_generate_password( 6, false ) );
		$product->set_status( 'publish' );
		$product->set_attributes( array( $attribute ) );
		$product->save();
		foreach ( $prices as $size => $price ) {
			$variation = new WC_Product_Variation();
			$variation->set_parent_id( $product->get_id() );
			$variation->set_attributes( array( 'size' => $size ) );
			$variation->set_regular_price( $price );
			$variation->set_status( 'publish' );
			$variation->save();
		}
		WC_Product_Variable::sync( $product->get_id() );
		return wc_get_product( $product->get_id() );
	}

	/**
	 * Create a product category.
	 *
	 * @param string $name   Name.
	 * @param int    $parent Parent term id.
	 * @return int Term id.
	 */
	public static function category( $name = '', $parent = 0 ) {
		$name = $name ? $name : 'Cat ' . wp_generate_password( 6, false );
		$term = wp_insert_term( $name, 'product_cat', array( 'parent' => $parent ) );
		return (int) $term['term_id'];
	}

	/**
	 * Create an order containing the given products.
	 *
	 * @param WC_Product[] $products Products.
	 * @param string       $status   Order status.
	 * @param array        $billing  Billing fields.
	 * @return WC_Order
	 */
	public static function order( $products, $status = 'completed', $billing = array() ) {
		$order = wc_create_order();
		foreach ( $products as $product ) {
			$order->add_product( $product, 1 );
		}
		$order->set_billing_email( isset( $billing['email'] ) ? $billing['email'] : 'buyer@example.org' );
		$order->set_billing_first_name( isset( $billing['first_name'] ) ? $billing['first_name'] : 'Test' );
		$order->calculate_totals();
		$order->set_status( $status );
		$order->save();
		return $order;
	}

	/**
	 * Store an order bump in wps_ubo_bump_list (same shape the admin creation form saves).
	 *
	 * @param int   $offer_product_id Offer product id.
	 * @param array $overrides        Bump keys to override.
	 * @param int   $bump_id          Bump id (list key).
	 * @param bool  $replace          Replace the whole list (lite behaviour) or merge.
	 * @return array The stored bump.
	 */
	public static function bump( $offer_product_id, $overrides = array(), $bump_id = 1, $replace = true ) {
		$bump = array_merge(
			array(
				'wps_upsell_bump_status'               => 'yes',
				'wps_upsell_bump_name'                 => 'Test bump ' . $bump_id,
				'wps_upsell_bump_target_ids'           => array(),
				'wps_upsell_bump_target_categories'    => array(),
				'wps_upsell_bump_exclude_roles'        => array(),
				'wps_upsell_bump_schedule'             => array( '0' ),
				'wps_upsell_bump_products_in_offer'    => (string) $offer_product_id,
				'wps_upsell_offer_price_type'          => '%',
				'wps_upsell_bump_offer_discount_price' => '20',
				'wps_bump_upsell_selected_template'    => '1',
				'wps_ubo_selected_template'            => '1',
				'wps_upsell_offer_image'               => '',
				'wps_ubo_offer_global_funnel'          => 'no',
				'wps_ubo_offer_replace_target'         => 'no',
				'design_css'                           => function_exists( 'wps_ubo_lite_offer_template_1' ) ? wps_ubo_lite_offer_template_1() : array(),
				'design_text'                          => function_exists( 'wps_ubo_lite_offer_default_text' ) ? wps_ubo_lite_offer_default_text() : array(),
			),
			$overrides
		);
		$list = $replace ? array() : (array) get_option( 'wps_ubo_bump_list', array() );
		$list[ $bump_id ] = $bump;
		update_option( 'wps_ubo_bump_list', $list );
		return $bump;
	}
}
