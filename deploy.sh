#!/bin/bash

git pull
composer install
./artisan migrate
npm install
bower install
gulp build
