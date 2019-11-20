<?php

require_once($FUNCTIONDIR.'/divisional.inc');
require_once($FUNCTIONDIR."/database.inc");
require_once($FUNCTIONDIR.'/users.inc');
require_once($BACKEND.'/email.inc');
require_once($BACKEND.'/RBAC.inc');
require_once(__DIR__.'/VOLUNTEERS.inc');
require_once(__DIR__.'/POSITION.inc');
require_once(__DIR__.'/REGISTRATION.inc');
require_once(__DIR__.'/LIST.inc');
require_once(__DIR__.'/ATTENDANCE.inc');


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
    $sql = "SELECT PositionID FROM ConComPositions WHERE Name = '$position';";
    $result = DB::run($sql);
    $value = $result->fetch();
    if ($value === false) {
        return null;
    }
    return $value['PositionID'];

}


function printTableHeader($section, $long = true)
{
    echo "<tr id=\"table_head_$section\" class=\"CONCOM-table-head\">";
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
    echo "<table class='CONCOM-division-table'>\n";
    foreach ($Divisions as $div) {
        $currentDivDirector = false;
        echo '<tr id="division" class="event-color-secondary"><th id="'.$div.'" class="UI-center" colspan=2>'.$div;
        if ($div != 'Committees' && $div != 'Corporate Staff') {
            echo ' Division';
        }
        echo '</th><th class="UI-center" colspan=3>';
        echo '<div class="CONCOM-goto-dropdown">';
        echo '  Go To Section';
        echo '  <div class="CONCOM-goto-dropdown-content">';
        echo '    <a href="#main_nav" class="UI-border">Top of Page</a><br />';
        foreach ($Divisions as $menuDiv) {
            // ##==## Likely need to add some translation here to blot out '&'
            echo '    <a href="#'.$menuDiv.'" class="UI-border">'.$menuDiv.'</a><br />';
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
                        echo '<tr><td>Division Director</td><td>'.$div.'</td>';
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
                            echo '<td class="UI-red">';
                            echo '<a href="#" onclick="confirmRemoval(\''.$kfName.'\', \''.$klName.'\', \''.$tmp[2].'\', \''.$div.'\', \'Head\');">Remove</a>';
                            echo '</td>';
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
                            echo '<td class="UI-red">';
                            echo '<a href="#" onclick="confirmRemoval(\''.$kfName.'\', \''.$klName.'\', \''.$tmp[2].'\', \''.$div.'\', \'Specialist\');">Remove</a>';
                            echo '</td>';
                        }
                        echo "</tr>\n";
                    }
                    unset($tmp);
                }
            }
            unset($pos);
        }
        if ($div != 'Committees' && $div != 'Corporate Staff' && (isset($_SESSION['IS_ADMIN']) || $currentDivDirector)) {
            echo '<tr><td class="UI-center" colspan=8><a href="index.php?Function=concom&AddDepartment='.$div.'">Add someone to '.$div.'</a></td></tr>'."\n";
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
                                        echo '<td class="UI-red">';
                                        echo '<a href="#" onclick="confirmRemoval(\''.$kfName.'\', \''.$klName.'\', \''.$tmp[2].'\', \''.$kdep.'\', \''.$pos.'\');">Remove</a>';
                                        echo '</td>';
                                    }
                                    echo "</tr>\n";
                                }
                                unset($tmp);
                            }
                        }
                    }
                }
                if (($kdep != 'Division Director' && $kdep != 'Division Support') && (isset($_SESSION['IS_ADMIN']) || $currentDivDirector || ($currentDepHead && $pos != "Head"))) {
                    echo '<tr><td class="UI-center" colspan=8><a href="index.php?Function=concom&AddDepartment='.urlencode($kdep).'">Add someone to '.$kdep.'</a></td></tr>'."\n";
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
        foreach ($dep['Email'] as $listEmails) {
            array_push($email, $listEmails['EMail']);
        }
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
    global $Departments;

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
    $result = DB::run($sql);
    $value = $result->fetch();
    if ($value !== false) {
        // if the department is already assigned to this user, wipe it and replace it with the new info
        $record = $value['ListRecordID'];
        $sql = "UPDATE ConComList SET  PositionID = $positionid, Note = '$note' WHERE ListRecordID = $record;";
    } else {
        $sql = "INSERT INTO ConComList (AccountID, DepartmentID, PositionID, EventID, Note) VALUES ($accountid, ".$Departments[$department]['id'].", $positionid, $event, '$note');";
    }
    $result = DB::run($sql);

    if ($notify && $result) {
        /* Notify the Division Director, so they know */
        $to = getDepartmentEmails(getDivision($department))[0];
        $subject = 'ConCom Division addition to '.$department;

        $name = $user['First Name']." ".$user['Last Name'];

        $message = $_SESSION['fullName'].' has added '.$name." to ".$department.' as '.$position.'.';

        \ciab\Email::mail($to, getNoReplyAddress(), $subject, $message);
        return true;
    } elseif (!$result) {
        die("Failed Add comcon member to database!");
    }

}


function RemoveConComPosition($accountid, $department, $position, $notify = true)
{
    global $Departments;

    $positionid = getPositionID($position);
    if ($positionid == null) {
        return;
    }

    $sql = "DELETE FROM ConComList WHERE AccountID = $accountid AND DepartmentID = ".$Departments[$department]['id']." AND PositionID = $positionid";
    $result = DB::run($sql);
    if ($result) {
        if ($notify) {
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
            \ciab\Email::mail($to, getNoReplyAddress(), $subject, $message);
        }
        return true;
    } else {
        sendError("Failed to apply remove to Database".__FILE__, $result);
    }

}


function events_served($accountid)
{
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

    $result = DB::run($sql);
    $years = [];
    $value = $result->fetch();
    while ($value !== false) {
        $years[] = $value['Event'];
        $value = $result->fetch();
    }
    return $years;

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
            echo "class='CONCOM-department' ";
            echo "id=".$id." ";
            echo "draggable='true' ";
            $data = [
            'Division' => 0,
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
    echo "<div class='CONCOM-new-department-div'>\n";
    echo "<button onclick='newEntry($did)' ";
    echo "class='CONCOM-new-department-button' ";
    echo "ondrop='dragDropParent(event, 2);'>";
    echo "<i class='fas fa-plus-square' ";
    echo "ondrop='dragDropParent(event, 3);'></i></button>\n";
    echo "</div>\n";

}


function build_structure_sections()
{
    global $Divisions, $Departments, $deptCount;

    echo "<div id='division_new' class='UI-container UI-margin'>";
    echo "<button onclick='newEntry(-1)' class='CONCOM-new-division-button'><span>Add New Division</span> <i class='fas fa-plus-square'></i></button>";
    echo "</div>\n";

    foreach ($Divisions as $div) {
        $id = $Departments[$div]['id'];
        if ($id <= 0) {
            continue;
        }
        $div_content_id = "division_content_$id";
        $data = [
        'Division' => 1,
        'Name' => $div,
        'Id' => $id,
        'Pid' => $id,
        'Children' => $Departments[$div]['childCount'],
        'Count' => $deptCount[$id],
        'Email' => null
                ];
        $data_enc = base64_encode(json_encode($data));
        echo "<div id='division_$id' class='UI-container UI-margin'>\n";
        echo "<span class='CONCOM-division-span' ondblclick='dblClick(\"".$data_enc."\");'>$div</span>";
        echo "<div id='$div_content_id' class='CONCOM-division-drag-div' ondragover='dragOverDivision(\"$div_content_id\");' ondragleave='dragLeaveDivision(\"$div_content_id\");' ondragend='dragLeaveDivision(\"$div_content_id\");' ondrop='dragDropDivision(event);' data-dbId=\"$id\">\n";
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
    $sql = "DELETE FROM `EMails` WHERE `DepartmentID` = $id";
    DB::run($sql);
    $sql = "DELETE FROM `EmailListAccess` WHERE `DepartmentID` = $id";
    DB::run($sql);
    $sql = <<<SQL
        UPDATE
            `Departments`
        SET
            `ParentDepartmentID` = (
                SELECT
                    `DepartmentID`
                FROM
                    (
                        SELECT
                            *
                        FROM
                            `Departments`
                    ) as x
                WHERE
                    Name = 'Historical Placeholder'
                LIMIT
                    1
            )
        WHERE
            `DepartmentID` = $id;
SQL;
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


function getDepartmentPermissions($depId)
{
    $rc = [];
    $sql = "SELECT * FROM `ConComPositions`;";
    $result = DB::run($sql);
    $value = $result->fetch();
    while ($value !== false) {
        $id = $depId.'.'.$value['PositionID'];
        $mine = \ciab\RBAC::getPermissions($id, false);
        $inherited = \ciab\RBAC::getPermissions($id, true);
        $inherited = array_diff($inherited, $mine);
        $rc[$value['PositionID']]  = ['name' => $value['Name'], 'position' => $mine, 'inherited' => $inherited];
        $value = $result->fetch();
    }
    return $rc;

}


function deleteDepartmentPermission($depId, $posId, $perm)
{
    $id = $depId.'.'.$posId;
    $sql = "DELETE FROM `ConComPermissions` WHERE `Position` = $id AND `Permission` = '$perm';";
    DB::run($sql);
    \ciab\RBAC::reload();

}


function addDepartmentPermission($depId, $posId, $perm)
{
    $id = $depId.'.'.$posId;
    $sql = "INSERT INTO`ConComPermissions` (`Position`, `Permission`) VALUES ('$id', '$perm');";
    DB::run($sql);
    \ciab\RBAC::reload();

}


function buildDonutData()
{
    global $Departments;
    $data = [];
    foreach ($Departments as $dpt => $d) {
        if ($d['Division'] == $dpt) {
            $data[$dpt] = [];
        }
    }
    ksort($data);
    foreach ($Departments as $dpt => $d) {
        if ($d['Division'] == $dpt) {
            continue;
        }
        $data[$d['Division']][] = $dpt;
    }
    foreach ($data as $dpt) {
        ksort($dpt);
    }

    $output = "var data = [";
    $pid = 0;
    foreach ($data as $i => $d) {
        $output .= "{'nodeData':{";
        $output .= "'label':'$i',";
        $output .= "'colorIndex': $pid,";
        $output .= "'strokeWidth': 3,";
        $output .= "'link': 'index.php?Function=concom#".$i."',";
        $output .= "'dsize':1,";
        $output .= "},";

        if (count($d) > 0) {
            $output .= "'subData':[";
            $ii = 0;
            foreach ($d as $j) {
                $z = $ii + $pid;
                $output .= "{";
                $output .= "'nodeData': {";
                $output .= "'label': '$j',";
                $output .= "'colorIndex': $z,";
                $output .= "'strokeWidth': 1,";
                $output .= "'link': 'index.php?Function=concom#".$j."',";
                $output .= "'dsize': ".strval(1 / count($d)).",";
                $output .= "},";
                $output .= "},";
                $ii++;
            }
            $output .= "],";
        } else {
            $output .= "'subData':[";
            $output .= "{";
            $output .= "'nodeData': {";
            $output .= "'colorIndex': 'rgba(255, 255, 255, .4)',";
            $output .= "'strokeWidth': 0,";
            $output .= "'dsize': 1,";
            $output .= "},";
            $output .= "},";
            $output .= "],";
        }
        $output .= "},";
        $pid++;
    }
    $output .= "];";

    return $output;

}
