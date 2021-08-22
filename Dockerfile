FROM composer as builder

# Dockerfile to build image for running unit tests
# Uses two stage process (build, then a fresh image w/source, copying from the build assets)

WORKDIR /app

COPY composer.json ./
COPY composer.lock ./
RUN composer install

FROM php:7.3-cli

RUN pecl install xdebug && docker-php-ext-enable xdebug

WORKDIR /app
COPY . /app

COPY --from=builder /app/vendor /app/vendor
