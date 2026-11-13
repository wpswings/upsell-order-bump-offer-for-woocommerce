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
		var $root = $( '#wps-ubo-acr' );
		if ( ! $root.length ) {
			return;
		}
		var $form       = $( '#wps-ubo-acr-form' );
		var $sectionIn  = $form.find( '.wps-ubo-acr__section-input' );
		var formPanels  = [ 'settings', 'offers', 'emails' ];
		var dirty       = false;
		var submitting  = false;

		// WooCommerce product search (selectWoo).
		$( document.body ).trigger( 'wc-enhanced-select-init' );

		/* ---------------------------------------------------------- sections */

		/**
		 * Show one section; keep the URL and the form's return section in sync.
		 *
		 * @param {string}  key     Section slug.
		 * @param {boolean} focus   Move focus to the panel.
		 */
		function showSection( key, focus ) {
			var $panel = $root.find( '.wps-ubo-acr__panel[data-section="' + key + '"]' );
			if ( ! $panel.length ) {
				return;
			}
			$root.find( '.wps-ubo-acr__panel' ).attr( 'hidden', true );
			$panel.removeAttr( 'hidden' );
			$root.find( '.wps-ubo-acr__nav-link' ).each( function () {
				var on = $( this ).data( 'section' ) === key;
				$( this ).toggleClass( 'is-active', on ).attr( 'aria-selected', on ? 'true' : 'false' );
			} );
			$root.attr( 'data-active', key );
			if ( -1 !== formPanels.indexOf( key ) ) {
				$sectionIn.val( key );
			}
			if ( window.history && window.history.replaceState ) {
				var url = new URL( window.location.href );
				url.searchParams.set( 'acr_section', key );
				url.searchParams.delete( 'acr_notice' );
				if ( 'reports' !== key ) {
					url.searchParams.delete( 'acr_status' );
					url.searchParams.delete( 'acr_page' );
				}
				window.history.replaceState( null, '', url.toString() );
			}
			if ( focus ) {
				$panel.trigger( 'focus' );
			}
			revealTab();
		}

		// Keep the active tab in view when the tab strip scrolls (narrow screens).
		function revealTab() {
			var nav  = $root.find( '.wps-ubo-acr__nav' ).get( 0 );
			var link = $root.find( '.wps-ubo-acr__nav-link.is-active' ).get( 0 );
			if ( nav && link && nav.scrollWidth > nav.clientWidth ) {
				nav.scrollLeft = link.offsetLeft - nav.offsetLeft - ( nav.clientWidth - link.offsetWidth ) / 2;
			}
		}
		revealTab();

		// Nav links and "go to" links switch in place (they are real links without JS).
		$root.on( 'click', '.wps-ubo-acr__nav-link, .wps-ubo-acr__goto', function ( e ) {
			if ( e.metaKey || e.ctrlKey || e.shiftKey || 1 !== ( e.which || 1 ) ) {
				return;
			}
			e.preventDefault();
			showSection( $( this ).data( 'section' ), $( this ).hasClass( 'wps-ubo-acr__goto' ) );
		} );

		// Arrow-key navigation between tabs.
		$root.on( 'keydown', '.wps-ubo-acr__nav-link', function ( e ) {
			var $links = $root.find( '.wps-ubo-acr__nav-link' );
			var i      = $links.index( this );
			var next   = null;
			if ( 'ArrowRight' === e.key ) {
				next = ( i + 1 ) % $links.length;
			} else if ( 'ArrowLeft' === e.key ) {
				next = ( i - 1 + $links.length ) % $links.length;
			} else if ( 'Home' === e.key ) {
				next = 0;
			} else if ( 'End' === e.key ) {
				next = $links.length - 1;
			}
			if ( null !== next ) {
				e.preventDefault();
				$links.eq( next ).trigger( 'focus' );
				showSection( $links.eq( next ).data( 'section' ), false );
			}
		} );

		/* ------------------------------------------------------- email steps */

		function setOpen( $step, open ) {
			var $btn = $step.find( '.wps-ubo-acr__step-toggle' );
			$btn.attr( 'aria-expanded', open ? 'true' : 'false' );
			$step.toggleClass( 'is-open', open );
			$step.find( '.wps-ubo-acr__step-body' ).prop( 'hidden', ! open );
		}

		$root.find( '.wps-ubo-acr__step' ).each( function () {
			var $step   = $( this );
			var $toggle = $step.find( '.wps-ubo-acr__step-enabled' );
			var $when   = $step.find( '.wps-ubo-acr__step-when' );
			var $subj   = $step.find( '.wps-ubo-acr__step-subject' );
			var $delay  = $step.find( '.wps-ubo-acr__step-delay' );
			var $unit   = $step.find( '.wps-ubo-acr__step-unit' );
			var $subIn  = $step.find( '.wps-ubo-acr__step-subject-input' );

			var sync = function () {
				$step.toggleClass( 'is-disabled', ! $toggle.is( ':checked' ) );
			};
			var meta = function () {
				var forms = cfg.stepWhen && cfg.stepWhen[ $unit.val() ];
				if ( forms ) {
					var n = parseInt( $delay.val(), 10 );
					$when.text( forms[ 1 === n ? 0 : 1 ].replace( '%s', isNaN( n ) ? '?' : n ) );
				}
				$subj.text( $subIn.val() );
			};

			$toggle.on( 'change', sync );
			$delay.add( $unit ).add( $subIn ).on( 'input change', meta );
			$step.find( '.wps-ubo-acr__step-toggle' ).on( 'click', function () {
				setOpen( $step, 'true' !== $( this ).attr( 'aria-expanded' ) );
			} );
			$step.toggleClass( 'is-open', 'true' === $step.find( '.wps-ubo-acr__step-toggle' ).attr( 'aria-expanded' ) );
			sync();
		} );

		/* ------------------------------------------------- save / validation */

		// A field in a hidden section or collapsed email can fail validation: reveal it first.
		$form.get( 0 ).addEventListener(
			'invalid',
			function ( e ) {
				var $field = $( e.target );
				var $panel = $field.closest( '.wps-ubo-acr__panel' );
				if ( $panel.is( '[hidden]' ) ) {
					showSection( $panel.data( 'section' ), false );
				}
				var $step = $field.closest( '.wps-ubo-acr__step' );
				if ( $step.length && ! $step.hasClass( 'is-open' ) ) {
					setOpen( $step, true );
				}
			},
			true
		);

		$form.on( 'input change', ':input', function () {
			if ( ! dirty ) {
				dirty = true;
				$root.addClass( 'is-dirty' );
				$root.find( '.wps-ubo-acr__dirty' ).prop( 'hidden', false );
			}
		} );
		// selectWoo changes do not bubble an input event.
		$form.find( '.wc-product-search' ).on( 'select2:select select2:clear', function () {
			$( this ).trigger( 'change' );
		} );

		// Any form on the tab (save, send test, cart actions) leaves the page on purpose.
		$root.on( 'submit', 'form', function ( e ) {
			if ( ! e.isDefaultPrevented() ) {
				submitting = true;
			}
		} );
		$( window ).on( 'beforeunload', function () {
			if ( dirty && ! submitting ) {
				return cfg.leaveWarning || true;
			}
		} );

		$( '.wps-ubo-acr__delete' ).on( 'submit', function ( e ) {
			if ( ! window.confirm( cfg.confirmDelete ) ) {
				e.preventDefault();
			}
		} );
	} );
}( jQuery ) );
