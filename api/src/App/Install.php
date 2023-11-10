<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

require_once __DIR__.'/../../../modules/concom/functions/RBAC.inc';

use Atlas\Query\Select;

/* setupInstall */

function forEachModule($container, $action, $subaction = null)
{
    global $DISABLEDMODULES;

    $modules = scandir(__DIR__.'/../Modules');
    foreach ($modules as $key => $module) {
        if (!in_array($module, array('.', '..'))) {
            if (!empty($DISABLEDMODULES) && in_array($module, $DISABLEDMODULES)) {
                continue;
            }
            if (is_dir(__DIR__.'/../Modules/'.$module)) {
                if (is_file(__DIR__.'/../Modules/'.$module.'/Settings.php')) {
                    $action($container, $module, $subaction);
                }
            }
        }
    }

}


function forEachModuleController($container, $action)
{
    $funct  = function ($con, $module, $action) {
        $module_settings = include(__DIR__.'/../Modules/'.$module.'/Settings.php');
        if (array_key_exists('baseControllers', $module_settings)) {
            foreach ($module_settings['baseControllers'] as $base) {
                $action($con, $base);
            }
        }
    };

    forEachModule($container, $funct, $action);

}


function setupInstall($container): void
{
    $baseClasses = [
        'App\Controller\Cycle\BaseCycle',
        'App\Controller\Event\BaseEvent',
        'Vendor\Payment\BasePayment',
        'App\Controller\Announcement\BaseAnnouncement',
        'App\Controller\Deadline\BaseDeadline',
        'App\Controller\Department\BaseDepartment',
        'App\Controller\Member\BaseMember',
        'App\Controller\Permissions\GetMemberPermissions',
        'App\Controller\Permissions\BasePermission',
        'App\Controller\Stores\BaseStore',
        'App\Controller\Stores\BaseProduct',
        'App\Controller\System\BaseSystem',
    ];

    $rbac = [
    ];


    /**
     * Database Updates and such
     **/
    foreach ($baseClasses as $class) {
        if (method_exists($class, 'databaseInstall')) {
            $class::databaseInstall($container);
        }
    }

    forEachModule($container, function ($cont, $module) {
        $module_settings = include(__DIR__.'/../Modules/'.$module.'/Settings.php');
        if (method_exists($module_settings['module'], 'databaseInstall')) {
            call_user_func(array($module_settings['module'], 'databaseInstall'), $cont);
        }
    });


    /**
     * RBAC work
     **/
    $container->RBAC->install($container);

    foreach ($baseClasses as $class) {
        $result = $class::permissions($container->db);
        if (!empty($result)) {
            $rbac = array_unique(array_merge($rbac, $result));
        }
    }

    /*
     * A future data check so that with other platforms this will not need to be run on every execution
     *
    $data = Select::new($container->db)
        ->columns('Value')
        ->from('Configuration')
        ->whereEquals(['Field' => 'APIMD5'])
        ->fetchOne();
    if (!$data || $data['Value'] != md5(json_encode($rbac))) {
     */
    foreach ($rbac as $entry) {
        $container->RBAC->registerPermissions($entry);
    }

    /*
     * custom install/update work can be done here also,
     * again, in the future not run every time but only when needed
     */
    foreach ($baseClasses as $class) {
        $class::install($container);
    }

    forEachModuleController($container, function ($container, $base) {
        $result = $base::permissions($container->db);
        if (!empty($result)) {
            $container->RBAC->registerPermissions($result);
        }
        $base::install($container);
    });

    forEachModule($container, function ($cont, $module) {
        $module_settings = include(__DIR__.'/../Modules/'.$module.'/Settings.php');
        if (method_exists($module_settings['module'], 'install')) {
            call_user_func(array($module_settings['module'], 'install'), $cont);
        }
    });

}
