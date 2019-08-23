<?php

require_once __DIR__.'/functions/functions.inc';
require_once __DIR__."/backends/oauth2_pdo.inc";

$dsn = "mysql:";
$storage = new CIAB\OAuth2\Pdo(array('dsn' => $dsn));
$sql = $storage->getBuildSql();
try {
    DB::run($sql);
} catch (Exception $e) {
    die("OAuth2 Database already up to date\n");
}
print("OAuth2 Database update complete\n");
