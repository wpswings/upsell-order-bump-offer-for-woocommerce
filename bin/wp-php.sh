#!/usr/bin/env bash
# Run a PHP script against the site's WordPress (used by the E2E fixtures).
# Defaults to the Local "upsell-plugin-nov-feature" PHP; set PHP_BIN to use another binary.
set -euo pipefail
LOCAL_PHP="$HOME/.config/Local/lightning-services/php-8.2.29+0/bin/linux"
LOCAL_RUN="${LOCAL_RUN:-$HOME/.config/Local/run/yGRKFQTxX}"
if [[ -z "${PHP_BIN:-}" && -x "$LOCAL_PHP/bin/php" ]]; then
	export LD_LIBRARY_PATH="$LOCAL_PHP/shared-libs${LD_LIBRARY_PATH:+:$LD_LIBRARY_PATH}"
	exec "$LOCAL_PHP/bin/php" -c "$LOCAL_RUN/conf/php/php.ini" -d "mysqli.default_socket=$LOCAL_RUN/mysql/mysqld.sock" -d display_errors=stderr -d error_reporting=0 "$@"
fi
exec "${PHP_BIN:-php}" -d display_errors=stderr "$@"
