# Dockerfile

FROM php:8.3-cli

WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip curl zip libzip-dev libonig-dev libxml2-dev libicu-dev libpq-dev \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath intl pcntl opcache sockets

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Fix Git dubious ownership (important for mounted volumes)
RUN git config --global --add safe.directory /var/www/html

# Set Composer memory limit
ENV COMPOSER_MEMORY_LIMIT=-1

# Copy project files
COPY . .

# Fix Laravel permissions
RUN chmod -R 777 storage bootstrap/cache

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader

# Default command (can be overridden in docker-compose)
CMD ["php", "artisan", "queue:work", "rabbitmq", "--queue=emails", "--sleep=1", "--tries=3"]