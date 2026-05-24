# Bước 1: Base image - PHP 8.2 với FPM
FROM php:8.2-fpm

# Bước 2: Cài system dependencies để build PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Bước 3: Cài PHP extensions cần cho Laravel
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    bcmath \
    zip \
    gd

# Bước 4: Cài Composer từ image chính thức
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Bước 5: Set thư mục làm việc
WORKDIR /var/www/html

# Bước 6: Copy composer files trước để tận dụng Docker cache
COPY composer.json composer.lock ./

# Bước 7: Cài PHP dependencies
RUN composer install --no-scripts --no-autoloader --ignore-platform-req=php

# Bước 8: COPY source code vào container ← ĐANG THIẾU!
COPY . .

# Bước 9: Chạy autoload
RUN composer dump-autoload --optimize

# Bước 10: Set permission cho storage và cache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache
# PHP-FPM chạy port 9000
EXPOSE 9000
CMD ["php-fpm"]
