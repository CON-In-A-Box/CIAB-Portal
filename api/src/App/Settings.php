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
'modules' => [],
    ],
 /* An array of Vendors to load.
  *
  * A string will load all the files in Vendors/<vendor>  (ex. 'Zend')
  * An associative array will only load the given files from the vendor
  *   (ex. ['Zend' => ['Rbac.php']])
  */
'vendors' => [
'Stripe'
    ]
];
