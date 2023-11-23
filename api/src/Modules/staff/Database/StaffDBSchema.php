<?php

/*.
    require_module 'standard';
.*/

namespace App\Modules\staff\Database;

class StaffDBSchema extends \App\Core\ModuleDBSchema
{

    private $tables = [
        'ConComList' => [
            'ListRecordID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'AccountID' => 'INT UNSIGNED NOT NULL',
            'DepartmentID' => 'INT UNSIGNED NOT NULL',
            'EventID' => 'INT UNSIGNED NOT NULL',
            'Note' => 'VARCHAR(100)',
            'PositionID' => 'INT UNSIGNED NOT NULL',
        ],
        'ConComPositions' => [
            'PositionID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'Name' => 'VARCHAR(50) NOT NULL',
        ],
        'ConComPermissions' => [
            'PermissionID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'Position' => 'VARCHAR(100) NOT NULL',
            'Permission' => 'VARCHAR(100) NOT NULL',
            'Note' => 'TEXT'
        ]
    ];

    private $foreignKeys = [
        'ConComList' => [
            'DepartmentID' => 'Departments (DepartmentID) ON DELETE RESTRICT ON UPDATE CASCADE',
            'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
            'PositionID' => 'ConComPositions (PositionID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],
    ];

    private $index = [
        'ConComPermissions' => [
            'Permission' => ['Permission']
        ]
    ];

    private $seed = [
    'ConComPositions' => [
        ['index' => ['PositionID' => 1], 'data' => ['Name' => 'Head']],
        ['index' => ['PositionID' => 2], 'data' => ['Name' => 'Sub-Head']],
        ['index' => ['PositionID' => 3], 'data' => ['Name' => 'Specialist']]
    ]];


    public function __construct($database)
    {
        parent::__construct('staff', $database, $this->tables, $this->foreignKeys, null, $this->index, $this->seed);

    }


    /* end */
}
