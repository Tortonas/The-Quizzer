FROM php:7.4.8-apache
RUN apt-get update && apt-get upgrade -y
RUN apt-get install git zlib1g-dev libxml2-dev libzip-dev -y
RUN docker-php-ext-install zip intl mysqli pdo pdo_mysql