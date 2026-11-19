// @ts-check
/**
 * WPS-7887 Heatmap tracker records interactions with the bump and the report shows them.
 */
const path = require( 'path' );
const { test, expect } = require( '@playwright/test' );
const { wp, STATE_FILE } = require( '../utils/fixtures' );
const { fillCart, openCheckout, state, ajaxResponse, isAjax } = require( '../utils/store' );

test( 'interactions with the bump are tracked and appear in the report', async ( { page, browser } ) => {
	const before = wp( 'heatmap-count' ).count;
	const { products } = state();
	await fillCart( page, [ products.target ] );
	await openCheckout( page );
	const bump = page.locator( '#wps_upsell_offer_main_id_1' );
	await expect( bump ).toBeVisible();
	await page.waitForFunction( () => !! ( /** @type {any} */ ( window ).wpsUboHeatmap ) );

	const tracked = page.waitForRequest( ( r ) => isAjax( r, 'wps_ubo_heatmap_track' ), { timeout: 30_000 } );
	await bump.scrollIntoViewIfNeeded();
	const box = /** @type {{x:number,y:number,width:number,height:number}} */ ( await bump.boundingBox() );
	for ( let i = 0; i < 5; i++ ) {
		await page.mouse.move( box.x + 20 + i * 10, box.y + 15 + i * 5 );
		await page.waitForTimeout( 300 );
	}
	await bump.locator( '.wps_bump_name, h4' ).first().click();
	// The tracker flushes on an interval and on page hide.
	await page.evaluate( () => document.dispatchEvent( new Event( 'visibilitychange' ) ) );
	await page.goto( '/' );
	await tracked;

	await expect.poll( () => wp( 'heatmap-count' ).count, { timeout: 20_000 } ).toBeGreaterThan( before );

	const admin = await browser.newContext( { storageState: path.join( path.dirname( STATE_FILE ), 'admin.json' ) } );
	const ap = await admin.newPage();
	const data = ajaxResponse( ap, 'wps_ubo_heatmap_data', 30_000 );
	await ap.goto( '/wp-admin/admin.php?page=upsell-order-bump-offer-for-woocommerce-heatmaps' );
	const json = await ( await data ).json();
	expect( json.success ).toBe( true );
	expect( json.data.totals.views + json.data.totals.clicks + json.data.totals.hovers ).toBeGreaterThan( 0 );
	await admin.close();
} );

test( 'tracker is not loaded for shop managers', async ( { browser } ) => {
	const admin = await browser.newContext( { storageState: path.join( path.dirname( STATE_FILE ), 'admin.json' ) } );
	const ap = await admin.newPage();
	await ap.goto( '/checkout/' );
	expect( await ap.evaluate( () => typeof ( /** @type {any} */ ( window ).wpsUboHeatmap ) ) ).toBe( 'undefined' );
	await admin.close();
} );

test( 'template filter redraws the preview and the view is kept after a refresh (WPS-8457)', async ( { browser } ) => {
	wp( 'heatmap-seed', '5' );
	const admin = await browser.newContext( { storageState: path.join( path.dirname( STATE_FILE ), 'admin.json' ) } );
	const ap = await admin.newPage();
	const firstData = ajaxResponse( ap, 'wps_ubo_heatmap_data', 30_000 );
	await ap.goto( '/wp-admin/admin.php?page=upsell-order-bump-offer-for-woocommerce-heatmaps' );
	await firstData;

	const template = ap.locator( '#wps-ubo-hm-template' );
	// The bump's current template is listed even without data, the older one because it has data.
	await expect( template.locator( 'option[value="1"]' ) ).toHaveText( /Template 1 \(current\)/ );
	await expect( template.locator( 'option[value="5"]' ) ).toHaveCount( 1 );
	await expect( ap.locator( '#wps-ubo-hm-edit' ) ).toHaveAttribute( 'href', /tab=creation-setting&bump_id=1/ );

	// Choosing template 5 asks for a template 5 preview and shows its 3 clicks.
	const preview = ap.waitForResponse( ( r ) => isAjax( r.request(), 'wps_ubo_heatmap_preview' ) && /template=5|name="template"\r?\n\r?\n5\r?\n/.test( r.request().postData() || '' ) );
	const data = ajaxResponse( ap, 'wps_ubo_heatmap_data', 30_000 );
	await template.selectOption( '5' );
	expect( ( await ( await preview ).json() ).data.template ).toBe( '5' );
	expect( ( await ( await data ).json() ).data.totals.clicks ).toBe( 3 );

	await ap.locator( 'input[name="wps-ubo-hm-mode"][value="hover"]' ).check( { force: true } );
	await ap.locator( '#wps-ubo-hm-days' ).selectOption( '7' );

	// After a refresh the same selections come back.
	const reloaded = ajaxResponse( ap, 'wps_ubo_heatmap_data', 30_000 );
	await ap.reload();
	await reloaded;
	await expect( template ).toHaveValue( '5' );
	await expect( ap.locator( '#wps-ubo-hm-days' ) ).toHaveValue( '7' );
	await expect( ap.locator( 'input[name="wps-ubo-hm-mode"][value="hover"]' ) ).toBeChecked();
	await admin.close();
} );
