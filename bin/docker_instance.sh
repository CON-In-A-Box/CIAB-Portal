#!/bin/bash

source .env

if [ "$1" == "up" ]; then
    shift
    docker-compose -f docker-compose.yml -f docker-compose.phpmyadmin.yml up $@
elif [ "$1" == "down" ]; then
    shift
    docker-compose -f docker-compose.yml -f docker-compose.phpmyadmin.yml down $@;
elif [ "$1" == "nuke" ]; then
    shift
    echo "BLOWING AWAY YOUR OLD DATABASE (a new one will be created next time you run 'up', but you'll need to reseed it!"
    docker-compose -f docker-compose.yml -f docker-compose.phpmyadmin.yml down --volumes --remove-orphans --rmi all $@;
fi
