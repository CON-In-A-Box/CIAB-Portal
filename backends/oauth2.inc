<?php

require_once __DIR__."/../vendor/autoload.php";
require_once __DIR__."/oauth2_pdo.inc";
require_once __DIR__."/../.ht_meetingsignin_config.php";

$dsn      = "mysql:dbname=$DBNAME;host=$DBHOST";

// error reporting (this is a demo, after all!)
//ini_set('display_errors',1);error_reporting(E_ALL);

$storage = new CIAB\OAuth2\Pdo(array('dsn' => $dsn, 'username' => $DBUSER, 'password' => $DBPASS));

// Pass a storage object or array of storage objects to the OAuth2 server class
$server = new OAuth2\Server($storage);
$server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
$server->addGrantType(new OAuth2\GrantType\UserCredentials($storage));
$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
