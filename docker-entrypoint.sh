#!/bin/sh
set -e

# Replace default Apache port 80 with Render's $PORT environment variable
if [ -n "$PORT" ]; then
    sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
    sed -i "s/:80/:$PORT/g" /etc/apache2/sites-enabled/000-default.conf
fi

# Start Apache in the foreground
exec apachectl -D FOREGROUND
