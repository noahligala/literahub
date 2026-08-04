FROM php:8.4-fpm-alpine

RUN apk add --no-cache git unzip icu-dev libzip-dev oniguruma-dev \
    && docker-php-ext-install intl mbstring pdo_mysql zip bcmath opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .
RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

CMD ["php-fpm"]
