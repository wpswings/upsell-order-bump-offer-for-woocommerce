/**
 * Gamified Offers admin tab: prize table editor and preview switcher.
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce
 * @since   3.1.8
 */
( function () {
	'use strict';

	var cfg = window.wpsUboGameAdmin || {};

	function ready( fn ) {
		if ( 'loading' === document.readyState ) {
			document.addEventListener( 'DOMContentLoaded', fn );
		} else {
			fn();
		}
	}

	ready( function () {
		var root = document.getElementById( 'wps-ubo-gm' );
		if ( ! root ) {
			return;
		}

		var tbody = root.querySelector( '#wps-ubo-gm-prizes tbody' );
		var tpl   = root.querySelector( '#wps-ubo-gm-row' );
		var msg   = root.querySelector( '#wps-ubo-gm-prize-msg' );
		var form  = root.querySelector( '#wps-ubo-gm-form' );

		function say( text ) {
			msg.textContent = text || '';
		}

		// Keep input names sequential (prizes[0..n]) and odds up to date.
		function refresh() {
			var rows  = Array.prototype.slice.call( tbody.querySelectorAll( 'tr' ) );
			var total = 0;
			rows.forEach( function ( row ) {
				total += Math.max( 0, parseInt( row.querySelector( '.wps-ubo-gm__weight' ).value, 10 ) || 0 );
			} );
			rows.forEach( function ( row, i ) {
				row.querySelectorAll( 'input' ).forEach( function ( input ) {
					input.name = input.name.replace( /prizes\[[^\]]*\]/, 'prizes[' + i + ']' );
				} );
				var w = Math.max( 0, parseInt( row.querySelector( '.wps-ubo-gm__weight' ).value, 10 ) || 0 );
				row.querySelector( '.wps-ubo-gm__odds' ).textContent = total ? ( Math.round( ( 1000 * w ) / total ) / 10 ) + '%' : '–';
			} );
			say( total ? '' : cfg.noWeight );
			return { rows: rows.length, total: total };
		}

		root.querySelector( '#wps-ubo-gm-add' ).addEventListener( 'click', function () {
			if ( tbody.querySelectorAll( 'tr' ).length >= cfg.max ) {
				say( cfg.maxPrizes );
				return;
			}
			tbody.appendChild( tpl.content.cloneNode( true ) );
			refresh();
			var rows = tbody.querySelectorAll( 'tr' );
			rows[ rows.length - 1 ].querySelector( 'input' ).focus();
		} );

		tbody.addEventListener( 'click', function ( e ) {
			var btn = e.target.closest( '.wps-ubo-gm__remove' );
			if ( ! btn ) {
				return;
			}
			if ( tbody.querySelectorAll( 'tr' ).length <= cfg.min ) {
				say( cfg.minPrizes );
				return;
			}
			btn.closest( 'tr' ).remove();
			refresh();
		} );

		tbody.addEventListener( 'input', function ( e ) {
			if ( e.target.classList.contains( 'wps-ubo-gm__weight' ) ) {
				refresh();
			}
		} );

		form.addEventListener( 'submit', function ( e ) {
			var state = refresh();
			if ( state.rows < cfg.min || ! state.total ) {
				e.preventDefault();
				say( state.total ? cfg.minPrizes : cfg.noWeight );
			}
		} );

		var reset = root.querySelector( '#wps-ubo-gm-reset' );
		if ( reset ) {
			reset.addEventListener( 'submit', function ( e ) {
				if ( ! window.confirm( cfg.confirmReset ) ) {
					e.preventDefault();
				}
			} );
		}

		// Preview tabs.
		root.querySelectorAll( '[data-preview]' ).forEach( function ( tab ) {
			if ( 'BUTTON' !== tab.tagName ) {
				return;
			}
			tab.addEventListener( 'click', function () {
				var target = tab.getAttribute( 'data-preview' );
				root.querySelectorAll( '.wps-ubo-gm__preview-tabs button' ).forEach( function ( b ) {
					var on = b === tab;
					b.classList.toggle( 'is-active', on );
					b.setAttribute( 'aria-selected', on ? 'true' : 'false' );
				} );
				root.querySelectorAll( '[data-preview-pane]' ).forEach( function ( pane ) {
					pane.hidden = pane.getAttribute( 'data-preview-pane' ) !== target;
					var game    = pane.querySelector( '.wps-ubo-game' );
					// Scratch canvas needs a visible size to paint.
					if ( ! pane.hidden && game && game._wpsRepaint ) {
						game._wpsRepaint();
					}
				} );
			} );
		} );

		refresh();
	} );
}() );
