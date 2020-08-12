#!/bin/sh
sleep 10
cd /var/www/html
composer install
php bin/console d:s:u --force
php bin/console doctrine:fixtures:load --append
echo "Project has been started"
echo "Website - http://192.168.2.2/"
echo "MYSQL - 192.168.2.3:3306"
