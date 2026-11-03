// @ts-check
/**
 * WPS-7890 Native cart abandonment recovery: capture at checkout → abandoned → recovery
 * emails → recovery link restores the cart with the upsell offer → order attributed.
 */
const { test, expect } = require( '@playwright/test' );
const { wp } = require( '../utils/fixtures' );
const { fillCart, cart, openCheckout, placeOrder, state, ajaxResponse } = require( '../utils/store' );

/**
 * Type the email at checkout and wait for the capture request.
 *
 * @param {import('@playwright/test').Page} page  Page.
 * @param {string} email Email.
 */
async function typeEmail( page, email ) {
	const captured = ajaxResponse( page, 'wps_ubo_acr_capture' );
	await page.locator( '.wc-block-checkout #email' ).fill( email );
	await page.locator( '.wc-block-checkout #email' ).press( 'Tab' );
	return ( await captured ).json();
}

/**
 * Click the "Welcome back" offer checkbox; the page reloads after the AJAX call.
 *
 * @param {import('@playwright/test').Page} page Page.
 */
async function toggleOffer( page ) {
	const toggled = ajaxResponse( page, 'wps_ubo_acr_toggle_offer' );
	await page.locator( '.wps-ubo-acr-offer__check' ).click();
	await toggled;
	await page.waitForLoadState( 'load' );
}

test.describe.serial( 'cart recovery', () => {
	const email = `recover.${ Date.now() }@e2e.test`;
	/** @type {any} */
	let mail2;

	test( 'checkout shows the consent notice and captures the guest cart', async ( { page } ) => {
		const { products } = state();
		await fillCart( page, [ products.target ] );
		await openCheckout( page );
		await expect( page.locator( '.wps-ubo-acr-consent' ) ).toBeVisible();
		const res = await typeEmail( page, email );
		expect( res.data.tracked ).toBe( true );

		// A first name typed after the email is sent too.
		const again = ajaxResponse( page, 'wps_ubo_acr_capture' );
		await page.locator( '.wc-block-checkout #billing-first_name' ).fill( 'Riya' );
		await page.locator( '.wc-block-checkout #billing-first_name' ).press( 'Tab' );
		await again;

		const row = wp( 'acr-row', email );
		expect( row.status ).toBe( 'active' );
		expect( row.first_name ).toBe( 'Riya' );
		expect( row.cart.map( ( i ) => i.product_id ) ).toContain( products.target );
	} );

	test( 'idle cart is abandoned and the email sequence goes out', async () => {
		expect( wp( 'acr-abandon', email ).status ).toBe( 'abandoned' );
		const mail1 = wp( 'acr-due', email );
		expect( mail1.subject ).toBeTruthy();
		expect( mail1.hasOffer ).toBe( false );
		mail2 = wp( 'acr-due', email );
		expect( mail2.hasOffer ).toBe( true );
		expect( Number( mail2.row.offer_product ) ).toBe( state().products.recovery );
		expect( Number( mail2.row.offer_discount ) ).toBe( 15 );
	} );

	test( '"Add to my order" link restores the cart with the offer in a new browser', async ( { browser } ) => {
		const { products, baseUrl } = state();
		const ctx = await browser.newContext( { baseURL: baseUrl } );
		const page = await ctx.newPage();
		await page.goto( mail2.offerUrl.replace( /^https?:\/\/[^/]+/, '' ) );
		await expect( page ).toHaveURL( /checkout/ );

		const box = page.locator( '.wps-ubo-acr-offer' );
		await expect( box ).toBeVisible();
		await expect( box ).toContainText( 'Riya' );
		await expect( box ).toContainText( 'E2E Beanie' );
		await expect( page.locator( '.wps-ubo-acr-offer__check' ) ).toBeChecked();

		const c = await cart( page );
		expect( c.items.map( ( i ) => i.id ) ).toEqual( expect.arrayContaining( [ products.target, products.recovery ] ) );
		expect( c.items.find( ( i ) => i.id === products.recovery )?.prices.price ).toBe( '1700' );

		// Untick → offer removed; tick → back.
		await toggleOffer( page );
		await expect.poll( async () => ( await cart( page ) ).items.some( ( i ) => i.id === products.recovery ) ).toBe( false );
		await page.goto( '/checkout/' );
		await expect( page.locator( '.wps-ubo-acr-offer__check' ) ).not.toBeChecked();
		await toggleOffer( page );
		await expect.poll( async () => ( await cart( page ) ).items.some( ( i ) => i.id === products.recovery ) ).toBe( true );

		// Place the order: recovered via the link with the offer accepted.
		await page.goto( '/checkout/' );
		const orderId = await placeOrder( page, email );
		expect( orderId ).toBeGreaterThan( 0 );
		const order = wp( 'order', String( orderId ) );
		expect( Number( order.acr_recovered ) ).toBe( Number( mail2.row.id ) );
		expect( order.items.find( ( i ) => i.name === 'E2E Beanie' )?.acr_offer ).toBeTruthy();

		const row = wp( 'acr-row', email );
		expect( row.status ).toBe( 'recovered' );
		expect( row.recovered_via ).toBe( 'link' );
		expect( Number( row.offer_accepted ) ).toBe( 1 );

		// The link cannot be reused after purchase.
		await page.goto( mail2.recoveryUrl.replace( /^https?:\/\/[^/]+/, '' ) );
		await expect( page ).not.toHaveURL( /checkout/ );
		await expect( page.locator( '.woocommerce-info, .woocommerce-message, .wc-block-components-notice-banner' ).first() ).toContainText( /already been completed/i );
		await ctx.close();
	} );

	test( 'expired / unknown link sends the customer to the cart with a notice', async ( { page } ) => {
		await page.goto( '/?wps_ubo_acr=' + 'a'.repeat( 32 ) );
		await expect( page ).toHaveURL( /cart/ );
		await expect( page.locator( 'body' ) ).toContainText( /expired/i );
	} );

	test( 'opt-out link stops tracking', async ( { page } ) => {
		const { products } = state();
		const optEmail = `optout.${ Date.now() }@e2e.test`;
		await fillCart( page, [ products.target ] );
		await openCheckout( page );
		const opted = ajaxResponse( page, 'wps_ubo_acr_optout' );
		await page.locator( '.wps-ubo-acr-consent a' ).click();
		await opted;
		await page.locator( '.wc-block-checkout #email' ).fill( optEmail );
		await page.locator( '.wc-block-checkout #email' ).press( 'Tab' );
		await page.waitForTimeout( 2000 );
		expect( wp( 'acr-row', optEmail ) ).toBeNull();
	} );

	test( 'signed unsubscribe link stops further emails', async ( { page, browser } ) => {
		const { products } = state();
		const unsubEmail = `unsub.${ Date.now() }@e2e.test`;
		await fillCart( page, [ products.target ] );
		await openCheckout( page );
		await typeEmail( page, unsubEmail );
		wp( 'acr-abandon', unsubEmail );
		const mail = wp( 'acr-due', unsubEmail );

		const ctx = await browser.newContext( { baseURL: state().baseUrl } );
		const guest = await ctx.newPage();
		// Tampered signature: ignored.
		await guest.goto( mail.unsubUrl.replace( /^https?:\/\/[^/]+/, '' ).replace( /sig=[^&]+/, 'sig=forged' ) );
		expect( wp( 'acr-row', unsubEmail ).status ).toBe( 'abandoned' );

		await guest.goto( mail.unsubUrl.replace( /^https?:\/\/[^/]+/, '' ) );
		await expect( guest.locator( 'body' ) ).toContainText( /unsubscribed/i );
		expect( wp( 'acr-row', unsubEmail ).status ).toBe( 'unsubscribed' );
		expect( wp( 'acr-due', unsubEmail ).subject ).toBeNull();
		await ctx.close();
	} );

	test( 'admin cart recovery tab lists the carts and stats', async ( { browser } ) => {
		const ctx = await browser.newContext( { storageState: require( 'path' ).join( __dirname, '../.auth/admin.json' ) } );
		const page = await ctx.newPage();
		await page.goto( '/wp-admin/admin.php?page=upsell-order-bump-offer-for-woocommerce-setting&tab=cart-recovery' );
		await expect( page.locator( '.wps-ubo-acr__stats' ) ).toBeVisible();
		await expect( page.locator( '.wps-ubo-acr__table-wrap' ) ).toContainText( email );
		await ctx.close();
	} );
} );
