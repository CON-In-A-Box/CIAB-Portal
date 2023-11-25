<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/


function setupStaffAPI($app, $authMiddleware)
{
    $app->group(
        '/member',
        function () use ($app, $authMiddleware) {
            $app->get('/staff_membership/', 'App\Modules\staff\Controller\GetMemberPosition');
            $app->get('/{id}/staff_membership', 'App\Modules\staff\Controller\GetMemberPosition');
            $app->post('/{id}/staff_membership', 'App\Modules\staff\Controller\PostStaffMembership');
            $app->put('/{id}/staff_membership', 'App\Modules\staff\Controller\PutStaffMembership');
        }
    )->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

    $app->group(
        '/staff',
        function () use ($app, $authMiddleware) {
            $app->get('[/]', 'App\Modules\staff\Controller\ListStaff');
            $app->get('/membership/{id}', 'App\Modules\staff\Controller\GetStaffMembership');
            $app->delete('/membership/{id}', 'App\Modules\staff\Controller\DeleteStaffMembership');
            $app->get('/positions', 'App\Modules\staff\Controller\ListStaffPositions');
        }
    )->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

    $app->group(
        '/department',
        function () use ($app, $authMiddleware) {
            $app->get('/{id}/staff', 'App\Modules\staff\Controller\ListDepartmentStaff');
        }
    )->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

    $app->group(
        '/division',
        function () use ($app, $authMiddleware) {
            $app->get('/{id}/staff', 'App\Modules\staff\Controller\ListDivisionStaff');
        }
    )->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

}
