# WPS-7890 — Native Cart Abandonment Upsell Recovery

Today the plugin's only cart-abandonment feature is the **"Abandoned Cart Bump"** toggle. It needs both the Pro plugin and the separate **WooCommerce Cart Abandonment Recovery** plugin (by CartFlows), which detects abandoned carts and sends the emails; Pro then creates special "abandoned bumps" for those customers.

This feature adds a **built-in** abandoned-cart recovery flow that needs no other plugin. It:

1. **captures** carts (logged-in customers, and guests once they type their email at checkout);
2. **detects** abandonment after a configurable idle time;
3. **sends a sequence of recovery emails**. Each email can include an **upsell offer**: a product chosen from the cart's matching order bump, the Smart Recommendations engine or a fixed product, at an exclusive discount, with an optional one-time coupon;
4. **restores the cart** from the email in one click. **"Add to my order"** restores the cart *and* adds the offer at the email price. If the customer comes back without the offer, a **"Welcome back" offer box** appears at checkout;
5. **attributes recovered orders and accepted offers**, and reports them in a new **Cart Recovery** admin tab.

> **Nothing existing changes.** The external-plugin integration ("Abandoned Cart Bump" setting, `wps_is_abandoned_bump` bumps, the "Abandoned Cart Bump List" sub-menu) keeps working exactly as before. The native feature uses its own `wps_ubo_acr_*` table, options, session keys, cart-item keys and hooks, and is **off by default**. If the external plugin is active, the Cart Recovery tab warns that both would send emails.

---

## 1. File map

| File | Responsibility |
|---|---|
| `includes/class-upsell-order-bump-acr-db.php` | Table `{prefix}wps_ubo_acr_carts`, settings, queries, statistics, install/upgrade/uninstall |
| `includes/class-upsell-order-bump-acr-engine.php` | Scheduled processing every 5 minutes: abandonment detection, the email sequence, offer selection, coupons, "lost" marking, retention purge |
| `includes/class-upsell-order-bump-acr-mailer.php` | Email rendering (WooCommerce email wrapper), placeholders, cart table, offer block, preview and test send |
| `public/class-upsell-order-bump-acr-public.php` | Cart capture, recovery and unsubscribe links, the "Welcome back" checkout offer, offer pricing, order attribution, privacy exporter/eraser |
| `public/js/wps-ubo-acr.js`, `public/css/wps-ubo-acr.css` | Email capture at checkout (classic and block), consent notice / opt-out, the checkout offer box |
| `admin/class-upsell-order-bump-acr-admin.php` | "Cart Recovery" tab: settings, email steps, dashboard, carts list, preview and test email |
| `admin/partials/templates/wps-ubo-acr-display.php`, `admin/js/wps-ubo-acr-admin.js`, `admin/css/wps-ubo-acr-admin.css` | Tab UI |
| Core edits | Loader wiring, activator (table), uninstall. **No changes to bump or funnel code.** |

---

## 2. Data model

```
{prefix}wps_ubo_acr_carts
  id              BIGINT PK
  session_key     VARCHAR(64)  UNIQUE   -- WC session customer id (user id or guest hash)
  user_id         BIGINT
  email           VARCHAR(190)          -- index
  first_name      VARCHAR(100)
  cart            LONGTEXT              -- JSON [{product_id, variation_id, quantity, variation}]
  cart_total      DECIMAL(19,4)
  currency        CHAR(3)
  status          VARCHAR(20)           -- active | abandoned | recovered | lost | unsubscribed
  token           CHAR(32)     UNIQUE   -- recovery link token
  emails_sent     TINYINT
  next_email_at   DATETIME NULL (GMT)
  offer_product   BIGINT                -- decided at the first email that contains an offer
  offer_discount  TINYINT               -- %
  coupon_code     VARCHAR(50)
  clicked_at      DATETIME NULL
  order_id        BIGINT
  recovered_total DECIMAL(19,4)
  offer_accepted  TINYINT
  created_at, updated_at, abandoned_at, recovered_at (GMT)
  KEY status_updated (status, updated_at), KEY status_next (status, next_email_at)
```

**Statuses:**
- `active`: the cart is still being used, or is idle for less than the timeout.
- `abandoned`: idle longer than the timeout, with an email address, and no order yet. Emails are sent from this state.
- `recovered`: an order was placed after abandonment. `recovered_via` is `link` when the customer used the email link, and `direct` otherwise.
- `lost`: the whole sequence was sent, plus `lost_after_days` more, and there is still no order.
- `unsubscribed`: the customer used the unsubscribe link. Every later cart with that email is never emailed.

A cart that is still `active` when its order is placed is deleted, because it was a normal purchase. Rows older than `retention_days` are purged.

---

## 3. Settings (option `wps_ubo_acr_settings`)

| Key | Default | Meaning |
|---|---|---|
| `enabled` | `off` | Master switch |
| `abandon_minutes` | `30` | Idle minutes before a cart counts as abandoned (5–1440) |
| `capture_guests` | `on` | Capture guest carts once a valid email is typed at checkout |
| `consent_notice` | text | Short notice under the checkout email field, with an "opt out" link (empty = hidden) |
| `exclude_roles` | `administrator`, `shop_manager` | Roles that are never tracked |
| `offer_source` | `auto` | `auto` (Smart Recommendations → matching order bump → fixed product), `smart`, `bump` or `fixed` |
| `offer_product` | `0` | Product used by `fixed`, and the last fallback for `auto` |
| `offer_discount` | `15` | Default % off for the offer |
| `checkout_offer` | `on` | Show the "Welcome back" offer box at checkout after a recovery click |
| `coupon_expiry_days` | `7` | Validity of generated coupons |
| `lost_after_days` | `7` | Days after the last email before a cart is marked lost |
| `retention_days` | `90` | Carts older than this are deleted |
| `steps` | 3 steps | Each step has `enabled`, `delay`, `unit` (minutes/hours/days), `subject`, `heading`, `body`, `include_offer`, `discount` (blank = default) and `coupon` (% off cart, 0 = none) |

**Default sequence**, with delays counted from abandonment:

| # | Delay | Offer | Coupon | Subject |
|---|---|---|---|---|
| 1 | 1 hour | — | — | "You left something in your cart" |
| 2 | 1 day | yes | — | "Still thinking? Here's something extra for you" |
| 3 | 3 days | yes | 10% | "Last chance: your cart and an exclusive offer" |

**Email placeholders:**
- `{customer_name}`, `{site_name}`
- `{cart_items}` (table with images), `{cart_total}`
- `{recovery_link}`, `{recovery_button}`
- `{offer_block}`, `{offer_product}`, `{offer_price}`, `{offer_discount}`
- `{coupon_code}`, `{coupon_percent}`
- `{unsubscribe_link}`

`{offer_block}` and `{coupon_code}` render empty when the step has no offer or coupon. If the body leaves out `{unsubscribe_link}`, a footer link is added automatically.

---

## 4. Flows

### 4.1 Capture
- **Logged-in customers:** whenever the cart changes (`woocommerce_add_to_cart`, `woocommerce_cart_item_removed`, `woocommerce_after_cart_item_quantity_update`, `woocommerce_cart_emptied`), the row is upserted once per request at `shutdown`.
- **Guests:** once the email is typed on the checkout page, `wps-ubo-acr.js` sends it to the AJAX action `wps_ubo_acr_capture` (nonce checked, email validated with `is_email`). It listens on the classic `#billing_email` and the block checkout email field, debounced. The first name usually comes after the email on the form, so it is sent once more when the name field is filled in. Other fields never trigger a request, and a saved name is never overwritten with an empty one.
- **Never tracked:**
  - users in an excluded role;
  - visitors who used the opt-out link (stored in the session `wps_ubo_acr_optout`);
  - carts that are empty (an existing `active` row is deleted);
  - a cart already restored from a link, which keeps its row and token.
- **Stored data:** product ids, variation, quantity, cart total, currency, email and first name. No address and no IP.

### 4.2 Detection & email sequence (cron `wps_ubo_acr_process`, every 5 minutes)
This runs through Action Scheduler when available, otherwise WP-Cron, and handles at most 50 carts per run.

1. **Detect:** `active` carts with an email whose `updated_at` is older than the timeout become `abandoned`. `next_email_at` is set to abandonment time + the first enabled step's delay.
2. **Send**, for each `abandoned` cart with `next_email_at` ≤ now:
   - If an order exists for that email since abandonment, mark the cart `recovered` (`direct`) and skip the email.
   - If the email is unsubscribed, skip it.
   - If the step includes an offer and none is decided yet, **select the offer** (§4.3).
   - If the step has a coupon, **create it** (§4.4).
   - Send the email, increment `emails_sent`, and set `next_email_at` to the next enabled step, or null.
3. **Lost:** `abandoned` carts with no next email whose last email is older than `lost_after_days` become `lost`.
4. **Purge:** rows older than `retention_days` are deleted.

### 4.3 Offer selection (the upsell)
Candidates must not already be in the cart, and must be published, purchasable and in stock.

- **`smart`:** `Upsell_Order_Bump_Reco_Engine::recommend( cart parents )`, the top result. Requires Smart Recommendations to be enabled and trained.
- **`bump`:** the first **live** order bump whose target products or categories match the abandoned cart. A global bump matches any cart. Its offer product is used, and its % discount if the step has none.
- **`fixed`:** `offer_product`.
- **`auto`:** smart → bump → fixed.

The decision (product and %) is saved on the row, so every email and the checkout box show the same offer. Filter: `wps_ubo_acr_offer( $offer, $row )`.

### 4.4 Coupons
A unique `WC_Coupon` (`acr-xxxxxxxx`) is created with:
- percent cart discount, individual use, usage limit 1;
- `email_restrictions` = the customer's email;
- an expiry date of `coupon_expiry_days`;
- meta `_wps_ubo_acr_cart_id`.

One coupon is created per cart (reused by later steps); a later step with a higher % updates it.

### 4.5 Recovery link
- **Link format:** `?wps_ubo_acr=<token>[&wps_ubo_acr_add=1]`, handled on `template_redirect`.
- **Restoring the cart:**
  - the current cart is emptied and every item is re-added (unavailable products are skipped with a notice);
  - the coupon is applied if there is one;
  - `clicked_at` is set and the session `wps_ubo_acr_recovery = { cart_id, token }` is stored;
  - the row's `session_key` moves to the visitor's current session, so further edits update the same row;
  - the saved email and first name are put in the new session, so the "Welcome back, {name}" greeting works on another device.
- **`add=1` ("Add to my order" button in the email):** the offer product is also added as a cart item with `wps_ubo_acr_offer = { cart_id, discount }`.
- The visitor is then redirected to checkout, with the token removed from the URL.
- **Offer price:** applied on `woocommerce_before_calculate_totals` from the server-side row data, never from the request.

### 4.6 "Welcome back" checkout offer
When the session has a recovery, the cart has an offer and the offer isn't in the cart yet, an offer box is printed:
- **Classic checkout:** on `woocommerce_before_checkout_form`.
- **Block checkout:** prepended through `render_block_woocommerce/checkout`.

The box shows the product image, name, the regular price struck through, the offer price and an "Add to my order" checkbox. Toggling it calls the AJAX action `wps_ubo_acr_toggle_offer` (nonce checked; the product and discount come from the row) and refreshes the checkout.

### 4.7 Attribution
On `woocommerce_checkout_order_processed` and `woocommerce_store_api_checkout_order_processed`:
- **Matching:** the cart row is found from the recovery session, or else the session key, or else the abandoned row for the billing email.
- **Abandoned or lost row:** becomes `recovered`, with `order_id`, `recovered_total` and `recovered_via` set. `offer_accepted` = 1 if a line carries `wps_ubo_acr_offer`. The order gets meta `_wps_ubo_acr_recovered` = cart id.
- **Active row:** deleted.
- **Offer lines** get hidden item meta `_wps_ubo_acr_offer` ("Cart recovery offer (15%)").

### 4.8 Unsubscribe
`?wps_ubo_acr_unsub=<token>&sig=<hmac>`, where the signature is `wp_hash( token . 'unsub' )`. It sets the status to `unsubscribed` and shows a confirmation notice.

Email links usually open in a browser with no WooCommerce session yet. Before any link notice is added ("unsubscribed", "This cart link has expired", "already completed"), a guest session is started so the notice is not lost.

---

## 5. Admin — "Cart Recovery" tab
URL: `admin.php?page=upsell-order-bump-offer-for-woocommerce-setting&tab=cart-recovery`, added through the existing `wps_ubo_setting_tab` and `wps_ubo_lite_setting_tab_html` hooks.

- **Notices:** a warning when the external Cart Abandonment Recovery plugin is active, plus the result of each action (saved, test sent or failed, email sent, deleted).
  - They use the tab's own classes (`wps-ubo-acr__notice is-success|is-warning|is-error`), not `notice-success` / `notice-error` / `notice-warning`.
  - Reason: the existing settings page template replaces any notice with those classes inside a tab with a generic "Settings saved.", which would hide warnings and errors.
- **Dashboard (last 30 days):**
  - abandoned carts and emails sent;
  - recovered carts and the recovery rate;
  - recovered revenue;
  - offers accepted and the offer acceptance rate.
- **Settings:** every key in §3. The fixed offer product uses the WooCommerce product search.
- **Email sequence:** 3 step cards (enable, delay, subject, heading, body, offer and discount, coupon) with a placeholder list. Each step has **Preview** (opens the rendered email in a new tab) and **Send test** (to the admin email).
- **Carts:** filter by status, paginated 20 per page. Columns: email, status, cart (products × qty), total, emails sent, offer, last activity, order link. Row actions: delete, and "Send next email now" for abandoned carts.

Saving uses POST + redirect, with the nonce `wps_ubo_acr_admin` and the `manage_woocommerce` capability.

---

## 6. Security & privacy
- **Public AJAX** (`capture`, `optout`, `toggle_offer`) checks a nonce. Email is validated, cart data comes from `WC()->cart` and never from the request, and the offer product and discount come only from the row.
- **Links:** recovery tokens are 32 random characters (`wp_generate_password( 32, false )`), and unsubscribe links are signed with `wp_hash`.
- **Privacy:** a personal data **exporter and eraser** is registered (keyed by email), privacy-policy text is added, an opt-out link sits under the email field, and data is deleted after the retention period.
- **Prices** are always computed on the server from the row.
- **Escaping:** all admin output is escaped, and email bodies pass through `wp_kses_post`.

## 7. Hooks
| Hook | Type | Purpose |
|---|---|---|
| `wps_ubo_acr_should_track( $bool, $user_id )` | filter | Opt a visitor in or out of tracking |
| `wps_ubo_acr_offer( $offer, $row )` | filter | Change the selected offer `{ product_id, discount, source }` |
| `wps_ubo_acr_email_args( $args, $row, $step )` | filter | Change the subject, heading or body before sending |
| `wps_ubo_acr_cart_abandoned( $row )` | action | A cart became abandoned |
| `wps_ubo_acr_email_sent( $row, $step_index )` | action | After a recovery email |
| `wps_ubo_acr_cart_recovered( $row, $order )` | action | After attribution |

## 8. Lifecycle
- **Activation** and `admin_init` (`maybe_upgrade`) create the table. The schema version is stored in `wps_ubo_acr_db_version`.
- **Scheduling:** the cron is scheduled while the feature is enabled and unscheduled when it is disabled or the plugin is deactivated.
- **Turning the feature off:**
  - stops capture, sending and the checkout script;
  - links in emails already sent keep working: restore and unsubscribe still work, and the offer keeps its emailed price, so customers never land on a broken link.
- **Uninstall** (with "delete all data"): drops the table, deletes the options and unschedules the cron.

## 9. QA checklist
1. **Off by default:** with the feature off, nothing is tracked and nothing is scheduled. The existing Abandoned Cart Bump setting and submenu are unchanged.
2. **Capture:**
   - A guest types their email at checkout (classic and block): a row is created and follows cart edits.
   - A logged-in customer adding to the cart gets a row.
   - Admins are not tracked.
3. **Detection:** after the timeout the cart becomes `abandoned`, and email 1 is sent after its delay.
4. **Offer in emails:** email 2 shows the offer from the right source; email 3 adds the coupon, which is restricted to that email.
5. **Recovery links:**
   - "Restore cart" puts the items back and goes to checkout, and the "Welcome back" box appears.
   - "Add to my order" also adds the offer at the email price.
6. **Purchase:** the cart becomes `recovered (link)`, the offer is marked accepted, and the dashboard updates.
7. **Ordering without the link:** the next run marks the cart `recovered (direct)` and sends no more email.
8. **Unsubscribe:** no further emails for that address.
9. **Opt-out link** under the email field: the visitor is not tracked.
10. **Privacy tools:** the exporter and eraser cover the rows.
11. **Admin:** settings save and are clamped, Preview and Send test work, the carts list filters, and Delete and Send now work.
