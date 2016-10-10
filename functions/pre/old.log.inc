<?php

if (!empty($_REQUEST['logentry_textarea'])) {
  if (!empty($_REQUEST['logentry_tag'])) {
    $LogTags=array_sum($_REQUEST['logentry_tag']);
  } else {
    $LogTags=0;
  }
  
  if (!empty($_REQUEST['department_tag'])) {
    $DepartmentTags=array_sum($_REQUEST['department_tag']);
  } else {
    $DepartmentTags=0;
  }

  if (empty($_REQUEST['UpdateID'])) {
    // Create New MasterEntryID
    $query="INSERT INTO " . $LOGSTABLE . " (Tags,InterestedDepartments,Active) VALUES($LogTags,$DepartmentTags," . $_REQUEST['logentry_activetag'] . ")";
    $result=run_query($query);

    $_REQUEST['UpdateID'] = mysql_insert_id();
  } else {
    // Update MasterEntryID entry
    $query="REPLACE INTO " . $LOGSTABLE . "  (MasterEntryID,Tags,InterestedDepartments,Active) VALUES(" . $_REQUEST['UpdateID'] . ",$LogTags,$DepartmentTags," . $_REQUEST['logentry_activetag'] . ")";
    $result=run_query($query);
  }

  $TextEntry=$_SESSION['FullName'] . " (" . $_SESSION['username'] . " - " . $_SESSION['position'] . ") at " . date("l, F d, Y H:i") . "\n\n";
  $TextEntry.=$_REQUEST['logentry_textarea'] . "\n";

  $Fields="MasterEntryID,UserName,LogEntry";
  $Data=$_REQUEST['UpdateID'] . ",'" . $_SESSION['username'] . "','" . mysql_real_escape_string(nl2br(htmlentities($TextEntry, ENT_QUOTES))) . "'";

  $query="INSERT INTO $LOGENTRIESTABLE ($Fields) VALUES($Data)";
  $result=run_query($query);
}
?>