<?php

/*.
    require_module 'standard';
.*/

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


function conComRBAC($instance)
{
    $positions = [];
    $sql = "SELECT `PositionID`, `Name` FROM `ConComPositions` ORDER BY `PositionID` ASC";
    $result = \DB::run($sql);
    $value = $result->fetch();
    while ($value !== false) {
        $positions[intval($value['PositionID'])] = $value['Name'];
        $value = $result->fetch();
    }

    $sql = "SELECT `DepartmentID`, `ParentDepartmentID`, `Name` FROM `Departments`";
    $result = \DB::run($sql);
    $value = $result->fetch();
    $retvalue = [];
    while ($value !== false) {
        $parent = $value['ParentDepartmentID'];
        $department = $value['DepartmentID'];
        $dept_name = $value['Name'];
        $i = array_keys($positions)[0];
        if ($parent != $department) {
            $perm = "concom.modify.$dept_name.".$positions[$i];
            $target = "$parent.".end(array_keys($positions));
            try {
                $role = $instance->getRole($target);
                $role->addPermission($perm);
            } catch (Exception\InvalidArgumentException $e) {
                error_log($e);
            }
        }

        $target = "$department.$i";
        $perm = "concom.add.$dept_name";
        try {
            $role = $instance->getRole($target);
            $role->addPermission($perm);
        } catch (Exception\InvalidArgumentException $e) {
            error_log($e);
        }

        foreach ($positions as $id => $position) {
            $nid = intval($id) + 1;
            if (array_key_exists($nid, $positions)) {
                $target_pos = $positions[$nid];
                $perm = "concom.modify.$dept_name.$target_pos";
                $target = "$department.$i";
                try {
                    $role = $instance->getRole($target);
                    $role->addPermission($perm);
                } catch (Exception\InvalidArgumentException $e) {
                    error_log($e);
                }
            }
        }

        $value = $result->fetch();
    }

}


function buildConComRBAC()
{
    \ciab\RBAC::customizeRBAC('conComRBAC');

}


function printDepartmentHeader($section, $isDepartment, $priv = false)
{
    echo "<div id=\"table_head_$section\" class=\" UI-table-row CONCOM-table-head\">";
    if ($isDepartment) {
        echo '<div class="UI-table-cell-no-border">Department</div>';
        echo '<div class="UI-table_cell">Division</div>';
    } else {
        echo '<div class="UI-table-cell-no-border">Division</div>';
    }
    echo '<div class="UI-table-cell-no-border">Name</div>';
    echo '<div class="UI-table-cell-no-border">Position</div>';
    if ($isDepartment) {
        echo '<div class="UI-table-cell-no-border">Email</div>';
    }
    echo '<div class="UI-table-cell-no-border">Note</div>';
    if ($priv) {
        echo '<div class="UI-table-cell-no-border"></div>';
    }
    echo "</div>\n";

}


function isSpecialDivision($div)
{
    return ($div == 'Committees' || $div == 'Corporate Staff' ||
            $div == 'Board Departments');

}


function printDivisionHeader($div)
{
    global $Divisions;

    echo '<div id="'.$div.'" class="UI-table UI-table-heading">';
    echo '<div id="division" class="UI-table-row event-color-secondary">';
    echo '<div class="UI-center UI-table-cell-no-border">'.$div;
    if (!isSpecialDivision($div)) {
        echo ' Division';
    }
    echo '</div>';
    echo '<div class="UI-center UI-table-cell-no-border">';
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
    echo '</div>';
    echo '<div class="UI-table-cell-no-border">';
    echo implode('<br />', getDepartmentEmails($div));
    echo "</div></div>\n";
    echo "</div>";

}


function printDivisionMember($pos, $div, $position, $rbacpos)
{
    foreach ($pos as $kName => $lName) {
        $tmp = explode('|', $lName);
        echo '<div class="UI-table-row">';
        echo '<div class="UI-table-cell-no-border">'.$div.'</div>';
        echo '<div class="UI-table-cell-no-border">'.$kName.'</div>';
        echo '<div class="UI-table-cell-no-border">'.$position.'</div>';
        echo '<div class="UI-table-cell-no-border">';
        echo $tmp[1];
        if ($_SESSION['accountId'] == $tmp[2]) {
            if (!empty($tmp[1])) {
                echo '<br />';
            }
            echo 'This is you!';
        }
        echo "</div>";
            // System Admins can remove anyone

        $perm = "concom.modify.$div.$rbacpos";
        if (\ciab\RBAC::havePermission($perm)) {
            echo '<div class="UI-red UI-table-cell-no-border">';
            echo '<a href="#" onclick="confirmRemoval(\''.$kName.'\', \''.$tmp[2].'\', \''.$div.'\', \'Head\');">Remove</a>';
            echo '</div>';
        }
        echo "</div>\n";
    }

}


function printDepartmentMember(
    $div,
    $kdep,
    $dep,
    $pos
) {
    foreach ($dep[$pos] as $kName => $lName) {
        $tmp = explode('|', $lName);
        echo '<div class="UI-table-row">';
        echo '<div class="UI-table-cell-no-border">'.$kdep.'</div>';
        if (!isSpecialDivision($div)) {
            echo '<div class="UI-table-cell-no-border">'.$div.'</div>';
        }
        echo '<div class="UI-table-cell-no-border">'.$kName.'</div>';
        if (!isSpecialDivision($div)) {
            echo '<div class="UI-table-cell-no-border">'.$pos.'</div>';
        }
        if ($div == 'Corporate Staff') {
            echo '<div class="UI-table-cell-no-border">'.getDepartmentEmails($kdep)[0].'</div>';
        } else {
            echo '<div class="UI-table-cell-no-border">'.$tmp[0].'</div>';
        }
        echo '<div class="UI-table-cell-no-border">';
        echo $tmp[1];

        // Check to see if this is an entry for the logged in person
        if ($_SESSION['accountId'] == $tmp[2]) {
            if (!empty($tmp[1])) {
                echo '<br />';
            }
            echo 'This is you!';
        }
        echo '</div>';
        $perm = "concom.modify.$kdep.$pos";
        if (\ciab\RBAC::havePermission($perm)) {
            echo '<div class="UI-red UI-table-cell-no-border">';
            echo '<a href="#" onclick="confirmRemoval(\''.$kName.'\', \''.$tmp[2].'\', \''.$kdep.'\', \''.$pos.'\');">Remove</a>';
            echo '</div>';
        }
        echo "</div>\n";
    }

}


function printDivisionTable($div, $printList)
{
    global $ConComPositions;

    printDivisionHeader($div);

    $priv = false;
    foreach ($ConComPositions as $pos) {
        $perm = "concom.modify.$div.$pos";
        $priv |= \ciab\RBAC::havePermission($perm);
    }

    echo "<div class='UI-table-all'>\n";
    if (!isSpecialDivision($div)) {
        printDepartmentHeader($div, false, $priv);
    }

    // Division Directors
    if (isset($printList[$div]['Division Director'])) {
        foreach ($printList[$div]['Division Director'] as $pos) {
            printDivisionMember($pos, $div, 'Director', 'Head');
        }
    }
    //Division Support
    if (isset($printList[$div]['Division Support'])) {
        foreach ($printList[$div]['Division Support'] as $pos) {
            printDivisionMember($pos, $div, 'Support', 'Sub-Head');
        }
    }
    echo '</div>'; // End Table

    $perm = "concom.add.$div";
    if (!isSpecialDivision($div) && \ciab\RBAC::havePermission($perm)) {
        $positions = base64_encode(json_encode(['Division Director', null, 'Divisional Support']));
        echo '<div class="UI-center"><input id="add_'.$div.'" type="button" class="UI-yellowbutton" onclick="addMember(\''.$div.'\', \''.$positions.'\');" value="Add someone to '.$div.'"></div>'."\n";
    }
    echo '&nbsp;';

}


function printDepartmentTable($div, $kdep, $dep)
{
    global $ConComPositions;

    echo '<div class="UI-table UI-table-heading">';
    if ($div != 'Corporate Staff' && $kdep != 'Division Director' && $kdep != 'Division Support') {
        echo '<div id="department" class="UI-table-row event-color-primary">';
        if ($div == 'Committees') {
            echo '<div class="UI-table-cell-no-border" id="'.$kdep.'">'.$kdep.'</div>';
        } else {
            echo '<div class="UI-table-cell-no-border" id="'.$kdep.'">'.$kdep.'</div>';
            echo '<div class="UI-table-cell-no-border"><a href="#'.$div.'">'.$div.'</a></div>';
        }
        echo '<div class="UI-table-cell-no-border">';
        echo implode('<br />', getDepartmentEmails($kdep));
        echo "</div></div>\n";
    }

    $priv = false;
    foreach ($ConComPositions as $pos) {
        $perm = "concom.modify.$kdep.$pos";
        $priv |= \ciab\RBAC::havePermission($perm);
    }

    if ($kdep != 'Division Director' && $kdep != 'Division Support') {
        if ($div != 'Corporate Staff') {
            if ($div != 'Committees') {
                echo "</div>";
                echo "<div class='UI-table-all UI-table-heading'>\n";
                printDepartmentHeader($div, true, $priv);
            } else {
                echo "</div>";
                echo "<div class='UI-table UI-table-heading'>\n";
                printDepartmentHeader($div, false, $priv);
            }
        }
        foreach ($ConComPositions as $pos) {
            if (isset($dep[$pos])) {
                printDepartmentMember($div, $kdep, $dep, $pos);
            }
        }
    }
    echo '</div>';  // End Table

    $perm = "concom.add.$kdep";
    if (($kdep != 'Division Director' && $kdep != 'Division Support') && \ciab\RBAC::havePermission($perm)) {
        $positions = base64_encode(json_encode(array_values($GLOBALS['ConComPositions'])));
        echo '<div class="UI-center"><input id="add_'.$div.'" type="button" class="UI-yellowbutton" onclick="addMember(\''.$kdep.'\', \''.$positions.'\');" value="Add someone to '.$kdep.'"></div>'."\n";
    }
    echo '&nbsp;';

}


function ConComListDisplay()
{
    global $Divisions;

    $concomlist = ConComListBuild();
    $printList = array();
    // Put each entry into a divisional/department mapped array
    foreach ($concomlist as $line) {
        $printList[$line['Division']][$line['Department']][$line['Position']][$line['Full Name']] = $line['Email'].'|'.$line['Note'].'|'.$line['Account ID'];
    }

    // Sort everything so we don't have to later
    ksort_recursive($printList);

    buildConComRBAC();

    // Start the crazy print routine!
    foreach ($Divisions as $div) {
        echo '<div class="UI-container UI-padding UI-border">';
        printDivisionTable($div, $printList);

        // Departments
        echo '<div class="UI-container UI-padding">';
        if (!empty($printList[$div])) {
            foreach ($printList[$div] as $kdep => $dep) {
                printDepartmentTable($div, $kdep, $dep);
            }
        }
        echo '</div>';
        echo '</div>';
        echo '&nbsp;';
        /* End Division */
    }

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
            'Full Name' => '',
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

    if (is_numeric($position)) {
        $positionid = intval($position);
    } else {
        $positionid = getPositionID($position);
    }
    if ($positionid == null) {
        print("Failed to find position: $position");
        return false;
    }

    /* Convention event... assume most recent one */
    $event = current_eventID();

    $data = lookup_user_by_id($accountid);
    if ($data['code']) {
        print("Failed to find user: $accountid");
        return false;
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

        $name = $user['Full Name'];

        $message = $_SESSION['fullName'].' has added '.$name." to ".$department.' as '.$position.'.';

        \ciab\Email::mail($to, getNoReplyAddress(), $subject, $message);
        return true;
    } elseif (!$result) {
        print("Failed Add comcon member to database!");
        return false;
        ;
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
            $name = $user['Full Name'];
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
            'Email' => $Departments[$name]['Email'],
            'Fallback' => $Departments[$name]['Fallback'],
            'FallbackID' => $Departments[$name]['FallbackID']
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
        'Email' => null,
        'Fallback' => $Departments[$div]['Fallback'],
        'FallbackID' => $Departments[$div]['FallbackID']
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
    $fallback = $data->FallbackID;
    if ($fallback == -1 || empty($fallback)) {
        $fallback = "NULL";
    }
    if ($data->Id != -1) {
        $sql = "UPDATE `Departments` SET ";
        $sql .= " `Name` = '".$data->Name."' ";
        $sql .= ", `ParentDepartmentID` = ".$parent;
        $sql .= ", `FallbackID` = ".$fallback;
        $sql .= " WHERE `DepartmentID` = ".$data->Id;
        DB::run($sql);
    } else {
        $sql = "INSERT INTO `Departments` (`DepartmentID`, `Name`, `ParentDepartmentID`, `FallbackID`) VALUES (NULL, '".$data->Name."', ".$parent.", ".$fallback.");";
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
    $sql = "DELETE FROM `ConComPermissions` WHERE `Position` = '$id' AND `Permission` = '$perm';";
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


function find_fallback_parent($data, $head)
{
    $checked = [];
    $index = $head;
    $next = $head;

    while ($next != null && !in_array($next, $checked) &&
        in_array($next, $data)) {
        $checked[] = $next;
        foreach ($data as $i => $k) {
            if ($k == $next) {
                $index = $next;
                $next = $i;
                break;
            }
        }
        if ($next == $head) {
            break;
        }
    }

    if ($next != null) {
        return $next;
    }
    return $index;

}


function fallback_cmp($a, $b)
{
    if ($a == $b) {
        return 0;
    }
    if ($a == null) {
        return 1;
    }
    if ($b == null) {
        return -1;
    }
    return 0;

}


function buildDonutData()
{
    global $Departments;
    $data = [];
    $fallback = [];
    foreach ($Departments as $dpt => $d) {
        if ($d['Division'] == $dpt) {
            $data[$dpt] = [];
            $fallback[$dpt] = $d['Fallback'];
        }
    }
    ksort($data);
    foreach ($Departments as $dpt => $d) {
        if ($d['Division'] == $dpt) {
            continue;
        }
        $data[$d['Division']]['data'][] = $dpt;
    }
    foreach ($data as $dpt) {
        ksort($dpt);
    }

    ksort($fallback);
    uasort($fallback, 'fallback_cmp');

    /* Order based on fallback */

    $data2 = [];
    while (!empty($fallback)) {
        reset($fallback);
        $key = find_fallback_parent($fallback, key($fallback));
        $next = $fallback[$key];
        $data2[$key] = $data[$key];
        if ($next !== null) {
            $data2[$key]['arrow'] = "\u{2193}";
        } else {
            $data2[$key]['arrow'] = "\u{233E}";
        }
        unset($fallback[$key]);
        while ($next !== null && array_key_exists($next, $fallback)) {
            $key = $next;
            $next = $fallback[$next];
            $data2[$key] = $data[$key];
            if ($next !== null) {
                $data2[$key]['arrow'] = "\u{2193}";
            } else {
                $data2[$key]['arrow'] = "\u{233E}";
            }
            unset($fallback[$key]);
        }
    }

    unset($data);

    $pid = 0;
    foreach ($data2 as $i => $d) {
        $nodeData = [
        'label' => $i,
        'colorIndex' => $pid,
        'strokeWidth' => 3,
        'link' => 'index.php?Function=concom#'.strval($i),
        'dsize' => 1
        ];

        $subData = [];
        $subData['nodeData'] = [
        'label' => $d['arrow'],
        'colorIndex' => $pid,
        'strokeWidth' => 2,
        'dsize' => 1,
        'noRotate' => 1,
        'pieWidth' => 10
        ];

        $subSubData = [];
        if (count($d['data']) > 0) {
            foreach ($d['data'] as $j) {
                $subSubData[]['nodeData'] = [
                'label' => $j,
                'colorIndex' => $pid,
                'strokeWidth' => 1,
                'link' => 'index.php?Function=concom#'.$j,
                'dsize' => strval(1 / count($d['data']))
                ];
            }
        } else {
            $subSubData[]['nodeData'] = [
            'colorIndex' => 'rgba(255, 255, 255, .4)',
            'strokeWidth' => 0,
            'dsize' => 1
            ];
        }
        $subData['subData'] = $subSubData;
        $data[] = [
        'nodeData' => $nodeData,
        'subData' => array($subData)
        ];
        $pid++;
    }

    return $data;

}


function buildBackupList($depId)
{
    $sql = <<<SQL
        SELECT
            *
        FROM
            `Departments` as d
        WHERE
            `ParentDepartmentID` = `DepartmentID`
            AND NOT `DepartmentID` = $depId
            AND `DepartmentID` NOT IN (
                  SELECT
                      `DepartmentID`
                  FROM
                      `Departments`
                  WHERE
                      Name = 'Historical Placeholder'
                )
            AND (`DepartmentID` NOT IN (
                    SELECT
                        `FallbackID`
                    FROM
                        `Departments` as dd
                    WHERE
                        dd.`FallbackID` IS NOT NULL
                )
                OR `DepartmentID` = (
                    SELECT
                        `FallbackID`
                    FROM
                        `Departments` as ddd
                    WHERE
                        ddd.`DepartmentID` = $depId)
        )
SQL;
    $result = \DB::run($sql);
    $value = $result->fetch();
    $data = array();
    while ($value !== false) {
        $data[] = $value;
        $value = $result->fetch();
    }
    return $data;

}
