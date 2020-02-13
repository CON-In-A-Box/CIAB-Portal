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
$modules = scandir(__DIR__.'/../modules');
foreach ($modules as $key => $value) {
    if (!in_array($value, array(',', '..'))) {
        if (in_array($value, $DISABLEDMODULES)) {
            continue;
        }
        if (is_dir(__DIR__.'/../modules/'.$value)) {
            if (is_file(__DIR__.'/../modules/'.$value.'/App/Dependencies.php')) {
                include(__DIR__.'/../modules/'.$value.'/App/Dependencies.php');
            }
            if (is_file(__DIR__.'/../modules/'.$value.'/App/Routes.php')) {
                include(__DIR__.'/../modules/'.$value.'/App/Routes.php');
            }
        }
    }
}
