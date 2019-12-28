<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

$app->get('/member/{name}/status', 'App\Controller\Member\GetStatus');

$app->group(
    '/member',
    function () use ($app, $authMiddleware) {
        $app->get('/[{name}]', 'App\Controller\Member\GetMember');
    }
)->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

$app->group(
    '/department',
    function () use ($app, $authMiddleware) {
        $app->get('/', 'App\Controller\Department\ListDepartments');
        $app->get('/{name}', 'App\Controller\Department\GetDepartment');
    }
)->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);
