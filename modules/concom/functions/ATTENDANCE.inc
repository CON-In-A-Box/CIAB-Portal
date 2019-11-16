<?php

require_once(__DIR__.'/../functions/concom.inc');


function update_meeting_attendance($meeting, $accountId)
{
    $sql = "INSERT INTO `MeetingAttendance` (AccountID, MeetingID) VALUES ($accountId, $meeting);";
    DB::run($sql);

}


function check_meeting_attendance($meeting)
{
    $sql = "SELECT * FROM `MeetingAttendance` WHERE AccountID = ".$_SESSION['accountId']." AND MeetingID = $meeting;";
    $result = \DB::run($sql);
    $value = $result->fetch();
    return ($value !== false);

}


function check_meeting_pastrequest($meeting)
{
    $sql = "SELECT * FROM `DeepLinks` WHERE AccountID = ".$_SESSION['accountId']." AND Type = 'pastcred' AND ApprovedValue = $meeting;";
    $result = \DB::run($sql);
    $value = $result->fetch();
    return ($value !== false);

}


function request_past_attendance($meeting)
{
    $user = lookup_user_by_id($_SESSION['accountId']);
    $aprid = 3218; ##==##
    $auth = bin2hex(random_bytes(20));
    $desc = $user['users'][0]['First Name']." ".$user['users'][0]['Last Name']." has claimed to be at meeting ID: ".$meeting;
    $type = "pastcred";
    $sql = "INSERT INTO `DeepLinks` (AccountID, ApproverID, ApprovedValue, Auth, Description, Type) VALUES (".$_SESSION['accountId'].", $aprid, $meeting, '$auth', '$desc', '$type');";
    DB::run($sql);

}
