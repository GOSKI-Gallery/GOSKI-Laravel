#!/bin/sh
set -e

mkdir -p storage/framework/views storage/framework/cache storage/framework/sessions storage/logs bootstrap/cache

if [ "${SKIP_DB_CHECK:-false}" != "true" ]; then
    echo "→ Aguardando banco de dados ficar pronto..."
    max_tries=30
    try=0
    until php -r "new PDO('pgsql:host=${DB_HOST};port=${DB_PORT:-5432};dbname=${DB_DATABASE};sslmode=${DB_SSLMODE:-require}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null || [ $try -ge $max_tries ]; do
        try=$((try + 1))
        sleep 1
    done

    if [ $try -ge $max_tries ]; then
        echo "✗ Banco não respondeu após $max_tries segundos"
        exit 1
    fi
    echo "✓ Banco pronto"

    echo "→ Criando schema laravel..."
    php -r "new PDO('pgsql:host=${DB_HOST};port=${DB_PORT:-5432};dbname=${DB_DATABASE};sslmode=${DB_SSLMODE:-require}', '${DB_USERNAME}', '${DB_PASSWORD}')->exec('CREATE SCHEMA IF NOT EXISTS laravel');" 2>&1

    if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ] || [ ${#APP_KEY} -lt 20 ]; then
        echo "→ Gerando APP_KEY..."
        php artisan key:generate --force
    fi

    echo "→ Rodando migrations..."
    php artisan migrate --force
fi

if [ "$APP_ENV" = "production" ]; then
    echo "→ Cache de produção..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

echo "✓ App pronto"
exec "$@"
