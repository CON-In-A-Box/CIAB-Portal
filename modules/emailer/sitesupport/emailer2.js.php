<?php
header("content-type: application/x-javascript");
require_once(__DIR__."/../../../functions/functions.inc");
require_once(__DIR__."/../../../functions/divisional.inc");
?>
/*
 * Javacript for the Emailer
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals confirmbox, showSidebar */

function buildDepartmentList(row) {
    var output = '<select class="UI-select" id="department_'+row+'" onchange="changeDepartment('+row+')">';
<?php
foreach ($Departments as $dep => $set) {
    echo '    output += \'<option value="'.$set['id'].'"';
    echo '>'.$dep."</option>';\n";
}
?>
    output += '</select>';
    return output;
}

function buildPositionList(row) {
    var output = '<select class="UI-select" id="position_'+row+'" onchange="changePosition('+row+')">';
<?php
if (class_exists('\\concom\\POSITION') && method_exists('\\concom\\POSITION', 'listConComPositions')) {
    $positions = \concom\POSITION::listConComPositions();
    foreach ($positions as $idx=>$pos) {
        echo '    output += \'<option value="'.$pos['PositionID'].'"';
        echo '>'.$pos['Name']."</option>';\n";
    }
}
?>
    output += '</select>';
    return output;
}
