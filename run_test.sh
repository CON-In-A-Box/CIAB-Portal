#!/bin/bash

docker-compose run php ./vendor/bin/phpunit --configuration phpunit.xml api
