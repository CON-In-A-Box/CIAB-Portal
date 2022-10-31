<?php

/*.
    require_module 'standard';
.*/

namespace announcements;

$DB_tables = [
'Announcements' => [
'AnnouncementID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
'DepartmentID' => 'INT UNSIGNED NOT NULL',
'PostedBy' => 'INT UNSIGNED NOT NULL',
'PostedOn' => 'DATE NOT NULL',
'Scope' => 'INT UNSIGNED NOT NULL',
'Text' => 'TEXT NOT NULL'
    ]

];

$DB_foreignKeys = [
'Announcements' => [
'DepartmentID' => 'Departments (DepartmentID) ON DELETE RESTRICT ON UPDATE CASCADE',
'PostedBy' => 'Members (AccountID) ON DELETE RESTRICT ON UPDATE CASCADE',
    ]

];

\DB::addTables($DB_tables);
\DB::addForeignKeys($DB_foreignKeys);
