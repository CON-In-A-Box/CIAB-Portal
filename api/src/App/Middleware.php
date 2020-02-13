<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

use Chadicus\Slim\OAuth2\Middleware;

$authMiddleware = new Middleware\Authorization($server, $app->getContainer());
