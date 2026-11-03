<?php
/**
 * WPS-7890 Cart recovery end-to-end flow (server side): capture → abandon → emails →
 * recovery link → offer → order attribution, plus unsubscribe / lost / privacy.
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce/Tests
 */

/**
 * @covers Upsell_Order_Bump_Acr_Public
 * @covers Upsell_Order_Bump_Acr_Engine
 * @covers Upsell_Order_Bump_Acr_Mailer
 */
class AcrFlowTest extends WPS_UBO_Test_Case {

	/** @var Upsell_Order_Bump_Acr_Public */
	private $pub;

	/** @var WC_Product[] */
	private $p = array();

	public function set_up() {
		parent::set_up();
		delete_transient( Upsell_Order_Bump_Acr_Engine::LOCK );
		$this->pub      = new Upsell_Order_Bump_Acr_Public( '3.1.8' );
		$this->p['a']   = WPS_UBO_Test_Factory::simple_product( array( 'name' => 'Hoodie', 'regular_price' => '45' ) );
		$this->p['b']   = WPS_UBO_Test_Factory::simple_product( array( 'name' => 'T-Shirt', 'regular_price' => '18' ) );
		$this->p['off'] = WPS_UBO_Test_Factory::simple_product( array( 'name' => 'Beanie', 'regular_price' => '20' ) );
		$this->configure();
	}

	/**
	 * Enable the feature with a fixed offer product.
	 *
	 * @param array $overrides Raw overrides.
	 */
	private function configure( $overrides = array() ) {
		$raw = array_merge(
			Upsell_Order_Bump_Acr_Db::default_settings(),
			array(
				'enabled'        => 'on',
				'capture_guests' => 'on',
				'checkout_offer' => 'on',
				'offer_source'   => 'fixed',
				'offer_product'  => $this->p['off']->get_id(),
				'offer_discount' => 15,
				'exclude_roles'  => array( 'administrator', 'shop_manager' ),
			),
			$overrides
		);
		update_option( Upsell_Order_Bump_Acr_Db::SETTINGS_OPTION, Upsell_Order_Bump_Acr_Db::sanitize_settings( $raw ) );
	}

	private function nonce() {
		return wp_create_nonce( Upsell_Order_Bump_Acr_Public::NONCE_ACTION );
	}

	private function capture( $email, $name = '' ) {
		return $this->ajax_call(
			array( $this->pub, 'ajax_capture' ),
			array(
				'nonce'      => $this->nonce(),
				'email'      => $email,
				'first_name' => $name,
			)
		)['json'];
	}

	private function session_row() {
		return Upsell_Order_Bump_Acr_Db::get_by( 'session_key', (string) WC()->session->get_customer_id() );
	}

	private function age( $id, $column, $seconds ) {
		global $wpdb;
		$wpdb->update( Upsell_Order_Bump_Acr_Db::table(), array( $column => gmdate( 'Y-m-d H:i:s', time() - $seconds ) ), array( 'id' => $id ) );
	}

	/**
	 * Guest cart that has been abandoned and received all three emails.
	 *
	 * @return array Row.
	 */
	private function abandoned_with_all_emails() {
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		WC()->cart->add_to_cart( $this->p['b']->get_id() );
		$this->capture( 'Guest.One@example.org', 'Asha' );
		$id = (int) $this->session_row()['id'];
		$this->age( $id, 'updated_at', 40 * MINUTE_IN_SECONDS );
		Upsell_Order_Bump_Acr_Engine::process();
		for ( $i = 0; $i < 3; $i++ ) {
			$this->age( $id, 'next_email_at', 60 );
			Upsell_Order_Bump_Acr_Engine::process();
		}
		return Upsell_Order_Bump_Acr_Db::get_by( 'id', $id );
	}

	private function offer_line() {
		foreach ( WC()->cart->get_cart() as $item ) {
			if ( ! empty( $item[ Upsell_Order_Bump_Acr_Public::ITEM_KEY ] ) ) {
				return $item;
			}
		}
		return null;
	}

	/* ---------------------------------------------------------------- capture */

	public function test_guest_capture_saves_active_cart_with_lowercase_email() {
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		$r = $this->capture( 'Guest.One@Example.org', 'Asha' );
		$this->assertTrue( $r['data']['tracked'] );
		$row = $this->session_row();
		$this->assertSame( 'guest.one@example.org', $row['email'] );
		$this->assertSame( 'Asha', $row['first_name'] );
		$this->assertSame( 'active', $row['status'] );
		$this->assertSame( $this->p['a']->get_id(), (int) $row['cart'][0]['product_id'] );
	}

	public function test_invalid_email_not_tracked() {
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		$this->assertFalse( $this->capture( 'not-an-email' )['data']['tracked'] );
		$this->assertNull( $this->session_row() );
	}

	public function test_capture_rejects_bad_nonce() {
		$r = $this->ajax_call( array( $this->pub, 'ajax_capture' ), array( 'nonce' => 'bad', 'email' => 'x@example.org' ) );
		$this->assertSame( '-1', trim( $r['raw'] ) );
	}

	public function test_guests_not_captured_when_disabled_for_guests() {
		$this->configure( array( 'capture_guests' => 'off' ) );
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		$this->assertFalse( $this->capture( 'guest@example.org' )['data']['tracked'] );
	}

	public function test_nothing_captured_when_feature_off() {
		$this->configure( array( 'enabled' => 'off' ) );
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		$this->assertFalse( $this->capture( 'guest@example.org' )['data']['tracked'] );
	}

	public function test_cart_edit_updates_same_row() {
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		$this->capture( 'edit@example.org' );
		$id = (int) $this->session_row()['id'];
		WC()->cart->add_to_cart( $this->p['b']->get_id() );
		$this->pub->mark_dirty();
		$this->pub->maybe_sync();
		$row = Upsell_Order_Bump_Acr_Db::get_by( 'id', $id );
		$this->assertCount( 2, $row['cart'] );
		$this->assertGreaterThan( 0, (float) $row['cart_total'] );
	}

	public function test_name_not_blanked_by_later_sync_without_name() {
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		$this->capture( 'name@example.org', 'Ravi' );
		$this->capture( 'name@example.org', '' );
		$this->assertSame( 'Ravi', $this->session_row()['first_name'] );
	}

	public function test_excluded_role_not_tracked() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );
		self::reset_wc();
		$this->assertFalse( Upsell_Order_Bump_Acr_Public::should_track() );
	}

	public function test_logged_in_customer_tracked() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'customer', 'user_email' => 'cust@example.org' ) ) );
		self::reset_wc();
		$this->assertTrue( Upsell_Order_Bump_Acr_Public::should_track() );
	}

	public function test_should_track_filter() {
		add_filter( 'wps_ubo_acr_should_track', '__return_false' );
		$this->assertFalse( Upsell_Order_Bump_Acr_Public::should_track() );
		remove_filter( 'wps_ubo_acr_should_track', '__return_false' );
	}

	public function test_optout_stops_tracking_and_deletes_active_row() {
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		$this->capture( 'opt@example.org' );
		$this->assertNotNull( $this->session_row() );
		$this->ajax_call( array( $this->pub, 'ajax_optout' ), array( 'nonce' => $this->nonce() ) );
		$this->assertNull( $this->session_row() );
		$this->assertFalse( $this->capture( 'opt@example.org' )['data']['tracked'] );
	}

	/* ---------------------------------------------------------- engine / mail */

	public function test_process_does_nothing_when_disabled() {
		$this->configure( array( 'enabled' => 'off' ) );
		$this->assertSame( 0, array_sum( Upsell_Order_Bump_Acr_Engine::process() ) );
	}

	public function test_process_respects_lock() {
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		$this->capture( 'lock@example.org' );
		$this->age( (int) $this->session_row()['id'], 'updated_at', HOUR_IN_SECONDS );
		set_transient( Upsell_Order_Bump_Acr_Engine::LOCK, 1, 60 );
		Upsell_Order_Bump_Acr_Engine::process();
		$this->assertSame( 'active', $this->session_row()['status'] );
	}

	public function test_recent_cart_stays_active() {
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		$this->capture( 'fresh@example.org' );
		Upsell_Order_Bump_Acr_Engine::process();
		$this->assertSame( 'active', $this->session_row()['status'] );
	}

	public function test_email_sequence() {
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		$this->capture( 'seq@example.org', 'Asha' );
		$id = (int) $this->session_row()['id'];

		$this->age( $id, 'updated_at', 40 * MINUTE_IN_SECONDS );
		$c = Upsell_Order_Bump_Acr_Engine::process();
		$this->assertSame( 1, $c['abandoned'] );
		$row = Upsell_Order_Bump_Acr_Db::get_by( 'id', $id );
		$this->assertSame( 'abandoned', $row['status'] );
		$this->assertNotEmpty( $row['next_email_at'] );
		$this->assertCount( 0, $this->sent_mails(), 'First email waits for its delay.' );

		// Email 1: cart + restore link + unsubscribe, no offer.
		$this->age( $id, 'next_email_at', 60 );
		Upsell_Order_Bump_Acr_Engine::process();
		$m1 = $this->sent_mails()[0];
		$this->assertSame( 'seq@example.org', $m1['to'] );
		$this->assertStringContainsString( 'Hoodie', $m1['message'] );
		$this->assertStringContainsString( 'wps_ubo_acr=' . $row['token'], $m1['message'] );
		$this->assertStringContainsString( 'wps_ubo_acr_unsub', $m1['message'] );
		$this->assertStringNotContainsString( 'wps_ubo_acr_add=1', $m1['message'] );

		// Email 2: upsell offer with "Add to my order".
		$this->age( $id, 'next_email_at', 60 );
		Upsell_Order_Bump_Acr_Engine::process();
		$row = Upsell_Order_Bump_Acr_Db::get_by( 'id', $id );
		$m2  = $this->sent_mails()[1];
		$this->assertSame( $this->p['off']->get_id(), (int) $row['offer_product'] );
		$this->assertSame( 15, (int) $row['offer_discount'] );
		$this->assertSame( 'fixed', $row['offer_source'] );
		$this->assertStringContainsString( 'Beanie', $m2['message'] );
		$this->assertStringContainsString( 'wps_ubo_acr_add=1', $m2['message'] );

		// Email 3: single-use coupon locked to the email.
		$this->age( $id, 'next_email_at', 60 );
		Upsell_Order_Bump_Acr_Engine::process();
		$row    = Upsell_Order_Bump_Acr_Db::get_by( 'id', $id );
		$coupon = new WC_Coupon( $row['coupon_code'] );
		$this->assertGreaterThan( 0, $coupon->get_id() );
		$this->assertEquals( 10, $coupon->get_amount() );
		$this->assertSame( 'percent', $coupon->get_discount_type() );
		$this->assertSame( 1, $coupon->get_usage_limit() );
		$this->assertSame( array( 'seq@example.org' ), $coupon->get_email_restrictions() );
		$this->assertStringContainsStringIgnoringCase( $row['coupon_code'], $this->sent_mails()[2]['message'] );
		$this->assertSame( 3, (int) $row['emails_sent'] );
		$this->assertNull( $row['next_email_at'], 'Sequence finished.' );
	}

	public function test_disabled_steps_are_skipped() {
		$s                        = Upsell_Order_Bump_Acr_Db::sanitize_settings(
			array_merge(
				Upsell_Order_Bump_Acr_Db::default_settings(),
				array(
					'enabled'        => 'on',
					'capture_guests' => 'on',
					'offer_source'   => 'fixed',
					'offer_product'  => $this->p['off']->get_id(),
				)
			)
		);
		$s['steps'][0]['enabled'] = 'off';
		$s['steps'][1]['enabled'] = 'off';
		update_option( Upsell_Order_Bump_Acr_Db::SETTINGS_OPTION, $s );

		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		$this->capture( 'skip@example.org' );
		$id = (int) $this->session_row()['id'];
		$this->age( $id, 'updated_at', HOUR_IN_SECONDS );
		Upsell_Order_Bump_Acr_Engine::process();
		$this->age( $id, 'next_email_at', 60 );
		Upsell_Order_Bump_Acr_Engine::process();
		$this->assertCount( 1, $this->sent_mails() );
		$this->assertSame( $s['steps'][2]['subject'], $this->sent_mails()[0]['subject'] );
	}

	public function test_ensure_coupon_reuses_and_raises_amount() {
		$id   = Upsell_Order_Bump_Acr_Db::insert( array( 'session_key' => 'c1', 'email' => 'c@example.org', 'cart' => array() ) );
		$row  = Upsell_Order_Bump_Acr_Db::get_by( 'id', $id );
		$code = Upsell_Order_Bump_Acr_Engine::ensure_coupon( $row, 5 );
		$this->assertStringStartsWith( 'acr-', $code );
		$row   = Upsell_Order_Bump_Acr_Db::get_by( 'id', $id );
		$again = Upsell_Order_Bump_Acr_Engine::ensure_coupon( $row, 12 );
		$this->assertSame( $code, $again );
		$this->assertEquals( 12, ( new WC_Coupon( $code ) )->get_amount() );
		Upsell_Order_Bump_Acr_Engine::ensure_coupon( $row, 3 );
		$this->assertEquals( 12, ( new WC_Coupon( $code ) )->get_amount(), 'Amount never lowered.' );
	}

	public function test_ordering_without_link_marks_recovered_direct_and_stops_emails() {
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		$this->capture( 'direct@example.org' );
		$id = (int) $this->session_row()['id'];
		$this->age( $id, 'updated_at', HOUR_IN_SECONDS );
		Upsell_Order_Bump_Acr_Engine::process();

		WPS_UBO_Test_Factory::order( array( $this->p['a'] ), 'processing', array( 'email' => 'direct@example.org' ) );
		$this->age( $id, 'next_email_at', 60 );
		Upsell_Order_Bump_Acr_Engine::process();
		$row = Upsell_Order_Bump_Acr_Db::get_by( 'id', $id );
		$this->assertSame( 'recovered', $row['status'] );
		$this->assertSame( 'direct', $row['recovered_via'] );
		$this->assertCount( 0, $this->sent_mails() );
	}

	public function test_finished_sequence_becomes_lost() {
		$id = Upsell_Order_Bump_Acr_Db::insert(
			array(
				'session_key'   => 'lost',
				'email'         => 'lost@example.org',
				'cart'          => array( array( 'product_id' => $this->p['a']->get_id(), 'variation_id' => 0, 'quantity' => 1 ) ),
				'status'        => 'abandoned',
				'emails_sent'   => 3,
				'abandoned_at'  => Upsell_Order_Bump_Acr_Db::now( -20 * DAY_IN_SECONDS ),
				'last_email_at' => Upsell_Order_Bump_Acr_Db::now( -10 * DAY_IN_SECONDS ),
			)
		);
		Upsell_Order_Bump_Acr_Engine::process();
		$this->assertSame( 'lost', Upsell_Order_Bump_Acr_Db::get_by( 'id', $id )['status'] );
	}

	/* --------------------------------------------------------- offer sources */

	public function test_select_offer_fixed_skips_product_already_in_cart() {
		$row = array( 'cart' => array( array( 'product_id' => $this->p['off']->get_id(), 'variation_id' => 0 ) ), 'user_id' => 0 );
		$this->assertNull( Upsell_Order_Bump_Acr_Engine::select_offer( $row ) );
	}

	public function test_select_offer_from_matching_bump_uses_bump_percent() {
		$this->configure( array( 'offer_source' => 'bump' ) );
		WPS_UBO_Test_Factory::bump(
			$this->p['off']->get_id(),
			array(
				'wps_upsell_bump_target_ids'           => array( (string) $this->p['a']->get_id() ),
				'wps_upsell_bump_offer_discount_price' => '50',
			)
		);
		$offer = Upsell_Order_Bump_Acr_Engine::select_offer( array( 'cart' => array( array( 'product_id' => $this->p['a']->get_id(), 'variation_id' => 0 ) ), 'user_id' => 0 ) );
		$this->assertSame( $this->p['off']->get_id(), $offer['product_id'] );
		$this->assertSame( 'bump', $offer['source'] );
		$this->assertSame( 50, $offer['discount'] );

		// A step-level discount overrides the bump percent.
		$this->assertSame( 25, Upsell_Order_Bump_Acr_Engine::select_offer( array( 'cart' => array( array( 'product_id' => $this->p['a']->get_id(), 'variation_id' => 0 ) ), 'user_id' => 0 ), 25 )['discount'] );
	}

	public function test_select_offer_bump_ignores_non_matching_or_disabled() {
		$this->configure( array( 'offer_source' => 'bump' ) );
		WPS_UBO_Test_Factory::bump( $this->p['off']->get_id(), array( 'wps_upsell_bump_target_ids' => array( (string) $this->p['b']->get_id() ) ) );
		$cart = array( 'cart' => array( array( 'product_id' => $this->p['a']->get_id(), 'variation_id' => 0 ) ), 'user_id' => 0 );
		$this->assertNull( Upsell_Order_Bump_Acr_Engine::select_offer( $cart ) );

		WPS_UBO_Test_Factory::bump(
			$this->p['off']->get_id(),
			array(
				'wps_upsell_bump_status'      => 'no',
				'wps_ubo_offer_global_funnel' => 'yes',
			)
		);
		$this->assertNull( Upsell_Order_Bump_Acr_Engine::select_offer( $cart ) );
	}

	public function test_select_offer_skips_out_of_stock() {
		$this->p['off']->set_stock_status( 'outofstock' );
		$this->p['off']->save();
		$this->assertNull( Upsell_Order_Bump_Acr_Engine::select_offer( array( 'cart' => array(), 'user_id' => 0 ) ) );
	}

	public function test_select_offer_filter() {
		$pid    = $this->p['b']->get_id();
		$filter = static function () use ( $pid ) {
			return array( 'product_id' => $pid, 'discount' => 500, 'source' => 'custom' );
		};
		add_filter( 'wps_ubo_acr_offer', $filter );
		$offer = Upsell_Order_Bump_Acr_Engine::select_offer( array( 'cart' => array(), 'user_id' => 0 ) );
		remove_filter( 'wps_ubo_acr_offer', $filter );
		$this->assertSame( $pid, $offer['product_id'] );
		$this->assertSame( 90, $offer['discount'] );
	}

	/* ------------------------------------------------------- recovery links */

	public function test_recovery_link_restores_cart_offer_coupon_and_name() {
		$row = $this->abandoned_with_all_emails();
		self::reset_wc();
		WC()->cart->empty_cart();

		// Mail clients may upper-case the link; the token still matches.
		$loc = $this->redirect_of(
			array( $this->pub, 'handle_links' ),
			array(
				'wps_ubo_acr'     => strtoupper( $row['token'] ),
				'wps_ubo_acr_add' => '1',
			)
		);
		$this->assertSame( wc_get_checkout_url(), $loc );

		WC()->cart->calculate_totals();
		$ids = wp_list_pluck( WC()->cart->get_cart(), 'product_id' );
		$this->assertContains( $this->p['a']->get_id(), $ids );
		$this->assertContains( $this->p['b']->get_id(), $ids );

		$line = $this->offer_line();
		$this->assertNotNull( $line );
		$this->assertEqualsWithDelta( 17.0, (float) $line['data']->get_price(), 0.001, '20 at 15% off.' );
		$this->assertTrue( WC()->cart->has_discount( $row['coupon_code'] ) );

		$box = Upsell_Order_Bump_Acr_Public::offer_box_html();
		$this->assertStringContainsString( 'Asha', $box );
		$this->assertStringContainsString( 'checked', $box );
	}

	public function test_toggle_offer_removes_and_adds() {
		$row = $this->abandoned_with_all_emails();
		self::reset_wc();
		$this->redirect_of( array( $this->pub, 'handle_links' ), array( 'wps_ubo_acr' => $row['token'] ) );
		$this->assertNull( $this->offer_line(), 'Plain restore link does not add the offer.' );
		$this->assertStringNotContainsString( 'checked', Upsell_Order_Bump_Acr_Public::offer_box_html() );

		$this->ajax_call( array( $this->pub, 'ajax_toggle_offer' ), array( 'nonce' => $this->nonce(), 'add' => '1' ) );
		$this->assertNotNull( $this->offer_line() );
		$this->ajax_call( array( $this->pub, 'ajax_toggle_offer' ), array( 'nonce' => $this->nonce(), 'add' => '' ) );
		$this->assertNull( $this->offer_line() );
	}

	public function test_unknown_token_redirects_to_cart() {
		$this->assertSame( wc_get_cart_url(), $this->redirect_of( array( $this->pub, 'handle_links' ), array( 'wps_ubo_acr' => str_repeat( 'a', 32 ) ) ) );
	}

	public function test_no_token_does_nothing() {
		$this->assertSame( '', $this->redirect_of( array( $this->pub, 'handle_links' ), array() ) );
	}

	public function test_order_after_link_is_attributed_and_link_cannot_be_reused() {
		$row = $this->abandoned_with_all_emails();
		self::reset_wc();
		$this->redirect_of(
			array( $this->pub, 'handle_links' ),
			array(
				'wps_ubo_acr'     => $row['token'],
				'wps_ubo_acr_add' => '1',
			)
		);
		WC()->cart->calculate_totals();
		$order_id = WC()->checkout()->create_order(
			array(
				'payment_method'     => 'cod',
				'billing_email'      => 'guest.one@example.org',
				'billing_first_name' => 'Asha',
				'billing_country'    => 'US',
			)
		);
		$this->assertIsInt( $order_id );
		$order = wc_get_order( $order_id );
		$order->set_status( 'processing' );
		$order->save();
		$this->pub->on_order_classic( $order_id );

		$row = Upsell_Order_Bump_Acr_Db::get_by( 'id', $row['id'] );
		$this->assertSame( 'recovered', $row['status'] );
		$this->assertSame( 'link', $row['recovered_via'] );
		$this->assertSame( $order_id, (int) $row['order_id'] );
		$this->assertSame( 1, (int) $row['offer_accepted'] );
		$this->assertSame( (int) $row['id'], (int) wc_get_order( $order_id )->get_meta( '_wps_ubo_acr_recovered' ) );

		$meta = array();
		foreach ( wc_get_order( $order_id )->get_items() as $item ) {
			if ( $item->get_meta( '_wps_ubo_acr_offer' ) ) {
				$meta[] = $item->get_name();
			}
		}
		$this->assertSame( array( 'Beanie' ), $meta );

		$this->assertSame( wc_get_page_permalink( 'shop' ), $this->redirect_of( array( $this->pub, 'handle_links' ), array( 'wps_ubo_acr' => $row['token'] ) ) );
	}

	public function test_unsubscribe_requires_valid_signature() {
		$row = $this->abandoned_with_all_emails();
		Upsell_Order_Bump_Acr_Db::update( $row['id'], array( 'status' => 'abandoned' ) );

		$this->redirect_of(
			array( $this->pub, 'handle_links' ),
			array(
				'wps_ubo_acr_unsub' => $row['token'],
				'sig'               => 'forged',
			)
		);
		$this->assertSame( 'abandoned', Upsell_Order_Bump_Acr_Db::get_by( 'id', $row['id'] )['status'] );

		$loc = $this->redirect_of(
			array( $this->pub, 'handle_links' ),
			array(
				'wps_ubo_acr_unsub' => $row['token'],
				'sig'               => Upsell_Order_Bump_Acr_Mailer::unsubscribe_sig( $row['token'] ),
			)
		);
		$this->assertSame( wc_get_page_permalink( 'shop' ), $loc );
		$this->assertSame( 'unsubscribed', Upsell_Order_Bump_Acr_Db::get_by( 'id', $row['id'] )['status'] );
		$this->assertTrue( Upsell_Order_Bump_Acr_Db::is_unsubscribed( 'guest.one@example.org' ) );
	}

	public function test_unsubscribed_email_gets_no_new_emails() {
		Upsell_Order_Bump_Acr_Db::insert( array( 'session_key' => 'old', 'email' => 'quiet@example.org', 'cart' => array(), 'status' => 'unsubscribed' ) );
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		$this->capture( 'quiet@example.org' );
		$row = $this->session_row();
		if ( $row ) {
			$this->age( (int) $row['id'], 'updated_at', HOUR_IN_SECONDS );
			Upsell_Order_Bump_Acr_Engine::process();
			$this->age( (int) $row['id'], 'next_email_at', 60 );
			Upsell_Order_Bump_Acr_Engine::process();
		}
		$this->assertCount( 0, $this->sent_mails() );
	}

	/* --------------------------------------------------------------- privacy */

	public function test_privacy_exporter_and_eraser() {
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		$this->capture( 'privacy@example.org' );
		$this->assertArrayHasKey( 'wps-ubo-acr', Upsell_Order_Bump_Acr_Public::register_exporter( array() ) );
		$this->assertArrayHasKey( 'wps-ubo-acr', Upsell_Order_Bump_Acr_Public::register_eraser( array() ) );

		$export = Upsell_Order_Bump_Acr_Public::export( 'privacy@example.org' );
		$this->assertCount( 1, $export['data'] );
		$this->assertTrue( $export['done'] );

		$erase = Upsell_Order_Bump_Acr_Public::erase( 'privacy@example.org' );
		$this->assertTrue( (bool) $erase['items_removed'] );
		$this->assertNull( $this->session_row() );
		$this->assertCount( 0, Upsell_Order_Bump_Acr_Public::export( 'privacy@example.org' )['data'] );
	}

	public function test_feature_does_not_touch_legacy_abandoned_cart_option() {
		update_option( 'wps_ubo_global_options', array( 'wps_ubo_abandoned_cart' => 'yes' ) );
		$this->abandoned_with_all_emails();
		$this->assertSame( array( 'wps_ubo_abandoned_cart' => 'yes' ), get_option( 'wps_ubo_global_options' ) );
	}
}
