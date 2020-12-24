<?php

/*.
    require_module 'standard';
.*/

namespace event;

$DB_tables = [
'AnnualCycles' => [ // Bylaw defined "year", used for tracking
'AnnualCycleID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
'DateFrom' => 'DATE NOT NULL',
'DateTo' => 'DATE NOT NULL',
        ],
'BadgeTypes' => [
'BadgeTypeID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
'AvailableFrom' => 'DATE NOT NULL',
'AvailableTo' => 'DATE NOT NULL',
'Cost' => 'DECIMAL(6,2) NOT NULL',
'EventID' => 'INT UNSIGNED NOT NULL',
'Name' => 'VARCHAR(50) NOT NULL',
'BackgroundImage' => 'VARCHAR(100)',
        ],
'Events' => [
'EventID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
'AnnualCycleID' => 'INT UNSIGNED NOT NULL',
'DateFrom' => 'DATE NOT NULL',
'DateTo' => 'DATE NOT NULL',
'EventName' => 'VARCHAR(50) NOT NULL',
        ],
'MeetingAttendance' => [
'AttendanceRecordID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
'AccountID' => 'INT UNSIGNED NOT NULL',
'MeetingID' => 'INT UNSIGNED NOT NULL',
        ],
'OfficialMeetings' => [
'MeetingID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
'Date' => 'DATE NOT NULL',
'EventID' => 'INT UNSIGNED NOT NULL',
'Name' => 'VARCHAR(50) NOT NULL',
        ],
'Registrations' => [
'RegistrationID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
'AccountID' => 'INT UNSIGNED NOT NULL',
'BadgeDependentOnID' => 'INT UNSIGNED',
'BadgeName' => 'VARCHAR(100)',
'BadgesPickedUp' => 'INT UNSIGNED',
'BadgeTypeID' => 'INT UNSIGNED NOT NULL',
'EmergencyContact' => 'VARCHAR(300)',
'EventID' => 'INT UNSIGNED NOT NULL',
'RegisteredByID' => 'INT UNSIGNED NOT NULL',
'RegistrationDate' => 'DATETIME NOT NULL',
'BoardingPassGenerated' => 'DATETIME',
'PrintRequested' => 'DATETIME',
'LastPrintedDate' => 'DATETIME',
'PrintRequestIp' => 'VARCHAR(46)',
'Note' => 'TEXT',
'VoidDate' => 'DATETIME',
'VoidBy' => 'INT UNSIGNED',
'VoidReason' => 'TEXT'
        ],


];

$DB_foreignKeys = [
'BadgeTypes' => [
'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],
'Events' => [
'AnnualCycleID' => 'AnnualCycles (AnnualCycleID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],
'MeetingAttendance' => [
'MeetingID' => 'OfficialMeetings (MeetingID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],
'OfficialMeetings' => [
'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],
'Registrations' => [
'BadgeDependentOnID' => 'Registrations (RegistrationID) ON DELETE RESTRICT ON UPDATE CASCADE',
'BadgeTypeID' => 'BadgeTypes (BadgeTypeID) ON DELETE RESTRICT ON UPDATE CASCADE',
'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ]

];

$MODULE_TABLES = array_merge($MODULE_TABLES, $DB_tables);
$MODULE_FOREIGNKEYS = array_merge($MODULE_FOREIGNKEYS, $DB_foreignKeys);
