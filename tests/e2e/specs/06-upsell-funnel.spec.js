// @ts-check
/**
 * Free one-click upsell funnel: after the (block) checkout the customer lands on the offer
 * page; "No thanks" finishes the order as it was, "Add this to my Order" adds the offer to
 * the same order at the funnel discount.
 *
 * The Pro plugin replaces the free funnels with its own ( covered by the Pro suite ), so this
 * spec is skipped on sites where Pro is active.
 */
const { test, expect } = require( '@playwright/test' );
const { wp } = require( '../utils/fixtures' );
const { fillCart, openCheckout, placeOrder, state } = require( '../utils/store' );

const BUY = 'a.e2e-offer-buy';
const NO = 'a.e2e-offer-no';

/** @type {string} */
let offerPage = '';

test.describe( 'free upsell funnel', () => {
	test.skip( () => wp( 'env' ).pro, 'Pro is active: it runs its own funnels.' );

	test.beforeAll( () => {
		offerPage = wp( 'funnel-on' ).offerPage;
	} );

	test.afterAll( () => {
		wp( 'funnel-off' );
	} );

	/**
	 * Check out the funnel target with Cash on delivery and wait for the offer page.
	 *
	 * @param {import('@playwright/test').Page} page Page.
	 * @param {string} email Email.
	 */
	async function checkoutToOffer( page, email ) {
		const { products } = state();
		await fillCart( page, [ products.recovery ] );
		await openCheckout( page );
		const offerPath = new URL( offerPage ).pathname;
		// placeOrder waits for the thank-you page; here the customer goes to the offer instead.
		const reached = page.waitForURL( ( url ) => url.pathname === offerPath && url.searchParams.has( 'ocuf_ns' ), { timeout: 60_000 } );
		placeOrder( page, email ).catch( () => {} );
		await reached;
	}

	/**
	 * Order id of the thank-you page.
	 *
	 * @param {import('@playwright/test').Page} page Page.
	 */
	function orderId( page ) {
		const match = new URL( page.url() ).pathname.match( /order-received\/(\d+)/ );
		return match ? match[ 1 ] : '0';
	}

	test( 'orders without the funnel target are not sent to the offer', async ( { page } ) => {
		const { products } = state();
		await fillCart( page, [ products.target ] );
		await openCheckout( page );
		const id = await placeOrder( page, `nofunnel-${ Date.now() }@e2e.test` );
		expect( id ).toBeGreaterThan( 0 );
	} );

	test( 'offer page shows the offer product and its actions', async ( { page } ) => {
		await checkoutToOffer( page, `funnel-view-${ Date.now() }@e2e.test` );
		await expect( page.locator( '.e2e-offer-title' ) ).toContainText( 'E2E Lens Cap' );
		// 50 at 50% off.
		await expect( page.locator( '.e2e-offer-price' ) ).toContainText( '25' );
		await expect( page.locator( BUY ) ).toHaveAttribute( 'href', /ocuf_ok=/ );
		await expect( page.locator( NO ) ).toHaveAttribute( 'href', /ocuf_th=1/ );
	} );

	test( '"No thanks" keeps the order as placed', async ( { page } ) => {
		await checkoutToOffer( page, `funnel-no-${ Date.now() }@e2e.test` );
		await Promise.all( [ page.waitForURL( ( url ) => /order-received/.test( url.pathname ), { timeout: 60_000 } ), page.locator( NO ).click() ] );
		const order = wp( 'order', orderId( page ) );
		expect( order.items.map( ( /** @type {{name:string}} */ i ) => i.name ) ).toEqual( [ 'E2E Beanie' ] );
		expect( order.total ).toBeCloseTo( 20, 2 );
	} );

	test( '"Add this to my Order" adds the offer at the funnel price', async ( { page } ) => {
		await checkoutToOffer( page, `funnel-yes-${ Date.now() }@e2e.test` );
		await Promise.all( [ page.waitForURL( ( url ) => /order-received/.test( url.pathname ), { timeout: 60_000 } ), page.locator( BUY ).click() ] );
		const order = wp( 'order', orderId( page ) );
		expect( order.items.map( ( /** @type {{name:string}} */ i ) => i.name ).sort() ).toEqual( [ 'E2E Beanie', 'E2E Lens Cap' ] );
		const offer = order.items.find( ( /** @type {{name:string}} */ i ) => i.name === 'E2E Lens Cap' );
		expect( offer.total ).toBeCloseTo( 25, 2 );
		expect( offer.upsell ).toBeTruthy();
		expect( order.total ).toBeCloseTo( 45, 2 );
	} );

	test( 'funnel stats count the views and the accepted offer', async () => {
		const funnel = wp( 'funnel' );
		expect( Number( funnel.funnel_triggered_count ) ).toBeGreaterThanOrEqual( 3 );
		expect( Number( funnel.offers_accept_count ) ).toBeGreaterThanOrEqual( 1 );
	} );
} );
