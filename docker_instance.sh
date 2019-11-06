#!/bin/bash

if [ "$1" == "up" ]; then
    shift
    DBUSER="ciabuser" DBNAME="ciab" DBPASS="ciabpass" docker-compose -f docker-compose.yml -f docker-compose.phpmyadmin.yml up
elif [ "$1" == "down" ]; then
    shift
    docker-compose -f docker-compose.yml -f docker-compose.phpmyadmin.yml down $@;
fi
