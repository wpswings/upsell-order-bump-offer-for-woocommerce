/**
 * Cart Recovery admin tab.
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce
 * @since   3.1.8
 */
( function ( $ ) {
	'use strict';

	var cfg = window.wpsUboAcrAdmin || {};

	$( function () {
		// WooCommerce product search (selectWoo).
		$( document.body ).trigger( 'wc-enhanced-select-init' );

		$( '.wps-ubo-acr__delete' ).on( 'submit', function ( e ) {
			if ( ! window.confirm( cfg.confirmDelete ) ) {
				e.preventDefault();
			}
		} );

		// Dim a step's fields while it is disabled.
		$( '.wps-ubo-acr__step' ).each( function () {
			var $step   = $( this );
			var $toggle = $step.find( '.wps-ubo-acr__step-head input[type="checkbox"]' );
			var sync    = function () {
				$step.toggleClass( 'is-disabled', ! $toggle.is( ':checked' ) );
			};
			$toggle.on( 'change', sync );
			sync();
		} );
	} );
}( jQuery ) );
