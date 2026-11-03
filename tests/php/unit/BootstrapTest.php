<?php
/**
 * Sanity checks that the plugin booted inside the test environment.
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce/Tests
 */

/**
 * @coversNothing
 */
class BootstrapTest extends WPS_UBO_Test_Case {

	public function test_woocommerce_is_loaded() {
		$this->assertTrue( class_exists( 'WooCommerce' ) );
		$this->assertTrue( function_exists( 'WC' ) );
	}

	public function test_plugin_constants_defined() {
		$this->assertSame( '3.1.8', UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_VERSION );
		$this->assertTrue( defined( 'UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_DIR_PATH' ) );
	}

	public function test_core_classes_loaded() {
		foreach ( array(
			'Upsell_Order_Bump_Offer_For_Woocommerce',
			'Upsell_Order_Bump_Offer_For_Woocommerce_Public',
			'Upsell_Order_Bump_Heatmap_Db',
			'Upsell_Order_Bump_Reco_Db',
			'Upsell_Order_Bump_Reco_Engine',
			'Upsell_Order_Bump_Gamified',
			'Upsell_Order_Bump_Acr_Db',
			'Upsell_Order_Bump_Acr_Engine',
			'Upsell_Order_Bump_Acr_Mailer',
		) as $class ) {
			$this->assertTrue( class_exists( $class ), "$class should be loaded" );
		}
	}

	public function test_feature_tables_exist() {
		global $wpdb;
		foreach ( array( Upsell_Order_Bump_Heatmap_Db::table(), Upsell_Order_Bump_Acr_Db::table(), Upsell_Order_Bump_Reco_Db::pairs_table(), Upsell_Order_Bump_Reco_Db::products_table() ) as $table ) {
			$this->assertSame( $table, $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ), "$table missing" );
		}
	}
}
