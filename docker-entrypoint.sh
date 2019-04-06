#!/bin/bash

COMPOSER=/usr/local/bin/composer

cd ${APP_DIR}
${COMPOSER} install
chown -R ciab ${APP_DIR}
chmod u+s ${APP_DIR}
if [ "$1" = 'dev' ]; then
  exec docker-php-entrypoint apache2-foreground
fi

exec $@
