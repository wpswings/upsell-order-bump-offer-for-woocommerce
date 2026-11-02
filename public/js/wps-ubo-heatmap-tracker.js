/**
 * Offer Heatmaps - front-end tracker.
 *
 * Records impressions, clicks and sampled hover positions on Order Bump
 * containers and Upsell offer pages, normalised to the container box, and
 * sends them in batches with navigator.sendBeacon().
 *
 * @since 3.1.8
 * @package Upsell_Order_Bump_Offer_For_Woocommerce
 */
( function ( window, document ) {
	'use strict';

	var cfg = window.wpsUboHeatmap;
	if ( ! cfg || ! cfg.ajaxUrl ) {
		return;
	}

	// Sampling: decided once per page view.
	if ( Math.random() * 100 >= cfg.sampleRate ) {
		return;
	}

	var BUMP_SELECTOR = '.wps_upsell_offer_main_wrapper';
	var BUMP_ID_RE    = /^wps_upsell_offer_main_id_(\d+)$/;

	var queue    = [];
	var tracked  = new WeakMap(); // container -> state.
	var viewed   = {};            // offer key -> true (one view per offer per page view).
	var observer = null;

	/* ------------------------------------------------------------------ */
	/* Helpers                                                            */
	/* ------------------------------------------------------------------ */

	function visitorId() {
		var key = 'wps_ubo_hm_vid';
		try {
			var id = window.sessionStorage.getItem( key );
			if ( ! id ) {
				id = Math.random().toString( 36 ).slice( 2 ) + Date.now().toString( 36 );
				window.sessionStorage.setItem( key, id );
			}
			return id;
		} catch ( e ) {
			return 'nostorage';
		}
	}

	function device() {
		var w = window.innerWidth || document.documentElement.clientWidth;
		if ( w >= 1024 ) {
			return 'desktop';
		}
		return w >= 768 ? 'tablet' : 'mobile';
	}

	function clamp( v ) {
		v = Math.round( v );
		return v < 0 ? 0 : ( v > 10000 ? 10000 : v );
	}

	function matches( el, selector ) {
		var fn = el.matches || el.msMatchesSelector || el.webkitMatchesSelector;
		try {
			return fn.call( el, selector );
		} catch ( e ) {
			return false;
		}
	}

	/**
	 * Walk up from target to container; the first zone (in config order) that
	 * matches any ancestor-or-self wins at the deepest level.
	 */
	function resolveZone( target, container, type ) {
		var map = ( cfg.zones && cfg.zones[ type ] ) || {};
		var keys = Object.keys( map );
		var el = target;

		while ( el && el.nodeType === 1 ) {
			for ( var i = 0; i < keys.length; i++ ) {
				if ( matches( el, map[ keys[ i ] ] ) ) {
					return keys[ i ];
				}
			}
			if ( el === container ) {
				break;
			}
			el = el.parentElement;
		}
		return 'other';
	}

	/* ------------------------------------------------------------------ */
	/* Transport                                                          */
	/* ------------------------------------------------------------------ */

	function flush() {
		if ( ! queue.length ) {
			return;
		}

		var batch = queue.splice( 0, 100 );
		var data  = new FormData();
		data.append( 'action', cfg.action );
		data.append( 'nonce', cfg.nonce );
		data.append( 'device', device() );
		data.append( 'vid', visitorId() );
		data.append( 'events', JSON.stringify( batch ) );

		var sent = false;
		if ( navigator.sendBeacon ) {
			try {
				sent = navigator.sendBeacon( cfg.ajaxUrl, data );
			} catch ( e ) {
				sent = false;
			}
		}
		if ( ! sent && window.fetch ) {
			window.fetch( cfg.ajaxUrl, { method: 'POST', body: data, credentials: 'same-origin', keepalive: true } ).catch( function () {} );
		}

		if ( queue.length ) {
			flush();
		}
	}

	function push( evt ) {
		queue.push( evt );
		if ( queue.length >= cfg.flushSize ) {
			flush();
		}
	}

	/* ------------------------------------------------------------------ */
	/* Container binding                                                  */
	/* ------------------------------------------------------------------ */

	function point( state, clientX, clientY ) {
		if ( state.type === 'upsell' ) {
			var doc = document.documentElement;
			var w = Math.max( doc.scrollWidth, document.body ? document.body.scrollWidth : 0 ) || 1;
			var h = Math.max( doc.scrollHeight, document.body ? document.body.scrollHeight : 0 ) || 1;
			return {
				x: clamp( ( clientX + window.pageXOffset ) / w * 10000 ),
				y: clamp( ( clientY + window.pageYOffset ) / h * 10000 )
			};
		}
		var r = state.el.getBoundingClientRect();
		if ( ! r.width || ! r.height ) {
			return null;
		}
		return {
			x: clamp( ( clientX - r.left ) / r.width * 10000 ),
			y: clamp( ( clientY - r.top ) / r.height * 10000 )
		};
	}

	function base( state, eventType ) {
		return { t: state.type, o: state.offerId, s: state.subOfferId, e: eventType, c: state.context };
	}

	function recordView( state ) {
		var key = state.type + ':' + state.offerId + ':' + state.subOfferId;
		if ( viewed[ key ] ) {
			return;
		}
		viewed[ key ] = true;
		push( base( state, 'view' ) );
	}

	function onPointer( state, ev, eventType ) {
		var p = point( state, ev.clientX, ev.clientY );
		if ( ! p ) {
			return;
		}
		var evt = base( state, eventType );
		evt.x = p.x;
		evt.y = p.y;
		evt.z = resolveZone( ev.target, state.el, state.type );
		push( evt );
		return p;
	}

	function bind( el, state ) {
		if ( tracked.has( el ) ) {
			return;
		}
		tracked.set( el, state );
		state.el = el;
		state.clicks = 0;
		state.hovers = 0;
		state.lastHoverAt = 0;
		state.lastHover = null;

		// Clicks. "click" also fires for keyboard activation (clientX = 0) – skip those, they carry no position.
		el.addEventListener( 'click', function ( ev ) {
			if ( state.clicks >= cfg.maxClicks || ( ev.clientX === 0 && ev.clientY === 0 && ev.detail === 0 ) ) {
				return;
			}
			// A <label> click re-dispatches a click on its input: count the gesture once.
			var now = Date.now();
			if ( now - ( state.lastClickAt || 0 ) < 60 ) {
				return;
			}
			state.lastClickAt = now;
			state.clicks++;
			onPointer( state, ev, 'click' );
			// Accepting an offer usually triggers a fragment refresh or navigation.
			flush();
		}, { capture: true, passive: true } );

		// Hovers (mouse only, sampled).
		if ( cfg.trackHover ) {
			el.addEventListener( 'pointermove', function ( ev ) {
				if ( ev.pointerType && ev.pointerType !== 'mouse' ) {
					return;
				}
				if ( state.hovers >= cfg.maxHovers ) {
					return;
				}
				var now = Date.now();
				if ( now - state.lastHoverAt < cfg.hoverInterval ) {
					return;
				}
				var p = point( state, ev.clientX, ev.clientY );
				if ( ! p ) {
					return;
				}
				if ( state.lastHover && Math.abs( p.x - state.lastHover.x ) < 200 && Math.abs( p.y - state.lastHover.y ) < 200 ) {
					return; // Moved less than 2 % – not a new sample.
				}
				state.lastHoverAt = now;
				state.lastHover = p;
				state.hovers++;
				var evt = base( state, 'hover' );
				evt.x = p.x;
				evt.y = p.y;
				evt.z = resolveZone( ev.target, el, state.type );
				push( evt );
			}, { passive: true } );
		}

		// Impression.
		if ( state.type === 'upsell' ) {
			recordView( state );
		} else if ( 'IntersectionObserver' in window ) {
			if ( ! observer ) {
				observer = new IntersectionObserver( function ( entries ) {
					entries.forEach( function ( entry ) {
						if ( entry.isIntersecting ) {
							var s = tracked.get( entry.target );
							if ( s ) {
								recordView( s );
							}
							observer.unobserve( entry.target );
						}
					} );
				}, { threshold: 0.5 } );
			}
			observer.observe( el );
		} else {
			recordView( state );
		}
	}

	function scanBumps( root ) {
		var list = ( root || document ).querySelectorAll ? ( root || document ).querySelectorAll( BUMP_SELECTOR ) : [];
		Array.prototype.forEach.call( list, function ( el ) {
			var m = BUMP_ID_RE.exec( el.id || '' );
			if ( ! m ) {
				return;
			}
			var ctx = cfg.context;
			if ( el.closest && el.closest( '[class*="popup"]' ) ) {
				ctx = 'popup';
			}
			bind( el, { type: 'bump', offerId: parseInt( m[ 1 ], 10 ), subOfferId: 0, context: ctx } );
		} );
	}

	/* ------------------------------------------------------------------ */
	/* Boot                                                               */
	/* ------------------------------------------------------------------ */

	function init() {
		if ( cfg.context === 'upsell_page' && cfg.upsell ) {
			bind( document.documentElement, {
				type: 'upsell',
				offerId: parseInt( cfg.upsell.funnel_id, 10 ),
				subOfferId: parseInt( cfg.upsell.offer_id, 10 ),
				context: 'upsell_page'
			} );
		} else {
			scanBumps( document );

			// Bumps are (re)rendered by AJAX: checkout fragments, block checkout, popups.
			if ( 'MutationObserver' in window ) {
				var pending = false;
				new MutationObserver( function () {
					if ( pending ) {
						return;
					}
					pending = true;
					window.setTimeout( function () {
						pending = false;
						scanBumps( document );
					}, 300 );
				} ).observe( document.body, { childList: true, subtree: true } );
			}
		}

		window.setInterval( flush, cfg.flushInterval );
		document.addEventListener( 'visibilitychange', function () {
			if ( document.visibilityState === 'hidden' ) {
				flush();
			}
		} );
		window.addEventListener( 'pagehide', flush );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}( window, document ) );
