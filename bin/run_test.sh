#!/bin/bash
source .env
docker-compose run --rm php ./vendor/bin/phpunit --configuration phpunit.xml api
