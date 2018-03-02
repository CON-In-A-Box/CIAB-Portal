<?php

// CON-In-A-Box DB Schema
// 2018 Thomas Keeley

// The Current CIAB DB Schema in an array for checking and applying

class SCHEMA
{
  public static $REQUIED_DB_SCHEMA = 2018030200; // Current DB Version - YYYYMMDDvv format (vv=daily counter form 00)

  public static $DB_tables = [
    'Configuration' => [
        'Field' => 'VARCHAR(15) NOT NULL PRIMARY KEY',
        'Value' => 'VARCHAR(100) NOT NULL',
    ],
    'ConComList' => [
        'ListRecordID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'AccountID' => 'INT UNSIGNED NOT NULL', // Taken from NeonCRM Currently
        'DepartmentID' => 'INT UNSIGNED NOT NULL',
        'PositionID' => 'INT UNSIGNED NOT NULL',
        'Note' => 'VARCHAR(100)',
        'YearID' => 'INT UNSIGNED NOT NULL',
        'FOREIGN KEY (DepartmentID)' => 'REFERENCES Departments (DepartmentID) ON DELETE RESTRICT ON UPDATE CASCADE',
        'FOREIGN KEY (PositionID)' => 'REFERENCES ConComPositions (PositionID) ON DELETE RESTRICT ON UPDATE CASCADE',
        'FOREIGN KEY (YearID)' => 'REFERENCES ConventionYear (YearID) ON DELETE RESTRICT ON UPDATE CASCADE',
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
        'ParentDepartmentID' => 'INT UNSIGNED NOT NULL',
        'FOREIGN KEY (ParentDepartmentID)' => 'REFERENCES Departments (DepartmentID) ON DELETE RESTRICT ON UPDATE CASCADE',
    ],
    'ElegibleVoters' => [
        'VoterRecordID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'AccountID' => 'INT UNSIGNED NOT NULL',
        'YearID' => 'INT UNSIGNED NOT NULL',
        'FOREIGN KEY (YearID)' => 'REFERENCES ConventionYear (YearID) ON DELETE RESTRICT ON UPDATE CASCADE',
    ],
    'EMails' => [
        'EMailAliasID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'DepartmentID' => 'INT UNSIGNED NOT NULL',
        'IsAlias' => 'BOOLEAN',
        'EMail' => 'VARCHAR(100) NOT NULL',
        'FOREIGN KEY (DepartmentID)' => 'REFERENCES Departments (DepartmentID) ON DELETE RESTRICT ON UPDATE CASCADE',
    ],
    'HourRedemptions' => [
        'ClaimID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'AccountID' => 'INT UNSIGNED NOT NULL',
        'PrizeID' => 'INT UNSIGNED NOT NULL',
        'YearID' => 'INT UNSIGNED NOT NULL',
        'FOREIGN KEY (PrizeID)' => 'REFERENCES VolunteerRewards (PrizeID) ON DELETE RESTRICT ON UPDATE CASCADE',
        'FOREIGN KEY (YearID)' => 'REFERENCES ConventionYear (YearID) ON DELETE RESTRICT ON UPDATE CASCADE',
    ],
    'MeetingAttendance' => [
        'AttendanceRecordID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'AccountID' => 'INT UNSIGNED NOT NULL',
        'MeetingID' => 'INT UNSIGNED NOT NULL',
        'FOREIGN KEY (MeetingID)' => 'REFERENCES OfficialMeetings (MeetingID) ON DELETE RESTRICT ON UPDATE CASCADE',
    ],
    'OfficialMeetings' => [
        'MeetingID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'Name' => 'VARCHAR(50) NOT NULL',
        'Date' => 'DATE NOT NULL',
        'YearID' => 'INT UNSIGNED NOT NULL',
        'FOREIGN KEY (YearID)' => 'REFERENCES ConventionYear (YearID) ON DELETE RESTRICT ON UPDATE CASCADE',
    ],
    'RewardGroup' => [
        'RewardGroupID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'RedeemLimit' => 'INT UNSIGNED',
    ],
    'VolunteerHours' => [
        'HourEntryID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'AccountID' => 'INT UNSIGNED NOT NULL',
        'ActualHours' => 'FLOAT(5,3) NOT NULL',
        'EndDateTime' => 'DATETIME NOT NULL',
        'TimeModifier' => 'FLOAT(2,1) NOT NULL',
        'DepartmentID' => 'INT UNSIGNED NOT NULL',
        'EnteredByID' => 'INT UNSIGNED NOT NULL',
        'AuthorizedByID' => 'INT UNSIGNED NOT NULL',
        'YearID' => 'INT UNSIGNED NOT NULL',
        'FOREIGN KEY (DepartmentID)' => 'REFERENCES Departments (DepartmentID) ON DELETE RESTRICT ON UPDATE CASCADE',
        'FOREIGN KEY (YearID)' => 'REFERENCES ConventionYear (YearID) ON DELETE RESTRICT ON UPDATE CASCADE',
    ],
    'VolunteerRewards' => [
        'PrizeID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'Name' => 'VARCHAR(50) NOT NULL',
        'Value' => 'DECIMAL(5,2) NOT NULL',
        'Promo' => 'BOOLEAN',
        'RewardGroupID' => 'INT UNSIGNED',
        'TotalInventory' => 'INT NOT NULL',
        'FOREIGN KEY (RewardGroupID)' => 'REFERENCES RewardGroup (RewardGroupID) ON DELETE RESTRICT ON UPDATE CASCADE',
    ],
  ];
}
?>
