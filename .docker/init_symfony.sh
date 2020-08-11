#!/bin/sh
sleep 10
cd /var/www/html
composer install
php bin/console d:s:u --force
