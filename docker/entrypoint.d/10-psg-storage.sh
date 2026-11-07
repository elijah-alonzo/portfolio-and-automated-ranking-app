#!/bin/sh
# Runs on container start, before the image's Laravel automations
# (50-laravel-automations: migrate, storage:link, caches).
# A new storage volume is empty: create Laravel's folders, and the SQLite
# file when the app uses SQLite.

if [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY is not set. Generate one: docker run --rm <image> php artisan key:generate --show" >&2
    exit 1
fi

cd /var/www/html || exit 1

mkdir -p \
    storage/app/private \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ] && [ -n "${DB_DATABASE:-}" ] && [ "$DB_DATABASE" != ":memory:" ]; then
    mkdir -p "$(dirname "$DB_DATABASE")"
    [ -f "$DB_DATABASE" ] || touch "$DB_DATABASE"
fi
