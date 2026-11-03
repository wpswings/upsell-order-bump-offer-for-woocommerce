# WPS-7886 — Automated test cases (unit, integration, E2E)

This suite covers the plugin's main features: the core order bump, the admin settings and list, and the four 3.1.8 features (Offer Heatmaps, Smart Recommendations, Spin & Scratch bumps and Cart Recovery).

| Layer | Tool | Where | Count |
|---|---|---|---|
| Unit | PHPUnit 9 + WordPress test suite + WooCommerce | `tests/php/unit/` | 116 tests |
| Integration | same | `tests/php/integration/` | 72 tests |
| End-to-end | Playwright (system Chrome) | `tests/e2e/specs/` | 33 tests + 1 known-bug test |

The PHPUnit run reports **202** tests. Data providers expand the 188 test methods.

Nothing here ships with the plugin. All tooling is dev-only: `composer.json` (require-dev), `package.json` (devDependencies), `phpunit.xml.dist`, `playwright.config.js`, `bin/` and `tests/`.

---

## 1. Running the tests

### PHP (unit + integration)

```bash
composer install
bin/run-php-tests.sh                        # everything
bin/run-php-tests.sh --testsuite unit
bin/run-php-tests.sh --testsuite integration
bin/run-php-tests.sh --filter AcrFlowTest   # one class
```

- **Separate database.** The tests run against their own database, `wps_ubo_tests`. It is **dropped and re-created on every run**, so never point it at real data.
- **Same code as the site.** WordPress and WooCommerce are loaded from the site this plugin lives in. That means the tests use the same WP and WC versions as the site.
- **Local defaults.** `bin/run-php-tests.sh` uses Local's PHP 8.2 and MySQL socket. On another machine or in CI, override these:

| Env var | Default |
|---|---|
| `PHP_BIN` | Local's PHP 8.2 (falls back to `php`) |
| `WPS_TEST_DB_NAME` / `_USER` / `_PASSWORD` / `_HOST` | `wps_ubo_tests` / `root` / `root` / `localhost:<Local socket>` |
| `WPS_TEST_ABSPATH` | the WordPress root five levels above `tests/php/` |
| `WPS_TEST_WC_DIR` | `../woocommerce` |

Create the database once: `CREATE DATABASE wps_ubo_tests;`

### E2E (browser)

```bash
npm install
npm run test:e2e             # headless
npm run test:e2e:headed      # watch it
npm run test:e2e:report      # HTML report of the last run
```

- **Needs a running site.** The suite runs against the Local site (`http://upsell-plugin-nov-feature.local`).
- **Before the run**, `tests/e2e/global-setup.js` calls `tests/e2e/fixtures/wp-fixtures.php setup`. That step:
  1. backs up every plugin option the suite touches: bump list, global options, Spin & Scratch, heatmap, cart recovery and recommendation settings, dynamic rules, upsell funnels, and WooCommerce "coming soon";
  2. creates three virtual products (`E2E Camera`, `E2E Lens Cap`, `E2E Beanie`), one bump and a temporary administrator;
  3. turns coming-soon mode off.
- **After the run**, `global-teardown.js` restores the backup. It also deletes the products, the temporary admin, and every order, saved cart and coupon with an `@e2e.test` email.
  - This was checked: a hash of all those options is identical before and after a full run.
- **Interrupted runs** are recovered: the next run tears down first, then seeds again.

| Env var | Purpose |
|---|---|
| `WPS_E2E_BASE_URL` | Site URL (default: read from WordPress) |
| `WPS_E2E_CHANNEL` | Browser channel, default `chrome`; set to empty to use Playwright's Chromium (`npx playwright install chromium`) |
| `PHP_BIN` | PHP for the fixture script (default: Local's PHP, see `bin/wp-php.sh`) |
| `WPS_E2E_KEEP=1` | Skip teardown (debugging). Run `bin/wp-php.sh tests/e2e/fixtures/wp-fixtures.php teardown` afterwards. |

Specs run one at a time (`workers: 1`) because they share the store's settings.

### On GitHub (every pull request)

`.github/workflows/tests.yml` runs on every pull request to `master` or `release/**`. It can also be started by hand from the Actions tab (workflow_dispatch). It has two jobs that run in parallel:

| Job | What it does |
|---|---|
| **PHPUnit (unit + integration)** | 1. MySQL 8 service, PHP 8.2.<br>2. `composer install`.<br>3. `bin/ci-install-wp.sh core` downloads WordPress `7.1.3` and WooCommerce `11.2.1`.<br>4. `bin/run-php-tests.sh`. |
| **Playwright (E2E)** | 1. MySQL 8 service, PHP 8.2 + WP-CLI, Node 22.<br>2. `npm ci`, then Playwright's Chromium.<br>3. `bin/ci-install-wp.sh site` installs a store like the dev site (India/INR, block checkout, Cash on delivery) with this plugin active.<br>4. Serves the store with `php -S localhost:8889`.<br>5. `npx playwright test`.<br>On failure, the HTML report, traces, screenshots, server log and `debug.log` are uploaded as the `e2e-report` artifact. |

- **Versions** are set by the `WP_VERSION`, `WC_VERSION` and `PHP_VERSION` env values at the top of the workflow. WooCommerce is pinned because `woocommerce.zip` ("latest") was serving a beta.
- **Merge rule:** to block merging on failing tests, mark both jobs as required checks in the branch protection rules for `release/*`.
- **Checked locally:** both jobs were dry-run on a fresh WordPress downloaded by `bin/ci-install-wp.sh`. PHPUnit gave 202 tests passing; Playwright gave 33 passing.

---

## 2. What is covered

### Core order bump

| Test | What it proves |
|---|---|
| `unit/LegacyFunctionsTest` | **Defaults and allowed HTML:** default global options, default text and template CSS, and the allowed-HTML list (keeps bump markup, strips `<script>`). |
| | **Location hooks:** the hook and priority for each of the 4 bump locations, plus WooCommerce Germanized. |
| | **Bump price:** `%`, fixed, no-discount, more than 100 %, negative values, invalid product, and the currency-conversion filter. |
| | **Bump details:** `wps_ubo_lite_fetch_bump_offer_details` for percent and fixed bumps, and for a missing bump. |
| | **Cart checks:** in-cart (bump items ignored, variation found by its parent), already-in-cart, category in cart (including child categories), and category parent. |
| | **Role exclusion:** guest and customer roles (`is_valid_user_role`). |
| | **Session:** session validations (status, out of stock, skip-if-in-cart) and session cleanup. |
| | **Dynamic discount rules:** comparison operators, device detection, rule sanitising, subtotal and user-status rules, and the all-rules-must-pass check. |
| | **Choosing which bump to show:** by target product, target category, status and weekday schedule. |
| | **Free-tier limit:** only the first bump is used, and Smart Offer Upgrade is forced off. |
| `integration/BumpCartFlowTest` | **AJAX add:** `add_offer_in_cart` uses the discount stored on the server and ignores the one the browser sends; it rejects a bad nonce; it handles fixed price; a variable offer opens the variation popup. |
| | **AJAX remove:** `remove_offer_in_cart`. |
| | **Cart:** removing the target product also removes the offer. |
| | **Stats:** view and accept counters, and the report counters. |
| | **Order:** line-item meta (`is_order_bump_purchase`), the meta is hidden in the order view, and the order is flagged `wps_bump_order`. |
| | **Session and HTML:** emptying the cart clears the bump session; the bump HTML and checkbox render; the output can be replaced through a filter. |
| `e2e/02-order-bump-checkout` | **Block checkout, show/hide:** the bump is hidden without its target product, shown with it, and hidden when set to Sandbox. |
| | **Block checkout, add/remove:** ticking adds the offer at 20 % off (₹50 → ₹40 in the cart), and unticking removes it. |
| | **Placing an order:** the order is placed through the real checkout UI, with bump meta and the discounted line total. |
| | **Cart:** removing the target also removes the bump. |

### Admin

| Test | What it proves |
|---|---|
| `integration/AdminTest` | **Bump list status toggle:** Live/Sandbox, values limited to yes/no, unknown bump, non-admins, and bad nonce. |
| | **Campaign labels:** created; duplicates rejected even with different case or extra spaces; name and colour required; admins only. |
| | **Popup settings:** saving them. |
| | **Import/export helpers:** CSV value conversion, row normalising, export skip-keys, header mapping and the last-index helpers. |
| | **Registration:** report and screen registration; AJAX actions and option filters hooked. |
| | **Spin & Scratch tab:** save (an unticked box saves as off; prizes capped at the maximum), reset stats, and nonce and capability checks. |
| | **Cart Recovery tab:** save (and rescheduling), test email, delete a cart, non-managers refused, the tab renders, and it stays hidden on other tabs. |
| | **Activation:** the feature tables and options are created. |
| `e2e/01-admin` | **Pages load without PHP errors:** every settings tab, plus the Heatmaps and Smart Recommendations pages. |
| | **Navigation:** the feature tabs are linked. |
| | **Through the UI:** the bump status toggle saves; Cart Recovery settings save and keep their values; the email preview renders. |

### Offer Heatmaps (WPS-7887)

| Test | What it proves |
|---|---|
| `unit/HeatmapTest` | **Storage and reports:** default settings; inserting events; the grid buckets clicks into 100-px cells (capped at 99); zone and total counts; filters by device, template, offer and date; purging old rows by retention; the daily purge cron. |
| | **Collecting events (`ingest`):** the template is looked up on the server, never taken from the browser; invalid offer type, event, context or offer is rejected; coordinates are clamped; unknown zone and device are ignored; upsell, custom-page and default templates are recognised. |
| | **Limits and safeguards:** at most 100 events per request; bad nonce, feature off or broken JSON store nothing; a per-IP rate limit; a filter can drop events. |
| | **Tracker loading:** hidden admin bar in preview mode; the tracker is not loaded on other pages, and on a shortcode page it loads with its settings. |
| | **Admin endpoints:** a capability check; data and settings-save responses with clamped values. |
| `e2e/04-heatmap` | **Real browser:** hover and click on the bump on the checkout are sent and stored, and the admin report receives the totals. The tracker is not loaded for shop managers. |

### Smart Recommendations (WPS-7888)

| Test | What it proves |
|---|---|
| `unit/RecoTest` | **Settings and model:** settings limited to allowed ranges and values; model status and `has_model`; scheduling (weekly / manual); order-status filter; training lock. |
| | **Scoring:** scores from several cart products are combined; variations count as their parent product. |
| | **Choosing products:** sorted by score; excluded products, out-of-stock and draft products are skipped; products already in the cart are never offered; products the customer already bought are skipped. |
| | **Live model and AI:** the top product pairs; AI results are read correctly (code fences, values limited to 0–1, reasons cleaned of HTML). |
| | **Offer swap:** the targeting mode and product source; cart products; AI product swap on read, restored before save (a fixed price is not carried over to the swapped product); the same for funnels. |
| | **Product availability and admin:** offer-product availability; admin field values limited to allowed options; save settings (capability check, values clamped). |
| `integration/RecoTrainingTest` | **Training on real orders:** products often bought together are found; pairs under the minimum support are removed; the model version goes up on each run; with no orders the model is empty; deleting the model works. |
| `e2e/01-admin` | The Smart Recommendations page loads. |

### Spin & Scratch bumps (WPS-7889)

| Test | What it proves |
|---|---|
| `unit/GamifiedTest` | **Settings:** defaults; on/off switches; maximum discount clamped; formats; prize sanitising, defaults when invalid, at most 12 prizes; text and colour fall back to defaults. |
| | **The draw:** prize weights are respected (statistically checked); one draw per session; skip and its stats; a filter cannot exceed the maximum discount; clearing the session. |
| | **Discount:** the bump's own discount is guaranteed. |
| | **Bump price swap:** the won price is applied on read and the stored price is restored before save. |
| | **Uninstall:** options removed. |
| `integration/BumpCartFlowTest` | **AJAX play:** refused unless the bump is being shown; one play per session; the won discount is the price in the cart. Game HTML renders and standard bumps are not replaced. |
| `e2e/03-gamified` | **Real browser:** the spin wheel and the scratch card each replace the checkbox; playing reveals a prize (at least the bump's 20 %, at most 50 %); the real bump appears at the won price; a reload keeps the prize. Skip gives the plain offer; with the feature off, the normal checkbox shows. |

### Cart Recovery (WPS-7890)

| Test | What it proves |
|---|---|
| `unit/AcrDbTest` | **Settings:** defaults; numbers clamped; on/off switches and offer source; only existing roles kept; email steps cleaned (subject, body HTML, units, discount, coupon). |
| | **Timing:** step delay in seconds; next enabled step; the 5-minute cron. |
| | **Saved-cart records:** add, read, update and delete; unsubscribed lookup; most recent open cart for an email. |
| | **Admin list and stats:** list filtering and pages; dashboard stats and rates. |
| | **Scheduling and price:** scheduled while on, cleared when off; offer price. |
| | **Email links:** recovery, offer and unsubscribe links and the unsubscribe signature. |
| | **Email content:** preview with sample coupon and offer; HTML entities decoded in the subject; sending goes through `wp_mail`. |
| `integration/AcrFlowTest` | **Capturing carts:** guests (email saved lowercase); invalid email; bad nonce; guests off; feature off; editing the cart updates the same saved cart; the name is not wiped; excluded roles; logged-in customers; the tracking filter; opt-out. |
| | **Processing:** disabled; locked; recent cart left alone. |
| | **Email sequence:** three emails (cart → offer → single-use coupon locked to the customer's email); disabled steps skipped; the coupon is reused and its amount only goes up. |
| | **End states:** ordering without the link counts as recovered (direct); a finished sequence becomes lost. |
| | **Offer choice:** fixed product (skipped when already in the cart), matching bump and its percentage, step discount override, out of stock, filter. |
| | **Recovery links:** restore cart, offer, coupon and name (works even when the mail client upper-cases the token); the offer tick box; expired and missing tokens; the order is linked to the saved cart and the link cannot be reused. |
| | **Unsubscribe, privacy, legacy:** unsubscribe signature checked; unsubscribed emails get no more emails; privacy export and erase; the legacy "Abandoned Cart Bump" option is untouched. |
| `e2e/05-cart-recovery` | **Real browser:** consent notice; capture (and the name typed after the email); abandoned → email 1 → email 2 with the offer. In a new browser, "Add to my order" restores the cart with the offer at ₹17 and the "Welcome back, Riya" box; untick and re-tick; the order placed in the browser is recovered via the link with the offer accepted; the link can't be reused. |
| | **Links and admin:** unknown link → cart page with a notice; opt-out stops tracking; a forged unsubscribe is ignored while the signed one works and stops emails; the admin tab lists carts and stats. |

---

## 3. Issues found while writing the tests

These were **not changed**. Existing behaviour was left alone; fix them in separate tasks.

1. **Block checkout: a tick in the first second after load is lost.**
   - `public/js/wps_ubo_lite_public_script.js` moves the bump into the checkout block by copying its HTML after `setTimeout(…, 1000)`.
   - A click before that copy happens goes to the old element. The box then shows unticked although the offer was added, and ticking again adds the offer **twice**.
   - The same applies to the Spin & Scratch play button.
   - Recorded as `test.fail()` in `02-order-bump-checkout.spec.js` ("known issue…"). It will start failing, and so flag the fix, once this is fixed.
2. **Accepting a bump that was never counted as viewed throws a PHP warning.**
   - `Wps_Upsell_Order_Bump_Report_Sales_By_Bump::remove_unwanted_view_count()` returns `$this->bump_series[$id]['offer_view_count']` without checking it exists.
   - This gives "Undefined array key" when a bump is accepted before any view was counted, for example a freshly saved bump.
3. **`is_valid_user_role()` has an inverted name.** It returns `true` when the user's role is *excluded*. The tests check the current behaviour.
4. **Smart Recommendations caches "active" for the whole PHP process.** `Upsell_Order_Bump_Reco_Public::active()` keeps the value in a `static` variable, so turning the feature on or off in the same request has no effect until the next request.
5. **Lite free-tier list with no bumps.** `wps_upsell_bump_list_callback_return()` returns `['' => ['wps_ubo_offer_replace_target' => 'no']]` instead of an empty array.
6. **Admin requests missing real nonce or capability checks:**
   - delete and clone bump (`del_bump_id`, `clone_bump_id`);
   - the select2 product and category searches (they verify a nonce created in the same request);
   - `wps_refresh_bump_stats` / `wps_refresh_upsell_funnel_stats` have no nonce or capability check (any logged-in user can call them), and the bump stats add `bump_total_sales` to the summary twice (`admin/class-upsell-order-bump-offer-for-woocommerce-admin.php:3868` and `:3872`).
7. **The FBT discount price is never sent.** The frequently-bought-together script reads `getElementById(" wps_fbt_discount_price")` with a leading space, so it always sends `null`. The server ignores that value anyway.

---

## 4. Not covered

- **FBT and recommendation popup:** only the server code is in this plugin. The markup comes from the Pro plugin, which isn't installed.
- **One-click upsell funnel offer pages:** covered only as far as the heatmap template lookup and the Smart Recommendations funnel swap. The E2E fixture empties the funnel list during the run so orders reach the thank-you page; it is restored afterwards.
- **Classic (shortcode) checkout in E2E:** the site uses the block checkout. The classic flow is covered in PHP.
- **AI providers:** tested by parsing their responses, never by calling them.
- **JavaScript is not unit-tested on its own:** the browser specs exercise every feature script (tracker, games, cart capture, bump add/remove).
