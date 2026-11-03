<?php
/**
 * WordPress test-suite configuration.
 *
 * Every value can be overridden with an environment variable so the suite runs
 * on any machine / CI. Defaults target the "upsell-plugin-nov-feature" Local site.
 *
 * WARNING: the test database is DROPPED and re-created on every run. Never point
 * WPS_TEST_DB_NAME at a database that holds real data.
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce/Tests
 */

$wps_env = static function ( $name, $fallback ) {
	$value = getenv( $name );
	return ( false === $value || '' === $value ) ? $fallback : $value;
};

// WordPress core used by the tests: the WP install this plugin lives in.
define( 'ABSPATH', rtrim( $wps_env( 'WPS_TEST_ABSPATH', dirname( __DIR__, 5 ) ), '/' ) . '/' );

define( 'WP_DEFAULT_THEME', 'default' );
define( 'WP_DEBUG', true );

define( 'DB_NAME', $wps_env( 'WPS_TEST_DB_NAME', 'wps_ubo_tests' ) );
define( 'DB_USER', $wps_env( 'WPS_TEST_DB_USER', 'root' ) );
define( 'DB_PASSWORD', $wps_env( 'WPS_TEST_DB_PASSWORD', 'root' ) );
define( 'DB_HOST', $wps_env( 'WPS_TEST_DB_HOST', 'localhost' ) );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

$table_prefix = 'wptests_'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

define( 'WP_TESTS_DOMAIN', 'example.org' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'Upsell Order Bump Tests' );
define( 'WP_PHP_BINARY', $wps_env( 'WPS_TEST_PHP_BINARY', PHP_BINARY ) );
define( 'WPLANG', '' );
