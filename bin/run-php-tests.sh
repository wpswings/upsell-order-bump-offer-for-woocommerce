#!/usr/bin/env bash
# Run the PHPUnit suite with a PHP binary that has mysqli.
# Defaults target the Local "upsell-plugin-nov-feature" site; override with env vars:
#   PHP_BIN=/usr/bin/php WPS_TEST_DB_HOST=127.0.0.1 bin/run-php-tests.sh --testsuite unit
set -euo pipefail
cd "$(dirname "$0")/.."

LOCAL_PHP="$HOME/.config/Local/lightning-services/php-8.2.29+0/bin/linux"
LOCAL_RUN="${LOCAL_RUN:-$HOME/.config/Local/run/yGRKFQTxX}"

if [[ -z "${PHP_BIN:-}" && -x "$LOCAL_PHP/bin/php" ]]; then
	export LD_LIBRARY_PATH="$LOCAL_PHP/shared-libs${LD_LIBRARY_PATH:+:$LD_LIBRARY_PATH}"
	PHP_BIN="$LOCAL_PHP/bin/php"
	PHP_ARGS=(-c "$LOCAL_RUN/conf/php/php.ini")
	# The WP installer runs in a child PHP process, so pass the socket through DB_HOST.
	export WPS_TEST_DB_HOST="${WPS_TEST_DB_HOST:-localhost:$LOCAL_RUN/mysql/mysqld.sock}"
else
	PHP_BIN="${PHP_BIN:-php}"
	PHP_ARGS=()
fi
export WPS_TEST_PHP_BINARY="$PHP_BIN"

exec "$PHP_BIN" "${PHP_ARGS[@]}" -d memory_limit=1G vendor/bin/phpunit "$@"
