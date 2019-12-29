<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

return [
'settings' => [
'displayErrorDetails' => true,
'db' => [
'host' => $_ENV['DBHOST'],
'dbname' => $_ENV['DBNAME'],
'user' => $_ENV['DBUSER'],
'pass' => $_ENV['DBPASS']
        ],
    ],
];
