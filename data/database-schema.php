<?php
// phpcs:disable Generic.WhiteSpace.ScopeIndent.IncorrectExact

// CON-In-A-Box DB Schema
// The Current CIAB DB Schema in an array for checking and applying

class SCHEMA
{

    public static $REQUIED_DB_SCHEMA = 2019111900; // Current DB Version - YYYYMMDDvv format (vv=daily counter form 00)

    public static $DB_tables = [
        'ActivityLog' => [
            'LogEntryID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'AccountID' => 'INT UNSIGNED NOT NULL', // Use 0 for System AccountID
            'Function' => 'VARCHAR(100) NOT NULL',
            'Query' => 'TEXT NOT NULL',
            'Date' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
        ],
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
        'Configuration' => [
            'Field' => 'VARCHAR(15) NOT NULL PRIMARY KEY',
            'Value' => 'TEXT NOT NULL',
        ],
        'ConComList' => [
            'ListRecordID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'AccountID' => 'INT UNSIGNED NOT NULL', // Taken from NeonCRM Currently
            'DepartmentID' => 'INT UNSIGNED NOT NULL',
            'EventID' => 'INT UNSIGNED NOT NULL',
            'Note' => 'VARCHAR(100)',
            'PositionID' => 'INT UNSIGNED NOT NULL',
        ],
        'ConComPositions' => [
            'PositionID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'Name' => 'VARCHAR(50) NOT NULL',
        ],
        'DBPullPage' => [ // Bandaid table to help Neon - To be removed post-neon
            'RegistrationID' => 'INT UNSIGNED NOT NULL PRIMARY KEY',  // 1:1 mapping of the Registrations Primary Key
            'Page' => 'INT UNSIGNED NOT NULL',
        ],
        'DeepLinks' => [
            'LinkID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'AccountID' => 'INT UNSIGNED NOT NULL',
            'ApproverID' => 'INT UNSIGNED NOT NULL',
            'ApprovedValue' => 'TEXT NOT NULL',
            'Auth' => 'VARCHAR(40) NOT NULL',
            'Date' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'Description' => 'VARCHAR(100) NOT NULL',
            'Type' => 'VARCHAR(10) NOT NULL',
        ],
        'Departments' => [
            'DepartmentID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'Name' => 'VARCHAR(50) NOT NULL',
            'ParentDepartmentID' => 'INT UNSIGNED NOT NULL',
            'FallbackID' => 'INT UNSIGNED UNIQUE',
        ],
        'ElegibleVoters' => [
            'VoterRecordID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'AccountID' => 'INT UNSIGNED NOT NULL',
            'AnnualCycleID' => 'INT UNSIGNED NOT NULL',
        ],
        'EMails' => [
            'EMailAliasID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'DepartmentID' => 'INT UNSIGNED NOT NULL',
            'IsAlias' => 'BOOLEAN',
            'EMail' => 'VARCHAR(100) NOT NULL',
        ],
        'TempEventPage' => [
            'AccountID' => 'INT UNSIGNED NOT NULL',
            'PageFound' => 'INT UNSIGNED NOT NULL',
        ],
        'Events' => [
            'EventID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'AnnualCycleID' => 'INT UNSIGNED NOT NULL',
            'DateFrom' => 'DATE NOT NULL',
            'DateTo' => 'DATE NOT NULL',
            'EventName' => 'VARCHAR(50) NOT NULL',
        ],
        'HourRedemptions' => [
            'ClaimID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'AccountID' => 'INT UNSIGNED NOT NULL',
            'EventID' => 'INT UNSIGNED NOT NULL',
            'PrizeID' => 'INT UNSIGNED NOT NULL',
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
        ],
        'RewardGroup' => [
            'RewardGroupID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'RedeemLimit' => 'INT UNSIGNED',
        ],
        'VolunteerHours' => [
            'HourEntryID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'AccountID' => 'INT UNSIGNED NOT NULL',
            'ActualHours' => 'FLOAT(5,3) NOT NULL',
            'AuthorizedByID' => 'INT UNSIGNED NOT NULL',
            'DepartmentID' => 'INT UNSIGNED NOT NULL',
            'EndDateTime' => 'DATETIME NOT NULL',
            'EnteredByID' => 'INT UNSIGNED NOT NULL',
            'EventID' => 'INT UNSIGNED NOT NULL',
            'TimeModifier' => 'FLOAT(2,1) NOT NULL',
        ],
        'VolunteerRewards' => [
            'PrizeID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'Name' => 'VARCHAR(50) NOT NULL',
            'Promo' => 'BOOLEAN',
            'RewardGroupID' => 'INT UNSIGNED',
            'TotalInventory' => 'INT NOT NULL',
            'Value' => 'DECIMAL(5,2) NOT NULL',
        ],
        'Authentication' => [
            'AccountID' => 'INT UNSIGNED NOT NULL PRIMARY KEY',
            'Authentication' => 'VARCHAR(110)',
            'LastLogin' => 'DATETIME',
            'Expires' => 'DATETIME',
            'FailedAttempts' => 'INT UNSIGNED NOT NULL DEFAULT 0',
            'OneTime' => 'VARCHAR(110)',
            'OneTimeExpires' => 'DATETIME',
        ],
        'Members' => [
            'AccountID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'FirstName' => 'VARCHAR(50)',
            'MiddleName' => 'VARCHAR(50)',
            'LastName' => 'VARCHAR(50)',
            'Suffix' => 'VARCHAR(50)',
            'Login' => 'VARCHAR(50) UNIQUE',
            'Email' => 'VARCHAR(50)',
            'Email2' => 'VARCHAR(50)',
            'Email3' => 'VARCHAR(50)',
            'Phone' => 'VARCHAR(50)',
            'Phone2' => 'VARCHAR(50)',
            'AddressLine1' => 'VARCHAR(200)',
            'AddressLine2' => 'VARCHAR(200)',
            'AddressCity' => 'VARCHAR(200)',
            'AddressState' => 'VARCHAR(50)',
            'AddressZipCode' => 'VARCHAR(20)',
            'AddressZipCodeSuffix' => 'VARCHAR(20)',
            'AddressCountry' => 'VARCHAR(60)',
            'AddressProvince' => 'VARCHAR(50)',
            'Deceased' => 'BOOLEAN',
            'DoNotContact' => 'BOOLEAN',
            'EmailOptOut' => 'BOOLEAN',
            'Birthdate' => 'DATE',
            'Gender' => 'VARCHAR(50)',
            /* from custom fields */
            'PreferredFirstName' => 'VARCHAR(50)',
            'PreferredLastName' => 'VARCHAR(50)',
            'DisplayPhone' => 'BOOLEAN',
        ],
        'EmailLists' => [
            'EmailListID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'Name' => 'VARCHAR(100) NOT NULL',
            'Description' => 'TEXT NOT NULL',
            'Code' => 'TEXT',
        ],
        'EmailListAccess' => [
            'DepartmentID' => 'INT UNSIGNED NOT NULL',
            'PositionID' => 'INT UNSIGNED NOT NULL',
            'EmailListID' => 'INT UNSIGNED NOT NULL',
            'EditList' => 'BOOLEAN NOT NULL',
            'ChangeAccess' => 'BOOLEAN NOT NULL',
        ],
        'ConComPermissions' => [
            'PermissionID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'Position' => 'VARCHAR(100) NOT NULL',
            'Permission' => 'VARCHAR(100) NOT NULL',
            'Note' => 'TEXT'
        ]

    ];

    public static $DB_foreignKeys = [
        'BadgeTypes' => [
            'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],
        'ConComList' => [
            'DepartmentID' => 'Departments (DepartmentID) ON DELETE RESTRICT ON UPDATE CASCADE',
            'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
            'PositionID' => 'ConComPositions (PositionID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],
        'DBPullPage' => [
            'RegistrationID' => 'Registrations (RegistrationID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],
        'Departments' => [
            'ParentDepartmentID' => 'Departments (DepartmentID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],
        'ElegibleVoters' => [
            'AnnualCycleID' => 'AnnualCycles (AnnualCycleID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],
        'EMails' => [
            'DepartmentID' => 'Departments (DepartmentID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],
        'Events' => [
            'AnnualCycleID' => 'AnnualCycles (AnnualCycleID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],
        'HourRedemptions' => [
            'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
            'PrizeID' => 'VolunteerRewards (PrizeID) ON DELETE RESTRICT ON UPDATE CASCADE',
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
        ],
        'VolunteerHours' => [
            'DepartmentID' => 'Departments (DepartmentID) ON DELETE RESTRICT ON UPDATE CASCADE',
            'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],
        'VolunteerRewards' => [
            'RewardGroupID' => 'RewardGroup (RewardGroupID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],
    ];

    public static $DB_primaryKeys = [
        'TempEventPage' => ['AccountID', 'PageFound'],
        'EmailListAccess' => ['DepartmentID', 'PositionID', 'EmailListID'],
    ];

    public static $DB_index = [
        'ConComPermissions' => ['Permission'],
    ];
}
