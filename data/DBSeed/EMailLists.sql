INSERT INTO EmailLists (Name, Description, Code) VALUES ('All', 'All Members' ,NULL);
INSERT INTO EmailLists (Name, Description, Code) VALUES ('All ConCom ', 'All ConCom for all events' ,'`AccountID` IN ( SELECT `AccountID` FROM `ConComList` WHERE `DepartmentID` > 0)');
INSERT INTO EmailLists (Name, Description, Code) VALUES ('${event} ConCom ', 'All ConCom for ${event}' ,'`AccountID` IN ( SELECT `AccountID` FROM `ConComList` WHERE `DepartmentID` > 0 AND `EventID` = ${event})');
INSERT INTO EmailLists (Name, Description, Code) VALUES ('All Volunteers', 'All volunteers for all events' ,'`AccountID` IN ( SELECT `AccountID` FROM `VolunteerHours`)');
INSERT INTO EmailLists (Name, Description, Code) VALUES ('${event} Volunteers', 'All volunteers for ${event}' ,'`AccountID` IN ( SELECT `AccountID` FROM `VolunteerHours` WHERE `EventID` = ${event})');
INSERT INTO EmailLists (Name, Description, Code) VALUES ('${event} Registered ', 'All Registered members for ${event}' ,'`AccountID` IN ( SELECT `AccountID` FROM `Registrations` WHERE `EventID` = ${event})');
