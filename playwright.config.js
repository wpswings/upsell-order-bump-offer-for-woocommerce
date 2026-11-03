// @ts-check
/**
 * Playwright config for the plugin E2E suite.
 *
 * The suite runs against a real WordPress + WooCommerce site (default: the Local site this
 * plugin lives in). global-setup seeds fixtures and backs up the plugin settings;
 * global-teardown restores them.
 *
 * Env:
 *   WPS_E2E_BASE_URL   Site URL (default: read from WordPress by the fixture script).
 *   WPS_E2E_CHANNEL    Browser channel (default "chrome" = system Chrome; "" = Playwright's Chromium).
 *   PHP_BIN            PHP binary for the fixture script (default: Local's PHP, see bin/wp-php.sh).
 */
const { defineConfig } = require( '@playwright/test' );

const channel = process.env.WPS_E2E_CHANNEL ?? 'chrome';

module.exports = defineConfig( {
	testDir: './tests/e2e/specs',
	outputDir: './tests/e2e/.results',
	globalSetup: require.resolve( './tests/e2e/global-setup.js' ),
	globalTeardown: require.resolve( './tests/e2e/global-teardown.js' ),
	// Specs share one store (bump list, settings): run them one at a time.
	fullyParallel: false,
	workers: 1,
	retries: process.env.CI ? 1 : 0,
	timeout: 90_000,
	expect: { timeout: 15_000 },
	reporter: [ [ 'list' ], [ 'html', { outputFolder: 'tests/e2e/.report', open: 'never' } ] ],
	use: {
		baseURL: process.env.WPS_E2E_BASE_URL || undefined,
		channel: channel || undefined,
		headless: true,
		ignoreHTTPSErrors: true,
		trace: 'retain-on-failure',
		screenshot: 'only-on-failure',
		video: 'off',
		actionTimeout: 15_000,
		navigationTimeout: 45_000,
	},
} );
