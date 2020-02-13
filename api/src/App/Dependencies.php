<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

use Psr\Container\ContainerInterface;
use App\Handler\ApiError;

$container = $app->getContainer();

$container['errorHandler'] = function (): ApiError {
    return new ApiError;

};

$container['db'] = function ($c) {
    $settings = $c->get('settings')['db'];
    $pdo = new PDO(
        "mysql:host=".$settings['host'].";dbname=".$settings['dbname'],
        $settings['user'],
        $settings['pass']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;

};
