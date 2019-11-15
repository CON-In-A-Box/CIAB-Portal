#!/bin/bash

if [ "$1" == "up" ]; then
    shift
    chown o+w resources
    DBUSER="ciabuser" DBNAME="ciab" DBPASS="ciabpass" DB_BACKEND="mysqlpdo.inc" docker-compose -f docker-compose.yml -f docker-compose.phpmyadmin.yml up
elif [ "$1" == "down" ]; then
    shift
    docker-compose -f docker-compose.yml -f docker-compose.phpmyadmin.yml down $@;
fi
