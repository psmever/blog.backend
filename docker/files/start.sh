#!/bin/bash

service php8.2-fpm start

tail -f "/var/www/storage/logs/laravel.log" | sed 's/^/laravel: /' &
tail -f "/var/log/php8.2-fpm.log" | sed 's/^/php8.2-fpm: /' &
tail -f "/var/log/nginx/access.log" | sed 's/^/access.log: /' &
tail -f "/var/log/nginx/error.log" | sed 's/^/error.log: /' &


nginx -g "daemon off;"


