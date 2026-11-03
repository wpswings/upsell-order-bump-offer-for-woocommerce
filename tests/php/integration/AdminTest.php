<?php
/**
 * Admin side: bump list status toggle, labels, popup settings, import/export helpers,
 * report registration, and the settings forms of the Spin & Scratch and Cart Recovery tabs.
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce/Tests
 */

/**
 * @covers Upsell_Order_Bump_Offer_For_Woocommerce_Admin
 * @covers Upsell_Order_Bump_Gamified_Admin
 * @covers Upsell_Order_Bump_Acr_Admin
 */
class AdminTest extends WPS_UBO_Test_Case {

	/** @var Upsell_Order_Bump_Offer_For_Woocommerce_Admin */
	private $admin;

	public function set_up() {
		parent::set_up();
		$this->admin = new Upsell_Order_Bump_Offer_For_Woocommerce_Admin( 'upsell-order-bump-offer-for-woocommerce', '3.1.8' );
	}

	private function as_admin() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );
	}

	private function as_customer() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'customer' ) ) );
	}

	/* --------------------------------------------------- bump status toggle */

	public function test_toggle_bump_status() {
		$this->as_admin();
		WPS_UBO_Test_Factory::bump( WPS_UBO_Test_Factory::simple_product()->get_id() );
		$post = array(
			'security' => wp_create_nonce( 'wps_admin_nonce' ),
			'bump_id'  => '1',
			'status'   => 'no',
		);
		$r = $this->ajax_call( array( $this->admin, 'handle_bump_status_toggle' ), $post )['json'];
		$this->assertTrue( $r['success'] );
		$this->assertSame( 'no', $r['data']['status'] );
		$this->assertSame( 'Sandbox', $r['data']['label'] );
		$this->assertSame( 'no', get_option( 'wps_ubo_bump_list' )[1]['wps_upsell_bump_status'] );

		$post['status'] = 'hacked';
		$this->assertSame( 'no', $this->ajax_call( array( $this->admin, 'handle_bump_status_toggle' ), $post )['json']['data']['status'] );

		$post['status'] = 'yes';
		$this->assertSame( 'Live', $this->ajax_call( array( $this->admin, 'handle_bump_status_toggle' ), $post )['json']['data']['label'] );
	}

	public function test_toggle_bump_status_rejects_unknown_bump_and_non_admins() {
		$this->as_admin();
		update_option( 'wps_ubo_bump_list', array() );
		$r = $this->ajax_call( array( $this->admin, 'handle_bump_status_toggle' ), array( 'security' => wp_create_nonce( 'wps_admin_nonce' ), 'bump_id' => '7', 'status' => 'yes' ) )['json'];
		$this->assertFalse( $r['success'] );

		$this->as_customer();
		WPS_UBO_Test_Factory::bump( WPS_UBO_Test_Factory::simple_product()->get_id() );
		$r = $this->ajax_call( array( $this->admin, 'handle_bump_status_toggle' ), array( 'security' => wp_create_nonce( 'wps_admin_nonce' ), 'bump_id' => '1', 'status' => 'no' ) )['json'];
		$this->assertFalse( $r['success'] );
		$this->assertSame( 'yes', get_option( 'wps_ubo_bump_list' )[1]['wps_upsell_bump_status'] );
	}

	public function test_toggle_bump_status_bad_nonce() {
		$this->as_admin();
		$this->assertSame( '-1', trim( $this->ajax_call( array( $this->admin, 'handle_bump_status_toggle' ), array( 'security' => 'bad' ) )['raw'] ) );
	}

	/* --------------------------------------------------------------- labels */

	public function test_create_campaign_label() {
		$this->as_admin();
		update_option( 'wps_ubo_global_options', array() );
		$post = array(
			'nonce'               => wp_create_nonce( 'wps_ubo_labels' ),
			'wps_ubo_label_name'  => 'Black Friday',
			'wps_ubo_label_color' => '#ff0000',
		);
		$r = $this->ajax_call( array( $this->admin, 'wps_ubo_create_label_callback' ), $post )['json'];
		$this->assertTrue( $r['success'] );
		$this->assertSame( array( array( 'name' => 'Black Friday', 'color' => '#ff0000' ) ), get_option( 'wps_ubo_global_options' )['wps_bump_label'] );

		$post['wps_ubo_label_name'] = '  black friday ';
		$this->assertFalse( $this->ajax_call( array( $this->admin, 'wps_ubo_create_label_callback' ), $post )['json']['success'], 'Duplicate names rejected.' );

		$post['wps_ubo_label_name'] = '';
		$this->assertFalse( $this->ajax_call( array( $this->admin, 'wps_ubo_create_label_callback' ), $post )['json']['success'] );

		$post['wps_ubo_label_name']  = 'Summer';
		$post['wps_ubo_label_color'] = '';
		$this->assertFalse( $this->ajax_call( array( $this->admin, 'wps_ubo_create_label_callback' ), $post )['json']['success'] );
	}

	public function test_create_label_requires_capability() {
		$this->as_customer();
		$r = $this->ajax_call(
			array( $this->admin, 'wps_ubo_create_label_callback' ),
			array(
				'nonce'               => wp_create_nonce( 'wps_ubo_labels' ),
				'wps_ubo_label_name'  => 'X',
				'wps_ubo_label_color' => '#000000',
			)
		)['json'];
		$this->assertFalse( $r['success'] );
	}

	public function test_save_popup_settings() {
		$this->as_admin();
		$r = $this->ajax_call(
			array( $this->admin, 'wps_ubo_save_popup_system_settings_callback' ),
			array(
				'nonce'       => wp_create_nonce( 'wps_ubo_labels' ),
				'popup_type'  => '<b>exit</b>',
				'popup_delay' => '5s',
			)
		)['json'];
		$this->assertTrue( $r['success'] );
		$this->assertSame( 'exit', get_option( 'wps_ubo_popup_type' ) );
		$this->assertSame( 5, (int) get_option( 'wps_ubo_popup_delay' ) );
	}

	/* ------------------------------------------------- import/export helpers */

	public function test_last_index_helpers() {
		$this->assertSame( 10, $this->invoke( $this->admin, 'get_last_bump_index', array( array( 3 => 1, 'a' => 1, 10 => 1 ) ) ) );
		$this->assertSame( 0, $this->invoke( $this->admin, 'get_last_bump_index', array( array() ) ) );
		$this->assertSame( 10, $this->invoke( $this->admin, 'get_last_numeric_index', array( array( 3 => 1, 10 => 1 ) ) ) );
	}

	public function test_convert_value_for_csv() {
		$this->assertSame( '1|2', $this->invoke( $this->admin, 'convert_value_for_csv', array( array( 1, 2 ) ) ) );
		$this->assertSame( '{"a":1}', $this->invoke( $this->admin, 'convert_value_for_csv', array( array( 'a' => 1 ) ) ) );
		$this->assertSame( 'yes', $this->invoke( $this->admin, 'convert_value_for_csv', array( true ) ) );
		$this->assertSame( '5', $this->invoke( $this->admin, 'convert_value_for_csv', array( 5 ) ) );
	}

	public function test_normalize_bump_row() {
		$this->assertSame( array(), $this->invoke( $this->admin, 'normalize_bump_row', array( 'x' ) ) );
		$row = $this->invoke(
			$this->admin,
			'normalize_bump_row',
			array(
				array(
					'wps_upsell_bump_target_ids'        => '1|2',
					'wps_upsell_bump_products_in_offer' => '7|8',
				),
			)
		);
		$this->assertSame( array( '1', '2' ), $row['wps_upsell_bump_target_ids'] );
		$this->assertSame( '7', $row['wps_upsell_bump_products_in_offer'] );
	}

	public function test_export_skip_keys_and_header_mapping() {
		$this->assertTrue( $this->invoke( $this->admin, 'should_skip_bump_export_key', array( 'Bump_Orders_Count' ) ) );
		$this->assertFalse( $this->invoke( $this->admin, 'should_skip_bump_export_key', array( 'wps_upsell_bump_name' ) ) );
		$this->assertSame( 'wps_upsell_bump_name', $this->invoke( $this->admin, 'map_csv_header_to_key', array( 'Bump Name' ) ) );
	}

	/* ----------------------------------------------------- small filters */

	public function test_screen_and_report_registration() {
		$this->assertContains( 'toplevel_page_upsell-order-bump-offer-for-woocommerce-setting', $this->admin->add_wps_frontend_screens( array() ) );
		$this->assertContains( 'display', $this->admin->wocuf_lite_add_style_attribute( array() ) );

		$reports = $this->admin->add_order_bump_reporting( array() );
		$this->assertArrayHasKey( 'wps_order_bump', $reports );
		$this->assertArrayHasKey( 'sales_by_date', $reports['wps_order_bump']['reports'] );
		$this->assertArrayHasKey( 'upsell', $this->admin->add_upsell_reporting( array() ) );
	}

	public function test_core_hooks_registered() {
		$this->assertNotFalse( has_action( 'wp_ajax_add_offer_in_cart' ) );
		$this->assertNotFalse( has_action( 'wp_ajax_nopriv_add_offer_in_cart' ) );
		$this->assertNotFalse( has_action( 'wp_ajax_remove_offer_in_cart' ) );
		$this->assertNotFalse( has_action( 'wp_ajax_wps_ubo_toggle_bump_status' ) );
		$this->assertNotFalse( has_action( 'wp_ajax_nopriv_wps_ubo_heatmap_track' ) );
		$this->assertNotFalse( has_action( 'wp_ajax_nopriv_wps_ubo_game_play' ) );
		$this->assertNotFalse( has_action( 'wp_ajax_nopriv_wps_ubo_acr_capture' ) );
		$this->assertNotFalse( has_action( 'wps_ubo_setting_tab' ) );
		$this->assertNotFalse( has_filter( 'option_wps_ubo_bump_list' ) );
	}

	/* --------------------------------------------- Spin & Scratch admin tab */

	public function test_gamified_admin_saves_checkboxes_and_prizes() {
		$this->as_admin();
		$tab   = new Upsell_Order_Bump_Gamified_Admin( '3.1.8' );
		$_POST = array(
			'wps_ubo_gamified_action' => 'save',
			'wps_ubo_gamified_nonce'  => wp_create_nonce( Upsell_Order_Bump_Gamified_Admin::NONCE_ACTION ),
			'enabled'                 => '1',
			'max_discount'            => '30',
			'formats'                 => array( '4' => 'spin_wheel' ),
			'prizes'                  => array(
				array( 'label' => 'Five', 'discount' => '5', 'weight' => '3', 'color' => '#111111' ),
				array( 'label' => 'Forty', 'discount' => '40', 'weight' => '1', 'color' => '#222222' ),
			),
		);
		$_REQUEST = $_POST;
		$loc      = $this->redirect_of( array( $tab, 'handle_post' ), $_GET );
		$_POST    = array();
		$this->assertStringContainsString( 'wps_notice=saved', $loc );
		$s = Upsell_Order_Bump_Gamified::get_settings();
		$this->assertSame( 'on', $s['enabled'] );
		$this->assertSame( 'off', $s['allow_skip'], 'Unticked box saved as off.' );
		$this->assertSame( array( 4 => 'spin_wheel' ), $s['formats'] );
		$this->assertSame( 30, $s['prizes'][1]['discount'], 'Prize capped at max discount.' );
	}

	public function test_gamified_admin_reset_stats() {
		$this->as_admin();
		Upsell_Order_Bump_Gamified::bump_stat( 1, 'plays' );
		$tab   = new Upsell_Order_Bump_Gamified_Admin( '3.1.8' );
		$_POST = array(
			'wps_ubo_gamified_action' => 'reset_stats',
			'wps_ubo_gamified_nonce'  => wp_create_nonce( Upsell_Order_Bump_Gamified_Admin::NONCE_ACTION ),
		);
		$_REQUEST = $_POST;
		$this->redirect_of( array( $tab, 'handle_post' ), $_GET );
		$_POST = array();
		$this->assertSame( array(), Upsell_Order_Bump_Gamified::get_stats() );
	}

	public function test_gamified_admin_ignores_other_posts_and_needs_nonce() {
		$this->as_admin();
		$tab = new Upsell_Order_Bump_Gamified_Admin( '3.1.8' );
		$this->assertSame( '', $this->redirect_of( array( $tab, 'handle_post' ) ) );

		$_POST    = array(
			'wps_ubo_gamified_action' => 'save',
			'wps_ubo_gamified_nonce'  => 'bad',
		);
		$_REQUEST = $_POST;
		$this->expectException( WPDieException::class );
		try {
			$tab->handle_post();
		} finally {
			$_POST = array();
		}
	}

	/* ---------------------------------------------- Cart Recovery admin tab */

	private function acr_post( $fields ) {
		$_POST    = array_merge(
			array( 'wps_ubo_acr_nonce' => wp_create_nonce( Upsell_Order_Bump_Acr_Admin::NONCE_ACTION ) ),
			$fields
		);
		$_REQUEST = $_POST;
		$loc      = $this->redirect_of( array( new Upsell_Order_Bump_Acr_Admin( '3.1.8' ), 'handle_post' ), array() );
		$_POST    = array();
		return $loc;
	}

	public function test_acr_admin_save_enables_and_schedules() {
		$this->as_admin();
		$loc = $this->acr_post(
			array(
				'wps_ubo_acr_action' => 'save',
				'acr'                => array(
					'enabled'         => 'on',
					'abandon_minutes' => '45',
					'steps'           => array( array( 'enabled' => 'on', 'delay' => '2', 'unit' => 'hours' ) ),
				),
			)
		);
		$this->assertStringContainsString( 'acr_notice=saved', $loc );
		$s = Upsell_Order_Bump_Acr_Db::get_settings();
		$this->assertSame( 'on', $s['enabled'] );
		$this->assertSame( 45, $s['abandon_minutes'] );
		$this->assertSame( 2, $s['steps'][0]['delay'] );
		$this->assertNotFalse( as_next_scheduled_action( Upsell_Order_Bump_Acr_Db::CRON_HOOK, array(), Upsell_Order_Bump_Acr_Db::AS_GROUP ) );
	}

	public function test_acr_admin_test_email_and_delete() {
		$this->as_admin();
		$this->assertStringContainsString( 'acr_notice=test_sent', $this->acr_post( array( 'wps_ubo_acr_action' => 'test', 'step' => '1' ) ) );
		$this->assertCount( 1, $this->sent_mails() );

		$id = Upsell_Order_Bump_Acr_Db::insert( array( 'session_key' => 'del', 'email' => 'd@example.org', 'cart' => array() ) );
		$this->assertStringContainsString( 'acr_notice=deleted', $this->acr_post( array( 'wps_ubo_acr_action' => 'delete', 'cart_id' => (string) $id ) ) );
		$this->assertNull( Upsell_Order_Bump_Acr_Db::get_by( 'id', $id ) );
	}

	public function test_acr_admin_rejects_non_managers() {
		$this->as_customer();
		$this->expectException( WPDieException::class );
		$_POST    = array(
			'wps_ubo_acr_action' => 'save',
			'wps_ubo_acr_nonce'  => wp_create_nonce( Upsell_Order_Bump_Acr_Admin::NONCE_ACTION ),
		);
		$_REQUEST = $_POST;
		try {
			( new Upsell_Order_Bump_Acr_Admin( '3.1.8' ) )->handle_post();
		} finally {
			$_POST = array();
		}
	}

	public function test_acr_tab_renders_dashboard_and_steps() {
		$this->as_admin();
		Upsell_Order_Bump_Acr_Db::insert( array( 'session_key' => 'r', 'email' => 'listed@example.org', 'cart' => array(), 'status' => 'abandoned', 'abandoned_at' => Upsell_Order_Bump_Acr_Db::now() ) );
		$_GET = array(
			'page' => Upsell_Order_Bump_Acr_Admin::PAGE,
			'tab'  => Upsell_Order_Bump_Acr_Admin::TAB,
		);
		$tab  = new Upsell_Order_Bump_Acr_Admin( '3.1.8' );
		ob_start();
		$tab->render_tab_link();
		$tab->render_tab_content();
		$html = ob_get_clean();
		$_GET = array();
		$this->assertStringContainsString( 'nav-tab-active', $html );
		$this->assertStringContainsString( 'listed@example.org', $html );
		$this->assertSame( 3, substr_count( $html, 'wps-ubo-acr__step"' ) );
	}

	public function test_acr_tab_hidden_on_other_tabs() {
		$this->as_admin();
		$_GET = array( 'page' => Upsell_Order_Bump_Acr_Admin::PAGE, 'tab' => 'general-setting' );
		ob_start();
		( new Upsell_Order_Bump_Acr_Admin( '3.1.8' ) )->render_tab_content();
		$html = ob_get_clean();
		$_GET = array();
		$this->assertSame( '', $html );
	}

	/* ------------------------------------------------------- activation */

	public function test_activation_creates_feature_tables_and_options() {
		activate_upsell_order_bump_offer_for_woocommerce();
		$this->assertSame( Upsell_Order_Bump_Acr_Db::DB_VERSION, get_option( Upsell_Order_Bump_Acr_Db::DB_VERSION_OPTION ) );
		$this->assertSame( Upsell_Order_Bump_Heatmap_Db::DB_VERSION, get_option( Upsell_Order_Bump_Heatmap_Db::DB_VERSION_OPTION ) );
		$this->assertSame( Upsell_Order_Bump_Reco_Db::DB_VERSION, get_option( Upsell_Order_Bump_Reco_Db::DB_VERSION_OPTION ) );
		$this->assertNotFalse( get_option( 'wps_ubo_global_options' ) );
	}
}
