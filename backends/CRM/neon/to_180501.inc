<?php

/*.
    require_module 'standard';
.*/

require_once(__DIR__."/../../../functions/functions.inc");
require_once(__DIR__."/../../../functions/database.inc");

$meetings = [];


function _loadCustomFields($neon)
{
    $fields = [];
    $fields['definedFields'] = [];

    // Custom Field Data for Session - Parse it to an easy to use array
    $request = [
    'method' => 'common/listCustomFields',
    'parameters' => [
    'searchCriteria.component' => "Account",
        ],
        ];
    $result = $neon->go($request);
    if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
        $fields['definedFields']['customField'] = $result['customFields']['customField'];
        foreach ($result['customFields']['customField'] as $val) {
            $fields['definedFields'][$val['fieldId']] = $val['fieldName'];
        }
    } else {
        die("Impossible error during Defined Custom Fields Download");
    }
    return $fields;

}


function _import_meetings($neon)
{
    global $meetings;

    $fields = _loadCustomFields($neon);
    $event = current_eventID();

    foreach ($fields['definedFields']['customField'] as $field) {
        if ($field['fieldName'] == 'Official Meeting Attendance') {
            foreach ($field['fieldOptions']['fieldOption'] as $meet) {
                $data = explode(' - ', $meet['name']);
                $id = $meet['id'];
                $date = $data[0];
                $name = $data[1];
                $sql = <<<SQL
                    INSERT IGNORE INTO `OfficialMeetings`
                    SET  MeetingID = $id,
                         Date = '$date',
                         EventID = $event,
                         Name = '$name';
SQL;
                DB::run($sql);
                print "<span>Imported Meeting <b>$name</b><br></span>";
                $meetings[] = [
                'MeetingID' => $id,
                'Name' => $name,
                'Date' => $date];
            }
        }
    }

}


function _find_meeting($meeting)
{
    global $meetings;
    foreach ($meetings as $meet) {
        if ($meeting[0] == $meet['Date'] &&
            $meeting[1] == $meet['Name']) {
            return $meet['MeetingID'];
        }
    }
    return null;

}


function _import_attendance($neon)
{
    global $meetings;

    $mt = search_definedFields('Official Meeting Attendance');
    $conComFieldId = search_definedFields('CVGConCom Current Position');
    $page = 1;
    $runPages = 1;

    do {
        $search = [
        'method' => 'account/listAccounts',
        'criteria' => [
        [$conComFieldId, 'NOT_BLANK', ''],
        ],
        'columns' => [
        'standardFields' => ['Account ID', 'First Name'],
        'customFields' => [$mt],
        ],
        'page' => [
        'currentPage' => $page,
        'pageSize' => 200,
        'sortColumn' => 'Account ID',
        'sortDirection' => 'ASC',
        ],
        ];
        $result = $neon->search($search);

        if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
            $runPage = $result['pages'];
            foreach ($result['searchResults'] as $val) {
                $aid = $val['Account ID'];
                if (!empty($val['Official Meeting Attendance'])) {
                    $base_meeting_data = explode('|', $val['Official Meeting Attendance']);
                    foreach ($base_meeting_data as $meeting) {
                        $meeting_data = explode(' - ', $meeting);
                        $mid = _find_meeting($meeting_data);
                        if ($mid === null) {
                            continue;
                        }
                        $msql = <<<SQL
                    INSERT INTO `MeetingAttendance` (AccountID, MeetingID)
                    SELECT
                        *
                    FROM
                        (
                            SELECT
                                $aid,
                                $mid
                        ) AS tmp
                    WHERE
                        NOT EXISTS (
                            SELECT
                                AttendanceRecordID
                            FROM
                                `MeetingAttendance`
                            WHERE
                                AccountID = $aid
                                AND MeetingID = $mid
                        )
                    LIMIT
                        1;
SQL;
                        DB::run($msql);
                        print "<span>Imported Attendance <b>$aid</b> $meeting<br></span>";
                    }
                }
            }
        }
        $page++;
    } while ($page <= $runPages);
    return;

}


function to_180501($neon)
{
    _import_meetings($neon);
    _import_attendance($neon);

}
