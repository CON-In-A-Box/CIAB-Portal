<?php declare(strict_types=1);

use Slim\App;
use App\Modules\staff\Service\StaffService;
use App\Modules\staff\Repository\StaffRepository;

/* setupStaffAPIDependencies */

function setupStaffAPIDependencies(App $app, array $settings)
{
    $container = $app->getContainer();

    $container['StaffService'] = function ($c): StaffService {
        return new StaffService(
            $c->get('EventService'),
            $c->get('DepartmentService'),
            $c->get('MemberService'),
            new StaffRepository($c['db'])
        );
    };

}
