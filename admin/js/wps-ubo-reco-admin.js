/**
 * Smart Recommendations admin page.
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce
 * @since   3.1.8
 */
( function ( $ ) {
	'use strict';

	var cfg  = window.wpsUboReco || {};
	var i18n = cfg.i18n || {};
	var $root;

	function post( action, data ) {
		return $.post( cfg.ajaxUrl, $.extend( { action: action, nonce: cfg.nonce }, data || {} ) );
	}

	function notice( message, type ) {
		var $n = $( '#wps-ubo-reco-notice' );
		$n.removeClass( 'is-error is-success is-info' ).addClass( 'is-' + ( type || 'success' ) ).text( message ).prop( 'hidden', ! message );
	}

	function failMessage( res ) {
		return res && res.data && res.data.message ? res.data.message : i18n.error;
	}

	function pct( value ) {
		return null === value || undefined === value ? '–' : ( Math.round( value * 1000 ) / 10 ) + '%';
	}

	function settingsFromForm() {
		var out = {};
		$( '#wps-ubo-reco-settings' ).find( 'input, select' ).each( function () {
			if ( ! this.name ) {
				return;
			}
			if ( 'checkbox' === this.type ) {
				out[ this.name ] = this.checked ? 'on' : 'off';
			} else {
				out[ this.name ] = this.value;
			}
		} );
		return out;
	}

	function setStatus( key, text ) {
		$root.find( '[data-status="' + key + '"]' ).text( text || '' );
	}

	function cell( text, cls ) {
		return $( '<td/>' ).addClass( cls || '' ).text( text );
	}

	function render( data ) {
		var model = data.model || {};
		var ai    = data.ai || {};

		setStatus( 'engine', data.enabled ? 'On' : 'Off' );
		$root.find( '[data-status="engine"]' ).toggleClass( 'is-on', !! data.enabled ).toggleClass( 'is-off', ! data.enabled );
		setStatus( 'schedule', model.next_human ? 'Next training ' + model.next_human : '' );

		setStatus( 'trained', 'training' === model.status ? '…' : ( model.trained_human || i18n.never ) );
		setStatus( 'model', model.trained_at ? ( model.orders + ' orders · ' + ( 'analytics' === model.source ? 'WooCommerce Analytics' : 'order store' ) + ' · ' + model.duration + 's' ) : '' );
		setStatus( 'pairs', String( model.pairs || 0 ) );
		setStatus( 'products', model.products ? model.products + ' products' : '' );

		var aiText = ! ai.enabled ? 'Off' : ( ai.available ? 'Ready' : 'Unavailable' );
		setStatus( 'ai', aiText );
		$root.find( '[data-status="ai"]' ).toggleClass( 'is-on', ai.enabled && ai.available ).toggleClass( 'is-off', ! ( ai.enabled && ai.available ) );
		var aiSub = '';
		if ( ai.enabled && ! ai.available ) {
			aiSub = ai.reason;
		} else if ( 'done' === model.ai_status ) {
			aiSub = model.ai_anchors + ' products refined' + ( ai.provider ? ' · ' + ai.provider : '' );
		} else if ( model.ai_error ) {
			aiSub = model.ai_error;
		}
		setStatus( 'ai-sub', aiSub );

		var $err = $root.find( '[data-status="error"]' );
		$err.text( model.error || '' ).prop( 'hidden', ! model.error );

		// Associations.
		var $pairs = $( '#wps-ubo-reco-pairs' ).empty();
		if ( ! data.pairs || ! data.pairs.length ) {
			$pairs.append( $( '<tr/>' ).append( $( '<td colspan="9" class="wps-ubo-reco__empty"/>' ).text( i18n.noPairs ) ) );
		} else {
			data.pairs.forEach( function ( p ) {
				$pairs.append(
					$( '<tr/>' ).append(
						cell( p.source + ' (#' + p.source_id + ')' ),
						cell( p.target + ' (#' + p.target_id + ')' ),
						cell( String( p.co ), 'num' ),
						cell( String( p.seq ), 'num' ),
						cell( pct( p.ml ), 'num' ),
						cell( p.lift ? p.lift.toFixed( 2 ) : '–', 'num' ),
						cell( pct( p.ai ), 'num' ),
						cell( pct( p.score ), 'num' ),
						cell( p.reason || '' )
					)
				);
			} );
		}

		// Offers.
		var $offers = $( '#wps-ubo-reco-offers' ).empty();
		if ( ! data.offers || ! data.offers.length ) {
			$offers.append( $( '<tr/>' ).append( $( '<td colspan="5" class="wps-ubo-reco__empty"/>' ).text( '—' ) ) );
		} else {
			data.offers.forEach( function ( o ) {
				$offers.append(
					$( '<tr/>' ).append(
						$( '<td/>' ).append( $( '<a/>' ).attr( 'href', o.url ).text( o.name ) ),
						cell( o.type ),
						cell( o.mode ),
						cell( o.source ),
						$( '<td/>' ).append( $( '<span class="wps-ubo-reco__badge"/>' ).addClass( o.live ? 'is-on' : 'is-off' ).text( o.live ? 'Live' : 'Sandbox' ) )
					)
				);
			} );
		}
	}

	function refresh() {
		post( 'wps_ubo_reco_status' ).done( function ( res ) {
			if ( res && res.success ) {
				render( res.data );
			}
		} );
	}

	function busy( $btn, on ) {
		$btn.prop( 'disabled', on ).toggleClass( 'is-busy', on );
	}

	$( function () {
		$root = $( '#wps-ubo-reco' );
		if ( ! $root.length ) {
			return;
		}

		refresh();

		$( '#wps-ubo-reco-settings' ).on( 'submit', function ( e ) {
			e.preventDefault();
			var $btn = $( this ).find( '[type="submit"]' );
			busy( $btn, true );
			post( 'wps_ubo_reco_save_settings', { settings: settingsFromForm() } )
				.done( function ( res ) {
					if ( res && res.success ) {
						notice( i18n.saved, 'success' );
						render( res.data );
					} else {
						notice( failMessage( res ), 'error' );
					}
				} )
				.fail( function () {
					notice( i18n.error, 'error' );
				} )
				.always( function () {
					busy( $btn, false );
				} );
		} );

		$( '#wps-ubo-reco-train' ).on( 'click', function () {
			var $btn = $( this );
			busy( $btn, true );
			notice( i18n.training, 'info' );
			setStatus( 'trained', '…' );
			post( 'wps_ubo_reco_train' )
				.done( function ( res ) {
					if ( res && res.success ) {
						notice( i18n.trained, 'success' );
					} else {
						notice( failMessage( res ), 'error' );
					}
					if ( res && res.data && res.data.model ) {
						render( res.data );
					}
				} )
				.fail( function () {
					notice( i18n.error, 'error' );
					refresh();
				} )
				.always( function () {
					busy( $btn, false );
				} );
		} );

		$( '#wps-ubo-reco-delete' ).on( 'click', function () {
			if ( ! window.confirm( i18n.confirmDelete ) ) {
				return;
			}
			var $btn = $( this );
			busy( $btn, true );
			post( 'wps_ubo_reco_delete_model' )
				.done( function ( res ) {
					if ( res && res.success ) {
						notice( i18n.deleted, 'success' );
						render( res.data );
					}
				} )
				.always( function () {
					busy( $btn, false );
				} );
		} );

		$( '#wps-ubo-reco-test-ai' ).on( 'click', function () {
			var $btn = $( this );
			busy( $btn, true );
			notice( i18n.testing, 'info' );
			post( 'wps_ubo_reco_test_ai', { settings: settingsFromForm() } )
				.done( function ( res ) {
					if ( res && res.success ) {
						notice( i18n.aiOk + ' ' + res.data.reply, 'success' );
					} else {
						notice( failMessage( res ), 'error' );
					}
				} )
				.fail( function () {
					notice( i18n.error, 'error' );
				} )
				.always( function () {
					busy( $btn, false );
				} );
		} );

		$( '#wps-ubo-reco-try' ).on( 'click', function () {
			var products = $( '#wps-ubo-reco-try-products' ).val() || [];
			var $list    = $( '#wps-ubo-reco-try-results' ).empty();
			if ( ! products.length ) {
				$list.append( $( '<li class="wps-ubo-reco__empty"/>' ).text( i18n.pickProducts ) );
				return;
			}
			var $btn = $( this );
			busy( $btn, true );
			post( 'wps_ubo_reco_preview', { products: products } )
				.done( function ( res ) {
					if ( ! res || ! res.success ) {
						$list.append( $( '<li class="wps-ubo-reco__empty"/>' ).text( failMessage( res ) ) );
						return;
					}
					if ( ! res.data.items.length ) {
						$list.append( $( '<li class="wps-ubo-reco__empty"/>' ).text( i18n.noRecs ) );
						return;
					}
					res.data.items.forEach( function ( item ) {
						var strong = item.score >= res.data.threshold;
						var $li    = $( '<li/>' ).toggleClass( 'is-match', strong );
						$li.append( $( '<a/>' ).attr( 'href', item.url ).text( item.name + ' (#' + item.id + ')' ) );
						$li.append( $( '<span class="wps-ubo-reco__score"/>' ).text( pct( item.score ) ) );
						var meta = 'via ' + item.anchor_name + ' · together ' + item.co + ' · next ' + item.seq + ' · lift ' + ( item.lift ? item.lift.toFixed( 2 ) : '–' ) + ( null !== item.ai ? ' · AI ' + pct( item.ai ) : '' );
						$li.append( $( '<small/>' ).text( meta ) );
						if ( item.reason ) {
							$li.append( $( '<em/>' ).text( item.reason ) );
						}
						$list.append( $li );
					} );
				} )
				.always( function () {
					busy( $btn, false );
				} );
		} );
	} );
}( jQuery ) );
