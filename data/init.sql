/*
 * Set up the Initial Database for Us
 * CON-In-A-Box 2018 Thomas Keeley
 */

-- SHOW DATABASES;
-- SHOW TABLES;

CREATE TABLE 'ConComList' (
  'ListRecordID' INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  'AccountID' INT UNSIGNED NOT NULL,
      -- Taken from NeonCRM Currently
  'Department' INT UNSIGNED NOT NULL,
  'Position' INT UNSIGNED NOT NULL,
  'Note' VARCHAR(100),
  'ConventionYear' INT UNSIGNED NOT NULL
);

CREATE TABLE 'ConComPositions' (
  'PositionID' INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  'Name' VARCHAR(50) NOT NULL
);

INSERT INTO 'ConComPositions' ('Name') VALUES ('Head');
INSERT INTO 'ConComPositions' ('Name') VALUES ('Sub-Head');
INSERT INTO 'ConComPositions' ('Name') VALUES ('Specialist');

CREATE TABLE 'ConventionYear' (
  'YearID' INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  'Name' VARCHAR(50) NOT NULL
);

INSERT INTO 'ConventionYear' ('Name') VALUES ('1999');
INSERT INTO 'ConventionYear' ('Name') VALUES ('2000');
INSERT INTO 'ConventionYear' ('Name') VALUES ('2001');
INSERT INTO 'ConventionYear' ('Name') VALUES ('2002');
INSERT INTO 'ConventionYear' ('Name') VALUES ('2003');
INSERT INTO 'ConventionYear' ('Name') VALUES ('2004');
INSERT INTO 'ConventionYear' ('Name') VALUES ('2005');
INSERT INTO 'ConventionYear' ('Name') VALUES ('2006');
INSERT INTO 'ConventionYear' ('Name') VALUES ('2007');
INSERT INTO 'ConventionYear' ('Name') VALUES ('2008');
INSERT INTO 'ConventionYear' ('Name') VALUES ('2009');
INSERT INTO 'ConventionYear' ('Name') VALUES ('2010');
INSERT INTO 'ConventionYear' ('Name') VALUES ('2011');
INSERT INTO 'ConventionYear' ('Name') VALUES ('2012');
INSERT INTO 'ConventionYear' ('Name') VALUES ('2013');
INSERT INTO 'ConventionYear' ('Name') VALUES ('2014');
INSERT INTO 'ConventionYear' ('Name') VALUES ('2015');
INSERT INTO 'ConventionYear' ('Name') VALUES ('2016');
INSERT INTO 'ConventionYear' ('Name') VALUES ('2017');
INSERT INTO 'ConventionYear' ('Name') VALUES ('2018');

CREATE TABLE 'Departments' (
  'DepartmentID' INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  'Name' VARCHAR(50) NOT NULL,
  'Division' INT UNSIGNED NOT NULL,
);

INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Accessibility and Inclusion', 'External Relations and Communications');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Allocations', 'Hospitality');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Archives', 'Administration');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Artist Alley', 'External Relations and Communications');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Art Show', 'Administration');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Book Swap', 'Activities');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Ceremonies', 'Productions');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('CFO', 'Corporate Staff');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Cinema Rex', 'Activities');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('CoF2E2', 'Hospitality');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Connies Quantum Sandbox', 'Activities');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('ConSuite', 'Hospitality');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Creative Services', 'External Relations and Communications');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('CVG-TV', 'Productions');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Dealers Room', 'External Relations and Communications');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Decor', 'Facilities');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Dock', 'Facilities');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Exhibits', 'Activities');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Finance', 'Systems');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('First Advisors', 'Hospitality');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Future Visioning', 'Committees');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Gaming', 'Activities');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Guest Search', 'Committees');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Guests of Honor', 'Hospitality');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Harmonic Convergence', 'Productions');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Hotel', 'Facilities');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Invited Participants', 'Activities');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('IT', 'Systems');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Logistics', 'Facilities');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('MainStage', 'Productions');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Masquerade', 'Productions');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Meeting Childcare', 'Facilities');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Member Advocates', 'Committees');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Merchandise', 'Administration');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Nerf Herders', 'Facilities');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Operations', 'Hospitality');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Partner Management', 'Administration');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Partner Search', 'Committees');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Photography', 'External Relations and Communications');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('President', 'Corporate Staff');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Programming', 'Activities');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Publications', 'External Relations and Communications');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Registration', 'Administration');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Resume', 'Facilities');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Room Parties', 'Hospitality');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Secretary', 'Corporate Staff');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Smokers Paradise', 'Hospitality');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Social Media', 'External Relations and Communications');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Space Lounge', 'Hospitality');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Teen Room', 'Activities');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Theater Nippon', 'Activities');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Transportation', 'Facilities');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Treasurer', 'Corporate Staff');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Vice-President', 'Corporate Staff');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Video', 'Productions');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Volunteer Den', 'Hospitality');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Volunteers', 'Administration');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Webteam', 'External Relations and Communications');
INSERT INTO 'Departments' ('Name', 'Division') VALUES ('Youth Programming Advisory', 'Committees');

CREATE TABLE 'Divisions' (
  'DivisionID' INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  'Name' VARCHAR(50) NOT NULL,
);

INSERT INTO 'Divisions' ('Name') VALUES ('Activities');
INSERT INTO 'Divisions' ('Name') VALUES ('Administration');
INSERT INTO 'Divisions' ('Name') VALUES ('External Relations and Communications');
INSERT INTO 'Divisions' ('Name') VALUES ('Facilities');
INSERT INTO 'Divisions' ('Name') VALUES ('Hospitality');
INSERT INTO 'Divisions' ('Name') VALUES ('Productions');
INSERT INTO 'Divisions' ('Name') VALUES ('Systems');
INSERT INTO 'Divisions' ('Name') VALUES ('Committees');
INSERT INTO 'Divisions' ('Name') VALUES ('Corporate Staff');


CREATE TABLE 'ElegibleVoters' (
  'VoterRecordID' INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  'AccountID' INT UNSIGNED NOT NULL,
  'ConventionYear' INT UNSIGNED NOT NULL
);

CREATE TABLE 'EMailAliases' (
  'EMailID' INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  'Department' VARCHAR(50) NOT NULL,
  'EMail' VARCHAR(100) NOT NULL
);

INSERT INTO 'EMailAliases' ('Department', 'EMail') VALUES ('Accessibility and Inclusion', 'asl@convergence-con.org');
INSERT INTO 'EMailAliases' ('Department', 'EMail') VALUES ('Gaming', 'gaming-board@convergence-con.org');
INSERT INTO 'EMailAliases' ('Department', 'EMail') VALUES ('Gaming', 'gaming-card@convergence-con.org');
INSERT INTO 'EMailAliases' ('Department', 'EMail') VALUES ('Gaming', 'gaming-computer@convergence-con.org');
INSERT INTO 'EMailAliases' ('Department', 'EMail') VALUES ('Gaming', 'gaming-consuite@convergence-con.org');
INSERT INTO 'EMailAliases' ('Department', 'EMail') VALUES ('Gaming', 'gaming-rpg@convergence-con.org');
INSERT INTO 'EMailAliases' ('Department', 'EMail') VALUES ('IT', 'shiftboard@convergence-con.org');
INSERT INTO 'EMailAliases' ('Department', 'EMail') VALUES ('Publications', 'advertising@convergence-con.org');

CREATE TABLE 'HourRedemptions' (
  'ClaimID' INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  'AccountID' INT UNSIGNED NOT NULL,
  'PrizeId' INT UNSIGNED NOT NULL,
  'ConventionYear' INT UNSIGNED NOT NULL
);

CREATE TABLE 'MeetingAttendance' (
  'AttendanceRecordID' INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  'AccountID' INT UNSIGNED NOT NULL,
  'MeetingID' INT UNSIGNED NOT NULL
);

CREATE TABLE 'OfficialMeetings' (
  'MeetingID' INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  'Name' VARCHAR(50) NOT NULL,
  'Date' DATE NOT NULL,
  'ConventionYear' INT UNSIGNED NOT NULL
);

CREATE TABLE 'RewardGroup' (
  'GroupID' INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  'Limit' INT UNSIGNED
);

CREATE TABLE 'VolunteerHours' (
  'HourEntryID' INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  'AccountID' INT UNSIGNED NOT NULL,
  'ActualHours' FLOAT(5,3) NOT NULL
  'EndDateTime' DATETIME NOT NULL,
  'TimeModifier' FLOAT(2,1) NOT NULL
  'DepartmentWorked' INT UNSIGNED NOT NULL,
  'EnteredBy' INT UNSIGNED NOT NULL,
  'AuthorizedBy' INT UNSIGNED NOT NULL,
  'ConventionYear' INT UNSIGNED NOT NULL
);

CREATE TABLE 'VolunteerRewards' (
  'PrizeID' INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  'Name' VARCHAR(50) NOT NULL,
  'Value' DECIMAL(5,2) NOT NULL,
  'Promo' BOOLEAN,
  'Group' INT UNSIGNED,
  'TotalInventory' INT NOT NULL
);

