<?php

/*.
    require_module 'standard';
.*/

namespace emailer;

$DB_tables = [
'EmailLists' => [
'EmailListID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
'Name' => 'VARCHAR(100) NOT NULL',
'Description' => 'TEXT NOT NULL',
'Code' => 'TEXT',
],
'EmailListAccess' => [
'DepartmentID' => 'INT UNSIGNED NOT NULL',
'PositionID' => 'INT UNSIGNED NOT NULL',
'EmailListID' => 'INT UNSIGNED NOT NULL',
'EditList' => 'BOOLEAN NOT NULL',
'ChangeAccess' => 'BOOLEAN NOT NULL',
]

];

$DB_primaryKeys = [
'EmailListAccess' => ['DepartmentID', 'PositionID', 'EmailListID']

];

\DB::addTables($DB_tables);
\DB::addPrimaryKeys($DB_primaryKeys);
