<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

$app->get('/member/{name}/status', 'App\Controller\Member\GetStatus');
$app->post('/member[/]', 'App\Controller\Member\PostMember');
$app->post('/member/{email}/password', 'App\Controller\Member\PostPassword');
$app->put('/member/{email}/password/recovery', 'App\Controller\Member\PutPassword');

$app->group(
    '/member',
    function () use ($app, $authMiddleware) {
        $app->get('/find', 'App\Controller\Member\FindMembers');
        $app->get('[/[{id}]]', 'App\Controller\Member\GetMember');
        $app->put('/{id}', 'App\Controller\Member\PutMember');
        $app->put('/{id}/password', 'App\Controller\Member\PutPassword');
        $app->get('/{id}/deadlines', 'App\Controller\Member\ListDeadlines');
        $app->get('/{id}/announcements', 'App\Controller\Announcement\ListMemberAnnouncements');
        $app->get('/{id}/configuration[/{key}]', 'App\Controller\Member\GetConfiguration');
        $app->put('/{id}/configuration', 'App\Controller\Member\PutConfiguration');
    }
)->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

$app->group(
    '/department',
    function () use ($app, $authMiddleware) {
        $app->get('/', 'App\Controller\Department\ListDepartments');
        $app->get('/{name}', 'App\Controller\Department\GetDepartment');
        $app->get('/{name}/deadlines', 'App\Controller\Department\ListDeadlines');
        $app->get('/{name}/announcements', 'App\Controller\Announcement\ListDepartmentAnnouncements');
    }
)->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

$app->group(
    '/deadline',
    function () use ($app, $authMiddleware) {
        /* GET
            /deadline/{id} */
        $app->get('/{id}', 'App\Controller\Deadline\GetDeadline');
        /* POST
            /deadline/{dept}?Deadline={date}&Note={text} */
        $app->post('/{dept}', 'App\Controller\Deadline\PostDeadline');
        /* PUT
            /deadline/{id}?[Department={dept}]&[Deadline={date}]&[Note={text}]*/
        $app->put('/{id}', 'App\Controller\Deadline\PutDeadline');
        /* DELETE
            /deadline/{id} */
        $app->delete('/{id}', 'App\Controller\Deadline\DeleteDeadline');
    }
)->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);


$app->group(
    '/permissions',
    function () use ($app, $authMiddleware) {
        $app->get('/resource/deadline/{department}[/{method}]', 'App\Controller\Permissions\DeadlineResource');
        $app->get('/method/deadline[/{method}[/{department}]]', 'App\Controller\Permissions\DeadlineMethod');
        $app->get('/resource/announcement/{department}[/{method}]', 'App\Controller\Permissions\AnnouncementResource');
        $app->get('/method/announcement[/{method}[/{department}]]', 'App\Controller\Permissions\AnnouncementMethod');
    }
)->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

$app->group(
    '/admin',
    function () use ($app, $authMiddleware) {
        $app->post('/SUDO/{name}', 'App\Controller\Member\SUDO');
        $app->get('/configuration[/{key}]', 'App\Controller\System\GetConfiguration');
        $app->put('/configuration', 'App\Controller\System\PutConfiguration');
    }
)->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);


$app->group(
    '/announcement',
    function () use ($app, $authMiddleware) {
        $app->get('/{id}', 'App\Controller\Announcement\GetAnnouncement');
        $app->put('/{dept}', 'App\Controller\Announcement\PutAnnouncement');
        $app->post('/{id}', 'App\Controller\Announcement\PostAnnouncement');
        $app->delete('/{id}', 'App\Controller\Announcement\DeleteAnnouncement');
    }
)->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

$app->group(
    '/cycle',
    function () use ($app, $authMiddleware) {
        $app->get('[/]', 'App\Controller\Cycle\ListCycles');
        $app->get('/{id}', 'App\Controller\Cycle\GetCycle');
        $app->put('/{id}', 'App\Controller\Cycle\PutCycle');
        $app->post('[/]', 'App\Controller\Cycle\PostCycle');
        $app->delete('/{id}', 'App\Controller\Cycle\DeleteCycle');
    }
)->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

$app->group(
    '/event',
    function () use ($app, $authMiddleware) {
        $app->get('[/]', 'App\Controller\Event\ListEvents');
        $app->get('/{id}', 'App\Controller\Event\GetEvent');
    }
)->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);
