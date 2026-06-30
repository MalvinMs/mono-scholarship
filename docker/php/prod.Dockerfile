FROM dunglas/frankenphp:latest AS base

RUN install-php-extensions \
    pdo_pgsql \
    pgsql \
    mbstring \
    intl \
    zip \
    pcntl \
    redis \
    opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && npm ci --no-audit --no-fund \
    && npm run build \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan event:cache \
    && php artisan view:cache

EXPOSE 80 443

CMD ["php", "artisan", "octane:frankenphp"]
