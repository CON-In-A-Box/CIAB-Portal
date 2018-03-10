<?php

require_once(__DIR__."/../functions/functions.inc");
require_once($FUNCTIONDIR."/unit.inc");

$module_tests = [];

$modules = scandir($MODULESDIR);
foreach ($modules as $key => $value) {
    if (!in_array($value, array(".", ".."))) {
        if (is_dir($MODULESDIR.DIRECTORY_SEPARATOR.$value)) {
            if (is_file($MODULESDIR.DIRECTORY_SEPARATOR.$value.DIRECTORY_SEPARATOR.'test/test.inc')) {
                require_once($MODULESDIR.DIRECTORY_SEPARATOR.$value.DIRECTORY_SEPARATOR.'test/test.inc');
            }
        }
    }
}

foreach ($module_tests as $test) {
    print 'Testing '.$test."\n";
    $test();
}

report();
