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
'Note' => 'TEXT NOT NULL',
'Scope' => 'INT UNSIGNED',
'PostedBy' => 'INT UNSIGNED'
    ]

];

$DB_foreignKeys = [
'Deadlines' => [
'DepartmentID' => 'Departments (DepartmentID) ON DELETE RESTRICT ON UPDATE CASCADE',
    ]

];

\DB::addTables($DB_tables);
\DB::addForeignKeys($DB_foreignKeys);
