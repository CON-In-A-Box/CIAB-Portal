<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

require __DIR__.'/../../../vendor/autoload.php';
require __DIR__.'/../../../functions/functions.inc';

if (is_file(__DIR__.'/../../../.env')) {
    $dotenv = Dotenv\Dotenv::create(__DIR__.'/../../..');
    $dotenv->load();
}

$settings = require __DIR__.'/Settings.php';
$app = new \Slim\App($settings);
require __DIR__.'/Dependencies.php';
require __DIR__.'/OAuth2.php';
require __DIR__.'/Middleware.php';
require __DIR__.'/Routes.php';
