<?php
/**
 * Core order bump helper functions (global-functions.php).
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce/Tests
 */

/**
 * @coversNothing
 */
class LegacyFunctionsTest extends WPS_UBO_Test_Case {

	public function test_default_global_options() {
		$d = wps_ubo_lite_default_global_options();
		$this->assertSame( 'on', $d['wps_bump_enable_plugin'] );
		$this->assertSame( 'yes', $d['wps_bump_skip_offer'] );
		$this->assertSame( '_after_payment_gateways', $d['wps_ubo_offer_location'] );
		$this->assertSame( 'yes', $d['wps_ubo_offer_removal'] );
		$this->assertSame( 'regular_to_offer', $d['wps_ubo_offer_price_html'] );
		$this->assertSame( '1', $d['wps_bump_order_bump_limit'] );
	}

	public function test_default_text_and_template_css() {
		$text = wps_ubo_lite_offer_default_text();
		$this->assertArrayHasKey( 'wps_ubo_discount_title_for_percent', $text );
		$this->assertStringContainsString( '{dc_%}', $text['wps_ubo_discount_title_for_percent'] );
		$css = wps_ubo_lite_offer_template_1();
		$this->assertIsArray( $css );
		$this->assertArrayHasKey( 'parent_border_type', $css );
	}

	public function test_pro_not_detected_in_lite() {
		$this->assertFalse( wps_ubo_lite_if_pro_exists() );
	}

	public function test_allowed_html_keeps_bump_markup_and_strips_scripts() {
		$allowed = wps_ubo_lite_allowed_html();
		$this->assertArrayHasKey( 'input', $allowed );
		$this->assertArrayHasKey( 'class', $allowed['input'] );
		$this->assertSame( 'x', wp_kses( '<script>x</script>', $allowed ) );
		$this->assertStringContainsString( '<input', wp_kses( '<input type="checkbox" class="add_offer_in_cart">', $allowed ) );
	}

	/**
	 * @dataProvider location_provider
	 */
	public function test_bump_location_details( $key, $hook, $priority ) {
		$details = wps_ubo_lite_retrieve_bump_location_details( $key );
		$this->assertSame( $hook, $details['hook'] );
		$this->assertSame( $priority, $details['priority'] );
	}

	public function location_provider() {
		return array(
			'before summary'       => array( '_before_order_summary', 'woocommerce_checkout_order_review', 9 ),
			'before gateways'      => array( '_before_payment_gateways', 'woocommerce_checkout_order_review', 11 ),
			'after gateways'       => array( '_after_payment_gateways', 'wps_ubo_after_pg_before_terms', 19 ),
			'before place order'   => array( '_before_place_order_button', 'woocommerce_review_order_before_submit', 21 ),
		);
	}

	public function test_germanized_location() {
		$details = wps_ubo_lite_retrieve_bump_location_details_for_wc_germanized_compatibility( '_before_place_order_button' );
		$this->assertSame( 'woocommerce_gzd_review_order_before_submit', $details['hook'] );
	}

	/* ------------------------------------------------------------- pricing */

	/**
	 * @dataProvider price_provider
	 */
	public function test_custom_price_numeric( $discount, $expected ) {
		$p = WPS_UBO_Test_Factory::simple_product( array( 'regular_price' => '100' ) );
		$this->assertEquals( $expected, wps_ubo_lite_custom_price_html( $p->get_id(), $discount, 'price' ) );
	}

	public function price_provider() {
		return array(
			'20 percent'        => array( '20+%', 80 ),
			'over 100 percent'  => array( '150+%', 0 ),
			'negative percent'  => array( '-5+%', 100 ),
			'fixed price'       => array( '15+fixed', 15 ),
			'no discount'       => array( '0+no_disc', 100 ),
		);
	}

	public function test_custom_price_html_shows_regular_and_offer_price() {
		$p    = WPS_UBO_Test_Factory::simple_product( array( 'regular_price' => '100' ) );
		$html = wps_ubo_lite_custom_price_html( $p->get_id(), '20+%' );
		$this->assertStringContainsString( '<del', $html );
		$this->assertStringContainsString( '80', wp_strip_all_tags( $html ) );
		$this->assertStringContainsString( '100', wp_strip_all_tags( $html ) );
	}

	public function test_custom_price_invalid_product() {
		$this->assertNull( wps_ubo_lite_custom_price_html( 999999, '20+%', 'price' ) );
	}

	public function test_currency_filter_applied_to_bump_price() {
		$p      = WPS_UBO_Test_Factory::simple_product( array( 'regular_price' => '100' ) );
		$double = static function ( $price ) {
			return $price * 2;
		};
		add_filter( 'wps_ubo_convert_base_price_diffrent_currency', $double, 99 );
		$price = wps_ubo_lite_custom_price_html( $p->get_id(), '20+%', 'price' );
		remove_filter( 'wps_ubo_convert_base_price_diffrent_currency', $double, 99 );
		$this->assertEquals( 160, $price );
	}

	/* ----------------------------------------------------------- bump info */

	public function test_fetch_bump_offer_details_percent_and_fixed() {
		$p = WPS_UBO_Test_Factory::simple_product( array( 'regular_price' => '100', 'name' => 'Gift wrap' ) );
		WPS_UBO_Test_Factory::bump( $p->get_id() );
		$details = wps_ubo_lite_fetch_bump_offer_details( 1, 'cartkey' );
		$this->assertSame( $p->get_id(), (int) $details['id'] );
		$this->assertSame( '20+%', $details['discount_price'] );
		$this->assertSame( '20%', $details['bump_price_html'] );
		$this->assertSame( 'cartkey', $details['target_key'] );

		WPS_UBO_Test_Factory::bump(
			$p->get_id(),
			array(
				'wps_upsell_offer_price_type'          => 'fixed',
				'wps_upsell_bump_offer_discount_price' => '15',
			)
		);
		$details = wps_ubo_lite_fetch_bump_offer_details( 1 );
		$this->assertSame( '15+fixed', $details['discount_price'] );
		$this->assertEquals( 15, $details['bump_price_at_zero'] );
	}

	public function test_fetch_bump_offer_details_missing() {
		update_option( 'wps_ubo_bump_list', array() );
		$this->assertNull( wps_ubo_lite_fetch_bump_offer_details( 42 ) );
	}

	/* ---------------------------------------------------------- cart checks */

	public function test_check_if_in_cart_ignores_bump_items() {
		$a = WPS_UBO_Test_Factory::simple_product();
		$b = WPS_UBO_Test_Factory::simple_product();
		$key = WC()->cart->add_to_cart( $a->get_id() );
		WC()->cart->add_to_cart( $b->get_id(), 1, 0, array(), array( 'wps_discounted_price' => '20+%', 'wps_ubo_offer_product' => true ) );
		$this->assertSame( $key, wps_ubo_lite_check_if_in_cart( $a->get_id() ) );
		$this->assertNull( wps_ubo_lite_check_if_in_cart( $b->get_id() ) );
		$this->assertNull( wps_ubo_lite_check_if_in_cart( 999999 ) );
	}

	public function test_check_if_in_cart_matches_variation_by_parent() {
		$v   = WPS_UBO_Test_Factory::variable_product();
		$var = $v->get_children()[0];
		$key = WC()->cart->add_to_cart( $v->get_id(), 1, $var, array( 'attribute_size' => 'small' ) );
		$this->assertSame( $key, wps_ubo_lite_check_if_in_cart( $v->get_id() ) );
	}

	public function test_already_in_cart() {
		$a = WPS_UBO_Test_Factory::simple_product();
		$this->assertFalse( wps_ubo_lite_already_in_cart( $a->get_id() ) );
		WC()->cart->add_to_cart( $a->get_id() );
		$this->assertTrue( wps_ubo_lite_already_in_cart( $a->get_id() ) );
		$this->assertFalse( wps_ubo_lite_already_in_cart( 999999 ) );
	}

	public function test_category_in_cart_including_child_categories() {
		$parent = WPS_UBO_Test_Factory::category( 'Clothing' );
		$child  = WPS_UBO_Test_Factory::category( 'Shirts', $parent );
		$other  = WPS_UBO_Test_Factory::category( 'Music' );
		$shirt  = WPS_UBO_Test_Factory::simple_product( array( 'category_ids' => array( $child ) ) );
		WPS_UBO_Test_Factory::simple_product( array( 'category_ids' => array( $other ) ) );
		$key = WC()->cart->add_to_cart( $shirt->get_id() );

		$this->assertSame( $key, wps_ubo_lite_check_category_in_cart( $child ) );
		$this->assertSame( $key, wps_ubo_lite_check_category_in_cart( $parent ) );
		$this->assertNull( wps_ubo_lite_check_category_in_cart( $other ) );
	}

	public function test_category_has_parent() {
		$parent = WPS_UBO_Test_Factory::category( 'Top' );
		$child  = WPS_UBO_Test_Factory::category( 'Sub', $parent );
		WPS_UBO_Test_Factory::simple_product( array( 'category_ids' => array( $parent, $child ) ) );
		$this->assertSame( 'is_parent', wps_ubo_lite_category_has_parent( $parent ) );
		$this->assertSame( $parent, (int) wps_ubo_lite_category_has_parent( $child ) );
	}

	/* ------------------------------------------------------------- roles */

	public function test_is_valid_user_role_returns_true_when_role_excluded() {
		WPS_UBO_Test_Factory::bump( WPS_UBO_Test_Factory::simple_product()->get_id(), array( 'wps_upsell_bump_exclude_roles' => array( 'guest' ) ) );
		wp_set_current_user( 0 );
		$this->assertTrue( is_valid_user_role( 1 ), 'Guests excluded.' );

		wp_set_current_user( self::factory()->user->create( array( 'role' => 'customer' ) ) );
		$this->assertFalse( is_valid_user_role( 1 ) );

		WPS_UBO_Test_Factory::bump( WPS_UBO_Test_Factory::simple_product()->get_id(), array( 'wps_upsell_bump_exclude_roles' => array( 'customer' ) ) );
		$this->assertTrue( is_valid_user_role( 1 ) );
	}

	/* --------------------------------------------------------- validations */

	public function test_session_validations() {
		$offer = WPS_UBO_Test_Factory::simple_product();
		$bump  = WPS_UBO_Test_Factory::bump( $offer->get_id() );
		$g     = wps_ubo_lite_default_global_options();

		$this->assertNotFalse( wps_ubo_order_bump_session_validations( array( 1 => $bump ), $g, 1 ) );
		$this->assertFalse( wps_ubo_order_bump_session_validations( array( 1 => $bump ), $g, null ) );
		$this->assertFalse( wps_ubo_order_bump_session_validations( array( 1 => array_merge( $bump, array( 'wps_upsell_bump_status' => 'no' ) ) ), $g, 1 ) );

		WC()->cart->add_to_cart( $offer->get_id() );
		$this->assertFalse( wps_ubo_order_bump_session_validations( array( 1 => $bump ), $g, 1 ), 'Offer already in cart and skip=yes.' );
		$this->assertNotFalse( wps_ubo_order_bump_session_validations( array( 1 => $bump ), array_merge( $g, array( 'wps_bump_skip_offer' => 'no' ) ), 1 ) );
	}

	public function test_session_validations_out_of_stock_offer() {
		$offer = WPS_UBO_Test_Factory::simple_product( array( 'stock_status' => 'outofstock' ) );
		$bump  = WPS_UBO_Test_Factory::bump( $offer->get_id() );
		$this->assertFalse( wps_ubo_order_bump_session_validations( array( 1 => $bump ), wps_ubo_lite_default_global_options(), 1 ) );
	}

	public function test_session_destroy_clears_bump_keys() {
		WC()->session->set( 'encountered_bump_array', array( 1 ) );
		WC()->session->set( 'encountered_bump_tarket_key_array', array( 'k' ) );
		WC()->session->set( 'bump_offer_status', 'added' );
		wps_ubo_session_destroy();
		$this->assertNull( WC()->session->get( 'encountered_bump_array' ) );
		$this->assertNull( WC()->session->get( 'encountered_bump_tarket_key_array' ) );
		$this->assertNull( WC()->session->get( 'bump_offer_status' ) );
	}

	public function test_destroy_encountered_session() {
		WC()->session->set( 'encountered_bump_array', array( 1 ) );
		wps_ubo_destroy_encountered_session();
		$this->assertNull( WC()->session->get( 'encountered_bump_array' ) );
	}

	/* ------------------------------------------------- dynamic conditions */

	/**
	 * @dataProvider compare_provider
	 */
	public function test_compare_rule_value( $op, $target, $values, $expected ) {
		$this->assertSame( $expected, (bool) wc_compare_rule_value( 'field', $op, $target, $values ) );
	}

	public function compare_provider() {
		return array(
			'greater true'         => array( 'greater_than', 150, array( '100' ), true ),
			'greater false'        => array( 'greater_than', 50, array( '100' ), false ),
			'less true'            => array( 'less_than', 50, array( '100' ), true ),
			'is case insensitive'  => array( 'is', 'IN', array( 'in', 'us' ), true ),
			'is miss'              => array( 'is', 'FR', array( 'in', 'us' ), false ),
			'is_not with match'    => array( 'is_not', array( 'C1' ), array( 'c1' ), false ),
			'is_not without match' => array( 'is_not', array( 'C2' ), array( 'c1' ), true ),
			'unknown operator'     => array( 'between', 1, array( 1 ), false ),
		);
	}

	public function test_detect_device_type() {
		$saved = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : null;
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) Mobile/15E148';
		$this->assertSame( 'mobile', wc_detect_device_type() );
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0';
		$this->assertSame( 'desktop', wc_detect_device_type() );
		unset( $_SERVER['HTTP_USER_AGENT'] );
		$this->assertSame( 'desktop', wc_detect_device_type() );
		if ( null !== $saved ) {
			$_SERVER['HTTP_USER_AGENT'] = $saved;
		}
	}

	public function test_sanitize_rules_array() {
		$this->assertSame(
			array(
				'a' => 'x',
				'n' => array( 'y' ),
			),
			sanitize_rules_array(
				array(
					'a' => '<b>x</b>',
					'n' => array( '<i>y' ),
				)
			)
		);
	}

	public function test_evaluate_rule_cart_subtotal_and_user_status() {
		$p = WPS_UBO_Test_Factory::simple_product( array( 'regular_price' => '60' ) );
		WC()->cart->add_to_cart( $p->get_id(), 2 );
		WC()->cart->calculate_totals();
		$this->assertTrue( (bool) wc_evaluate_rule_condition( array( 'field' => 'subtotal', 'operator' => 'greater_than', 'value' => array( '100' ) ), WC()->cart ) );
		$this->assertFalse( (bool) wc_evaluate_rule_condition( array( 'field' => 'subtotal', 'operator' => 'less_than', 'value' => array( '100' ) ), WC()->cart ) );
		$this->assertTrue( (bool) wc_evaluate_rule_condition( array( 'field' => 'user_status', 'operator' => 'is', 'value' => array( 'guest' ) ), WC()->cart ) );
		$this->assertFalse( (bool) wc_evaluate_rule_condition( array( 'field' => 'unknown_field', 'operator' => 'is', 'value' => array( 'x' ) ), WC()->cart ) );
	}

	public function test_dynamic_conditions_pass_all_rules_must_match() {
		$p = WPS_UBO_Test_Factory::simple_product( array( 'regular_price' => '60' ) );
		WC()->cart->add_to_cart( $p->get_id(), 2 );
		WC()->cart->calculate_totals();

		$this->assertFalse( (bool) wc_dynamic_discount_conditions_pass( 'wps_bump_one', 1 ), 'No rules → false.' );

		update_option(
			'wc_dynamic_discount_rules',
			array(
				'wps_bump_one' => array(
					1 => array(
						array( 'field' => 'subtotal', 'operator' => 'greater_than', 'value' => array( '100' ) ),
						array( 'field' => 'user_status', 'operator' => 'is', 'value' => array( 'guest' ) ),
					),
				),
			)
		);
		$this->assertTrue( (bool) wc_dynamic_discount_conditions_pass( 'wps_bump_one', 1 ) );

		update_option(
			'wc_dynamic_discount_rules',
			array(
				'wps_bump_one' => array(
					1 => array(
						array( 'field' => 'subtotal', 'operator' => 'greater_than', 'value' => array( '100' ) ),
						array( 'field' => 'user_status', 'operator' => 'is', 'value' => array( 'logged_in' ) ),
					),
				),
			)
		);
		$this->assertFalse( (bool) wc_dynamic_discount_conditions_pass( 'wps_bump_one', 1 ) );
	}

	/* ------------------------------------------------------- bump selection */

	public function test_fetch_order_bump_from_collection_matches_target_product() {
		$target = WPS_UBO_Test_Factory::simple_product();
		$offer  = WPS_UBO_Test_Factory::simple_product();
		$bump   = WPS_UBO_Test_Factory::bump( $offer->get_id(), array( 'wps_upsell_bump_target_ids' => array( (string) $target->get_id() ) ) );
		$g      = wps_ubo_lite_default_global_options();

		$this->assertEmpty( Upsell_Order_Bump_Offer_For_Woocommerce_Public::fetch_order_bump_from_collection( array( 1 => $bump ), $g )['encountered_bump_array'] ?? array() );

		$key    = WC()->cart->add_to_cart( $target->get_id() );
		$result = Upsell_Order_Bump_Offer_For_Woocommerce_Public::fetch_order_bump_from_collection( array( 1 => $bump ), $g );
		$this->assertContains( 1, array_map( 'intval', $result['encountered_bump_array'] ) );
		$this->assertContains( $key, $result['wps_upsell_bump_target_key'] );
	}

	public function test_fetch_order_bump_from_collection_respects_status_and_schedule() {
		$target = WPS_UBO_Test_Factory::simple_product();
		$offer  = WPS_UBO_Test_Factory::simple_product();
		WC()->cart->add_to_cart( $target->get_id() );
		$g = wps_ubo_lite_default_global_options();

		$off = WPS_UBO_Test_Factory::bump(
			$offer->get_id(),
			array(
				'wps_upsell_bump_target_ids' => array( (string) $target->get_id() ),
				'wps_upsell_bump_status'     => 'no',
			)
		);
		$this->assertEmpty( Upsell_Order_Bump_Offer_For_Woocommerce_Public::fetch_order_bump_from_collection( array( 1 => $off ), $g )['encountered_bump_array'] ?? array() );

		$other_day = (string) ( ( (int) gmdate( 'N' ) % 7 ) + 1 );
		$sched     = WPS_UBO_Test_Factory::bump(
			$offer->get_id(),
			array(
				'wps_upsell_bump_target_ids' => array( (string) $target->get_id() ),
				'wps_upsell_bump_schedule'   => array( $other_day ),
			)
		);
		$this->assertEmpty( Upsell_Order_Bump_Offer_For_Woocommerce_Public::fetch_order_bump_from_collection( array( 1 => $sched ), $g )['encountered_bump_array'] ?? array() );

		$today = WPS_UBO_Test_Factory::bump(
			$offer->get_id(),
			array(
				'wps_upsell_bump_target_ids' => array( (string) $target->get_id() ),
				'wps_upsell_bump_schedule'   => array( gmdate( 'N' ) ),
			)
		);
		$this->assertNotEmpty( Upsell_Order_Bump_Offer_For_Woocommerce_Public::fetch_order_bump_from_collection( array( 1 => $today ), $g )['encountered_bump_array'] );
	}

	public function test_fetch_order_bump_from_collection_matches_category() {
		$cat    = WPS_UBO_Test_Factory::category();
		$target = WPS_UBO_Test_Factory::simple_product( array( 'category_ids' => array( $cat ) ) );
		$offer  = WPS_UBO_Test_Factory::simple_product();
		$bump   = WPS_UBO_Test_Factory::bump( $offer->get_id(), array( 'wps_upsell_bump_target_categories' => array( (string) $cat ) ) );
		WC()->cart->add_to_cart( $target->get_id() );
		$result = Upsell_Order_Bump_Offer_For_Woocommerce_Public::fetch_order_bump_from_collection( array( 1 => $bump ), wps_ubo_lite_default_global_options() );
		$this->assertContains( 1, array_map( 'intval', $result['encountered_bump_array'] ) );
	}

	/* ---------------------------------------------------- free-tier limit */

	public function test_lite_bump_list_returns_only_first_bump_without_smart_upgrade() {
		$p = WPS_UBO_Test_Factory::simple_product()->get_id();
		WPS_UBO_Test_Factory::bump( $p, array( 'wps_ubo_offer_replace_target' => 'yes' ), 3 );
		WPS_UBO_Test_Factory::bump( $p, array(), 5, false );
		$list = Upsell_Order_Bump_Offer_For_Woocommerce::wps_upsell_bump_list_callback_return();
		$this->assertSame( array( 3 ), array_keys( $list ) );
		$this->assertSame( 'no', $list[3]['wps_ubo_offer_replace_target'] );
	}
}
