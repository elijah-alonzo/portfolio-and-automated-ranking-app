#!/bin/sh
set -e

cd /var/www/html

mkdir -p database/data
[ -f database/data/database.sqlite ] || { touch database/data/database.sqlite; FRESH=1; }
chown -R www-data:www-data database/data storage

php artisan storage:link --force >/dev/null 2>&1 || true
php artisan migrate --force
[ -z "$FRESH" ] || php artisan db:seed --force
php artisan filament:optimize >/dev/null 2>&1 || true

exec "$@"
