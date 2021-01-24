<?php

/*.
    require_module 'standard';
.*/

namespace registration;

$DB_tables = [

'Registration_Configuration' => [
'Field' => 'VARCHAR(30) NOT NULL PRIMARY KEY',
'Value' => 'TEXT NOT NULL'
    ]

];

$MODULE_TABLES = array_merge($MODULE_TABLES, $DB_tables);
