#!/bin/sh
# Runs after the image's Laravel automations have migrated the database.
# DatabaseSeeder only adds what is missing (PSG's departments, councils,
# positions and the first admin), so running it on every start is safe.

cd /var/www/html || exit 1

php artisan db:seed --force --no-interaction
