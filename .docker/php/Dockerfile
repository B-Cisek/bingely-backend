FROM php:8.4.13-fpm-alpine AS os

WORKDIR /app

# Create bingely user
RUN addgroup -g 1000 bingely && adduser -D -u 1000 -G bingely bingely

# ${PHPIZE_DEPS} = autoconf  dpkg-dev dpkg   file   g++   gcc   libc-dev   make   pkgconf   re2c
RUN apk add --virtual .build-deps ${PHPIZE_DEPS}  \
    && apk add --no-cache \
    icu-dev libzip-dev \
    bash \
    nano \
    curl \
    icu-libs \
    libzip \
    rabbitmq-c-dev \
    && apk add --no-cache postgresql-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_pgsql pgsql intl zip opcache \
    && pecl install amqp \
    && docker-php-ext-enable pdo_pgsql pgsql zip opcache amqp \
    && docker-php-source delete \
    && apk del .build-deps

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

FROM os AS php-local
RUN apk add --update --no-cache --virtual .build-deps $PHPIZE_DEPS linux-headers
RUN pecl install xdebug-3.4.4 \
    && docker-php-ext-enable xdebug \
    && pecl clear-cache
RUN apk del .build-deps $PHPIZE_DEPS
COPY php-dev.ini /usr/local/etc/php/conf.d/php.ini
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
ENV PHP_IDE_CONFIG="serverName=dev"
USER bingely

FROM os AS php-prod
COPY php.ini /usr/local/etc/php/conf.d/php.ini
USER bingely
