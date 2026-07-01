#!/bin/bash
set -e

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
