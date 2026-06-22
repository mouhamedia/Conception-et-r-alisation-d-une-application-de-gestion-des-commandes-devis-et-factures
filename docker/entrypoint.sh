#!/bin/sh
set -e

if [ ! -L /var/www/html/public/storage ]; then
    php artisan storage:link
fi

php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec supervisord -c /etc/supervisord.conf
