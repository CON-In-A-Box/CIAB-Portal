<?php declare(strict_types=1);

require_once(__DIR__.'/../vendor/autoload.php');
require_once(__DIR__.'/../backends/mysqlpdo.inc');

/* Initializes the api */
require_once(__DIR__.'/../api/src/App/App.php');

use Atlas\Query\Delete;
use Atlas\Query\Insert;
use Atlas\Query\Select;
use Atlas\Query\Update;

/* Cleanup Tables */
function __cleanupTable($table): void
{
    global $db;

    $delete = Delete::new($db);
    try {
        $delete->from($table)->perform();
    } catch (Exception $e) {
    }

}


function __cleanupDepartments(): void
{
    global $db;

    $data = Select::new($db)->from('Departments')->columns('DepartmentID')->whereEquals(['Name' => 'Historical Placeholder'])->fetchOne();
    $id = $data['DepartmentID'];

    $update = Update::new($db);
    $update->table('Departments')->columns(['ParentDepartmentID' => $id, 'FallbackID' => null])->where('DepartmentID != ', $id)->perform();

    $delete = Delete::new($db);
    $delete->from('Departments')->where('DepartmentID != ', $id)->perform();

}


function __seedConventionDepartments()
{
    global $db;
    $sql_data = file_get_contents('test/DBSeed/Departments.sql');
    $db->query($sql_data);

}


function _addMember($db, $id, $firstName, $lastName, $email, $gender, $password)
{
    $insert = Insert::new($db);
    $insert->into('Members')->columns([
        'AccountID' => $id,
        'FirstName' => $firstName,
        'LastName' => $lastName,
        'Email' => $email,
        'Gender' => $gender
    ])->perform();

    $auth = \password_hash($password, PASSWORD_DEFAULT);

    $insert = Insert::new($db);
    $insert->into('Authentication')->columns([
        'AccountID' => $id,
        'Authentication' => $auth,
        'LastLogin' => null,
        'Expires' => date('Y-m-d', strtotime('+1 year')),
        'FailedAttempts' => 0,
        'OneTime' => null,
        'OneTimeExpires' => null
    ])->perform();

}


function _addMemberToDepartment($db, $id, $eventId, $pos, $departmentId)
{
    $select = Select::new($db);
    $select->columns('PositionID')->from('ConComPositions')->whereEquals(['Name' => $pos]);
    $val = $select->fetchOne();
    $posId = $val['PositionID'];

    $insert = Insert::new($db);
    $insert->into('ConComList')->columns([
        'AccountID' => $id,
        'DepartmentID' => $departmentId,
        'EventID' => $eventId,
        'PositionID' => $posId
    ])->perform();

}


// Build what we need.

$db = MyPDO::instance();

__cleanupTable('Announcements');
__cleanupTable('Authentication');
__cleanupTable('MeetingAttendance');
__cleanupTable('OfficialMeetings');
__cleanupTable('ConComList');
__cleanupTable('Registrations');
__cleanupTable('BadgeTypes');
__cleanupTable('VolunteerHours');
__cleanupTable('HourRedemptions');
__cleanupTable('Events');
__cleanupTable('AnnualCycles');
__cleanupTable('AccountConfiguration');
__cleanupTable('Members');
__cleanupTable('HourRedemptions');
__cleanupTable('VolunteerHours');
__cleanupTable('VolunteerRewards');
__cleanupDepartments();

__seedConventionDepartments();

$select = Select::new($db);
$select->columns('Value')->from('Configuration')->whereEquals(['Field' => 'ADMINACCOUNTS']);
$val = $select->fetchOne();

$val['Value'] = '1000,'.$val['Value'];

$update = Update::new($db);
$update->table('Configuration')->columns($val)->whereEquals(['Field' => 'ADMINACCOUNTS'])->perform();

$from = strtotime('-1 month');
$to = strtotime('+1 year');

$insert = Insert::new($db);
$insert->into('AnnualCycles')->columns([
    'DateFrom' => date('Y-m-d', $from),
    'DateTo' => date('Y-m-d', $to)
])->perform();
$cycleId = $insert->getLastInsertId();

$from = strtotime('-1 day');
$to = strtotime('+1 day');

$insert = Insert::new($db);
$insert->into('Events')->columns([
    'AnnualCycleID' => $cycleId,
    'DateFrom' => date('Y-m-d', $from),
    'DateTo' => date('Y-m-d', $to),
    'EventName' => 'AsgardCon'
])->perform();
$eventId = $insert->getLastInsertId();

$insert = Insert::new($db);
$insert->into('BadgeTypes')->columns([
    'AvailableFrom' => date('Y-m-d', $from),
    'AvailableTo' => date('Y-m-d', $to),
    'Cost' => 0,
    'Name' => 'A Badge',
    'EventID' => $eventId
])->perform();

_addMember($db, 1000, 'Odin', 'Allfather', 'allfather@oneeye.com', 'Allfather', 'Sleipnir');
_addMemberToDepartment($db, 1000, $eventId, 'Head', 2);
_addMember($db, 1001, 'Thor', 'Odinson', 'thor@oneeye.com', 'He', 'Mjolnir');
_addMemberToDepartment($db, 1001, $eventId, 'Head', 105);
_addMember($db, 1002, 'Sif', 'Odinson', 'sif@oneeye.com', 'She', 'Ullr');
_addMemberToDepartment($db, 1002, $eventId, 'Specialist', 105);
_addMember($db, 1003, 'Ran', 'OfTheOcean', 'ran@oneeye.com', 'She', 'Aegir');
