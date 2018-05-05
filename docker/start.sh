#!/bin/bash

sudo docker-compose exec php composer install
sudo docker-compose exec php bin/console doctrine:database:create --if-not-exists
sudo docker-compose exec php bin/console doctrine:migrations:migrate -n