# WPS-7889 — Gamified Order Bump Formats (Spin Wheel & Scratch Card)

The standard order bump is a static box with a checkbox. This feature adds two **interactive formats** that an admin can choose per bump:

- **Spin wheel:** the customer spins a prize wheel to unlock their discount on the bump product.
- **Scratch card:** the customer scratches a card (mouse, touch or keyboard) to reveal their discount.

After the reveal, the customer sees the normal bump template showing the **won price**. They accept it with the usual checkbox or button, so the existing add-to-cart, variation popup, quantity and removal flows are reused unchanged.

Everything is configured in a new admin tab: **Upsell Funnel Builder → Gamified Offers**.

---

## 1. Goals

| Goal | How |
|---|---|
| More engagement and conversion at checkout | A one-click mini-game with a guaranteed prize, then the familiar accept flow |
| No cheating | The prize is drawn **on the server** (`wp_rand`, weighted), stored in the WooCommerce session, and applied to the price on the server. The browser only animates the result it is given. |
| No re-rolls | One draw per bump per session. Refreshing, re-spinning or a checkout refresh shows the same prize. |
| Never worse than the normal offer | Optional guarantee that the customer gets at least the bump's configured % discount |
| Safe for the store | Admin cap on the maximum discount, and the prize never changes the saved bump settings |
| Accessible | Real `<button>`s, keyboard "Reveal" for the scratch card, `aria-live` result text, `prefers-reduced-motion` respected, and an optional "Skip, show me the offer" link |
| Works with existing features | Classic checkout, block checkout, cart, the shortcode and checkout refreshes (`updated_checkout`), plus the heatmaps and Smart Recommendations features |

Non-goals: prizes other than a percentage discount on the bump product (free shipping, coupons and so on are possible later through the filter in §8), and gamified upsell funnel pages.

---

## 2. File map

| File | Responsibility |
|---|---|
| `includes/class-upsell-order-bump-gamified.php` | Settings, prize table, weighted draw, session state, price override on read and guard on write, statistics |
| `public/class-upsell-order-bump-gamified-public.php` | Rendering the game in place of the bump, the "You won" ribbon, assets, the AJAX `play` endpoint, accept tracking, order item meta |
| `public/js/wps-ubo-gamified.js` | Wheel (SVG) and scratch-card (canvas) widgets, AJAX, swapping in the revealed offer. Also used by the admin preview. |
| `public/css/wps-ubo-gamified.css` | Front-end styles (themeable through CSS variables) |
| `admin/class-upsell-order-bump-gamified-admin.php` | "Gamified Offers" tab link and content, saving settings, admin assets |
| `admin/partials/templates/wps-ubo-gamified-display.php` | Tab markup: settings, per-bump format, prize table, texts and colours, live preview, statistics |
| `admin/js/wps-ubo-gamified-admin.js`, `admin/css/wps-ubo-gamified-admin.css` | Prize table editor (add/remove rows, live odds), live preview |
| **Core edits** | 3 hook points in `wps_ubo_analyse_and_display_order_bump()`, loader wiring, uninstall cleanup |

---

## 3. Settings (option `wps_ubo_gamified_settings`)

| Key | Default | Meaning |
|---|---|---|
| `enabled` | `off` | Master switch. When off, every bump renders as normal. |
| `formats` | `{}` | Map of bump id to `standard`, `spin_wheel` or `scratch_card`. |
| `prizes` | 6 rows | `[{label, discount, weight, color}]`. `discount` is a % from 0 to 100, where 0 means "standard offer". `weight` is the relative odds. |
| `guarantee_offer_discount` | `on` | If the bump already has an X% discount, the customer gets max(X, prize). |
| `max_discount` | `50` | Hard cap on any prize. Rows above it are clamped on save and again at draw time. |
| `allow_skip` | `on` | Show a "No thanks, just show me the offer" link, which reveals the standard offer (counted as a skip). |
| `headline_wheel` | "Spin the wheel to unlock your exclusive discount!" | Shown above the wheel |
| `headline_scratch` | "Scratch the card to reveal your exclusive discount!" | Shown above the card |
| `button_wheel` | "Spin now" | Wheel button text |
| `button_scratch` | "Reveal" | Keyboard / one-click alternative to scratching |
| `win_message` | "🎉 You unlocked {discount} off {product}!" | Placeholders: `{discount}`, `{label}`, `{product}` |
| `color_primary` / `color_accent` / `color_text` | `#2271b1` / `#ffb900` / `#1d2327` | Game colours (CSS variables) |

Statistics are kept in a separate option, `wps_ubo_gamified_stats`: `{ bump_id: { plays, skips, accepts, prizes: { index: count } } }`.

---

## 4. Runtime flow

```
Checkout renders bump N (format = spin_wheel)
  └─ filter wps_ubo_bump_offer_output_handled → Gamified_Public::render_game()
       ├─ prize already drawn for N in this session? → return false (render normal bump, see "Reveal" below)
       └─ print game: headline, teaser "Win up to 50% off {product}", wheel/card, Play button, Skip link → return true

Customer clicks Spin / starts scratching
  └─ POST admin-ajax.php?action=wps_ubo_game_play {nonce, bump_id}
       ├─ checks: feature on, nonce, bump in session "encountered_bump_array", bump live, format is a game
       ├─ draw = session prize ?? weighted draw (wp_rand) → clamp to max_discount → store in session
       ├─ stats.plays++ (only for a new draw)
       └─ response: { index, label, discount, message, html }
            html = output of wps_ubo_analyse_and_display_order_bump(N) rendered with the prize applied

JS: wheel spins to the slice at `index` (or the card is scratched) → shows the message → replaces the game
    with `html` (the standard template + "You won" ribbon). The existing delegated handlers
    (.add_offer_in_cart) take over from here.

Reveal (any later render, e.g. after update_checkout)
  └─ actions wps_ubo_before_bump_offer_html / wps_ubo_after_bump_offer_html wrap the normal bump in
     <div class="wps-ubo-game-won"> with the ribbon.
```

### 4.1 How the prize is applied (server side)
- `option_wps_ubo_bump_list` (read filter) applies to front-end requests that have a WooCommerce session. For each bump with a won prize where `discount > 0`:
  - `wps_upsell_offer_price_type` is set to `%`;
  - `wps_upsell_bump_offer_discount_price` is set to the effective discount, which is max(configured %, prize) when the guarantee is on and the bump uses `%`, otherwise the prize;
  - the original values are kept under the marker `_wps_ubo_game_orig`.
- `pre_update_option_wps_ubo_bump_list` restores the original values and removes the marker before any save (for example when sales statistics are written), so **prizes never reach the saved bump settings**.
- Because `add_offer_in_cart` (the accept AJAX handler) reads the discount from the bump settings on the server, the cart line gets the won price. The price posted by the browser is never trusted.
- The prize is stored on the order line item as hidden meta (`_wps_ubo_game_prize`, for example "Spin wheel: 20% OFF (20%)"), and the session prize is cleared when the order is created.

### 4.2 Weighted draw
```
total = Σ weight_i                (rows with weight > 0)
r     = wp_rand(1, total)
pick the first row whose cumulative weight ≥ r
```
The odds are shown live in the admin prize table: weight_i / total.

---

## 5. Front end

### 5.1 Spin wheel
- SVG wheel drawn from `data-segments` (labels and colours only; **weights are never sent to the browser**), with a fixed pointer at the top.
- Spin: rotate to `360·k + (360 − centre angle of the winning slice)`, plus a small random offset inside the slice, using a CSS `transition` with ease-out over 4.2 s. With `prefers-reduced-motion`, it takes 0.01 s.
- While spinning, the button is disabled. The result is announced through `aria-live`.

### 5.2 Scratch card
- A `<canvas>` overlay painted with a silver gradient and a "Scratch here" hint covers the prize area.
- The first pointer-down draws the prize (AJAX) and writes its label under the overlay. Pointer, mouse and touch moves erase with `destination-out`.
- When more than 45% of the area is cleared (sampled every 4th pixel), it auto-reveals. The **Reveal** button does the same for keyboard and screen-reader users.

### 5.3 Swap to the real offer
After the animation, `.wps-ubo-game[data-bump-id=N]` is replaced with the returned HTML: the "You won" ribbon plus the template's own bump HTML, `<style>` and variation popup. `updated_checkout` and `wps_ubo_game_revealed` events re-initialise any games that are still unplayed.

### 5.4 Block checkout
On block checkout, `wps_ubo_lite_public_script.js` moves the bumps into the checkout block by copying `$('.wrapup_order_bump').html()` as a string and removing the original. A string copy keeps attributes but loses event listeners and canvas pixels. For that reason:
- a game's "initialised" state is a **DOM property** (`el._wpsGame`), never an attribute;
- a `MutationObserver` sets up any `.wps-ubo-game` without that property, so the copied game starts fresh;
- a scratch card painted while hidden (0×0) repaints through a `ResizeObserver` once it becomes visible.

---

## 6. Admin — "Gamified Offers" tab

URL: `admin.php?page=upsell-order-bump-offer-for-woocommerce-setting&tab=gamified-offers`. The link is added through the `wps_ubo_setting_tab` action, and the content through `wps_ubo_lite_setting_tab_html`.

1. **General:** enable, maximum discount cap, guarantee, allow skip.
2. **Bump formats:** every saved bump with a format select (Standard / Spin wheel / Scratch card) and its configured offer discount.
3. **Prizes:** an editable table (label, % off, weight, colour, live odds) with add and remove rows. It needs at least 2 rows and has a maximum of 12.
4. **Texts & colours.**
5. **Live preview:** a working wheel and scratch card that use the same front-end JS in demo mode, drawing locally with no AJAX.
6. **Statistics:** per bump, the plays, skips, accepts after play, the conversion (accepts ÷ plays) and how often each prize was drawn, plus a Reset statistics button.

Saving uses a normal POST with the nonce `wps_ubo_gamified_save` and the `manage_woocommerce` capability, and all values are sanitised and clamped (§3).

---

## 7. Security
- **Play endpoint:** `wp_ajax(_nopriv)_wps_ubo_game_play` checks the nonce `wps_ubo_game` and requires the bump to be currently shown in the session (`encountered_bump_array`) and set to a game format. Requests that fail any check are rejected.
- **Server-side prices:** all prizes and prices are decided on the server and stored in the WooCommerce session. The browser never sends a discount that is trusted.
- **No re-rolls:** a session keeps its first draw. The maximum discount cap limits the worst case if someone repeatedly starts new sessions.
- **Output:** every admin string is escaped on output, and colours pass through `sanitize_hex_color`.

## 8. Hooks

| Hook | Type | Purpose |
|---|---|---|
| `wps_ubo_bump_offer_output_handled( $handled, $bump_id, $target_key, $key )` | filter (core) | Return true when you printed a replacement for the standard bump |
| `wps_ubo_before_bump_offer_html( $bump_id, $bump )` / `wps_ubo_after_bump_offer_html` | action (core) | Wrap the standard bump output |
| `wps_ubo_game_prizes( $prizes, $bump_id )` | filter | Change the prize table per bump |
| `wps_ubo_game_draw( $prize, $bump_id )` | filter | Override or adjust the drawn prize |
| `wps_ubo_game_effective_discount( $discount, $prize, $bump )` | filter | Change the discount that is applied |

## 9. Lifecycle
- Settings are created with defaults on first save, with nothing to install.
- Uninstall (when "delete all data" is on) deletes `wps_ubo_gamified_settings` and `wps_ubo_gamified_stats`.

## 10. QA checklist
1. With the feature off, or a bump set to Standard, the checkout is identical to before.
2. **Spin wheel bump:**
   - The wheel shows. Spinning lands on the slice matching the message.
   - The bump appears with the won % and the right price.
   - Accepting adds the product at that price, and the order item has `_wps_ubo_game_prize`.
3. **Re-rolls:**
   - Reloading the checkout shows the same prize with no wheel.
   - Calling the endpoint again returns the same prize.
   - A new session can draw a new prize.
4. **Scratch card:**
   - Scratching reveals the prize, and so does the Reveal button (also by keyboard).
   - Touch works on mobile.
5. **Guarantee:** a bump with 30% configured and a 10% prize is charged 30%.
6. **Cap:** a 90% prize row is clamped to the cap on save.
7. **Skip:** the standard offer appears and the skip is counted.
8. **Saved settings:** the bump settings saved in the database never change, even after an accept triggers a statistics save.
9. **Reduced motion:** the wheel stops instantly.
10. **Block checkout and cart:** the game renders and the swap works.
11. **Admin:**
    - The tab shows, and settings save and are clamped.
    - The prize editor's odds sum to 100%.
    - The preview spins and scratches.
    - The statistics update, and Reset clears them.
