<?php

/*.
    require_module 'standard';
.*/

namespace concom;

$DB_tables = [
'ConComList' => [
'ListRecordID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
'AccountID' => 'INT UNSIGNED NOT NULL', // Taken from NeonCRM Currently
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

$DB_foreignKeys = [
'ConComList' => [
'DepartmentID' => 'Departments (DepartmentID) ON DELETE RESTRICT ON UPDATE CASCADE',
'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
'PositionID' => 'ConComPositions (PositionID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],

];

$DB_index = [
'ConComPermissions' => ['Permission'],
];

$MODULE_TABLES = array_merge($MODULE_TABLES, $DB_tables);
$MODULE_FOREIGNKEYS = array_merge($MODULE_FOREIGNKEYS, $DB_foreignKeys);
$MODULE_INDEX = array_merge($MODULE_INDEX, $DB_index);
