#!/bin/sh

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

php artisan storage:link

exec /usr/bin/supervisord -c /etc/supervisord.conf
