#!/bin/sh
cd /var/www/html/dev
/usr/bin/php artisan queue:work & > /dev/null 2>&1
