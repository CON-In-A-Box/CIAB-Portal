<?php

/*.
    require_module 'standard';
.*/

namespace App\Database;

class DBSchema extends \App\Core\ModuleDBSchema
{

    private $tables = [
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
            'Pronouns' => 'VARCHAR(50)',
            /* from custom fields */
            'PreferredFirstName' => 'VARCHAR(50)',
            'PreferredLastName' => 'VARCHAR(50)',
            'DisplayPhone' => 'BOOLEAN',
            'dependentOnID' => 'INT UNSIGNED',
        ],
        'Registration_Configuration' => [
            'Field' => 'VARCHAR(30) NOT NULL PRIMARY KEY',
            'Value' => 'TEXT NOT NULL'
        ],
        'Announcements' => [
            'AnnouncementID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'DepartmentID' => 'INT UNSIGNED NOT NULL',
            'PostedBy' => 'INT UNSIGNED NOT NULL',
            'PostedOn' => 'DATE NOT NULL',
            'Scope' => 'INT UNSIGNED NOT NULL',
            'Text' => 'TEXT NOT NULL'
        ],
        'Deadlines' => [
            'DeadlineID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'DepartmentID' => 'INT UNSIGNED NOT NULL',
            'Deadline' => 'DATE NOT NULL',
            'Note' => 'TEXT NOT NULL',
            'Scope' => 'INT UNSIGNED',
            'PostedBy' => 'INT UNSIGNED'
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
            'BadgeID' => 'VARCHAR(10)',
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
        'Stores' => [
            'StoreID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'StoreSlug' => 'VARCHAR(16) NOT NULL UNIQUE',
            'Name' => 'VARCHAR(255) NOT NULL UNIQUE',
            'Description' => 'TEXT'
        ],
        'Products' => [
            'ProductID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'StoreID' => 'INT UNSIGNED NOT NULL',
            'ProductSlug' => 'VARCHAR(16) NOT NULL UNIQUE',
            'Name' => 'VARCHAR(255) NOT NULL UNIQUE',
            'Description' => 'TEXT',
            'UnitPriceCents' => 'INT NOT NULL DEFAULT 0',
            'PaymentSystemRef' => 'VARCHAR(255) UNIQUE'
        ],
        'Purchases' => [
          'PurchaseID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
          'StoreID' => 'INT UNSIGNED NOT NULL',
          'PaymentSystemRef' => 'VARCHAR(255) UNIQUE'
        ],
        'PurchaseLineItems' => [
            'PurchaseLineItemID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'PurchaseID' => 'INT UNSIGNED NOT NULL',
            'ProductID' => 'INT UNSIGNED NOT NULL',
            'PriceCents' => 'INT NOT NULL DEFAULT 0',
            'PaymentSystemRef' => 'VARCHAR(255) UNIQUE'
        ],

    ];

    private $foreignKeys = [
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
        'Announcements' => [
            'DepartmentID' => 'Departments (DepartmentID) ON DELETE RESTRICT ON UPDATE CASCADE',
            'PostedBy' => 'Members (AccountID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],
        'Deadlines' => [
            'DepartmentID' => 'Departments (DepartmentID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],
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
        ],
        'Products' => [
            'StoreID' => 'Stores (StoreID) ON DELETE RESTRICT ON UPDATE CASCADE'
        ],
        'Purchases' => [
            'StoreID' => 'Stores (StoreID) ON DELETE RESTRICT ON UPDATE CASCADE'
        ],
        'PurchaseLineItems' => [
            'PurchaseID' => 'Purchases (PurchaseID) ON DELETE RESTRICT ON UPDATE CASCADE',
            'ProductID' => 'Products (ProductID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],

    ];

    private $primaryKeys = [
        'TempEventPage' => ['AccountID', 'PageFound'],
        'AccountConfiguration' => [ 'AccountID', 'Field'],
        'ConfigurationOption' => [ 'Field', 'Name'],
        'EmailListAccess' => ['DepartmentID', 'PositionID', 'EmailListID']

    ];

    private $index = [
        'Members' => [
            'idx_Email' => ['Email'],
            'idx_FirstName' => ['FirstName'],
            'idx_LastName' => ['LastName']
        ],
        'Stores' => [
            'idx_StoreSlug' => ['StoreSlug'],
        ],
        'Products' => [
            'idx_ProductSlug' => ['ProductSlug'],
            'idx_PaymentSystemRef_on_Products' => ['PaymentSystemRef'],
        ],
        'Purchases' => [
            'idx_PaymentSystemRef_on_Purchases' => ['PaymentSystemRef'],
        ],
        'PurchaseLineItems' => [
            'idx_PaymentSystemRef_on_PurchaseLineItems' => ['PaymentSystemRef']
        ]

    ];

    private $seed = [
        'Configuration' => [
            [
                'index' => [
                    'Field' => 'CONHOST',
                ],
                'data' => [
                    'Value' => 'Convention Dev'
                ]
            ],
            [
                'index' => [
                    'Field' => 'ADMINEMAIL',
                ],
                'data' => [
                    'Value' => ''
                ]
            ],
            [
                'index' => [
                    'Field' => 'FEEDBACK_EMAIL',
                ],
                'data' => [
                    'Value' => ''
                ]
            ],
            [
                'index' => [
                    'Field' => 'SECURITY_EMAIL',
                ],
                'data' => [
                    'Value' => ''
                ]
            ],
            [
                'index' => [
                    'Field' => 'HELP_EMAIL',
                ],
                'data' => [
                    'Value' => ''
                ]
            ],
            [
                'index' => [
                    'Field' => 'NOREPLY_EMAIL',
                ],
                'data' => [
                    'Value' => ''
                ]
            ],
            [
                'index' => [
                    'Field' => 'ADMINACCOUNTS',
                ],
                'data' => [
                    'Value' => ''
                ]
            ],
            [
                'index' => [
                    'Field' => 'DUMPSECURE',
                ],
                'data' => [
                    'Value' => 'MPfKCJuEn7ZN2ypDc564NNXHAeGq6mc8dcNVYfXhg4FKq85d6K'
                ]
            ],
            [
                'index' => [
                    'Field' => 'TIMEZONE',
                ],
                'data' => [
                    'Value' => 'America/Chicago'
                ]
            ],
            [
                'index' => [
                    'Field' => 'DBSchemaVersion',
                ],
                'data' => [
                    'Value' => '0'
                ]
            ],
            [
                'index' => [
                    'Field' => 'CONCOMHOURS',
                ],
                'data' => [
                    'Value' => '60'
                ]
            ],
            [
                'index' => [
                    'Field' => 'PASSWORDEXPIRE',
                ],
                'data' => [
                    'Value' => '+1 year'
                ]
            ],
            [
                'index' => [
                    'Field' => 'MAXLOGINFAIL',
                ],
                'data' => [
                    'Value' => '5'
                ]
            ],
            [
                'index' => [
                    'Field' => 'PASSWORDRESET',
                ],
                'data' => [
                    'Value' => '+60 minutes'
                ]
            ]
        ],
        'ConfigurationTypes' => [
            [
                'index' => [ 'Type' => 'boolean' ],
                'data' => [],
            ],
            [
                'index' => [ 'Type' => 'text' ],
                'data' => [],
            ],
            [
                'index' => [ 'Type' => 'integer' ],
                'data' => [],
            ],
            [
                'index' => [ 'Type' => 'select' ],
                'data' => [],
            ],
            [
                'index' => [ 'Type' => 'list' ],
                'data' => [],
            ],
        ],
        'ConfigurationField' => [
            [
                'index' => [
                    'Field' => 'ADMINEMAIL',
                ],
                'data' => [
                    'TargetTable' => 'Configuration',
                    'Type' => 'text',
                    'InitialValue' => '',
                    'Description' => 'Email Address for Site ADMIN contact.'
                ]
            ],
            [
                'index' => [
                    'Field' => 'CONCOMHOURS',
                ],
                'data' => [
                    'TargetTable' => 'Configuration',
                    'Type' => 'integer',
                    'InitialValue' => 60,
                    'Description' => 'Volunteer hours given to Convention Staff.'
                ]
            ],
            [
                'index' => [
                    'Field' => 'ADMINACCOUNTS',
                ],
                'data' => [
                    'TargetTable' => 'Configuration',
                    'Type' => 'list',
                    'InitialValue' => '',
                    'Description' => 'List of account IDs who are site administrators.'
                ]
            ],
            [
                'index' => [
                    'Field' => 'CONHOST',
                ],
                'data' => [
                    'TargetTable' => 'Configuration',
                    'Type' => 'text',
                    'InitialValue' => '',
                    'Description' => 'Name of the orginization putting on the convention.'
                ]
            ],
            [
                'index' => [
                    'Field' => 'DISABLEDMODULES',
                ],
                'data' => [
                    'TargetTable' => 'Configuration',
                    'Type' => 'list',
                    'InitialValue' => '',
                    'Description' => 'List of modules disabled on the site.'
                ]
            ],
            [
                'index' => [
                    'Field' => 'FEEDBACK_EMAIL',
                ],
                'data' => [
                    'TargetTable' => 'Configuration',
                    'Type' => 'text',
                    'InitialValue' => '',
                    'Description' => 'Email Address for feedback on the site.'
                ]
            ],
            [
                'index' => [
                    'Field' => 'HELP_EMAIL',
                ],
                'data' => [
                    'TargetTable' => 'Configuration',
                    'Type' => 'text',
                    'InitialValue' => '',
                    'Description' => 'Email Address for help on the site.'
                ]
            ],
            [
                'index' => [
                    'Field' => 'MAXLOGINFAIL',
                ],
                'data' => [
                    'TargetTable' => 'Configuration',
                    'Type' => 'integer',
                    'InitialValue' => 5,
                    'Description' => 'Number of time login can fail before an account is locked.'
                ]
            ],
            [
                'index' => [
                    'Field' => 'NOREPLY_EMAIL',
                ],
                'data' => [
                    'TargetTable' => 'Configuration',
                    'Type' => 'text',
                    'InitialValue' => '',
                    'Description' => 'Email Address for no-reply messages.'
                ]
            ],
            [
                'index' => [
                    'Field' => 'PASSWORDEXPIRE',
                ],
                'data' => [
                    'TargetTable' => 'Configuration',
                    'Type' => 'text',
                    'InitialValue' => '+1 year',
                    'Description' => 'Time period after which an account authentication expires.'
                ]
            ],
            [
                'index' => [
                    'Field' => 'PASSWORDRESET',
                ],
                'data' => [
                    'TargetTable' => 'Configuration',
                    'Type' => 'text',
                    'InitialValue' => '+60 minutes',
                    'Description' => 'Time period after which a reset request expires.'
                ]
            ],
            [
                'index' => [
                    'Field' => 'SECURITY_EMAIL',
                ],
                'data' => [
                    'TargetTable' => 'Configuration',
                    'Type' => 'text',
                    'InitialValue' => '',
                    'Description' => 'Email Address for security messages.'
                ]
            ],
            [
                'index' => [
                    'Field' => 'TIMEZONE',
                ],
                'data' => [
                    'TargetTable' => 'Configuration',
                    'Type' => 'text',
                    'InitialValue' => 'America/Chicago',
                    'Description' => 'Timezone for the event.'
                ]
            ],
            [
                'index' => [
                    'Field' => 'col.primary',
                ],
                'data' => [
                    'TargetTable' => 'Configuration',
                    'Type' => 'text',
                    'InitialValue' => '#FFF',
                    'Description' => 'HTML Color key for primary site color.'
                ]
            ],
            [
                'index' => [
                    'Field' => 'col.prim-back',
                ],
                'data' => [
                    'TargetTable' => 'Configuration',
                    'Type' => 'text',
                    'InitialValue' => '#4CAF50',
                    'Description' => 'HTML Color key for primary site background color.'
                ]
            ],
            [
                'index' => [
                    'Field' => 'col.secondary',
                ],
                'data' => [
                    'TargetTable' => 'Configuration',
                    'Type' => 'text',
                    'InitialValue' => '#FFF',
                    'Description' => 'HTML Color key for secondary site color.'
                ]
            ],
            [
                'index' => [
                    'Field' => 'col.second-back',
                ],
                'data' => [
                    'TargetTable' => 'Configuration',
                    'Type' => 'text',
                    'InitialValue' => '#2196F3',
                    'Description' => 'HTML Color key for secondary site background color.'
                ]
            ],
            [
                'index' => [
                    'Field' => 'ASSET_BACKEND',
                ],
                'data' => [
                    'TargetTable' => 'Configuration',
                    'Type' => 'text',
                    'InitialValue' => 'file.inc',
                    'Description' => 'Asset Library Backend.'
                ]
            ],
            [
                'index' => [
                    'Field' => 'FILE_ASSET_DATA',
                ],
                'data' => [
                    'TargetTable' => 'Configuration',
                    'Type' => 'boolean',
                    'InitialValue' => 0,
                    'Description' => 'Asset path is not web accessable.'
                ]
            ],
            [
                'index' => [
                    'Field' => 'FILE_ASSET_PATH',
                ],
                'data' => [
                    'TargetTable' => 'Configuration',
                    'Type' => 'text',
                    'InitialValue' => 'resources/images',
                    'Description' => 'Asset data path.'
                ]
            ],
            [
                'index' => [
                    'Field' => 'RegistrationOpen',
                ],
                'data' => [
                    'TargetTable' => 'Registration_Configuration',
                    'Type' => 'integer',
                    'InitialValue' => 0,
                    'Description' => 'Scheduled hour on event days that online check-in opens (24-hour clock)'
                ]
            ],
            [
                'index' => [
                    'Field' => 'RegistrationClose',
                ],
                'data' => [
                    'TargetTable' => 'Registration_Configuration',
                    'Type' => 'integer',
                    'InitialValue' => 24,
                    'Description' => 'Scheduled hour on event days that online check-in closes (24-hour clock)'
                ]
            ],
            [
                'index' => [
                    'Field' => 'ForceOpen',
                ],
                'data' => [
                    'TargetTable' => 'Registration_Configuration',
                    'Type' => 'boolean',
                    'InitialValue' => 0,
                    'Description' => 'Online Check-In Registration is open. (Only valid during event days)'
                ]
            ],
            [
                'index' => [
                    'Field' => 'badgeNotice',
                ],
                'data' => [
                    'TargetTable' => 'Registration_Configuration',
                    'Type' => 'text',
                    'InitialValue' => '',
                    'Description' => 'Notice text presented at the top of the Checkin/Badge Pickup screen.'
                ]
            ],
            [
                'index' => [
                    'Field' => 'passInstructions',
                ],
                'data' => [
                    'TargetTable' => 'Registration_Configuration',
                    'Type' => 'text',
                    'InitialValue' => 'Please use this boarding pass to pick up your badge at registration.',
                    'Description' => 'Instructions for use of the boarding pass.'
                ]
            ],
            [
                'index' => [
                    'Field' => 'AnnounceEmail',
                ],
                'data' => [
                    'TargetTable' => 'AccountConfiguration',
                    'Type' => 'boolean',
                    'InitialValue' => 1,
                    'Description' => 'Receive new announcements via E-mail'
                ]
            ],

            [
                'index' => [
                    'Field' => 'G_CLIENT_SECRET',
                ],'data' => [
                    'TargetTable' => 'Configuration',
                    'Type' => 'text',
                    'InitialValue' => '',
                    'Description' => 'Google Documents client secret.'
                ]
            ],
            [
                'index' => [
                    'Field' => 'G_CLIENT_CRED',
                ],
                'data' => [
                    'TargetTable' => 'Configuration',
                    'Type' => 'text',
                    'InitialValue' => '',
                    'Description' => 'Google Documents client credentials.',
                ]
            ],
            [
                'index' => [
                    'Field' => 'G_ROOTFOLDER',
                ],
                'data' => [
                    'TargetTable' => 'Configuration',
                    'Type' => 'text',
                    'InitialValue' => '',
                    'Description' => 'Google Documents root folder.',
                ]
            ],
        ],
        'EmailLists' => [
            [
                'index' => [
                    'Name' => 'All',
                ],
                'data' => [
                    'Description' => 'All Members',
                    'Code' => null
                ]
            ],
            [
                'index' => [
                    'Name' => 'All ConCom',
                ],
                'data' => [
                    'Description' => 'All ConCom for all events',
                    'Code' => '`AccountID` IN ( SELECT `AccountID` FROM `ConComList` WHERE `DepartmentID` > 0)'
                ]
            ],
            [
                'index' => [
                    'Name' => '${event} ConCom',
                ],
                'data' => [
                    'Description' => 'All ConCom for ${event}',
                    'Code' => '`AccountID` IN ( SELECT `AccountID` FROM `ConComList` WHERE `DepartmentID` > 0 AND `EventID` = ${event})'
                ]
            ],
            [
                'index' => [
                    'Name' => 'All Volunteers',
                ],
                'data' => [
                    'Description' => 'All volunteers for all events',
                    'Code' => '`AccountID` IN ( SELECT `AccountID` FROM `VolunteerHours`)'
                ]
            ],
            [
                'index' => [
                    'Name' => '${event} Volunteers',
                ],
                'data' => [
                    'Description' => 'All volunteers for ${event}',
                    'Code' => '`AccountID` IN ( SELECT `AccountID` FROM `VolunteerHours` WHERE `EventID` = ${event})'
                ]
            ],
            [
                'index' => [
                    'Name' => '${event} Registered',
                ],
                'data' => [
                    'Description' => 'All Registered members for ${event}',
                    'Code' => '`AccountID` IN ( SELECT `AccountID` FROM `Registrations` WHERE `EventID` = ${event})'
                ]
            ],
        ],
        'Departments' => [
            [
                'index' => [
                    'DepartmentID' => '1',
                ],
                'data' => [
                    'Name' => 'Historical Placeholder',
                    'ParentDepartmentID' => '1'
                ]
            ]
        ]

    ];


    public function __construct($container)
    {
        parent::__construct(
            "core",
            $container->db,
            $this->tables,
            $this->foreignKeys,
            $this->primaryKeys,
            $this->index,
            $this->seed
        );

    }


    /* end */
}
