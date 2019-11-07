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


function doConComAddSearch($department)
{
    echo '<h3>Add someone to '.htmlspecialchars($department)."</h3>\n";
?>
    <fieldset><legend>Search the User Database</legend>
    <div class='UI-rest UI-center' id='userLookup_div'> </div>
    </fieldset>
<?php

}


function doConComAddAssign($department, $accountid)
{
    $user = lookup_user_by_id($accountid);
    $name = $user['users'][0]['First Name'].' '.$user['users'][0]['Last Name'];
    echo '<h3>Add '.htmlspecialchars($name).' to '.htmlspecialchars($department).'</h3>';
    echo '<form action="index.php?Function=concom" method="POST" class="form-inline">';
    echo '<input type="hidden" name="AddDepartment" value="'.htmlspecialchars($department).'">'."\n";
    echo '<input type="hidden" name="accountId" value="'.htmlspecialchars($accountid).'">'."\n";
    echo '<fieldset><legend>Assign '.$name.' to ConCom</legend>';
    echo '<p>';
    echo 'Position';
    echo '<select name="Position">';
    if (getDivision($department) == $department) {
        if (isset($_SESSION['IS_ADMIN'])) {
            echo '<option value="Head">Division Director</option>';
        }
        echo '<option value="Specialist">Divisional Support</option>';
    } else {
        if (isset($_SESSION['IS_ADMIN']) || isset($_SESSION['DIR_OF'])) {
            echo '<option value="Head">Head</option>';
        }
        echo '<option value="Sub-Head" selected>Sub-Head</option>';
        echo '<option value="Specialist">Specialist</option>';
    }
    echo "</select>\n";
    echo 'Note';
    echo '<input type=text name="note"></input>';
    echo "</p>\n";
    echo '<input type="submit" value="Assign to ConCom" class="UI-eventbutton">';
    echo '</fieldset>';
    echo "</form>\n";

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
