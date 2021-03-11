#!/bin/bash

docker-compose run --rm php ./vendor/bin/openapi -o ./ciab.openapi.yaml api/
