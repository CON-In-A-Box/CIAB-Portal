#!/bin/bash
source .env
export DBNAME=${TEST_DBNAME}

docker-compose run --rm php php tools/create_schema.php
docker-compose run --rm php php tools/test_prereqs.php
docker-compose run --rm php ./vendor/bin/phpunit --configuration phpunit.xml api $@
