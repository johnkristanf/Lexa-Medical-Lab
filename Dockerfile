# Stage 1: Node Build Stage
FROM node:18-alpine as node_builder

WORKDIR /app

COPY ./package*.json .
RUN npm install

COPY . .
RUN npm run build



# Stage 2: PHP build stage
FROM php:8.2-fpm AS php_builder
WORKDIR /app

RUN apt-get update && apt-get install -y unzip libzip-dev zip curl
RUN docker-php-ext-install pdo pdo_mysql zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction


# Stage 3: Runtime
FROM php:8.3-fpm
WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    unzip libpng-dev libonig-dev libxml2-dev zip curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd


COPY --from=php_builder /app /var/www
COPY --from=node_builder /app/public/build /var/www/public/build


COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
