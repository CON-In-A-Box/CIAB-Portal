#!/bin/bash
source .env
docker-compose run php ./vendor/bin/phpunit --configuration phpunit.xml api
