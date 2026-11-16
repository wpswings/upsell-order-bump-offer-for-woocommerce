<?php
/**
 * WPS-7889 Gamified bump offers: settings, draw, price override.
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce/Tests
 */

/**
 * @covers Upsell_Order_Bump_Gamified
 */
class GamifiedTest extends WPS_UBO_Test_Case {

	/**
	 * Enable the feature with a given format for bump 1.
	 *
	 * @param array $overrides Raw settings overrides.
	 */
	private function enable( $overrides = array() ) {
		$raw = array_merge(
			array(
				'enabled'                  => 'on',
				'formats'                  => array( 1 => 'spin_wheel' ),
				'guarantee_offer_discount' => 'on',
				'max_discount'             => 50,
			),
			$overrides
		);
		update_option( Upsell_Order_Bump_Gamified::SETTINGS_OPTION, Upsell_Order_Bump_Gamified::sanitize_settings( $raw ) );
	}

	public function test_defaults_are_off_with_six_prizes() {
		delete_option( Upsell_Order_Bump_Gamified::SETTINGS_OPTION );
		$settings = Upsell_Order_Bump_Gamified::get_settings();
		$this->assertSame( 'off', $settings['enabled'] );
		$this->assertCount( 6, $settings['prizes'] );
		$this->assertFalse( Upsell_Order_Bump_Gamified::is_enabled() );
		$this->assertSame( 'standard', Upsell_Order_Bump_Gamified::format_for( 1 ) );
	}

	public function test_sanitize_toggles_only_accept_on() {
		$clean = Upsell_Order_Bump_Gamified::sanitize_settings(
			array(
				'enabled'    => 'yes',
				'allow_skip' => 'on',
			)
		);
		$this->assertSame( 'off', $clean['enabled'] );
		$this->assertSame( 'on', $clean['allow_skip'] );
		$this->assertSame( 'off', $clean['guarantee_offer_discount'] );
	}

	public function test_sanitize_clamps_max_discount() {
		$this->assertSame( 100, Upsell_Order_Bump_Gamified::sanitize_settings( array( 'max_discount' => 500 ) )['max_discount'] );
		$this->assertSame( 1, Upsell_Order_Bump_Gamified::sanitize_settings( array( 'max_discount' => -4 ) )['max_discount'] );
		$this->assertSame( 50, Upsell_Order_Bump_Gamified::sanitize_settings( array( 'max_discount' => 'abc' ) )['max_discount'] );
	}

	public function test_sanitize_formats_drop_standard_and_unknown() {
		$clean = Upsell_Order_Bump_Gamified::sanitize_settings(
			array(
				'formats' => array(
					'3' => 'spin_wheel',
					'4' => 'scratch_card',
					'5' => 'standard',
					'6' => 'slot_machine',
				),
			)
		);
		$this->assertSame(
			array(
				3 => 'spin_wheel',
				4 => 'scratch_card',
			),
			$clean['formats']
		);
	}

	public function test_sanitize_prizes_clamped_and_cleaned() {
		$clean = Upsell_Order_Bump_Gamified::sanitize_settings(
			array(
				'max_discount' => 40,
				'prizes'       => array(
					array( 'label' => '<b>Big</b> win label that is way too long', 'discount' => 90, 'weight' => 5000, 'color' => 'red' ),
					array( 'label' => '   ', 'discount' => 10, 'weight' => 1 ),
					array( 'label' => 'Small', 'discount' => -5, 'weight' => -1, 'color' => '#00ff00' ),
				),
			)
		);
		$this->assertCount( 2, $clean['prizes'] );
		$this->assertSame( 24, mb_strlen( $clean['prizes'][0]['label'] ) );
		$this->assertStringNotContainsString( '<b>', $clean['prizes'][0]['label'] );
		$this->assertSame( 40, $clean['prizes'][0]['discount'] );
		$this->assertSame( 1000, $clean['prizes'][0]['weight'] );
		$this->assertSame( '#2271b1', $clean['prizes'][0]['color'] );
		$this->assertSame( 0, $clean['prizes'][1]['discount'] );
		$this->assertSame( 0, $clean['prizes'][1]['weight'] );
		$this->assertSame( '#00ff00', $clean['prizes'][1]['color'] );
	}

	public function test_sanitize_falls_back_to_default_prizes_when_invalid() {
		$one = Upsell_Order_Bump_Gamified::sanitize_settings( array( 'prizes' => array( array( 'label' => 'Only', 'discount' => 5, 'weight' => 1 ) ) ) );
		$this->assertCount( 6, $one['prizes'] );

		$zero_weight = Upsell_Order_Bump_Gamified::sanitize_settings(
			array(
				'prizes' => array(
					array( 'label' => 'A', 'discount' => 5, 'weight' => 0 ),
					array( 'label' => 'B', 'discount' => 5, 'weight' => 0 ),
				),
			)
		);
		$this->assertCount( 6, $zero_weight['prizes'] );
	}

	public function test_sanitize_caps_prize_count_at_twelve() {
		$rows = array();
		for ( $i = 0; $i < 20; $i++ ) {
			$rows[] = array( 'label' => "P$i", 'discount' => 5, 'weight' => 1 );
		}
		$this->assertCount( Upsell_Order_Bump_Gamified::MAX_PRIZES, Upsell_Order_Bump_Gamified::sanitize_settings( array( 'prizes' => $rows ) )['prizes'] );
	}

	public function test_sanitize_text_and_colors_fall_back_to_defaults() {
		$d     = Upsell_Order_Bump_Gamified::default_settings();
		$clean = Upsell_Order_Bump_Gamified::sanitize_settings(
			array(
				'headline_wheel' => '',
				'button_wheel'   => 'Go!',
				'color_primary'  => 'not-a-color',
				'color_accent'   => '#abcdef',
			)
		);
		$this->assertSame( $d['headline_wheel'], $clean['headline_wheel'] );
		$this->assertSame( 'Go!', $clean['button_wheel'] );
		$this->assertSame( $d['color_primary'], $clean['color_primary'] );
		$this->assertSame( '#abcdef', $clean['color_accent'] );
	}

	public function test_format_for_respects_enabled_flag() {
		$this->enable();
		$this->assertSame( 'spin_wheel', Upsell_Order_Bump_Gamified::format_for( 1 ) );
		$this->assertSame( 'standard', Upsell_Order_Bump_Gamified::format_for( 2 ) );
		$this->enable( array( 'enabled' => 'off' ) );
		$this->assertSame( 'standard', Upsell_Order_Bump_Gamified::format_for( 1 ) );
	}

	public function test_weighted_index_only_picks_positive_weight() {
		$prizes = array(
			array( 'weight' => 0 ),
			array( 'weight' => 5 ),
			array( 'weight' => 0 ),
		);
		for ( $i = 0; $i < 50; $i++ ) {
			$this->assertSame( 1, Upsell_Order_Bump_Gamified::weighted_index( $prizes ) );
		}
		$this->assertSame( 0, Upsell_Order_Bump_Gamified::weighted_index( array( array( 'weight' => 0 ) ) ) );
	}

	public function test_weighted_index_distribution_roughly_follows_weights() {
		$prizes = array( array( 'weight' => 90 ), array( 'weight' => 10 ) );
		$hits   = array( 0, 0 );
		for ( $i = 0; $i < 2000; $i++ ) {
			++$hits[ Upsell_Order_Bump_Gamified::weighted_index( $prizes ) ];
		}
		$this->assertGreaterThan( 1600, $hits[0] );
		$this->assertGreaterThan( 100, $hits[1] );
	}

	public function test_draw_is_stored_once_per_session_and_counted() {
		$this->enable();
		delete_option( Upsell_Order_Bump_Gamified::STATS_OPTION );

		$first = Upsell_Order_Bump_Gamified::draw( 1 );
		$this->assertTrue( $first['new'] );
		$this->assertFalse( $first['prize']['skipped'] );
		$this->assertSame( 'spin_wheel', $first['prize']['format'] );
		$this->assertLessThanOrEqual( 50, $first['prize']['discount'] );

		$second = Upsell_Order_Bump_Gamified::draw( 1 );
		$this->assertFalse( $second['new'] );
		$this->assertSame( $first['prize'], $second['prize'] );

		$stats = Upsell_Order_Bump_Gamified::get_stats();
		$this->assertSame( 1, $stats[1]['plays'] );
		$this->assertSame( 1, $stats[1]['prizes'][ $first['prize']['index'] ] );
	}

	public function test_skip_records_zero_discount_and_skip_stat() {
		$this->enable();
		delete_option( Upsell_Order_Bump_Gamified::STATS_OPTION );
		$result = Upsell_Order_Bump_Gamified::draw( 1, true );
		$this->assertTrue( $result['prize']['skipped'] );
		$this->assertSame( 0, $result['prize']['discount'] );
		$this->assertSame( 1, Upsell_Order_Bump_Gamified::get_stats()[1]['skips'] );
	}

	public function test_draw_filter_cannot_exceed_max_discount() {
		$this->enable( array( 'max_discount' => 20 ) );
		$filter = static function ( $prize ) {
			$prize['discount'] = 99;
			return $prize;
		};
		add_filter( 'wps_ubo_game_draw', $filter );
		$result = Upsell_Order_Bump_Gamified::draw( 1 );
		remove_filter( 'wps_ubo_game_draw', $filter );
		$this->assertSame( 20, $result['prize']['discount'] );
	}

	public function test_clear_session_forgets_prizes() {
		$this->enable();
		Upsell_Order_Bump_Gamified::draw( 1 );
		$this->assertNotNull( Upsell_Order_Bump_Gamified::get_prize( 1 ) );
		Upsell_Order_Bump_Gamified::clear_session();
		$this->assertNull( Upsell_Order_Bump_Gamified::get_prize( 1 ) );
	}

	public function test_effective_discount_guarantees_bump_percent() {
		$this->enable();
		$bump = array(
			'wps_upsell_offer_price_type'          => '%',
			'wps_upsell_bump_offer_discount_price' => '25',
		);
		$this->assertSame( 25, Upsell_Order_Bump_Gamified::effective_discount( $bump, array( 'discount' => 10 ) ) );
		$this->assertSame( 30, Upsell_Order_Bump_Gamified::effective_discount( $bump, array( 'discount' => 30 ) ) );
		// A skipped game (0) never gets the guaranteed discount bumped up.
		$this->assertSame( 0, Upsell_Order_Bump_Gamified::effective_discount( $bump, array( 'discount' => 0 ) ) );

		$this->enable( array( 'guarantee_offer_discount' => 'off' ) );
		$this->assertSame( 10, Upsell_Order_Bump_Gamified::effective_discount( $bump, array( 'discount' => 10 ) ) );

		$fixed = array(
			'wps_upsell_offer_price_type'          => 'fixed',
			'wps_upsell_bump_offer_discount_price' => '25',
		);
		$this->enable();
		$this->assertSame( 10, Upsell_Order_Bump_Gamified::effective_discount( $fixed, array( 'discount' => 10 ) ) );
	}

	public function test_bump_list_read_filter_applies_prize_and_write_guard_restores() {
		$this->enable( array( 'guarantee_offer_discount' => 'off' ) );
		update_option(
			'wps_ubo_bump_list',
			array(
				1 => array(
					'wps_upsell_offer_price_type'          => 'fixed',
					'wps_upsell_bump_offer_discount_price' => '5',
				),
				2 => array(
					'wps_upsell_offer_price_type'          => 'fixed',
					'wps_upsell_bump_offer_discount_price' => '7',
				),
			)
		);
		WC()->session->set(
			Upsell_Order_Bump_Gamified::SESSION_KEY,
			array(
				1 => array( 'discount' => 30 ),
				2 => array( 'discount' => 30 ),
			)
		);

		$filtered = get_option( 'wps_ubo_bump_list' );
		$this->assertSame( '%', $filtered[1]['wps_upsell_offer_price_type'] );
		$this->assertSame( '30', $filtered[1]['wps_upsell_bump_offer_discount_price'] );
		// Bump 2 is a standard bump: untouched.
		$this->assertSame( 'fixed', $filtered[2]['wps_upsell_offer_price_type'] );

		// Raw read bypasses the override.
		$raw = Upsell_Order_Bump_Gamified::raw_bumps();
		$this->assertSame( 'fixed', $raw[1]['wps_upsell_offer_price_type'] );

		// Saving the filtered list must not persist the prize.
		update_option( 'wps_ubo_bump_list', $filtered );
		$stored = Upsell_Order_Bump_Gamified::raw_bumps();
		$this->assertSame( 'fixed', $stored[1]['wps_upsell_offer_price_type'] );
		$this->assertSame( '5', $stored[1]['wps_upsell_bump_offer_discount_price'] );
		$this->assertArrayNotHasKey( Upsell_Order_Bump_Gamified::MARKER, $stored[1] );
	}

	/*
	 * QA WPS-7889 regressions.
	 */

	public function test_g2_fixed_price_bump_never_gets_more_expensive_after_a_win() {
		$this->enable();
		$product = WPS_UBO_Test_Factory::simple_product( array( 'regular_price' => '18' ) );
		$cheap   = array(
			'wps_upsell_offer_price_type'          => 'fixed',
			'wps_upsell_bump_offer_discount_price' => '5',
			'wps_upsell_bump_products_in_offer'    => $product->get_id(),
		);
		// 18 at 30% off is 12.60, dearer than the normal fixed price of 5: keep the normal offer.
		$this->assertSame( 0, Upsell_Order_Bump_Gamified::effective_discount( $cheap, array( 'discount' => 30 ) ) );

		// Normal fixed price 15 > 12.60: the prize is the better deal.
		$dear = array_merge( $cheap, array( 'wps_upsell_bump_offer_discount_price' => '15' ) );
		$this->assertSame( 30, Upsell_Order_Bump_Gamified::effective_discount( $dear, array( 'discount' => 30 ) ) );

		// Without the guarantee the prize always applies.
		$this->enable( array( 'guarantee_offer_discount' => 'off' ) );
		$this->assertSame( 30, Upsell_Order_Bump_Gamified::effective_discount( $cheap, array( 'discount' => 30 ) ) );
	}

	public function test_g2_percent_guarantee_keeps_fractional_discount() {
		$this->enable();
		$bump = array(
			'wps_upsell_offer_price_type'          => '%',
			'wps_upsell_bump_offer_discount_price' => '12.5',
		);
		$this->assertSame( 12.5, Upsell_Order_Bump_Gamified::effective_discount( $bump, array( 'discount' => 10 ) ), 'Was truncated to 12.' );
	}

	public function test_g8_lowering_max_discount_also_caps_prizes_already_drawn() {
		$this->enable(
			array(
				'guarantee_offer_discount' => 'off',
				'max_discount'             => 10,
			)
		);
		$this->assertSame( 10, Upsell_Order_Bump_Gamified::effective_discount( array(), array( 'discount' => 30 ) ) );
	}

	public function test_g8_empty_prize_table_gives_the_standard_offer() {
		$this->enable();
		delete_option( Upsell_Order_Bump_Gamified::STATS_OPTION );
		$empty = static function () {
			return array();
		};
		add_filter( 'wps_ubo_game_prizes', $empty );
		$result = Upsell_Order_Bump_Gamified::draw( 1 );
		remove_filter( 'wps_ubo_game_prizes', $empty );

		$this->assertSame( -1, $result['prize']['index'] );
		$this->assertSame( 0, $result['prize']['discount'] );
		$this->assertArrayNotHasKey( 1, Upsell_Order_Bump_Gamified::get_stats() );
	}

	public function test_g3_capped_prize_label_follows_the_discount() {
		$raw      = array(
			'max_discount' => 20,
			'prizes'       => array(
				array( 'label' => '50% OFF', 'discount' => 50, 'weight' => 1 ),
				array( 'label' => 'Mystery', 'discount' => 40, 'weight' => 1 ),
				array( 'label' => '5% OFF', 'discount' => 5, 'weight' => 1 ),
			),
		);
		$settings = Upsell_Order_Bump_Gamified::sanitize_settings( $raw );

		$this->assertSame( array( '20% OFF', 'Mystery', '5% OFF' ), wp_list_pluck( $settings['prizes'], 'label' ) );
		$this->assertSame( array( 20, 20, 5 ), wp_list_pluck( $settings['prizes'], 'discount' ) );
		$this->assertSame( 2, Upsell_Order_Bump_Gamified::count_capped_prizes( $raw ) );
		$this->assertSame( 'Save 10 %', Upsell_Order_Bump_Gamified::label_for_discount( 'Save 12,5 %', 10 ) );
	}

	public function test_g4_draw_uses_the_prize_saved_by_a_parallel_request() {
		global $wpdb;
		$this->enable();
		delete_option( Upsell_Order_Bump_Gamified::STATS_OPTION );

		// Another request drew first and saved it; this request's in-memory session has no prize yet.
		$saved = array(
			1 => array(
				'index'    => 2,
				'label'    => '20% OFF',
				'discount' => 20,
				'skipped'  => false,
				'format'   => 'spin_wheel',
				'time'     => time(),
				'accepted' => false,
			),
		);
		$wpdb->replace(
			"{$wpdb->prefix}woocommerce_sessions",
			array(
				'session_key'    => (string) WC()->session->get_customer_id(),
				'session_value'  => maybe_serialize( array( Upsell_Order_Bump_Gamified::SESSION_KEY => maybe_serialize( $saved ) ) ),
				'session_expiry' => time() + HOUR_IN_SECONDS,
			)
		);

		$result = Upsell_Order_Bump_Gamified::draw( 1 );
		$this->assertFalse( $result['new'], 'No second draw.' );
		$this->assertSame( $saved[1], $result['prize'] );
		$this->assertSame( $saved[1], Upsell_Order_Bump_Gamified::get_prize( 1 ) );
		$this->assertArrayNotHasKey( 1, Upsell_Order_Bump_Gamified::get_stats(), 'Not counted twice.' );
	}

	public function test_g6_accept_counted_once_per_session_and_labels_stored() {
		$this->enable();
		delete_option( Upsell_Order_Bump_Gamified::STATS_OPTION );
		$result = Upsell_Order_Bump_Gamified::draw( 1 );

		$this->assertTrue( Upsell_Order_Bump_Gamified::mark_accepted( 1 ) );
		$this->assertFalse( Upsell_Order_Bump_Gamified::mark_accepted( 1 ), 'Re-adding the offer is not a new accept.' );
		$this->assertSame( 1, Upsell_Order_Bump_Gamified::get_stats()[1]['labels'][ $result['prize']['label'] ] );
	}

	public function test_g1_prize_kept_until_the_order_is_really_placed() {
		$this->enable();
		$pub = new Upsell_Order_Bump_Gamified_Public( '3.1.8' );

		// Not cleared when the order is created (before payment).
		foreach ( array( 'woocommerce_checkout_order_processed', 'woocommerce_store_api_checkout_order_processed' ) as $hook ) {
			foreach ( (array) ( isset( $GLOBALS['wp_filter'][ $hook ] ) ? $GLOBALS['wp_filter'][ $hook ]->callbacks : array() ) as $callbacks ) {
				foreach ( $callbacks as $cb ) {
					$this->assertFalse( is_array( $cb['function'] ) && $cb['function'][0] instanceof Upsell_Order_Bump_Gamified_Public, $hook );
				}
			}
		}

		Upsell_Order_Bump_Gamified::draw( 1 );
		$product = WPS_UBO_Test_Factory::simple_product();

		// Failed payment: the prize stays (no re-roll; the retry keeps it).
		$pub->clear_after_paid_order( WPS_UBO_Test_Factory::order( array( $product ), 'failed' )->get_id() );
		$this->assertNotNull( Upsell_Order_Bump_Gamified::get_prize( 1 ) );

		// Order went through: single use, cleared.
		$pub->clear_after_paid_order( WPS_UBO_Test_Factory::order( array( $product ), 'processing' )->get_id() );
		$this->assertNull( Upsell_Order_Bump_Gamified::get_prize( 1 ) );

		// WooCommerce empties the cart after a successful payment.
		Upsell_Order_Bump_Gamified::draw( 1 );
		$pub->clear_after_order();
		$this->assertNull( Upsell_Order_Bump_Gamified::get_prize( 1 ) );
	}

	public function test_guard_ignores_non_arrays() {
		$this->assertSame( 'x', Upsell_Order_Bump_Gamified::guard_bump_list( 'x' ) );
	}

	public function test_uninstall_removes_options() {
		$this->enable();
		Upsell_Order_Bump_Gamified::bump_stat( 1, 'plays' );
		Upsell_Order_Bump_Gamified::uninstall();
		$this->assertFalse( get_option( Upsell_Order_Bump_Gamified::SETTINGS_OPTION ) );
		$this->assertFalse( get_option( Upsell_Order_Bump_Gamified::STATS_OPTION ) );
	}
}
