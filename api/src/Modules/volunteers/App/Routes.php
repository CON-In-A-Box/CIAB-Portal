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
            $app->get('/{id}/volunteer/claims', 'App\Modules\volunteers\Controller\Claims\GetMemberClaims');
            $app->get('/{id}/volunteer/claims/summary', 'App\Modules\volunteers\Controller\Claims\GetMemberClaimsSummary');
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
        '/volunteer/rewards',
        function () use ($app, $authMiddleware) {
            $app->get('[/]', 'App\Modules\volunteers\Controller\Rewards\ListRewards');
            $app->get('/{id}', 'App\Modules\volunteers\Controller\Rewards\GetReward');
            $app->post('[/]', 'App\Modules\volunteers\Controller\Rewards\PostReward');
            $app->put('/{id}', 'App\Modules\volunteers\Controller\Rewards\PutReward');
            $app->delete('/{id}', 'App\Modules\volunteers\Controller\Rewards\DeleteReward');
            $app->put('/{id}/inventory', 'App\Modules\volunteers\Controller\Rewards\PutRewardInventory');
        }
    )->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

    $app->group(
        '/volunteer/claims',
        function () use ($app, $authMiddleware) {
            $app->get('/{id}', 'App\Modules\volunteers\Controller\Claims\GetClaim');
            $app->post('[/]', 'App\Modules\volunteers\Controller\Claims\PostClaim');
            $app->put('/{id}', 'App\Modules\volunteers\Controller\Claims\PutClaim');
            $app->delete('/{id}', 'App\Modules\volunteers\Controller\Claims\DeleteClaim');
        }
    )->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

    $app->group(
        '/volunteer/reward_group',
        function () use ($app, $authMiddleware) {
            $app->get('/{id}', 'App\Modules\volunteers\Controller\Rewards\GetRewardGroup');
            $app->post('[/]', 'App\Modules\volunteers\Controller\Rewards\PostRewardGroup');
            $app->put('/{id}', 'App\Modules\volunteers\Controller\Rewards\PutRewardGroup');
            $app->delete('/{id}', 'App\Modules\volunteers\Controller\Rewards\DeleteRewardGroup');
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
            $app->get('/{id}/volunteer/claims/summary', 'App\Modules\volunteers\Controller\Claims\GetEventClaimsSummary');
        }
    )->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

}
