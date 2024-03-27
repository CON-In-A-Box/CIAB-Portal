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
    if (array_key_exists($dep, $GLOBALS['Departments']) &&
        $GLOBALS['Departments'][$dep]['Email']) {
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
        $divMail = getDepartmentEmails($div);
        if (!empty($divMail)) {
            array_push($email, $divMail[0]);
        }
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
        $emails = getDepartmentEmails(getDivision($department));
        if (count($emails) > 0) {
            $to = $emails[0];
            $subject = 'ConCom Division addition to '.$department;
            $name = $user['First Name'].' '.$user['Last Name'];
            if ($_SESSION['preferredName']) {
                $message = $_SESSION['preferredName'].' has added '.$name." to ".$department.'.';
            } else {
                $message = $name." has been added to ".$department.' by an automated process.';
            }
            \ciab\Email::mail($to, getNoReplyAddress(), $subject, $message);
        }
        return true;
    } elseif (!$result) {
        print("Failed Add comcon member to database!");
        return false;
        ;
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
    } else {
        $parent = "MIN(DepartmentID)";
    }
    $fallback = $data->FallbackID;
    if ($fallback == -1 || empty($fallback)) {
        $fallback = "NULL";
    }
    $name = \MyPDO::quote($data->Name);
    if ($data->Id != -1) {
        $sql = "UPDATE `Departments` SET ";
        $sql .= " `Name` = ".$name;
        $sql .= ", `ParentDepartmentID` = ".$parent;
        $sql .= ", `FallbackID` = ".$fallback;
        $sql .= " WHERE `DepartmentID` = ".$data->Id;
        DB::run($sql);
    } else {
        $sql = "INSERT INTO `Departments`( `DepartmentID`, `Name`, `ParentDepartmentID`, `FallbackID`) VALUES (NULL, $name, $parent, $fallback)";
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
    $sql = "UPDATE `Departments` SET `FallbackID` = NULL WHERE `FallbackID` = $id";
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
            ),
            `FallbackID` = NULL
        WHERE
            `DepartmentID` = $id;
SQL;
    DB::run($sql);

    $sql = <<<SQL
        UPDATE
            `Departments`
        SET
            `FallbackID` = NULL
        WHERE
            `FallbackID` = $id;
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


function concom_report($event = null)
{
    if ($event === null) {
        $event = \current_eventID();
    }

    return <<<SQL
        SELECT
            AccountID,
            (
            SELECT
                `Name`
            FROM
                `Departments`
            WHERE
                `DepartmentID` = a.`DepartmentID`
        ) AS Department,
        (
            SELECT
                `Name`
            FROM
                `Departments`
            WHERE
                `DepartmentID` =(
                SELECT
                    `ParentDepartmentID`
                FROM
                    `Departments`
                WHERE
                    `DepartmentID` = a.`DepartmentID`
            )
        ) AS Division,
        (
            SELECT
                `EventName`
            FROM
                `Events`
            WHERE
                `EventID` = a.`EventID`
        ) AS EVENT,
        (
            SELECT
                `Name`
            FROM
                `ConComPositions`
            WHERE
                `PositionID` = a.`PositionID`
        ) AS POSITION,
        (
            SELECT
                `Email`
            FROM
                `Members`
            WHERE
                `AccountID` = a.`AccountID`
        ) AS Email,
        Note
        FROM
            `ConComList` AS a
        WHERE
            `EventID` = '$event'
SQL;

}
