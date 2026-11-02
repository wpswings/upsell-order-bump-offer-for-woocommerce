/**
 * Gamified order bump formats: spin wheel & scratch card.
 *
 * The prize is always decided by the server (AJAX); this script only animates
 * the given result and then swaps in the standard bump at the won price.
 * In admin preview mode (data-preview="1") the draw is simulated locally.
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce
 * @since   3.1.8
 */
( function () {
	'use strict';

	var cfg     = window.wpsUboGame || {};
	var i18n    = cfg.i18n || {};
	var SVG_NS  = 'http://www.w3.org/2000/svg';
	var reduced = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function parse( json, fallback ) {
		try {
			return JSON.parse( json ) || fallback;
		} catch ( e ) {
			return fallback;
		}
	}

	/* ------------------------------------------------------------------ */
	/* Server                                                              */
	/* ------------------------------------------------------------------ */

	function requestPrize( root, skip ) {
		if ( root.getAttribute( 'data-preview' ) ) {
			return Promise.resolve( localDraw( root, skip ) );
		}
		var body = new FormData();
		body.append( 'action', cfg.action );
		body.append( 'nonce', cfg.nonce );
		body.append( 'bump_id', root.getAttribute( 'data-bump-id' ) );
		if ( skip ) {
			body.append( 'skip', '1' );
		}
		return fetch( cfg.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body } )
			.then( function ( r ) {
				return r.json();
			} )
			.then( function ( res ) {
				if ( ! res || ! res.success ) {
					throw new Error( res && res.data && res.data.message ? res.data.message : i18n.error );
				}
				return res.data;
			} );
	}

	// Admin preview only: weights are present in the markup there, never on the front end.
	function localDraw( root, skip ) {
		var weights = parse( root.getAttribute( 'data-weights' ), [] );
		var results = parse( root.getAttribute( 'data-results' ), [] );
		if ( skip ) {
			return { index: -1, discount: 0, skipped: true, message: i18n.standard || '', html: '' };
		}
		var total = weights.reduce( function ( a, b ) {
			return a + Math.max( 0, b );
		}, 0 );
		var r     = Math.random() * total;
		var index = 0;
		for ( var i = 0; i < weights.length; i++ ) {
			r -= Math.max( 0, weights[ i ] );
			if ( r < 0 ) {
				index = i;
				break;
			}
		}
		var res = results[ index ] || {};
		return { index: index, label: res.label || '', discount: res.discount || 0, message: res.message || '', html: '' };
	}

	/* ------------------------------------------------------------------ */
	/* Shared UI                                                           */
	/* ------------------------------------------------------------------ */

	function setResult( root, text ) {
		var el = root.querySelector( '.wps-ubo-game__result' );
		if ( el ) {
			el.textContent = text || '';
		}
	}

	function lock( root, on ) {
		root.classList.toggle( 'is-busy', on );
		root.querySelectorAll( 'button' ).forEach( function ( b ) {
			b.disabled = on;
		} );
	}

	function finish( root, data ) {
		root.classList.add( 'is-done' );
		setResult( root, data.message || '' );
		if ( root.getAttribute( 'data-preview' ) ) {
			setTimeout( function () {
				reset( root );
			}, 2500 );
			return;
		}
		setTimeout( function () {
			swap( root, data.html );
		}, reduced ? 300 : 1400 );
	}

	function swap( root, html ) {
		if ( ! html ) {
			return;
		}
		var holder       = document.createElement( 'div' );
		holder.innerHTML = html;
		var nodes        = Array.prototype.slice.call( holder.childNodes );
		var parent       = root.parentNode;
		nodes.forEach( function ( n ) {
			parent.insertBefore( n, root );
		} );
		parent.removeChild( root );

		var won = parent.querySelector( '.wps-ubo-game-won' );
		if ( won ) {
			won.classList.add( 'is-new' );
		}
		document.dispatchEvent( new CustomEvent( 'wps_ubo_game_revealed', { detail: { bumpId: root.getAttribute( 'data-bump-id' ) } } ) );
		if ( window.jQuery ) {
			window.jQuery( document.body ).trigger( 'wps_ubo_game_revealed', [ root.getAttribute( 'data-bump-id' ) ] );
		}
	}

	function fail( root, err ) {
		lock( root, false );
		setResult( root, ( err && err.message ) || i18n.error );
	}

	function bindSkip( root ) {
		var skip = root.querySelector( '.wps-ubo-game__skip' );
		if ( ! skip ) {
			return;
		}
		skip.addEventListener( 'click', function () {
			lock( root, true );
			requestPrize( root, true ).then( function ( data ) {
				finish( root, data );
			} ).catch( function ( e ) {
				fail( root, e );
			} );
		} );
	}

	/* ------------------------------------------------------------------ */
	/* Spin wheel                                                          */
	/* ------------------------------------------------------------------ */

	function point( cx, cy, r, deg ) {
		var rad = ( deg * Math.PI ) / 180;
		return [ cx + r * Math.sin( rad ), cy - r * Math.cos( rad ) ];
	}

	function buildWheel( container, segments ) {
		var size = 300;
		var c    = size / 2;
		var r    = c - 6;
		var n    = segments.length;
		var step = 360 / n;

		var svg = document.createElementNS( SVG_NS, 'svg' );
		svg.setAttribute( 'viewBox', '0 0 ' + size + ' ' + size );
		svg.setAttribute( 'class', 'wps-ubo-game__svg' );

		var group = document.createElementNS( SVG_NS, 'g' );
		group.setAttribute( 'class', 'wps-ubo-game__rotor' );

		segments.forEach( function ( seg, i ) {
			var a0   = i * step;
			var a1   = a0 + step;
			var p0   = point( c, c, r, a0 );
			var p1   = point( c, c, r, a1 );
			var path = document.createElementNS( SVG_NS, 'path' );
			path.setAttribute( 'd', 'M' + c + ',' + c + ' L' + p0[ 0 ] + ',' + p0[ 1 ] + ' A' + r + ',' + r + ' 0 ' + ( step > 180 ? 1 : 0 ) + ' 1 ' + p1[ 0 ] + ',' + p1[ 1 ] + ' Z' );
			path.setAttribute( 'fill', seg.color || '#2271b1' );
			path.setAttribute( 'stroke', '#fff' );
			path.setAttribute( 'stroke-width', '2' );
			group.appendChild( path );

			var mid  = a0 + step / 2;
			var tp   = point( c, c, r * 0.62, mid );
			var text = document.createElementNS( SVG_NS, 'text' );
			text.setAttribute( 'x', tp[ 0 ] );
			text.setAttribute( 'y', tp[ 1 ] );
			text.setAttribute( 'text-anchor', 'middle' );
			text.setAttribute( 'dominant-baseline', 'middle' );
			text.setAttribute( 'transform', 'rotate(' + ( mid > 90 && mid < 270 ? mid + 180 : mid ) + ' ' + tp[ 0 ] + ' ' + tp[ 1 ] + ')' );
			text.setAttribute( 'class', 'wps-ubo-game__seg-label' );
			text.textContent = seg.label;
			group.appendChild( text );
		} );

		var hub = document.createElementNS( SVG_NS, 'circle' );
		hub.setAttribute( 'cx', c );
		hub.setAttribute( 'cy', c );
		hub.setAttribute( 'r', 18 );
		hub.setAttribute( 'class', 'wps-ubo-game__hub' );

		svg.appendChild( group );
		svg.appendChild( hub );
		container.innerHTML = '';
		container.appendChild( svg );
		return group;
	}

	/**
	 * Rotation (deg, clockwise) that puts slice `index` under the top pointer.
	 * Slice i spans [i*step, (i+1)*step) clockwise from 12 o'clock.
	 */
	function targetRotation( current, index, n ) {
		var step   = 360 / n;
		var centre = index * step + step / 2;
		var jitter = ( Math.random() - 0.5 ) * step * 0.6;
		var base   = current - ( current % 360 ) + 360 * ( reduced ? 1 : 6 );
		return base + ( 360 - centre ) + jitter;
	}

	function initWheel( root ) {
		var segments = parse( root.getAttribute( 'data-segments' ), [] );
		var rotor    = buildWheel( root.querySelector( '.wps-ubo-game__wheel' ), segments );
		var play     = root.querySelector( '.wps-ubo-game__play' );
		var rotation = 0;

		play.addEventListener( 'click', function () {
			lock( root, true );
			setResult( root, i18n.spinning || '' );
			requestPrize( root, false ).then( function ( data ) {
				if ( data.index < 0 || data.index >= segments.length ) {
					finish( root, data );
					return;
				}
				rotation                  = targetRotation( rotation, data.index, segments.length );
				rotor.style.transition    = 'transform ' + ( reduced ? '0.01s' : '4.2s cubic-bezier(0.17, 0.67, 0.12, 0.99)' );
				rotor.style.transform     = 'rotate(' + rotation + 'deg)';
				var done                  = false;
				var end                   = function () {
					if ( done ) {
						return;
					}
					done = true;
					finish( root, data );
				};
				rotor.addEventListener( 'transitionend', end, { once: true } );
				setTimeout( end, reduced ? 100 : 4500 );
			} ).catch( function ( e ) {
				fail( root, e );
			} );
		} );
		bindSkip( root );
	}

	/* ------------------------------------------------------------------ */
	/* Scratch card                                                        */
	/* ------------------------------------------------------------------ */

	function initScratch( root ) {
		var card     = root.querySelector( '.wps-ubo-game__card' );
		var prizeEl  = root.querySelector( '.wps-ubo-game__prize' );
		var canvas   = root.querySelector( '.wps-ubo-game__scratch' );
		var play     = root.querySelector( '.wps-ubo-game__play' );
		var ctx      = canvas.getContext( '2d' );
		var dpr      = window.devicePixelRatio || 1;
		var drawing  = false;
		var moves    = 0;
		var result   = null;
		var pending  = null;
		var revealed = false;

		function paint() {
			var w         = card.clientWidth;
			var h         = card.clientHeight;
			canvas.width  = w * dpr;
			canvas.height = h * dpr;
			ctx.setTransform( dpr, 0, 0, dpr, 0, 0 );
			var g = ctx.createLinearGradient( 0, 0, w, h );
			g.addColorStop( 0, '#c9ccd1' );
			g.addColorStop( 0.5, '#eef0f2' );
			g.addColorStop( 1, '#b4b9bf' );
			ctx.globalCompositeOperation = 'source-over';
			ctx.fillStyle                = g;
			ctx.fillRect( 0, 0, w, h );
			ctx.fillStyle    = '#50575e';
			ctx.font         = '600 16px system-ui, sans-serif';
			ctx.textAlign    = 'center';
			ctx.textBaseline = 'middle';
			ctx.fillText( '✦ ' + ( i18n.scratchHere || 'Scratch here' ) + ' ✦', w / 2, h / 2 );
		}

		function ensurePrize() {
			if ( ! pending ) {
				pending = requestPrize( root, false ).then( function ( data ) {
					result              = data;
					prizeEl.textContent = data.label || '';
					return data;
				} );
				pending.catch( function ( e ) {
					fail( root, e );
				} );
			}
			return pending;
		}

		function clearedRatio() {
			var data  = ctx.getImageData( 0, 0, canvas.width, canvas.height ).data;
			var clear = 0;
			var total = 0;
			for ( var i = 3; i < data.length; i += 16 ) {
				total++;
				if ( data[ i ] === 0 ) {
					clear++;
				}
			}
			return total ? clear / total : 0;
		}

		function reveal() {
			if ( revealed ) {
				return;
			}
			revealed = true;
			lock( root, true );
			ensurePrize().then( function ( data ) {
				canvas.classList.add( 'is-cleared' );
				finish( root, data );
			} );
		}

		function scratchAt( e ) {
			var rect                     = canvas.getBoundingClientRect();
			ctx.globalCompositeOperation = 'destination-out';
			ctx.beginPath();
			ctx.arc( e.clientX - rect.left, e.clientY - rect.top, 18, 0, Math.PI * 2 );
			ctx.fill();
			if ( ++moves % 12 === 0 && result && clearedRatio() > 0.45 ) {
				reveal();
			}
		}

		canvas.addEventListener( 'pointerdown', function ( e ) {
			if ( revealed ) {
				return;
			}
			drawing = true;
			canvas.setPointerCapture( e.pointerId );
			ensurePrize();
			scratchAt( e );
		} );
		canvas.addEventListener( 'pointermove', function ( e ) {
			if ( drawing && ! revealed ) {
				e.preventDefault();
				scratchAt( e );
			}
		} );
		[ 'pointerup', 'pointercancel', 'pointerleave' ].forEach( function ( type ) {
			canvas.addEventListener( type, function () {
				drawing = false;
				if ( result && ! revealed && clearedRatio() > 0.45 ) {
					reveal();
				}
			} );
		} );

		play.addEventListener( 'click', reveal );
		bindSkip( root );
		paint();
		root._wpsRepaint = paint;

		// Painted while hidden (0×0, e.g. while the block checkout moves the bump): repaint once visible.
		if ( ! card.clientWidth && window.ResizeObserver ) {
			var ro = new ResizeObserver( function () {
				if ( card.clientWidth && ! revealed ) {
					ro.disconnect();
					paint();
				}
			} );
			ro.observe( card );
		}
	}

	/* ------------------------------------------------------------------ */
	/* Boot                                                                */
	/* ------------------------------------------------------------------ */

	function reset( root ) {
		// Admin preview: rebuild the widget so it can be played again.
		var clone = root.cloneNode( true );
		root.parentNode.replaceChild( clone, root );
		init( clone );
	}

	/*
	 * "Initialised" is tracked as a DOM property, not an attribute: the block checkout
	 * moves bumps by copying their HTML as a string, which keeps attributes but drops
	 * listeners and canvas pixels. A copied game therefore has no property and is
	 * initialised again from a clean state.
	 */
	function init( root ) {
		if ( root._wpsGame ) {
			return;
		}
		root._wpsGame = true;
		root.classList.remove( 'is-done', 'is-busy' );
		root.querySelectorAll( 'button' ).forEach( function ( b ) {
			b.disabled = false;
		} );
		setResult( root, '' );
		if ( 'spin_wheel' === root.getAttribute( 'data-format' ) ) {
			initWheel( root );
		} else {
			initScratch( root );
		}
	}

	function scan( scope ) {
		( scope || document ).querySelectorAll( '.wps-ubo-game' ).forEach( init );
	}

	window.WpsUboGame = { init: init, scan: scan, targetRotation: targetRotation };

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			scan();
		} );
	} else {
		scan();
	}

	// Classic checkout / cart refresh fragments; block checkout inserts bumps later.
	if ( window.jQuery ) {
		window.jQuery( document.body ).on( 'updated_checkout updated_cart_totals wps_ubo_game_revealed', function () {
			scan();
		} );
	}
	if ( window.MutationObserver ) {
		new MutationObserver( function ( list ) {
			for ( var i = 0; i < list.length; i++ ) {
				if ( list[ i ].addedNodes.length ) {
					scan();
					return;
				}
			}
		} ).observe( document.body, { childList: true, subtree: true } );
	}
}() );
