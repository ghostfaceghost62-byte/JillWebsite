FROM php:8.2-apache

# Install required system dependencies for PHP extensions (gd, zip)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite for clean URL routing
RUN a2enmod rewrite

# Allow .htaccess overrides in /var/www/html
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Set Apache DocumentRoot working directory
WORKDIR /var/www/html

# Copy application code into container
COPY . /var/www/html

# Copy Composer binary from official image and install dependencies
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set proper file permissions for Apache www-data user
RUN chown -R www-data:www-data /var/www/html

# Copy entrypoint script for dynamic $PORT binding on Render
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
