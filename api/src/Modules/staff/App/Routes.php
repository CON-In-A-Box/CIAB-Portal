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
        }
    )->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

    $app->group(
        '/staff_membership',
        function () use ($app, $authMiddleware) {
            $app->get('/{id}', 'App\Modules\staff\Controller\GetStaffMembership');
            $app->delete('/{id}', 'App\Modules\staff\Controller\DeleteStaffMembership');
        }
    )->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

    $app->group(
        '/department',
        function () use ($app, $authMiddleware) {
            $app->get('/staff/', 'App\Modules\staff\Controller\ListStaff');
            $app->get('/{id}/staff', 'App\Modules\staff\Controller\GetDepartment');
        }
    )->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

}
