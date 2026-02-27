FROM ghcr.io/eventpoints/php:main AS php

ENV APP_ENV="prod" \
    APP_DEBUG=0 \
    PHP_OPCACHE_PRELOAD="/app/config/preload.php" \
    PHP_EXPOSE_PHP="off" \
    PHP_OPCACHE_VALIDATE_TIMESTAMPS=0

RUN rm -f /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN mkdir -p var/cache var/log

# Intentionally split into multiple steps to leverage docker layer caching
COPY composer.json composer.lock symfony.lock ./

RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts

FROM node:22-alpine AS js-builder

WORKDIR /app

# Vendor is needed because assets reference files under ../vendor/ (e.g. kerrialnewham/autocomplete)
COPY --from=php /app/vendor ./vendor

COPY package.json package-lock.json webpack.config.js tailwind.config.js postcss.config.mjs ./

RUN npm install

# Tailwind JIT scans templates, so copy them before building
COPY ./assets ./assets
COPY ./templates ./templates

# Debug: List files to verify templates are copied
RUN ls -la templates/ || echo "Templates directory not found"

RUN npm run build

# Debug: Check if CSS was generated
RUN ls -la public/build/ || echo "Build directory not found"

FROM php AS app

WORKDIR /app

COPY . .

# Overlay compiled assets from the node stage
COPY --from=js-builder /app/public/build ./public/build

# Need to run again to trigger scripts with application code present
RUN composer install --no-dev --no-interaction --classmap-authoritative
RUN composer symfony:dump-env prod
RUN chmod -R 777 var

FROM app AS worker

# Install supervisor for managing background processes
RUN apt-get update && apt-get install -y --no-install-recommends \
    supervisor \
    && rm -rf /var/lib/apt/lists/* \
    && mkdir -p /var/log/supervisor

COPY .deployment/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

HEALTHCHECK --interval=30s --timeout=10s --start-period=30s --retries=3 \
    CMD supervisorctl status | grep -E "RUNNING" || exit 1

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

FROM ghcr.io/eventpoints/caddy:main AS caddy

COPY --from=app /app/public public/
