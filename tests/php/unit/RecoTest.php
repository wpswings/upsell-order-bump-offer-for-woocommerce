<?php
/**
 * WPS-7888 Smart recommendations: settings, model meta, scoring, offer swaps, AI parsing.
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce/Tests
 */

/**
 * @covers Upsell_Order_Bump_Reco_Db
 * @covers Upsell_Order_Bump_Reco_Engine
 * @covers Upsell_Order_Bump_Reco_Public
 * @covers Upsell_Order_Bump_Reco_Ai::parse
 * @covers Upsell_Order_Bump_Reco_Admin
 */
class RecoTest extends WPS_UBO_Test_Case {

	public function set_up() {
		parent::set_up();
		Upsell_Order_Bump_Reco_Engine::flush_cache();
	}

	/**
	 * Insert a scored pair into the live model table.
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

	public function test_defaults() {
		delete_option( Upsell_Order_Bump_Reco_Db::SETTINGS_OPTION );
		$s = Upsell_Order_Bump_Reco_Db::get_settings();
		$this->assertSame( 'off', $s['enabled'] );
		$this->assertSame( 365, $s['lookback_days'] );
		$this->assertSame( 'daily', $s['schedule'] );
		$this->assertFalse( Upsell_Order_Bump_Reco_Db::is_enabled() );
	}

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

	public function test_model_meta_and_has_model() {
		delete_option( Upsell_Order_Bump_Reco_Db::MODEL_OPTION );
		$this->assertSame( 'idle', Upsell_Order_Bump_Reco_Db::get_model()['status'] );
		$this->assertFalse( Upsell_Order_Bump_Reco_Db::has_model() );
		Upsell_Order_Bump_Reco_Db::update_model( array( 'trained_at' => time(), 'pairs' => 0 ) );
		$this->assertFalse( Upsell_Order_Bump_Reco_Db::has_model(), 'A model with zero pairs is not usable.' );
		$meta = Upsell_Order_Bump_Reco_Db::update_model( array( 'pairs' => 4 ) );
		$this->assertTrue( Upsell_Order_Bump_Reco_Db::has_model() );
		$this->assertSame( 4, $meta['pairs'] );
	}

	public function test_reschedule_by_setting() {
		update_option( Upsell_Order_Bump_Reco_Db::SETTINGS_OPTION, Upsell_Order_Bump_Reco_Db::sanitize_settings( array( 'enabled' => 'on', 'schedule' => 'weekly' ) ) );
		Upsell_Order_Bump_Reco_Db::reschedule();
		$this->assertNotFalse( as_next_scheduled_action( Upsell_Order_Bump_Reco_Db::TRAIN_HOOK, array(), Upsell_Order_Bump_Reco_Db::AS_GROUP ) );

		update_option( Upsell_Order_Bump_Reco_Db::SETTINGS_OPTION, Upsell_Order_Bump_Reco_Db::sanitize_settings( array( 'enabled' => 'on', 'schedule' => 'manual' ) ) );
		Upsell_Order_Bump_Reco_Db::reschedule();
		$this->assertFalse( as_next_scheduled_action( Upsell_Order_Bump_Reco_Db::TRAIN_HOOK, array(), Upsell_Order_Bump_Reco_Db::AS_GROUP ) );
	}

	public function test_order_statuses_filter_falls_back() {
		$this->assertSame( array( 'wc-completed', 'wc-processing' ), Upsell_Order_Bump_Reco_Engine::order_statuses() );
		add_filter( 'wps_ubo_reco_order_statuses', '__return_empty_array' );
		$this->assertSame( array( 'wc-completed' ), Upsell_Order_Bump_Reco_Engine::order_statuses() );
		remove_filter( 'wps_ubo_reco_order_statuses', '__return_empty_array' );
	}

	public function test_train_refuses_when_locked() {
		set_transient( Upsell_Order_Bump_Reco_Engine::LOCK, 1, 60 );
		$result = Upsell_Order_Bump_Reco_Engine::train();
		$this->assertWPError( $result );
		$this->assertSame( 'wps_ubo_reco_locked', $result->get_error_code() );
		delete_transient( Upsell_Order_Bump_Reco_Engine::LOCK );
	}

	/* ------------------------------------------------------------ scoring */

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

	public function test_score_for_variation_uses_parent() {
		$a   = WPS_UBO_Test_Factory::simple_product();
		$var = WPS_UBO_Test_Factory::variable_product();
		$this->pair( $a->get_id(), $var->get_id(), 0.4 );
		$child = $var->get_children()[0];
		$this->assertSame( $var->get_id(), Upsell_Order_Bump_Reco_Engine::parent_id( $child ) );
		$this->assertEqualsWithDelta( 0.4, Upsell_Order_Bump_Reco_Engine::score_for( $child, array( $a->get_id() ) )['score'], 0.0001 );
	}

	public function test_recommend_orders_excludes_and_validates() {
		$a     = WPS_UBO_Test_Factory::simple_product();
		$best  = WPS_UBO_Test_Factory::simple_product();
		$mid   = WPS_UBO_Test_Factory::simple_product();
		$oos   = WPS_UBO_Test_Factory::simple_product( array( 'stock_status' => 'outofstock' ) );
		$draft = WPS_UBO_Test_Factory::simple_product();
		wp_update_post( array( 'ID' => $draft->get_id(), 'post_status' => 'draft' ) );

		$this->pair( $a->get_id(), $mid->get_id(), 0.3 );
		$this->pair( $a->get_id(), $best->get_id(), 0.9, array( 'ai_score' => 0.8, 'ai_reason' => 'Pairs well' ) );
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

	public function test_recommend_never_returns_context_products() {
		$a = WPS_UBO_Test_Factory::simple_product();
		$b = WPS_UBO_Test_Factory::simple_product();
		$this->pair( $a->get_id(), $b->get_id(), 0.9 );
		$this->assertSame( array(), Upsell_Order_Bump_Reco_Engine::recommend( array( $a->get_id(), $b->get_id() ) ) );
	}

	public function test_recommend_skips_already_purchased_for_customer() {
		update_option( Upsell_Order_Bump_Reco_Db::SETTINGS_OPTION, Upsell_Order_Bump_Reco_Db::sanitize_settings( array( 'enabled' => 'on', 'exclude_purchased' => 'on' ) ) );
		$user = self::factory()->user->create( array( 'role' => 'customer', 'user_email' => 'bought@example.org' ) );
		$a    = WPS_UBO_Test_Factory::simple_product();
		$b    = WPS_UBO_Test_Factory::simple_product();
		$this->pair( $a->get_id(), $b->get_id(), 0.9 );
		$order = WPS_UBO_Test_Factory::order( array( $b ), 'completed', array( 'email' => 'bought@example.org' ) );
		$order->set_customer_id( $user );
		$order->save();

		$this->assertCount( 1, Upsell_Order_Bump_Reco_Engine::recommend( array( $a->get_id() ) ) );
		$this->assertCount( 0, Upsell_Order_Bump_Reco_Engine::recommend( array( $a->get_id() ), array( 'customer_id' => $user ) ) );
	}

	public function test_top_pairs() {
		$this->pair( 1, 2, 0.2 );
		$this->pair( 1, 3, 0.7 );
		$top = Upsell_Order_Bump_Reco_Engine::top_pairs( 1 );
		$this->assertCount( 1, $top );
		$this->assertSame( '3', (string) $top[0]['target_id'] );
	}

	/* ------------------------------------------------- public / offer swap */

	public function test_targeting_mode_and_product_source() {
		$this->assertSame( 'static', Upsell_Order_Bump_Reco_Public::targeting_mode( array() ) );
		$this->assertSame( 'hybrid', Upsell_Order_Bump_Reco_Public::targeting_mode( array( 'wps_ubo_targeting_mode' => 'hybrid' ) ) );
		$this->assertSame( 'static', Upsell_Order_Bump_Reco_Public::targeting_mode( array( 'wps_ubo_targeting_mode' => 'weird' ) ) );
		$this->assertSame( 'static', Upsell_Order_Bump_Reco_Public::targeting_mode( 'not-array' ) );
		$this->assertSame( 'ai', Upsell_Order_Bump_Reco_Public::product_source( array( 'wps_ubo_offer_product_source' => 'ai' ) ) );
		$this->assertSame( 'static', Upsell_Order_Bump_Reco_Public::product_source( array( 'wps_ubo_offer_product_source' => 'x' ) ) );
	}

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

	public function test_bump_list_read_filter_swaps_ai_product_and_guard_restores() {
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
				1 => array( 'pid' => $rec->get_id(), 'sig' => 'x' ),
				2 => array( 'pid' => $rec->get_id(), 'sig' => 'x' ),
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
		$this->assertWPError( Upsell_Order_Bump_Reco_Ai::parse( '{"recommendations":[]}' ) );
	}

	public function test_ai_connectors_empty_without_wp_ai_client() {
		if ( Upsell_Order_Bump_Reco_Ai::client_exists() ) {
			$this->markTestSkipped( 'WP AI client present on this install.' );
		}
		$this->assertSame( array(), Upsell_Order_Bump_Reco_Ai::get_ai_connectors() );
	}

	/* --------------------------------------------------------------- admin */

	public function test_admin_save_bump_fields_whitelists_values() {
		$admin                                  = new Upsell_Order_Bump_Reco_Admin( '3.1.8' );
		$_POST['wps_ubo_targeting_mode']        = 'smart';
		$_POST['wps_ubo_offer_product_source']  = 'evil';
		$out                                    = $admin->save_bump_fields( array( 'keep' => 1 ), 1 );
		$this->assertSame( 'smart', $out['wps_ubo_targeting_mode'] );
		$this->assertSame( 'static', $out['wps_ubo_offer_product_source'] );
		$this->assertSame( 1, $out['keep'] );
		$_POST = array();
		$this->assertSame( 'static', $admin->save_funnel_fields( array(), 1 )['wps_ubo_targeting_mode'] );
	}

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
