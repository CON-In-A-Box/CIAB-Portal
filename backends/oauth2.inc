<?php

require_once __DIR__."/../vendor/autoload.php";
require_once __DIR__."/oauth2_pdo.inc";


function setupOAUTH2()
{
    $dotenv = Dotenv\Dotenv::create(dirname(__DIR__));
    $dotenv->load();

    $dsn      = "mysql:dbname=${_ENV['DBNAME']};host=${_ENV['DBHOST']}";

    $storage = new CIAB\OAuth2\Pdo(array('dsn' => $dsn, 'username' => $_ENV['DBUSER'],
                                         'password' => $_ENV['DBPASS']));

    // Pass a storage object or array of storage objects to the OAuth2 server class
    $server = new OAuth2\Server(
        $storage,
        [
            'access_lifetime' => 3600,
        ],
        [
            new OAuth2\GrantType\ClientCredentials($storage),
            new OAuth2\GrantType\AuthorizationCode($storage),
            new OAuth2\GrantType\UserCredentials($storage),
            new OAuth2\GrantType\RefreshToken($storage, array(
                'always_issue_new_refresh_token' => true,
            )),
        ]
    );

    return [$server, $storage];

}
