/**
 * Store-front helpers shared by the specs.
 */
const { expect } = require( '@playwright/test' );
const { state } = require( './fixtures' );

/**
 * Empty the cart and add products through the Store API (fast and theme independent).
 *
 * @param {import('@playwright/test').Page} page Page.
 * @param {number[]} ids Product ids.
 */
async function fillCart( page, ids ) {
	await page.goto( '/' );
	const nonce = await page.evaluate( async () => {
		const r = await fetch( '/wp-json/wc/store/v1/cart', { credentials: 'same-origin' } );
		return r.headers.get( 'Nonce' );
	} );
	await page.evaluate(
		async ( { ids: list, nonce: n } ) => {
			await fetch( '/wp-json/wc/store/v1/cart/items', { method: 'DELETE', credentials: 'same-origin', headers: { Nonce: n } } );
			for ( const id of list ) {
				await fetch( '/wp-json/wc/store/v1/cart/add-item', {
					method: 'POST',
					credentials: 'same-origin',
					headers: { Nonce: n, 'Content-Type': 'application/json' },
					body: JSON.stringify( { id, quantity: 1 } ),
				} );
			}
		},
		{ ids, nonce }
	);
}

/**
 * Current cart from the Store API.
 *
 * @param {import('@playwright/test').Page} page Page.
 * @return {Promise<{items:Array<{id:number,name:string,prices:{price:string}}>, totals:object, coupons:Array<{code:string}>}>}
 */
async function cart( page ) {
	// page.request shares the browser cookies and survives page reloads.
	const res = await page.request.get( '/wp-json/wc/store/v1/cart' );
	return res.json();
}

/**
 * Open the (block) checkout and wait for it to render.
 *
 * @param {import('@playwright/test').Page} page Page.
 */
async function openCheckout( page ) {
	await page.goto( '/checkout/' );
	await expect( page.locator( '.wc-block-checkout, form.woocommerce-checkout' ).first() ).toBeVisible();
}

/**
 * On the block checkout the plugin moves the bump into place about 1s after load by copying
 * its HTML. Wait for that before interacting, or the interaction is lost with the old node.
 *
 * @param {import('@playwright/test').Page} page Page.
 */
async function waitForBump( page ) {
	await page.waitForFunction( () => {
		const wrap = document.querySelector( '.wrapup_order_bump' );
		return !! wrap && ( ! document.querySelector( '.wc-block-checkout' ) || !! wrap.closest( '.wc-block-checkout' ) );
	} );
	await page.waitForTimeout( 300 );
}

/**
 * Fill the block checkout billing form and place the order (Cash on delivery).
 *
 * @param {import('@playwright/test').Page} page  Page.
 * @param {string} email Email.
 * @return {Promise<number>} Order id.
 */
async function placeOrder( page, email ) {
	const fields = {
		'#email': email,
		'#billing-first_name': 'Eetoo',
		'#billing-last_name': 'Tester',
		'#billing-address_1': '1 Test Street',
		'#billing-city': 'Pune',
		'#billing-postcode': '411001',
		'#billing-phone': '9876543210',
	};
	for ( const [ selector, value ] of Object.entries( fields ) ) {
		const input = page.locator( `.wc-block-checkout ${ selector }` );
		if ( ( await input.count() ) && ! ( await input.inputValue() ) ) {
			await input.fill( value );
		}
	}
	const state_ = page.locator( '.wc-block-checkout #billing-state' );
	if ( await state_.count() ) {
		const tag = await state_.evaluate( ( el ) => el.tagName );
		if ( 'SELECT' === tag ) {
			await state_.selectOption( { index: 1 } );
		} else if ( ! ( await state_.inputValue() ) ) {
			await state_.fill( 'MH' );
		}
	}
	const cod = page.locator( 'input[value="cod"]' );
	if ( await cod.count() ) {
		await cod.check( { force: true } );
	}
	await page.locator( '.wc-block-components-checkout-place-order-button' ).click();
	// Match on the path only: the site host itself may contain words like "upsell".
	await page.waitForURL( ( url ) => /order-received/.test( url.pathname ), { timeout: 60_000 } );
	const match = new URL( page.url() ).pathname.match( /order-received\/(\d+)/ );
	return match ? Number( match[ 1 ] ) : 0;
}

/**
 * True when a request is an admin-ajax call for the given action. Handles both
 * form-encoded bodies (jQuery) and multipart FormData bodies (fetch / sendBeacon).
 *
 * @param {import('@playwright/test').Request} request Request.
 * @param {string} action AJAX action.
 * @return {boolean}
 */
function isAjax( request, action ) {
	if ( ! request.url().includes( 'admin-ajax.php' ) ) {
		return false;
	}
	const body = request.postData() || '';
	return body.includes( `action=${ action }` ) || new RegExp( `name="action"\\r?\\n\\r?\\n${ action }\\r?\\n` ).test( body );
}

/**
 * Wait for the response of an admin-ajax action.
 *
 * @param {import('@playwright/test').Page} page   Page.
 * @param {string} action AJAX action.
 * @param {number} [timeout] Timeout.
 */
function ajaxResponse( page, action, timeout = 20_000 ) {
	return page.waitForResponse( ( r ) => isAjax( r.request(), action ), { timeout } );
}

module.exports = { fillCart, cart, openCheckout, waitForBump, placeOrder, state, isAjax, ajaxResponse };
