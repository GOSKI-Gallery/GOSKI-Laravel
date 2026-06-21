FROM alpine:3.20

RUN apk add --no-cache \
    php83 php83-common php83-fpm \
    php83-intl php83-bcmath php83-gd \
    php83-pdo php83-pdo_pgsql php83-opcache \
    php83-pcntl php83-zip \
    php83-pecl-redis php83-pecl-uuid \
    php83-curl php83-phar \
    php83-tokenizer php83-fileinfo php83-iconv php83-dom php83-exif \
    dcron busybox-suid libcap curl unzip git

RUN ln -sf /usr/bin/php83 /usr/bin/php && \
    ln -sf /usr/bin/php83 /usr/local/bin/php && \
    ln -sf /usr/sbin/php-fpm83 /usr/sbin/php-fpm

RUN sed -i 's/^listen = .*/listen = 127.0.0.1:9000/' /etc/php83/php-fpm.d/www.conf && \
    sed -i 's/^;error_log = log\/php83\/error.log/error_log = \/proc\/self\/fd\/2/' /etc/php83/php-fpm.conf

COPY --from=ochinchina/supervisord:latest /usr/local/bin/supervisord /usr/local/bin/supervisord
COPY --from=caddy:2.2.1 /usr/bin/caddy /usr/local/bin/caddy
RUN setcap 'cap_net_bind_service=+ep' /usr/local/bin/caddy
COPY --from=composer/composer:2 /usr/bin/composer /usr/local/bin/composer

ENV LARAVEL_PATH=/srv/app
WORKDIR $LARAVEL_PATH

RUN addgroup -S app && adduser -S app -G app && addgroup app wheel

COPY composer.json composer.lock ./
RUN chown -R app:app $LARAVEL_PATH && \
    su app -c "composer install --prefer-dist --no-scripts --no-dev --no-autoloader && rm -rf /home/app/.composer"

COPY --chown=app:app . $LARAVEL_PATH/
COPY ./.deploy/config/php/local.ini /etc/php83/conf.d/local.ini
COPY ./.deploy/config/crontab /etc/crontabs/app

RUN chmod 777 /usr/sbin/crond && \
    chown -R app:app /etc/crontabs/app && \
    setcap cap_setgid=ep /usr/sbin/crond

USER app

EXPOSE 80
COPY ./.deploy/entrypoint.sh /
ENTRYPOINT ["sh", "/entrypoint.sh"]
