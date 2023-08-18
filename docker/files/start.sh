#!/bin/bash

service php8.2-fpm start

tail -F /var/www/storage/logs/laravel.log &
tail -F /var/log/php8.2-fpm.log &
tail -F /var/log/nginx/access.log &
tail -F /var/log/nginx/error.log &

nginx -g "daemon off;"


