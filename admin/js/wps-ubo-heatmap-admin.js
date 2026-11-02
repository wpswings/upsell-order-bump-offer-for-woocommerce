/**
 * Offer Heatmaps - admin viewer.
 *
 * Loads aggregated interaction data and the real template preview, then
 * paints a canvas heatmap over the template.
 *
 * @since 3.1.8
 * @package Upsell_Order_Bump_Offer_For_Woocommerce
 */
( function () {
	'use strict';

	const cfg = window.wpsUboHeatmapAdmin;
	const root = document.getElementById( 'wps-ubo-hm' );
	if ( ! cfg || ! root ) {
		return;
	}

	const $ = ( id ) => document.getElementById( id );

	const el = {
		type: $( 'wps-ubo-hm-type' ),
		offer: $( 'wps-ubo-hm-offer' ),
		offerLabel: $( 'wps-ubo-hm-offer-label' ),
		subWrap: $( 'wps-ubo-hm-suboffer-wrap' ),
		sub: $( 'wps-ubo-hm-suboffer' ),
		template: $( 'wps-ubo-hm-template' ),
		device: $( 'wps-ubo-hm-device' ),
		days: $( 'wps-ubo-hm-days' ),
		radius: $( 'wps-ubo-hm-radius' ),
		opacity: $( 'wps-ubo-hm-opacity' ),
		stage: $( 'wps-ubo-hm-stage' ),
		frame: $( 'wps-ubo-hm-frame' ),
		preview: $( 'wps-ubo-hm-preview' ),
		canvas: $( 'wps-ubo-hm-canvas' ),
		empty: $( 'wps-ubo-hm-empty' ),
		loading: $( 'wps-ubo-hm-loading' ),
		zones: $( 'wps-ubo-hm-zones' ),
		stats: $( 'wps-ubo-hm-stats' ),
		notice: $( 'wps-ubo-hm-notice' ),
		settings: $( 'wps-ubo-hm-settings' ),
		settingsToggle: $( 'wps-ubo-hm-settings-toggle' ),
		del: $( 'wps-ubo-hm-delete' ),
	};

	// Template preview widths per device (bumps are column components, upsell pages are full pages).
	const WIDTHS = {
		bump: { '': 600, desktop: 600, tablet: 480, mobile: 340 },
		upsell: { '': 1280, desktop: 1280, tablet: 768, mobile: 375 },
	};
	const MAX_IFRAME_HEIGHT = 8000;

	const state = {
		data: null,
		target: null, // Element whose box the normalised coordinates map to.
		requestId: 0,
	};

	/* ------------------------------------------------------------------ */
	/* Utilities                                                          */
	/* ------------------------------------------------------------------ */

	function post( action, extra ) {
		const body = new FormData();
		body.append( 'action', action );
		body.append( 'nonce', cfg.nonce );
		Object.keys( extra || {} ).forEach( ( k ) => body.append( k, extra[ k ] ) );
		return fetch( cfg.ajaxUrl, { method: 'POST', body, credentials: 'same-origin' } )
			.then( ( r ) => r.json() )
			.then( ( json ) => {
				if ( ! json || ! json.success ) {
					throw new Error( ( json && json.data && json.data.message ) || cfg.i18n.error );
				}
				return json.data;
			} );
	}

	function notify( message, type ) {
		el.notice.textContent = message;
		el.notice.className = 'wps-ubo-hm__notice is-' + ( type || 'success' );
		el.notice.hidden = false;
		window.clearTimeout( notify.t );
		notify.t = window.setTimeout( () => ( el.notice.hidden = true ), 4000 );
	}

	function option( value, label, selected ) {
		const o = document.createElement( 'option' );
		o.value = value;
		o.textContent = label;
		if ( selected ) {
			o.selected = true;
		}
		return o;
	}

	function fmt( n ) {
		return Number( n || 0 ).toLocaleString();
	}

	function debounce( fn, ms ) {
		let t;
		return function () {
			window.clearTimeout( t );
			t = window.setTimeout( fn, ms );
		};
	}

	function filters() {
		return {
			offer_type: el.type.value,
			offer_id: el.offer.value || 0,
			sub_offer_id: el.type.value === 'upsell' ? el.sub.value || 0 : 0,
			template: el.template.value,
			device: el.device.value,
			days: el.days.value,
		};
	}

	function mode() {
		const checked = root.querySelector( 'input[name="wps-ubo-hm-mode"]:checked' );
		return checked ? checked.value : 'click';
	}

	/* ------------------------------------------------------------------ */
	/* Selectors                                                          */
	/* ------------------------------------------------------------------ */

	function populateOffers() {
		const type = el.type.value;
		const list = ( cfg.offers && cfg.offers[ type ] ) || [];

		el.offer.innerHTML = '';
		el.offerLabel.textContent = type === 'bump' ? cfg.i18n.bumpLabel : cfg.i18n.funnelLabel;
		list.forEach( ( item ) => {
			const label = type === 'bump' ? `${ item.name } (${ cfg.i18n.template } ${ item.template })` : item.name;
			el.offer.appendChild( option( item.id, label ) );
		} );

		el.subWrap.hidden = type !== 'upsell';
		populateSubOffers();
		resetTemplates();
	}

	function populateSubOffers() {
		el.sub.innerHTML = '';
		if ( el.type.value !== 'upsell' ) {
			return;
		}
		const funnel = ( cfg.offers.upsell || [] ).find( ( f ) => String( f.id ) === el.offer.value );
		( funnel ? funnel.offers : [] ).forEach( ( o ) => el.sub.appendChild( option( o.id, o.name ) ) );
	}

	function resetTemplates() {
		el.template.innerHTML = '';
		el.template.appendChild( option( '', cfg.i18n.allTemplates, true ) );
	}

	function updateTemplates( templates ) {
		const current = el.template.value;
		resetTemplates();
		( templates || [] ).forEach( ( t ) => {
			if ( t !== '' ) {
				el.template.appendChild( option( t, `${ cfg.i18n.template } ${ t }`, t === current ) );
			}
		} );
	}

	/* ------------------------------------------------------------------ */
	/* Preview                                                            */
	/* ------------------------------------------------------------------ */

	function waitForImages( container, timeout ) {
		const pending = Array.from( container.querySelectorAll( 'img' ) ).filter( ( img ) => ! img.complete );
		if ( ! pending.length ) {
			return Promise.resolve();
		}
		const loaded = Promise.all(
			pending.map( ( img ) => new Promise( ( done ) => {
				img.addEventListener( 'load', done, { once: true } );
				img.addEventListener( 'error', done, { once: true } );
			} ) )
		);
		return Promise.race( [ loaded, new Promise( ( done ) => window.setTimeout( done, timeout ) ) ] );
	}

	function clearPreview() {
		el.preview.innerHTML = '';
		el.preview.removeAttribute( 'style' );
		el.frame.removeAttribute( 'style' );
		state.target = null;
	}

	function loadBumpPreview( f ) {
		return post( 'wps_ubo_heatmap_preview', f ).then( ( res ) => {
			clearPreview();
			const width = WIDTHS.bump[ f.device ] || 600;
			el.preview.style.width = width + 'px';

			if ( res.css ) {
				const style = document.createElement( 'style' );
				style.textContent = res.css; // textContent: CSS can never execute script.
				el.preview.appendChild( style );
			}

			const holder = document.createElement( 'div' );
			holder.className = 'wps-ubo-hm__bump-holder';
			holder.innerHTML = res.html || ''; // Already sanitized server-side with wp_kses().
			el.preview.appendChild( holder );

			state.target = holder.querySelector( '.wps_upsell_offer_main_wrapper' );
			if ( ! state.target ) {
				holder.classList.add( 'is-blank' );
				state.target = holder;
				notify( cfg.i18n.previewError, 'warning' );
			}
			// Measure only after images have their final size.
			return waitForImages( holder, 2000 );
		} );
	}

	function loadUpsellPreview( f ) {
		return post( 'wps_ubo_heatmap_preview', f ).then( ( res ) => {
			clearPreview();
			const width = WIDTHS.upsell[ f.device ] || 1280;

			if ( ! res.url ) {
				const blank = document.createElement( 'div' );
				blank.className = 'wps-ubo-hm__bump-holder is-blank is-page';
				el.preview.appendChild( blank );
				state.target = blank;
				notify( cfg.i18n.previewError, 'warning' );
				return;
			}

			return new Promise( ( resolve ) => {
				const iframe = document.createElement( 'iframe' );
				iframe.className = 'wps-ubo-hm__iframe';
				iframe.setAttribute( 'sandbox', 'allow-same-origin' ); // No scripts, forms or navigation.
				iframe.setAttribute( 'scrolling', 'no' );
				iframe.setAttribute( 'tabindex', '-1' );
				iframe.setAttribute( 'title', 'preview' );
				iframe.style.width = width + 'px';
				iframe.style.height = '900px';
				const fit = ( doc ) => {
					const h = Math.max( doc.documentElement.scrollHeight, doc.body ? doc.body.scrollHeight : 0 );
					iframe.style.height = Math.min( h || 900, MAX_IFRAME_HEIGHT ) + 'px';
				};
				iframe.addEventListener( 'load', () => {
					let doc;
					try {
						doc = iframe.contentDocument;
					} catch ( e ) {
						doc = null; // Cross-origin: keep default height.
					}
					if ( ! doc || ! doc.documentElement ) {
						resolve();
						return;
					}
					// Scripts are sandboxed off, so reveal content that page builders animate in with JS.
					const reveal = doc.createElement( 'style' );
					reveal.textContent = '.elementor-invisible{visibility:visible!important}.wow,[data-aos]{visibility:visible!important;opacity:1!important;transform:none!important}';
					( doc.head || doc.documentElement ).appendChild( reveal );
					fit( doc );
					// Lazy images below the first 900px load after the resize; measure again once they settle.
					waitForImages( doc, 2500 ).then( () => {
						fit( doc );
						resolve();
					} );
				} );
				iframe.src = res.url;

				el.preview.style.width = width + 'px';
				el.preview.appendChild( iframe );
				state.target = iframe;
			} );
		} );
	}

	/**
	 * Scale the preview to the stage width and place the canvas over the target box.
	 */
	function layout() {
		if ( ! state.target ) {
			return null;
		}

		const stageWidth = el.stage.clientWidth - 32;
		const naturalWidth = el.preview.offsetWidth || 1;
		const scale = Math.min( 1, stageWidth / naturalWidth );

		el.preview.style.transform = `scale(${ scale })`;
		el.preview.style.transformOrigin = 'top left';
		el.frame.style.width = naturalWidth * scale + 'px';
		el.frame.style.height = el.preview.offsetHeight * scale + 'px';

		const frameRect = el.frame.getBoundingClientRect();
		const r = state.target.getBoundingClientRect(); // Already includes the CSS transform.

		const box = {
			left: r.left - frameRect.left,
			top: r.top - frameRect.top,
			width: Math.max( 1, Math.round( r.width ) ),
			height: Math.max( 1, Math.round( r.height ) ),
		};

		el.canvas.style.left = box.left + 'px';
		el.canvas.style.top = box.top + 'px';
		el.canvas.width = box.width;
		el.canvas.height = box.height;
		el.canvas.style.width = box.width + 'px';
		el.canvas.style.height = box.height + 'px';

		return box;
	}

	/* ------------------------------------------------------------------ */
	/* Heatmap renderer (no third-party dependency)                       */
	/* ------------------------------------------------------------------ */

	let palette = null;
	function getPalette() {
		if ( palette ) {
			return palette;
		}
		const c = document.createElement( 'canvas' );
		c.width = 1;
		c.height = 256;
		const ctx = c.getContext( '2d' );
		const g = ctx.createLinearGradient( 0, 0, 0, 256 );
		g.addColorStop( 0.25, 'rgb(0,0,255)' );
		g.addColorStop( 0.45, 'rgb(0,255,255)' );
		g.addColorStop( 0.6, 'rgb(0,255,0)' );
		g.addColorStop( 0.8, 'rgb(255,255,0)' );
		g.addColorStop( 1.0, 'rgb(255,0,0)' );
		ctx.fillStyle = g;
		ctx.fillRect( 0, 0, 1, 256 );
		palette = ctx.getImageData( 0, 0, 1, 256 ).data;
		return palette;
	}

	function brush( radius ) {
		const blur = radius * 0.6;
		const c = document.createElement( 'canvas' );
		const size = ( radius + blur ) * 2;
		c.width = c.height = size;
		const ctx = c.getContext( '2d' );
		// Draw the circle off-canvas and cast its blurred shadow into view.
		ctx.shadowOffsetX = ctx.shadowOffsetY = size * 2;
		ctx.shadowBlur = blur;
		ctx.shadowColor = 'black';
		ctx.beginPath();
		ctx.arc( -size * 1.5, -size * 1.5, radius, 0, Math.PI * 2, true );
		ctx.closePath();
		ctx.fill();
		return { canvas: c, offset: radius + blur };
	}

	function drawHeatmap() {
		const box = layout();
		const ctx = el.canvas.getContext( '2d' );
		if ( ! box ) {
			return;
		}
		ctx.clearRect( 0, 0, box.width, box.height );

		const points = ( state.data && state.data[ mode() ] ) || [];
		el.empty.hidden = points.length > 0;
		el.empty.textContent = cfg.i18n.noData;
		if ( ! points.length ) {
			return;
		}

		const radius = parseInt( el.radius.value, 10 );
		const b = brush( radius );
		const max = points.reduce( ( m, p ) => Math.max( m, p[ 2 ] ), 1 );

		points.forEach( ( p ) => {
			const x = ( ( p[ 0 ] + 0.5 ) / 100 ) * box.width;
			const y = ( ( p[ 1 ] + 0.5 ) / 100 ) * box.height;
			ctx.globalAlpha = Math.min( 1, Math.max( 0.05, p[ 2 ] / max ) );
			ctx.drawImage( b.canvas, x - b.offset, y - b.offset );
		} );
		ctx.globalAlpha = 1;

		// Colourise alpha channel.
		const img = ctx.getImageData( 0, 0, box.width, box.height );
		const px = img.data;
		const pal = getPalette();
		for ( let i = 3; i < px.length; i += 4 ) {
			const a = px[ i ];
			if ( a ) {
				const j = a * 4;
				px[ i - 3 ] = pal[ j ];
				px[ i - 2 ] = pal[ j + 1 ];
				px[ i - 1 ] = pal[ j + 2 ];
			}
		}
		ctx.putImageData( img, 0, 0 );
		el.canvas.style.opacity = parseInt( el.opacity.value, 10 ) / 100;
	}

	/* ------------------------------------------------------------------ */
	/* Stats + zones                                                      */
	/* ------------------------------------------------------------------ */

	function renderStats() {
		const t = ( state.data && state.data.totals ) || {};
		const set = ( key, val ) => {
			const node = el.stats.querySelector( `[data-stat="${ key }"]` );
			if ( node ) {
				node.textContent = val;
			}
		};
		set( 'views', fmt( t.views ) );
		set( 'clicks', fmt( t.clicks ) );
		set( 'ctr', t.views ? ( ( t.clicks / t.views ) * 100 ).toFixed( 1 ) + '%' : '–' );
		set( 'visitors', fmt( t.visitors ) );
	}

	function highlightZone( zone ) {
		root.querySelectorAll( '.wps-ubo-hm-zone-highlight' ).forEach( ( n ) => n.classList.remove( 'wps-ubo-hm-zone-highlight' ) );
		if ( ! zone || el.type.value !== 'bump' || ! state.target ) {
			return;
		}
		const selector = cfg.zones.bump && cfg.zones.bump[ zone ];
		if ( ! selector ) {
			return;
		}
		try {
			state.target.querySelectorAll( selector ).forEach( ( n ) => n.classList.add( 'wps-ubo-hm-zone-highlight' ) );
		} catch ( e ) {}
	}

	function renderZones() {
		const zones = ( state.data && state.data.zones ) || {};
		const rows = Object.keys( zones ).map( ( key ) => ( { key, click: zones[ key ].click || 0, hover: zones[ key ].hover || 0 } ) );
		const totalClicks = rows.reduce( ( s, r ) => s + r.click, 0 );
		rows.sort( ( a, b ) => b.click - a.click || b.hover - a.hover );

		el.zones.innerHTML = '';
		if ( ! rows.length ) {
			const tr = document.createElement( 'tr' );
			const td = document.createElement( 'td' );
			td.colSpan = 4;
			td.textContent = cfg.i18n.noData;
			tr.appendChild( td );
			el.zones.appendChild( tr );
			return;
		}

		rows.forEach( ( r ) => {
			const share = totalClicks ? ( r.click / totalClicks ) * 100 : 0;
			const tr = document.createElement( 'tr' );
			tr.dataset.zone = r.key;

			const name = document.createElement( 'td' );
			name.textContent = cfg.zoneLabels[ r.key ] || r.key;
			const bar = document.createElement( 'span' );
			bar.className = 'wps-ubo-hm__bar';
			bar.style.width = share.toFixed( 1 ) + '%';
			name.appendChild( bar );

			const clicks = document.createElement( 'td' );
			clicks.className = 'num';
			clicks.textContent = fmt( r.click );

			const pct = document.createElement( 'td' );
			pct.className = 'num';
			pct.textContent = share.toFixed( 1 ) + '%';

			const hovers = document.createElement( 'td' );
			hovers.className = 'num';
			hovers.textContent = fmt( r.hover );

			tr.append( name, clicks, pct, hovers );
			tr.addEventListener( 'mouseenter', () => highlightZone( r.key ) );
			tr.addEventListener( 'mouseleave', () => highlightZone( null ) );
			el.zones.appendChild( tr );
		} );
	}

	/* ------------------------------------------------------------------ */
	/* Loading                                                            */
	/* ------------------------------------------------------------------ */

	function load( withPreview ) {
		const f = filters();
		const id = ++state.requestId;

		if ( ! f.offer_id || ( f.offer_type === 'upsell' && el.sub.options.length === 0 ) ) {
			clearPreview();
			state.data = null;
			el.empty.hidden = false;
			el.empty.textContent = cfg.i18n.noOffers;
			renderStats();
			renderZones();
			return;
		}

		el.loading.hidden = false;

		const previewPromise = withPreview
			? ( f.offer_type === 'upsell' ? loadUpsellPreview( f ) : loadBumpPreview( f ) )
			: Promise.resolve();

		Promise.all( [ post( 'wps_ubo_heatmap_data', f ), previewPromise ] )
			.then( ( [ data ] ) => {
				if ( id !== state.requestId ) {
					return; // A newer request superseded this one.
				}
				state.data = data;
				updateTemplates( data.templates );
				renderStats();
				renderZones();
				drawHeatmap();
			} )
			.catch( ( e ) => notify( e.message || cfg.i18n.error, 'error' ) )
			.finally( () => {
				if ( id === state.requestId ) {
					el.loading.hidden = true;
				}
			} );
	}

	/* ------------------------------------------------------------------ */
	/* Events                                                             */
	/* ------------------------------------------------------------------ */

	el.type.addEventListener( 'change', () => {
		populateOffers();
		load( true );
	} );
	el.offer.addEventListener( 'change', () => {
		populateSubOffers();
		resetTemplates();
		load( true );
	} );
	el.sub.addEventListener( 'change', () => {
		resetTemplates();
		load( true );
	} );
	el.device.addEventListener( 'change', () => load( true ) );
	el.template.addEventListener( 'change', () => load( false ) );
	el.days.addEventListener( 'change', () => load( false ) );

	root.querySelectorAll( 'input[name="wps-ubo-hm-mode"]' ).forEach( ( r ) => r.addEventListener( 'change', drawHeatmap ) );
	el.radius.addEventListener( 'input', debounce( drawHeatmap, 60 ) );
	el.opacity.addEventListener( 'input', () => ( el.canvas.style.opacity = parseInt( el.opacity.value, 10 ) / 100 ) );
	window.addEventListener( 'resize', debounce( drawHeatmap, 150 ) );

	el.settingsToggle.addEventListener( 'click', () => {
		const open = el.settings.hidden;
		el.settings.hidden = ! open;
		el.settingsToggle.setAttribute( 'aria-expanded', String( open ) );
	} );

	el.settings.addEventListener( 'submit', ( ev ) => {
		ev.preventDefault();
		const form = new FormData( el.settings );
		post( 'wps_ubo_heatmap_save_settings', {
			enabled: form.get( 'enabled' ) || 'off',
			track_hover: form.get( 'track_hover' ) || 'off',
			sample_rate: form.get( 'sample_rate' ) || 100,
			retention_days: form.get( 'retention_days' ) || 90,
		} )
			.then( ( saved ) => {
				const badge = root.querySelector( '.wps-ubo-hm__status' );
				if ( badge ) {
					badge.classList.toggle( 'is-on', saved.enabled === 'on' );
					badge.classList.toggle( 'is-off', saved.enabled !== 'on' );
					badge.textContent = saved.enabled === 'on' ? cfg.i18n.active : cfg.i18n.paused;
				}
				notify( cfg.i18n.saved );
			} )
			.catch( ( e ) => notify( e.message, 'error' ) );
	} );

	el.del.addEventListener( 'click', () => {
		if ( ! window.confirm( cfg.i18n.confirmDelete ) ) {
			return;
		}
		post( 'wps_ubo_heatmap_delete_all' )
			.then( () => {
				notify( cfg.i18n.deleted );
				load( false );
			} )
			.catch( ( e ) => notify( e.message, 'error' ) );
	} );

	// Boot: default to upsell if there are no bumps but there are funnels.
	if ( ! ( cfg.offers.bump || [] ).length && ( cfg.offers.upsell || [] ).length ) {
		el.type.value = 'upsell';
	}
	populateOffers();
	load( true );
}() );
