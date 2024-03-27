<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

use Psr\Container\ContainerInterface;
use App\Handler\ApiError;
use App\Handler\RBAC;
use App\Repository\DepartmentRepository;
use App\Repository\EmailListAccessRepository;
use App\Repository\EmailRepository;
use App\Repository\EventRepository;
use App\Repository\MemberRepository;
use App\Repository\PermissionRepository;
use App\Service\DepartmentService;
use App\Service\EmailListAccessService;
use App\Service\EmailService;
use App\Service\EventService;
use App\Service\MemberService;
use App\Service\PermissionService;
use Slim\App;

/* setupAPIDependencies */


function setupAPIDependencies(App $app, array $settings)
{
    $container = $app->getContainer();

    $container['errorHandler'] = function (): ApiError {
        return new ApiError;
    };

    $container['db'] = function ($c) {
        $settings = $c->get('settings')['db'];
        $pdo = new PDO(
            "mysql:host=".$settings['host'].";dbname=".$settings['dbname'],
            $settings['user'],
            $settings['pass']
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    };

    $container['RBAC'] = function (): RBAC {
        return new RBAC;
    };

    $container['EmailService'] = function ($c): EmailService {
        return new EmailService(
            new EmailRepository($c['db'])
        );
    };

    $container['EmailListAccessService'] = function ($c): EmailListAccessService {
        return new EmailListAccessService(
            new EmailListAccessRepository($c['db'])
        );
    };

    $container['PermissionService'] = function ($c): PermissionService {
        return new PermissionService(
            new PermissionRepository($c['db'])
        );
    };

    $container['DepartmentService'] = function ($c): DepartmentService {
        return new DepartmentService(
            $c->get('EmailService'),
            $c->get('EmailListAccessService'),
            $c->get('PermissionService'),
            new DepartmentRepository($c['db'])
        );
    };

    $container['EventService'] = function ($c): EventService {
        return new EventService(
            new EventRepository($c['db'])
        );
    };

    $container['MemberService'] = function ($c): MemberService {
        return new MemberService(
            new MemberRepository($c['db'])
        );
    };

}
