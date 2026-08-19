#!/bin/sh
set -e

# Railway (and other PaaS providers) inject the port to listen on via $PORT.
# Fall back to 80 for local/manual runs.
PORT="${PORT:-80}"
sed -i "s/__PORT__/$PORT/g" /etc/nginx/sites-available/default

# Start PHP-FPM in the background, then run Nginx in the foreground so the
# container keeps running as long as Nginx is alive.
php-fpm -D
nginx -g 'daemon off;'
