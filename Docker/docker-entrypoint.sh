#!/bin/bash

set -e
check_error=true

if $check_error ; then

    sh -c "cp /app/Docker/docker.env .env"

    sh -c 'pm2 start /app/odoo.yml'
    sh -c 'pm2 start /app/odoo_teacher.yml'
    sh -c 'pm2 start /app/queue.yml'

    sh -c "php artisan migrate"
    sh -c "chmod 777 -R storage"
    sh -c "chmod 777 -R public"

    exec "$@"
else
    echo 'Exit'
fi
