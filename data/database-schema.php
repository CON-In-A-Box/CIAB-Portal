<?php

// CON-In-A-Box DB Schema
// 2018 Thomas Keeley

// The Current CIAB DB Schema in an array for checking and applying

$DB_tables = [
    'Configuration' => [
        'Field' => 'VARCHAR(15) NOT NULL PRIMARY KEY',
        'Value' => 'VARCHAR(100) NOT NULL',
    ],
    'ConComList' => [
        'ListRecordID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'AccountID' => 'INT UNSIGNED NOT NULL', // Taken from NeonCRM Currently
        'Department' => 'INT UNSIGNED NOT NULL',
        'Position' => 'INT UNSIGNED NOT NULL',
        'Note' => 'VARCHAR(100)',
        'ConventionYear' => 'INT UNSIGNED NOT NULL',
    ],
    'ConComPositions' => [
        'PositionID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'Name' => 'VARCHAR(50) NOT NULL',
    ],
    'ConventionYear' => [
        'YearID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'Name' => 'VARCHAR(50) NOT NULL',
    ],
    'Departments' => [
        'DepartmentID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'Name' => 'VARCHAR(50) NOT NULL',
        'Division' => 'INT UNSIGNED NOT NULL',
    ],
    'Divisions' => [
        'DivisionID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'Name' => 'VARCHAR(50) NOT NULL',
    ],
    'ElegibleVoters' => [
        'VoterRecordID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'AccountID' => 'INT UNSIGNED NOT NULL',
        'ConventionYear' => 'INT UNSIGNED NOT NULL',
    ],
    'EMailAliases' => [
        'EMailID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'Department' => 'VARCHAR(50) NOT NULL',
        'EMail' => 'VARCHAR(100) NOT NULL',
    ],
    'HourRedemptions' => [
        'ClaimID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'AccountID' => 'INT UNSIGNED NOT NULL',
        'PrizeId' => 'INT UNSIGNED NOT NULL',
        'ConventionYear' => 'INT UNSIGNED NOT NULL',
    ],
    'MeetingAttendance' => [
        'AttendanceRecordID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'AccountID' => 'INT UNSIGNED NOT NULL',
        'MeetingID' => 'INT UNSIGNED NOT NULL',
    ],
    'OfficialMeetings' => [
        'MeetingID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'Name' => 'VARCHAR(50) NOT NULL',
        'Date' => 'DATE NOT NULL',
        'ConventionYear' => 'INT UNSIGNED NOT NULL',
    ],
    'RewardGroup' => [
        'GroupID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'Limit' => 'INT UNSIGNED',
    ],
    'VolunteerHours' => [
        'HourEntryID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'AccountID' => 'INT UNSIGNED NOT NULL',
        'ActualHours' => 'FLOAT(5,3) NOT NULL',
        'EndDateTime' => 'DATETIME NOT NULL',
        'TimeModifier' => 'FLOAT(2,1) NOT NULL',
        'DepartmentWorked' => 'INT UNSIGNED NOT NULL',
        'EnteredBy' => 'INT UNSIGNED NOT NULL',
        'AuthorizedBy' => 'INT UNSIGNED NOT NULL',
        'ConventionYear' => 'INT UNSIGNED NOT NULL',
    ],
    'VolunteerRewards' => [
        'PrizeID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'Name' => 'VARCHAR(50) NOT NULL',
        'Value' => 'DECIMAL(5,2) NOT NULL',
        'Promo' => 'BOOLEAN',
        'Group' => 'INT UNSIGNED',
        'TotalInventory' => 'INT NOT NULL',
    ],
]
?>
