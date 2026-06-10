FROM composer:latest AS composer
FROM php:8.4-fpm-alpine

ARG APP_ENV=production

RUN apk add --no-cache \
    oniguruma-dev \
    libxml2-dev \
    curl-dev \
    postgresql-dev \
    sqlite-dev \
    libzip-dev \
    icu-dev

RUN docker-php-ext-install \
    mbstring \
    xml \
    bcmath \
    curl \
    pdo_pgsql \
    pdo_sqlite \
    fileinfo \
    zip \
    intl

RUN apk add --no-cache autoconf g++ make \
    && pecl install redis pcov \
    && docker-php-ext-enable redis pcov \
    && apk del autoconf g++ make

RUN apk add --no-cache nodejs npm

COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini

WORKDIR /var/www/html

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

COPY composer.json composer.lock package.json package-lock.json ./

RUN composer install --no-interaction --prefer-dist --no-progress --no-scripts

RUN npm ci

COPY . .

RUN npm run build

RUN if [ "$APP_ENV" = "production" ]; then \
        composer install --no-dev --no-interaction; \
    fi

RUN chown -R www-data:www-data storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
