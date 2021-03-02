<?php

require_once(__DIR__.'/../functions/database.inc');
require_once(__DIR__.'/../data/database-schema.php');
require_once(__DIR__.'/../functions/update.inc');

$db = new DB();
\db_do_update(0, SCHEMA::$REQUIED_DB_SCHEMA);
