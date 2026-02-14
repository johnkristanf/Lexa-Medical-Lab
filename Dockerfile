# Stage 1: PHP build stage
FROM php:8.2-fpm AS php_builder
WORKDIR /app

RUN apt-get update && apt-get install -y unzip libzip-dev zip curl
RUN docker-php-ext-install pdo pdo_mysql zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction


# Stage 2: Node Build Stage
FROM node:18-alpine AS node_builder

WORKDIR /app

ARG VITE_PUSHER_APP_KEY
ARG VITE_PUSHER_APP_CLUSTER
ARG VITE_PUSHER_SCHEME

ENV VITE_PUSHER_APP_KEY=$VITE_PUSHER_APP_KEY
ENV VITE_PUSHER_APP_CLUSTER=$VITE_PUSHER_APP_CLUSTER
ENV VITE_PUSHER_SCHEME=$VITE_PUSHER_SCHEME

COPY ./package*.json ./
RUN npm install

COPY resources ./resources
COPY public ./public

COPY vite.config.js .
COPY tailwind.config.js .
COPY postcss.config.js .

COPY --from=php_builder /app/vendor/tightenco/ziggy ./vendor/tightenco/ziggy

RUN npm run build

 
# Stage 3: Runtime
FROM php:8.3-fpm AS php_runtime
WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    unzip libpng-dev libonig-dev libxml2-dev zip curl \
    libpq-dev \
    && docker-php-ext-install \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd

COPY --from=php_builder /app /var/www
COPY --from=node_builder /app/public/build /var/www/public/build

# Ensure the entrypoint script is copied from build context, not a potentially-missing relative location
COPY ./entrypoint.sh /var/www/entrypoint.sh
RUN chmod +x /var/www/entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["/var/www/entrypoint.sh"]
