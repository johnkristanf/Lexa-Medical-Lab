#!/bin/sh

set -e

echo "Setting correct storage and bootstrap/cache permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache


php artisan migrate --seed --force
php artisan optimize

exec php-fpm -F -R