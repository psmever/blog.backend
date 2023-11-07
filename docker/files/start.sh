#!/bin/bash

service php8.2-fpm start

tail -F /var/www/storage/logs/laravel.log | xargs -I {} echo "laravel.log -> {}" &
tail -F /var/log/php8.2-fpm.log | xargs -I {} echo "php8.2-fpm.log -> {}" &
tail -F /var/log/nginx/access.log | xargs -I {} echo "access.log -> {}" &
tail -F /var/log/nginx/error.log | xargs -I {} echo "error.log -> {}" &

nginx -g "daemon off;"


