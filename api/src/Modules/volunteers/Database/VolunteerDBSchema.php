<?php

/*.
    require_module 'standard';
.*/

namespace App\Modules\volunteers\Database;

class VolunteerDBSchema extends \App\Core\ModuleDBSchema
{

    private $tables = [
    'HourRedemptions' => [
    'ClaimID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
    'AccountID' => 'INT UNSIGNED NOT NULL',
    'EventID' => 'INT UNSIGNED NOT NULL',
    'PrizeID' => 'INT UNSIGNED NOT NULL',
    'test2' => 'INT UNSIGNED',
        ],
    'RewardGroup' => [
    'RewardGroupID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
    'RedeemLimit' => 'INT UNSIGNED',
    'Name' => 'VARCHAR(50)'
        ],
    'VolunteerHours' => [
    'HourEntryID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
    'AccountID' => 'INT UNSIGNED NOT NULL',
    'ActualHours' => 'FLOAT(5,3) NOT NULL',
    'AuthorizedByID' => 'INT UNSIGNED NOT NULL',
    'DepartmentID' => 'INT UNSIGNED NOT NULL',
    'EndDateTime' => 'DATETIME NOT NULL',
    'EnteredByID' => 'INT UNSIGNED NOT NULL',
    'EventID' => 'INT UNSIGNED NOT NULL',
    'TimeModifier' => 'FLOAT(2,1) NOT NULL',
        ],
    'VolunteerRewards' => [
    'PrizeID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
    'Name' => 'VARCHAR(50) NOT NULL',
    'Promo' => 'BOOLEAN',
    'Retired' => 'BOOLEAN',
    'RewardGroupID' => 'INT UNSIGNED',
    'TotalInventory' => 'INT NOT NULL',
    'Value' => 'DECIMAL(5,2) NOT NULL',
        ]

    ];

    private $foreignKeys = [
    'VolunteerHours' => [
    'DepartmentID' => 'Departments (DepartmentID) ON DELETE RESTRICT ON UPDATE CASCADE',
    'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],
    'VolunteerRewards' => [
    'RewardGroupID' => 'RewardGroup (RewardGroupID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],
    'HourRedemptions' => [
    'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
    'PrizeID' => 'VolunteerRewards (PrizeID) ON DELETE RESTRICT ON UPDATE CASCADE',
    ]

    ];


    public function __construct($database)
    {
        parent::__construct('vol', $database, $this->tables, $this->foreignKeys);

    }


    /* end */
}
