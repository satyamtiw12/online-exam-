# Base image: PHP 8.2 with Apache
FROM php:8.2-apache

# Install extensions: mysqli (MySQL) optional, PDO + pgsql for PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && docker-php-ext-install mysqli \
    && a2enmod rewrite \
    && apt-get clean

# Set working directory
WORKDIR /var/www/html

# Copy project files into container
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80
