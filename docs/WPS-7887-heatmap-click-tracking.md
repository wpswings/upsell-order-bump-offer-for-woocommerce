# WPS-7887 — Offer Heatmaps (Click & Hover Tracking)

> Track where customers click and hover on **Order Bump** templates (checkout / cart)
> and **One-Click Upsell** offer pages (post-purchase), then visualise the data as a
> heatmap in wp-admin so merchants can make template design decisions from real behaviour.

---

## 1. Goals & non-goals

| Goals | Non-goals |
| --- | --- |
| Capture clicks, sampled hover positions, and impressions on every rendered bump / upsell offer. | Full session recording or screen replays. |
| Store data anonymously (no PII, no IP, no user ID). | Tracking non-offer areas of the checkout page. |
| Show a heatmap overlaid on the **real** template preview in admin. | Cross-site/cross-domain tracking. |
| Show per-zone stats (which part of the template gets the attention). | A/B testing (this feature only provides the data an A/B decision needs). |
| Keep the checkout fast: zero blocking requests, batched beacons. | |

---

## 2. Where it lives (file map)

Follows the existing plugin layout (`includes/`, `admin/`, `public/`, `admin/partials/templates/`).

```
includes/
  class-upsell-order-bump-heatmap-db.php        # Table schema, install/upgrade, insert, aggregate queries, purge.
public/
  class-upsell-order-bump-heatmap-public.php    # Decides when to track, enqueues tracker, AJAX ingestion endpoint.
  js/wps-ubo-heatmap-tracker.js                 # Front-end tracker (vanilla JS, no jQuery dependency).
admin/
  class-upsell-order-bump-heatmap-admin.php     # Sub-menu, settings save, data AJAX, bump preview renderer, purge.
  partials/templates/wps-ubo-heatmap-display.php# Admin page markup.
  js/wps-ubo-heatmap-admin.js                   # Filters, canvas heatmap renderer, zone table.
  css/wps-ubo-heatmap-admin.css                 # Admin page styles.
docs/
  WPS-7887-heatmap-click-tracking.md            # This document.
```

Wiring changes in existing files:

* `includes/class-upsell-order-bump-offer-for-woocommerce.php` — requires the three new classes and registers their hooks through the existing `Loader` (`define_heatmap_hooks()`). Public hooks are only registered when the plugin is enabled (`wps_bump_enable_plugin`).
* `includes/class-upsell-order-bump-offer-for-woocommerce-activator.php` — creates the table on activation.
* `includes/class-upsell-order-bump-offer-for-woocommerce-deactivator.php` — clears the purge cron.
* `uninstall.php` — drops the table and options when *Delete all data on uninstall* is on.

The existing admin class is **not** modified: the heatmap page enqueues its own assets using the hook suffix returned by `add_submenu_page()` (robust against translated menu titles, which change the screen id).

---

## 3. End-to-end flow

```
 ┌──────────── Storefront ─────────────┐        ┌──────────── Server ───────────┐        ┌──────── wp-admin ────────┐
 │ checkout / cart / upsell offer page │        │ admin-ajax.php                │        │ Upsell Funnel Builder →  │
 │                                     │ beacon │ action=wps_ubo_heatmap_track  │        │ Offer Heatmaps           │
 │ wps-ubo-heatmap-tracker.js          │──────▶ │ validate → sanitize → insert  │──────▶ │ AJAX: aggregated grid    │
 │  • finds offer containers           │ (batch)│ {prefix}wps_ubo_heatmap_events│  SQL   │ canvas heatmap over the  │
 │  • view / click / hover events      │        │ daily cron purge (retention)  │ GROUP  │ real template preview    │
 └─────────────────────────────────────┘        └───────────────────────────────┘   BY   └──────────────────────────┘
```

### 3.1 When does the tracker load?

`Upsell_Order_Bump_Heatmap_Public::enqueue_tracker()` (on `wp_enqueue_scripts`) loads the script only if **all** are true:

1. Tracking is enabled in settings (default **on**).
2. The page can show an offer: `is_checkout()`, `is_cart()`, a page using `[wps_bump_offer_shortcode]`, **or** a live upsell offer request (`ocuf_fid` + `ocuf_ofd` in the URL).
3. The visitor is not a shop manager (`manage_woocommerce`) — admins previewing their own checkout would pollute data. Override with the `wps_ubo_heatmap_track_admins` filter.
4. The visitor passes sampling (`sample_rate` %, decided once per page view in JS).
5. Not the order-received endpoint.

### 3.2 What is a “container”?

| Offer type | Container element | Identified by |
| --- | --- | --- |
| Order Bump | `.wps_upsell_offer_main_wrapper` (every one of the 14 templates renders it, classic **and** block checkout, popup and shortcode) | `id="wps_upsell_offer_main_id_{bump_id}"` → `bump_id`. Template comes from a `bump_id → template` map localized from `wps_ubo_bump_list`. |
| Upsell offer page | the whole document (`document.documentElement`) — the page *is* the template | `funnel_id` = `ocuf_fid`, `offer_id` = `ocuf_ofd`, template from `wps_wocuf_funnels_list[funnel]['wps_wocuf_pro_offer_template'][offer]`. Localized server-side. |

A `MutationObserver` re-scans for bump containers because bumps are injected/refreshed by AJAX (`update_checkout`, block checkout, popup).

### 3.3 Events captured

| Event | Trigger | Notes |
| --- | --- | --- |
| `view` | Container is ≥ 50 % visible for the first time in this page view (`IntersectionObserver`). | Denominator for click-through rates. One per container per page view. |
| `click` | `pointerdown`/`click` inside the container. | Coordinates + zone. Max 30 per container per page view. |
| `hover` | `mousemove` inside the container, sampled every 250 ms, only when the pointer moved > 2 % since the last sample. | Desktop pointers only (`pointerType === 'mouse'`). Max 60 per container per page view. Can be switched off in settings. |

### 3.4 Coordinates

Coordinates are stored **normalised to the container** in basis points (0–10000), so they are layout independent:

```
x = round( (clientX - rect.left) / rect.width  * 10000 )
y = round( (clientY - rect.top)  / rect.height * 10000 )
```

For upsell pages the container is the document, so `x = pageX / scrollWidth`, `y = pageY / scrollHeight`.

Device class (`desktop` ≥ 1024 px, `tablet` ≥ 768 px, `mobile` otherwise) is recorded because templates reflow; admin filters by device and renders the preview at a matching width.

### 3.5 Zones (semantic element)

Every click/hover is also tagged with the **zone** it landed in, resolved by walking up from `event.target` with `closest()` against an ordered selector map. This survives template redesigns better than raw pixels and powers the “Zone breakdown” table.

Bump zones (first match wins):

| Zone | Selectors |
| --- | --- |
| `quantity` | `.wps_quantity_input`, `.wps_input_quantity`, `.wps-ubo__qty-btn`, `.quantity` |
| `variation` | `select`, `.wps-option`, `.wps_variable_qty_temp_12` |
| `accept_checkbox` | `.add_offer_in_cart`, `.wps_upsell_bump_checkbox_container`, `.wps-ubo__temp-prod-check`, `.upsell-order-check`, `.checkmark` |
| `accept_button` | `.wps-ubo__btn`, `.custom-btn`, `button` |
| `accept_text` | `.add_offer_in_cart_text`, `.wps_upsell_offer_primary_section` (the accept bar around the checkbox) |
| `image` | `.wps_upsell_offer_img`, `.wps_upsell_offer_image`, `.upsell-product-img`, `.wps-ob_ta-p-img`, `img` |
| `title` | `.wps_bump_name`, `.bump-offer-product-name`, `.wps-ob_ta-p-name`, `.wps-ob_ta-o-title`, `.wps_upsell_product_permalink` |
| `price` | `.wps_upsell_offer_product_price`, `.product-price`, `.wps-ob_ta-p-price`, `.wps-ubo__temp-prod-price-new` |
| `timer` | `.wps-timer-wrap`, `.upsell-offer-timer-section` |
| `discount` | `.wps_upsell_offer_discount_section`, `.uspell-offer-discount` |
| `offer_text` | `.wps_upsell_offer_secondary_section`, `.wps-ob_ta-o-desc` |
| `description` | `.wps_upsell_offer_product_description`, `.upsell-product-desc`, `.wps-ob_ta-p-desc` |
| `other` | anything else inside the container |

Resolution walks from the clicked element up to the container; at each level the zones are tested in the order above, so the **innermost** match wins (e.g. a `<button class="wps-ubo__qty-btn">` is `quantity`, not `accept_button`). A `<label>` click and the click it re-dispatches to its checkbox are de-duplicated (60 ms window).

Upsell page zones: `accept_button` (`a[href*="wps_wocuf_pro_buy"]`, `[name="wps_wocuf_pro_buy"]`, `.wps_wocuf_pro_custom_buy`), `decline_button` (`a[href*="ocuf_th=1"]`, `.wps_wocuf_pro_no`, `.wps_wocuf_pro_skip`), `quantity`, `variation`, `image` (`.wps_upsell_offer_product_image`), `price`, `timer` (`.wps_upsell_lite_display_timer_wrap`), `other`.

Both maps are filterable server-side (`wps_ubo_heatmap_zone_selectors`) so the Pro plugin or custom templates can add zones.

### 3.6 Transport

* Events are queued in memory and flushed every **10 s**, when the queue reaches **25** events, and on `visibilitychange → hidden` / `pagehide` (this is important: accepting a bump reloads the checkout fragment and accepting an upsell navigates away).
* Flush uses `navigator.sendBeacon()` with `FormData` (falls back to `fetch(…, {keepalive:true})`). Never blocks checkout.
* Payload: `action`, `nonce`, `device`, `vid` (random per-tab id from `sessionStorage`, no cookie) and `events` — a JSON array of compact objects `{t: offer_type, o: offer_id, s: sub_offer_id, e: event_type, x, y, z: zone, c: context}`.
* Config is printed with `wp_add_inline_script()` + `wp_json_encode()` (not `wp_localize_script()`, which would stringify numbers/booleans).
* Known limitation: on full-page-cached checkout pages a nonce older than 24 h is rejected, so events from those stale pages are dropped (checkout is normally excluded from page cache).

---

## 4. Server-side ingestion

`wp_ajax_wps_ubo_heatmap_track` + `wp_ajax_nopriv_wps_ubo_heatmap_track` → `Upsell_Order_Bump_Heatmap_Public::ingest()`.

Validation pipeline (anything failing is silently dropped, response is always `204`-like JSON so the tracker never retries):

1. Tracking enabled.
2. `check_ajax_referer( 'wps_ubo_heatmap_track', 'nonce', false )`.
3. Rate limit: per hashed IP (never stored), max **60 requests / minute** (60 s transient). Normal browsing sends ~6–10 / minute.
4. `events` decoded, capped at **100** per request.
5. Every field whitelisted / clamped:
   * `offer_type` ∈ {`bump`, `upsell`}
   * `event_type` ∈ {`view`, `click`, `hover`}
   * `offer_id` must exist in `wps_ubo_bump_list` (bump) or `wps_wocuf_funnels_list` (upsell); `sub_offer_id` absint.
   * `x`, `y` clamped to 0–10000; `zone` must be in the known zone list (else `other`); `device` ∈ {desktop, tablet, mobile}; `ctx` ∈ {checkout, cart, checkout_block, cart_block, shortcode, popup, upsell_page}.
   * `template` is **not trusted from the client**; it is looked up server-side from the saved offer.
6. Bulk insert with a single prepared multi-row `INSERT`.

### 4.1 Privacy

* No IP, user ID, email or order data is stored.
* `session_hash` = `md5( wp_salt('nonce') . vid . gmdate('Y-m-d') )` where `vid` is a random id kept in `sessionStorage` — rotates daily, lets us count unique visitors per day without identifying anyone and without setting a cookie.
* Data is auto-deleted after the retention period (default **90 days**).
* Documented in the plugin's privacy policy text through `wp_add_privacy_policy_content()`.

---

## 5. Database

Table: `{$wpdb->prefix}wps_ubo_heatmap_events` (created with `dbDelta`; version tracked in option `wps_ubo_heatmap_db_version`, auto-upgraded on `admin_init` so sites that update without re-activating also get it).

| Column | Type | Notes |
| --- | --- | --- |
| `id` | BIGINT UNSIGNED PK AI | |
| `offer_type` | VARCHAR(10) | `bump` / `upsell` |
| `offer_id` | BIGINT UNSIGNED | Bump id, or funnel id |
| `sub_offer_id` | BIGINT UNSIGNED | Upsell offer index inside the funnel (0 for bumps) |
| `template` | VARCHAR(20) | Template id at time of event (so redesigns can be compared) |
| `event_type` | VARCHAR(10) | `view` / `click` / `hover` |
| `x` | SMALLINT UNSIGNED | 0–10000 |
| `y` | SMALLINT UNSIGNED | 0–10000 |
| `zone` | VARCHAR(30) | Semantic zone |
| `device` | VARCHAR(10) | desktop / tablet / mobile |
| `context` | VARCHAR(20) | where it was shown |
| `session_hash` | CHAR(32) | Anonymous, daily-rotating |
| `created_at` | DATETIME | GMT |

Indexes: `offer_lookup (offer_type, offer_id, sub_offer_id, event_type, created_at)`, `created_at (created_at)`.

### 5.1 Aggregation (what admin actually loads)

Raw rows are never sent to the browser. Points are binned to a **100 × 100 grid** server-side:

```sql
SELECT FLOOR(x/100) gx, FLOOR(y/100) gy, COUNT(*) c
FROM   {table}
WHERE  offer_type=%s AND offer_id=%d AND sub_offer_id=%d AND event_type=%s
       AND created_at BETWEEN %s AND %s [AND device=%s] [AND template=%s]
GROUP  BY gx, gy
```

Plus a zone breakdown (`GROUP BY zone, event_type`) and totals (`views`, `clicks`, `hovers`, `unique visitors`). Max payload ≈ 10 000 cells, typically a few hundred.

---

## 6. Admin UI

Menu: **Upsell Funnel Builder → Offer Heatmaps** (`admin.php?page=upsell-order-bump-offer-for-woocommerce-heatmaps`, capability `manage_woocommerce`).

```
┌─────────────────────────────────────────────────────────────────────────────┐
│ Offer Heatmaps                                         [Settings] [Delete…] │
├─────────────────────────────────────────────────────────────────────────────┤
│ Offer type [Order Bump ▾]  Offer [Bump #3 – Summer ▾]  Device [All ▾]       │
│ Date [Last 30 days ▾]   Mode (●) Clicks ( ) Hovers   Radius ━━●━  Opacity ━●│
├──────────────┬──────────────┬──────────────┬────────────────────────────────┤
│ Impressions  │ Clicks       │ Click rate   │ Unique visitors                │
│ 4 210        │ 912          │ 21.7 %       │ 3 380                          │
├──────────────┴──────────────┴──────────────┴────────────────────────────────┤
│  ┌────────── real template preview + canvas overlay ──────────┐  Zones      │
│  │  ░░▒▒▓▓██  ← accept checkbox hot spot                      │  accept 61% │
│  │            ▒▒ image                                         │  image  14% │
│  └─────────────────────────────────────────────────────────────┘  title   9% │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 6.1 Preview backdrop

* **Order Bump** — rendered server-side with the *same functions the checkout uses* (`wps_ubo_lite_fetch_bump_offer_details()` + `wps_ubo_lite_bump_offer_html*()`), exactly like the existing “Offer Preview” on the bump edit screen. Returned via AJAX, injected into a sandboxed wrapper sized to the device width (desktop 600 px / tablet 480 px / mobile 340 px — the bump is a column component). Normalised coordinates map 1:1 onto the wrapper's box.
* **Upsell offer page** — loaded in a same-origin `<iframe>` of the offer page with `?wps_ubo_heatmap_preview=1` (the existing `admin_view` mode of the shortcodes renders the product; the admin bar is suppressed for that request). Iframe width = 1280 / 768 / 375 px by device, scaled to fit with CSS `transform`. The iframe is `sandbox="allow-same-origin"` (no scripts, forms or navigation); a small style is injected to reveal page-builder elements that are normally animated in by JS (`.elementor-invisible`). After load (and again after lazy images settle) the iframe height is set to the document's `scrollHeight` (max 8000 px) so coordinates map 1:1. A transparent overlay blocks interaction with the preview.

### 6.2 Heatmap renderer

Vanilla canvas, no third-party library (keeps us off a new dependency for WordPress.org review):

1. For each grid cell draw a radial alpha gradient (radius from slider) with alpha ∝ `count / max` onto an off-screen canvas.
2. Read pixels, map each alpha through a 256-step colour gradient (blue → cyan → lime → yellow → red).
3. Draw onto the visible canvas with the chosen opacity.

Zone table lists each zone's clicks, share of clicks, hovers, and click rate (`zone clicks / views`), sorted by clicks. Hovering a zone row highlights matching elements in the preview (bump only).

### 6.3 Settings (same page, collapsible)

Stored in option `wps_ubo_heatmap_settings`:

| Key | Default | Meaning |
| --- | --- | --- |
| `enabled` | `on` | Master switch for tracking. |
| `track_hover` | `on` | Record sampled hover positions. |
| `sample_rate` | `100` | % of page views tracked (lower on high-traffic stores). |
| `retention_days` | `90` | Auto-purge older events (daily cron `wps_ubo_heatmap_purge`). |

“Delete all heatmap data” button → nonce-protected AJAX `TRUNCATE`.

---

## 7. Security checklist

* All admin AJAX: `check_ajax_referer()` + `current_user_can( 'manage_woocommerce' )`.
* Ingestion: nonce, rate limit, whitelist validation, server-side template lookup, prepared statements, row caps.
* Bump preview HTML is passed through `wp_kses( …, wps_ubo_lite_allowed_html() )` — same as the edit screen.
* Iframe preview is same-origin and only built from URLs/permalinks saved in the funnel; `esc_url()` applied.
* All output escaped; all strings translatable with the `upsell-order-bump-offer-for-woocommerce` text domain.

## 8. Performance

* Tracker ≈ 5 KB, `defer`-style footer load, passive listeners, throttled hover, no layout thrashing (one `getBoundingClientRect` per event).
* Ingestion is one multi-row INSERT per beacon.
* Composite index covers all admin queries; data is binned in SQL.
* Retention cron keeps the table bounded.

## 9. Extensibility (hooks)

| Hook | Type | Purpose |
| --- | --- | --- |
| `wps_ubo_heatmap_should_track` | filter | Final say on loading the tracker for a request. |
| `wps_ubo_heatmap_track_admins` | filter | Allow tracking shop managers (default `false`). |
| `wps_ubo_heatmap_zone_selectors` | filter | Add / change zone selector maps. |
| `wps_ubo_heatmap_tracker_config` | filter | Adjust tracker config (flush interval, caps). |
| `wps_ubo_heatmap_before_insert` | filter | Modify / drop sanitized rows before insert. |
| `wps_ubo_heatmap_events_inserted` | action | Fired after a batch is stored. |

## 10. QA checklist

1. Classic checkout, bump template 1 → click checkbox → row with `event_type=click`, `zone=accept_checkbox`.
2. Block checkout bump → same as above, `context=checkout_block`.
3. Bump in popup mode and via `[wps_bump_offer_shortcode]` → tracked.
4. Accept bump (fragment refresh) → pending events still flushed (pagehide/visibility + MutationObserver re-binds).
5. Complete order → upsell offer page → click “Add to my order” → `offer_type=upsell`, `zone=accept_button` stored before navigation.
6. Logged in as shop manager → no tracker script on checkout.
7. Admin page: choose bump, device filter, date range, clicks/hovers toggle → heatmap & zone table update; empty state shown when no data.
8. Settings off → script not enqueued; ingestion rejects.
9. Retention 1 day + run cron → older rows deleted. “Delete all” empties table.
10. Uninstall with *delete all data* on → table + options removed.
11. Spoofed payloads (unknown bump id, x = 99999, zone = `<script>`) → dropped / clamped.
