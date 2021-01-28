<?php
// phpcs:disable Generic.WhiteSpace.ScopeIndent.IncorrectExact

// CON-In-A-Box DB Schema
// The Current CIAB DB Schema in an array for checking and applying

class SCHEMA
{

    public static $REQUIED_DB_SCHEMA = 2019121600; // Current DB Version - YYYYMMDDvv format (vv=daily counter form 00)

    public static $DB_tables = [
        'ActivityLog' => [
            'LogEntryID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'AccountID' => 'INT UNSIGNED NOT NULL', // Use 0 for System AccountID
            'Function' => 'VARCHAR(100) NOT NULL',
            'Query' => 'TEXT NOT NULL',
            'Date' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
        ],
        'ConfigurationOption' => [
            'Field' => 'VARCHAR(100) NOT NULL',
            'Name' => 'VARCHAR(100) NOT NULL'
        ],
        'ConfigurationTypes' => [
            'Type' => 'VARCHAR(100) NOT NULL PRIMARY KEY'
        ],
        'ConfigurationField' => [
            'Field' => 'VARCHAR(100) NOT NULL PRIMARY KEY',
            'TargetTable' => 'VARCHAR(100) NOT NULL',
            'Type' => 'VARCHAR(100) NOT NULL',
            'InitialValue' => 'TEXT NOT NULL',
            'Description' => 'TEXT NOT NULL'
        ],
        'AccountConfiguration' => [
            'AccountID' => 'INT UNSIGNED NOT NULL',
            'Field' => 'VARCHAR(100) NOT NULL',
            'Value' => 'TEXT NOT NULL'
        ],
        'Configuration' => [
            'Field' => 'VARCHAR(15) NOT NULL PRIMARY KEY',
            'Value' => 'TEXT NOT NULL',
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
            'dependentOnID' => 'INT UNSIGNED',
        ]

    ];

    public static $DB_foreignKeys = [
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
        'ConfigurationField' => [
            'Type' => 'ConfigurationTypes (Type) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],
        'AccountConfiguration' => [
            'AccountID' => 'Members (AccountID) ON DELETE RESTRICT ON UPDATE CASCADE',
            'Field' => 'ConfigurationField (Field) ON DELETE RESTRICT ON UPDATE CASCADE'
        ],
        'ConfigurationOption' => [
            'Field' => 'ConfigurationField (Field) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],
        'Members' => [
            'dependentOnID' => 'Members (AccountID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],

    ];

    public static $DB_primaryKeys = [
        'TempEventPage' => ['AccountID', 'PageFound'],
        'AccountConfiguration' => [ 'AccountID', 'Field'],
        'ConfigurationOption' => [ 'Field', 'Name']

    ];

    public static $DB_index = [
        'Members' => [
            'idx_Email' => ['Email'],
            'idx_FirstName' => ['FirstName'],
            'idx_LastName' => ['LastName']
        ]
    ];
}
