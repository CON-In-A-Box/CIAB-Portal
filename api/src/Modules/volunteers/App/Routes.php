<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/


function setupVolunteersAPI($app, $authMiddleware)
{
    $app->group(
        '/member',
        function () use ($app, $authMiddleware) {
            $app->get('/{id}/volunteer/hours', 'App\Modules\volunteers\Controller\Hours\GetMemberHours');
            $app->get('/{id}/volunteer/hours/summary', 'App\Modules\volunteers\Controller\Hours\GetMemberHoursSummary');
        }
    )->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

    $app->group(
        '/volunteer/hours',
        function () use ($app, $authMiddleware) {
            $app->get('/{id}', 'App\Modules\volunteers\Controller\Hours\GetHours');
            $app->post('[/]', 'App\Modules\volunteers\Controller\Hours\PostHours');
            $app->put('/{id}', 'App\Modules\volunteers\Controller\Hours\PutHours');
            $app->delete('/{id}', 'App\Modules\volunteers\Controller\Hours\DeleteHours');
        }
    )->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

    $app->group(
        '/department',
        function () use ($app, $authMiddleware) {
            $app->get('/{id}/volunteer/hours', 'App\Modules\volunteers\Controller\Hours\GetDepartmentHours');
            $app->get('/{id}/volunteer/hours/summary', 'App\Modules\volunteers\Controller\Hours\GetDepartmentHoursSummary');
        }
    )->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

    $app->group(
        '/event',
        function () use ($app, $authMiddleware) {
            $app->get('/{id}/volunteer/hours', 'App\Modules\volunteers\Controller\Hours\GetEventHours');
            $app->get('/{id}/volunteer/hours/summary', 'App\Modules\volunteers\Controller\Hours\GetEventHoursSummary');
        }
    )->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

}
