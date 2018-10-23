#!/usr/bin/env bash
set -e

if [ ! -f /var/www/html/env.json ]; then
    if [ -f /var/www/html/env.json.dist ]; then
        cp /var/www/html/env.json.dist /var/www/html/env.json
        chown www-data:www-data /var/www/html/env.json
    fi
fi

if [ -f /var/www/html/composer.json ]; then
    composer install
fi

if [ -f /var/www/html/package.json ]; then
    npm install
fi

exec supervisord -c /etc/supervisor/supervisord.conf
