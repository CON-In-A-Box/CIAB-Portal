<?php

namespace concom\tests;

require_once($FUNCTIONDIR."/divisional.inc");
require_once($FUNCTIONDIR."/unit.inc");


function validate_divisional()
{
    global $ConComPositions, $Departments, $Divisions;
    unit_ok(count($ConComPositions) > 0, "No ConCom position loaded");
    unit_ok(count($Departments) > 0, "No Departments loaded");
    unit_ok(count($Divisions) > 0, "No Divisions loaded");
    foreach ($Departments as $key => $dept) {
        unit_ok(array_key_exists('Division', $dept), "No 'Division' in ".$key);
        unit_ok(array_key_exists('Email', $dept), "No 'Email' in ".$key);
    }

}

$module_tests[] = 'concom\tests\validate_divisional';
