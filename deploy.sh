#!/bin/bash

git pull
composer install
php artisan migrate
cd public/front/
npm install
gulp build
