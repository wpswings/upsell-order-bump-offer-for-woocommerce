<?php
/**
 * Admin side: bump list status toggle, labels, popup settings, import/export helpers,
 * report registration, and the settings forms of the Spin & Scratch and Cart Recovery tabs.
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce/Tests
 */

/**
 * Admin tests.
 *
 * @covers Upsell_Order_Bump_Offer_For_Woocommerce_Admin
 * @covers Upsell_Order_Bump_Gamified_Admin
 * @covers Upsell_Order_Bump_Acr_Admin
 */
class Admin_Test extends WPS_UBO_Test_Case {

	/**
	 * Admin.
	 *
	 * @var Upsell_Order_Bump_Offer_For_Woocommerce_Admin
	 */
	private $admin;

	/**
	 * Set up the test fixture.
	 */
	public function set_up() {
		parent::set_up();
		$this->admin = new Upsell_Order_Bump_Offer_For_Woocommerce_Admin( 'upsell-order-bump-offer-for-woocommerce', '3.1.8' );
	}

	/**
	 * As admin.
	 */
	private function as_admin() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );
	}

	/**
	 * As customer.
	 */
	private function as_customer() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'customer' ) ) );
	}

	/* --------------------------------------------------- bump status toggle */

	/**
	 * Test: Toggle bump status.
	 */
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

	/**
	 * Test: Toggle bump status rejects unknown bump and non admins.
	 */
	public function test_toggle_bump_status_rejects_unknown_bump_and_non_admins() {
		$this->as_admin();
		update_option( 'wps_ubo_bump_list', array() );
		$r = $this->ajax_call(
			array( $this->admin, 'handle_bump_status_toggle' ),
			array(
				'security' => wp_create_nonce( 'wps_admin_nonce' ),
				'bump_id' => '7',
				'status' => 'yes',
			)
		)['json'];
		$this->assertFalse( $r['success'] );

		$this->as_customer();
		WPS_UBO_Test_Factory::bump( WPS_UBO_Test_Factory::simple_product()->get_id() );
		$r = $this->ajax_call(
			array( $this->admin, 'handle_bump_status_toggle' ),
			array(
				'security' => wp_create_nonce( 'wps_admin_nonce' ),
				'bump_id' => '1',
				'status' => 'no',
			)
		)['json'];
		$this->assertFalse( $r['success'] );
		$this->assertSame( 'yes', get_option( 'wps_ubo_bump_list' )[1]['wps_upsell_bump_status'] );
	}

	/**
	 * Test: Toggle bump status bad nonce.
	 */
	public function test_toggle_bump_status_bad_nonce() {
		$this->as_admin();
		$this->assertSame( '-1', trim( $this->ajax_call( array( $this->admin, 'handle_bump_status_toggle' ), array( 'security' => 'bad' ) )['raw'] ) );
	}

	/* --------------------------------------------------------------- labels */

	/**
	 * Test: Create campaign label.
	 */
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
		$this->assertSame(
			array(
				array(
					'name' => 'Black Friday',
					'color' => '#ff0000',
				),
			),
			get_option( 'wps_ubo_global_options' )['wps_bump_label']
		);

		$post['wps_ubo_label_name'] = '  black friday ';
		$this->assertFalse( $this->ajax_call( array( $this->admin, 'wps_ubo_create_label_callback' ), $post )['json']['success'], 'Duplicate names rejected.' );

		$post['wps_ubo_label_name'] = '';
		$this->assertFalse( $this->ajax_call( array( $this->admin, 'wps_ubo_create_label_callback' ), $post )['json']['success'] );

		$post['wps_ubo_label_name']  = 'Summer';
		$post['wps_ubo_label_color'] = '';
		$this->assertFalse( $this->ajax_call( array( $this->admin, 'wps_ubo_create_label_callback' ), $post )['json']['success'] );
	}

	/**
	 * Test: Create label requires capability.
	 */
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

	/**
	 * Test: Save popup settings.
	 */
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

	/**
	 * Test: Last index helpers.
	 */
	public function test_last_index_helpers() {
		$this->assertSame(
			10,
			$this->invoke(
				$this->admin,
				'get_last_bump_index',
				array(
					array(
						3 => 1,
						'a' => 1,
						10 => 1,
					),
				)
			)
		);
		$this->assertSame( 0, $this->invoke( $this->admin, 'get_last_bump_index', array( array() ) ) );
		$this->assertSame(
			10,
			$this->invoke(
				$this->admin,
				'get_last_numeric_index',
				array(
					array(
						3 => 1,
						10 => 1,
					),
				)
			)
		);
	}

	/**
	 * Test: Convert value for CSV.
	 */
	public function test_convert_value_for_csv() {
		$this->assertSame( '1|2', $this->invoke( $this->admin, 'convert_value_for_csv', array( array( 1, 2 ) ) ) );
		$this->assertSame( '{"a":1}', $this->invoke( $this->admin, 'convert_value_for_csv', array( array( 'a' => 1 ) ) ) );
		$this->assertSame( 'yes', $this->invoke( $this->admin, 'convert_value_for_csv', array( true ) ) );
		$this->assertSame( '5', $this->invoke( $this->admin, 'convert_value_for_csv', array( 5 ) ) );
	}

	/**
	 * Test: Normalize bump row.
	 */
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

	/**
	 * Test: Export skip keys and header mapping.
	 */
	public function test_export_skip_keys_and_header_mapping() {
		$this->assertTrue( $this->invoke( $this->admin, 'should_skip_bump_export_key', array( 'Bump_Orders_Count' ) ) );
		$this->assertFalse( $this->invoke( $this->admin, 'should_skip_bump_export_key', array( 'wps_upsell_bump_name' ) ) );
		$this->assertSame( 'wps_upsell_bump_name', $this->invoke( $this->admin, 'map_csv_header_to_key', array( 'Bump Name' ) ) );
	}

	/* ----------------------------------------------------- small filters */

	/**
	 * Test: Screen and report registration.
	 */
	public function test_screen_and_report_registration() {
		$this->assertContains( 'toplevel_page_upsell-order-bump-offer-for-woocommerce-setting', $this->admin->add_wps_frontend_screens( array() ) );
		$this->assertContains( 'display', $this->admin->wocuf_lite_add_style_attribute( array() ) );

		$reports = $this->admin->add_order_bump_reporting( array() );
		$this->assertArrayHasKey( 'wps_order_bump', $reports );
		$this->assertArrayHasKey( 'sales_by_date', $reports['wps_order_bump']['reports'] );
		$this->assertArrayHasKey( 'upsell', $this->admin->add_upsell_reporting( array() ) );
	}

	/**
	 * Test: Core hooks registered.
	 */
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

	/**
	 * Test: Gamified admin saves checkboxes and prizes.
	 */
	public function test_gamified_admin_saves_checkboxes_and_prizes() {
		$this->as_admin();
		$tab   = new Upsell_Order_Bump_Gamified_Admin( '3.1.8' );
		$post = array(
			'wps_ubo_gamified_action' => 'save',
			'wps_ubo_gamified_nonce'  => wp_create_nonce( Upsell_Order_Bump_Gamified_Admin::NONCE_ACTION ),
			'enabled'                 => '1',
			'max_discount'            => '30',
			'formats'                 => array( '4' => 'spin_wheel' ),
			'prizes'                  => array(
				array(
					'label' => 'Five',
					'discount' => '5',
					'weight' => '3',
					'color' => '#111111',
				),
				array(
					'label' => 'Forty',
					'discount' => '40',
					'weight' => '1',
					'color' => '#222222',
				),
			),
		);
		$_POST    = $post;
		$_REQUEST = $post;
		$loc      = $this->redirect_of( array( $tab, 'handle_post' ), $_GET );
		$_POST    = array();
		$this->assertStringContainsString( 'wps_notice=saved', $loc );
		$s = Upsell_Order_Bump_Gamified::get_settings();
		$this->assertSame( 'on', $s['enabled'] );
		$this->assertSame( 'off', $s['allow_skip'], 'Unticked box saved as off.' );
		$this->assertSame( array( 4 => 'spin_wheel' ), $s['formats'] );
		$this->assertSame( 30, $s['prizes'][1]['discount'], 'Prize capped at max discount.' );
	}

	/**
	 * Test: Gamified admin reset stats.
	 */
	public function test_gamified_admin_reset_stats() {
		$this->as_admin();
		Upsell_Order_Bump_Gamified::bump_stat( 1, 'plays' );
		$tab   = new Upsell_Order_Bump_Gamified_Admin( '3.1.8' );
		$post = array(
			'wps_ubo_gamified_action' => 'reset_stats',
			'wps_ubo_gamified_nonce'  => wp_create_nonce( Upsell_Order_Bump_Gamified_Admin::NONCE_ACTION ),
		);
		$_POST    = $post;
		$_REQUEST = $post;
		$this->redirect_of( array( $tab, 'handle_post' ), $_GET );
		$_POST = array();
		$this->assertSame( array(), Upsell_Order_Bump_Gamified::get_stats() );
	}

	/**
	 * Test: Gamified admin ignores other posts and needs nonce.
	 */
	public function test_gamified_admin_ignores_other_posts_and_needs_nonce() {
		$this->as_admin();
		$tab = new Upsell_Order_Bump_Gamified_Admin( '3.1.8' );
		$this->assertSame( '', $this->redirect_of( array( $tab, 'handle_post' ) ) );

		$post = array(
			'wps_ubo_gamified_action' => 'save',
			'wps_ubo_gamified_nonce'  => 'bad',
		);
		$_POST    = $post;
		$_REQUEST = $post;
		$this->expectException( WPDieException::class );
		try {
			$tab->handle_post();
		} finally {
			$_POST = array();
		}
	}

	/* ---------------------------------------------- Cart Recovery admin tab */

	/**
	 * ACR post.
	 *
	 * @param mixed $fields Fields.
	 */
	private function acr_post( $fields ) {
		$post = array_merge(
			array( 'wps_ubo_acr_nonce' => wp_create_nonce( Upsell_Order_Bump_Acr_Admin::NONCE_ACTION ) ),
			$fields
		);
		$_POST    = $post;
		$_REQUEST = $post;
		$loc      = $this->redirect_of( array( new Upsell_Order_Bump_Acr_Admin( '3.1.8' ), 'handle_post' ), array() );
		$_POST    = array();
		return $loc;
	}

	/**
	 * Test: ACR admin save enables and schedules.
	 */
	public function test_acr_admin_save_enables_and_schedules() {
		$this->as_admin();
		$loc = $this->acr_post(
			array(
				'wps_ubo_acr_action' => 'save',
				'acr'                => array(
					'enabled'         => 'on',
					'abandon_minutes' => '45',
					'steps'           => array(
						array(
							'enabled' => 'on',
							'delay' => '2',
							'unit' => 'hours',
						),
					),
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

	/**
	 * Test: ACR admin test email and delete.
	 */
	public function test_acr_admin_test_email_and_delete() {
		$this->as_admin();
		$this->assertStringContainsString(
			'acr_notice=test_sent',
			$this->acr_post(
				array(
					'wps_ubo_acr_action' => 'test',
					'step' => '1',
				)
			)
		);
		$this->assertCount( 1, $this->sent_mails() );

		$id = Upsell_Order_Bump_Acr_Db::insert(
			array(
				'session_key' => 'del',
				'email' => 'd@example.org',
				'cart' => array(),
			)
		);
		$this->assertStringContainsString(
			'acr_notice=deleted',
			$this->acr_post(
				array(
					'wps_ubo_acr_action' => 'delete',
					'cart_id' => (string) $id,
				)
			)
		);
		$this->assertNull( Upsell_Order_Bump_Acr_Db::get_by( 'id', $id ) );
	}

	/**
	 * Test: ACR admin rejects non managers.
	 */
	public function test_acr_admin_rejects_non_managers() {
		$this->as_customer();
		$this->expectException( WPDieException::class );
		$post = array(
			'wps_ubo_acr_action' => 'save',
			'wps_ubo_acr_nonce'  => wp_create_nonce( Upsell_Order_Bump_Acr_Admin::NONCE_ACTION ),
		);
		$_POST    = $post;
		$_REQUEST = $post;
		try {
			( new Upsell_Order_Bump_Acr_Admin( '3.1.8' ) )->handle_post();
		} finally {
			$_POST = array();
		}
	}

	/**
	 * Test: ACR tab renders dashboard and steps.
	 */
	public function test_acr_tab_renders_dashboard_and_steps() {
		$this->as_admin();
		Upsell_Order_Bump_Acr_Db::insert(
			array(
				'session_key' => 'r',
				'email' => 'listed@example.org',
				'cart' => array(),
				'status' => 'abandoned',
				'abandoned_at' => Upsell_Order_Bump_Acr_Db::now(),
			)
		);
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

	/**
	 * Test: ACR tab hidden on other tabs.
	 */
	public function test_acr_tab_hidden_on_other_tabs() {
		$this->as_admin();
		$_GET = array(
			'page' => Upsell_Order_Bump_Acr_Admin::PAGE,
			'tab' => 'general-setting',
		);
		ob_start();
		( new Upsell_Order_Bump_Acr_Admin( '3.1.8' ) )->render_tab_content();
		$html = ob_get_clean();
		$_GET = array();
		$this->assertSame( '', $html );
	}

	/* ------------------------------------------------------- activation */

	/**
	 * Test: Activation creates feature tables and options.
	 */
	public function test_activation_creates_feature_tables_and_options() {
		activate_upsell_order_bump_offer_for_woocommerce();
		$this->assertSame( Upsell_Order_Bump_Acr_Db::DB_VERSION, get_option( Upsell_Order_Bump_Acr_Db::DB_VERSION_OPTION ) );
		$this->assertSame( Upsell_Order_Bump_Heatmap_Db::DB_VERSION, get_option( Upsell_Order_Bump_Heatmap_Db::DB_VERSION_OPTION ) );
		$this->assertSame( Upsell_Order_Bump_Reco_Db::DB_VERSION, get_option( Upsell_Order_Bump_Reco_Db::DB_VERSION_OPTION ) );
		$this->assertNotFalse( get_option( 'wps_ubo_global_options' ) );
	}

	/* ------------------------------------------------ security review ( WPS-7886 ) */

	/**
	 * Run an admin search handler with an optional nonce.
	 *
	 * @param string      $method Handler.
	 * @param string|null $nonce  Search nonce, null for none.
	 * @return array ajax_call() result.
	 */
	private function search( $method, $nonce ) {
		$_GET = array( 'q' => 'SAVE' );
		$post = null === $nonce ? array() : array( 'wps_ubo_search_nonce' => $nonce );
		$r    = $this->ajax_call( array( $this->admin, $method ), $post );
		$_GET = array();
		return $r;
	}

	/**
	 * Test: F3 coupon search needs shop manager and nonce.
	 */
	public function test_f3_coupon_search_needs_shop_manager_and_nonce() {
		$coupon = new WC_Coupon();
		$coupon->set_code( 'SAVE10' );
		$coupon->save();

		$this->as_customer();
		$r = $this->search( 'search_coupon_for_offers', wp_create_nonce( 'wps_ubo_admin_search' ) );
		$this->assertFalse( $r['json']['success'], 'Customers cannot list coupons.' );
		$this->assertStringNotContainsString( 'SAVE10', $r['raw'] );

		$this->as_admin();
		$this->assertSame( '-1', trim( $this->search( 'search_coupon_for_offers', null )['raw'] ), 'No nonce: refused.' );
		$this->assertSame( '-1', trim( $this->search( 'search_coupon_for_offers', 'bad' )['raw'] ) );

		$r = $this->search( 'search_coupon_for_offers', wp_create_nonce( 'wps_ubo_admin_search' ) );
		$this->assertSame( $coupon->get_id(), (int) $r['json'][0][0] );
	}

	/**
	 * Test: F3 product searches refuse customers.
	 */
	public function test_f3_product_searches_refuse_customers() {
		WPS_UBO_Test_Factory::simple_product( array( 'name' => 'SAVE kayak' ) );
		$this->as_customer();
		$nonce = wp_create_nonce( 'wps_ubo_admin_search' );
		foreach ( array( 'search_products_for_bump', 'search_products_for_offers', 'search_products_for_bump_offer_id', 'search_product_categories_for_bump', 'seach_products_for_funnel', 'search_product_categories_for_funnel', 'seach_products_for_offers' ) as $method ) {
			$r = $this->search( $method, $nonce );
			$this->assertFalse( $r['json']['success'], $method );
		}
	}

	/**
	 * Test: F3 search nonce is attached to free and pro search scripts.
	 */
	public function test_f3_search_nonce_is_attached_to_free_and_pro_search_scripts() {
		$this->as_admin();
		wp_register_script( 'wps-wocuf-pro-add_new-offer-script', 'https://example.org/pro.js', array(), '1', true );
		wp_enqueue_script( 'wps-wocuf-pro-add_new-offer-script' );
		$this->admin->attach_admin_search_nonce();
		$before = implode( '', (array) wp_scripts()->get_data( 'wps-wocuf-pro-add_new-offer-script', 'before' ) );
		$this->assertStringContainsString( 'wps_ubo_search_nonce', $before );
		$this->assertStringContainsString( 'seach_products_for_funnel', $before );
		wp_dequeue_script( 'wps-wocuf-pro-add_new-offer-script' );
		wp_deregister_script( 'wps-wocuf-pro-add_new-offer-script' );
	}

	/**
	 * Test: F4 stats AJAX needs shop manager and nonce.
	 */
	public function test_f4_stats_ajax_needs_shop_manager_and_nonce() {
		$this->as_customer();
		$r = $this->ajax_call( array( $this->admin, 'wps_ajax_get_bump_stats_data' ), array( 'nonce' => wp_create_nonce( 'wps_ubo_reports' ) ) );
		$this->assertFalse( $r['json']['success'] );

		$this->as_admin();
		$this->assertSame( '-1', trim( $this->ajax_call( array( $this->admin, 'wps_ajax_get_upsell_funnel_data' ), array() )['raw'] ) );
	}

	/**
	 * Test: F5 skip onboarding needs nonce and capability.
	 */
	public function test_f5_skip_onboarding_needs_nonce_and_capability() {
		$helper = Wpswings_Onboarding_Helper::get_instance();
		delete_option( 'onboarding-data-skipped' );

		$this->as_customer();
		$this->ajax_call( array( $helper, 'skip_onboarding_popup' ), array( 'nonce' => wp_create_nonce( 'wps_onboarding_nonce' ) ) );
		$this->assertFalse( get_option( 'onboarding-data-skipped' ) );

		wp_set_current_user( 0 );
		$this->assertSame( '-1', trim( $this->ajax_call( array( $helper, 'skip_onboarding_popup' ), array() )['raw'] ) );
		$this->assertFalse( has_action( 'wp_ajax_nopriv_skip_onboarding_popup' ), 'No guest hook.' );

		$this->as_admin();
		$this->ajax_call( array( $helper, 'skip_onboarding_popup' ), array( 'nonce' => wp_create_nonce( 'wps_onboarding_nonce' ) ) );
		$this->assertNotFalse( get_option( 'onboarding-data-skipped' ) );
	}

	/**
	 * Test: F1 no placeholder plugin is created and old one is removed.
	 */
	public function test_f1_no_placeholder_plugin_is_created_and_old_one_is_removed() {
		$this->assertFalse( function_exists( 'wps_create_plugin_folder' ) );
		$this->assertFalse( has_filter( 'plugin_action_links', 'wps_remove_deactivate_option' ) );

		$dir  = WP_PLUGIN_DIR . '/woo-one-click-upsell-funnel';
		$file = $dir . '/woocommerce-one-click-upsell-funnel.php';
		if ( ! is_dir( $dir ) ) {
			mkdir( $dir, 0755, true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_mkdir
		}
		$stub = "<?php\n\t\t\t\t/*\n\t\t\t\tPlugin Name: Woo One Click Upsell Funnel\n\t\t\t\tPlugin URI: https://example.com\n\t\t\t\tDescription: Upsell Funnel Builder.\n\t\t\t\tVersion: 4.2.11\n\t\t\t\tAuthor: WPSwings\n\t\t\t\tAuthor URI: https://wpswings.com/\n\t\t\t\t*/\n\t\t\t\t";
		file_put_contents( $file, $stub ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		update_option( 'active_plugins', array_merge( (array) get_option( 'active_plugins', array() ), array( 'woo-one-click-upsell-funnel/woocommerce-one-click-upsell-funnel.php' ) ) );
		update_option( 'wps_manual_create_upsell', 'done' );

		$this->as_customer();
		wps_ubo_remove_legacy_funnel_stub();
		$this->assertFileExists( $file, 'Only admins who can manage plugins trigger the cleanup.' );

		$this->as_admin();
		wps_ubo_remove_legacy_funnel_stub();
		$this->assertFileDoesNotExist( $file );
		$this->assertNotContains( 'woo-one-click-upsell-funnel/woocommerce-one-click-upsell-funnel.php', (array) get_option( 'active_plugins' ) );
		$this->assertFalse( get_option( 'wps_manual_create_upsell' ) );
	}

	/**
	 * Test: F1 a real plugin in that folder is never touched.
	 */
	public function test_f1_a_real_plugin_in_that_folder_is_never_touched() {
		$dir  = WP_PLUGIN_DIR . '/woo-one-click-upsell-funnel';
		$file = $dir . '/woocommerce-one-click-upsell-funnel.php';
		if ( ! is_dir( $dir ) ) {
			mkdir( $dir, 0755, true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_mkdir
		}
		file_put_contents( $file, "<?php\n/*\nPlugin Name: Woo One Click Upsell Funnel\n*/\nrequire __DIR__ . '/includes/real.php';\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		update_option( 'wps_manual_create_upsell', 'done' );

		$this->as_admin();
		wps_ubo_remove_legacy_funnel_stub();
		$this->assertFileExists( $file );

		unlink( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink
		rmdir( $dir ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_rmdir
	}
}
