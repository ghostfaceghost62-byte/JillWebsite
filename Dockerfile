FROM php:8.2-apache

# Install pdo_mysql PHP extension for MySQL database connectivity
RUN docker-php-ext-install pdo_mysql

# Enable Apache mod_rewrite for URL routing
RUN a2enmod rewrite

# Copy application files
COPY . /var/www/html

# Set proper file ownership for the www-data user
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
