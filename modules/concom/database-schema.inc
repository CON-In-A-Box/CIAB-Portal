<?php

/*.
    require_module 'standard';
.*/

namespace concom;

$concom_DB_tables = [
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

$concom_DB_foreignKeys = [
'ConComList' => [
'DepartmentID' => 'Departments (DepartmentID) ON DELETE RESTRICT ON UPDATE CASCADE',
'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
'PositionID' => 'ConComPositions (PositionID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],

];

$concom_DB_index = [
'ConComPermissions' => [
'Permission' => ['Permission']
    ]
];

\DB::addTables($concom_DB_tables);
\DB::addForeignKeys($concom_DB_foreignKeys);
\DB::addIndexes($concom_DB_index);
