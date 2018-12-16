<?php

require_once($FUNCTIONDIR.'/divisional.inc');
require_once($FUNCTIONDIR."/database.inc");
require_once($FUNCTIONDIR.'/users.inc');
require_once(__DIR__.'/VOLUNTEERS.inc');
require_once(__DIR__.'/POSITION.inc');
require_once(__DIR__.'/REGISTRATION.inc');
require_once(__DIR__.'/LIST.inc');


function getDivision($dep)
{
    return($GLOBALS['Departments'][$dep]['Division']);

}


function getDepartmentEmails($dep)
{
    $result = [];
    if ($GLOBALS['Departments'][$dep]['Email']) {
        foreach ($GLOBALS['Departments'][$dep]['Email'] as $email) {
            array_push($result, $email['EMail']);
        }
    }
    return $result;

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


function printTableHeader($section, $long = true)
{
    echo "<tr id=\"table_head_$section\" class=\"event-color-secondary w3-small\">";
    if ($long) {
        echo '<th class="">Department</th>';
        echo '<th class="">Division</th>';
    } else {
        echo '<th class="" colspan=2>Department</th>';
    }
    echo '<th class="">First Name</th>';
    if ($long) {
        echo '<th class="">Last Name</th>';
        echo '<th class="">Position</th>';
    } else {
        echo '<th class="" colspan=2>Last Name</th>';
    }
    echo '<th class="">Email</th>';
    echo '<th class="" colspan=2>Note</th>';
    echo "</tr>\n";

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
    echo "<table class='w3-table-all w3-hoverable'>\n";
    foreach ($Divisions as $div) {
        $currentDivDirector = false;
        echo '<tr id="division" class="event-color-secondary"><th id="'.$div.'" class="w3-center" colspan=2>'.$div;
        if ($div != 'Committees' && $div != 'Corporate Staff' && $div != 'CFO Staff') {
            echo ' Division';
        }
        echo '</th><th class="w3-center" colspan=3>';
        echo '<div class="w3-dropdown-hover event-color-secondary">';
        echo '  Go To Section';
        echo '  <div class="w3-dropdown-content event-color-secondary">';
        echo '    <a href="#main_nav" class="w3-border">Top of Page</a><br />';
        foreach ($Divisions as $menuDiv) {
            // ##==## Likely need to add some translation here to blot out '&'
            echo '    <a href="#'.$menuDiv.'" class="w3-border">'.$menuDiv.'</a><br />';
        }
        echo '  </div>';
        echo '</div>';
        echo '</th><th colspan=3>';
        echo implode('<br />', getDepartmentEmails($div));
        echo "</th></tr>\n";
        if ($div != 'Corporate Staff' && $div != 'Committees') {
            printTableHeader($div);
        }
        // Division Directors
        if (isset($printList[$div]['Division Director'])) {
            foreach ($printList[$div]['Division Director'] as $pos) {
                foreach ($pos as $kfName => $fName) {
                    foreach ($fName as $klName => $lName) {
                        $tmp = explode('|', $lName);
                        if ($div != "CFO Staff") {
                            echo '<tr><td>Division Director</td><td>'.$div.'</td>';
                        } else {
                            echo '<tr><td colspan=2>CFO</td>';
                        }
                        echo '<td>'.$kfName.'</td><td>'.$klName.'</td><td>Director</td><td>'.getDepartmentEmails($div)[0].'</td>';
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
                            echo '<td class="w3-red"><a href="index.php?Function=concom&Remove='.urlencode($tmp[2]).'&Department='.urlencode($div).'&Position=Director" onclick="return confirm(\'Are you sure you want to remove '.$kfName.' '.$klName.' from '.$div.'?\');">Remove</a></td>';
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
                            echo '<td class="w3-red"><a href="index.php?Function=concom&Remove='.urlencode($tmp[2]).'&Department='.urlencode($div).'&Position=Specialist" onclick="return confirm(\'Are you sure you want to remove '.$kfName.' '.$klName.' from '.$div.'?\');">Remove</a></td>';
                        }
                        echo "</tr>\n";
                    }
                    unset($tmp);
                }
            }
            unset($pos);
        }
        if ($div != 'Committees' && $div != 'Corporate Staff' && (isset($_SESSION['IS_ADMIN']) || $currentDivDirector)) {
            echo '<tr><td class="w3-center" colspan=8><a href="index.php?Function=concom&AddDepartment='.$div.'">Add someone to '.$div.'</a></td></tr>'."\n";
        }
        // Departments
        if (!empty($printList[$div])) {
            foreach ($printList[$div] as $kdep => $dep) {
                $currentDepHead = false;
                if ($div != 'Corporate Staff' && $kdep != 'Division Director' && $kdep != 'Division Support') {
                    echo '<tr id="department" class="event-color-primary">';
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
                    if ($div != 'Corporate Staff') {
                        if ($div != 'Committees') {
                            printTableHeader($div);
                        } else {
                            printTableHeader($div, false);
                        }
                    }
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
                                        echo '<td class="w3-red"><a href="index.php?Function=concom&Remove='.urlencode($tmp[2]).'&Department='.urlencode($kdep).'&Position='.$pos.'" onclick="return confirm(\'Are you sure you want to remove '.$kfName.' '.$klName.' from '.$kdep.'?\');">Remove</a></td>';
                                    }

                                    echo "</tr>\n";
                                }
                                unset($tmp);
                            }
                        }
                    }
                }
                if (($div != 'Corporate Staff' && $kdep != 'Division Director' && $kdep != 'Division Support') && (isset($_SESSION['IS_ADMIN']) || $currentDivDirector || ($currentDepHead && $pos != "Head"))) {
                    echo '<tr><td class="w3-center" colspan=8><a href="index.php?Function=concom&AddDepartment='.urlencode($kdep).'">Add someone to '.$kdep.'</a></td></tr>'."\n";
                }
            }
        }
    }
    echo "</table>\n";

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

    $db_staff = \concom\ConcomList::listBuild();
    $tmp = array_merge_recursive($fullConComList, $db_staff);
    return($tmp);

}


function DumpConComList()
{
    global $Divisions, $Departments;
    $db_staff = \concom\ConcomList::listBuild();
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


function AddConComPosition($accountid, $department, $position, $note, $notify = true)
{
    global $Departments, $db;

    $positionid = getPositionID($position);
    if ($positionid == null) {
        die("Failed to find position: $position");
    }

    /* Convention event... assume most recent one */
    $event = current_eventID();

    $data = lookup_user_by_id($accountid);
    if ($data['code']) {
        die("Failed to find user: $accountid");
    }
    $user = $data['users'][0];

    $sql = "SELECT ListRecordID FROM ConComList WHERE DepartmentID = ".$Departments[$department]['id']." AND AccountID = $accountid and EventID = $event;";
    $result = $db->run($sql);
    $value = $result->fetch();
    if ($value !== false) {
        // if the department is already assigned to this user, wipe it and replace it with the new info
        $record = $value['ListRecordID'];
        $sql = "UPDATE ConComList SET  PositionID = $positionid, Note = '$Note' WHERE ListRecordID = $record;";
    } else {
        $sql = "INSERT INTO ConComList (AccountID, DepartmentID, PositionID, EventID, Note) VALUES ($accountid, ".$Departments[$department]['id'].", $positionid, $event, '$note');";
    }
    $result = $db->run($sql);

    if ($notify && $result) {
        /* Notify the Division Director, so they know */
        $to = getDepartmentEmails(getDivision($department))[0];
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


function events_served($accountid)
{
    global $db;

    $sql = <<<SQL
        SELECT
            (
                SELECT
                    `EventName`
                from
                    Events AS e
                where
                    e.EventID = l.EventID
            ) AS Event
        FROM
            `ConComList` AS l
        WHERE
            `AccountID` = $accountid
        GROUP BY
            `EventID`
SQL;

    $result = $db->run($sql);
    $years = [];
    $value = $result->fetch();
    while ($value !== false) {
        $years[] = $value['Event'];
        $value = $result->fetch();
    }
    return $years;

}


function check_meeting_attendance($meeting)
{
    $aid = $_SESSION['accountId'];
    $sql = "SELECT * FROM `MeetingAttendance` WHERE AccountID = $aid AND MeetingID = $meeting;";
    $result = \DB::run($sql);
    $value = $result->fetch();
    return ($value !== false);

}


function _departments($division)
{
    global $Departments, $deptCount;

    $did = $Departments[$division]['id'];
    foreach ($Departments as $name => $dep) {
        if ($Departments[$name]['Division'] == $division &&
            $Departments[$name]['Division'] != $name) {
            $id = $Departments[$name]['id'];
            $DivId = $Departments[$Departments[$name]['Division']]['id'];
            echo "<div ";
            echo "class='w3-quarter event-color-secondary w3-xlarge w3-padding w3-margin w3-round-xxlarge' ";
            echo "id=".$id." ";
            echo "draggable='true' ";
            $data = [
            'Divison' => 0,
            'Name' => $name,
            'Id' => $id,
            'Pid' => $DivId,
            'Children' => 0,
            'Count' => $deptCount[$id],
            'Email' => $Departments[$name]['Email']
                    ];
            $data_enc = base64_encode(json_encode($data));
            echo "ondblclick='dblClick(\"".$data_enc."\");' ";
            echo "ondrop='dragDropParent(event, 1);'";
            echo "ondragstart='drag(event);'";
            echo ">$name</div>\n";
        }
    }
    echo "<div class='w3-quarter w3-xlarge w3-padding w3-margin w3-round-xxlarge'>\n";
    echo "<button onclick='newEntry($did)' ";
    echo "class='w3-yellow w3-xlarge w3-button w3-round-xxlarge' ";
    echo "ondrop='dragDropParent(event, 2);'>";
    echo "<i class='fa fa-plus-square' ";
    echo "ondrop='dragDropParent(event, 3);'></i></button>\n";
    echo "</div>\n";

}


function build_structure_sections()
{
    global $Divisions, $Departments, $deptCount;

    echo "<div id='division_new' class='w3-container w3-margin'>";
    echo "<button onclick='newEntry(-1)' class='w3-yellow w3-xlarge w3-button w3-round-xxlarge'><span>Add New Division</span> <i class='fa fa-plus-square'></i></button>";
    echo "</div>\n";

    foreach ($Divisions as $div) {
        $id = $Departments[$div]['id'];
        if ($id <= 0) {
            continue;
        }
        $div_content_id = "division_content_$id";
        $data = [
        'Divison' => 1,
        'Name' => $div,
        'Id' => $id,
        'Pid' => $id,
        'Children' => $Departments[$div]['c  hildCount'],
        'Count' => $deptCount[$id],
        'Email' => null
                ];
        $data_enc = base64_encode(json_encode($data));
        echo "<div id='division_$id' class='w3-container w3-margin'>\n";
        echo "<span class='event-color-primary w3-xxxlarge w3-padding w3-margin w3-round-xlarge' ondblclick='dblClick(\"".$data_enc."\");'>$div</span>";
        echo "<div id='$div_content_id' class='w3-container w3-buffer w3-section' ondragover='dragOverDivision(\"$div_content_id\");' ondragleave='dragLeaveDivision(\"$div_content_id\");' ondragend='dragLeaveDivision(\"$div_content_id\");' ondrop='dragDropDivision(event);' data-dbId=\"$id\">\n";
        _departments($div);
        echo "</div>";
        echo "</div>";
    }

}


function save_position($data)
{
    $parent = 0;
    if ($data->ParentDept >= 0) {
        $parent = $data->ParentDept;
    }
    if ($data->Id != -1) {
        $sql = "UPDATE `Departments` SET ";
        $sql .= " `Name` = '".$data->Name."' ";
        $sql .= ", `ParentDepartmentID` = ".$parent;
        $sql .= " WHERE `DepartmentID` = ".$data->Id;
        DB::run($sql);
    } else {
        $sql = "INSERT INTO `Departments` (`DepartmentID`, `Name`, `ParentDepartmentID`) VALUES (NULL, '".$data->Name."', ".$parent.");";
        DB::run($sql);
        if ($data->ParentDept < 0) {
            $sql = "SELECT `DepartmentID` FROM `Departments` ORDER BY `DepartmentID` DESC LIMIT 1";
            $result = DB::run($sql);
            $value = $result->fetch();
            $sql = "UPDATE `Departments` SET `ParentDepartmentID` = `DepartmentID` WHERE `DepartmentID` = ".$value['DepartmentID'];
            DB::run($sql);
        }
    }

}


function reparent($data)
{
    $sql = "UPDATE `Departments` SET ";
    $sql .= "`ParentDepartmentID` = ".$data->newParent;
    $sql .= " WHERE `DepartmentID` = ".$data->Id;
    DB::run($sql);

}


function departmentDetails($event = null)
{
    $sql = <<<SQL
        SELECT
            `DepartmentID`,
            (
                SELECT
                    COUNT(`AccountID`)
                FROM
                    `ConComList` AS b
                WHERE
                    a.`DepartmentID` = b.`DepartmentID`
SQL;
    if ($event !== null) {
        $sql .= <<<SQL
                    AND b.`EventID` = $event
SQL;
    }
    $sql .= <<<SQL
            ) as count
        FROM
            `Departments` AS a
        WHERE
            `DepartmentID` > 0
        ORDER BY
            `DepartmentID` ASC
SQL;
    $table = load_table($sql);
    $data = [];
    foreach ($table as $entry) {
        $data[$entry['DepartmentID']] = $entry['count'];
    }
    return $data;

}


function delete_position($id)
{
    $sql = "UPDATE `Departments` SET `ParentDepartmentID` = 0 WHERE `DepartmentID` = $id";
    DB::run($sql);
    $sql = "DELETE FROM `Departments` WHERE `DepartmentID` = $id";
    DB::run($sql);

}


function save_email($data)
{
    if ($data->Id != -1) {
        $sql = "UPDATE `EMails` SET ";
        $sql .= " `DepartmentID` = '".$data->Dept."' ";
        $sql .= ", `IsAlias` = ".$data->Alias;
        $sql .= ", `EMail` = '".$data->Email."' ";
        $sql .= " WHERE `EMailAliasID` = ".$data->Id;
        DB::run($sql);
    } else {
        $sql = "INSERT INTO `EMails` (`EMailAliasID`, `DepartmentID`, `IsAlias`, `EMail`) VALUES (NULL, ".$data->Dept.", ".$data->Alias.", '".$data->Email."');";
        DB::run($sql);
    }

}


function delete_email($id)
{
    $sql = "DELETE FROM `EMails` WHERE `EMailAliasID` = $id";
    DB::run($sql);

}
