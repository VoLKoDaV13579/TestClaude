#!/bin/bash
set -e

git config --global --add safe.directory /app 2>/dev/null || true

if [ ! -f /app/vendor/autoload.php ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist
fi

if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    if [ -f /app/.env ] && grep -q "^APP_KEY=$" /app/.env; then
        echo "Generating application key..."
        php artisan key:generate --force
    fi
fi

exec "$@"
