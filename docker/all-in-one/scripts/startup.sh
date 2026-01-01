#!/bin/sh

echo "--- Preparing runtime directories ---"
mkdir -p /var/lib/nginx/logs
mkdir -p /var/lib/nginx/tmp/client_body
mkdir -p /var/lib/nginx/tmp/proxy
mkdir -p /var/lib/nginx/tmp/fastcgi
mkdir -p /var/lib/nginx/tmp/uwsgi
mkdir -p /var/lib/nginx/tmp/scgi

echo "Initializing Laravel storage directories..."
mkdir -p /app/backend/storage/app/public
mkdir -p /app/backend/storage/framework/cache/data
mkdir -p /app/backend/storage/framework/sessions
mkdir -p /app/backend/storage/framework/testing
mkdir -p /app/backend/storage/framework/views
mkdir -p /app/backend/storage/logs

cd /app/backend || exit 1

if ! php artisan migrate --force; then
    echo "============================================"
    echo "ERROR: Migrations could not complete. Check the error above."
    echo "Ensure DATABASE_URL is set."
    echo "============================================"
fi

echo "Clearing and caching Laravel configuration..."

php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache

exec /usr/bin/supervisord -c /etc/supervisord.conf
