<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

return [
'setupRoutes' => 'setupVolunteersAPI',
'module' => 'App\\Modules\\volunteers\\ModuleVolunteers',
'baseControllers' => [
    'App\Modules\volunteers\Controller\Rewards\BaseReward',
    'App\Modules\volunteers\Controller\Rewards\BaseRewardGroup',
    'App\Modules\volunteers\Controller\Hours\BaseHours',
    'App\Modules\volunteers\Controller\Claims\BaseClaims'
]
];
