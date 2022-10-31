<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

function setupRegistrationAPI($app, $authMiddleware)
{
    $app->get('/registration/open', 'App\Modules\registration\Controller\GetOpen');

    $app->group(
        '/registration',
        function () use ($app, $authMiddleware) {
            $app->get('/admin', 'App\Modules\registration\Controller\IsAdmin');
            $app->group(
                '/configuration',
                function () use ($app, $authMiddleware) {
                    $app->get('[/{key}]', 'App\Modules\registration\Controller\GetConfiguration');
                    $app->put('', 'App\Modules\registration\Controller\PutConfiguration');
                }
            )->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);
            $app->group(
                '/ticket',
                function () use ($app, $authMiddleware) {
                    $app->get('/list[/{member}]', 'App\Modules\registration\Controller\Ticket\ListTickets');
                    $app->get('/type[/{id}]', 'App\Modules\registration\Controller\Ticket\GetTicketTypes');
                    $app->put('/printqueue/claim/{id}', 'App\Modules\registration\Controller\Ticket\PrintQueueClaim');
                    $app->get('/printqueue', 'App\Modules\registration\Controller\Ticket\PrintQueue');
                    $app->get('/{id}', 'App\Modules\registration\Controller\Ticket\GetTicket');
                    $app->put('/{id}/checkin', 'App\Modules\registration\Controller\Ticket\CheckinTicket');
                    $app->put('/{id}/lost', 'App\Modules\registration\Controller\Ticket\LostTicket');
                    $app->put('/{id}/pickup', 'App\Modules\registration\Controller\Ticket\PickupTicket');
                    $app->put('/{id}/email', 'App\Modules\registration\Controller\Ticket\EmailTicket');
                    $app->put('/{id}/print', 'App\Modules\registration\Controller\Ticket\PrintBadge');

                    $app->put('/{id}', 'App\Modules\registration\Controller\Ticket\PutTicket');
                    $app->post('[/]', 'App\Modules\registration\Controller\Ticket\PostTicket');
                    $app->put('/{id}/void', 'App\Modules\registration\Controller\Ticket\VoidTicket');
                    $app->put('/{id}/reinstate', 'App\Modules\registration\Controller\Ticket\UnvoidTicket');
                    $app->delete('/{id}', 'App\Modules\registration\Controller\Ticket\DeleteTicket');
                }
            )->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);
        }
    )->add(new App\Middleware\CiabMiddleware($app))->add($authMiddleware);

}
