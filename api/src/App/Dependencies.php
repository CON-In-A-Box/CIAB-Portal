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
