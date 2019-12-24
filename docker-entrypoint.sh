#!/bin/bash

set -e
check_error=true

if $check_error ; then
    sh -c 'pm2 start /var/www/nct_detail.yml'
    sh -c 'pm2 start /var/www/queue.yml'

    sh -c "php artisan migrate"
    exec "$@"
else
    echo 'Exit'
fi
