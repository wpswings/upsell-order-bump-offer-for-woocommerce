<?php
/**
 * WPS-7887 Offer heatmaps: storage, aggregation, event ingestion and admin endpoints.
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce/Tests
 */

/**
 * @covers Upsell_Order_Bump_Heatmap_Db
 * @covers Upsell_Order_Bump_Heatmap_Public
 * @covers Upsell_Order_Bump_Heatmap_Admin
 */
class HeatmapTest extends WPS_UBO_Test_Case {

	/** @var Upsell_Order_Bump_Heatmap_Public */
	private $pub;

	public function set_up() {
		parent::set_up();
		$this->pub = new Upsell_Order_Bump_Heatmap_Public( '3.1.8' );
		update_option( Upsell_Order_Bump_Heatmap_Db::SETTINGS_OPTION, Upsell_Order_Bump_Heatmap_Db::default_settings() );
		$_SERVER['REMOTE_ADDR'] = '203.0.113.' . wp_rand( 1, 250 );
		update_option(
			'wps_ubo_bump_list',
			array(
				7 => array(
					'wps_upsell_bump_status'    => 'yes',
					'wps_ubo_selected_template' => '3',
				),
			)
		);
		update_option(
			'wps_wocuf_funnels_list',
			array(
				4 => array(
					'wps_wocuf_pro_offer_template'    => array( 1 => 'two' ),
					'wps_wocuf_offer_custom_page_url' => array( 2 => 'https://example.org/x' ),
				),
			)
		);
	}

	private function row( $overrides = array() ) {
		return array_merge(
			array(
				'offer_type'   => 'bump',
				'offer_id'     => 7,
				'sub_offer_id' => 0,
				'template'     => '3',
				'event_type'   => 'click',
				'x'            => 150,
				'y'            => 250,
				'zone'         => 'accept_checkbox',
				'device'       => 'desktop',
				'context'      => 'checkout',
				'session_hash' => md5( 'v1' ),
			),
			$overrides
		);
	}

	private function filters( $overrides = array() ) {
		return array_merge(
			array(
				'offer_type'   => 'bump',
				'offer_id'     => 7,
				'sub_offer_id' => 0,
				'device'       => '',
				'template'     => '',
				'date_from'    => gmdate( 'Y-m-d H:i:s', time() - DAY_IN_SECONDS ),
				'date_to'      => gmdate( 'Y-m-d H:i:s', time() + 60 ),
			),
			$overrides
		);
	}

	private function ingest( $events, $extra = array() ) {
		return $this->ajax_call(
			array( $this->pub, 'ingest' ),
			array_merge(
				array(
					'nonce'  => wp_create_nonce( Upsell_Order_Bump_Heatmap_Public::NONCE_ACTION ),
					'events' => wp_slash( wp_json_encode( $events ) ),
					'device' => 'mobile',
					'vid'    => 'visitor1',
				),
				$extra
			)
		)['json'];
	}

	private function count_rows() {
		global $wpdb;
		return (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . Upsell_Order_Bump_Heatmap_Db::table() ); // phpcs:ignore
	}

	/* ---------------------------------------------------------------- db */

	public function test_default_settings() {
		delete_option( Upsell_Order_Bump_Heatmap_Db::SETTINGS_OPTION );
		$this->assertSame(
			array(
				'enabled'        => 'on',
				'track_hover'    => 'on',
				'sample_rate'    => 100,
				'retention_days' => 90,
			),
			Upsell_Order_Bump_Heatmap_Db::get_settings()
		);
	}

	public function test_insert_events_and_empty_insert() {
		$this->assertSame( 0, Upsell_Order_Bump_Heatmap_Db::insert_events( array() ) );
		$this->assertSame( 2, Upsell_Order_Bump_Heatmap_Db::insert_events( array( $this->row(), $this->row( array( 'event_type' => 'view' ) ) ) ) );
	}

	public function test_grid_buckets_by_hundred_and_caps_at_99() {
		Upsell_Order_Bump_Heatmap_Db::insert_events(
			array(
				$this->row( array( 'x' => 150, 'y' => 250 ) ),
				$this->row( array( 'x' => 199, 'y' => 299 ) ),
				$this->row( array( 'x' => 10000, 'y' => 10000 ) ),
				$this->row( array( 'event_type' => 'hover' ) ),
			)
		);
		$grid = Upsell_Order_Bump_Heatmap_Db::get_grid( $this->filters(), 'click' );
		$this->assertContains( array( 1, 2, 2 ), $grid );
		$this->assertContains( array( 99, 99, 1 ), $grid );
		$this->assertCount( 1, Upsell_Order_Bump_Heatmap_Db::get_grid( $this->filters(), 'hover' ) );
	}

	public function test_zones_and_totals() {
		Upsell_Order_Bump_Heatmap_Db::insert_events(
			array(
				$this->row(),
				$this->row(),
				$this->row( array( 'event_type' => 'hover', 'zone' => 'image' ) ),
				$this->row( array( 'event_type' => 'view', 'zone' => 'other', 'session_hash' => md5( 'v2' ) ) ),
			)
		);
		$zones = Upsell_Order_Bump_Heatmap_Db::get_zones( $this->filters() );
		$this->assertSame( array( 'click' => 2, 'hover' => 0 ), $zones['accept_checkbox'] );
		$this->assertSame( array( 'click' => 0, 'hover' => 1 ), $zones['image'] );
		$this->assertArrayNotHasKey( 'other', $zones, 'Views are not zone interactions.' );

		$this->assertSame(
			array(
				'views'    => 1,
				'clicks'   => 2,
				'hovers'   => 1,
				'visitors' => 2,
			),
			Upsell_Order_Bump_Heatmap_Db::get_totals( $this->filters() )
		);
	}

	public function test_filters_by_device_template_offer_and_date() {
		Upsell_Order_Bump_Heatmap_Db::insert_events(
			array(
				$this->row( array( 'device' => 'mobile' ) ),
				$this->row( array( 'template' => '5' ) ),
				$this->row( array( 'offer_id' => 8 ) ),
			)
		);
		$this->assertSame( 1, Upsell_Order_Bump_Heatmap_Db::get_totals( $this->filters( array( 'device' => 'mobile' ) ) )['clicks'] );
		$this->assertSame( 1, Upsell_Order_Bump_Heatmap_Db::get_totals( $this->filters( array( 'template' => '5' ) ) )['clicks'] );
		$this->assertSame( 2, Upsell_Order_Bump_Heatmap_Db::get_totals( $this->filters() )['clicks'] );
		$this->assertSame( 0, Upsell_Order_Bump_Heatmap_Db::get_totals( $this->filters( array( 'date_to' => '2000-01-01 00:00:00' ) ) )['clicks'] );
		$templates = Upsell_Order_Bump_Heatmap_Db::get_templates_with_data( 'bump', 7, 0 );
		sort( $templates );
		$this->assertSame( array( '3', '5' ), $templates );
	}

	public function test_purge_old_respects_retention() {
		global $wpdb;
		Upsell_Order_Bump_Heatmap_Db::insert_events( array( $this->row(), $this->row() ) );
		$wpdb->query( $wpdb->prepare( 'UPDATE ' . Upsell_Order_Bump_Heatmap_Db::table() . ' SET created_at = %s ORDER BY id ASC LIMIT 1', gmdate( 'Y-m-d H:i:s', time() - 100 * DAY_IN_SECONDS ) ) ); // phpcs:ignore
		Upsell_Order_Bump_Heatmap_Db::purge_old();
		$this->assertSame( 1, $this->count_rows() );
	}

	public function test_purge_cron_scheduled() {
		Upsell_Order_Bump_Heatmap_Db::unschedule_purge();
		$this->assertFalse( wp_next_scheduled( Upsell_Order_Bump_Heatmap_Db::PURGE_CRON ) );
		Upsell_Order_Bump_Heatmap_Db::schedule_purge();
		$this->assertNotFalse( wp_next_scheduled( Upsell_Order_Bump_Heatmap_Db::PURGE_CRON ) );
		$this->assertTrue( has_action( Upsell_Order_Bump_Heatmap_Db::PURGE_CRON ) !== false );
	}

	/* ------------------------------------------------------------ ingest */

	public function test_ingest_stores_valid_events_with_server_side_template() {
		$r = $this->ingest(
			array(
				array( 't' => 'bump', 'e' => 'click', 'c' => 'checkout', 'z' => 'accept_checkbox', 'o' => 7, 'x' => 120, 'y' => 40 ),
				array( 't' => 'bump', 'e' => 'view', 'c' => 'checkout', 'z' => 'image', 'o' => 7, 'x' => 120, 'y' => 40 ),
			)
		);
		$this->assertTrue( $r['success'] );
		$this->assertSame( 2, $r['data']['stored'] );

		global $wpdb;
		$rows = $wpdb->get_results( 'SELECT * FROM ' . Upsell_Order_Bump_Heatmap_Db::table() . ' ORDER BY id', ARRAY_A ); // phpcs:ignore
		$this->assertSame( '3', $rows[0]['template'] );
		$this->assertSame( 'mobile', $rows[0]['device'] );
		$this->assertSame( 32, strlen( $rows[0]['session_hash'] ) );
		$this->assertSame( '0', $rows[1]['x'], 'Views carry no coordinates.' );
		$this->assertSame( 'other', $rows[1]['zone'] );
	}

	public function test_ingest_rejects_invalid_events() {
		$r = $this->ingest(
			array(
				array( 't' => 'bump', 'e' => 'click', 'c' => 'checkout', 'o' => 999 ),
				array( 't' => 'popup', 'e' => 'click', 'c' => 'checkout', 'o' => 7 ),
				array( 't' => 'bump', 'e' => 'scroll', 'c' => 'checkout', 'o' => 7 ),
				array( 't' => 'bump', 'e' => 'click', 'c' => 'evil', 'o' => 7 ),
				'not-an-array',
			)
		);
		$this->assertSame( 0, $r['data']['stored'] );
	}

	public function test_ingest_clamps_coordinates_and_unknown_zone_and_device() {
		$this->ingest(
			array( array( 't' => 'bump', 'e' => 'click', 'c' => 'cart', 'z' => '<script>', 'o' => 7, 'x' => -50, 'y' => 999999 ) ),
			array( 'device' => 'fridge' )
		);
		global $wpdb;
		$row = $wpdb->get_row( 'SELECT * FROM ' . Upsell_Order_Bump_Heatmap_Db::table(), ARRAY_A ); // phpcs:ignore
		$this->assertSame( '0', $row['x'] );
		$this->assertSame( '10000', $row['y'] );
		$this->assertSame( 'other', $row['zone'] );
		$this->assertSame( 'desktop', $row['device'] );
	}

	public function test_ingest_upsell_template_resolution() {
		$this->ingest(
			array(
				array( 't' => 'upsell', 'e' => 'click', 'c' => 'upsell_page', 'z' => 'accept_button', 'o' => 4, 's' => 1 ),
				array( 't' => 'upsell', 'e' => 'click', 'c' => 'upsell_page', 'z' => 'accept_button', 'o' => 4, 's' => 2 ),
				array( 't' => 'upsell', 'e' => 'click', 'c' => 'upsell_page', 'z' => 'accept_button', 'o' => 4, 's' => 3 ),
			)
		);
		global $wpdb;
		$this->assertSame( array( 'two', 'custom', 'default' ), $wpdb->get_col( 'SELECT template FROM ' . Upsell_Order_Bump_Heatmap_Db::table() . ' ORDER BY id' ) ); // phpcs:ignore
	}

	public function test_ingest_caps_events_per_request() {
		$events = array_fill( 0, 150, array( 't' => 'bump', 'e' => 'click', 'c' => 'checkout', 'o' => 7 ) );
		$this->assertSame( Upsell_Order_Bump_Heatmap_Public::MAX_EVENTS_PER_REQUEST, $this->ingest( $events )['data']['stored'] );
	}

	public function test_ingest_ignored_with_bad_nonce_or_disabled_or_garbage() {
		$event = array( array( 't' => 'bump', 'e' => 'click', 'c' => 'checkout', 'o' => 7 ) );
		$this->assertSame( 0, $this->ingest( $event, array( 'nonce' => 'bad' ) )['data']['stored'] );
		$this->assertSame( 0, $this->ingest( 'not json' )['data']['stored'] );

		update_option( Upsell_Order_Bump_Heatmap_Db::SETTINGS_OPTION, array( 'enabled' => 'off' ) );
		$this->assertSame( 0, $this->ingest( $event )['data']['stored'] );
		$this->assertSame( 0, $this->count_rows() );
	}

	public function test_ingest_rate_limited_per_ip() {
		set_transient( 'wps_ubo_hm_rl_' . md5( $_SERVER['REMOTE_ADDR'] ), Upsell_Order_Bump_Heatmap_Public::MAX_REQUESTS_PER_MINUTE, 60 );
		$this->assertSame( 0, $this->ingest( array( array( 't' => 'bump', 'e' => 'click', 'c' => 'checkout', 'o' => 7 ) ) )['data']['stored'] );
	}

	public function test_before_insert_filter_can_drop_rows() {
		add_filter( 'wps_ubo_heatmap_before_insert', '__return_empty_array' );
		$stored = $this->ingest( array( array( 't' => 'bump', 'e' => 'click', 'c' => 'checkout', 'o' => 7 ) ) )['data']['stored'];
		remove_filter( 'wps_ubo_heatmap_before_insert', '__return_empty_array' );
		$this->assertSame( 0, $stored );
	}

	public function test_allowed_zones_include_all_selector_keys() {
		$zones = Upsell_Order_Bump_Heatmap_Public::allowed_zones();
		foreach ( array( 'other', 'accept_checkbox', 'price', 'decline_button', 'timer' ) as $zone ) {
			$this->assertContains( $zone, $zones );
		}
	}

	public function test_admin_bar_hidden_only_for_preview_by_managers() {
		$this->assertTrue( $this->pub->maybe_hide_admin_bar( true ) );
		$_GET['wps_ubo_heatmap_preview'] = 1;
		$this->assertTrue( $this->pub->maybe_hide_admin_bar( true ), 'Guests are not affected.' );
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );
		$this->assertFalse( $this->pub->maybe_hide_admin_bar( true ) );
		unset( $_GET['wps_ubo_heatmap_preview'] );
	}

	public function test_tracker_not_enqueued_outside_offer_pages_or_for_admins() {
		$this->go_to( home_url( '/' ) );
		$this->pub->enqueue_tracker();
		$this->assertFalse( wp_script_is( 'wps-ubo-heatmap-tracker', 'enqueued' ) );
	}

	public function test_tracker_enqueued_on_shortcode_page_with_config() {
		$page = self::factory()->post->create( array( 'post_type' => 'page', 'post_content' => '[wps_bump_offer_shortcode]' ) );
		$this->go_to( get_permalink( $page ) );
		$this->pub->enqueue_tracker();
		$this->assertTrue( wp_script_is( 'wps-ubo-heatmap-tracker', 'enqueued' ) );
		$inline = wp_scripts()->get_data( 'wps-ubo-heatmap-tracker', 'before' );
		$this->assertStringContainsString( '"context":"shortcode"', implode( '', (array) $inline ) );
		wp_dequeue_script( 'wps-ubo-heatmap-tracker' );
	}

	/* ------------------------------------------------------------- admin */

	private function admin_call( $method, $post = array() ) {
		$admin = new Upsell_Order_Bump_Heatmap_Admin( '3.1.8' );
		return $this->ajax_call( array( $admin, $method ), array_merge( array( 'nonce' => wp_create_nonce( Upsell_Order_Bump_Heatmap_Admin::NONCE_ACTION ) ), $post ) )['json'];
	}

	public function test_admin_endpoints_require_capability() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'customer' ) ) );
		$r = $this->admin_call( 'ajax_get_data' );
		$this->assertFalse( $r['success'] );
	}

	public function test_admin_get_data_and_save_settings() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );
		Upsell_Order_Bump_Heatmap_Db::insert_events( array( $this->row(), $this->row( array( 'event_type' => 'view' ) ) ) );

		$r = $this->admin_call( 'ajax_get_data', array( 'offer_type' => 'bump', 'offer_id' => 7, 'days' => 7 ) );
		$this->assertTrue( $r['success'] );
		$this->assertSame( 1, $r['data']['totals']['clicks'] );
		$this->assertSame( 1, $r['data']['totals']['views'] );

		$r = $this->admin_call( 'ajax_save_settings', array( 'enabled' => 'on', 'sample_rate' => 500, 'retention_days' => 0 ) );
		$this->assertSame(
			array(
				'enabled'        => 'on',
				'track_hover'    => 'off',
				'sample_rate'    => 100,
				'retention_days' => 1,
			),
			$r['data']
		);
		$this->assertSame( $r['data'], get_option( Upsell_Order_Bump_Heatmap_Db::SETTINGS_OPTION ) );
	}
}
