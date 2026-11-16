/**
 * Gamified Offers admin tab: section tabs, prize table editor, preview switcher,
 * unsaved-changes indicator.
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

		var tbody     = root.querySelector( '#wps-ubo-gm-prizes tbody' );
		var tpl       = root.querySelector( '#wps-ubo-gm-row' );
		var msg       = root.querySelector( '#wps-ubo-gm-prize-msg' );
		var form      = root.querySelector( '#wps-ubo-gm-form' );
		var tabInput  = root.querySelector( '#wps-ubo-gm-active-tab' );
		var preview   = root.querySelector( '#wps-ubo-gm-preview' );
		var layout    = root.querySelector( '.wps-ubo-gm__layout' );
		var count     = root.querySelector( '#wps-ubo-gm-prize-count' );
		var savebar   = root.querySelector( '.wps-ubo-gm__savebar' );
		var dirtyText = root.querySelector( '#wps-ubo-gm-dirty' );
		var tabs      = Array.prototype.slice.call( root.querySelectorAll( '.wps-ubo-gm__tab' ) );

		/* ---------------------------------------------------------------
		 * Section tabs
		 * ------------------------------------------------------------- */
		function showTab( slug, focus ) {
			tabs.forEach( function ( tab ) {
				var on = tab.getAttribute( 'data-tab' ) === slug;
				tab.classList.toggle( 'is-active', on );
				tab.setAttribute( 'aria-selected', on ? 'true' : 'false' );
				tab.setAttribute( 'tabindex', on ? '0' : '-1' );
				if ( on && focus ) {
					tab.focus();
				}
			} );
			root.querySelectorAll( '[data-panel]' ).forEach( function ( panel ) {
				panel.hidden = panel.getAttribute( 'data-panel' ) !== slug;
			} );

			// Analytics is not part of the settings form and needs no preview.
			var isAnalytics = 'analytics' === slug;
			form.hidden    = isAnalytics;
			preview.hidden = isAnalytics;
			layout.classList.toggle( 'is-wide', isAnalytics );

			if ( tabInput && ! isAnalytics ) {
				tabInput.value = slug;
			}
			root.setAttribute( 'data-active-tab', slug );

			// Keep the tab in the URL so reloads / shared links open the same section.
			if ( window.history && window.history.replaceState ) {
				var url = new URL( window.location.href );
				url.searchParams.set( 'gm_tab', slug );
				url.searchParams.delete( 'wps_notice' );
				url.searchParams.delete( 'gm_capped' );
				window.history.replaceState( null, '', url.toString() );
			}

			repaintPreview();
		}

		tabs.forEach( function ( tab, i ) {
			tab.addEventListener( 'click', function () {
				showTab( tab.getAttribute( 'data-tab' ) );
			} );
			// Arrow keys move between tabs (WAI-ARIA tabs pattern).
			tab.addEventListener( 'keydown', function ( e ) {
				var next = null;
				if ( 'ArrowRight' === e.key ) {
					next = tabs[ ( i + 1 ) % tabs.length ];
				} else if ( 'ArrowLeft' === e.key ) {
					next = tabs[ ( i - 1 + tabs.length ) % tabs.length ];
				} else if ( 'Home' === e.key ) {
					next = tabs[ 0 ];
				} else if ( 'End' === e.key ) {
					next = tabs[ tabs.length - 1 ];
				}
				if ( next ) {
					e.preventDefault();
					showTab( next.getAttribute( 'data-tab' ), true );
				}
			} );
		} );

		// A required field in a hidden tab blocks submit silently: open its tab first.
		form.addEventListener(
			'invalid',
			function ( e ) {
				var panel = e.target.closest( '[data-panel]' );
				if ( panel && panel.hidden ) {
					showTab( panel.getAttribute( 'data-panel' ) );
				}
			},
			true
		);

		/* ---------------------------------------------------------------
		 * Prize editor
		 * ------------------------------------------------------------- */
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
			if ( count ) {
				count.textContent = rows.length;
			}
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
			markDirty();
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
			markDirty();
		} );

		tbody.addEventListener( 'input', function ( e ) {
			if ( e.target.classList.contains( 'wps-ubo-gm__weight' ) ) {
				refresh();
			}
		} );

		/* ---------------------------------------------------------------
		 * Unsaved changes + submit
		 * ------------------------------------------------------------- */
		var dirty = false;
		function markDirty() {
			if ( dirty ) {
				return;
			}
			dirty = true;
			savebar.classList.add( 'is-dirty' );
			dirtyText.textContent = cfg.unsaved || '';
		}
		form.addEventListener( 'input', markDirty );
		form.addEventListener( 'change', markDirty );

		var submitting = false;
		form.addEventListener( 'submit', function ( e ) {
			var state = refresh();
			if ( state.rows < cfg.min || ! state.total ) {
				e.preventDefault();
				showTab( 'prizes' );
				say( state.total ? cfg.minPrizes : cfg.noWeight );
				return;
			}
			submitting = true;
		} );

		window.addEventListener( 'beforeunload', function ( e ) {
			if ( dirty && ! submitting ) {
				e.preventDefault();
				e.returnValue = '';
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

		/* ---------------------------------------------------------------
		 * Preview format switcher
		 * ------------------------------------------------------------- */
		function repaintPreview() {
			root.querySelectorAll( '[data-preview-pane]' ).forEach( function ( pane ) {
				var game = pane.querySelector( '.wps-ubo-game' );
				// Scratch canvas needs a visible size to paint.
				if ( ! pane.hidden && game && game._wpsRepaint ) {
					game._wpsRepaint();
				}
			} );
		}

		root.querySelectorAll( '.wps-ubo-gm__preview-tabs [data-preview]' ).forEach( function ( tab ) {
			tab.addEventListener( 'click', function () {
				var target = tab.getAttribute( 'data-preview' );
				root.querySelectorAll( '.wps-ubo-gm__preview-tabs [data-preview]' ).forEach( function ( b ) {
					var on = b === tab;
					b.classList.toggle( 'is-active', on );
					b.setAttribute( 'aria-selected', on ? 'true' : 'false' );
				} );
				root.querySelectorAll( '[data-preview-pane]' ).forEach( function ( pane ) {
					pane.hidden = pane.getAttribute( 'data-preview-pane' ) !== target;
				} );
				repaintPreview();
			} );
		} );

		var previewBox = root.querySelector( '.wps-ubo-gm__preview-box' );
		if ( previewBox ) {
			// On narrow screens the preview sits above the settings: start collapsed
			// so the settings are reachable without scrolling past the whole game.
			if ( window.matchMedia && window.matchMedia( '(max-width: 1280px)' ).matches ) {
				previewBox.open = false;
			}
			previewBox.addEventListener( 'toggle', repaintPreview );
		}

		refresh();
	} );
}() );
