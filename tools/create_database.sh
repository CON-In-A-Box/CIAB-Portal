#!/bin/bash

function usage {
    echo "$0 usage:";
    echo "      $0 <mysql root password>";
    exit;
}

which mysql > /dev/null

if [ $? -ne 0 ]; then
    echo "FAILED: Command mysql not in the path.";
    echo "Performing NO actions.";
    exit;
fi


if [[ -f ".env" ]]; then
    echo "FAILED: Environment already exists.";
    echo "Performing NO actions.";
    exit;
fi

if [ "$#" -ne 1 ]; then
    usage;
fi

if [ "$1" == "//q" ] || [ "$1" == "-?" ] || [ "$1" == "-h" ] ; then
    usage;
fi

DB_ROOT_PASSWORD=$1

PASSWORD=$(cat /dev/urandom | env LC_CTYPE=C tr -dc a-zA-Z0-9 | head -c 16;)
POSTFIX=$(cat /dev/urandom | env LC_CTYPE=C tr -dc a-zA-Z0-9 | head -c 4;)
CON_DB='ciab_'${POSTFIX}
USER='ciab-dev-'${POSTFIX}

echo "DBUSER=\"${USER}\"" > .env;
echo "DBNAME=\"${CON_DB}\"">> .env;
echo "DBPASS=\"${PASSWORD}\"">> .env;


mysql -h localhost -u root "-p${DB_ROOT_PASSWORD}" -A -e " \
    CREATE DATABASE ${CON_DB};
    CREATE USER '${USER}'@'localhost' IDENTIFIED BY '${PASSWORD}';
    GRANT ALL PRIVILEGES ON `${CON_DB}`.* TO '${USER}'@'localhost' WITH GRANT OPTION;
    FLUSH PRIVILEGES;
"

`chmod oug+rwx .env`
