FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    git curl libpng-dev libjpeg-turbo-dev freetype-dev oniguruma-dev \
    libxml2-dev zip unzip libpq-dev icu-dev nginx supervisor

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd intl opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

RUN php artisan key:generate --force

RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD ["/bin/sh", "-c", "php artisan migrate --force && php artisan db:seed --force && /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf"]
