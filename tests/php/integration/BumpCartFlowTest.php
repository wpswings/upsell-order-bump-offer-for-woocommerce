<?php
/**
 * Core order bump flow: add/remove via AJAX, discounted price in cart, target removal,
 * order item meta, bump statistics and the bump HTML on checkout.
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce/Tests
 */

/**
 * @covers Upsell_Order_Bump_Offer_For_Woocommerce_Public
 */
class BumpCartFlowTest extends WPS_UBO_Test_Case {

	/** @var Upsell_Order_Bump_Offer_For_Woocommerce_Public */
	private $pub;

	/** @var WC_Product */
	private $target;

	/** @var WC_Product */
	private $offer;

	public function set_up() {
		parent::set_up();
		$this->pub    = new Upsell_Order_Bump_Offer_For_Woocommerce_Public( 'upsell-order-bump-offer-for-woocommerce', '3.1.8' );
		$this->target = WPS_UBO_Test_Factory::simple_product( array( 'name' => 'Camera', 'regular_price' => '200' ) );
		$this->offer  = WPS_UBO_Test_Factory::simple_product( array( 'name' => 'Lens cap', 'regular_price' => '50' ) );
		update_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );
		WPS_UBO_Test_Factory::bump( $this->offer->get_id(), array( 'wps_upsell_bump_target_ids' => array( (string) $this->target->get_id() ) ) );
		$this->viewed();
	}

	/**
	 * Record a view, as rendering the bump on checkout does before it can be accepted.
	 */
	private function viewed() {
		( new Wps_Upsell_Order_Bump_Report_Sales_By_Bump( 1 ) )->add_offer_view_count();
	}

	private function add_offer( $overrides = array() ) {
		$target_key = WC()->cart->find_product_in_cart( WC()->cart->generate_cart_id( $this->target->get_id() ) );
		return $this->ajax_call(
			array( $this->pub, 'add_offer_in_cart' ),
			array_merge(
				array(
					'nonce'                => wp_create_nonce( 'wps_ubo_lite_nonce' ),
					'id'                   => $this->offer->get_id(),
					'discount'             => '99+%',
					'bump_index'           => 'index_1',
					'bump_target_cart_key' => $target_key,
					'order_bump_id'        => '1',
				),
				$overrides
			)
		);
	}

	private function bump_item() {
		foreach ( WC()->cart->get_cart() as $key => $item ) {
			if ( ! empty( $item['wps_ubo_offer_product'] ) ) {
				return array( $key, $item );
			}
		}
		return array( null, null );
	}

	public function test_add_offer_adds_item_with_server_side_discount() {
		WC()->cart->add_to_cart( $this->target->get_id() );
		$r = $this->add_offer();
		$this->assertSame( 'added', $r['json'] );

		list( $key, $item ) = $this->bump_item();
		$this->assertNotNull( $key );
		$this->assertSame( '20+%', $item['wps_discounted_price'], 'Client-posted discount (99%) is ignored.' );
		$this->assertSame( '1', (string) $item['wps_ubo_bump_id'] );
		$this->assertSame( 'index_1', $item['wps_ubo_offer_index'] );
		$this->assertSame( 'added', WC()->session->get( 'bump_offer_status' ) );
		$this->assertSame( $key, WC()->session->get( 'bump_offer_status_index_1' ) );

		WC()->cart->calculate_totals();
		list( , $item ) = $this->bump_item();
		$this->assertEqualsWithDelta( 40.0, (float) $item['data']->get_price(), 0.001, '50 at 20% off.' );
		$this->assertEqualsWithDelta( 240.0, (float) WC()->cart->get_subtotal(), 0.001 );
	}

	public function test_add_offer_rejects_bad_nonce() {
		$r = $this->add_offer( array( 'nonce' => 'nope' ) );
		$this->assertSame( '-1', trim( $r['raw'] ) );
		$this->assertNull( $this->bump_item()[0] );
	}

	public function test_fixed_price_offer() {
		WPS_UBO_Test_Factory::bump(
			$this->offer->get_id(),
			array(
				'wps_upsell_offer_price_type'          => 'fixed',
				'wps_upsell_bump_offer_discount_price' => '12',
			)
		);
		$this->viewed();
		WC()->cart->add_to_cart( $this->target->get_id() );
		$this->add_offer();
		WC()->cart->calculate_totals();
		$this->assertEqualsWithDelta( 12.0, (float) $this->bump_item()[1]['data']->get_price(), 0.001 );
	}

	public function test_variable_offer_returns_price_html_and_adds_nothing() {
		$var = WPS_UBO_Test_Factory::variable_product();
		WPS_UBO_Test_Factory::bump( $var->get_id() );
		$r = $this->add_offer( array( 'id' => $var->get_id() ) );
		$this->assertSame( 'true', $r['json']['key'] );
		$this->assertNotEmpty( $r['json']['message'] );
		$this->assertNull( $this->bump_item()[0] );
	}

	public function test_remove_offer() {
		WC()->cart->add_to_cart( $this->target->get_id() );
		$this->add_offer();
		$r = $this->ajax_call(
			array( $this->pub, 'remove_offer_in_cart' ),
			array(
				'nonce'         => wp_create_nonce( 'wps_ubo_lite_nonce' ),
				'bump_index'    => 'index_1',
				'order_bump_id' => '1',
			)
		);
		$this->assertSame( 'removed', $r['json'] );
		$this->assertNull( $this->bump_item()[0] );
		$this->assertNull( WC()->session->get( 'bump_offer_status_index_1' ) );
		$this->assertCount( 1, WC()->cart->get_cart() );
	}

	public function test_removing_target_removes_dependent_offer() {
		$target_key = WC()->cart->add_to_cart( $this->target->get_id() );
		$this->add_offer();
		$this->assertCount( 2, WC()->cart->get_cart() );
		WC()->cart->remove_cart_item( $target_key );
		$this->assertNull( $this->bump_item()[0], 'Offer removed with its target (wps_ubo_offer_removal = yes).' );
	}

	public function test_accept_stats_recorded_in_bump_list() {
		WC()->cart->add_to_cart( $this->target->get_id() );
		$this->add_offer();
		$list = get_option( 'wps_ubo_bump_list' );
		$this->assertSame( 1, (int) $list[1]['offer_accept_count'] );
		$this->assertSame( 0, (int) $list[1]['offer_view_count'], 'Accepting removes the duplicate view from the re-render.' );
	}

	public function test_reporting_counters() {
		$report = new Wps_Upsell_Order_Bump_Report_Sales_By_Bump( 1 );
		$report->add_offer_view_count();
		$report->add_offer_view_count();
		$report->add_offer_remove_count();
		$report->add_bump_success_count();
		$report->add_bump_total_sales( 25.5 );
		$list = get_option( 'wps_ubo_bump_list' );
		$this->assertSame( 3, (int) $list[1]['offer_view_count'], 'One view from set_up plus two.' );
		$this->assertSame( 1, (int) $list[1]['offer_remove_count'] );
		$this->assertSame( 1, (int) $list[1]['bump_success_count'] );
		$this->assertEqualsWithDelta( 25.5, (float) $list[1]['bump_total_sales'], 0.001 );

		// Unknown bump: nothing written.
		( new Wps_Upsell_Order_Bump_Report_Sales_By_Bump( 99 ) )->add_offer_view_count();
		$this->assertArrayNotHasKey( 99, get_option( 'wps_ubo_bump_list' ) );
	}

	public function test_order_line_item_gets_bump_meta_and_meta_is_hidden() {
		WC()->cart->add_to_cart( $this->target->get_id() );
		$this->add_offer();
		WC()->cart->calculate_totals();
		$order_id = WC()->checkout()->create_order(
			array(
				'payment_method' => 'cod',
				'billing_email'  => 'buyer@example.org',
			)
		);
		$order = wc_get_order( $order_id );
		$found = null;
		foreach ( $order->get_items() as $item ) {
			if ( 'true' === $item->get_meta( 'is_order_bump_purchase' ) ) {
				$found = $item;
			}
		}
		$this->assertNotNull( $found );
		$this->assertSame( 'Lens cap', $found->get_name() );
		$this->assertEqualsWithDelta( 40.0, (float) $found->get_total(), 0.001 );

		$hidden = $this->pub->hide_order_bump_meta( $found->get_formatted_meta_data( '_', true ) );
		foreach ( $hidden as $meta ) {
			$this->assertNotSame( 'is_order_bump_purchase', $meta->key );
		}
	}

	public function test_order_marked_as_bump_order() {
		WC()->cart->add_to_cart( $this->target->get_id() );
		$this->add_offer();
		WC()->cart->calculate_totals();
		$order_id = WC()->checkout()->create_order( array( 'payment_method' => 'cod', 'billing_email' => 'b@example.org' ) );
		$this->pub->wps_mark_bump_order_if_needed( $order_id );
		$this->assertSame( 'true', wc_get_order( $order_id )->get_meta( 'wps_bump_order' ) );
	}

	public function test_cart_emptied_clears_bump_session() {
		WC()->cart->add_to_cart( $this->target->get_id() );
		$this->add_offer();
		WC()->cart->empty_cart();
		$this->assertNull( WC()->session->get( 'bump_offer_status' ) );
	}

	public function test_bump_html_rendered_with_checkbox_and_price() {
		$key = WC()->cart->add_to_cart( $this->target->get_id() );
		ob_start();
		wps_ubo_analyse_and_display_order_bump( 1, $key, 1 );
		$html = ob_get_clean();
		$this->assertStringContainsString( 'wps_upsell_offer_main_id_1', $html );
		$this->assertStringContainsString( 'add_offer_in_cart', $html );
		$this->assertStringContainsString( 'Lens cap', $html );
		$this->assertStringContainsString( 'index_1', $html );
	}

	public function test_bump_output_can_be_replaced_by_filter() {
		$handled = static function () {
			echo '<div class="custom-bump"></div>';
			return true;
		};
		add_filter( 'wps_ubo_bump_offer_output_handled', $handled );
		ob_start();
		wps_ubo_analyse_and_display_order_bump( 1, '', 1 );
		$html = ob_get_clean();
		remove_filter( 'wps_ubo_bump_offer_output_handled', $handled );
		$this->assertStringContainsString( 'custom-bump', $html );
		$this->assertStringNotContainsString( 'add_offer_in_cart', $html );
	}

	/* ------------------------------------------- gamified bump on the cart */

	public function test_gamified_play_draws_prize_and_returns_bump_html() {
		update_option(
			Upsell_Order_Bump_Gamified::SETTINGS_OPTION,
			Upsell_Order_Bump_Gamified::sanitize_settings(
				array(
					'enabled'    => 'on',
					'formats'                  => array( 1 => 'scratch_card' ),
					'allow_skip'               => 'on',
					'guarantee_offer_discount' => 'on',
				)
			)
		);
		$game = new Upsell_Order_Bump_Gamified_Public( '3.1.8' );
		$post = array( 'nonce' => wp_create_nonce( Upsell_Order_Bump_Gamified_Public::NONCE_ACTION ), 'bump_id' => 1 );

		// Not shown to this customer yet → refused.
		$r = $this->ajax_call( array( $game, 'ajax_play' ), $post );
		$this->assertFalse( $r['json']['success'] );

		WC()->session->set( 'encountered_bump_array', array( 1 ) );
		$r = $this->ajax_call( array( $game, 'ajax_play' ), $post );
		$this->assertTrue( $r['json']['success'] );
		$this->assertGreaterThanOrEqual( 20, $r['json']['data']['discount'], 'Never lower than the bump\'s own 20%.' );
		$this->assertFalse( $r['json']['data']['repeat'] );

		$again = $this->ajax_call( array( $game, 'ajax_play' ), array_merge( $post, array( 'nonce' => wp_create_nonce( Upsell_Order_Bump_Gamified_Public::NONCE_ACTION ) ) ) );
		$this->assertTrue( $again['json']['data']['repeat'], 'One play per session.' );
		$this->assertSame( $r['json']['data']['discount'], $again['json']['data']['discount'] );

		// The won discount is what the offer costs in the cart.
		WC()->cart->add_to_cart( $this->target->get_id() );
		$this->add_offer();
		WC()->cart->calculate_totals();
		$expected = round( 50 * ( 100 - $r['json']['data']['discount'] ) / 100, 2 );
		$this->assertEqualsWithDelta( $expected, (float) $this->bump_item()[1]['data']->get_price(), 0.01 );
	}

	public function test_gamified_render_game_markup() {
		update_option( Upsell_Order_Bump_Gamified::SETTINGS_OPTION, Upsell_Order_Bump_Gamified::sanitize_settings( array( 'enabled' => 'on', 'formats' => array( 1 => 'spin_wheel' ) ) ) );
		$game = new Upsell_Order_Bump_Gamified_Public( '3.1.8' );
		ob_start();
		$handled = $game->render_game( false, 1 );
		$html    = ob_get_clean();
		$this->assertTrue( $handled );
		$this->assertStringContainsString( 'wps-ubo-game--spin_wheel', $html );
		$this->assertStringContainsString( 'wps-ubo-game__play', $html );
		$this->assertStringContainsString( 'Lens cap', $html );

		ob_start();
		$this->assertFalse( $game->render_game( false, 2 ), 'Standard bump not replaced.' );
		ob_end_clean();
	}
}
