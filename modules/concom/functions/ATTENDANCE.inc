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
    $aprid = 3218; // ##==## Lookup Division Director ??? Discussion=Send to all directors in case not part of concom?
    $auth = bin2hex(random_bytes(20));
    $desc = $user['users'][0]['First Name']." ".$user['users'][0]['Last Name']." has claimed to be at meeting ID: ".$meeting;
    $type = "concom/pastcred";

    $sql = "INSERT INTO `DeepLinks` (AccountID, ApproverID, ApprovedValue, Auth, Description, Type) VALUES (";
    $sql .= $_SESSION['accountId'].", $aprid, $meeting, '$auth', '$desc', '$type');";
    $result = \DB::run($sql);
    $deepLinkID = $result->lastInesrtID();

    // Send a Deeplink email for apprivial or denial
    // ##==## TODO Enhancement - Waiting Deeplinks should parse on login for "pending work" notification
    $to = "directors@convergenceevents.org"; // ##==## Likely should be a variable for general authority
    $subject = "Past Attendance Approvial Request";
    $message = "A member has requested credit for attending a past meeting.";
    $message .= " Please review, verify, and approve or deny.\n\n";
    $message .= $user['users'][0]['First Name']." ".$user['users'][0]['Last Name']." has claimed to be at the ";

    $sql = "SELECT `Date` FROM `OfficialMeetings` WHERE MeetingID = ".$meeting;
    $value = \DB::run($sql);

    $message .= $value['Date']." meeting.\n\n";
    $message .= " Please choose to <a href='/index.php?Function=".$type."&DeepLink=".$deepLinkID;
    $message .= "&Auth=".$auth."&approved=true'>approve</a> or ";
    $message .= " <a href='/index.php?Function=".$type."&DeepLink=".$deepLinkID;
    $message .= "&Auth=".$auth."&approved=false'>deny</a> this request.\n\n";
    $message .= "- This is an automated email from the signin sortal service, CON-In-A-Box. Please do not reply."
    \ciab\Email:mail($to, getNoReplyAddress(), $subject, $message);

}
