# syntax=docker/dockerfile:1
#
# Production image: PHP-FPM + NGINX (serversideup/php), listening on port 8080.
#
#   docker build -t psg-eportfolio .
#   docker run --rm psg-eportfolio php artisan key:generate --show     # once, for APP_KEY
#   docker run -d -p 8080:8080 \
#       -e APP_KEY=base64:... \
#       -e APP_URL=https://eportfolio.example.com \
#       -e ADMIN_EMAIL=you@example.com -e ADMIN_PASSWORD=a-long-password \
#       -v psg-storage:/var/www/html/storage \
#       psg-eportfolio
#
# On every start it prepares the storage volume, runs migrations, links public
# storage, caches config, routes and views, then seeds what is missing: PSG's
# departments, councils, positions and the first admin (docker/entrypoint.d).
# Data: SQLite in the storage volume by default; set DB_CONNECTION (mysql,
# pgsql) and the DB_* variables to use a database server instead.
# Sample people and evaluations, on an empty database only:
#   docker exec <container> php artisan db:seed --class=DemoDataSeeder --force

ARG PHP_IMAGE=serversideup/php:8.4-fpm-nginx-v4.5.3
ARG NODE_IMAGE=node:24.22.0-bookworm-slim

# ---- PHP with the extensions the app needs on top of the image's defaults ----
FROM ${PHP_IMAGE} AS base
USER root
RUN install-php-extensions intl gd
USER www-data
WORKDIR /var/www/html

# ---- Composer packages, without dev tools ----
FROM base AS vendor
COPY --chown=www-data:www-data composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-scripts --no-autoloader

# ---- Front-end build ----
# Vite's Wayfinder plugin runs `php artisan wayfinder:generate`, so this
# stage has PHP and Node. Only `public/build` leaves it.
FROM ${NODE_IMAGE} AS node
FROM base AS assets
USER root
COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm
COPY package.json package-lock.json ./
RUN npm ci
COPY --from=vendor /var/www/html/vendor ./vendor
COPY . .
# Booting artisan here needs no real database or cache.
RUN DB_CONNECTION=sqlite DB_DATABASE=:memory: CACHE_STORE=array \
    sh -c 'composer dump-autoload --no-dev && npm run build'

# ---- The app ----
FROM base AS app
# Notifications and reminder emails are sent right away (no queue worker to run).
ENV AUTORUN_ENABLED=true \
    PHP_OPCACHE_ENABLE=1 \
    APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    QUEUE_CONNECTION=sync \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/var/www/html/storage/database/database.sqlite
COPY --chown=www-data:www-data --from=vendor /var/www/html/vendor ./vendor
COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=assets /var/www/html/public/build ./public/build
COPY --chmod=755 docker/entrypoint.d/ /etc/entrypoint.d/
# Also runs package discovery and publishes Filament's assets into public/.
RUN DB_DATABASE=:memory: CACHE_STORE=array \
    composer dump-autoload --no-dev --optimize --classmap-authoritative
