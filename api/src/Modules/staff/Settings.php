<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

return [
'setupRoutes' => 'setupStaffAPI',
'setupDependencies' => 'setupStaffAPIDependencies',
'module' => 'App\\Modules\\staff\\ModuleStaff',
'baseControllers' => [
    'App\Modules\staff\Controller\BaseStaff'
],
];
