<?php

/*.
    require_module 'standard';
.*/

require_once(__DIR__.'/functions.inc');
require_once(__DIR__.'/session.inc');
if (isset($GLOBALS['NEONID']) && isset($GLOBALS['NEONKEY'])) {
    require_once(__DIR__.'/neon.inc');
}


function _update_DB_Version($target)
{
    global $db;
    $sql = <<<SQL
        UPDATE Configuration
        SET Value = '$target'
        WHERE Field = 'DBSchemaVersion';
SQL;
    $db->run($sql);
    echo "<br><hr>";
    echo "<h3>Database Schema updated to: $target</h3>";
    echo "<h3>Done!</h3>";

}


function loadDefinedFields()
{
    global $_SESSION, $Neon, $ADMINACCOUNTS;

    // Custom Field Data for Session - Parse it to an easy to use array
    $request = [
    'method' => 'common/listCustomFields',
    'parameters' => [
    'searchCriteria.component' => "Account",
        ],
        ];
    $result = $Neon->go($request);
    if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
        $_SESSION['definedFields']['customField'] = $result['customFields']['customField'];
        foreach ($result['customFields']['customField'] as $val) {
            $_SESSION['definedFields'][$val['fieldId']] = $val['fieldName'];
            if (isset($val['fieldOptions'])) {
                foreach ($val['fieldOptions']['fieldOption'] as $fval) {
                    $_SESSION['definedFields'][$fval['id']] = $fval['name'];
                }
                unset($fval);
                if ($val['fieldName'] == "Official Meeting Attendance") {
                    foreach ($val['fieldOptions']['fieldOption'] as $fval) {
                        $_SESSION['definedFields']['Meetings'][$fval['id']] = $fval['name'];
                    }
                    unset($fval);
                } elseif ($val['fieldName'] == "Lottery Status") {
                    foreach ($val['fieldOptions']['fieldOption'] as $fval) {
                        $_SESSION['definedFields']['Lottery Status'][$fval['id']] = $fval['name'];
                    }
                    unset($fval);
                } elseif ($val['fieldName'] == "Bed Preference") {
                    foreach ($val['fieldOptions']['fieldOption'] as $fval) {
                        $_SESSION['definedFields']['Bed Preference'][$fval['id']] = $fval['name'];
                    }
                    unset($fval);
                } elseif ($val['fieldName'] == "Hotel Qualifier") {
                    foreach ($val['fieldOptions']['fieldOption'] as $fval) {
                        $_SESSION['definedFields']['Hotel Qualifier'][$fval['id']] = $fval['name'];
                    }
                    unset($fval);
                }
            }
        }
        unset($val);
    } else {
        die("Impossible error during Defined Custom Fields Download");
    }

    // Sort everything so displays come out correct always
    arsort($_SESSION['definedFields']['Meetings']);

}


function db_do_update($from, $to)
{
    if (isset($GLOBALS['Neon'])) {
        loadDefinedFields();
        if ($from < 2018011700) {
            require_once(__DIR__."/update/from_neon_to_152.inc");
            from_neon_to_152();
            _update_DB_Version(2018011700);
        }
        if ($from < 2018091700) {
            require_once(__DIR__."/update/to_180501.inc");
            to_180501();
            _update_DB_Version(2018091700);
        }
        if ($from < 2018092400) {
            require_once(__DIR__."/update/to_180924.inc");
            to_180924();
            _update_DB_Version(2018092400);
        }
    }
    _update_DB_Version($to);

}
