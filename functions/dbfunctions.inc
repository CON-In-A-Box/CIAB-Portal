<?php

// Standard DB Functions
$AUTHENTICATIONTABLE="Authentication";
$SYSTEMLOGTABLE="SystemLog";

// Pulled from NeonCRM API
$CUSTOMFIELDS="CustomFields";
$ATTENDEETABLE="EventAttendees";

// Open the Great DB Of Knowledge!
function OpenDBConnection() {
  global $DBHOST, $DBUSER, $DBNAME, $DBPASS;
  $DBHandle=mysql_connect($DBHOST, $DBUSER, $DBPASS) or die("Could not connect to Database " . $DBHOST . " as " . $DBUSER . ".  Aborting: " . mysql_error());
  mysql_select_db($DBNAME) or die ("Could not select database " . $DBNAME . ".  Aborting: " . mysql_error());
  return $DBHandle;
}

function run_query($query) {
  $result=mysql_query($query);

  if (!$result) {
    if (mysql_errno()==1146) { // Error for missing table
      $oquery=$query; // Save this query

      // Attempt to rebuild tables.... maybe this is our first run?
      require($GLOBALS['FUNCTIONDIR'] . "/populatestatictabledata.inc");

      // Reattempt original query
      $result=mysql_query($oquery) or die("Could not successfully run query ($oquery) from DB: " . mysql_error());
    } else {
      die("Could not successfully run query ($query) from DB: " . mysql_error());
    }
  }

  return $result;
}

function MakeLog($Entry) {
  if (isset($_SESSION['username']) && $_SESSION['username']) {
    $User=$_SESSION['username'];
  } elseif (isset($_REQUEST['user']) && $_REQUEST['user']) {
    $User=$_REQUEST['user'];
  } else {
    $User="Public User";
  }

  $Entry=date("r") . " - " . $User . ":" . $Entry;

  $query="INSERT INTO " . $GLOBALS['SYSTEMLOGTABLE'] . " (LogEntry) VALUES('" . mysql_real_escape_string($Entry) . "')";
  run_query($query);
}

// Get a DB Handle (since we almost ALWAYS need it)
if(empty($DBHandle) || !$DBHandle) {
  $DBHandle=OpenDBConnection();
}

?>
