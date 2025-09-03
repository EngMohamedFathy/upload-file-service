FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    pkg-config \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_sqlite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

RUN chown -R www-data:www-data storage bootstrap/cache database

CMD ["php-fpm"]
