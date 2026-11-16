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
		var generic = function () {
			// Network errors / non-JSON responses: show the translated message, not "Failed to fetch".
			throw new Error( i18n.error );
		};
		return fetch( cfg.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body } )
			.then( function ( r ) {
				return r.json().catch( generic );
			}, generic )
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

	function el( tag, cls, text ) {
		var node = document.createElement( tag );
		if ( cls ) {
			node.className = cls;
		}
		if ( text ) {
			node.textContent = text;
		}
		return node;
	}

	// "You won!" card shown over the stage before the offer is revealed.
	function showWin( root, data ) {
		var win = root.querySelector( '.wps-ubo-game__win' );
		if ( ! win ) {
			return;
		}
		var discount = parseInt( data.discount, 10 ) || 0;
		var product  = root.getAttribute( 'data-product' ) || '';
		win.innerHTML = '';
		win.appendChild( confetti() );

		if ( discount > 0 ) {
			win.appendChild( el( 'div', 'wps-ubo-game__win-title', i18n.youWon || 'You won!' ) );
			win.appendChild( el( 'div', 'wps-ubo-game__win-rule' ) );
			var big = el( 'div', 'wps-ubo-game__win-amount', discount + '% ' );
			big.appendChild( el( 'b', '', ( i18n.off || 'Off' ).toUpperCase() ) );
			win.appendChild( big );
			if ( product ) {
				win.appendChild( el( 'div', 'wps-ubo-game__win-product', ( i18n.onProduct || 'on %s' ).replace( '%s', product ) ) );
			}
			win.appendChild( el( 'div', 'wps-ubo-game__win-note', i18n.applied || '' ) );
		} else {
			win.appendChild( el( 'div', 'wps-ubo-game__win-title', data.message || i18n.standard || '' ) );
			win.appendChild( el( 'div', 'wps-ubo-game__win-note', i18n.standardSub || '' ) );
		}
		win.classList.toggle( 'is-standard', discount <= 0 );
		win.hidden = false;
		root.classList.add( 'has-win' );
	}

	function confetti() {
		var svg = document.createElementNS( SVG_NS, 'svg' );
		svg.setAttribute( 'viewBox', '0 0 64 48' );
		svg.setAttribute( 'class', 'wps-ubo-game__win-confetti' );
		svg.setAttribute( 'aria-hidden', 'true' );
		svg.innerHTML =
			'<path class="c1" d="M14 44 28 14l12 18Z"/>' +
			'<path class="c2" d="M28 14l12 18-6 2Z"/>' +
			'<path class="c3" d="M34 12c4-6 10-6 12-2M38 20c6-3 12 0 13 4M30 8c0-4 2-6 5-7"/>' +
			'<circle class="c4" cx="52" cy="10" r="2"/><circle class="c1" cx="8" cy="20" r="2"/>' +
			'<rect class="c2" x="50" y="30" width="4" height="4" transform="rotate(30 52 32)"/>' +
			'<rect class="c4" x="16" y="6" width="4" height="4" transform="rotate(20 18 8)"/>';
		return svg;
	}

	function finish( root, data ) {
		root.classList.add( 'is-done' );
		setResult( root, data.message || '' );
		showWin( root, data );
		if ( root.getAttribute( 'data-preview' ) ) {
			setTimeout( function () {
				reset( root );
			}, reduced ? 2500 : 3500 );
			return;
		}
		// Let the customer see the "You won!" card before the offer replaces the game.
		setTimeout( function () {
			swap( root, data.html );
		}, reduced ? 1200 : 2400 );
	}

	function swap( root, html ) {
		if ( ! html ) {
			return;
		}
		var bumpId       = root.getAttribute( 'data-bump-id' );
		// The game's buttons were disabled while playing, so focus has dropped to <body>.
		var active       = document.activeElement;
		var hadFocus     = ! active || active === document.body || root.contains( active );
		var holder       = document.createElement( 'div' );
		holder.innerHTML = html;
		var nodes        = Array.prototype.slice.call( holder.childNodes );
		var parent       = root.parentNode;
		nodes.forEach( function ( n ) {
			parent.insertBefore( n, root );
		} );
		parent.removeChild( root );

		// Only the bump that was just revealed (there can be several bumps on the page).
		var won = null;
		nodes.forEach( function ( n ) {
			if ( won || 1 !== n.nodeType ) {
				return;
			}
			won = n.matches( '.wps-ubo-game-won' ) ? n : n.querySelector( '.wps-ubo-game-won[data-bump-id="' + bumpId + '"], .wps-ubo-game-won' );
		} );
		if ( won ) {
			won.classList.add( 'is-new' );
		}

		// Keyboard / screen-reader users continue on the revealed offer.
		if ( hadFocus ) {
			// First *visible* control (some templates keep their checkbox hidden; focusing a
			// hidden element silently fails and leaves focus on <body>).
			// Tried in this order: the accept control first, then anything focusable.
			var target    = null;
			var selectors = [ '.wps-ubo__temp-add-btn.wps-active, .wps-ubo__temp-rmv-btn.wps-active', '.add_offer_in_cart', 'button, input:not([type="hidden"]), select', 'a[href]' ];
			var visible   = function ( c ) {
				return ! c.disabled && c.getClientRects().length && 'hidden' !== window.getComputedStyle( c ).visibility;
			};
			selectors.some( function ( selector ) {
				nodes.some( function ( n ) {
					if ( 1 === n.nodeType ) {
						target = Array.prototype.filter.call( n.querySelectorAll( selector ), visible )[ 0 ] || null;
					}
					return !! target;
				} );
				return !! target;
			} );
			if ( ! target && won ) {
				won.setAttribute( 'tabindex', '-1' );
				target = won;
			}
			if ( target && target.focus ) {
				try {
					target.focus( { preventScroll: true } );
				} catch ( e ) {
					target.focus();
				}
			}
		}
		document.dispatchEvent( new CustomEvent( 'wps_ubo_game_revealed', { detail: { bumpId: bumpId } } ) );
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

	// 24×24 line icons (stroke only), drawn on each slice.
	var ICONS = {
		tag: 'M3 12.5V4.8C3 3.8 3.8 3 4.8 3h7.7L21 11.5 11.5 21Z M7.8 7.8h.01',
		gift: 'M3 8h18v4H3Z M5 12h14v9H5Z M12 8v13 M12 8c-1.6-3.6-5.6-4.1-5.6-1.6C6.4 8 12 8 12 8Z M12 8c1.6-3.6 5.6-4.1 5.6-1.6C17.6 8 12 8 12 8Z',
		retry: 'M20 12a8 8 0 1 1-2.4-5.7 M20 4v5h-5'
	};

	function svgEl( tag, attrs ) {
		var node = document.createElementNS( SVG_NS, tag );
		Object.keys( attrs ).forEach( function ( k ) {
			node.setAttribute( k, attrs[ k ] );
		} );
		return node;
	}

	/**
	 * Split a prize label into a big and a small line: "15% OFF" → ["15%", "OFF"].
	 * Labels without a percentage are split near the middle when long.
	 */
	function splitLabel( label ) {
		label = String( label || '' ).trim();
		var m = label.match( /^(\d+(?:[.,]\d+)?\s*%)\s*(.*)$/ );
		if ( m ) {
			return [ m[ 1 ].replace( /\s+/g, '' ), m[ 2 ] ];
		}
		if ( label.length <= 9 || label.indexOf( ' ' ) < 0 ) {
			return [ label, '' ];
		}
		var mid = Math.floor( label.length / 2 );
		var cut = label.lastIndexOf( ' ', mid );
		if ( cut < 0 ) {
			cut = label.indexOf( ' ', mid );
		}
		return [ label.slice( 0, cut ), label.slice( cut + 1 ) ];
	}

	function buildWheel( container, segments ) {
		var size = 300;
		var c    = size / 2;
		var r    = c - 14; // Leave room for the rim.
		var n    = segments.length;
		var step = 360 / n;
		var best = 0;
		segments.forEach( function ( s ) {
			best = Math.max( best, parseInt( s.discount, 10 ) || 0 );
		} );

		var svg = svgEl( 'svg', { viewBox: '0 0 ' + size + ' ' + size, 'class': 'wps-ubo-game__svg' } );

		// Soft highlight that gives each slice depth.
		var defs  = svgEl( 'defs', {} );
		var shine = svgEl( 'radialGradient', { id: 'wps-ubo-shine-' + Math.random().toString( 36 ).slice( 2 ), cx: '50%', cy: '50%', r: '50%' } );
		shine.appendChild( svgEl( 'stop', { offset: '0.35', 'stop-color': '#fff', 'stop-opacity': '0.18' } ) );
		shine.appendChild( svgEl( 'stop', { offset: '1', 'stop-color': '#000', 'stop-opacity': '0.12' } ) );
		defs.appendChild( shine );
		svg.appendChild( defs );

		// Rim + rim lights (static).
		svg.appendChild( svgEl( 'circle', { cx: c, cy: c, r: c - 3, 'class': 'wps-ubo-game__rim' } ) );
		for ( var d = 0; d < 360; d += 360 / Math.max( 12, n * 2 ) ) {
			var lp = point( c, c, c - 8, d );
			svg.appendChild( svgEl( 'circle', { cx: lp[ 0 ], cy: lp[ 1 ], r: 2.2, 'class': 'wps-ubo-game__light' } ) );
		}

		var group = svgEl( 'g', { 'class': 'wps-ubo-game__rotor' } );

		segments.forEach( function ( seg, i ) {
			var a0 = i * step;
			var a1 = a0 + step;
			var p0 = point( c, c, r, a0 );
			var p1 = point( c, c, r, a1 );
			var d  = 'M' + c + ',' + c + ' L' + p0[ 0 ] + ',' + p0[ 1 ] + ' A' + r + ',' + r + ' 0 ' + ( step > 180 ? 1 : 0 ) + ' 1 ' + p1[ 0 ] + ',' + p1[ 1 ] + ' Z';

			group.appendChild( svgEl( 'path', { d: d, fill: seg.color || '#6b21a8', 'class': 'wps-ubo-game__slice' } ) );
			group.appendChild( svgEl( 'path', { d: d, fill: 'url(#' + shine.getAttribute( 'id' ) + ')', 'class': 'wps-ubo-game__slice-shine' } ) );

			// Slice content, rotated so it reads from the rim towards the centre.
			var mid      = a0 + step / 2;
			var content  = svgEl( 'g', { transform: 'rotate(' + mid + ' ' + c + ' ' + c + ')', 'class': 'wps-ubo-game__slice-content' } );
			var lines    = splitLabel( seg.label );
			var discount = parseInt( seg.discount, 10 ) || 0;
			var icon     = discount <= 0 ? ICONS.retry : ( discount === best && n > 2 ? ICONS.gift : ICONS.tag );

			// Fit text to the slice width at its radius.
			var chordAt = function ( radius ) {
				return Math.min( 2 * radius * Math.sin( Math.min( step, 120 ) * Math.PI / 360 ) - 8, r * 0.9 );
			};
			var bigSize   = Math.max( 10, Math.min( n <= 6 ? 26 : 20, chordAt( r * 0.56 ) / ( Math.max( lines[ 0 ].length, 2 ) * 0.62 ) ) );
			var smallSize = Math.max( 8, Math.min( 13, chordAt( r * 0.4 ) / ( Math.max( lines[ 1 ].length, 3 ) * 0.62 ) ) );
			var iconSize  = Math.max( 14, Math.min( 24, chordAt( r * 0.8 ) * 0.5 ) );

			var iconG = svgEl( 'g', { transform: 'translate(' + ( c - iconSize / 2 ) + ' ' + ( c - r * 0.82 - iconSize / 2 ) + ') scale(' + ( iconSize / 24 ) + ')', 'class': 'wps-ubo-game__slice-icon' } );
			iconG.appendChild( svgEl( 'path', { d: icon } ) );
			content.appendChild( iconG );

			var big = svgEl( 'text', { x: c, y: c - r * 0.56, 'text-anchor': 'middle', 'dominant-baseline': 'middle', 'class': 'wps-ubo-game__seg-big', 'font-size': bigSize.toFixed( 1 ) } );
			big.textContent = lines[ 0 ];
			content.appendChild( big );
			if ( lines[ 1 ] ) {
				var small = svgEl( 'text', { x: c, y: c - r * 0.56 + bigSize * 0.5 + smallSize * 0.9, 'text-anchor': 'middle', 'dominant-baseline': 'middle', 'class': 'wps-ubo-game__seg-small', 'font-size': smallSize.toFixed( 1 ) } );
				small.textContent = lines[ 1 ].toUpperCase();
				content.appendChild( small );
			}
			group.appendChild( content );
		} );

		svg.appendChild( group );
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
		var hub      = root.querySelector( '.wps-ubo-game__hub' );
		var rotation = 0;

		// Centre "SPIN" hub: a mouse/touch shortcut for the Spin button (the button stays the
		// keyboard / screen-reader control, so the hub is aria-hidden).
		if ( hub ) {
			var hubLabel = hub.querySelector( 'b' );
			if ( hubLabel ) {
				hubLabel.textContent = ( i18n.spin || 'Spin' ).toUpperCase();
			}
			hub.addEventListener( 'click', function () {
				if ( ! play.disabled && ! root.classList.contains( 'is-done' ) ) {
					play.click();
				}
			} );
		}

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
		var result    = null;
		var pending   = null;
		var revealed  = false;
		var revealing = false;

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
				// A failed draw can be retried (by scratching again or pressing Reveal).
				pending.catch( function ( e ) {
					pending   = null;
					revealing = false;
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
			if ( revealed || revealing ) {
				return;
			}
			revealing = true;
			lock( root, true );
			ensurePrize().then( function ( data ) {
				// Only now is the card really revealed; on failure it stays playable.
				revealed  = true;
				revealing = false;
				canvas.classList.add( 'is-cleared' );
				finish( root, data );
			} ).catch( function () {
				// Error already shown by ensurePrize(); controls unlocked by fail().
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
			ensurePrize().catch( function () {} );
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
		root.classList.remove( 'is-done', 'is-busy', 'has-win' );
		var win = root.querySelector( '.wps-ubo-game__win' );
		if ( win ) {
			win.hidden    = true;
			win.innerHTML = '';
		}
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
