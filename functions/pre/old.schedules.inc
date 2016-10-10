<?php
// Can't this info be somehow moved to DB and made dynamic/configurable?

//Shift definitions, Use Appends for OnDuty board.  Use Append_LogIn for login positions to add to Staff base
$ShiftSet=array("Administrators" => array("Ops_Head", "Ops_SubHead"),
                "Staff" => array("XO", "Dispatch_Officer", "Comm_Officer-1", "Comm_Officer-2", "Triage", "Gopher"),
                "Roaming" => array("Wandering_Host-1Floating", "Wandering_Host-2Parties", "Wandering_Host-3North Tower", "Wandering_Host-4South Tower", "Wandering_Host-5Parties", "Wandering_Host-6South Tower", "Wandering_Host-7Floating"));

$AppendSet=array("Administrators" => array("Exec_On_Duty", "Facilities"),
                  "Staff" => array(),
                  "Roaming" => array(),
                  "LogIn" => array("Ops_SubHead", "Ops_Head"));

// Days of the convention run (must be unique), definable incase we don't want "monday tuesday wednesday" etc
$DaysOfCon=array("Wednesday", "Thursday", "Friday", "Saturday", "Sunday", "Monday", "Tuesday");
// Breaks support future features
$ShiftBreaks=array("Morning" => array("TopHour" => "0",
                                      "BottomHour" => "12",
				      "First" => "TRUE",
				      "Last" => "FALSE",
				      "PreviousDay" => "TRUE",
				      "PreviousBreak" => "Evening",
				      "NextBreak" => "Evening"),
                   "Evening" => array("TopHour" => "12",
                                      "BottomHour" => "24",
				      "First" => "FALSE",
				      "Last" => "TRUE",
				      "PreviousDay" => "FALSE",
				      "PreviousBreak" => "Morning",
				      "NextBreak" => "Morning"));
?>
