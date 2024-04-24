#syntax=docker/dockerfile:1.4
FROM php:8.3.6-fpm-alpine3.19

RUN set -eux; \
    apk update; \
    apk del; \
    rm -rf /var/lib/apt/lists/*; \
    mkdir /root/history

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions @composer opcache pdo_pgsql intl; \
    ln -s composer /usr/local/bin/c;

COPY --link php.ini $PHP_INI_DIR/conf.d/
COPY --link fpm.conf /usr/local/etc/php-fpm.d/zzz-docker.conf
RUN set -eux; \
    cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"; \
    mkdir /var/run/php;
