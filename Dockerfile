FROM php:8.2-apache

# Install pdo_mysql PHP extension for MySQL database connectivity
RUN docker-php-ext-install pdo_mysql

# Enable Apache mod_rewrite for URL routing
RUN a2enmod rewrite

# Set Apache document root to /var/www/html
ENV APACHE_DOCUMENT_ROOT=/var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy application files
COPY . /var/www/html

# Set proper file ownership for the www-data user
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
