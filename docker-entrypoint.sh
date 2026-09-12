#!/bin/sh
set -e

# Replace default Apache port 80 with Render's $PORT environment variable if defined
if [ -n "$PORT" ]; then
    sed -i "s/80/$PORT/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf
fi

exec "$@"

