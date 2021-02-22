#!/bin/bash

docker-compose run php ./vendor/bin/phpunit --configuration phpunit.xml --coverage-clover build/logs/clover.xml --coverage-html build/coverage api
