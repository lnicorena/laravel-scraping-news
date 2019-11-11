#!/bin/bash

docker exec -it php-fpm php artisan $1 $2 $3
