FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    git curl libpng-dev libjpeg-turbo-dev freetype-dev oniguruma-dev \
    libxml2-dev zip unzip libpq-dev icu-dev nginx supervisor

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd intl opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN echo "APP_NAME=Cultulangues" > .env \
    && echo "APP_ENV=production" >> .env \
    && echo "APP_DEBUG=true" >> .env \
    && echo "APP_URL=" >> .env \
    && echo "DB_CONNECTION=pgsql" >> .env \
    && echo "LOG_CHANNEL=stack" >> .env \
    && echo "CACHE_DRIVER=file" >> .env \
    && echo "FILESYSTEM_DISK=local" >> .env \
    && echo "QUEUE_CONNECTION=sync" >> .env \
    && echo "SESSION_DRIVER=file" >> .env \
    && echo "SESSION_LIFETIME=120" >> .env

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress \
    && php artisan key:generate --force

RUN mkdir -p storage/logs storage/framework/sessions storage/framework/views storage/framework/cache \
    && chmod -R 777 storage bootstrap/cache

COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8080

CMD ["/bin/sh", "-c", "php artisan migrate --force; php artisan view:cache; /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf"]
