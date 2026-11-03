#!/usr/bin/env bash
# Install a throw-away WordPress + WooCommerce for CI (or a local dry run).
#
#   bin/ci-install-wp.sh core   WordPress core + WooCommerce files only (enough for PHPUnit).
#   bin/ci-install-wp.sh site   A working store for the Playwright suite: installs WordPress,
#                               activates WooCommerce and this plugin, sets up the checkout.
#
# Env (defaults in brackets):
#   WP_DIR [/tmp/wordpress]  WP_VERSION [latest]  WC_VERSION [latest-stable]
#   WP_URL [http://localhost:8889]
#   DB_NAME [wordpress]  DB_USER [root]  DB_PASSWORD [root]  DB_HOST [127.0.0.1]
#   WP_CLI [path of `wp` on PATH]  PHP_BIN [php]
set -euo pipefail

MODE="${1:-core}"
PLUGIN_DIR="$(cd "$(dirname "$0")/.." && pwd)"
PLUGIN_SLUG="upsell-order-bump-offer-for-woocommerce"
WP_DIR="${WP_DIR:-/tmp/wordpress}"
WP_VERSION="${WP_VERSION:-latest}"
WC_VERSION="${WC_VERSION:-latest-stable}"
WP_URL="${WP_URL:-http://localhost:8889}"
DB_NAME="${DB_NAME:-wordpress}"
DB_USER="${DB_USER:-root}"
DB_PASSWORD="${DB_PASSWORD:-root}"
DB_HOST="${DB_HOST:-127.0.0.1}"
PHP_BIN="${PHP_BIN:-php}"
WP_CLI="${WP_CLI:-$(command -v wp || echo wp)}"
TMP="$(mktemp -d)"

wp() { "$PHP_BIN" -d memory_limit=512M $WP_CLI --path="$WP_DIR" --allow-root "$@"; }

echo "::group::WordPress ${WP_VERSION} core"
mkdir -p "$WP_DIR"
if [[ ! -f "$WP_DIR/wp-load.php" ]]; then
	if [[ "$WP_VERSION" == "latest" ]]; then
		curl -fsSL https://wordpress.org/latest.tar.gz -o "$TMP/wp.tar.gz"
	else
		curl -fsSL "https://wordpress.org/wordpress-${WP_VERSION}.tar.gz" -o "$TMP/wp.tar.gz"
	fi
	tar -xzf "$TMP/wp.tar.gz" -C "$TMP"
	cp -R "$TMP/wordpress/." "$WP_DIR/"
fi
echo "::endgroup::"

echo "::group::WooCommerce ${WC_VERSION}"
if [[ ! -f "$WP_DIR/wp-content/plugins/woocommerce/woocommerce.php" ]]; then
	if [[ "$WC_VERSION" == "latest-stable" ]]; then
		curl -fsSL https://downloads.wordpress.org/plugin/woocommerce.zip -o "$TMP/wc.zip"
	else
		curl -fsSL "https://downloads.wordpress.org/plugin/woocommerce.${WC_VERSION}.zip" -o "$TMP/wc.zip"
	fi
	unzip -q "$TMP/wc.zip" -d "$WP_DIR/wp-content/plugins/"
fi
# The plugin under test, linked into the install.
ln -sfn "$PLUGIN_DIR" "$WP_DIR/wp-content/plugins/$PLUGIN_SLUG"
echo "::endgroup::"

if [[ "$MODE" != "site" ]]; then
	rm -rf "$TMP"
	exit 0
fi

echo "::group::Install store"
if [[ ! -f "$WP_DIR/wp-config.php" ]]; then
	wp config create --dbname="$DB_NAME" --dbuser="$DB_USER" --dbpass="$DB_PASSWORD" --dbhost="$DB_HOST" --skip-check --force
	wp config set WP_DEBUG true --raw
	wp config set WP_DEBUG_DISPLAY false --raw
	wp config set WP_DEBUG_LOG true --raw
	wp config set WP_ENVIRONMENT_TYPE local
fi
wp db create 2>/dev/null || true
wp core install --url="$WP_URL" --title="UBO E2E" --admin_user=admin --admin_password="$(openssl rand -hex 12)" --admin_email=admin@example.org --skip-email
# Some WP-CLI/PHP combinations derive a sub-directory URL from the install path; pin it.
wp option update home "$WP_URL"
wp option update siteurl "$WP_URL"
wp rewrite structure '/%postname%/' --hard
wp plugin activate woocommerce
wp plugin activate "$PLUGIN_SLUG"

# Store like the dev site: India, INR, block checkout, Cash on delivery.
wp option update woocommerce_default_country 'IN:MH'
wp option update woocommerce_currency 'INR'
wp option update woocommerce_onboarding_profile '{"skipped":true}' --format=json
wp option update woocommerce_cod_settings '{"enabled":"yes","title":"Cash on delivery","description":"Pay with cash upon delivery.","instructions":"","enable_for_methods":[],"enable_for_virtual":"yes"}' --format=json
wp option update woocommerce_coming_soon 'no'
wp wc tool run install_pages --user=admin >/dev/null 2>&1 || true
CHECKOUT_ID="$(wp option get woocommerce_checkout_page_id)"
if ! wp post get "$CHECKOUT_ID" --field=post_content | grep -q 'wp:woocommerce/checkout'; then
	echo "Checkout page is not the checkout block" >&2
	exit 1
fi
wp rewrite flush --hard
echo "::endgroup::"
rm -rf "$TMP"
