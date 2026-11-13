// @ts-check
/**
 * Admin screens: every tab of the plugin settings page and each feature page loads
 * without PHP errors, and the bump list status toggle works.
 */
const path = require( 'path' );
const { test, expect } = require( '@playwright/test' );
const { wp, STATE_FILE } = require( '../utils/fixtures' );
const { ajaxResponse } = require( '../utils/store' );

test.use( { storageState: path.join( path.dirname( STATE_FILE ), 'admin.json' ) } );

const PAGE = '/wp-admin/admin.php?page=upsell-order-bump-offer-for-woocommerce-setting';

/**
 * Fail on PHP fatals / notices printed into the page.
 *
 * @param {import('@playwright/test').Page} page Page.
 */
async function expectNoPhpErrors( page ) {
	const body = await page.locator( 'body' ).innerText();
	expect( body ).not.toMatch( /Fatal error|Parse error|Warning:|Notice:|There has been a critical error/ );
}

const tabs = [
	[ 'General settings', '&tab=general-setting' ],
	[ 'Order bump global settings', '&tab=global-setting&sub_tab=pre-global-sect' ],
	[ 'Upsell global settings', '&tab=global-setting&sub_tab=post-global-sect' ],
	[ 'Order bump list', '&tab=order-bump-section&sub_tab=pre-list-offer-section' ],
	[ 'Upsell funnel list', '&tab=one-click-section&sub_tab=post-list-offer-section' ],
	[ 'Shortcodes', '&tab=shortcode-section' ],
	[ 'Spin & Scratch', '&tab=gamified-offers' ],
	[ 'Cart Recovery', '&tab=cart-recovery' ],
];

for ( const [ name, query ] of tabs ) {
	test( `settings tab loads: ${ name }`, async ( { page } ) => {
		const response = await page.goto( PAGE + query );
		expect( response?.status() ).toBeLessThan( 400 );
		await expect( page.locator( '#wpbody-content' ) ).toBeVisible();
		await expectNoPhpErrors( page );
	} );
}

test( 'feature tabs are linked in the plugin navigation', async ( { page } ) => {
	await page.goto( PAGE + '&tab=general-setting' );
	await expect( page.locator( 'a.nav-tab', { hasText: 'Cart Recovery' } ) ).toHaveCount( 1 );
	await expect( page.locator( 'a.nav-tab[href*="tab=gamified-offers"]' ) ).toHaveCount( 1 );
} );

test( 'heatmaps report page loads', async ( { page } ) => {
	await page.goto( '/wp-admin/admin.php?page=upsell-order-bump-offer-for-woocommerce-heatmaps' );
	await expectNoPhpErrors( page );
	await expect( page.locator( '#wpbody-content' ) ).toContainText( /heatmap/i );
} );

test( 'smart recommendations page loads', async ( { page } ) => {
	await page.goto( '/wp-admin/admin.php?page=upsell-order-bump-offer-for-woocommerce-smart-reco' );
	await expectNoPhpErrors( page );
	await expect( page.locator( '#wpbody-content' ) ).toContainText( /recommend/i );
} );

test( 'bump list shows the seeded bump and the status toggle saves', async ( { page } ) => {
	await page.goto( PAGE + '&tab=order-bump-section&sub_tab=pre-list-offer-section' );
	await expect( page.locator( '#wpbody-content' ) ).toContainText( 'E2E bump' );
	const toggle = page.locator( 'input.wps-ubo-status-toggle[data-bump-id="1"]' );
	await expect( toggle ).toHaveCount( 1 );

	const toggled = ajaxResponse( page, 'wps_ubo_toggle_bump_status' );
	await toggle.evaluate( ( el ) => el.click() );
	await toggled;
	expect( wp( 'bump' ).wps_upsell_bump_status ).toBe( 'no' );

	const back = ajaxResponse( page, 'wps_ubo_toggle_bump_status' );
	await toggle.evaluate( ( el ) => el.click() );
	await back;
	expect( wp( 'bump' ).wps_upsell_bump_status ).toBe( 'yes' );
} );

test( 'cart recovery settings save and keep values', async ( { page } ) => {
	await page.goto( PAGE + '&tab=cart-recovery' );
	// The tab opens on the Dashboard; settings live in the "Abandoned carts" section.
	await page.locator( '#wps-ubo-acr-tab-settings' ).click();
	await expect( page ).toHaveURL( /acr_section=settings/ );
	const minutes = page.locator( '[name="acr[abandon_minutes]"]' );
	await minutes.fill( '45' );
	await expect( page.locator( '.wps-ubo-acr__dirty' ) ).toBeVisible();
	await Promise.all( [ page.waitForLoadState( 'load' ), page.locator( '.wps-ubo-acr__form .button-primary' ).first().click() ] );
	await expect( page ).toHaveURL( /acr_notice=saved/ );
	// Saving returns to the same section.
	await expect( page ).toHaveURL( /acr_section=settings/ );
	await expect( page.locator( '[name="acr[abandon_minutes]"]' ) ).toBeVisible();
	await expect( page.locator( '[name="acr[abandon_minutes]"]' ) ).toHaveValue( '45' );
	await expect( page.locator( '.wps-ubo-acr__notice' ).first() ).toBeVisible();
} );

test( 'cart recovery email preview renders', async ( { page } ) => {
	await page.goto( PAGE + '&tab=cart-recovery' );
	const href = await page.locator( '.wps-ubo-acr__step-actions a' ).first().getAttribute( 'href' );
	expect( href ).toContain( 'wps_ubo_acr_preview' );
	await page.goto( /** @type {string} */ ( href ) );
	await expect( page.locator( 'body' ) ).toContainText( /cart/i );
} );
