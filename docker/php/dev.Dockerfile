FROM dunglas/frankenphp:latest

RUN install-php-extensions \
    pdo_pgsql \
    pgsql \
    mbstring \
    intl \
    zip \
    pcntl \
    redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

EXPOSE 80

CMD ["sh", "-c", "composer install --no-interaction && php artisan octane:frankenphp --watch"]
