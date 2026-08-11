#!/bin/sh
set -e

mkdir -p storage/framework/views storage/framework/cache storage/framework/sessions storage/logs bootstrap/cache

# Permite que o php-fpm (www-data) grave logs e caches criados pelo entrypoint (root)
if [ "$(id -u)" = "0" ]; then
    chown -R www-data:www-data storage bootstrap/cache
fi

# Popula o volume de assets com o build dos ativos da imagem (se vazio / primeira subida)
if [ -d /opt/goski-build ] && [ ! -f public/build/manifest.json ]; then
    echo "→ Seed de assets de produção..."
    mkdir -p public/build
    cp -a /opt/goski-build/. public/build/
fi

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
    if ! php artisan migrate --force 2>&1; then
        echo "✗ Migrations falharam (o container sobe mesmo assim; revise laravel.log)"
    fi
fi

if [ "$APP_ENV" = "production" ]; then
    echo "→ Cache de produção..."
    php artisan config:cache
    if ! php artisan route:cache 2>&1; then
        echo "✗ Route cache falhou (o container sobe mesmo assim; revise laravel.log)"
        php artisan route:clear 2>/dev/null || true
    fi
    if ! php artisan view:cache 2>&1; then
        echo "✗ View cache falhou (o container sobe mesmo assim; revise laravel.log)"
    fi
fi

echo "✓ App pronto"
exec "$@"
