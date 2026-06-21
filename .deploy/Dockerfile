FROM alpine:3.20

# Install PHP 8.3 and extensions as pre-compiled Alpine packages (zero compilation)
RUN apk add --no-cache \
    php83 \
    php83-common \
    php83-fpm \
    php83-intl \
    php83-bcmath \
    php83-gd \
    php83-pdo \
    php83-pdo_mysql \
    php83-pdo_pgsql \
    php83-opcache \
    php83-exif \
    php83-pcntl \
    php83-zip \
    php83-pecl-redis \
    php83-pecl-uuid \
    php83-curl \
    php83-phar \
    php83-tokenizer \
    php83-fileinfo \
    php83-iconv \
    php83-dom \
    dcron \
    busybox-suid \
    libcap \
    curl \
    unzip \
    git

# Create symlinks so standard commands work
RUN ln -sf /usr/bin/php83 /usr/bin/php && \
    ln -sf /usr/bin/php83 /usr/local/bin/php && \
    ln -sf /usr/sbin/php-fpm83 /usr/sbin/php-fpm

# Configure PHP-FPM to listen on TCP 9000 for Caddy, log to stderr
RUN sed -i 's/^listen = .*/listen = 127.0.0.1:9000/' /etc/php83/php-fpm.d/www.conf && \
    sed -i 's/^;error_log = log\/php83\/error.log/error_log = \/proc\/self\/fd\/2/' /etc/php83/php-fpm.conf

# Install supervisord
COPY --from=ochinchina/supervisord:latest /usr/local/bin/supervisord /usr/local/bin/supervisord

# Install Caddy
COPY --from=caddy:2.2.1 /usr/bin/caddy /usr/local/bin/caddy
RUN setcap 'cap_net_bind_service=+ep' /usr/local/bin/caddy

# Install composer
COPY --from=composer/composer:2 /usr/bin/composer /usr/local/bin/composer

# Set working directory
ENV LARAVEL_PATH=/srv/app
WORKDIR $LARAVEL_PATH

# Add non-root user
ARG NON_ROOT_GROUP=app
ARG NON_ROOT_USER=app
RUN addgroup -S $NON_ROOT_GROUP && adduser -S $NON_ROOT_USER -G $NON_ROOT_GROUP
RUN addgroup $NON_ROOT_USER wheel

# Set cron job
COPY ./.deploy/config/crontab /etc/crontabs/$NON_ROOT_USER
RUN chmod 777 /usr/sbin/crond
RUN chown -R $NON_ROOT_USER:$NON_ROOT_GROUP /etc/crontabs/$NON_ROOT_USER && setcap cap_setgid=ep /usr/sbin/crond

# Switch to non-root user & install app dependencies
COPY composer.json composer.lock ./
RUN chown -R $NON_ROOT_USER:$NON_ROOT_GROUP $LARAVEL_PATH
USER $NON_ROOT_USER
RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader
RUN rm -rf /home/$NON_ROOT_USER/.composer

# Copy app
COPY --chown=$NON_ROOT_USER:$NON_ROOT_GROUP . $LARAVEL_PATH/
COPY ./.deploy/config/php/local.ini /etc/php83/conf.d/local.ini

# Runtime ENVs — set via --build-arg or overridden at runtime with -e
ARG APP_KEY
ENV APP_KEY=${APP_KEY}
ARG APP_NAME
ENV APP_NAME=${APP_NAME:-Laravel}
ARG APP_URL
ENV APP_URL=${APP_URL}
ARG APP_ENV
ENV APP_ENV=${APP_ENV:-production}
ARG APP_DEBUG
ENV APP_DEBUG=${APP_DEBUG:-false}

ARG LOG_CHANNEL
ENV LOG_CHANNEL=${LOG_CHANNEL:-stack}

ARG DB_CONNECTION
ENV DB_CONNECTION=${DB_CONNECTION:-pgsql}
ARG DB_HOST
ENV DB_HOST=${DB_HOST}
ARG DB_PORT
ENV DB_PORT=${DB_PORT:-5432}
ARG DB_DATABASE
ENV DB_DATABASE=${DB_DATABASE}
ARG DB_USERNAME
ENV DB_USERNAME=${DB_USERNAME}
ARG DB_PASSWORD
ENV DB_PASSWORD=${DB_PASSWORD}
ARG DB_SSLMODE
ENV DB_SSLMODE=${DB_SSLMODE:-require}

ARG BROADCAST_DRIVER
ENV BROADCAST_DRIVER=${BROADCAST_DRIVER:-log}
ARG CACHE_DRIVER
ENV CACHE_DRIVER=${CACHE_DRIVER:-database}
ARG QUEUE_CONNECTION
ENV QUEUE_CONNECTION=${QUEUE_CONNECTION:-database}
ARG SESSION_DRIVER
ENV SESSION_DRIVER=${SESSION_DRIVER:-database}
ARG SESSION_LIFETIME
ENV SESSION_LIFETIME=${SESSION_LIFETIME:-120}

ARG REDIS_HOST
ENV REDIS_HOST=${REDIS_HOST:-127.0.0.1}
ARG REDIS_PASSWORD
ENV REDIS_PASSWORD=${REDIS_PASSWORD}
ARG REDIS_PORT
ENV REDIS_PORT=${REDIS_PORT:-6379}

ARG MAIL_MAILER
ENV MAIL_MAILER=${MAIL_MAILER:-log}
ARG MAIL_HOST
ENV MAIL_HOST=${MAIL_HOST:-127.0.0.1}
ARG MAIL_PORT
ENV MAIL_PORT=${MAIL_PORT:-2525}
ARG MAIL_USERNAME
ENV MAIL_USERNAME=${MAIL_USERNAME}
ARG MAIL_PASSWORD
ENV MAIL_PASSWORD=${MAIL_PASSWORD}
ARG MAIL_ENCRYPTION
ENV MAIL_ENCRYPTION=${MAIL_ENCRYPTION}
ARG MAIL_FROM_ADDRESS
ENV MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS:-hello@example.com}
ARG MAIL_FROM_NAME
ENV MAIL_FROM_NAME=${MAIL_FROM_NAME:-Laravel}

ARG PUSHER_APP_ID
ENV PUSHER_APP_ID=${PUSHER_APP_ID}
ARG PUSHER_APP_KEY
ENV PUSHER_APP_KEY=${PUSHER_APP_KEY}
ARG PUSHER_APP_SECRET
ENV PUSHER_APP_SECRET=${PUSHER_APP_SECRET}
ARG PUSHER_APP_CLUSTER
ENV PUSHER_APP_CLUSTER=${PUSHER_APP_CLUSTER}

ARG VITE_APP_NAME
ENV VITE_APP_NAME=${VITE_APP_NAME:-Laravel}
ARG VITE_DEV_SERVER_URL
ENV VITE_DEV_SERVER_URL=${VITE_DEV_SERVER_URL:-http://localhost:5173}

ARG SUPABASE_URL
ENV SUPABASE_URL=${SUPABASE_URL}
ARG SUPABASE_SERVICE_ROLE_KEY
ENV SUPABASE_SERVICE_ROLE_KEY=${SUPABASE_SERVICE_ROLE_KEY}
ARG SUPABASE_ANON_KEY
ENV SUPABASE_ANON_KEY=${SUPABASE_ANON_KEY}

# Start application
EXPOSE 80
COPY ./.deploy/entrypoint.sh /

ENTRYPOINT ["sh", "/entrypoint.sh"]
