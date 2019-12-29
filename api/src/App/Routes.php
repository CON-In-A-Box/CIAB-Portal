<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

$app->get('/member/{name}/status', 'App\Controller\Member\GetStatus');

$app->group(
    '/member',
    function () use ($app, $authMiddleware) {
        $app->get('/[{name}]', 'App\Controller\Member\GetMember');
        $app->get('/{name}/deadlines', 'App\Controller\Member\ListDeadlines');
        $app->get('/deadlines/', 'App\Controller\Member\ListDeadlines');
    }
)->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

$app->group(
    '/department',
    function () use ($app, $authMiddleware) {
        $app->get('/', 'App\Controller\Department\ListDepartments');
        $app->get('/{name}', 'App\Controller\Department\GetDepartment');
        $app->get('/{name}/deadlines', 'App\Controller\Department\ListDeadlines');
    }
)->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

$app->group(
    '/deadline',
    function () use ($app, $authMiddleware) {
        /* GET
            /deadline/{id} */
        $app->get('/{id}', 'App\Controller\Deadline\GetDeadline');
        /* PUT
            /deadline/{dept}?Deadline={date}&Note={text} */
        $app->put('/{dept}', 'App\Controller\Deadline\PutDeadline');
        /* POST
            /deadline/{id}?[Department={dept}]&[Deadline={date}]&[Note={text}]*/
        $app->post('/{id}', 'App\Controller\Deadline\PostDeadline');
        /* DELETE
            /deadline/{id} */
        $app->delete('/{id}', 'App\Controller\Deadline\DeleteDeadline');
    }
)->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);
