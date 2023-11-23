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

$insert = Insert::new($db);
$insert->into('Members')->columns([
    'AccountID' => 1000,
    'FirstName' => 'Odin',
    'LastName' => 'Allfather',
    'Email' => 'allfather@oneeye.com',
    'Gender' => 'Allfather'
])->perform();

$auth = \password_hash('Sleipnir', PASSWORD_DEFAULT);

$insert = Insert::new($db);
$insert->into('Authentication')->columns([
    'AccountID' => 1000,
    'Authentication' => $auth,
    'LastLogin' => null,
    'Expires' => date('Y-m-d', strtotime('+1 year')),
    'FailedAttempts' => 0,
    'OneTime' => null,
    'OneTimeExpires' => null
])->perform();

$select = Select::new($db);
$select->columns('PositionID')->from('ConComPositions')->whereEquals(['Name' => 'Head']);
$val = $select->fetchOne();
$posId = $val['PositionID'];

$insert = Insert::new($db);
$insert->into('ConComList')->columns([
    'AccountID' => 1000,
    'DepartmentID' => 2,
    'EventID' => $eventId,
    'PositionID' => $posId
])->perform();
