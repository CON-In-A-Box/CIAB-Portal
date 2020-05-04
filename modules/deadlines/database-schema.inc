<?php

/*.
    require_module 'standard';
.*/

namespace deadlines;

$DB_tables = [
'Deadlines' => [
'DeadlineID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
'DepartmentID' => 'INT UNSIGNED NOT NULL',
'Deadline' => 'DATE NOT NULL',
'Note' => 'TEXT NOT NULL'
    ]

];

$DB_foreignKeys = [
'Deadlines' => [
'DepartmentID' => 'Departments (DepartmentID) ON DELETE RESTRICT ON UPDATE CASCADE',
    ]

];

$MODULE_TABLES = array_merge($MODULE_TABLES, $DB_tables);
$MODULE_FOREIGNKEYS = array_merge($MODULE_FOREIGNKEYS, $DB_foreignKeys);
