#!/bin/bash

up() {
    DBUSER="ciabuser" DBNAME="ciab" DBPASS="ciabpass" DB_BACKEND="mysqlpdo.inc" docker-compose -f docker-compose.yml -f docker-compose.phpmyadmin.yml up $@

}

down() {
    docker-compose -f docker-compose.yml -f docker-compose.phpmyadmin.yml down $@;
}

nuke() {
    echo "BLOWING AWAY YOUR OLD DATABASE (a new one will be created next time you run 'up', but you'll need to reseed it!"
    docker-compose -f docker-compose.yml -f docker-compose.phpmyadmin.yml down -v $@;
}

if [ "$1" == "up" ]; then
    shift
    up $@
elif [ "$1" == "down" ]; then
    shift
    down $@
elif [ "$1" == "nuke" ]; then
    shift
    nuke $@
elif [ "$1" == "restart" ]; then
    shift
    down --remove-orphans
    up $@
fi
