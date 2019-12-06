<?php
/*.
    require_module 'standard';
    require_module 'json';
.*/

if (!isset($_SESSION['IS_ADMIN']) && empty($_SESSION['customFields']['currConComPos'])) {
    // This is for ConCom members only!  Silently return to main.
    goSite();
}

require_once(__dir__."/../functions/concom.inc");
require_once($FUNCTIONDIR.'/users.inc');

if (isset($_REQUEST['Remove']) && isset($_REQUEST['Department']) && isset($_REQUEST['Position'])) {
    if (isset($_SESSION['IS_ADMIN']) || (isset($_SESSION['DIR_OF']) && in_array(getDivision($_REQUEST['Department']), $_SESSION['DIR_OF'])) || (isset($_SESSION['HEAD_OF']) && in_array($_REQUEST['Department'], $_SESSION['HEAD_OF']))) {
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
      // Made a request without the right cedentials - Reset just in case
        goSite();
    }
}


  // if this is an update, but we don't have the right permissions, ignore it.
if (isset($_REQUEST['AddDepartment'])) {
    if (isset($_SESSION['IS_ADMIN']) || (isset($_SESSION['DIR_OF']) && in_array(getDivision($_REQUEST['AddDepartment']), $_SESSION['DIR_OF'])) || (isset($_SESSION['HEAD_OF']) && in_array($_REQUEST['AddDepartment'], $_SESSION['HEAD_OF']))) {
        if (isset($_REQUEST['accountId'])) {
            if (isset($_REQUEST['Position'])) {
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
            }
        }
    } else {
      // Made a request without the right cedentials - Reset just in case
        goSite();
    }
}
