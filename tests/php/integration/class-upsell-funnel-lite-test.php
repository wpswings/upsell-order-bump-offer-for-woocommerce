<?php
/**
 * Free one-click upsell funnels: which orders start a funnel after the (block) checkout
 * and where the customer is sent.
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce/Tests
 */

/**
 * Upsell Funnel Lite tests.
 *
 * @covers Upsell_Order_Bump_Offer_For_Woocommerce_Public
 */
class Upsell_Funnel_Lite_Test extends WPS_UBO_Test_Case {

	const OFFER_URL = 'https://example.org/special-offer/';

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
	 * Last order.
	 *
	 * @var WC_Order Order placed by the last checkout().
	 */
	private $last_order;

	/**
	 * Set up the test fixture.
	 */
	public function set_up() {
		parent::set_up();
		wp_set_current_user( 0 );
		$this->pub    = new Upsell_Order_Bump_Offer_For_Woocommerce_Public( 'upsell-order-bump-offer-for-woocommerce', UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_VERSION );
		$this->target = WPS_UBO_Test_Factory::simple_product(
			array(
				'name' => 'Kayak',
				'regular_price' => '400',
			)
		);
		$this->offer  = WPS_UBO_Test_Factory::simple_product(
			array(
				'name' => 'Paddle',
				'regular_price' => '60',
			)
		);
		delete_option( 'wps_wocuf_funnels_list' );
		delete_option( 'wps_upsell_lite_global_options' );
	}

	/**
	 * Tear down the test fixture.
	 */
	public function tear_down() {
		$_GET = array();
		delete_option( 'wps_wocuf_funnels_list' );
		delete_option( 'wps_upsell_lite_global_options' );
		parent::tear_down();
	}

	/**
	 * Save a free funnel ( shape saved by the free funnel editor ).
	 *
	 * @param array $overrides Funnel keys.
	 * @param int   $funnel_id Funnel id.
	 */
	private function funnel( $overrides = array(), $funnel_id = 1 ) {
		$list               = (array) get_option( 'wps_wocuf_funnels_list', array() );
		$list[ $funnel_id ] = array_merge(
			array(
				'wps_wocuf_funnel_id'             => (string) $funnel_id,
				'wps_wocuf_funnel_name'           => 'Funnel ' . $funnel_id,
				'wps_upsell_funnel_status'        => 'yes',
				'wps_upsell_fsav3'                => 'true',
				'wps_wocuf_global_funnel'         => 'no',
				'wps_wocuf_pro_funnel_schedule'   => array( '7' ),
				'wps_wocuf_target_pro_ids'        => array( (string) $this->target->get_id() ),
				'target_categories_ids'           => array(),
				'wps_wocuf_products_in_offer'     => array( 1 => (string) $this->offer->get_id() ),
				'wps_wocuf_offer_discount_price'  => array( 1 => '50%' ),
				'wps_wocuf_applied_offer_number'  => array( 1 => '1' ),
				'wps_wocuf_pro_offer_template'    => array( 1 => 'custom' ),
				'wps_wocuf_offer_custom_page_url' => array( 1 => self::OFFER_URL ),
				'wps_upsell_post_id_assigned'     => array(),
			),
			$overrides
		);
		update_option( 'wps_wocuf_funnels_list', $list );
	}

	/**
	 * Place an order through the block checkout hook and return the stored offer link.
	 *
	 * @param WC_Product[] $products Products.
	 * @param array        $args     payment_method, email, customer_id.
	 * @return string Offer link ( '' when no funnel started ).
	 */
	private function checkout( $products, $args = array() ) {
		$args  = wp_parse_args(
			$args,
			array(
				'payment_method' => 'cod',
				'email'          => 'paddler@example.org',
			)
		);
		$order = WPS_UBO_Test_Factory::order( $products, 'pending', $args );
		if ( ! empty( $args['payment_method'] ) ) {
			$order->set_payment_method( $args['payment_method'] );
		}
		if ( ! empty( $args['customer_id'] ) ) {
			$order->set_customer_id( $args['customer_id'] );
		}
		$order->save();
		$this->pub->wps_wocuf_initate_upsell_orders_api_checkout_org( $order );
		$this->last_order = wc_get_order( $order->get_id() );
		return (string) $this->last_order->get_meta( 'wps_wocfo_upsell_funnel_redirection_link_org' );
	}

	/**
	 * Test: Target starts the funnel on the offer page.
	 */
	public function test_target_starts_the_funnel_on_the_offer_page() {
		$this->funnel();
		$link = $this->checkout( array( $this->target ) );
		$this->assertStringStartsWith( self::OFFER_URL . '?', $link );
		parse_str( (string) wp_parse_url( $link, PHP_URL_QUERY ), $query );
		$this->assertSame( '1', (string) $query['ocuf_fid'] );
		$this->assertSame( '1', (string) $query['ocuf_ofd'] );
		$this->assertSame( $this->last_order->get_order_key(), $query['ocuf_ok'] );
		$this->assertNotEmpty( $query['ocuf_ns'] );

		$this->assertSame( 'block_checkout', $this->last_order->get_meta( '_wps_wocfo_org_checkout_through_block' ) );
		$this->assertSame( 'true', $this->last_order->get_meta( 'wps_upsell_order_started' ) );
		$this->assertSame( '1', (string) $this->last_order->get_meta( 'wps_upsell_funnel_id' ) );
	}

	/**
	 * Test: Funnel stats count trigger and view.
	 */
	public function test_funnel_stats_count_trigger_and_view() {
		$this->funnel();
		$this->checkout( array( $this->target ) );
		$funnels = get_option( 'wps_wocuf_funnels_list' );
		$this->assertSame( 1, (int) $funnels[1]['funnel_triggered_count'] );
		$this->assertSame( 1, (int) $funnels[1]['offers_view_count'] );
	}

	/**
	 * Test: Block checkout thank you URL points to the offer.
	 */
	public function test_block_checkout_thank_you_url_points_to_the_offer() {
		$this->funnel();
		$link = $this->checkout( array( $this->target ) );
		$this->assertSame( $link, apply_filters( 'woocommerce_get_checkout_order_received_url', 'https://example.org/thanks/', $this->last_order ) );
	}

	/**
	 * Test: No funnel without target or funnels.
	 */
	public function test_no_funnel_without_target_or_funnels() {
		$this->assertSame( '', $this->checkout( array( $this->target ) ), 'No funnels.' );
		$this->funnel();
		$this->assertSame( '', $this->checkout( array( WPS_UBO_Test_Factory::simple_product() ) ) );
	}

	/**
	 * Test: Free funnels only run for cash on delivery.
	 */
	public function test_free_funnels_only_run_for_cash_on_delivery() {
		$this->funnel();
		$this->assertSame( array( 'cod', 'stripe_cc', '' ), wps_upsell_lite_supported_gateways_funnel_builder() );
		$this->assertSame( '', $this->checkout( array( $this->target ), array( 'payment_method' => 'bacs' ) ) );
		$this->assertNotSame( '', $this->checkout( array( $this->target ), array( 'payment_method' => 'cod' ) ) );
	}

	/**
	 * Test: Sandbox funnel only for admins.
	 */
	public function test_sandbox_funnel_only_for_admins() {
		$this->funnel( array( 'wps_upsell_funnel_status' => 'no' ) );
		$this->assertSame( '', $this->checkout( array( $this->target ) ) );
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );
		$this->assertNotSame( '', $this->checkout( array( $this->target ) ) );
	}

	/**
	 * Test: Category and global funnels.
	 */
	public function test_category_and_global_funnels() {
		$cat    = WPS_UBO_Test_Factory::category();
		$in_cat = WPS_UBO_Test_Factory::simple_product( array( 'category_ids' => array( $cat ) ) );
		$this->funnel(
			array(
				'wps_wocuf_target_pro_ids' => array(),
				'target_categories_ids'    => array( $cat ),
			)
		);
		$this->assertNotSame( '', $this->checkout( array( $in_cat ) ) );
		$this->assertSame( '', $this->checkout( array( WPS_UBO_Test_Factory::simple_product() ) ) );

		$this->funnel(
			array(
				'wps_wocuf_target_pro_ids' => array(),
				'wps_wocuf_global_funnel'  => 'yes',
			),
			2
		);
		$this->assertStringContainsString( 'ocuf_fid=2', $this->checkout( array( WPS_UBO_Test_Factory::simple_product() ) ) );
	}

	/**
	 * Test: Skipped when offer already in order or out of stock.
	 */
	public function test_skipped_when_offer_already_in_order_or_out_of_stock() {
		$this->funnel();
		$this->assertSame( '', $this->checkout( array( $this->target, $this->offer ) ) );

		update_option( 'wps_upsell_lite_global_options', array( 'skip_similar_offer' => 'no' ) );
		$this->assertNotSame( '', $this->checkout( array( $this->target, $this->offer ) ) );

		$this->offer->set_stock_status( 'outofstock' );
		$this->offer->save();
		$this->assertSame( '', $this->checkout( array( $this->target ) ) );
	}

	/**
	 * Test: Exclusive offer once per email.
	 */
	public function test_exclusive_offer_once_per_email() {
		$this->funnel( array( 'wps_wocuf_exclusive_offer' => 'yes' ) );
		$this->assertNotSame( '', $this->checkout( array( $this->target ), array( 'email' => 'one@example.org' ) ) );
		$this->assertSame( '', $this->checkout( array( $this->target ), array( 'email' => 'one@example.org' ) ) );
		$this->assertNotSame( '', $this->checkout( array( $this->target ), array( 'email' => 'two@example.org' ) ) );
	}

	/**
	 * Test: Schedule limits the weekday.
	 */
	public function test_schedule_limits_the_weekday() {
		$tomorrow = (string) ( ( (int) gmdate( 'w' ) + 1 ) % 7 );
		$this->funnel( array( 'wps_wocuf_pro_funnel_schedule' => array( $tomorrow ) ) );
		$this->assertSame( '', $this->checkout( array( $this->target ) ) );
		$this->funnel( array( 'wps_wocuf_pro_funnel_schedule' => array( (string) gmdate( 'w' ) ) ) );
		$this->assertNotSame( '', $this->checkout( array( $this->target ) ) );
	}

	/**
	 * Test: Smart skip for offers bought before.
	 */
	public function test_smart_skip_for_offers_bought_before() {
		$user = self::factory()->user->create( array( 'role' => 'customer' ) );
		wp_set_current_user( $user );
		$past = WPS_UBO_Test_Factory::order( array( $this->offer ), 'completed' );
		$past->set_customer_id( $user );
		$past->save();
		$this->funnel();
		$this->assertNotSame( '', $this->checkout( array( $this->target ), array( 'customer_id' => $user ) ), 'Off by default.' );

		update_option( 'wps_upsell_lite_global_options', array( 'smart_skip_if_purchased' => 'yes' ) );
		$this->assertSame( '', $this->checkout( array( $this->target ), array( 'customer_id' => $user ) ) );
	}

	/**
	 * Test: Smart offer upgrade remembers the target item.
	 */
	public function test_smart_offer_upgrade_remembers_the_target_item() {
		$this->funnel( array( 'wps_wocuf_smart_offer_upgrade' => 'yes' ) );
		$this->checkout( array( $this->target ) );
		$item_ids = array_keys( $this->last_order->get_items() );
		$this->assertSame( (string) $item_ids[0], (string) $this->last_order->get_meta( '__smart_offer_upgrade_target_key' ) );
	}

	/**
	 * Test: Offer price from funnel discount.
	 */
	public function test_offer_price_from_funnel_discount() {
		$product = WPS_UBO_Test_Factory::simple_product( array( 'regular_price' => '60' ) );
		$this->assertEqualsWithDelta( 30.0, (float) $this->pub->wps_wocuf_pro_change_offered_product_price( $product, '50%' )->get_price(), 0.001 );
		$product = WPS_UBO_Test_Factory::simple_product( array( 'regular_price' => '60' ) );
		$this->assertEqualsWithDelta( 45.0, (float) $this->pub->wps_wocuf_pro_change_offered_product_price( $product, '45' )->get_price(), 0.001 );
	}

	/* ------------------------------------------------- live offer request gate */

	/**
	 * Start a funnel and put its offer link params in $_GET.
	 *
	 * @return array Query params of the real offer link.
	 */
	private function open_offer_link() {
		$this->funnel();
		$this->funnel( array(), 2 );
		parse_str( (string) wp_parse_url( $this->checkout( array( $this->target ) ), PHP_URL_QUERY ), $query );
		$_GET = $query;
		return $query;
	}

	/**
	 * Test: Real offer link is a live offer.
	 */
	public function test_real_offer_link_is_a_live_offer() {
		$this->open_offer_link();
		$this->assertSame( 'live_offer', $this->pub->validate_shortcode() );
	}

	/**
	 * Test: Offer link for another funnel is not live.
	 */
	public function test_offer_link_for_another_funnel_is_not_live() {
		$this->open_offer_link();
		$_GET['ocuf_fid'] = '2'; // Exists, but not the funnel started for this order.
		$this->assertFalse( $this->pub->validate_shortcode() );
	}

	/**
	 * Test: Offer link with unknown offer is not live.
	 */
	public function test_offer_link_with_unknown_offer_is_not_live() {
		$this->open_offer_link();
		$_GET['ocuf_ofd'] = '1;alert(document.domain)//';
		$this->assertFalse( $this->pub->validate_shortcode() );
	}

	/**
	 * Test: Offer link with injected nonce is not live.
	 */
	public function test_offer_link_with_injected_nonce_is_not_live() {
		$this->open_offer_link();
		$_GET['ocuf_ns'] = '" autofocus onfocus=alert(document.domain) x="';
		$this->assertFalse( $this->pub->validate_shortcode() );
		$this->assertStringNotContainsString( 'onfocus', (string) $this->pub->no_thanks_shortcode_content() );
		$this->assertStringNotContainsString( 'onfocus', (string) $this->pub->buy_now_shortcode_content() );
	}

	/**
	 * Test: Offer link for unknown order is not live.
	 */
	public function test_offer_link_for_unknown_order_is_not_live() {
		$this->open_offer_link();
		$_GET['ocuf_ok'] = 'wc_order_not_a_real_key';
		$this->assertFalse( $this->pub->validate_shortcode() );
	}
}
