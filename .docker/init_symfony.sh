#!/bin/sh
sleep 10
cd /var/www/html
composer install
php bin/console d:s:u --force
php bin/console doctrine:fixtures:load --append
echo "Project has been started"
echo "Access project website - http://192.168.2.2/"
echo "MYSQL Server - 192.168.2.3:3306"
echo "Access phpmyadmin via - http://192.168.2.4/"
echo "Access server SSH by writing - docker exec -it quizzer_web bash"