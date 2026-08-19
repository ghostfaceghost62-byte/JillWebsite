FROM php:8.2-fpm

# Install Nginx alongside PHP-FPM. Using PHP-FPM + Nginx avoids Apache
# entirely, so there are no MPM/mod_php conflicts to worry about.
RUN apt-get update && \
    apt-get install -y --no-install-recommends nginx && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Install pdo_mysql PHP extension for MySQL database connectivity
RUN docker-php-ext-install pdo_mysql

# Nginx configuration: serve the app from /var/www/html and forward
# any *.php request to the PHP-FPM socket.
COPY docker/nginx.conf /etc/nginx/sites-available/default

# Copy application files into the shared document root
COPY . /var/www/html

# Set proper file ownership for the www-data user
RUN chown -R www-data:www-data /var/www/html

# Entrypoint script that starts PHP-FPM and Nginx together
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 80

CMD ["/usr/local/bin/start.sh"]
