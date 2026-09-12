FROM php:8.2-cli

# Install Apache and the PHP module for Apache, using only the prefork MPM
RUN apt-get update && \
    apt-get install -y --no-install-recommends apache2 libapache2-mod-php8.2 && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Install pdo_mysql PHP extension for MySQL database connectivity
RUN docker-php-ext-install pdo_mysql

# Enable mod_rewrite for URL routing, mod_php for PHP handling, and the
# prefork MPM only (mod_php requires prefork and is incompatible with
# the threaded event/worker MPMs, so those are explicitly disabled)
RUN a2enmod rewrite php8.2 mpm_prefork \
    && a2dismod mpm_event mpm_worker || true

# Allow .htaccess overrides (needed for mod_rewrite) in the document root
RUN sed -ri -e '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' \
    /etc/apache2/apache2.conf

# Copy application files into Apache's document root
COPY . /var/www/html

# Make entrypoint script executable
RUN chmod +x /var/www/html/docker-entrypoint.sh

# Set proper file ownership for the www-data user
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

# Use entrypoint script to handle Render's dynamic PORT
CMD ["/var/www/html/docker-entrypoint.sh"]
