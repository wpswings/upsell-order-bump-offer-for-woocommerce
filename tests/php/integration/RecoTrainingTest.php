<?php
/**
 * WPS-7888 Smart recommendations: training from real orders.
 *
 * Training uses TRUNCATE / RENAME TABLE (DDL), which commits the test transaction, so this
 * class removes its own orders/products and model rows in tear_down().
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce/Tests
 */

/**
 * @covers Upsell_Order_Bump_Reco_Engine::train
 */
class RecoTrainingTest extends WPS_UBO_Test_Case {

	/** @var int[] */
	private $orders = array();

	/** @var int[] */
	private $products = array();

	public function tear_down() {
		foreach ( $this->orders as $id ) {
			$order = wc_get_order( $id );
			if ( $order ) {
				$order->delete( true );
			}
		}
		foreach ( $this->products as $id ) {
			wp_delete_post( $id, true );
		}
		Upsell_Order_Bump_Reco_Db::delete_model();
		delete_option( Upsell_Order_Bump_Reco_Db::MODEL_OPTION );
		delete_option( Upsell_Order_Bump_Reco_Db::SETTINGS_OPTION );
		Upsell_Order_Bump_Reco_Engine::release_lock();
		parent::tear_down();
	}

	private function product() {
		$p                = WPS_UBO_Test_Factory::simple_product();
		$this->products[] = $p->get_id();
		return $p;
	}

	private function order( $products, $email ) {
		$order          = WPS_UBO_Test_Factory::order( $products, 'completed', array( 'email' => $email ) );
		$this->orders[] = $order->get_id();
		return $order;
	}

	public function test_train_builds_model_from_baskets() {
		update_option(
			Upsell_Order_Bump_Reco_Db::SETTINGS_OPTION,
			Upsell_Order_Bump_Reco_Db::sanitize_settings(
				array(
					'enabled'         => 'on',
					'min_support'     => 2,
					'min_confidence'  => 10,
					'min_lift'        => 0,
					'seq_window_days' => 0,
					'schedule'        => 'manual',
				)
			)
		);
		$a = $this->product();
		$b = $this->product();
		$c = $this->product();
		$d = $this->product();
		// A+B bought together 3 times; A+C once (below min support); D alone.
		$this->order( array( $a, $b ), 'one@example.org' );
		$this->order( array( $a, $b ), 'two@example.org' );
		$this->order( array( $a, $b, $c ), 'three@example.org' );
		$this->order( array( $d ), 'four@example.org' );

		$meta = Upsell_Order_Bump_Reco_Engine::train();
		$this->assertIsArray( $meta );
		$this->assertSame( 'idle', $meta['status'] );
		$this->assertSame( 4, $meta['orders'] );
		$this->assertSame( 4, $meta['products'] );
		$this->assertGreaterThan( 0, $meta['trained_at'] );
		$this->assertTrue( Upsell_Order_Bump_Reco_Db::has_model() );
		$this->assertFalse( Upsell_Order_Bump_Reco_Engine::is_locked(), 'Lock released.' );

		Upsell_Order_Bump_Reco_Engine::flush_cache();
		$recs = Upsell_Order_Bump_Reco_Engine::recommend( array( $a->get_id() ) );
		$this->assertSame( $b->get_id(), $recs[0]['id'] );
		$this->assertSame( 3, $recs[0]['co'] );
		$this->assertNotContains( $c->get_id(), wp_list_pluck( $recs, 'id' ), 'Pairs under min support are pruned.' );
		$this->assertSame( array(), Upsell_Order_Bump_Reco_Engine::recommend( array( $d->get_id() ) ) );

		// Model version increments on each run.
		$again = Upsell_Order_Bump_Reco_Engine::train();
		$this->assertSame( $meta['version'] + 1, $again['version'] );
	}

	public function test_qa_r7_directional_sequences_and_top_k_in_sql() {
		global $wpdb;
		update_option(
			Upsell_Order_Bump_Reco_Db::SETTINGS_OPTION,
			Upsell_Order_Bump_Reco_Db::sanitize_settings(
				array(
					'enabled'         => 'on',
					'min_support'     => 1,
					'min_confidence'  => 0,
					'min_lift'        => 0,
					'seq_window_days' => 60,
					'seq_weight'      => 50,
					'max_candidates'  => 3,
					'schedule'        => 'manual',
				)
			)
		);
		$a = $this->product();
		$b = $this->product();
		$user = self::factory()->user->create( array( 'role' => 'customer', 'user_email' => 'seq@example.org' ) );

		// Same customer: A first, B ten days later (twice, from two customers would also do).
		$first = $this->order( array( $a ), 'seq@example.org' );
		$first->set_customer_id( $user );
		$first->set_date_created( time() - 20 * DAY_IN_SECONDS );
		$first->save();
		$next = $this->order( array( $b ), 'seq@example.org' );
		$next->set_customer_id( $user );
		$next->set_date_created( time() - 10 * DAY_IN_SECONDS );
		$next->save();

		// Anchor with five co-purchased targets: only the top 3 are kept.
		$anchor  = $this->product();
		$targets = array();
		for ( $i = 0; $i < 5; $i++ ) {
			$targets[] = $this->product();
		}
		$this->order( array_merge( array( $anchor ), $targets ), 'basket@example.org' );
		$this->order( array( $anchor, $targets[0], $targets[1], $targets[2] ), 'basket2@example.org' );

		foreach ( array( 'orders', 'analytics' ) as $expected_source ) {
			if ( 'analytics' === $expected_source ) {
				// Fill the WooCommerce Analytics tables so the SQL training path is used.
				foreach ( $this->orders as $order_id ) {
					\Automattic\WooCommerce\Admin\API\Reports\Customers\DataStore::sync_order_customer( $order_id );
					\Automattic\WooCommerce\Admin\API\Reports\Orders\Stats\DataStore::sync_order( $order_id );
					\Automattic\WooCommerce\Admin\API\Reports\Products\DataStore::sync_order_products( $order_id );
				}
			}
			Upsell_Order_Bump_Reco_Engine::flush_cache();
			$meta = Upsell_Order_Bump_Reco_Engine::train();
			$this->assertIsArray( $meta, is_wp_error( $meta ) ? $meta->get_error_message() : '' );
			$this->assertSame( $expected_source, $meta['source'] );

			$table = Upsell_Order_Bump_Reco_Db::pairs_table();
			$ab    = $wpdb->get_row( $wpdb->prepare( "SELECT seq_count FROM {$table} WHERE source_id = %d AND target_id = %d", $a->get_id(), $b->get_id() ), ARRAY_A );
			$this->assertNotNull( $ab, 'A -> B kept as a "bought next" pair.' );
			$this->assertSame( '1', (string) $ab['seq_count'] );
			$this->assertNull( $wpdb->get_row( $wpdb->prepare( "SELECT 1 FROM {$table} WHERE source_id = %d AND target_id = %d", $b->get_id(), $a->get_id() ) ), 'B -> A is not a sequence.' );

			$kept = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE source_id = %d", $anchor->get_id() ) );
			$this->assertSame( 3, $kept, 'Top-K per anchor.' );
		}
	}

	public function test_train_with_no_orders_produces_empty_model() {
		$meta = Upsell_Order_Bump_Reco_Engine::train();
		$this->assertSame( 0, $meta['orders'] );
		$this->assertSame( 0, $meta['pairs'] );
		$this->assertFalse( Upsell_Order_Bump_Reco_Db::has_model() );
	}

	public function test_delete_model_resets_and_bumps_version() {
		Upsell_Order_Bump_Reco_Db::update_model( array( 'trained_at' => time(), 'pairs' => 3, 'version' => 4 ) );
		Upsell_Order_Bump_Reco_Db::delete_model();
		$meta = Upsell_Order_Bump_Reco_Db::get_model();
		$this->assertSame( 5, $meta['version'] );
		$this->assertSame( 0, $meta['trained_at'] );
		$this->assertFalse( Upsell_Order_Bump_Reco_Db::has_model() );
	}
}
