<?php
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
    echo '<h3>Add someone to '.$department.'</h3>';

    // Retrieve and sanitize POST data
    $arguments = [
    'firstName' => FILTER_SANITIZE_SPECIAL_CHARS,
    'lastName'  => FILTER_SANITIZE_SPECIAL_CHARS,
    'email'     => FILTER_SANITIZE_EMAIL,
    ];
    if (!empty($_POST)) {
        $searchCriteria = filter_input_array(INPUT_POST, $arguments);
    } else {
        $searchCriteria = filter_input_array(INPUT_GET, $arguments);
    }
    // If there are search criteria present, execute the search query
    if (!empty($searchCriteria['firstName']) ||
        !empty($searchCriteria['lastName']) ||
        !empty($searchCriteria['email'])) {
        $result = lookup_users_by_name_email(
            $searchCriteria['firstName'],
            $searchCriteria['lastName'],
            $searchCriteria['email'],
            ['City', 'State']
        );

        $message = '<h3>No results match your search.</h3>';
    } else {
        $result = null;
        $message = '<h3>You must specify at least one of the above search criteria.</h3>';
    }

    echo '<form action="index.php?Function=concom" method="POST" class="form-inline">';
    echo '<input type="hidden" name="AddDepartment" value="'.$department.'">'."\n";
    echo '<fieldset><legend>Search the User Database</legend>';
    echo '<p>';
    echo 'First Name';
    echo '<input type="text" class="form-control" name="firstName" value="'.htmlentities($searchCriteria['firstName']).'"> ';
    echo 'Last Name';
    echo '<input type="text" class="form-control" name="lastName" value="'.htmlentities($searchCriteria['lastName']).'"> ';
    echo 'Email';
    echo '<input type="text" class="form-control" name="email" value="'.htmlentities($searchCriteria['email']).'"> ';
    echo "</p>\n";
    echo '<input type="submit" value="Search" class="btn btn-default">';
    echo '</fieldset>';
    echo "</form>\n";
    echo '<hr>';

    if ($result && !$result['code']) {
        echo '<table class="w3-table w3-striped w3-bordered">';
        echo '<tr><th>Name</th><th>Email</th><th>Location</th></tr>'."\n";
        foreach ($result['users'] as $r) {
            echo '<tr>';
            echo '<td><a href="index.php?Function=concom&AddDepartment='.$department.'&accountId='.$r['Id'].'&Name='.$r['First Name'].' '.$r['Last Name'].'">'.$r['First Name'].' '.$r['Last Name'].'</a></td>';
            echo '<td>'.$r['Email'].'</td>';
            echo '<td>'.$r['City'].' '.$r['State'].'</td>';
            echo "</tr>\n";
        }
        echo "</table>\n";
    } else {
        echo '<p>'.$message.'</p>';
    }

}


function doConComAddAssign($department, $accountid, $name)
{
    echo '<h3>Add '.$name.' to '.$department.'</h3>';
    echo '<form action="index.php?Function=concom" method="POST" class="form-inline">';
    echo '<input type="hidden" name="AddDepartment" value="'.$department.'">'."\n";
    echo '<input type="hidden" name="accountId" value="'.$accountid.'">'."\n";
    echo '<fieldset><legend>Assign '.$name.' to ConCom</legend>';
    echo '<p>';
    echo 'Position';
    echo '<select name="Position">';
    if (getDivision($department) == $department) {
        if (isset($_SESSION['IS_ADMIN'])) {
            echo '<option value="Director">Division Director</option>';
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
    echo '<input type="submit" value="Assign to ConCom" class="btn btn-default">';
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
