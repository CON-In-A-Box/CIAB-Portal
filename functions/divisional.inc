<?php

/*.
    require_module 'standard';
    require_module 'json';
.*/

require_once(__DIR__."/database.inc");
require_once(__DIR__.'/users.inc');

if (!isset($Departments)) {
    $Departments = array();
    $Divisions = array();

    $temp_dept = array();

    $sql = "SELECT * FROM Departments WHERE DepartmentID NOT IN (SELECT `DepartmentID` FROM `Departments` WHERE Name = 'Historical Placeholder')";
    $result = DB::run($sql);
    $value = $result->fetch();
    while ($value !== false) {
        $temp_dept[$value['DepartmentID']] = ['name' => $value['Name'], 'parent' => $value['ParentDepartmentID'], 'childCount' => 0];
        $value = $result->fetch();
    }
    foreach ($temp_dept as $id => $dept) {
        if ($dept['parent'] != $id) {
            $temp_dept[$dept['parent']]['childCount'] = $temp_dept[$dept['parent']]['childCount'] + 1;
        }
    }
    foreach ($temp_dept as $id => $dept) {
        $Departments[$dept['name']] = [];
        $Departments[$dept['name']]['Division'] = $temp_dept[$dept['parent']]['name'];
        $Departments[$dept['name']]['id'] = $id;
        $Departments[$dept['name']]['childCount'] = $dept['childCount'];
        $Departments[$dept['name']]['Email'] = [];

        if (in_array($Departments[$dept['name']]['Division'], $Divisions) == false) {
            array_push($Divisions, $Departments[$dept['name']]['Division']);
        }

        $sql = "SELECT * FROM EMails WHERE DepartmentID = ".$id.";";
        $result = DB::run($sql);
        $value = $result->fetch();
        while ($value !== false) {
            array_push($Departments[$dept['name']]['Email'], $value);
            $value = $result->fetch();
        }
    }
    asort($Departments);
    asort($Divisions);

    // Push non-divisions to the end of the list (force order)
    $endDivs = ['Board Staff', 'Committees', 'Corporate Staff'];
    $Divisions = array_diff($Divisions, $endDivs);
    $Divisions = array_merge($Divisions, $endDivs);
}
