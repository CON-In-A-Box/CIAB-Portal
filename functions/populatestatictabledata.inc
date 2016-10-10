<?php
// Build tables, and populate the static/default content

if (!empty($NOLOOP)) {
	// ACK!  We are looping!!!  Trying to write to an undefined table!
	die("Static Data file is self-referring!  " . __FILE__ . " Failed.  ($query) from DB: " . mysql_error());
}
else {
	// Set this to make sure that what we are about to do doesn't loop us into infinity.
	$NOLOOP=TRUE;
}

// Ops DB Structure
$HOTELALLOC_TABLES = [
  // SYSTEMLOGSTABLE needs to be the first table created as the create process posts to it on success (MakeLog)
  $SYSTEMLOGTABLE => "
    EntryID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    LogEntry TEXT,
    Created TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  ",
];


// Import Table from NeonCRM
$NEON_TABLES = [
/*
ATTENDEETABLE will probably be MAJORLY changed as we go, including bits, and "qualifications" for all other things
like IWAccess rooms, CON rooms, suites, direct assignments, etc
*/
  $ATTENDEETABLE => "
    ATTENDEEID INT NOT NULL PRIMARY KEY,
    WCACCESS BIT(1),
    25CONCOM BIT(1),
    HOTEL1 INT,
    HOTEL2 INT,
    HOTEL3 INT,
    BEDPREF INT,
    LASTCONTACT TIMESTAMP
  ",
  $CUSTOMFIELDS => "
    FIELDID INT NOT NULL PRIMARY KEY,
    FIELDLABEL TEXT
  "
];

// Combine all table structures
$DBTABLES=$HOTELALLOC_TABLES + $NEON_TABLES;

// Add all "static" content functions here (at least default vaules)

function PopulateStaticData ($table) {
  switch ($table) {
    case $GLOBALS['SYSTEMLOGTABLE']:
	    $EntryFields = "LogEntry";
      $EntryData = [
        "LastCronRun"
      ];

      foreach($EntryData as $line) {
        $query="REPLACE INTO " . $LOGTAGSTABLE . " (" . $EntryFields . ") VALUES(" . $line . ");";
        run_query($query);
      }

      MakeLog("Populated Static/Default data into " . $table . ": " . mysql_error());
      break;
    default:
      //MakeLog("No Static/Default data defined for " . $table); // Debug to system log
      break;
  }
}

// Run check and populate as needed
$Tables=[];
$result=run_query("SHOW TABLES");
while ($row=mysql_fetch_array($result)) {
	array_push($Tables, strtolower($row[0]));
}

foreach($DBTABLES as $Table => $Fields) {
	if (!in_array(strtolower($Table), $Tables)) {
		$query="CREATE TABLE ". $Table ." (" . $Fields . ");";
		run_query($query);
		MakeLog("Created -" . $Table . "- in Database");
		PopulateStaticData($Table);
	}
}

?>