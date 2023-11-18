FROM ghcr.io/eventpoints/php:main AS composer

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



# Use an official Node.js image with a specific version
FROM node:14 as js-builder

# Set the working directory in the container
WORKDIR /build

# Copy /vendor from the composer image
COPY --from=composer /app /vendor

# Set up Git credentials if needed (for private repositories)
# RUN echo "machine github.com login YOUR_GITHUB_TOKEN password x-oauth-basic" > ~/.netrc

# Install npm packages
COPY package.json yarn.lock webpack.config.js ./
RUN yarn install

# Production yarn build
COPY ./assets ./assets

# Run the build command
RUN yarn run build



FROM composer as php

COPY --from=js-builder /build .
COPY . .

# Need to run again to trigger scripts with application code present
RUN composer install --no-dev --no-interaction --classmap-authoritative
RUN composer symfony:dump-env prod
RUN chmod -R 777 var



FROM ghcr.io/eventpoints/caddy:main AS caddy

COPY --from=php /app/public public/
