<?php
/*.
    require_module 'standard';
    require_module 'json';
.*/

require_once($BACKEND."/RBAC.inc");
require_once($FUNCTIONDIR.'/users.inc');
require_once(__DIR__."/../functions/concom.inc");

buildConComRBAC();

if (isset($_REQUEST['Remove'])) {
    $perm = "concom.modify.".$_REQUEST['Department'].".".$_REQUEST['Position'];
    if (\ciab\RBAC::havePermission($perm)) {
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
    if (\ciab\RBAC::havePermission($perm) && isset($_REQUEST['accountId']) && isset($_REQUEST['Position'])) {
        $perm = "concom.add.".$_REQUEST['AddDepartment'];
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
