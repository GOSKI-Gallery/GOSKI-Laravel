#!/bin/sh
set -e

echo "→ Aguardando PostgreSQL ficar pronto..."
max_tries=30
try=0
until php artisan db:show --quiet 2>/dev/null || [ $try -ge $max_tries ]; do
    try=$((try + 1))
    sleep 1
done

if [ $try -ge $max_tries ]; then
    echo "✗ PostgreSQL não respondeu após $max_tries segundos"
    exit 1
fi
echo "✓ PostgreSQL pronto"

if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ] || [ ${#APP_KEY} -lt 20 ]; then
    echo "→ Gerando APP_KEY..."
    php artisan key:generate --force
fi

echo "→ Rodando migrations..."
php artisan migrate --force

if [ "$APP_ENV" = "production" ]; then
    echo "→ Cache de produção..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

echo "✓ App pronto"
exec "$@"
