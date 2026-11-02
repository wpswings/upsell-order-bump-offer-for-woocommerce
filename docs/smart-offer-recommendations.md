# Smart Offer Recommendations (ML + WordPress AI Connectors)

Order bumps and upsell funnels currently trigger only from **static rules**: the admin picks target products and categories, and the offer shows when one of them is in the cart or order. This feature keeps those rules and adds an **ML-driven recommendation engine**. The engine learns from the store's own order history which products customers actually buy together, or buy next. It can be used in two ways, chosen per offer:

1. **Decide when an offer shows (Smart targeting):** the bump or funnel triggers when the cart or order is strongly associated with the offer product, without any admin-selected target products or categories.
2. **Decide which product is offered (AI offer product):** the engine picks the best product for this cart or order. The admin-selected offer product is the fallback.

Optionally, the model is **enriched by an LLM** through the **WordPress AI Client and Connectors API** (WordPress 7.0+, **Settings → Connectors**). The LLM re-ranks candidates by how well they complement each other, and covers products with too little sales history ("cold start"). No API keys are stored by this plugin; it uses whichever provider the site owner connected (Anthropic, Google, OpenAI or any provider plugin).

---

## 1. Goals / non-goals

**Goals**
- Rule-based targeting stays the **default** for every existing bump and funnel. Nothing changes until an admin switches an offer to Smart/Hybrid or AI product.
- Recommendations are learned from real purchase patterns: co-purchase within the same order, and sequential purchases by the same customer within N days.
- Runs on any host. The model is trained in the background, and the checkout only does one indexed lookup.
- AI (LLM) use is optional, offline and bounded: it runs only during training, for a capped number of products, and never at checkout.
- Privacy: only **catalog data** (product name, categories, price, short description) is sent to the AI provider. No customer, order or personal data is ever sent.

**Non-goals**
- Real-time online learning, or per-user embeddings.
- Changing discount rules. The offer discount stays whatever the admin configured (see §7.3 for the fixed-price safeguard).

---

## 2. File map

| File | Responsibility |
|---|---|
| `includes/class-upsell-order-bump-reco-db.php` | Tables, settings, model meta, install/upgrade/uninstall, cron scheduling |
| `includes/class-upsell-order-bump-reco-engine.php` | Training (SQL path and PHP fallback), scoring, `recommend()` / `score_for()` |
| `includes/class-upsell-order-bump-reco-ai.php` | WordPress AI Client / Connectors integration: status, enrichment, connection test |
| `public/class-upsell-order-bump-reco-public.php` | Checkout and funnel integration: smart match, product resolution, option overrides |
| `admin/class-upsell-order-bump-reco-admin.php` | "Smart Recommendations" page, AJAX, per-bump/per-funnel fields |
| `admin/partials/templates/wps-ubo-reco-display.php` | Admin page markup |
| `admin/js/wps-ubo-reco-admin.js`, `admin/css/wps-ubo-reco-admin.css` | Admin UI |
| Edits in core files | 2 hook points in the bump form, 2 in the funnel form, 2 small integrations in `class-upsell-order-bump-offer-for-woocommerce-public.php`, loader wiring, activator/deactivator/uninstall |

---

## 3. Settings

### 3.1 Global (Upsell Funnel Builder → Smart Recommendations)

Stored in option `wps_ubo_reco_settings`:

| Key | Default | Meaning |
|---|---|---|
| `enabled` | `off` | Master switch. When off, every offer behaves as rule-based. |
| `lookback_days` | `365` | Orders older than this are ignored during training. |
| `min_support` | `3` | Minimum number of orders/customers in which a pair must occur. |
| `min_confidence` | `5` (%) | Minimum P(B \| A) for a pair to be kept. |
| `min_lift` | `1.0` | Pairs whose lift is below this are dropped (1.0 = no better than chance). |
| `seq_window_days` | `60` | "Bought A, then B within N days" signal. `0` disables it. |
| `seq_weight` | `50` (%) | How much a sequential purchase counts compared with a same-order purchase. |
| `match_threshold` | `10` (%) | Minimum score for Smart targeting to fire. |
| `exclude_purchased` | `on` | When AI picks the product, skip products the logged-in customer already bought. |
| `max_candidates` | `20` | Recommendations kept per product. |
| `schedule` | `daily` | `daily`, `weekly` or `manual` retraining. |
| `ai_enabled` | `off` | Use the WordPress AI connector to enrich the model. |
| `ai_provider` | `''` | `''` = let WordPress pick any configured provider, or a connector id (`anthropic`, `google`, `openai`, …). |
| `ai_anchor_limit` | `20` | Maximum products sent to the LLM per training run, best sellers first. Bounds cost. |
| `ai_weight` | `30` (%) | How much the LLM score counts in the blended score. |
| `ai_cold_start` | `on` | Ask the LLM to suggest complements for products with no co-purchase data. |

### 3.2 Per order bump (bump edit screen) and per funnel (funnel edit screen)

| Key (stored in the bump/funnel array) | Values | Default |
|---|---|---|
| `wps_ubo_targeting_mode` | `static`: rule-based target products/categories (current behaviour)<br>`smart`: AI recommendations decide; target fields are ignored<br>`hybrid`: rules **or** smart match | `static` |
| `wps_ubo_offer_product_source` | `static`: the selected offer product<br>`ai`: AI-recommended product, falling back to the selected one | `static` |

The fields render through new hooks in the existing forms:
- Bump form: action `wps_ubo_bump_targeting_fields`, filter `wps_ubo_bump_before_save`.
- Funnel form: action `wps_ubo_funnel_targeting_fields`, filter `wps_ubo_funnel_before_save`.

Pro or other add-ons can use the same hooks.

---

## 4. Data model

```
{prefix}wps_ubo_reco_products
  product_id   BIGINT UNSIGNED PK   -- parent product id
  order_count  INT UNSIGNED         -- orders containing the product in the window

{prefix}wps_ubo_reco_pairs
  source_id   BIGINT UNSIGNED   -- "anchor" (in cart / order)
  target_id   BIGINT UNSIGNED   -- recommended product
  co_count    INT UNSIGNED      -- orders containing both
  seq_count   INT UNSIGNED      -- customers who bought target within N days after source
  ml_score    FLOAT             -- confidence (0..1)
  lift        FLOAT
  ai_score    FLOAT NULL        -- LLM complementarity (0..1), NULL = not evaluated
  ai_reason   VARCHAR(191)      -- short LLM rationale (admin insight only)
  score       FLOAT             -- final blended score used at runtime
  PRIMARY KEY (source_id, target_id), KEY target_id, KEY source_score (source_id, score)
```

Training writes into `*_tmp` twin tables and then swaps them atomically with `RENAME TABLE`. The checkout therefore never reads a half-built model.

Model metadata is stored in option `wps_ubo_reco_model`:
- `status` (`idle` / `training` / `error`), `trained_at`, `duration`
- `source` (`analytics` / `orders`), `orders`, `products`, `pairs`
- `ai_status`, `ai_anchors`, `ai_error`, `version`

`version` is part of every session cache key, so a retrain invalidates all cached decisions.

---

## 5. Training (the "ML")

The model is item-to-item association-rule mining, a form of market-basket collaborative filtering. On top of it there is a sequential-purchase signal and optional LLM re-ranking.

### 5.1 Data source
1. **Analytics path (preferred).** WooCommerce's `wc_order_product_lookup` joined with `wc_order_stats`. All counting is done in SQL, so it scales to large stores, and it works the same with HPOS and legacy order storage.
   - This path is used when the analytics tables cover at least 90% of the paid orders in the window (`wc_get_orders` total).
2. **Orders fallback.** When analytics data is missing or not yet imported, the engine pages through `wc_get_orders()` (status `completed` / `processing`, inside the window). It reads at most `wps_ubo_reco_max_orders` orders (filterable, default 5000), counts in PHP, and bulk-inserts the result.

Order statuses are filterable (`wps_ubo_reco_order_statuses`, default `wc-completed`, `wc-processing`). Variations collapse to the parent product.

### 5.2 Scores
For anchor *A* and candidate *B*, with *N* = paid orders in the window:

"Bought next" is made **directional** before scoring:

```
seq(A,B) = max(0, after(A→B) − after(B→A))
```

Repeat customers buy most products in both orders over time, so a sequence only counts when it happens more often *after* A than *before* it. Without this, a store with many loyal customers sees almost every pair as "bought next".

```
support     = co(A,B) + seq(A,B)
confidence  = (co(A,B) + w_seq · seq(A,B)) / orders(A)            → ml_score (capped at 1)
lift        = confidence / (orders(B) / N)
keep pair if support ≥ min_support AND confidence ≥ min_confidence AND lift ≥ min_lift
keep only the top `max_candidates` pairs per anchor
```

### 5.3 LLM enrichment (optional, §6)
```
score = (1 − w_ai) · ml_score + w_ai · ai_score   when ai_score is set
score = ml_score                                  otherwise
```

### 5.4 Runtime: combining several cart items (noisy-OR)
```
score(B | cart) = 1 − Π_{A ∈ cart} (1 − score(A,B))
```
More supporting items raise the score, but it never exceeds 1.

### 5.5 Scheduling
- Hook `wps_ubo_reco_train`, run through **Action Scheduler** when available (bundled with WooCommerce), otherwise WP-Cron. Recurs `daily` or `weekly`, or `manual`.
- **Train now** in admin queues an async run, and the page polls the status.
- A transient lock (`wps_ubo_reco_training_lock`, 15 min) prevents concurrent runs.

---

## 6. WordPress AI connector integration

WordPress 7.0 ships the **PHP AI Client** (`wp_ai_client_prompt()`) and the **Connectors** screen (**Settings → Connectors**). There, site owners install a provider plugin and add an API key once (stored by core, or set via an env var or constant). Any plugin can then use AI without handling keys.

### 6.1 Availability check (`Upsell_Order_Bump_Reco_Ai::status()`)
AI is available when all of the following are true:
- `function_exists( 'wp_ai_client_prompt' )` (WordPress 7.0+);
- `wp_supports_ai()` is true (respects `WP_AI_SUPPORT` and the `wp_supports_ai` filter);
- at least one connector of type `ai_provider` is active (its provider plugin is installed) and has a key from the env, a constant or the database;
- `wp_ai_client_prompt()->is_supported_for_text_generation()` returns true (for the chosen provider when one is set).

The admin page lists every AI connector with its state and links to `options-connectors.php`.

### 6.2 Enrichment request (during training only)
For the top `ai_anchor_limit` products by order count, one prompt is sent per anchor:

```php
wp_ai_client_prompt( $user_prompt_json )
    ->using_system_instruction( $merchandiser_instruction )
    ->using_provider( $provider )          // only when the admin picked one
    ->using_temperature( 0.2 )
    ->as_json_response( $schema )         // { recommendations: [ { product_id, score, reason } ] }
    ->generate_text();
```

- **Input:** the anchor (id, name, categories, price, trimmed short description) and up to 15 candidates. Candidates are the anchor's ML pairs, plus best sellers when `ai_cold_start` is on and the anchor has fewer than 5 pairs. **Only catalog data is sent.**
- **Output validation:** the result is decoded as JSON, unknown ids are dropped, scores are clamped to [0,1], and reasons are cut to 191 characters.
- Results update `ai_score`, `ai_reason` and `score` in the tmp pairs table before the swap. Cold-start suggestions are inserted as new pairs (`co_count = 0`).
- **Failure handling:** a `WP_Error` is recorded in `ai_error`, and enrichment stops after 3 consecutive failures. The model is still published with ML-only scores, so AI never blocks training.
- **Cost bound:** at most `ai_anchor_limit` requests per training run, each with a small token budget (`using_max_tokens( 800 )`).

A **Test AI connection** button sends a one-line prompt and shows the provider's reply or error.

---

## 7. Runtime integration

### 7.1 Order bump (checkout, cart, block checkout, shortcode, popup)
In `Upsell_Order_Bump_Offer_For_Woocommerce_Public::fetch_order_bump_from_collection()`:

1. `prepare_bump()` runs first. When `wps_ubo_offer_product_source = ai`, it resolves the product for the current cart:
   - **Inputs:** cart parent product ids, excluding items added by bumps.
   - **Candidate filters:** the candidate must not be in the cart, must be published, purchasable and in stock, and must not be grouped or external. If `exclude_purchased` is on, it must not have been bought before by this logged-in customer.
   - **Choice:** the best candidate wins. If none qualifies, the admin product is used.
   - **Caching:** the decision is stored in the WC session as `wps_ubo_reco_bump[bump_id] = { pid, sig, v, score }`, where `sig` is the cart signature.
   - **Freezing:** once the bump product is in the cart, the decision is **frozen**, so accepting the offer, removing it and pricing stay consistent.
2. Targeting:
   - `static`: unchanged code path.
   - `smart`: the target-id and category loops are skipped. The bump fires when `score(offer product | cart) ≥ match_threshold`. The strongest anchor's cart key becomes the target key, which keeps "Smart Offer Upgrade" working.
   - `hybrid`: rules are tried first; if they don't match, the smart match is tried.
   - The "both target lists empty → skip" guard no longer applies to `smart`/`hybrid`.
3. Every later read of `wps_ubo_bump_list` in the same session goes through the `option_wps_ubo_bump_list` filter. The resolved product is substituted there, so the template, the add-to-cart AJAX, the variation popup and validations all see the same product without touching the 35 call sites.

### 7.2 Upsell funnel (post-purchase)
In `wps_wocuf_initiate_upsell_orders()`:

1. `prepare_upsell_order( $order )` runs before the funnels are loaded. For each live funnel with source `ai`, offer slot *k* gets the *k*-th best distinct recommendation for the order items, or the admin product for that slot when there aren't enough. The result is saved to order meta `_wps_ubo_reco_upsell` as `{ funnel_id: { offer_id: { pid, score } } }`, and the order becomes the request context.
2. Targeting is as for bumps: `smart` / `hybrid` use `funnel_matches_order()`, which checks the funnel's (resolved) first-offer product against the order items.
3. The offer pages, the charge request and the shortcodes read `wps_wocuf_funnels_list`. The `option_wps_wocuf_funnels_list` filter substitutes the stored product per offer. The order is identified from the request context or the `ocuf_ok` order key in the URL.

### 7.3 Safety rules
- **Never persisted:** `pre_update_option_wps_ubo_bump_list` and `pre_update_option_wps_wocuf_funnels_list` restore the raw admin product ids before any write, for example the exclusive-offer save in the funnel loop. Substituted products therefore never leak into the stored settings.
- **Front-end only:** substitution happens only on front-end requests and front-end AJAX (requests with a WC session or an `ocuf_ok` order context). Admin screens always show the admin's settings.
- **Fixed-price discounts:** a fixed price set for product X makes no sense for product Y. When AI substitutes a different product, a bump with price type `fixed` is shown as `no_disc`, and an upsell discount that isn't a percentage becomes `0%`. Percentage discounts and "no discount" apply as configured.
- **Graceful fallback:** if the engine is disabled, no model exists, the WC session is missing or no recommendation qualifies, behaviour falls back to rules and the admin product. A `smart` offer with no model behaves like `static`, so offers never disappear silently.

---

## 8. Admin UI — "Smart Recommendations"

The page is a sub-menu under the plugin menu (`upsell-order-bump-offer-for-woocommerce-smart-reco`, capability `manage_woocommerce`). It contains:

- **Status cards:**
  - Engine on/off.
  - Model: last trained, data source, orders and pairs.
  - AI: connector or provider in use, last enrichment, last error.
- **AI Connectors panel:** every `ai_provider` connector, with **Installed** / **Key set** badges and a link to Settings → Connectors.
- **Settings form:** all keys from §3.1, saved over AJAX.
- **Actions:**
  - **Train now:** async, with a live status poll.
  - **Test AI connection.**
  - **Delete model.**
- **Try it:** pick products as a simulated cart and see the ranked recommendations with ML, AI and final score and the AI reason. It reads only the stored model and makes no LLM call.
- **Top associations:** the 25 strongest pairs (anchor → recommended, co-orders, confidence, lift, AI score, reason).
- **Offers using AI:** each bump and funnel with a non-default mode or product source.

On the bump and funnel edit screens, a **Smart targeting** block appears right after "Select target categories", with the two selects from §3.2. A notice is shown there while the engine is off.

---

## 9. Hooks

| Hook | Type | Purpose |
|---|---|---|
| `wps_ubo_bump_targeting_fields( $bump_id, $bump )` | action | Render extra targeting fields on the bump form |
| `wps_ubo_bump_before_save( $bump, $bump_id )` | filter | Sanitize and attach extra bump fields |
| `wps_ubo_funnel_targeting_fields( $funnel_id, $funnel )` | action | Render extra targeting fields on the funnel form |
| `wps_ubo_funnel_before_save( $funnel, $funnel_id )` | filter | Sanitize and attach extra funnel fields |
| `wps_ubo_reco_order_statuses` | filter | Order statuses used for training |
| `wps_ubo_reco_max_orders` | filter | Order cap for the PHP fallback |
| `wps_ubo_reco_candidates( $scores, $context_ids, $context )` | filter | Modify ranked candidates before filtering (`$context` = `bump` / `upsell` / `preview`) |
| `wps_ubo_reco_bump_product( $pid, $bump_id, $bump )` | filter | Override the resolved bump product |
| `wps_ubo_reco_ai_prompt( $payload, $anchor_id )` | filter | Adjust the LLM payload (it must stay catalog-only) |
| `wps_ubo_reco_trained( $meta )` | action | Fired after a model is published |

---

## 10. Security & privacy
- Admin AJAX requires the `manage_woocommerce` capability and a nonce (`wps_ubo_reco_admin`). All inputs are whitelisted and clamped.
- All SQL uses `$wpdb->prepare`. Table names come from `$wpdb->prefix` constants only.
- **No new front-end endpoints:** all runtime work happens inside existing checkout and funnel requests.
- **LLM payload:** catalog fields only, with HTML stripped and text truncated. A privacy-policy paragraph is registered via `wp_add_privacy_policy_content()`.
- API keys are never read or stored by this plugin. They stay in WordPress core Connectors.

## 11. Performance
- **Checkout cost:** at most one indexed query per request (`WHERE source_id IN (cart ids)`), cached per request. Decisions are cached in the WC session.
- **Training:** runs in the background. On the SQL path, all counting is done in a few `INSERT … SELECT` statements.
- **LLM:** offline only, capped number of requests, 30 s timeout per request (WordPress core default).

## 12. Lifecycle
- **Activation:** creates the tables and default settings. The schema is versioned with `wps_ubo_reco_db_version`, and `maybe_upgrade` runs on `admin_init`.
- **Deactivation:** unschedules training.
- **Uninstall** (when "delete all data" is on): drops the tables and deletes the options and the order meta key.

## 13. QA checklist
1. **Default behaviour:** with the engine off, bumps and funnels behave exactly as before.
2. **Training:**
   - Create ≥ 20 orders where A+B are bought together.
   - Click Train now: the status ends `idle`, and pairs > 0.
   - The "Try it" panel with A in the cart shows B on top.
3. **Smart bump:** with mode `smart`, no targets, and offer product B, adding A to the cart shows the bump, while adding an unrelated product does not.
4. **Hybrid:** the rule target still triggers, and the smart match also triggers.
5. **AI bump product:**
   - With source `ai` and A in the cart, the bump offers B.
   - Accepting adds B at the configured % discount.
   - Removing B restores the offer.
   - With a fixed-price bump, B is shown at regular price.
6. **Funnel with smart / AI product:** the order with A redirects to an offer for B, accepting charges B, and the stored funnel settings still hold the admin product.
7. **AI:**
   - With no connector, the page shows "Not configured" with a link.
   - With a connector configured, Test connection succeeds, and training sets `ai_status = done` with AI scores and reasons visible.
   - With a bad key, the error is shown and the model is still published.
8. Delete model, uninstall and the cron schedule changes all work.
