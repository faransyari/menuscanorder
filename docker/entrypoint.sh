#!/usr/bin/env bash
#
# Container entrypoint.
#   1. Renders an .env file from the platform-provided environment variables.
#   2. Points Apache at the port the platform expects ($PORT, default 8080).
#   3. Runs database migrations (unless disabled).
#   4. Hands off to Apache in the foreground.
#
set -euo pipefail

APP_ROOT=/var/www/html
PORT="${PORT:-8080}"

# --------------------------------------------------------------------
# 1. Build .env from environment variables (nothing secret is baked in
#    the image; values are injected at runtime by the host).
# --------------------------------------------------------------------
ENV_FILE="${APP_ROOT}/.env"
{
    echo "CI_ENVIRONMENT = ${CI_ENVIRONMENT:-production}"
    [ -n "${APP_BASE_URL:-}" ]   && echo "app.baseURL = '${APP_BASE_URL}'"
    echo "app.forceGlobalSecureRequests = ${APP_FORCE_HTTPS:-true}"
    [ -n "${ENCRYPTION_KEY:-}" ] && echo "encryption.key = ${ENCRYPTION_KEY}"

    if [ -n "${DB_HOSTNAME:-}" ]; then
        echo "database.default.hostname = ${DB_HOSTNAME}"
        echo "database.default.database = ${DB_DATABASE:-}"
        echo "database.default.username = ${DB_USERNAME:-}"
        echo "database.default.password = ${DB_PASSWORD:-}"
        echo "database.default.DBDriver = ${DB_DRIVER:-MySQLi}"
        echo "database.default.port = ${DB_PORT:-3306}"
        echo "database.default.DBDebug = ${DB_DEBUG:-false}"
    fi
} > "${ENV_FILE}"

# --------------------------------------------------------------------
# 2. Configure Apache to listen on $PORT
# --------------------------------------------------------------------
sed -ri "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/\*:[0-9]+/*:${PORT}/" /etc/apache2/sites-available/000-default.conf

# --------------------------------------------------------------------
# 3. Database migrations
# --------------------------------------------------------------------
if [ "${RUN_MIGRATIONS:-true}" = "true" ] && [ -n "${DB_HOSTNAME:-}" ]; then
    echo "==> Running database migrations..."
    php "${APP_ROOT}/spark" migrate --all || echo "WARNING: migrations failed (continuing startup)."
fi

# --------------------------------------------------------------------
# 4. Start Apache
# --------------------------------------------------------------------
echo "==> Starting Apache on port ${PORT}"
exec apache2-foreground
