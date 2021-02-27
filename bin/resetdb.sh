#!/bin/bash

source .env

docker-compose down --remove-orphans
docker volume prune -f
./bin/docker_instance.sh up -d

echo "Waiting a few seconds for the various containers to re-initialize!"
sleep 10

echo "Creating test db"
docker-compose exec -T mysql mysql -uroot -hlocalhost -p${DBROOTPW} --protocol=socket <<-SQL
CREATE DATABASE IF NOT EXISTS \`${TEST_DBNAME}\` ;
SQL

docker-compose exec -T mysql mysql -uroot -hlocalhost -p${DBROOTPW} --protocol=socket <<-SQL
GRANT ALL ON \`${TEST_DBNAME}\`.* to '${DBUSER}'@'%' ;
SQL
