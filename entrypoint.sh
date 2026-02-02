#!/bin/sh

set -e

echo "Setting correct storage and bootstrap/cache permissions..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache


php artisan migrate --force
php artisan db:seed --force

php artisan optimize

exec php-fpm -F -R