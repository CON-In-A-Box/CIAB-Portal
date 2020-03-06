<?php
/*.
    require_module 'standard';
    require_module 'json';
.*/

require_once($BACKEND."/RBAC.inc");
require_once($FUNCTIONDIR.'/users.inc');
require_once(__DIR__."/../functions/concom.inc");


function getDepartmentName(/*.mixed.*/$id)
{
    global $Departments;

    $output = array();

    if (array_key_exists($id, $Departments)) {
        return $id;
    } else {
        foreach ($Departments as $key => $dept) {
            if ($dept['id'] == $id) {
                return $key;
            }
        }
    }
    return null;

}


function getPositionName(/*.mixed.*/$id)
{
    $sql = "SELECT `Name` FROM `ConComPositions` WHERE PositionID = $id";
    $result = \DB::run($sql);
    $value = $result->fetch();
    if ($value !== false) {
        return $value['Name'];
    }
    return null;

}


buildConComRBAC();

if (isset($_REQUEST['Remove'])) {
    $department = getDepartmentName($_REQUEST['Department']);
    $position = getPositionName($_REQUEST['Position']);
    $perm = "concom.modify.$department.$position";
    $perm2 = "concom.modify.$department.all";
    $perm3 = "concom.modify.all";
    if (\ciab\RBAC::havePermission($perm) ||
        \ciab\RBAC::havePermission($perm2) ||
        \ciab\RBAC::havePermission($perm3)) {
        // Retrieve and sanitize POST data
        $arguments = [
        'Remove'      => FILTER_SANITIZE_NUMBER_INT,
        'Department'  => FILTER_SANITIZE_SPECIAL_CHARS,
        'Position'    => FILTER_SANITIZE_SPECIAL_CHARS,
        ];
        if (!empty($_POST)) {
            $removeData = filter_input_array(INPUT_POST, $arguments);
        } else {
            $removeData = filter_input_array(INPUT_GET, $arguments);
        }
        RemoveConComPosition($removeData['Remove'], $removeData['Department'], $removeData['Position']);
        goSite('index.php?Function=concom#'.$removeData['Department']);
    } else {
        goSite('index.php?Function=concom');
    }
}


// if this is an update, but we don't have the right permissions, ignore it.
if (isset($_REQUEST['AddDepartment'])) {
    $department = getDepartmentName($_REQUEST['AddDepartment']);
    $perm = "concom.add.$department";
    $perm2 = "concom.add.all";
    if ((\ciab\RBAC::havePermission($perm) ||
         \ciab\RBAC::havePermission($perm2)) &&
        isset($_REQUEST['accountId']) && isset($_REQUEST['Position'])) {
        // apply change
        // Retrieve and sanitize POST data
        $arguments = [
        'accountId'      => FILTER_SANITIZE_NUMBER_INT,
        'AddDepartment'  => FILTER_SANITIZE_SPECIAL_CHARS,
        'Position'       => FILTER_SANITIZE_SPECIAL_CHARS,
        'note'           => FILTER_SANITIZE_SPECIAL_CHARS,
        ];
        if (!empty($_POST)) {
            $addData = filter_input_array(INPUT_POST, $arguments, true);
        } else {
            $addData = filter_input_array(INPUT_GET, $arguments, true);
        }

        AddConComPosition($addData['accountId'], $addData['AddDepartment'], $addData['Position'], $addData['note']);
        goSite('index.php?Function=concom#'.$addData['AddDepartment']);
    } else {
        goSite('index.php?Function=concom');
    }
}

if (!\ciab\RBAC::havePermission("concom.view") && empty($_SESSION['customFields']['currConComPos'])) {
    // This is for ConCom members only!  Silently return to main.
    goSite();
}

if (isset($_REQUEST['Modify'])) {
    $department = getDepartmentName($_REQUEST['Department']);
    $position = getPositionName($_REQUEST['Position']);
    $perm = "concom.modify.$department.$position";
    $perm2 = "concom.modify.$department.all";
    $perm3 = "concom.modify.all";
    if (\ciab\RBAC::havePermission($perm) ||
        \ciab\RBAC::havePermission($perm2) ||
        \ciab\RBAC::havePermission($perm3)) {
        // Retrieve and sanitize POST data
        $arguments = [
        'Modify'      => FILTER_SANITIZE_NUMBER_INT,
        'Department'  => FILTER_SANITIZE_SPECIAL_CHARS,
        'Position'    => FILTER_SANITIZE_SPECIAL_CHARS,
        'Note'        => FILTER_SANITIZE_SPECIAL_CHARS,
        ];
        $modifyData = filter_input_array(INPUT_POST, $arguments);
        ModifyConComPosition($modifyData['Modify'], $modifyData['Department'], $modifyData['Position'], $modifyData['Note']);
        goSite('index.php?Function=concom#'.$modifyData['Department']);
    } else {
        goSite('index.php?Function=concom');
    }
}
