FROM php:8.4-fpm-alpine

ARG APP_ENV=production

RUN docker-php-ext-install \
    mbstring \
    tokenizer \
    xml \
    bcmath \
    curl \
    pdo_pgsql \
    pdo_mysql \
    pdo_sqlite \
    fileinfo

RUN pecl install redis && docker-php-ext-enable redis

RUN apk add --no-cache nodejs npm

COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini

WORKDIR /var/www/html

COPY composer.json composer.lock package.json package-lock.json ./

RUN composer install --no-interaction --prefer-dist --no-progress

RUN npm ci && npm run build

COPY . .

RUN if [ "$APP_ENV" = "production" ]; then \
        composer install --no-dev --no-interaction; \
    fi

RUN chown -R www-data:www-data storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
