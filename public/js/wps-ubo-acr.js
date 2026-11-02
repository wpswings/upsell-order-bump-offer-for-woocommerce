/**
 * Cart recovery: guest email capture, consent notice / opt-out, and the
 * "Welcome back" recovery offer at checkout (classic + block checkout).
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce
 * @since   3.1.8
 */
( function () {
	'use strict';

	var cfg = window.wpsUboAcr || {};
	var EMAIL_SELECTOR = '#billing_email, .wc-block-checkout #email, .wc-block-checkout input[type="email"]';
	var NAME_SELECTOR  = '#billing_first_name, .wc-block-checkout #billing-first_name, .wc-block-checkout #shipping-first_name';
	var lastSent       = '';
	var lastName       = '';
	var timer          = null;
	var optedOut       = false;

	function post( action, data ) {
		var body = new FormData();
		body.append( 'action', action );
		body.append( 'nonce', cfg.nonce );
		Object.keys( data || {} ).forEach( function ( k ) {
			body.append( k, data[ k ] );
		} );
		return fetch( cfg.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body } ).then( function ( r ) {
			return r.json();
		} );
	}

	function validEmail( value ) {
		return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test( value );
	}

	/* -------------------------------------------------------------- */
	/* Guest email capture                                             */
	/* -------------------------------------------------------------- */

	function capture( input ) {
		if ( ! cfg.capture || optedOut ) {
			return;
		}
		var email     = ( input.value || '' ).trim();
		var nameField = document.querySelector( NAME_SELECTOR );
		var name      = nameField ? ( nameField.value || '' ).trim() : '';
		if ( ! validEmail( email ) || ( email === lastSent && name === lastName ) ) {
			return;
		}
		lastSent = email;
		lastName = name;
		post( 'wps_ubo_acr_capture', { email: email, first_name: name } ).catch( function () {
			lastSent = '';
		} );
	}

	function onEmailEvent( e ) {
		var input = e.target;
		if ( ! input.matches ) {
			return;
		}
		// The name field usually comes after the email: re-send once it is filled in.
		if ( input.matches( NAME_SELECTOR ) ) {
			if ( 'input' !== e.type && lastSent ) {
				var emailField = document.querySelector( EMAIL_SELECTOR );
				if ( emailField ) {
					capture( emailField );
				}
			}
			return;
		}
		if ( ! input.matches( EMAIL_SELECTOR ) ) {
			return;
		}
		clearTimeout( timer );
		if ( 'input' === e.type ) {
			timer = setTimeout( function () {
				capture( input );
			}, 1200 );
		} else {
			capture( input );
		}
	}

	[ 'input', 'change', 'focusout' ].forEach( function ( type ) {
		document.addEventListener( type, onEmailEvent, true );
	} );

	/* -------------------------------------------------------------- */
	/* Consent notice + opt-out                                        */
	/* -------------------------------------------------------------- */

	function addNotice() {
		if ( ! cfg.notice || optedOut ) {
			return;
		}
		var input = document.querySelector( EMAIL_SELECTOR );
		if ( ! input || document.querySelector( '.wps-ubo-acr-consent' ) ) {
			return;
		}
		var p       = document.createElement( 'p' );
		p.className = 'wps-ubo-acr-consent';
		p.appendChild( document.createTextNode( cfg.notice + ' ' ) );
		var a         = document.createElement( 'a' );
		a.href        = '#';
		a.textContent = cfg.optOut;
		a.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			optedOut = true;
			post( 'wps_ubo_acr_optout', {} );
			p.textContent = cfg.optedOut;
		} );
		p.appendChild( a );
		var anchor = input.closest( '.form-row, .wc-block-components-text-input' ) || input;
		anchor.parentNode.insertBefore( p, anchor.nextSibling );
	}

	/* -------------------------------------------------------------- */
	/* Recovery offer box                                              */
	/* -------------------------------------------------------------- */

	document.addEventListener( 'change', function ( e ) {
		if ( ! e.target.classList || ! e.target.classList.contains( 'wps-ubo-acr-offer__check' ) ) {
			return;
		}
		var box = e.target.closest( '.wps-ubo-acr-offer' );
		box.classList.add( 'is-busy' );
		e.target.disabled = true;
		post( 'wps_ubo_acr_toggle_offer', { add: e.target.checked ? '1' : '' } ).then( function () {
			// Reload so classic and block checkout both show the updated totals.
			window.location.reload();
		} ).catch( function () {
			box.classList.remove( 'is-busy' );
			e.target.disabled = false;
		} );
	} );

	/* -------------------------------------------------------------- */
	/* Boot (block checkout renders fields after load)                 */
	/* -------------------------------------------------------------- */

	function boot() {
		addNotice();
		var input = document.querySelector( EMAIL_SELECTOR );
		if ( input && input.value ) {
			capture( input );
		}
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
	if ( window.MutationObserver ) {
		var pending = false;
		new MutationObserver( function () {
			if ( pending ) {
				return;
			}
			pending = true;
			setTimeout( function () {
				pending = false;
				addNotice();
			}, 300 );
		} ).observe( document.body, { childList: true, subtree: true } );
	}
}() );
