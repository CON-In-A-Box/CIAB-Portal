<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/


function setupConComAPI($app, $authMiddleware)
{
    $app->group(
        '/member',
        function () use ($app, $authMiddleware) {
            $app->get('/concom/', 'App\Modules\concom\Controller\GetMemberPosition');
            $app->get('/{id}/concom', 'App\Modules\concom\Controller\GetMemberPosition');
        }
    )->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

    $app->group(
        '/staff_membership',
        function () use ($app, $authMiddleware) {
            $app->get('/{id}', 'App\Modules\concom\Controller\GetStaffMembership');
        }
    )->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

    $app->group(
        '/department',
        function () use ($app, $authMiddleware) {
            $app->get('/concom/', 'App\Modules\concom\Controller\ListConcom');
            $app->get('/{id}/concom', 'App\Modules\concom\Controller\GetDepartment');
        }
    )->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

}
