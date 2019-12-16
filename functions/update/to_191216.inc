<?php

/*.
    require_module 'standard';
.*/

require_once(__DIR__."/../functions.inc");
require_once(__DIR__."/../../vendor/autoload.php");
require_once(__DIR__."/../../backends/oauth2_pdo.inc");


function to_191216()
{
    $dsn = "mysql:dbname=${_ENV['DBNAME']};host=${_ENV['DBHOST']}";
    $storage = new CIAB\OAuth2\Pdo(array('dsn' => $dsn,
                                         'username' => $_ENV['DBUSER'],
                                         'password' => $_ENV['DBPASS']));
    $sql = $storage->getBuildSql();

    try {
        \DB::run($sql);
    } catch (Exception $e) {
        print ("<h2>OAuth2 Database already up to date</h2>\n");
    }
    print("<h2>OAuth2 Database update complete<h2>\n");

    $sql = "INSERT INTO `oauth_clients` (`client_id`) VALUES ('ciab')";
    try {
        \DB::run($sql);
    } catch (Exception $e) {
        print ("<h2>base ciab client already exists</h2>\n");
    }
    print("<h2>OAuth2 client update complete<h2>\n");

}
