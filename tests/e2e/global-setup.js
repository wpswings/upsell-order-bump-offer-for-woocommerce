const fs = require( 'fs' );
const path = require( 'path' );
const { chromium } = require( '@playwright/test' );
const { wp, STATE_FILE } = require( './utils/fixtures' );

/**
 * Seed the store and save an authenticated admin session.
 *
 * @param {import('@playwright/test').FullConfig} config Config.
 */
module.exports = async ( config ) => {
	// Recover from an interrupted previous run before seeding again.
	if ( fs.existsSync( STATE_FILE ) ) {
		wp( 'teardown' );
		fs.unlinkSync( STATE_FILE );
	}
	const seeded = wp( 'setup' );
	const baseUrl = ( process.env.WPS_E2E_BASE_URL || seeded.baseUrl ).replace( /\/$/, '' );
	seeded.baseUrl = baseUrl;
	fs.mkdirSync( path.dirname( STATE_FILE ), { recursive: true } );
	fs.writeFileSync( STATE_FILE, JSON.stringify( seeded, null, 2 ) );
	process.env.WPS_E2E_BASE_URL = baseUrl;

	const { channel } = config.projects[ 0 ].use;
	const browser = await chromium.launch( { channel } );
	const page = await browser.newPage( { baseURL: baseUrl } );
	await page.goto( '/wp-login.php' );
	await page.fill( '#user_login', seeded.admin.user );
	await page.fill( '#user_pass', seeded.admin.pass );
	await Promise.all( [ page.waitForURL( /wp-admin/ ), page.click( '#wp-submit' ) ] );
	await page.context().storageState( { path: path.join( path.dirname( STATE_FILE ), 'admin.json' ) } );
	await browser.close();
};
