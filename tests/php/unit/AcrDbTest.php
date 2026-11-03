<?php
/**
 * WPS-7890 Cart recovery: settings, step timing, row storage, stats, mailer helpers.
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce/Tests
 */

/**
 * @covers Upsell_Order_Bump_Acr_Db
 * @covers Upsell_Order_Bump_Acr_Mailer
 * @covers Upsell_Order_Bump_Acr_Engine::offer_price
 */
class AcrDbTest extends WPS_UBO_Test_Case {

	public function test_defaults_off_with_three_steps() {
		delete_option( Upsell_Order_Bump_Acr_Db::SETTINGS_OPTION );
		$s = Upsell_Order_Bump_Acr_Db::get_settings();
		$this->assertSame( 'off', $s['enabled'] );
		$this->assertFalse( Upsell_Order_Bump_Acr_Db::is_enabled() );
		$this->assertCount( 3, $s['steps'] );
		$this->assertSame( 30, $s['abandon_minutes'] );
		$this->assertSame( 10, $s['steps'][2]['coupon'] );
	}

	public function test_sanitize_clamps_numbers() {
		$s = Upsell_Order_Bump_Acr_Db::sanitize_settings(
			array(
				'abandon_minutes'    => 1,
				'offer_discount'     => 150,
				'coupon_expiry_days' => 0,
				'lost_after_days'    => 999,
				'retention_days'     => 'abc',
			)
		);
		$this->assertSame( 5, $s['abandon_minutes'] );
		$this->assertSame( 90, $s['offer_discount'] );
		$this->assertSame( 1, $s['coupon_expiry_days'] );
		$this->assertSame( 60, $s['lost_after_days'] );
		$this->assertSame( 90, $s['retention_days'] );
	}

	public function test_sanitize_toggles_and_source() {
		$s = Upsell_Order_Bump_Acr_Db::sanitize_settings(
			array(
				'enabled'      => 'on',
				'offer_source' => 'evil',
			)
		);
		$this->assertSame( 'on', $s['enabled'] );
		$this->assertSame( 'off', $s['capture_guests'], 'Unchecked checkbox (missing key) means off.' );
		$this->assertSame( 'auto', $s['offer_source'] );
		$this->assertSame( 'fixed', Upsell_Order_Bump_Acr_Db::sanitize_settings( array( 'offer_source' => 'fixed' ) )['offer_source'] );
	}

	public function test_sanitize_roles_keep_only_existing() {
		$s = Upsell_Order_Bump_Acr_Db::sanitize_settings( array( 'exclude_roles' => array( 'administrator', 'not_a_role', 'Customer' ) ) );
		$this->assertSame( array( 'administrator', 'customer' ), $s['exclude_roles'] );
	}

	public function test_sanitize_steps() {
		$s = Upsell_Order_Bump_Acr_Db::sanitize_settings(
			array(
				'steps' => array(
					array(
						'enabled'  => 'on',
						'delay'    => 0,
						'unit'     => 'weeks',
						'subject'  => '<b>Hi</b> there',
						'body'     => '<script>x</script><p>ok</p>',
						'discount' => '95',
						'coupon'   => '-3',
					),
					array(
						'enabled'  => 'on',
						'delay'    => 5000,
						'unit'     => 'minutes',
						'discount' => '',
						'subject'  => '',
					),
				),
			)
		);
		$d = Upsell_Order_Bump_Acr_Db::default_steps();
		$this->assertCount( 3, $s['steps'] );
		$this->assertSame( 1, $s['steps'][0]['delay'] );
		$this->assertSame( $d[0]['unit'], $s['steps'][0]['unit'] );
		$this->assertSame( 'Hi there', $s['steps'][0]['subject'] );
		$this->assertStringNotContainsString( '<script>', $s['steps'][0]['body'] );
		$this->assertStringContainsString( '<p>ok</p>', $s['steps'][0]['body'] );
		$this->assertSame( 90, $s['steps'][0]['discount'] );
		$this->assertSame( 0, $s['steps'][0]['coupon'] );
		$this->assertSame( 999, $s['steps'][1]['delay'] );
		$this->assertSame( 'minutes', $s['steps'][1]['unit'] );
		$this->assertSame( '', $s['steps'][1]['discount'] );
		$this->assertSame( $d[1]['subject'], $s['steps'][1]['subject'] );
		// Step 3 not posted at all: disabled, defaults kept.
		$this->assertSame( 'off', $s['steps'][2]['enabled'] );
		$this->assertSame( $d[2]['body'], $s['steps'][2]['body'] );
	}

	public function test_step_seconds() {
		$this->assertSame( 300, Upsell_Order_Bump_Acr_Db::step_seconds( array( 'delay' => 5, 'unit' => 'minutes' ) ) );
		$this->assertSame( 7200, Upsell_Order_Bump_Acr_Db::step_seconds( array( 'delay' => 2, 'unit' => 'hours' ) ) );
		$this->assertSame( 3 * DAY_IN_SECONDS, Upsell_Order_Bump_Acr_Db::step_seconds( array( 'delay' => 3, 'unit' => 'days' ) ) );
		$this->assertSame( HOUR_IN_SECONDS, Upsell_Order_Bump_Acr_Db::step_seconds( array( 'delay' => 1, 'unit' => 'bogus' ) ) );
	}

	public function test_next_step_index_skips_disabled() {
		$s                        = Upsell_Order_Bump_Acr_Db::default_settings();
		$s['steps'][0]['enabled'] = 'off';
		$s['steps'][1]['enabled'] = 'on';
		$s['steps'][2]['enabled'] = 'off';
		update_option( Upsell_Order_Bump_Acr_Db::SETTINGS_OPTION, $s );
		$this->assertSame( 1, Upsell_Order_Bump_Acr_Db::next_step_index( 0 ) );
		$this->assertSame( 1, Upsell_Order_Bump_Acr_Db::next_step_index( 1 ) );
		$this->assertSame( -1, Upsell_Order_Bump_Acr_Db::next_step_index( 2 ) );
	}

	public function test_cron_schedule_registered() {
		$schedules = Upsell_Order_Bump_Acr_Db::cron_schedules( array() );
		$this->assertSame( 300, $schedules['wps_ubo_acr_5min']['interval'] );
		$this->assertArrayHasKey( 'wps_ubo_acr_5min', wp_get_schedules() );
	}

	public function test_insert_get_update_delete_row() {
		$id = Upsell_Order_Bump_Acr_Db::insert(
			array(
				'session_key' => 'sess-1',
				'email'       => 'a@example.org',
				'cart'        => array( array( 'product_id' => 5, 'quantity' => 2 ) ),
			)
		);
		$this->assertGreaterThan( 0, $id );
		$row = Upsell_Order_Bump_Acr_Db::get_by( 'id', $id );
		$this->assertSame( 'active', $row['status'] );
		$this->assertMatchesRegularExpression( '/^[a-z0-9]{32}$/', $row['token'] );
		$this->assertSame( 5, $row['cart'][0]['product_id'], 'Cart JSON is hydrated back to an array.' );
		$this->assertSame( $id, (int) Upsell_Order_Bump_Acr_Db::get_by( 'token', $row['token'] )['id'] );
		$this->assertSame( $id, (int) Upsell_Order_Bump_Acr_Db::get_by( 'session_key', 'sess-1' )['id'] );

		Upsell_Order_Bump_Acr_Db::update( $id, array( 'status' => 'abandoned', 'cart' => array() ) );
		$this->assertSame( 'abandoned', Upsell_Order_Bump_Acr_Db::get_by( 'id', $id )['status'] );
		$this->assertSame( array(), Upsell_Order_Bump_Acr_Db::get_by( 'id', $id )['cart'] );

		Upsell_Order_Bump_Acr_Db::delete( $id );
		$this->assertNull( Upsell_Order_Bump_Acr_Db::get_by( 'id', $id ) );
	}

	public function test_get_by_rejects_unknown_column_and_empty_value() {
		$this->assertNull( Upsell_Order_Bump_Acr_Db::get_by( 'email', 'a@example.org' ) );
		$this->assertNull( Upsell_Order_Bump_Acr_Db::get_by( 'token', '' ) );
	}

	public function test_unsubscribed_and_open_row_lookup() {
		Upsell_Order_Bump_Acr_Db::insert( array( 'session_key' => 'u1', 'email' => 'gone@example.org', 'cart' => array(), 'status' => 'unsubscribed' ) );
		$this->assertTrue( Upsell_Order_Bump_Acr_Db::is_unsubscribed( 'Gone@Example.org' ) );
		$this->assertFalse( Upsell_Order_Bump_Acr_Db::is_unsubscribed( 'other@example.org' ) );

		$old = Upsell_Order_Bump_Acr_Db::insert( array( 'session_key' => 'o1', 'email' => 'x@example.org', 'cart' => array(), 'status' => 'abandoned', 'abandoned_at' => Upsell_Order_Bump_Acr_Db::now( -100 ) ) );
		$new = Upsell_Order_Bump_Acr_Db::insert( array( 'session_key' => 'o2', 'email' => 'x@example.org', 'cart' => array(), 'status' => 'lost', 'abandoned_at' => Upsell_Order_Bump_Acr_Db::now( -10 ) ) );
		Upsell_Order_Bump_Acr_Db::insert( array( 'session_key' => 'o3', 'email' => 'x@example.org', 'cart' => array(), 'status' => 'recovered', 'abandoned_at' => Upsell_Order_Bump_Acr_Db::now() ) );
		$this->assertSame( $new, (int) Upsell_Order_Bump_Acr_Db::open_row_for_email( 'x@example.org' )['id'] );
		$this->assertNotSame( $old, $new );
		$this->assertNull( Upsell_Order_Bump_Acr_Db::open_row_for_email( 'nobody@example.org' ) );
	}

	public function test_list_rows_filters_and_paginates() {
		for ( $i = 0; $i < 3; $i++ ) {
			Upsell_Order_Bump_Acr_Db::insert( array( 'session_key' => "l$i", 'email' => "l$i@example.org", 'cart' => array(), 'status' => 'abandoned' ) );
		}
		Upsell_Order_Bump_Acr_Db::insert( array( 'session_key' => 'anon', 'email' => '', 'cart' => array() ) );
		$all = Upsell_Order_Bump_Acr_Db::list_rows( '', 1, 2 );
		$this->assertSame( 3, $all['total'], 'Rows without email are hidden from the "all" view.' );
		$this->assertCount( 2, $all['rows'] );
		$this->assertCount( 1, Upsell_Order_Bump_Acr_Db::list_rows( '', 2, 2 )['rows'] );
		$this->assertSame( 3, Upsell_Order_Bump_Acr_Db::list_rows( 'abandoned', 1 )['total'] );
		$this->assertSame( 0, Upsell_Order_Bump_Acr_Db::list_rows( 'recovered', 1 )['total'] );
	}

	public function test_stats_rates() {
		$base = array( 'cart' => array(), 'abandoned_at' => Upsell_Order_Bump_Acr_Db::now( -3600 ) );
		Upsell_Order_Bump_Acr_Db::insert( $base + array( 'session_key' => 's1', 'email' => '1@e.org', 'status' => 'recovered', 'recovered_via' => 'link', 'recovered_total' => 40, 'emails_sent' => 2, 'offer_product' => 9, 'offer_accepted' => 1 ) );
		Upsell_Order_Bump_Acr_Db::insert( $base + array( 'session_key' => 's2', 'email' => '2@e.org', 'status' => 'recovered', 'recovered_via' => 'direct', 'recovered_total' => 10, 'emails_sent' => 1, 'offer_product' => 9 ) );
		Upsell_Order_Bump_Acr_Db::insert( $base + array( 'session_key' => 's3', 'email' => '3@e.org', 'status' => 'lost', 'emails_sent' => 3 ) );
		Upsell_Order_Bump_Acr_Db::insert( $base + array( 'session_key' => 's4', 'email' => '4@e.org', 'status' => 'abandoned' ) );
		// Older than the window: ignored.
		Upsell_Order_Bump_Acr_Db::insert( array( 'session_key' => 's5', 'email' => '5@e.org', 'cart' => array(), 'status' => 'recovered', 'abandoned_at' => Upsell_Order_Bump_Acr_Db::now( -40 * DAY_IN_SECONDS ) ) );

		$st = Upsell_Order_Bump_Acr_Db::stats( 30 );
		$this->assertSame( 4, $st['abandoned'] );
		$this->assertSame( 2, $st['recovered'] );
		$this->assertSame( 1, $st['via_link'] );
		$this->assertEqualsWithDelta( 50.0, $st['revenue'], 0.001 );
		$this->assertSame( 6, $st['emails'] );
		$this->assertSame( 1, $st['lost'] );
		$this->assertSame( 50.0, $st['rate'] );
		$this->assertSame( 50.0, $st['offer_rate'] );
	}

	public function test_stats_empty_has_zero_rates() {
		$st = Upsell_Order_Bump_Acr_Db::stats( 30 );
		$this->assertSame( 0, $st['abandoned'] );
		$this->assertSame( 0, $st['rate'] );
	}

	public function test_reschedule_follows_enabled_flag() {
		$s            = Upsell_Order_Bump_Acr_Db::default_settings();
		$s['enabled'] = 'on';
		update_option( Upsell_Order_Bump_Acr_Db::SETTINGS_OPTION, $s );
		Upsell_Order_Bump_Acr_Db::reschedule();
		$this->assertTrue( (bool) as_next_scheduled_action( Upsell_Order_Bump_Acr_Db::CRON_HOOK, array(), Upsell_Order_Bump_Acr_Db::AS_GROUP ) );

		$s['enabled'] = 'off';
		update_option( Upsell_Order_Bump_Acr_Db::SETTINGS_OPTION, $s );
		Upsell_Order_Bump_Acr_Db::reschedule();
		$this->assertFalse( as_next_scheduled_action( Upsell_Order_Bump_Acr_Db::CRON_HOOK, array(), Upsell_Order_Bump_Acr_Db::AS_GROUP ) );
	}

	public function test_offer_price() {
		$p = WPS_UBO_Test_Factory::simple_product( array( 'regular_price' => '18', 'sale_price' => '16' ) );
		$this->assertSame( 15.3, Upsell_Order_Bump_Acr_Engine::offer_price( $p, 15 ), 'Discount applies to the regular price.' );
		$this->assertSame( 1.8, Upsell_Order_Bump_Acr_Engine::offer_price( $p, 100 ), 'Capped at 90%.' );
		$this->assertSame( 18.0, Upsell_Order_Bump_Acr_Engine::offer_price( $p, -5 ) );
	}

	public function test_mailer_urls_and_signature() {
		$row = array( 'token' => 'abcdefabcdefabcdefabcdefabcdef12' );
		$this->assertStringContainsString( 'wps_ubo_acr=' . $row['token'], Upsell_Order_Bump_Acr_Mailer::recovery_url( $row ) );
		$this->assertStringNotContainsString( 'wps_ubo_acr_add', Upsell_Order_Bump_Acr_Mailer::recovery_url( $row ) );
		$this->assertStringContainsString( 'wps_ubo_acr_add=1', Upsell_Order_Bump_Acr_Mailer::recovery_url( $row, true ) );

		$unsub = Upsell_Order_Bump_Acr_Mailer::unsubscribe_url( $row );
		$sig   = Upsell_Order_Bump_Acr_Mailer::unsubscribe_sig( $row['token'] );
		$this->assertSame( 20, strlen( $sig ) );
		$this->assertStringContainsString( 'sig=' . $sig, $unsub );
		$this->assertNotSame( $sig, Upsell_Order_Bump_Acr_Mailer::unsubscribe_sig( 'other-token' ) );
	}

	public function test_mailer_sample_preview_has_coupon_and_offer() {
		WPS_UBO_Test_Factory::simple_product( array( 'name' => 'Sample offer' ) );
		$email = Upsell_Order_Bump_Acr_Mailer::render( Upsell_Order_Bump_Acr_Mailer::sample_row(), 2 );
		$this->assertArrayHasKey( 'html', $email );
		$this->assertArrayHasKey( 'subject', $email );
		$this->assertStringContainsString( 'ACR-SAMPLE', $email['html'] );
		$this->assertStringNotContainsString( '{coupon_code}', $email['html'], 'All placeholders replaced.' );
		$this->assertStringNotContainsString( '{recovery_button}', $email['html'] );
	}

	public function test_mailer_subject_decodes_entities() {
		$s                        = Upsell_Order_Bump_Acr_Db::default_settings();
		$s['steps'][0]['subject'] = 'Save on {site_name} &amp; more';
		update_option( Upsell_Order_Bump_Acr_Db::SETTINGS_OPTION, $s );
		$email = Upsell_Order_Bump_Acr_Mailer::render( Upsell_Order_Bump_Acr_Mailer::sample_row(), 0 );
		$this->assertStringNotContainsString( '&amp;', $email['subject'] );
		$this->assertStringContainsString( '& more', $email['subject'] );
	}

	public function test_send_step_uses_wp_mail() {
		$this->assertTrue( Upsell_Order_Bump_Acr_Mailer::send_step( Upsell_Order_Bump_Acr_Mailer::sample_row(), 0, 'shop@example.org' ) );
		$mails = $this->sent_mails();
		$this->assertCount( 1, $mails );
		$this->assertSame( 'shop@example.org', $mails[0]['to'] );
	}
}
