# Multi-stage build for Laravel (PHP 8.2 + Apache) with Vite assets
#
# Stage 1: Build frontend assets with Node (Vite)
FROM node:18-alpine AS node_build
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm ci
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm run build

# Stage 2: Install PHP dependencies with Composer
FROM composer:2 AS composer_build
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress
COPY . .
# Ensure autoload is optimized
RUN composer dump-autoload --optimize

# Stage 3: Final image with PHP 8.2 + Apache
FROM php:8.2-apache

# Set document root to Laravel public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf && \
    sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf && \
    a2enmod rewrite

# System packages and PHP extensions (PostgreSQL)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev git unzip \
 && docker-php-ext-install pdo pdo_pgsql \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy application source
COPY --from=composer_build /app /var/www/html

# Copy built frontend assets
COPY --from=node_build /app/public/build /var/www/html/public/build

# Permissions for storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose web port
EXPOSE 80

# Default command: start Apache
CMD ["apache2-foreground"]
