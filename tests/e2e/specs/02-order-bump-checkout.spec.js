// @ts-check
/**
 * Core order bump on the (block) checkout: shown for the target product, accepted at the
 * discounted price, removable, and recorded on the order.
 */
const { test, expect } = require( '@playwright/test' );
const { wp } = require( '../utils/fixtures' );
const { fillCart, cart, openCheckout, waitForBump, placeOrder, state, ajaxResponse } = require( '../utils/store' );

const BUMP = '#wps_upsell_offer_main_id_1';
const CHECKBOX = `${ BUMP } input.add_offer_in_cart`;

/**
 * @param {Awaited<ReturnType<typeof cart>>} c Cart.
 * @param {number} id Product id.
 */
const line = ( c, id ) => c.items.find( ( i ) => i.id === id );

/**
 * The checkbox is visually replaced by a styled box; click it the way the label does.
 *
 * @param {import('@playwright/test').Page} page Page.
 */
async function clickBump( page, settle = true ) {
	await expect( page.locator( CHECKBOX ) ).toBeAttached();
	if ( settle ) {
		await waitForBump( page );
	}
	await page.locator( CHECKBOX ).evaluate( ( el ) => /** @type {HTMLInputElement} */ ( el ).click() );
}

test.describe( 'order bump at checkout', () => {
	test( 'bump is not shown without its target product', async ( { page } ) => {
		const { products } = state();
		await fillCart( page, [ products.recovery ] );
		await openCheckout( page );
		await page.waitForTimeout( 1500 );
		await expect( page.locator( BUMP ) ).toHaveCount( 0 );
	} );

	test( 'bump shown for the target, accepted at 20% off, then removed', async ( { page } ) => {
		const { products } = state();
		await fillCart( page, [ products.target ] );
		await openCheckout( page );

		const bump = page.locator( BUMP );
		await expect( bump ).toBeVisible();
		await expect( bump ).toContainText( 'E2E Lens Cap' );

		const added = ajaxResponse( page, 'add_offer_in_cart' );
		await clickBump( page );
		expect( await ( await added ).json() ).toBe( 'added' );

		await expect.poll( async () => line( await cart( page ), products.offer )?.prices.price ).toBe( '4000' );
		await expect( page.locator( '.wc-block-components-order-summary' ).first() ).toContainText( 'E2E Lens Cap', { timeout: 30_000 } );
		await expect( page.locator( CHECKBOX ) ).toBeChecked();
		// After a reload the server renders the box ticked from the session.
		await page.reload();
		await waitForBump( page );
		await expect( page.locator( CHECKBOX ) ).toBeChecked();

		const removed = ajaxResponse( page, 'remove_offer_in_cart' );
		await clickBump( page );
		expect( await ( await removed ).json() ).toBe( 'removed' );
		await expect.poll( async () => line( await cart( page ), products.offer ) ).toBeUndefined();
	} );

	test( 'order with the bump is saved with bump meta and the discounted line total', async ( { page } ) => {
		const { products } = state();
		await fillCart( page, [ products.target ] );
		await openCheckout( page );
		const added = ajaxResponse( page, 'add_offer_in_cart' );
		await clickBump( page );
		await added;
		await expect.poll( async () => !! line( await cart( page ), products.offer ) ).toBe( true );
		await openCheckout( page );

		const orderId = await placeOrder( page, 'bump.buyer@e2e.test' );
		expect( orderId ).toBeGreaterThan( 0 );
		const order = wp( 'order', String( orderId ) );
		const bumpLine = order.items.find( ( i ) => i.name === 'E2E Lens Cap' );
		expect( bumpLine ).toBeTruthy();
		expect( bumpLine.bump ).toBe( 'true' );
		expect( bumpLine.total ).toBeCloseTo( 40, 2 );
		expect( order.bump_order ).toBe( 'true' );
	} );

	test( 'removing the target product also removes the bump', async ( { page } ) => {
		const { products } = state();
		await fillCart( page, [ products.target ] );
		await openCheckout( page );
		const added = ajaxResponse( page, 'add_offer_in_cart' );
		await clickBump( page );
		await added;

		const c = await cart( page );
		const target = c.items.find( ( i ) => i.id === products.target );
		await page.evaluate( async ( key ) => {
			const r = await fetch( '/wp-json/wc/store/v1/cart', { credentials: 'same-origin' } );
			await fetch( '/wp-json/wc/store/v1/cart/remove-item', {
				method: 'POST',
				credentials: 'same-origin',
				headers: { Nonce: /** @type {string} */ ( r.headers.get( 'Nonce' ) ), 'Content-Type': 'application/json' },
				body: JSON.stringify( { key } ),
			} );
		}, /** @type {any} */ ( target ).key );
		expect( ( await cart( page ) ).items ).toHaveLength( 0 );
	} );

	test( 'a disabled (sandbox) bump is not shown', async ( { page, browser } ) => {
		const { products } = state();
		// Toggle via the admin AJAX endpoint the list page uses.
		const admin = await browser.newContext( { storageState: require( 'path' ).join( __dirname, '../.auth/admin.json' ) } );
		const ap = await admin.newPage();
		await ap.goto( '/wp-admin/admin.php?page=upsell-order-bump-offer-for-woocommerce-setting&tab=order-bump-section&sub_tab=pre-list-offer-section' );
		const toggle = ap.locator( 'input.wps-ubo-status-toggle[data-bump-id="1"]' );
		const off = ajaxResponse( ap, 'wps_ubo_toggle_bump_status' );
		await toggle.evaluate( ( el ) => el.click() );
		await off;
		expect( wp( 'bump' ).wps_upsell_bump_status ).toBe( 'no' );

		try {
			await fillCart( page, [ products.target ] );
			await openCheckout( page );
			await page.waitForTimeout( 1500 );
			await expect( page.locator( BUMP ) ).toHaveCount( 0 );
		} finally {
			const on = ajaxResponse( ap, 'wps_ubo_toggle_bump_status' );
			await toggle.evaluate( ( el ) => el.click() );
			await on;
			await admin.close();
		}
		expect( wp( 'bump' ).wps_upsell_bump_status ).toBe( 'yes' );
	} );

	/*
	 * Known issue (block checkout): the bump is moved into place ~1s after load by copying its
	 * HTML. A tick in that first second is lost (the box shows unticked although the offer was
	 * added), and ticking again adds the offer a second time.
	 * Marked test.fail() so the suite stays green and flags when the bug is fixed.
	 */
	test( 'known issue: a tick during the first second after load is kept', async ( { page } ) => {
		test.fail();
		const { products } = state();
		await fillCart( page, [ products.target ] );
		await openCheckout( page );
		const added = ajaxResponse( page, 'add_offer_in_cart' );
		await clickBump( page, false );
		await added;
		await page.waitForTimeout( 2000 );
		await expect( page.locator( CHECKBOX ) ).toBeChecked( { timeout: 2000 } );
	} );
} );
