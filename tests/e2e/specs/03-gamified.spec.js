// @ts-check
/**
 * WPS-7889 Spin wheel / scratch card replace the bump checkbox until the customer plays.
 */
const { test, expect } = require( '@playwright/test' );
const { wp } = require( '../utils/fixtures' );
const { fillCart, cart, openCheckout, waitForBump, state, ajaxResponse } = require( '../utils/store' );

const GAME = '.wps-ubo-game[data-bump-id="1"]';

test.describe( 'gamified bump', () => {
	test.afterAll( () => {
		wp( 'gamified', 'off' );
	} );

	for ( const format of [ 'spin_wheel', 'scratch_card' ] ) {
		test( `${ format }: play reveals a prize and the bump at the won price`, async ( { page } ) => {
			wp( 'gamified', format );
			const { products } = state();
			await fillCart( page, [ products.target ] );
			await openCheckout( page );

			const game = page.locator( GAME );
			await expect( game ).toBeVisible();
			await waitForBump( page );
			await expect( game ).toHaveClass( new RegExp( `wps-ubo-game--${ format }` ) );
			await expect( page.locator( '#wps_upsell_offer_main_id_1 input.add_offer_in_cart' ) ).toHaveCount( 0 );

			const played = ajaxResponse( page, 'wps_ubo_game_play' );
			await game.locator( '.wps-ubo-game__play' ).click();
			const result = await ( await played ).json();
			expect( result.success ).toBe( true );
			expect( result.data.discount ).toBeGreaterThanOrEqual( 20 );
			expect( result.data.discount ).toBeLessThanOrEqual( 50 );

			// The real bump (with its checkbox) appears once the animation ends.
			const checkbox = page.locator( '#wps_upsell_offer_main_id_1 input.add_offer_in_cart' );
			await expect( checkbox ).toBeAttached( { timeout: 20_000 } );
			await expect( page.locator( '.wps-ubo-game-won' ).first() ).toContainText( `${ result.data.discount }%` );

			const added = ajaxResponse( page, 'add_offer_in_cart' );
			await page.waitForTimeout( 300 );
			await checkbox.evaluate( ( el ) => /** @type {HTMLInputElement} */ ( el ).click() );
			await added;
			const expected = Math.round( 5000 * ( 100 - result.data.discount ) / 100 );
			await expect.poll( async () => ( await cart( page ) ).items.find( ( i ) => i.id === products.offer )?.prices.price ).toBe( String( expected ) );

			// Reloading keeps the prize: no second play.
			await page.reload();
			await expect( page.locator( GAME ) ).toHaveCount( 0 );
			await expect( page.locator( '.wps-ubo-game-won' ).first() ).toBeVisible();
		} );
	}

	test( 'skip shows the normal offer without a game discount', async ( { page } ) => {
		wp( 'gamified', 'spin_wheel' );
		const { products } = state();
		await fillCart( page, [ products.target ] );
		await openCheckout( page );
		await waitForBump( page );
		const played = ajaxResponse( page, 'wps_ubo_game_play' );
		await page.locator( `${ GAME } .wps-ubo-game__skip` ).click();
		const result = await ( await played ).json();
		expect( result.data.skipped ).toBe( true );
		expect( result.data.discount ).toBe( 0 );
		await expect( page.locator( '#wps_upsell_offer_main_id_1 input.add_offer_in_cart' ) ).toBeAttached( { timeout: 20_000 } );
	} );

	test( 'game is off → standard checkbox bump', async ( { page } ) => {
		wp( 'gamified', 'off' );
		const { products } = state();
		await fillCart( page, [ products.target ] );
		await openCheckout( page );
		await expect( page.locator( '#wps_upsell_offer_main_id_1 input.add_offer_in_cart' ) ).toBeAttached();
		await expect( page.locator( GAME ) ).toHaveCount( 0 );
	} );
} );
