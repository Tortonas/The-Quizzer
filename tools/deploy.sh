#!/bin/bash
git pull
bin/console d:s:u --force
composer install
