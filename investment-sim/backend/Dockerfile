# Use the official PHP image
FROM php:8.2-cli

# Install necessary extensions
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy the current project files
COPY . /var/www

# Install dependencies
RUN composer install

# Expose the application port
EXPOSE 8000

# Start Laravel using Artisan
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
