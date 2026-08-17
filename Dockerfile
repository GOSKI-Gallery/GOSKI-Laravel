FROM composer:latest AS composer
FROM php:8.4-fpm-alpine

ARG APP_ENV=production
ENV APP_ENV=${APP_ENV} \
    APP_NAME="${APP_NAME:-Laravel}" \
    APP_DEBUG=false \
    APP_URL="${APP_URL:-http://localhost}" \
    DB_CONNECTION=pgsql \
    DB_HOST=127.0.0.1 \
    DB_PORT=5432 \
    DB_DATABASE=laravel \
    DB_USERNAME=laravel \
    DB_PASSWORD=secret \
    DB_SSLMODE=require \
    VITE_DEV_SERVER_URL=http://localhost:5173

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

RUN apk add --no-cache --virtual .build-deps autoconf g++ make linux-headers \
    && pecl install redis xdebug \
    && docker-php-ext-enable xdebug \
    && echo "extension=redis.so" > /usr/local/etc/php/conf.d/redis.ini \
    && apk del .build-deps

RUN apk add --no-cache nodejs npm

COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini

WORKDIR /var/www/html

COPY .env.example .env

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

COPY composer.json composer.lock package.json package-lock.json ./

RUN composer install --no-interaction --prefer-dist --no-progress --no-scripts

RUN npm ci

COPY . .

RUN npm run build \
    && mkdir -p /opt/goski-build \
    && cp -a public/build/. /opt/goski-build/

RUN if [ "$APP_ENV" = "production" ]; then \
        composer install --no-dev --no-interaction; \
    fi

RUN chown -R www-data:www-data storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
