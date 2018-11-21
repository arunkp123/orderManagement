#!/usr/bin/env bash

red=$'\e[1;31m'
grn=$'\e[1;32m'
blu=$'\e[1;34m'
mag=$'\e[1;35m'
cyn=$'\e[1;36m'
white=$'\e[0m'

sudo apt update
sudo apt install -y curl

echo " $red ----- Installing Pre requisites ------- $white "
sudo docker-compose down && docker-compose up --build -d


echo " $grn -------Installing Dependencies -----------$blu "
sudo sleep 180s

echo " $red ----- Running Migrations & Data Seeding ------- $white "
sudo chmod 777 -R ./code/*
docker exec manage_order_php php artisan migrate
docker exec manage_order_php php artisan db:seed

echo " $red ----- Running Intergration test cases ------- $white "
docker exec manage_order_php php ./vendor/phpunit/phpunit/phpunit /var/www/html/tests/Feature/OrderControllerTest.php

exit 0
