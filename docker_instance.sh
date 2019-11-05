#!/bin/bash

if [ "$1" == "up" ]; then
    shift
    DBUSER="ciabuser" DBNAME="ciab" DBPASS="ciabpass" docker-compose up $@;
elif [ "$1" == "down" ]; then
    shift
    docker-compose down $@;
fi

