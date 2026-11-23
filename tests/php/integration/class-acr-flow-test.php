<?php
/**
 * WPS-7890 Cart recovery end-to-end flow (server side): capture → abandon → emails →
 * recovery link → offer → order attribution, plus unsubscribe / lost / privacy.
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce/Tests
 */

/**
 * ACR Flow tests.
 *
 * @covers Upsell_Order_Bump_Acr_Public
 * @covers Upsell_Order_Bump_Acr_Engine
 * @covers Upsell_Order_Bump_Acr_Mailer
 */
class Acr_Flow_Test extends WPS_UBO_Test_Case {

	/**
	 * Pub.
	 *
	 * @var Upsell_Order_Bump_Acr_Public
	 */
	private $pub;

	/**
	 * P.
	 *
	 * @var WC_Product[]
	 */
	private $p = array();

	/**
	 * Set up the test fixture.
	 */
	public function set_up() {
		parent::set_up();
		delete_transient( Upsell_Order_Bump_Acr_Engine::LOCK );
		$this->pub      = new Upsell_Order_Bump_Acr_Public( '3.1.8' );
		$this->p['a']   = WPS_UBO_Test_Factory::simple_product(
			array(
				'name' => 'Hoodie',
				'regular_price' => '45',
			)
		);
		$this->p['b']   = WPS_UBO_Test_Factory::simple_product(
			array(
				'name' => 'T-Shirt',
				'regular_price' => '18',
			)
		);
		$this->p['off'] = WPS_UBO_Test_Factory::simple_product(
			array(
				'name' => 'Beanie',
				'regular_price' => '20',
			)
		);
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

	/**
	 * Nonce.
	 */
	private function nonce() {
		return wp_create_nonce( Upsell_Order_Bump_Acr_Public::NONCE_ACTION );
	}

	/**
	 * Capture nonce, bound to the current WC session ( QA C10 ).
	 *
	 * @return string
	 */
	private function capture_nonce() {
		$action = Upsell_Order_Bump_Acr_Public::CAPTURE_NONCE_ACTION;
		if ( WC()->session->has_session() ) {
			$action .= '|' . WC()->session->get_customer_id();
		}
		return wp_create_nonce( $action );
	}

	/**
	 * Capture.
	 *
	 * @param mixed  $email Email.
	 * @param string $name Name.
	 */
	private function capture( $email, $name = '' ) {
		// A real shopper's browser has the WooCommerce session cookie once the cart has items;
		// capture refuses sessionless requests ( QA C10 ).
		if ( ! WC()->session->has_session() ) {
			WC()->session->set_customer_session_cookie( true );
		}
		return $this->ajax_call(
			array( $this->pub, 'ajax_capture' ),
			array(
				'nonce'      => $this->capture_nonce(),
				'email'      => $email,
				'first_name' => $name,
			)
		)['json'];
	}

	/**
	 * Session row.
	 */
	private function session_row() {
		return Upsell_Order_Bump_Acr_Db::get_by( 'session_key', (string) WC()->session->get_customer_id() );
	}

	/**
	 * Age.
	 *
	 * @param mixed $id ID.
	 * @param mixed $column Column.
	 * @param mixed $seconds Seconds.
	 */
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

	/**
	 * Offer line.
	 */
	private function offer_line() {
		foreach ( WC()->cart->get_cart() as $item ) {
			if ( ! empty( $item[ Upsell_Order_Bump_Acr_Public::ITEM_KEY ] ) ) {
				return $item;
			}
		}
		return null;
	}

	/* ---------------------------------------------------------------- capture */

	/**
	 * Test: Guest capture saves active cart with lowercase email.
	 */
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

	/**
	 * Test: Invalid email not tracked.
	 */
	public function test_invalid_email_not_tracked() {
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		$this->assertFalse( $this->capture( 'not-an-email' )['data']['tracked'] );
		$this->assertNull( $this->session_row() );
	}

	/**
	 * Test: Capture rejects bad nonce.
	 */
	public function test_capture_rejects_bad_nonce() {
		$r = $this->ajax_call(
			array( $this->pub, 'ajax_capture' ),
			array(
				'nonce' => 'bad',
				'email' => 'x@example.org',
			)
		);
		$this->assertSame( '-1', trim( $r['raw'] ) );
	}

	/**
	 * Test: QA C10 capture rejects nonce from another session.
	 */
	public function test_qa_c10_capture_rejects_nonce_from_another_session() {
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		WC()->session->set_customer_session_cookie( true );
		$other = wp_create_nonce( Upsell_Order_Bump_Acr_Public::CAPTURE_NONCE_ACTION . '|another-session' );
		$r     = $this->ajax_call(
			array( $this->pub, 'ajax_capture' ),
			array(
				'nonce' => $other,
				'email' => 'x@example.org',
			)
		);
		$this->assertSame( '-1', trim( $r['raw'] ) );
		$this->assertNull( $this->session_row() );
	}

	/**
	 * Test: Guests not captured when disabled for guests.
	 */
	public function test_guests_not_captured_when_disabled_for_guests() {
		$this->configure( array( 'capture_guests' => 'off' ) );
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		$this->assertFalse( $this->capture( 'guest@example.org' )['data']['tracked'] );
	}

	/**
	 * Test: Nothing captured when feature off.
	 */
	public function test_nothing_captured_when_feature_off() {
		$this->configure( array( 'enabled' => 'off' ) );
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		$this->assertFalse( $this->capture( 'guest@example.org' )['data']['tracked'] );
	}

	/**
	 * Test: Cart edit updates same row.
	 */
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

	/**
	 * Test: Name not blanked by later sync without name.
	 */
	public function test_name_not_blanked_by_later_sync_without_name() {
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		$this->capture( 'name@example.org', 'Ravi' );
		$this->capture( 'name@example.org', '' );
		$this->assertSame( 'Ravi', $this->session_row()['first_name'] );
	}

	/**
	 * Test: Excluded role not tracked.
	 */
	public function test_excluded_role_not_tracked() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );
		self::reset_wc();
		$this->assertFalse( Upsell_Order_Bump_Acr_Public::should_track() );
	}

	/**
	 * Test: Logged in customer tracked.
	 */
	public function test_logged_in_customer_tracked() {
		wp_set_current_user(
			self::factory()->user->create(
				array(
					'role' => 'customer',
					'user_email' => 'cust@example.org',
				)
			)
		);
		self::reset_wc();
		$this->assertTrue( Upsell_Order_Bump_Acr_Public::should_track() );
	}

	/**
	 * Test: Should track filter.
	 */
	public function test_should_track_filter() {
		add_filter( 'wps_ubo_acr_should_track', '__return_false' );
		$this->assertFalse( Upsell_Order_Bump_Acr_Public::should_track() );
		remove_filter( 'wps_ubo_acr_should_track', '__return_false' );
	}

	/**
	 * Test: Optout stops tracking and deletes active row.
	 */
	public function test_optout_stops_tracking_and_deletes_active_row() {
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		$this->capture( 'opt@example.org' );
		$this->assertNotNull( $this->session_row() );
		$this->ajax_call( array( $this->pub, 'ajax_optout' ), array( 'nonce' => $this->nonce() ) );
		$this->assertNull( $this->session_row() );
		$this->assertFalse( $this->capture( 'opt@example.org' )['data']['tracked'] );
	}

	/* ---------------------------------------------------------- engine / mail */

	/**
	 * Test: Process does nothing when disabled.
	 */
	public function test_process_does_nothing_when_disabled() {
		$this->configure( array( 'enabled' => 'off' ) );
		$this->assertSame( 0, array_sum( Upsell_Order_Bump_Acr_Engine::process() ) );
	}

	/**
	 * Test: Process respects lock.
	 */
	public function test_process_respects_lock() {
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		$this->capture( 'lock@example.org' );
		$this->age( (int) $this->session_row()['id'], 'updated_at', HOUR_IN_SECONDS );
		set_transient( Upsell_Order_Bump_Acr_Engine::LOCK, 1, 60 );
		Upsell_Order_Bump_Acr_Engine::process();
		$this->assertSame( 'active', $this->session_row()['status'] );
	}

	/**
	 * Test: Recent cart stays active.
	 */
	public function test_recent_cart_stays_active() {
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		$this->capture( 'fresh@example.org' );
		Upsell_Order_Bump_Acr_Engine::process();
		$this->assertSame( 'active', $this->session_row()['status'] );
	}

	/**
	 * Test: Email sequence.
	 */
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

	/**
	 * Test: Disabled steps are skipped.
	 */
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

	/**
	 * Test: Ensure coupon reuses and raises amount.
	 */
	public function test_ensure_coupon_reuses_and_raises_amount() {
		$id   = Upsell_Order_Bump_Acr_Db::insert(
			array(
				'session_key' => 'c1',
				'email' => 'c@example.org',
				'cart' => array(),
			)
		);
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

	/**
	 * Test: Ordering without link marks recovered direct and stops emails.
	 */
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

	/**
	 * Test: Finished sequence becomes lost.
	 */
	public function test_finished_sequence_becomes_lost() {
		$id = Upsell_Order_Bump_Acr_Db::insert(
			array(
				'session_key'   => 'lost',
				'email'         => 'lost@example.org',
				'cart'          => array(
					array(
						'product_id' => $this->p['a']->get_id(),
						'variation_id' => 0,
						'quantity' => 1,
					),
				),
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

	/**
	 * Test: Select offer fixed skips product already in cart.
	 */
	public function test_select_offer_fixed_skips_product_already_in_cart() {
		$row = array(
			'cart' => array(
				array(
					'product_id' => $this->p['off']->get_id(),
					'variation_id' => 0,
				),
			),
			'user_id' => 0,
		);
		$this->assertNull( Upsell_Order_Bump_Acr_Engine::select_offer( $row ) );
	}

	/**
	 * Test: Select offer from matching bump uses bump percent.
	 */
	public function test_select_offer_from_matching_bump_uses_bump_percent() {
		$this->configure( array( 'offer_source' => 'bump' ) );
		WPS_UBO_Test_Factory::bump(
			$this->p['off']->get_id(),
			array(
				'wps_upsell_bump_target_ids'           => array( (string) $this->p['a']->get_id() ),
				'wps_upsell_bump_offer_discount_price' => '50',
			)
		);
		$offer = Upsell_Order_Bump_Acr_Engine::select_offer(
			array(
				'cart' => array(
					array(
						'product_id' => $this->p['a']->get_id(),
						'variation_id' => 0,
					),
				),
				'user_id' => 0,
			)
		);
		$this->assertSame( $this->p['off']->get_id(), $offer['product_id'] );
		$this->assertSame( 'bump', $offer['source'] );
		$this->assertSame( 50, $offer['discount'] );

		// A step-level discount overrides the bump percent.
		$this->assertSame(
			25,
			Upsell_Order_Bump_Acr_Engine::select_offer(
				array(
					'cart' => array(
						array(
							'product_id' => $this->p['a']->get_id(),
							'variation_id' => 0,
						),
					),
					'user_id' => 0,
				),
				25
			)['discount']
		);
	}

	/**
	 * Test: Select offer bump ignores non matching or disabled.
	 */
	public function test_select_offer_bump_ignores_non_matching_or_disabled() {
		$this->configure( array( 'offer_source' => 'bump' ) );
		WPS_UBO_Test_Factory::bump( $this->p['off']->get_id(), array( 'wps_upsell_bump_target_ids' => array( (string) $this->p['b']->get_id() ) ) );
		$cart = array(
			'cart' => array(
				array(
					'product_id' => $this->p['a']->get_id(),
					'variation_id' => 0,
				),
			),
			'user_id' => 0,
		);
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

	/**
	 * Test: Select offer skips out of stock.
	 */
	public function test_select_offer_skips_out_of_stock() {
		$this->p['off']->set_stock_status( 'outofstock' );
		$this->p['off']->save();
		$this->assertNull(
			Upsell_Order_Bump_Acr_Engine::select_offer(
				array(
					'cart' => array(),
					'user_id' => 0,
				)
			)
		);
	}

	/**
	 * Test: Select offer filter.
	 */
	public function test_select_offer_filter() {
		$pid    = $this->p['b']->get_id();
		$filter = static function () use ( $pid ) {
			return array(
				'product_id' => $pid,
				'discount' => 500,
				'source' => 'custom',
			);
		};
		add_filter( 'wps_ubo_acr_offer', $filter );
		$offer = Upsell_Order_Bump_Acr_Engine::select_offer(
			array(
				'cart' => array(),
				'user_id' => 0,
			)
		);
		remove_filter( 'wps_ubo_acr_offer', $filter );
		$this->assertSame( $pid, $offer['product_id'] );
		$this->assertSame( 90, $offer['discount'] );
	}

	/* ------------------------------------------------------- recovery links */

	/**
	 * Test: Recovery link restores cart offer coupon and name.
	 */
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

	/**
	 * Test: Toggle offer removes and adds.
	 */
	public function test_toggle_offer_removes_and_adds() {
		$row = $this->abandoned_with_all_emails();
		self::reset_wc();
		$this->redirect_of( array( $this->pub, 'handle_links' ), array( 'wps_ubo_acr' => $row['token'] ) );
		$this->assertNull( $this->offer_line(), 'Plain restore link does not add the offer.' );
		$this->assertStringNotContainsString( 'checked', Upsell_Order_Bump_Acr_Public::offer_box_html() );

		$this->ajax_call(
			array( $this->pub, 'ajax_toggle_offer' ),
			array(
				'nonce' => $this->nonce(),
				'add' => '1',
			)
		);
		$this->assertNotNull( $this->offer_line() );
		$this->ajax_call(
			array( $this->pub, 'ajax_toggle_offer' ),
			array(
				'nonce' => $this->nonce(),
				'add' => '',
			)
		);
		$this->assertNull( $this->offer_line() );
	}

	/**
	 * Test: Unknown token redirects to cart.
	 */
	public function test_unknown_token_redirects_to_cart() {
		$this->assertSame( wc_get_cart_url(), $this->redirect_of( array( $this->pub, 'handle_links' ), array( 'wps_ubo_acr' => str_repeat( 'a', 32 ) ) ) );
	}

	/**
	 * Test: No token does nothing.
	 */
	public function test_no_token_does_nothing() {
		$this->assertSame( '', $this->redirect_of( array( $this->pub, 'handle_links' ), array() ) );
	}

	/**
	 * Test: Order after link is attributed and link cannot be reused.
	 */
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
		// Order created ( before payment ): linked, but not yet recovered ( QA C3 ).
		$this->pub->on_order_classic( $order_id );
		$this->assertSame( 'abandoned', Upsell_Order_Bump_Acr_Db::get_by( 'id', $row['id'] )['status'] );
		$this->assertSame( (int) $row['id'], (int) wc_get_order( $order_id )->get_meta( '_wps_ubo_acr_cart_id' ) );

		// Payment goes through.
		$order = wc_get_order( $order_id );
		$order->set_status( 'processing' );
		$order->save();

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

	/**
	 * Test: Unsubscribe requires valid signature.
	 */
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

	/**
	 * Test: Unsubscribed email gets no new emails.
	 */
	public function test_unsubscribed_email_gets_no_new_emails() {
		Upsell_Order_Bump_Acr_Db::insert(
			array(
				'session_key' => 'old',
				'email' => 'quiet@example.org',
				'cart' => array(),
				'status' => 'unsubscribed',
			)
		);
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

	/* ------------------------------------------------------- QA WPS-7890 */

	/**
	 * Restore a row's cart through its recovery link ( with the offer ).
	 *
	 * @param array $row Row.
	 */
	private function open_link( $row ) {
		self::reset_wc();
		$this->redirect_of(
			array( $this->pub, 'handle_links' ),
			array(
				'wps_ubo_acr'     => $row['token'],
				'wps_ubo_acr_add' => '1',
			)
		);
	}

	/**
	 * Test: QA C1 variable parent is never the offer.
	 */
	public function test_qa_c1_variable_parent_is_never_the_offer() {
		$var = WPS_UBO_Test_Factory::variable_product();
		$this->configure( array( 'offer_product' => $var->get_id() ) );
		$this->assertSame( 0, (int) Upsell_Order_Bump_Acr_Db::get_settings()['offer_product'], 'Rejected on save.' );

		$this->assertFalse( Upsell_Order_Bump_Acr_Engine::is_single_line_product( $var ) );
		$this->assertTrue( Upsell_Order_Bump_Acr_Engine::is_single_line_product( wc_get_product( $var->get_children()[0] ) ) );

		// A row that already points at a variable parent: not added, and no error left behind.
		$row = array(
			'id'            => 1,
			'offer_product' => $var->get_id(),
		);
		wc_clear_notices();
		$this->assertFalse( Upsell_Order_Bump_Acr_Public::add_offer_item( $row ) );
		$this->assertCount( 0, wc_get_notices( 'error' ) );
	}

	/**
	 * Test: QA C3 failed payment keeps the cart and its reminders.
	 */
	public function test_qa_c3_failed_payment_keeps_the_cart_and_its_reminders() {
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		$this->capture( 'pay@example.org' );
		$id = (int) $this->session_row()['id'];
		$this->age( $id, 'updated_at', HOUR_IN_SECONDS );
		Upsell_Order_Bump_Acr_Engine::process();

		WC()->cart->calculate_totals();
		$order_id = WC()->checkout()->create_order(
			array(
				'payment_method'  => 'cod',
				'billing_email'   => 'pay@example.org',
				'billing_country' => 'US',
			)
		);
		$this->pub->on_order_classic( $order_id );
		$order = wc_get_order( $order_id );
		$order->set_status( 'failed' );
		$order->save();

		$row = Upsell_Order_Bump_Acr_Db::get_by( 'id', $id );
		$this->assertSame( 'abandoned', $row['status'], 'A failed payment is not a recovery.' );
		$this->assertNotNull( $row['next_email_at'], 'Reminders continue.' );
		$this->assertSame( 0, Upsell_Order_Bump_Acr_Db::stats()['recovered'] );

		$order = wc_get_order( $order_id );
		$order->set_status( 'processing' );
		$order->save();
		$this->assertSame( 'recovered', Upsell_Order_Bump_Acr_Db::get_by( 'id', $id )['status'] );
	}

	/**
	 * Test: QA C4 order placed before abandonment is found.
	 */
	public function test_qa_c4_order_placed_before_abandonment_is_found() {
		$order = WPS_UBO_Test_Factory::order( array( $this->p['a'] ), 'processing', array( 'email' => 'gap@example.org' ) );
		// Cart saved 2 h ago, ordered ( on another device ) 1 h ago, abandonment noticed afterwards.
		$order->set_date_created( time() - HOUR_IN_SECONDS );
		$order->save();
		$id = Upsell_Order_Bump_Acr_Db::insert(
			array(
				'session_key'   => 'gap-test',
				'email'         => 'gap@example.org',
				'cart'          => array(
					array(
						'product_id' => $this->p['a']->get_id(),
						'variation_id' => 0,
						'quantity' => 1,
						'variation' => array(),
					),
				),
				'status'        => 'abandoned',
				'created_at'    => gmdate( 'Y-m-d H:i:s', time() - 2 * HOUR_IN_SECONDS ),
				'abandoned_at'  => Upsell_Order_Bump_Acr_Db::now(),
				'next_email_at' => Upsell_Order_Bump_Acr_Db::now( -60 ),
			)
		);
		$this->assertSame( 'recovered', Upsell_Order_Bump_Acr_Engine::send_next( $id ) );
		$this->assertCount( 0, $this->sent_mails() );
	}

	/**
	 * Test: QA C5 logging in leaves no orphaned guest row.
	 */
	public function test_qa_c5_logging_in_leaves_no_orphaned_guest_row() {
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		$this->capture( 'login@example.org' );
		$guest = (int) $this->session_row()['id'];

		$user = self::factory()->user->create(
			array(
				'role'       => 'customer',
				'user_email' => 'login@example.org',
			)
		);
		wp_set_current_user( $user );
		self::reset_wc();
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		Upsell_Order_Bump_Acr_Public::sync();

		$this->assertNull( Upsell_Order_Bump_Acr_Db::get_by( 'id', $guest ), 'The guest duplicate is removed.' );
		$this->assertCount( 1, Upsell_Order_Bump_Acr_Db::open_rows_for_email( 'login@example.org' ) );
		wp_set_current_user( 0 );
	}

	/**
	 * Test: QA C6 one order is credited once.
	 */
	public function test_qa_c6_one_order_is_credited_once() {
		$ids = array();
		foreach ( array( 'one', 'two' ) as $key ) {
			$ids[] = Upsell_Order_Bump_Acr_Db::insert(
				array(
					'session_key'  => 'dup-' . $key,
					'email'        => 'twice@example.org',
					'cart'         => array(
						array(
							'product_id' => $this->p['a']->get_id(),
							'variation_id' => 0,
							'quantity' => 1,
							'variation' => array(),
						),
					),
					'status'       => 'abandoned',
					'abandoned_at' => Upsell_Order_Bump_Acr_Db::now(),
				)
			);
		}
		$order = WPS_UBO_Test_Factory::order( array( $this->p['a'] ), 'processing', array( 'email' => 'twice@example.org' ) );
		Upsell_Order_Bump_Acr_Public::attribute_paid_order( wc_get_order( $order->get_id() ) );
		foreach ( $ids as $id ) {
			Upsell_Order_Bump_Acr_Engine::mark_recovered( Upsell_Order_Bump_Acr_Db::get_by( 'id', $id ), wc_get_order( $order->get_id() ), 'direct' );
		}

		$stats = Upsell_Order_Bump_Acr_Db::stats();
		$this->assertSame( 1, $stats['recovered'] );
		$this->assertEqualsWithDelta( (float) $order->get_total(), $stats['revenue'], 0.001, 'Revenue counted once.' );
	}

	/**
	 * Test: QA C7 offer line is one unit without the coupon.
	 */
	public function test_qa_c7_offer_line_is_one_unit_without_the_coupon() {
		$row = $this->abandoned_with_all_emails();
		$this->open_link( $row );
		$key = '';
		foreach ( WC()->cart->get_cart() as $k => $item ) {
			if ( ! empty( $item[ Upsell_Order_Bump_Acr_Public::ITEM_KEY ] ) ) {
				$key = $k;
			}
		}
		$this->assertNotSame( '', $key );

		WC()->cart->cart_contents[ $key ]['quantity'] = 10;
		$this->pub->lock_offer_quantity( $key, 10, 1, WC()->cart );
		$this->assertSame( 1, (int) WC()->cart->cart_contents[ $key ]['quantity'] );
		$this->assertFalse( $this->pub->offer_quantity_editable( true, null, WC()->cart->cart_contents[ $key ] ) );

		$coupon = new WC_Coupon( $row['coupon_code'] );
		$this->assertFalse( $this->pub->coupon_not_on_offer( true, null, $coupon, WC()->cart->cart_contents[ $key ] ), 'Not on the offer line.' );
		$this->assertTrue( $this->pub->coupon_not_on_offer( true, null, $coupon, array( 'product_id' => $this->p['a']->get_id() ) ), 'Still on the rest of the cart.' );
	}

	/**
	 * Test: QA C8 emptying a reopened cart keeps its history.
	 */
	public function test_qa_c8_emptying_a_reopened_cart_keeps_its_history() {
		$row = $this->abandoned_with_all_emails();
		$this->open_link( $row );
		WC()->cart->empty_cart();
		Upsell_Order_Bump_Acr_Public::sync();

		$again = Upsell_Order_Bump_Acr_Db::get_by( 'id', $row['id'] );
		$this->assertNotNull( $again );
		$this->assertSame( 'abandoned', $again['status'] );
		$this->assertSame( 3, (int) $again['emails_sent'] );
		$this->assertNotNull( $again['clicked_at'] );
	}

	/**
	 * Test: QA C9 closed and old links no longer work.
	 */
	public function test_qa_c9_closed_and_old_links_no_longer_work() {
		$row = $this->abandoned_with_all_emails();
		foreach ( array( 'lost', 'unsubscribed' ) as $status ) {
			Upsell_Order_Bump_Acr_Db::update( $row['id'], array( 'status' => $status ) );
			self::reset_wc();
			$this->assertSame( wc_get_cart_url(), $this->redirect_of( array( $this->pub, 'handle_links' ), array( 'wps_ubo_acr' => $row['token'] ) ), $status );
			$this->assertTrue( WC()->cart->is_empty(), $status );
		}

		Upsell_Order_Bump_Acr_Db::update( $row['id'], array( 'status' => 'abandoned' ) );
		$this->age( (int) $row['id'], 'last_email_at', 30 * DAY_IN_SECONDS );
		$this->assertTrue( Upsell_Order_Bump_Acr_Public::link_expired( Upsell_Order_Bump_Acr_Db::get_by( 'id', $row['id'] ) ) );
		$this->age( (int) $row['id'], 'last_email_at', DAY_IN_SECONDS );
		$this->assertFalse( Upsell_Order_Bump_Acr_Public::link_expired( Upsell_Order_Bump_Acr_Db::get_by( 'id', $row['id'] ) ) );
	}

	/**
	 * Test: QA C10 capture needs a session and is rate limited.
	 */
	public function test_qa_c10_capture_needs_a_session_and_is_rate_limited() {
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		// No session cookie ( e.g. a script ): refused.
		$this->assertFalse(
			$this->ajax_call(
				array( $this->pub, 'ajax_capture' ),
				array(
					'nonce' => $this->capture_nonce(),
					'email' => 'bot@example.org',
				)
			)['json']['data']['tracked']
		);

		$limit = static function ( $limits ) {
			return array_map(
				static function () {
					return 2;
				},
				$limits
			);
		};
		add_filter( 'wps_ubo_acr_capture_limits', $limit );
		$this->assertTrue( $this->capture( 'one@example.org' )['data']['tracked'] );
		$this->assertTrue( $this->capture( 'two@example.org' )['data']['tracked'] );
		$this->assertFalse( $this->capture( 'three@example.org' )['data']['tracked'], 'Limit reached.' );
		remove_filter( 'wps_ubo_acr_capture_limits', $limit );
	}

	/**
	 * Test: QA C12 old integration bumps are not offered.
	 */
	public function test_qa_c12_old_integration_bumps_are_not_offered() {
		WPS_UBO_Test_Factory::bump(
			$this->p['off']->get_id(),
			array(
				'wps_upsell_bump_target_ids' => array( (string) $this->p['a']->get_id() ),
				'wps_is_abandoned_bump'      => 'yes',
			)
		);
		$this->configure( array( 'offer_source' => 'bump' ) );
		$row = array(
			'cart'    => array(
				array(
					'product_id' => $this->p['a']->get_id(),
					'variation_id' => 0,
					'quantity' => 1,
				),
			),
			'user_id' => 0,
		);
		$this->assertNull( Upsell_Order_Bump_Acr_Engine::select_offer( $row ) );
	}

	/**
	 * Test: QA C13 expired recovery coupons are deleted.
	 */
	public function test_qa_c13_expired_recovery_coupons_are_deleted() {
		$row     = $this->abandoned_with_all_emails();
		$expired = new WC_Coupon( $row['coupon_code'] );
		$expired->set_date_expires( time() - 3 * DAY_IN_SECONDS );
		$expired->save();
		$other = Upsell_Order_Bump_Acr_Engine::ensure_coupon( array_merge( $row, array( 'coupon_code' => '' ) ), 10 );

		$other_id = wc_get_coupon_id_by_code( $other );
		Upsell_Order_Bump_Acr_Engine::purge();
		$this->assertNull( get_post( $expired->get_id() ), 'Expired: deleted.' );
		$this->assertNotNull( get_post( $other_id ), 'Still valid: kept.' );
	}

	/**
	 * Test: QA C14 no sentence about a missing offer.
	 */
	public function test_qa_c14_no_sentence_about_a_missing_offer() {
		WC()->cart->add_to_cart( $this->p['a']->get_id() );
		$this->capture( 'nooffer@example.org', 'Ravi' );
		$row                  = $this->session_row();
		$row['offer_product'] = 0;
		$row['coupon_code']   = '';

		foreach ( array( 1, 2 ) as $index ) {
			$html = Upsell_Order_Bump_Acr_Mailer::render( $row, $index )['html'];
			$this->assertStringNotContainsString( 'exclusive', strtolower( $html ), "Step $index" );
			$this->assertStringNotContainsString( 'Use code', $html, "Step $index" );
			$this->assertStringContainsString( 'Hoodie', $html, "Step $index still lists the cart." );
		}
	}

	/* --------------------------------------------------------------- privacy */

	/**
	 * Test: Privacy exporter and eraser.
	 */
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

	/**
	 * Test: Feature does not touch legacy abandoned cart option.
	 */
	public function test_feature_does_not_touch_legacy_abandoned_cart_option() {
		update_option( 'wps_ubo_global_options', array( 'wps_ubo_abandoned_cart' => 'yes' ) );
		$this->abandoned_with_all_emails();
		$this->assertSame( array( 'wps_ubo_abandoned_cart' => 'yes' ), get_option( 'wps_ubo_global_options' ) );
	}
}
