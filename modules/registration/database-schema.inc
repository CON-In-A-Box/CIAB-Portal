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

\DB::addTables($DB_tables);
