<?php

/*.
    require_module 'standard';
    require_module 'json';
.*/

require_once(__DIR__."/database.inc");
require_once(__DIR__.'/users.inc');

if (!isset($ConComPositions)) {
    $ConComPositions = array();
    $sql = "SELECT * FROM ConComPositions;";
    $result = $db->run($sql);
    $value = $result->fetch();
    while ($value !== false) {
        $ConComPositions[$value['PositionID']] = $value['Name'];
        $value = $result->fetch();
    }
}


if (!isset($Departments)) {
    $Departments = array();
    $Divisions = array();

    $temp_dept = array();

    $sql = "SELECT * FROM Departments;";
    $result = $db->run($sql);
    $value = $result->fetch();
    while ($value !== false) {
        $temp_dept[$value['DepartmentID']] = ['name' => $value['Name'], 'parent' => $value['ParentDepartmentID']];
        $value = $result->fetch();
    }
    foreach ($temp_dept as $id => $dept) {
        $Departments[$dept['name']] = [];
        $Departments[$dept['name']]['Division'] = $temp_dept[$dept['parent']]['name'];
        $Departments[$dept['name']]['id'] = $id;
        $Departments[$dept['name']]['Email'] = [];

        if (in_array($Departments[$dept['name']]['Division'], $Divisions) == false) {
            array_push($Divisions, $Departments[$dept['name']]['Division']);
        }

        $sql = "SELECT * FROM EMails WHERE DepartmentID = ".$id.";";
        $result = $db->run($sql);
        $value = $result->fetch();
        while ($value !== false) {
            array_push($Departments[$dept['name']]['Email'], $value['EMail']);
            $value = $result->fetch();
        }
    }
    asort($Departments);
    asort($Divisions);
}


function getDivision($dep)
{
    return($GLOBALS['Departments'][$dep]['Division']);

}


function getDepartmentEmails($dep)
{
    return($GLOBALS['Departments'][$dep]['Email']);

}


function getPositionID($position)
{
    global $db;

    $sql = "SELECT PositionID FROM ConComPositions WHERE Name = '$position';";
    $result = $db->run($sql);
    $value = $result->fetch();
    if ($value === false) {
        return null;
    }
    return $value['PositionID'];

}


function AddConComPosition($accountid, $department, $position, $note, $notify = true)
{
    global $Departments, $db;

    $positionid = getPositionID($position);
    if ($positionid == null) {
        die("Failed to find position: $position");
    }

    /* Convention year... assume most recent one */
    $year = current_yearID();

    $data = lookup_user_by_id($accountid);
    if ($data['code']) {
        die("Failed to find user: $accountid");
    }
    $user = $data['users'][0];

    $sql = "SELECT ListRecordID FROM ConComList WHERE DepartmentID = ".$Departments[$department]['id']." AND AccountID = $accountid and YearID = $year;";
    $result = $db->run($sql);
    $value = $result->fetch();
    if ($value !== false) {
        // if the department is already assigned to this user, wipe it and replace it with the new info
        $record = $value['ListRecordID'];
        $sql = "UPDATE ConComList SET  PositionID = $positionid, Note = '$Note' WHERE ListRecordID = $record;";
    } else {
        $sql = "INSERT INTO ConComList (AccountID, DepartmentID, PositionID, YearID, Note) VALUES ($accountid, ".$Departments[$department]['id'].", $positionid, $year, '$note');";
    }
    $result = $db->run($sql);

    if ($notify && $result) {
        /* Notify the Division Director, so they know */
        $to = getDepartmentEmails(getDivision($department))[0];
        $preferredFirstName = search_definedFields('Preferred First Name');
        $preferredLastName = search_definedFields('Preferred Last Name');
        $subject = 'ConCom Division addition to '.$department;

        $name = $user['First Name']." ".$user['Last Name'];

        $message = $_SESSION['fullName'].' has added '.$name." to ".$department.' as '.$position.'.';
        $headers = 'From: noreply@convergence-con.org'."\r\n";
        $headers .= 'Reply-To: noreply@convergence-con.org';

        mail($to, $subject, $message, $headers);
        return true;
    } elseif (!$result) {
        die("Failed Add comcon member to database!");
    }

}


function RemoveConComPosition($accountid, $department, $position, $notify = true)
{
    global $Departments, $db;

    $positionid = getPositionID($position);
    if ($positionid == null) {
        return;
    }

    $sql = "DELETE FROM ConComList WHERE AccountID = $accountid AND DepartmentID = ".$Departments[$department]['id']." AND PositionID = $positionid";
    $result = $db->run($sql);
    if ($result) {
        if ($nodify) {
            // Notify the Division Director, so they know

            $data = lookup_user_by_id($accountid);
            if ($data['code']) {
                die("Failed to find user: $accountid");
            }
            $user = $data['users'][0];

            $to = getDepartmentEmails(getDivision($department))[0];
            $subject = 'ConCom Division Removal to '.$department;
            $name = $user['First Name']." ".$user['Last Name'];
            $message = $_SESSION['fullName'].' has removed '.$name." from ".$department.'.';
            $headers = 'From: noreply@convergence-con.org'."\r\n";
            $headers .= 'Reply-To: noreply@convergence-con.org';

            mail($to, $subject, $message, $headers);
        }
        return true;
    } else {
        sendError("Failed to apply remove to Database".__FILE__, $result);
    }

}


function ConComListBuildSearch()
{
    global $db;

    $year = current_yearID();
    $sql = <<<SQL
        SELECT l.AccountID, COALESCE(l.Note, "") AS Note,
              (SELECT Name FROM Departments WHERE DepartmentID = l.DepartmentID) AS Department,
              (SELECT (CASE WHEN DepartmentID = ParentDepartmentID THEN 1 ELSE 0 END) FROM Departments WHERE DepartmentID = l.DepartmentID ) AS Divisional,
              (SELECT Name FROM ConComPositions WHERE PositionID = l.PositionID)   AS Position,
              (SELECT Name FROM ConventionYear WHERE YearID = l.YearID) AS Year
          FROM ConComList AS l
          WHERE l.YearID = $year;
SQL;

    $ids = array();
    $result = $db->run($sql);
    $value = $result->fetch();
    $db_staff = array();
    while ($value !== false) {
        $Division = getDivision($value['Department']);
        if ($value['Divisional']) {
            if ($value['Position'] == "Head") {
                $value['Department'] = "Division Director";
                $value['Position'] = "Director";
            } elseif ($value['Position'] == "Specialist") {
                $value['Department'] = "Division Support";
            }
        }

        if (array_key_exists($value['AccountID'], $db_staff)) {
            $entry = array('Account ID' => $value['AccountID'],
                      'Division' => $Division,
                      'Department' => $value['Department'],
                      'Position' => $value['Position'],
                      'Email' => '',
                      'First Name' => $value['AccountID'],
                      'Last Name' => '',
                      'Note' => $value['Note']);
            array_push($entry, $db_staff[$value['AccountID']]);
        } else {
            $db_staff[$value['AccountID']] = [[
            'Account ID' => $value['AccountID'],
            'Division' => $Division,
            'Department' => $value['Department'],
            'Position' => $value['Position'],
            'Email' => '',
            'First Name' => $value['AccountID'],
            'Last Name' => '',
            'Note' => $value['Note'],
                  ]];
        }

        if (!in_array($value['AccountID'], $ids)) {
            $ids[] = $value['AccountID'];
        }
        $value = $result->fetch();
    }

    $str = "('".implode("', '", $ids)."')";
    $users = lookup_users_by_ids($str);

    foreach ($users['users'] as $user) {
        if (array_key_exists($user['Id'], $db_staff)) {
            foreach ($db_staff[$user['Id']] as $entry_key => $entry) {
                $db_staff[$user['Id']][$entry_key]['First Name'] = $user['First Name'];
                $db_staff[$user['Id']][$entry_key]['Last Name'] = $user['Last Name'];
                $db_staff[$user['Id']][$entry_key]['Email'] = $user['Email'];
            }
        }
    }

    $output = [];
    // Flatten our multi-dimentional array
    foreach ($db_staff as $staff) {
        foreach ($staff as $entry) {
            $output[] = $entry;
        }
    }

    return $output;

}


function ConComListBuild()
{
    $fullConComList = array();

    foreach ($GLOBALS['Departments'] as $kdep => $dep) {
        if ($dep['Division'] != $kdep) {
            $fullConComList[] = [
            'Division' => $dep['Division'],
            'Department' => $kdep,
            'Position' => 'Department',
            'First Name' => '',
            'Last Name' => '',
            'Email' => implode("<br />", $dep['Email']),
            'Note' => '',
            'Account ID' => '',
            ];
        }
    }

    $db_staff = ConComListBuildSearch();
    $tmp = array_merge_recursive($fullConComList, $db_staff);
    return($tmp);

}


function DumpConComList()
{
    global $Divisions, $Departments;
    $db_staff = ConComListBuildSearch();
    $email = array_unique(array_column($db_staff, 'Email'));

    foreach ($Departments as $kdep => $dep) {
        array_push($email, $dep['Email'][0]);
    }

    foreach ($Divisions as $div) {
        array_push($email, getDepartmentEmails($div)[0]);
    }

    $email = array_unique($email);
    asort($email);

    $email = implode(" ", $email);

    print $email;

}


function ConComListDisplay()
{
    global $Divisions, $ConComPositions;

    $list = ConComListBuild();
    // Put each entry into a divisional/department mapped array
    foreach ($list as $line) {
        $printList[$line['Division']][$line['Department']][$line['Position']][$line['First Name']][$line['Last Name']] = $line['Email'].'|'.$line['Note'].'|'.$line['Account ID'];
    }
  
    // Sort everything so we don't have to later
    ksort_recursive($printList);
  
    // Start the crazy print routine!
    echo "<div class='w3-responsive'>";
    echo "<table class='w3-table-all w3-hoverable'>\n";
    echo '<tr id="table_head" class="w3-purple">';
    echo '<th class="w3-center">Department</th>';
    echo '<th class="w3-center">Division</th>';
    echo '<th class="w3-center">First Name</th>';
    echo '<th class="w3-center">Last Name</th>';
    echo '<th class="w3-center">Position</th>';
    echo '<th class="w3-center">Email</th>';
    echo '<th class="w3-center" colspan=2>Note</th>';
    echo "</tr>\n";
    foreach ($Divisions as $div) {
        $currentDivDirector = false;
        echo '<tr id="division" class="w3-purple"><th id="'.$div.'" class="w3-center" colspan=2>'.$div;
        if ($div != 'Committees' && $div != 'Corporate Staff') {
            echo ' Division';
        }
        echo '</th><th class="w3-center" colspan=3>';
        echo '<div class="w3-dropdown-hover w3-purple">';
        echo '  Go To Section';
        echo '  <div class="w3-dropdown-content w3-purple">';
        echo '    <a href="#main_nav" class="w3-border">Top of Page</a>';
        foreach ($Divisions as $menuDiv) {
            // ##==## Likely need to add some translation here to blot out '&'
            echo '    <a href="#'.$menuDiv.'" class="w3-border">'.$menuDiv.'</a>';
        }
        echo '  </div>';
        echo '</div>';
        echo '</th><th colspan=3>';
        echo implode('<br />', getDepartmentEmails($div));
        echo "</th></tr>\n";
        // Division Directors
        if (isset($printList[$div]['Division Director'])) {
            foreach ($printList[$div]['Division Director'] as $pos) {
                foreach ($pos as $kfName => $fName) {
                    foreach ($fName as $klName => $lName) {
                        $tmp = explode('|', $lName);
                        echo '<tr><td>Division Director</td><td>'.$div.'</td><td>'.$kfName.'</td><td>'.$klName.'</td><td>Director</td><td>'.getDepartmentEmails($div)[0].'</td>';
                        if (isset($_SESSION['IS_ADMIN'])) {
                            echo '<td>';
                        } else {
                            echo '<td colspan=2>';
                        }
                        echo $tmp[1];
                        if ($_SESSION['accountId'] == $tmp[2]) {
                            if (!empty($tmp[1])) {
                                echo '<br />';
                            }
                            echo 'This is you!';
                            $currentDivDirector = true;
                            if (isset($_SESSION['DIR_OF'])) {
                                if (!in_array($div, $_SESSION['DIR_OF'])) {
                                    $_SESSION['DIR_OF'][] = $div;
                                }
                            } else {
                                $_SESSION['DIR_OF'][] = $div;
                            }
                        }
                        echo "</td>";
                        // System Admins can remove anyone
                        if (isset($_SESSION['IS_ADMIN'])) {
                            echo '<td class="w3-red"><a href="index.php?Function=list&Remove='.urlencode($tmp[2]).'&Department='.urlencode($div).'&Position=Director" onclick="return confirm(\'Are you sure you want to remove '.$kfName.' '.$klName.' from '.$div.'?\');">Remove</a></td>';
                        }
                        echo "</tr>\n";
                    }
                    unset($tmp);
                }
            }
            unset($pos);
        }
        //Division Support
        if (isset($printList[$div]['Division Support'])) {
            foreach ($printList[$div]['Division Support'] as $pos) {
                foreach ($pos as $kfName => $fName) {
                    foreach ($fName as $klName => $lName) {
                        $tmp = explode('|', $lName);
                        echo '<tr><td>Division Support</td><td>'.$div.'</td><td>'.$kfName.'</td><td>'.$klName.'</td><td>Specialist</td><td>'.$tmp[0].'</td>';
                        if (isset($_SESSION['IS_ADMIN']) || $currentDivDirector) {
                            echo '<td>';
                        } else {
                            echo '<td colspan=2>';
                        }
                        echo $tmp[1];
                        if ($_SESSION['accountId'] == $tmp[2]) {
                            if (!empty($tmp[1])) {
                                echo '<br />';
                            }
                            echo 'This is you!';
                        }
                        echo '</td>';
                        if (isset($_SESSION['IS_ADMIN']) || $currentDivDirector) {
                            echo '<td class="w3-red"><a href="index.php?Function=list&Remove='.urlencode($tmp[2]).'&Department='.urlencode($div).'&Position=Specialist" onclick="return confirm(\'Are you sure you want to remove '.$kfName.' '.$klName.' from '.$div.'?\');">Remove</a></td>';
                        }
                        echo "</tr>\n";
                    }
                    unset($tmp);
                }
            }
            unset($pos);
        }
        if ($div != 'Committees' && $div != 'Corporate Staff' && (isset($_SESSION['IS_ADMIN']) || $currentDivDirector)) {
            echo '<tr><td class="w3-center" colspan=8><a href="index.php?Function=list&AddDepartment='.$div.'">Add someone to '.$div.'</a></td></tr>'."\n";
        }
        // Departments
        foreach ($printList[$div] as $kdep => $dep) {
            $currentDepHead = false;
            if ($div != 'Corporate Staff' && $kdep != 'Division Director' && $kdep != 'Division Support') {
                echo '<tr id="department" class="w3-green">';
                if ($div == 'Committees') {
                    echo '<th id="'.$kdep.'" colspan=5>'.$kdep.'</th>';
                } else {
                    echo '<th id="'.$kdep.'">'.$kdep.'</th><th colspan=4>'.$div.'</th>';
                }
                echo '<th colspan=3>';
                echo implode('<br />', getDepartmentEmails($kdep));
                echo "</th></tr>\n";
            }
            if ($kdep != 'Division Director' && $kdep != 'Division Support') {
                foreach ($ConComPositions as $pos) {
                    if (isset($dep[$pos])) {
                        foreach ($dep[$pos] as $kfName => $fName) {
                            foreach ($fName as $klName => $lName) {
                                $tmp = explode('|', $lName);
                                echo '<tr>';
                                if ($div == 'Committees' || $div == 'Corporate Staff') {
                                    echo '<td colspan=2>'.$kdep.'</td>';
                                } else {
                                    echo '<td>'.$kdep.'</td>';
                                    echo '<td>'.$div.'</td>';
                                }
                                echo '<td>'.$kfName.'</td>';
                                if ($div == 'Corporate Staff' || $div == 'Committees') {
                                    echo '<td colspan=2>'.$klName.'</td>';
                                } else {
                                    echo '<td>'.$klName.'</td>';
                                    echo '<td>'.$pos.'</td>';
                                }
                                if ($div == 'Corporate Staff') {
                                    echo '<td>'.getDepartmentEmails($kdep)[0].'</td>';
                                } else {
                                    echo '<td>'.$tmp[0].'</td>';
                                }
                                if (isset($_SESSION['IS_ADMIN']) || $currentDivDirector || ($currentDepHead && $pos != 'Head')) {
                                    echo '<td>';
                                } else {
                                    echo '<td colspan=2>';
                                }
                                echo $tmp[1];

                                // Check to see if this is an entry for the logged in person
                                if ($_SESSION['accountId'] == $tmp[2]) {
                                    if (!empty($tmp[1])) {
                                        echo '<br />';
                                    }
                                    echo 'This is you!';
                                    // Check to see if this person is a head of a department
                                    if ($pos == 'Head') {
                                        $currentDepHead = true;
                                        if (isset($_SESSION['HEAD_OF'])) {
                                            if (!in_array($kdep, $_SESSION['HEAD_OF'])) {
                                                $_SESSION['HEAD_OF'][] = $kdep;
                                            }
                                        } else {
                                            $_SESSION['HEAD_OF'][] = $kdep;
                                        }
                                    }
                                }
                                echo '</td>';
                                if (isset($_SESSION['IS_ADMIN']) || $currentDivDirector || ($currentDepHead && $pos != "Head")) {
                                    echo '<td class="w3-red"><a href="index.php?Function=list&Remove='.urlencode($tmp[2]).'&Department='.urlencode($kdep).'&Position='.$pos.'" onclick="return confirm(\'Are you sure you want to remove '.$kfName.' '.$klName.' from '.$kdep.'?\');">Remove</a></td>';
                                }

                                echo "</tr>\n";
                            }
                            unset($tmp);
                        }
                    }
                }
            }
            if (($div != 'Corporate Staff' && $kdep != 'Division Director' && $kdep != 'Division Support') && (isset($_SESSION['IS_ADMIN']) || $currentDivDirector || ($currentDepHead && $pos != "Head"))) {
                echo '<tr><td class="w3-center" colspan=8><a href="index.php?Function=list&AddDepartment='.urlencode($kdep).'">Add someone to '.$kdep.'</a></td></tr>'."\n";
            }
        }
    }
    echo "</table>\n";
    echo "</div>";

}
