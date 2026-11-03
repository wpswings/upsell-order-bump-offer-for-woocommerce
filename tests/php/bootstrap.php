<?php
/**
 * PHPUnit bootstrap: WordPress test suite + WooCommerce + this plugin.
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce/Tests
 */

$wps_plugin_dir = dirname( __DIR__, 2 );

require_once $wps_plugin_dir . '/vendor/autoload.php';
require_once $wps_plugin_dir . '/vendor/yoast/phpunit-polyfills/phpunitpolyfills-autoload.php';

if ( ! getenv( 'WP_PHPUNIT__TESTS_CONFIG' ) ) {
	putenv( 'WP_PHPUNIT__TESTS_CONFIG=' . __DIR__ . '/wp-tests-config.php' );
}

$wps_tests_dir = getenv( 'WP_TESTS_DIR' ) ? getenv( 'WP_TESTS_DIR' ) : $wps_plugin_dir . '/vendor/wp-phpunit/wp-phpunit';
require_once $wps_tests_dir . '/includes/functions.php';

$wps_wc_dir = getenv( 'WPS_TEST_WC_DIR' ) ? getenv( 'WPS_TEST_WC_DIR' ) : dirname( $wps_plugin_dir ) . '/woocommerce';
if ( ! file_exists( $wps_wc_dir . '/woocommerce.php' ) ) {
	fwrite( STDERR, "WooCommerce not found at {$wps_wc_dir}. Set WPS_TEST_WC_DIR.\n" ); // phpcs:ignore
	exit( 1 );
}
define( 'WPS_UBO_TESTS_PLUGIN_DIR', $wps_plugin_dir );
define( 'WPS_UBO_TESTS_PLUGIN_FILE', $wps_plugin_dir . '/upsell-order-bump-offer-for-woocommerce.php' );

// The plugin only boots when it sees WooCommerce in active_plugins.
tests_add_filter(
	'pre_option_active_plugins',
	static function () {
		return array( 'woocommerce/woocommerce.php', 'upsell-order-bump-offer-for-woocommerce/upsell-order-bump-offer-for-woocommerce.php' );
	}
);

tests_add_filter(
	'muplugins_loaded',
	static function () use ( $wps_wc_dir ) {
		define( 'WC_TAX_ROUNDING_MODE', 'auto' );
		define( 'WC_USE_TRANSACTIONS', false );
		require_once $wps_wc_dir . '/woocommerce.php';
		require_once WPS_UBO_TESTS_PLUGIN_FILE;
	}
);

// Install WooCommerce + plugin tables once the environment is up.
tests_add_filter(
	'setup_theme',
	static function () {
		WC_Install::install();
		if ( function_exists( 'activate_upsell_order_bump_offer_for_woocommerce' ) ) {
			activate_upsell_order_bump_offer_for_woocommerce();
		}
		foreach ( array( 'Upsell_Order_Bump_Heatmap_Db', 'Upsell_Order_Bump_Reco_Db', 'Upsell_Order_Bump_Acr_Db' ) as $wps_class ) {
			if ( class_exists( $wps_class ) && method_exists( $wps_class, 'install' ) ) {
				$wps_class::install();
			}
		}
		// Reload WC roles/capabilities after install.
		$GLOBALS['wp_roles'] = null; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		wp_roles();
	}
);

// CLI has no headers: stop WooCommerce trying to set cookies.
tests_add_filter( 'woocommerce_set_cookie_enabled', '__return_false' );

// Never send real mail from tests.
tests_add_filter(
	'pre_wp_mail',
	static function ( $short_circuit, $atts ) {
		$GLOBALS['wps_ubo_test_mails'][] = $atts;
		return true;
	},
	10,
	2
);

require $wps_tests_dir . '/includes/bootstrap.php';

require_once __DIR__ . '/helpers/class-wps-ubo-test-factory.php';
require_once __DIR__ . '/helpers/class-wps-ubo-test-case.php';
require_once __DIR__ . '/helpers/class-wps-ubo-ajax-test-case.php';
