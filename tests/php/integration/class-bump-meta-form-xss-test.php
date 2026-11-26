<?php
/**
 * WPS-8604: guest-submitted bump meta form data ( form_data ) must not reach
 * order item meta as markup.
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce/Tests
 */

/**
 * Bump meta form stored XSS tests.
 *
 * @covers ::wps_ubo_lite_sanitize_bump_meta_form
 * @covers ::wps_ubo_lite_clean_meta_form_text
 * @covers Upsell_Order_Bump_Offer_For_Woocommerce_Public
 */
class Bump_Meta_Form_Xss_Test extends WPS_UBO_Test_Case {

	/**
	 * The researcher's payload: survives sanitize_text_field().
	 */
	const PAYLOAD = '< div id="tagsdiv-post_tag">< /div>< div class="tagsdiv" id="WFPROOF&quot;&gt;&lt;img src=x onerror=alert(document.domain)&gt;">< textarea class="the-tags">WFPROOF< /textarea>< /div>';

	/**
	 * Pub.
	 *
	 * @var Upsell_Order_Bump_Offer_For_Woocommerce_Public
	 */
	private $pub;

	/**
	 * Target.
	 *
	 * @var WC_Product
	 */
	private $target;

	/**
	 * Offer.
	 *
	 * @var WC_Product
	 */
	private $offer;

	/**
	 * Set up the test fixture.
	 */
	public function set_up() {
		parent::set_up();
		$this->pub    = new Upsell_Order_Bump_Offer_For_Woocommerce_Public( 'upsell-order-bump-offer-for-woocommerce', '3.1.8' );
		$this->target = WPS_UBO_Test_Factory::simple_product(
			array(
				'name'          => 'Camera',
				'regular_price' => '200',
			)
		);
		$this->offer  = WPS_UBO_Test_Factory::simple_product(
			array(
				'name'          => 'Lens cap',
				'regular_price' => '50',
			)
		);
		update_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );
		$this->make_bump( 'yes' );
		( new Wps_Upsell_Order_Bump_Report_Sales_By_Bump( 1 ) )->add_offer_view_count();
	}

	/**
	 * Bump 1 with a "Gift message" meta form field.
	 *
	 * @param string $meta_forms Meta forms on / off.
	 */
	private function make_bump( $meta_forms ) {
		WPS_UBO_Test_Factory::bump(
			$this->offer->get_id(),
			array(
				'wps_upsell_bump_target_ids' => array( (string) $this->target->get_id() ),
				'wps_ubo_offer_meta_forms'   => $meta_forms,
				'meta_form_fields'           => array(
					array(
						'label'       => 'Gift message',
						'type'        => 'text',
						'placeholder' => '',
						'description' => '',
					),
				),
			)
		);
	}

	/**
	 * Call add_offer_in_cart as the storefront does.
	 *
	 * @param array $form_data Form data.
	 */
	private function add_offer( $form_data ) {
		WC()->cart->add_to_cart( $this->target->get_id() );
		$target_key = WC()->cart->find_product_in_cart( WC()->cart->generate_cart_id( $this->target->get_id() ) );
		return $this->ajax_call(
			array( $this->pub, 'add_offer_in_cart' ),
			array(
				'nonce'                => wp_create_nonce( 'wps_ubo_lite_nonce' ),
				'id'                   => $this->offer->get_id(),
				'bump_index'           => 'index_1',
				'bump_target_cart_key' => $target_key,
				'order_bump_id'        => '1',
				'form_data'            => $form_data,
			)
		);
	}

	/**
	 * The bump item in the cart.
	 */
	private function bump_cart_item() {
		foreach ( WC()->cart->get_cart() as $item ) {
			if ( ! empty( $item['wps_ubo_offer_product'] ) ) {
				return $item;
			}
		}
		return null;
	}

	/**
	 * Check out the cart and return the bump order item.
	 */
	private function checkout_bump_item() {
		WC()->cart->calculate_totals();
		$order_id = WC()->checkout()->create_order(
			array(
				'payment_method' => 'cod',
				'billing_email'  => 'guest@example.org',
			)
		);
		foreach ( wc_get_order( $order_id )->get_items() as $item ) {
			if ( 'true' === $item->get_meta( 'is_order_bump_purchase' ) ) {
				return $item;
			}
		}
		return null;
	}

	/**
	 * Assert a string carries no markup or encoded delimiters.
	 *
	 * @param string $value Value.
	 */
	private function assert_inert( $value ) {
		$this->assertStringNotContainsString( '<', $value );
		$this->assertStringNotContainsString( '>', $value );
		$this->assertStringNotContainsString( '&quot;', $value );
		$this->assertStringNotContainsString( '&lt;', $value );
		$this->assertStringNotContainsString( '&gt;', $value );
	}

	/**
	 * Test: the researcher's payload is neutralised by the cleaner.
	 */
	public function test_clean_text_removes_spaced_tags_and_entities() {
		$clean = wps_ubo_lite_clean_meta_form_text( self::PAYLOAD );
		$this->assert_inert( $clean );
		$this->assertStringNotContainsString( 'img src', $clean );

		// Double-encoded entities are decoded all the way before stripping.
		$this->assert_inert( wps_ubo_lite_clean_meta_form_text( '&amp;lt;img src=x onerror=alert(1)&amp;gt;' ) );

		// Normal text is kept.
		$this->assertSame( 'Happy birthday, Tom & Jerry!', wps_ubo_lite_clean_meta_form_text( 'Happy birthday, Tom & Jerry!' ) );
		$this->assertSame( 'true', wps_ubo_lite_clean_meta_form_text( true ) );
		$this->assertSame( '', wps_ubo_lite_clean_meta_form_text( array( 'x' ) ) );
	}

	/**
	 * Test: only fields the bump defines are kept.
	 */
	public function test_sanitize_keeps_only_configured_fields() {
		$bump  = get_option( 'wps_ubo_bump_list' )[1];
		$clean = wps_ubo_lite_sanitize_bump_meta_form(
			array(
				array(
					'name'  => 'upsell_poc',
					'value' => 'attacker',
				),
				array(
					'name'  => 'Gift message',
					'value' => 'Hello',
				),
				array(
					'name'  => 'Gift message',
					'value' => 'second copy',
				),
				'not-an-array',
			),
			$bump
		);
		$this->assertSame(
			array(
				array(
					'name'  => 'Gift message',
					'value' => 'Hello',
				),
			),
			$clean
		);
	}

	/**
	 * Test: a bump without meta forms accepts no form data at all.
	 */
	public function test_bump_without_meta_forms_drops_form_data() {
		$this->make_bump( 'no' );
		( new Wps_Upsell_Order_Bump_Report_Sales_By_Bump( 1 ) )->add_offer_view_count();
		$r = $this->add_offer(
			array(
				array(
					'name'  => 'Gift message',
					'value' => 'Hello',
				),
			)
		);
		$this->assertStringContainsString( 'added', $r['raw'] );
		$item = $this->bump_cart_item();
		$this->assertNotNull( $item );
		$this->assertSame( array(), $item['wps_ubo_meta_form'] );
	}

	/**
	 * Test: the PoC flow ( guest add_offer_in_cart -> checkout ) stores no markup.
	 */
	public function test_poc_payload_does_not_reach_order_item_meta() {
		$this->add_offer(
			array(
				array(
					'name'  => 'upsell_poc',
					'value' => self::PAYLOAD,
				),
				array(
					'name'  => 'Gift message',
					'value' => self::PAYLOAD,
				),
			)
		);

		$cart_item = $this->bump_cart_item();
		$this->assertNotNull( $cart_item );
		$this->assertCount( 1, $cart_item['wps_ubo_meta_form'] );
		$this->assert_inert( $cart_item['wps_ubo_meta_form'][0]['value'] );

		$item = $this->checkout_bump_item();
		$this->assertNotNull( $item );
		$this->assertSame( '', (string) $item->get_meta( 'upsell_poc' ) );
		foreach ( $item->get_meta_data() as $meta ) {
			if ( is_scalar( $meta->value ) ) {
				$this->assert_inert( (string) $meta->value );
			}
		}
	}

	/**
	 * Test: a legitimate gift message still reaches the order.
	 */
	public function test_valid_meta_form_value_is_saved_on_order() {
		$this->add_offer(
			array(
				array(
					'name'  => 'Gift message',
					'value' => 'Happy birthday!',
				),
			)
		);
		$item = $this->checkout_bump_item();
		$this->assertNotNull( $item );
		$this->assertSame( 'Happy birthday!', $item->get_meta( 'Gift message' ) );
	}

	/**
	 * Test: a cart saved before the fix ( raw payload in session ) is re-checked at checkout.
	 */
	public function test_stale_cart_payload_is_cleaned_at_checkout() {
		$this->add_offer( array() );
		foreach ( WC()->cart->cart_contents as $key => $item ) {
			if ( ! empty( $item['wps_ubo_offer_product'] ) ) {
				WC()->cart->cart_contents[ $key ]['wps_ubo_meta_form'] = array(
					array(
						'name'  => 'upsell_poc',
						'value' => self::PAYLOAD,
					),
					array(
						'name'  => 'Gift message',
						'value' => self::PAYLOAD,
					),
				);
			}
		}
		$item = $this->checkout_bump_item();
		$this->assertNotNull( $item );
		$this->assertSame( '', (string) $item->get_meta( 'upsell_poc' ) );
		$this->assert_inert( (string) $item->get_meta( 'Gift message' ) );
	}

	/**
	 * Test: meta already stored on old orders is shown escaped in admin.
	 */
	public function test_existing_order_meta_is_displayed_escaped() {
		$this->add_offer( array() );
		$item = $this->checkout_bump_item();
		$this->assertNotNull( $item );
		$item->update_meta_data( 'upsell_poc', '<img src=x onerror=alert(1)>' . self::PAYLOAD );
		$item->save();

		add_filter( 'woocommerce_order_item_display_meta_value', array( $this->pub, 'escape_order_bump_meta_value' ), 10, 3 );

		$shown = null;
		foreach ( $item->get_formatted_meta_data( '_', true ) as $meta ) {
			if ( 'upsell_poc' === $meta->key ) {
				$shown = $meta->display_value;
			}
		}
		$this->assertNotNull( $shown );
		$rendered = wp_kses_post( force_balance_tags( $shown ) );
		$this->assertStringNotContainsString( '<img', $rendered );
		$this->assertStringNotContainsString( '< div', $rendered );
		$this->assertStringNotContainsString( 'onerror', $rendered );
	}

	/**
	 * Test: non-bump order items are left untouched by the display filter.
	 */
	public function test_display_filter_ignores_non_bump_items() {
		$item = new WC_Order_Item_Product();
		$meta = (object) array(
			'key'   => 'note',
			'value' => '<b>x</b>',
		);
		$this->assertSame( '<b>x</b>', $this->pub->escape_order_bump_meta_value( '<b>x</b>', $meta, $item ) );
	}
}
