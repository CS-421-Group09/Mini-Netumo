FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    libzip-dev libpq-dev libssl-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip gd

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files first (for caching)
COPY composer.json composer.lock ./

# Install Composer dependencies (no scripts, just vendor)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts

# Copy the rest of the app (including artisan)
COPY . .

# Now run composer install again, this time with scripts
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Install Nginx
RUN apt-get update && apt-get install -y nginx

# Copy Nginx config
COPY ./nginx.conf /etc/nginx/sites-available/default

EXPOSE 80

# Start both php-fpm and nginx
CMD service nginx start && php-fpm
