<?php
/**
 * WPS-7888 Smart recommendations: settings, model meta, scoring, offer swaps, AI parsing.
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce/Tests
 */

/**
 * Reco tests.
 *
 * @covers Upsell_Order_Bump_Reco_Db
 * @covers Upsell_Order_Bump_Reco_Engine
 * @covers Upsell_Order_Bump_Reco_Public
 * @covers Upsell_Order_Bump_Reco_Ai::parse
 * @covers Upsell_Order_Bump_Reco_Admin
 */
class Reco_Test extends WPS_UBO_Test_Case {

	/**
	 * Set up the test fixture.
	 */
	public function set_up() {
		parent::set_up();
		Upsell_Order_Bump_Reco_Engine::flush_cache();
	}

	/**
	 * Insert a scored pair into the live model table.
	 *
	 * @param mixed $source Source.
	 * @param mixed $target Target.
	 * @param mixed $score Score.
	 * @param array $extra Extra.
	 */
	private function pair( $source, $target, $score, $extra = array() ) {
		global $wpdb;
		$wpdb->insert(
			Upsell_Order_Bump_Reco_Db::pairs_table(),
			array_merge(
				array(
					'source_id' => $source,
					'target_id' => $target,
					'co_count'  => 5,
					'seq_count' => 0,
					'ml_score'  => $score,
					'lift'      => 2,
					'score'     => $score,
				),
				$extra
			)
		);
		Upsell_Order_Bump_Reco_Engine::flush_cache();
	}

	/* ----------------------------------------------------------- settings */

	/**
	 * Test: Defaults.
	 */
	public function test_defaults() {
		delete_option( Upsell_Order_Bump_Reco_Db::SETTINGS_OPTION );
		$s = Upsell_Order_Bump_Reco_Db::get_settings();
		$this->assertSame( 'off', $s['enabled'] );
		$this->assertSame( 365, $s['lookback_days'] );
		$this->assertSame( 'daily', $s['schedule'] );
		$this->assertFalse( Upsell_Order_Bump_Reco_Db::is_enabled() );
	}

	/**
	 * Test: Sanitize clamps and whitelists.
	 */
	public function test_sanitize_clamps_and_whitelists() {
		$s = Upsell_Order_Bump_Reco_Db::sanitize_settings(
			array(
				'enabled'         => 'on',
				'lookback_days'   => 1,
				'min_support'     => 0,
				'min_confidence'  => 200,
				'min_lift'        => 99.999,
				'seq_window_days' => -1,
				'max_candidates'  => 1,
				'schedule'        => 'hourly',
				'ai_provider'     => 'not-installed',
				'ai_anchor_limit' => 999,
			)
		);
		$this->assertSame( 'on', $s['enabled'] );
		$this->assertSame( 7, $s['lookback_days'] );
		$this->assertSame( 1, $s['min_support'] );
		$this->assertSame( 100, $s['min_confidence'] );
		$this->assertSame( 50.0, $s['min_lift'] );
		$this->assertSame( 0, $s['seq_window_days'] );
		$this->assertSame( 3, $s['max_candidates'] );
		$this->assertSame( 'daily', $s['schedule'] );
		$this->assertSame( '', $s['ai_provider'] );
		$this->assertSame( 200, $s['ai_anchor_limit'] );
		$this->assertSame( 'off', $s['exclude_purchased'] );
		$this->assertSame( 'weekly', Upsell_Order_Bump_Reco_Db::sanitize_settings( array( 'schedule' => 'weekly' ) )['schedule'] );
		$this->assertSame( 1.25, Upsell_Order_Bump_Reco_Db::sanitize_settings( array( 'min_lift' => '1.249' ) )['min_lift'] );
	}

	/**
	 * Test: Model meta and has model.
	 */
	public function test_model_meta_and_has_model() {
		delete_option( Upsell_Order_Bump_Reco_Db::MODEL_OPTION );
		$this->assertSame( 'idle', Upsell_Order_Bump_Reco_Db::get_model()['status'] );
		$this->assertFalse( Upsell_Order_Bump_Reco_Db::has_model() );
		Upsell_Order_Bump_Reco_Db::update_model(
			array(
				'trained_at' => time(),
				'pairs' => 0,
			)
		);
		$this->assertFalse( Upsell_Order_Bump_Reco_Db::has_model(), 'A model with zero pairs is not usable.' );
		$meta = Upsell_Order_Bump_Reco_Db::update_model( array( 'pairs' => 4 ) );
		$this->assertTrue( Upsell_Order_Bump_Reco_Db::has_model() );
		$this->assertSame( 4, $meta['pairs'] );
	}

	/**
	 * Test: Reschedule by setting.
	 */
	public function test_reschedule_by_setting() {
		update_option(
			Upsell_Order_Bump_Reco_Db::SETTINGS_OPTION,
			Upsell_Order_Bump_Reco_Db::sanitize_settings(
				array(
					'enabled' => 'on',
					'schedule' => 'weekly',
				)
			)
		);
		Upsell_Order_Bump_Reco_Db::reschedule();
		$this->assertNotFalse( as_next_scheduled_action( Upsell_Order_Bump_Reco_Db::TRAIN_HOOK, array(), Upsell_Order_Bump_Reco_Db::AS_GROUP ) );

		update_option(
			Upsell_Order_Bump_Reco_Db::SETTINGS_OPTION,
			Upsell_Order_Bump_Reco_Db::sanitize_settings(
				array(
					'enabled' => 'on',
					'schedule' => 'manual',
				)
			)
		);
		Upsell_Order_Bump_Reco_Db::reschedule();
		$this->assertFalse( as_next_scheduled_action( Upsell_Order_Bump_Reco_Db::TRAIN_HOOK, array(), Upsell_Order_Bump_Reco_Db::AS_GROUP ) );
	}

	/**
	 * Test: Order statuses filter falls back.
	 */
	public function test_order_statuses_filter_falls_back() {
		$this->assertSame( array( 'wc-completed', 'wc-processing' ), Upsell_Order_Bump_Reco_Engine::order_statuses() );
		add_filter( 'wps_ubo_reco_order_statuses', '__return_empty_array' );
		$this->assertSame( array( 'wc-completed' ), Upsell_Order_Bump_Reco_Engine::order_statuses() );
		remove_filter( 'wps_ubo_reco_order_statuses', '__return_empty_array' );
	}

	/**
	 * Test: Train refuses when locked.
	 */
	public function test_train_refuses_when_locked() {
		$this->assertTrue( Upsell_Order_Bump_Reco_Engine::acquire_lock() );
		$result = Upsell_Order_Bump_Reco_Engine::train();
		$this->assertWPError( $result );
		$this->assertSame( 'wps_ubo_reco_locked', $result->get_error_code() );
		Upsell_Order_Bump_Reco_Engine::release_lock();
	}

	/* ------------------------------------------------------------ scoring */

	/**
	 * Test: Score combines anchors with noisy or.
	 */
	public function test_score_combines_anchors_with_noisy_or() {
		$a = WPS_UBO_Test_Factory::simple_product();
		$b = WPS_UBO_Test_Factory::simple_product();
		$t = WPS_UBO_Test_Factory::simple_product();
		$this->pair( $a->get_id(), $t->get_id(), 0.5 );
		$this->pair( $b->get_id(), $t->get_id(), 0.2 );

		$one = Upsell_Order_Bump_Reco_Engine::score_for( $t->get_id(), array( $a->get_id() ) );
		$this->assertEqualsWithDelta( 0.5, $one['score'], 0.0001 );
		$this->assertSame( $a->get_id(), $one['anchor'] );

		// 1 - (1-0.5)(1-0.2) = 0.6, strongest anchor A.
		$both = Upsell_Order_Bump_Reco_Engine::score_for( $t->get_id(), array( $a->get_id(), $b->get_id() ) );
		$this->assertEqualsWithDelta( 0.6, $both['score'], 0.0001 );
		$this->assertSame( $a->get_id(), $both['anchor'] );

		$none = Upsell_Order_Bump_Reco_Engine::score_for( $t->get_id(), array( 999999 ) );
		$this->assertSame( 0.0, $none['score'] );
	}

	/**
	 * Test: Score for variation uses parent.
	 */
	public function test_score_for_variation_uses_parent() {
		$a   = WPS_UBO_Test_Factory::simple_product();
		$var = WPS_UBO_Test_Factory::variable_product();
		$this->pair( $a->get_id(), $var->get_id(), 0.4 );
		$child = $var->get_children()[0];
		$this->assertSame( $var->get_id(), Upsell_Order_Bump_Reco_Engine::parent_id( $child ) );
		$this->assertEqualsWithDelta( 0.4, Upsell_Order_Bump_Reco_Engine::score_for( $child, array( $a->get_id() ) )['score'], 0.0001 );
	}

	/**
	 * Test: Recommend orders excludes and validates.
	 */
	public function test_recommend_orders_excludes_and_validates() {
		$a     = WPS_UBO_Test_Factory::simple_product();
		$best  = WPS_UBO_Test_Factory::simple_product();
		$mid   = WPS_UBO_Test_Factory::simple_product();
		$oos   = WPS_UBO_Test_Factory::simple_product( array( 'stock_status' => 'outofstock' ) );
		$draft = WPS_UBO_Test_Factory::simple_product();
		wp_update_post(
			array(
				'ID' => $draft->get_id(),
				'post_status' => 'draft',
			)
		);

		$this->pair( $a->get_id(), $mid->get_id(), 0.3 );
		$this->pair(
			$a->get_id(),
			$best->get_id(),
			0.9,
			array(
				'ai_score' => 0.8,
				'ai_reason' => 'Pairs well',
			)
		);
		$this->pair( $a->get_id(), $oos->get_id(), 0.95 );
		$this->pair( $a->get_id(), $draft->get_id(), 0.97 );

		$recs = Upsell_Order_Bump_Reco_Engine::recommend( array( $a->get_id() ) );
		$this->assertSame( array( $best->get_id(), $mid->get_id() ), wp_list_pluck( $recs, 'id' ) );
		$this->assertSame( 'Pairs well', $recs[0]['reason'] );
		$this->assertSame( 0.8, $recs[0]['ai'] );
		$this->assertNull( $recs[1]['ai'] );

		$this->assertSame( array( $mid->get_id() ), wp_list_pluck( Upsell_Order_Bump_Reco_Engine::recommend( array( $a->get_id() ), array( 'exclude' => array( $best->get_id() ) ) ), 'id' ) );
		$this->assertCount( 1, Upsell_Order_Bump_Reco_Engine::recommend( array( $a->get_id() ), array( 'limit' => 1 ) ) );
		$this->assertCount( 4, Upsell_Order_Bump_Reco_Engine::recommend( array( $a->get_id() ), array( 'validate' => false ) ) );
		$this->assertSame( array(), Upsell_Order_Bump_Reco_Engine::recommend( array() ) );
	}

	/**
	 * Test: Recommend never returns context products.
	 */
	public function test_recommend_never_returns_context_products() {
		$a = WPS_UBO_Test_Factory::simple_product();
		$b = WPS_UBO_Test_Factory::simple_product();
		$this->pair( $a->get_id(), $b->get_id(), 0.9 );
		$this->assertSame( array(), Upsell_Order_Bump_Reco_Engine::recommend( array( $a->get_id(), $b->get_id() ) ) );
	}

	/**
	 * Test: Recommend skips already purchased for customer.
	 */
	public function test_recommend_skips_already_purchased_for_customer() {
		update_option(
			Upsell_Order_Bump_Reco_Db::SETTINGS_OPTION,
			Upsell_Order_Bump_Reco_Db::sanitize_settings(
				array(
					'enabled' => 'on',
					'exclude_purchased' => 'on',
				)
			)
		);
		$user = self::factory()->user->create(
			array(
				'role' => 'customer',
				'user_email' => 'bought@example.org',
			)
		);
		$a    = WPS_UBO_Test_Factory::simple_product();
		$b    = WPS_UBO_Test_Factory::simple_product();
		$this->pair( $a->get_id(), $b->get_id(), 0.9 );
		$order = WPS_UBO_Test_Factory::order( array( $b ), 'completed', array( 'email' => 'bought@example.org' ) );
		$order->set_customer_id( $user );
		$order->save();

		$this->assertCount( 1, Upsell_Order_Bump_Reco_Engine::recommend( array( $a->get_id() ) ) );
		$this->assertCount( 0, Upsell_Order_Bump_Reco_Engine::recommend( array( $a->get_id() ), array( 'customer_id' => $user ) ) );
	}

	/**
	 * Test: Top pairs.
	 */
	public function test_top_pairs() {
		$this->pair( 1, 2, 0.2 );
		$this->pair( 1, 3, 0.7 );
		$top = Upsell_Order_Bump_Reco_Engine::top_pairs( 1 );
		$this->assertCount( 1, $top );
		$this->assertSame( '3', (string) $top[0]['target_id'] );
	}

	/* ------------------------------------------------- public / offer swap */

	/**
	 * Test: Targeting mode and product source.
	 */
	public function test_targeting_mode_and_product_source() {
		$this->assertSame( 'static', Upsell_Order_Bump_Reco_Public::targeting_mode( array() ) );
		$this->assertSame( 'hybrid', Upsell_Order_Bump_Reco_Public::targeting_mode( array( 'wps_ubo_targeting_mode' => 'hybrid' ) ) );
		$this->assertSame( 'static', Upsell_Order_Bump_Reco_Public::targeting_mode( array( 'wps_ubo_targeting_mode' => 'weird' ) ) );
		$this->assertSame( 'static', Upsell_Order_Bump_Reco_Public::targeting_mode( 'not-array' ) );
		$this->assertSame( 'ai', Upsell_Order_Bump_Reco_Public::product_source( array( 'wps_ubo_offer_product_source' => 'ai' ) ) );
		$this->assertSame( 'static', Upsell_Order_Bump_Reco_Public::product_source( array( 'wps_ubo_offer_product_source' => 'x' ) ) );
	}

	/**
	 * Test: Cart context IDs ignore bump items.
	 */
	public function test_cart_context_ids_ignore_bump_items() {
		$a = WPS_UBO_Test_Factory::simple_product();
		$b = WPS_UBO_Test_Factory::simple_product();
		WC()->cart->add_to_cart( $b->get_id() );
		WC()->cart->add_to_cart( $a->get_id() );
		WC()->cart->add_to_cart( WPS_UBO_Test_Factory::simple_product()->get_id(), 1, 0, array(), array( 'wps_ubo_offer_product' => true ) );
		$expected = array( $a->get_id(), $b->get_id() );
		sort( $expected );
		$this->assertSame( $expected, Upsell_Order_Bump_Reco_Public::cart_context_ids() );
	}

	/**
	 * Test: Bump list read filter swaps AI product and guard restores.
	 */
	public function test_bump_list_read_filter_swaps_ai_product_and_guard_restores() {
		$this->enable_engine();
		$orig = WPS_UBO_Test_Factory::simple_product();
		$rec  = WPS_UBO_Test_Factory::simple_product();
		update_option(
			'wps_ubo_bump_list',
			array(
				1 => array(
					'wps_upsell_bump_status'            => 'yes',
					'wps_upsell_bump_products_in_offer' => (string) $orig->get_id(),
					'wps_upsell_offer_price_type'       => 'fixed',
					'wps_ubo_offer_product_source'      => 'ai',
				),
				2 => array(
					'wps_upsell_bump_products_in_offer' => (string) $orig->get_id(),
					'wps_upsell_offer_price_type'       => 'fixed',
				),
			)
		);
		WC()->session->set(
			Upsell_Order_Bump_Reco_Public::SESSION_KEY,
			array(
				1 => array(
					'pid' => $rec->get_id(),
					'sig' => 'x',
				),
				2 => array(
					'pid' => $rec->get_id(),
					'sig' => 'x',
				),
			)
		);
		$read = get_option( 'wps_ubo_bump_list' );
		$this->assertSame( (string) $rec->get_id(), $read[1]['wps_upsell_bump_products_in_offer'] );
		$this->assertSame( 'no_disc', $read[1]['wps_upsell_offer_price_type'], 'A fixed price is not carried to another product.' );
		$this->assertSame( (string) $orig->get_id(), $read[2]['wps_upsell_bump_products_in_offer'], 'Static bumps untouched.' );

		update_option( 'wps_ubo_bump_list', $read );
		$raw = Upsell_Order_Bump_Reco_Public::guard_bump_list( $read );
		$this->assertSame( (string) $orig->get_id(), $raw[1]['wps_upsell_bump_products_in_offer'] );
		$this->assertSame( 'fixed', $raw[1]['wps_upsell_offer_price_type'] );
		$this->assertArrayNotHasKey( Upsell_Order_Bump_Reco_Public::MARKER, $raw[1] );
	}

	/**
	 * Test: Funnels guard restores marker.
	 */
	public function test_funnels_guard_restores_marker() {
		$value = array(
			3 => array(
				'wps_wocuf_products_in_offer'    => array( 1 => '99' ),
				'wps_wocuf_offer_discount_price' => array( 1 => '0%' ),
				Upsell_Order_Bump_Reco_Public::MARKER => array(
					'products'  => array( 1 => '10' ),
					'discounts' => array( 1 => '5' ),
				),
			),
		);
		$out = Upsell_Order_Bump_Reco_Public::guard_funnels_list( $value );
		$this->assertSame( array( 1 => '10' ), $out[3]['wps_wocuf_products_in_offer'] );
		$this->assertSame( array( 1 => '5' ), $out[3]['wps_wocuf_offer_discount_price'] );
		$this->assertArrayNotHasKey( Upsell_Order_Bump_Reco_Public::MARKER, $out[3] );
		$this->assertSame( 'x', Upsell_Order_Bump_Reco_Public::guard_funnels_list( 'x' ) );
	}

	/**
	 * Test: Offer product is available.
	 */
	public function test_offer_product_is_available() {
		$p = WPS_UBO_Test_Factory::simple_product();
		$this->assertTrue( Upsell_Order_Bump_Reco_Public::offer_product_is_available( $p->get_id() ) );
		WC()->cart->add_to_cart( $p->get_id() );
		$this->assertFalse( Upsell_Order_Bump_Reco_Public::offer_product_is_available( $p->get_id() ) );
		$this->assertTrue( Upsell_Order_Bump_Reco_Public::offer_product_is_available( $p->get_id(), 'no' ) );
		$this->assertFalse( Upsell_Order_Bump_Reco_Public::offer_product_is_available( 0 ) );
		$oos = WPS_UBO_Test_Factory::simple_product( array( 'stock_status' => 'outofstock' ) );
		$this->assertFalse( Upsell_Order_Bump_Reco_Public::offer_product_is_available( $oos->get_id() ) );
	}

	/* ------------------------------------------------------------- AI parse */

	/**
	 * Test: AI parse handles fences and clamps.
	 */
	public function test_ai_parse_handles_fences_and_clamps() {
		$out = Upsell_Order_Bump_Reco_Ai::parse( "```json\n{\"recommendations\":[{\"product_id\":\"12\",\"score\":1.7,\"reason\":\"<b>Great</b> match\"},{\"product_id\":\"x\",\"score\":0.5},{\"score\":0.4}]}\n```" );
		$this->assertSame(
			array(
				12 => array(
					'score'  => 1.0,
					'reason' => 'Great match',
				),
			),
			$out
		);
		$this->assertWPError( Upsell_Order_Bump_Reco_Ai::parse( 'no json here' ) );
		$this->assertSame( array(), Upsell_Order_Bump_Reco_Ai::parse( '{"recommendations":[]}' ), 'An empty list is a valid answer.' );
	}

	/**
	 * Test: AI connectors empty without wp AI client.
	 */
	public function test_ai_connectors_empty_without_wp_ai_client() {
		if ( Upsell_Order_Bump_Reco_Ai::client_exists() ) {
			$this->markTestSkipped( 'WP AI client present on this install.' );
		}
		$this->assertSame( array(), Upsell_Order_Bump_Reco_Ai::get_ai_connectors() );
	}

	/* ------------------------------------------------------- QA (WPS-7888) */

	/**
	 * Engine on with a published model.
	 *
	 * @param array $extra Extra.
	 */
	private function enable_engine( $extra = array() ) {
		update_option( Upsell_Order_Bump_Reco_Db::SETTINGS_OPTION, Upsell_Order_Bump_Reco_Db::sanitize_settings( array_merge( array( 'enabled' => 'on' ), $extra ) ) );
		Upsell_Order_Bump_Reco_Db::update_model(
			array(
				'trained_at' => time(),
				'pairs' => 1,
			)
		);
	}

	/**
	 * AI-sourced bump #1 offering $offer by default.
	 *
	 * @param mixed $offer Offer.
	 * @param array $extra Extra.
	 */
	private function ai_bump( $offer, $extra = array() ) {
		update_option(
			'wps_ubo_bump_list',
			array(
				1 => array_merge(
					array(
						'wps_upsell_bump_status'            => 'yes',
						'wps_upsell_bump_products_in_offer' => (string) $offer->get_id(),
						'wps_ubo_offer_product_source'      => 'ai',
					),
					$extra
				),
			)
		);
	}

	/**
	 * Test: QA R1 engine off restores admin product and clears session.
	 */
	public function test_qa_r1_engine_off_restores_admin_product_and_clears_session() {
		$this->enable_engine();
		$orig = WPS_UBO_Test_Factory::simple_product();
		$rec  = WPS_UBO_Test_Factory::simple_product();
		$this->ai_bump( $orig );
		WC()->session->set(
			Upsell_Order_Bump_Reco_Public::SESSION_KEY,
			array(
				1 => array(
					'pid' => $rec->get_id(),
					'sig' => 'x',
				),
			)
		);
		$this->assertSame( (string) $rec->get_id(), get_option( 'wps_ubo_bump_list' )[1]['wps_upsell_bump_products_in_offer'] );

		// Switched off: the very next read shows the admin product and the decision is gone.
		update_option( Upsell_Order_Bump_Reco_Db::SETTINGS_OPTION, Upsell_Order_Bump_Reco_Db::sanitize_settings( array( 'enabled' => 'off' ) ) );
		$this->assertSame( (string) $orig->get_id(), get_option( 'wps_ubo_bump_list' )[1]['wps_upsell_bump_products_in_offer'] );
		$this->assertNull( WC()->session->get( Upsell_Order_Bump_Reco_Public::SESSION_KEY ) );

		// Model deleted: same.
		$this->enable_engine();
		WC()->session->set(
			Upsell_Order_Bump_Reco_Public::SESSION_KEY,
			array(
				1 => array(
					'pid' => $rec->get_id(),
					'sig' => 'x',
				),
			)
		);
		Upsell_Order_Bump_Reco_Db::delete_model();
		$this->assertSame( (string) $orig->get_id(), get_option( 'wps_ubo_bump_list' )[1]['wps_upsell_bump_products_in_offer'] );
	}

	/**
	 * Test: QA R2 train now queues background run.
	 */
	public function test_qa_r2_train_now_queues_background_run() {
		delete_option( Upsell_Order_Bump_Reco_Db::MODEL_OPTION );
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );
		$admin = new Upsell_Order_Bump_Reco_Admin( '3.1.8' );
		$r     = $this->ajax_call( array( $admin, 'ajax_train' ), array( 'nonce' => wp_create_nonce( Upsell_Order_Bump_Reco_Admin::NONCE_ACTION ) ) )['json'];

		$this->assertTrue( $r['success'] );
		$this->assertSame( 'queued', $r['data']['model']['status'] );
		$this->assertSame( 0, (int) Upsell_Order_Bump_Reco_Db::get_model()['trained_at'], 'Nothing is trained inside the request.' );
		$this->assertTrue( as_has_scheduled_action( Upsell_Order_Bump_Reco_Db::TRAIN_HOOK, array( 'manual' => 1 ), Upsell_Order_Bump_Reco_Db::AS_GROUP ) );

		// Saving settings (which reschedules the group) keeps the queued run.
		Upsell_Order_Bump_Reco_Db::reschedule();
		$this->assertTrue( as_has_scheduled_action( Upsell_Order_Bump_Reco_Db::TRAIN_HOOK, array( 'manual' => 1 ), Upsell_Order_Bump_Reco_Db::AS_GROUP ) );

		// A second click while a run holds the lock is refused.
		Upsell_Order_Bump_Reco_Engine::acquire_lock();
		$r = $this->ajax_call( array( $admin, 'ajax_train' ), array( 'nonce' => wp_create_nonce( Upsell_Order_Bump_Reco_Admin::NONCE_ACTION ) ) )['json'];
		$this->assertFalse( $r['success'] );
		Upsell_Order_Bump_Reco_Engine::release_lock();
		as_unschedule_all_actions( Upsell_Order_Bump_Reco_Db::TRAIN_HOOK, array(), Upsell_Order_Bump_Reco_Db::AS_GROUP );
	}

	/**
	 * Test: QA R3 lock is exclusive expires and stale status recovers.
	 */
	public function test_qa_r3_lock_is_exclusive_expires_and_stale_status_recovers() {
		global $wpdb;
		$this->assertTrue( Upsell_Order_Bump_Reco_Engine::acquire_lock() );
		$this->assertFalse( Upsell_Order_Bump_Reco_Engine::acquire_lock(), 'Second run cannot take a live lock.' );
		$this->assertTrue( Upsell_Order_Bump_Reco_Engine::is_locked() );

		// Dead process: lock expired without heartbeat → can be taken over.
		$wpdb->update( $wpdb->options, array( 'option_value' => (string) ( time() - 1 ) ), array( 'option_name' => Upsell_Order_Bump_Reco_Engine::LOCK ) );
		$this->assertFalse( Upsell_Order_Bump_Reco_Engine::is_locked() );
		$this->assertTrue( Upsell_Order_Bump_Reco_Engine::acquire_lock() );
		Upsell_Order_Bump_Reco_Engine::release_lock();

		// "training" left behind by a killed run is reported as an error.
		Upsell_Order_Bump_Reco_Db::update_model( array( 'status' => 'training' ) );
		$model = Upsell_Order_Bump_Reco_Engine::recover_stale_status();
		$this->assertSame( 'error', $model['status'] );
		$this->assertNotEmpty( $model['error'] );

		// A live run is left alone.
		Upsell_Order_Bump_Reco_Db::update_model( array( 'status' => 'training' ) );
		Upsell_Order_Bump_Reco_Engine::acquire_lock();
		$this->assertSame( 'training', Upsell_Order_Bump_Reco_Engine::recover_stale_status()['status'] );
		Upsell_Order_Bump_Reco_Engine::release_lock();
	}

	/**
	 * Test: QA R4 parser is robust.
	 */
	public function test_qa_r4_parser_is_robust() {
		$prose = 'Sure! Here you go: {"recommendations":[{"product_id":12,"score":0.8,"reason":"Fits {nicely}"}]} Let me know {see notes}.';
		$this->assertSame( array( 12 ), array_keys( Upsell_Order_Bump_Reco_Ai::parse( $prose ) ) );
		$this->assertSame( 'Fits {nicely}', Upsell_Order_Bump_Reco_Ai::parse( $prose )[12]['reason'] );

		$out = Upsell_Order_Bump_Reco_Ai::parse( '{"recommendations":[{"product_id":-18,"score":0.5},{"product_id":7.5,"score":0.5},{"product_id":9,"score":0.5,"reason":["x"]}]}' );
		$this->assertSame( array( 9 ), array_keys( $out ), 'Negative and non-integer ids are dropped.' );
		$this->assertSame( '', $out[9]['reason'], 'A non-string reason is ignored, no notice.' );

		$this->assertSame( array(), Upsell_Order_Bump_Reco_Ai::parse( "```json\n{\"recommendations\":[]}\n```" ) );
		$this->assertWPError( Upsell_Order_Bump_Reco_Ai::parse( '{"other":1}' ) );
	}

	/**
	 * Test: QA R5 out of stock pick is replaced.
	 */
	public function test_qa_r5_out_of_stock_pick_is_replaced() {
		$this->enable_engine();
		$anchor = WPS_UBO_Test_Factory::simple_product();
		$first  = WPS_UBO_Test_Factory::simple_product();
		$second = WPS_UBO_Test_Factory::simple_product();
		$admin  = WPS_UBO_Test_Factory::simple_product();
		$this->pair( $anchor->get_id(), $first->get_id(), 0.9 );
		$this->pair( $anchor->get_id(), $second->get_id(), 0.5 );
		$this->ai_bump( $admin );
		WC()->cart->add_to_cart( $anchor->get_id() );

		$bump = Upsell_Order_Bump_Reco_Public::prepare_bump( 1, get_option( 'wps_ubo_bump_list' )[1] );
		$this->assertSame( (string) $first->get_id(), $bump['wps_upsell_bump_products_in_offer'] );
		$this->assertTrue( Upsell_Order_Bump_Reco_Public::session_still_valid( 1, $bump ) );

		$first->set_stock_status( 'outofstock' );
		$first->save();
		$this->assertFalse( Upsell_Order_Bump_Reco_Public::session_still_valid( 1, $bump ), 'Shown bump is dropped.' );
		$bump = Upsell_Order_Bump_Reco_Public::prepare_bump( 1, get_option( 'wps_ubo_bump_list' )[1] );
		$this->assertSame( (string) $second->get_id(), $bump['wps_upsell_bump_products_in_offer'], 'Next pick offered, not nothing.' );
	}

	/**
	 * Test: QA R6 guest purchase history by email.
	 */
	public function test_qa_r6_guest_purchase_history_by_email() {
		update_option(
			Upsell_Order_Bump_Reco_Db::SETTINGS_OPTION,
			Upsell_Order_Bump_Reco_Db::sanitize_settings(
				array(
					'enabled' => 'on',
					'exclude_purchased' => 'on',
				)
			)
		);
		$a = WPS_UBO_Test_Factory::simple_product();
		$b = WPS_UBO_Test_Factory::simple_product();
		$this->pair( $a->get_id(), $b->get_id(), 0.9 );
		WPS_UBO_Test_Factory::order( array( $b ), 'completed', array( 'email' => 'guest-buyer@example.org' ) );

		$this->assertCount( 1, Upsell_Order_Bump_Reco_Engine::recommend( array( $a->get_id() ), array( 'email' => 'other@example.org' ) ) );
		$this->assertCount( 0, Upsell_Order_Bump_Reco_Engine::recommend( array( $a->get_id() ), array( 'email' => 'guest-buyer@example.org' ) ) );

		// Checkout email of a guest shopper is used by the bump.
		$this->enable_engine( array( 'exclude_purchased' => 'on' ) );
		$admin = WPS_UBO_Test_Factory::simple_product();
		$this->ai_bump( $admin );
		WC()->cart->add_to_cart( $a->get_id() );
		WC()->customer->set_billing_email( 'guest-buyer@example.org' );
		$bump = Upsell_Order_Bump_Reco_Public::prepare_bump( 1, get_option( 'wps_ubo_bump_list' )[1] );
		$this->assertSame( (string) $admin->get_id(), $bump['wps_upsell_bump_products_in_offer'], 'Already bought: falls back to the admin product.' );
	}

	/**
	 * Test: QA R7 options autoload and no upgrade on AJAX.
	 */
	public function test_qa_r7_options_autoload_and_no_upgrade_on_ajax() {
		global $wpdb;
		Upsell_Order_Bump_Reco_Db::install();
		Upsell_Order_Bump_Reco_Db::update_model( array( 'status' => 'idle' ) );
		$autoload = $wpdb->get_col( $wpdb->prepare( "SELECT autoload FROM {$wpdb->options} WHERE option_name IN (%s, %s)", Upsell_Order_Bump_Reco_Db::SETTINGS_OPTION, Upsell_Order_Bump_Reco_Db::MODEL_OPTION ) );
		$this->assertCount( 2, $autoload );
		foreach ( $autoload as $value ) {
			$this->assertContains( $value, array( 'yes', 'on', 'auto-on' ) );
		}

		update_option( Upsell_Order_Bump_Reco_Db::DB_VERSION_OPTION, 'old' );
		add_filter( 'wp_doing_ajax', '__return_true' );
		Upsell_Order_Bump_Reco_Db::maybe_upgrade();
		remove_filter( 'wp_doing_ajax', '__return_true' );
		$this->assertSame( 'old', get_option( Upsell_Order_Bump_Reco_Db::DB_VERSION_OPTION ) );
		Upsell_Order_Bump_Reco_Db::maybe_upgrade();
		$this->assertSame( Upsell_Order_Bump_Reco_Db::DB_VERSION, get_option( Upsell_Order_Bump_Reco_Db::DB_VERSION_OPTION ) );
	}

	/**
	 * Test: QA R8 hybrid rechecked after cart change.
	 */
	public function test_qa_r8_hybrid_rechecked_after_cart_change() {
		$this->enable_engine( array( 'match_threshold' => 10 ) );
		$anchor = WPS_UBO_Test_Factory::simple_product();
		$target = WPS_UBO_Test_Factory::simple_product();
		$offer  = WPS_UBO_Test_Factory::simple_product();
		$other  = WPS_UBO_Test_Factory::simple_product();
		$this->pair( $anchor->get_id(), $offer->get_id(), 0.9 );
		$bump = array(
			'wps_upsell_bump_status'            => 'yes',
			'wps_upsell_bump_products_in_offer' => (string) $offer->get_id(),
			'wps_ubo_targeting_mode'            => 'hybrid',
			'wps_upsell_bump_target_ids'        => array( (string) $target->get_id() ),
		);

		// Shown by smart match.
		$key = WC()->cart->add_to_cart( $anchor->get_id() );
		WC()->cart->add_to_cart( $other->get_id() );
		$this->assertTrue( Upsell_Order_Bump_Reco_Public::session_still_valid( 1, $bump ) );

		// Anchor removed, no rule matches either: no longer valid.
		WC()->cart->remove_cart_item( $key );
		$this->assertFalse( Upsell_Order_Bump_Reco_Public::session_still_valid( 1, $bump ) );

		// A rule target in the cart keeps it valid.
		WC()->cart->add_to_cart( $target->get_id() );
		$this->assertTrue( Upsell_Order_Bump_Reco_Public::session_still_valid( 1, $bump ) );
	}

	/* --------------------------------------------------------------- admin */

	/**
	 * Test: Admin save bump fields whitelists values.
	 */
	public function test_admin_save_bump_fields_whitelists_values() {
		$admin = new Upsell_Order_Bump_Reco_Admin( '3.1.8' );
		$post  = array(
			'wps_upsell_bump_nonce'        => wp_create_nonce( 'wps_upsell_bump_creation_nonce' ),
			'wps_ubo_targeting_mode'       => 'smart',
			'wps_ubo_offer_product_source' => 'evil',
		);
		$_POST = $post;
		$out   = $admin->save_bump_fields( array( 'keep' => 1 ), 1 );
		$this->assertSame( 'smart', $out['wps_ubo_targeting_mode'] );
		$this->assertSame( 'static', $out['wps_ubo_offer_product_source'] );
		$this->assertSame( 1, $out['keep'] );

		// Without the bump form's nonce the posted values are not used.
		$post['wps_upsell_bump_nonce'] = 'bad';
		$_POST                         = $post;
		$this->assertSame( 'static', $admin->save_bump_fields( array(), 1 )['wps_ubo_targeting_mode'] );

		$_POST = array();
		$this->assertSame( 'static', $admin->save_funnel_fields( array(), 1 )['wps_ubo_targeting_mode'] );
	}

	/**
	 * Test: Admin save settings requires capability and sanitizes.
	 */
	public function test_admin_save_settings_requires_capability_and_sanitizes() {
		$admin = new Upsell_Order_Bump_Reco_Admin( '3.1.8' );
		$post  = array(
			'nonce'    => wp_create_nonce( Upsell_Order_Bump_Reco_Admin::NONCE_ACTION ),
			'settings' => array(
				'enabled'       => 'on',
				'lookback_days' => '3',
			),
		);
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'customer' ) ) );
		$post['nonce'] = wp_create_nonce( Upsell_Order_Bump_Reco_Admin::NONCE_ACTION );
		$this->assertFalse( $this->ajax_call( array( $admin, 'ajax_save_settings' ), $post )['json']['success'] );

		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );
		$post['nonce'] = wp_create_nonce( Upsell_Order_Bump_Reco_Admin::NONCE_ACTION );
		$r             = $this->ajax_call( array( $admin, 'ajax_save_settings' ), $post )['json'];
		$this->assertTrue( $r['success'] );
		$saved = get_option( Upsell_Order_Bump_Reco_Db::SETTINGS_OPTION );
		$this->assertSame( 'on', $saved['enabled'] );
		$this->assertSame( 7, $saved['lookback_days'] );
	}
}
