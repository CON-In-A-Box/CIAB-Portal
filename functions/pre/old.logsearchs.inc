<?php
function count_logsearch ($tags, $excl, $dept, $active) {
  $query="SELECT COUNT(MasterEntryID) FROM " . $GLOBALS['LOGSTABLE'];

  if (!empty($tags)) {
    if (empty($search)) {
      $search = " WHERE";
    } else {
      $search .= " AND";
    }
    $search .= " Tags & " . $tags;
  }

  if (!empty($excl)) {
    if (empty($search)) {
      $search = " WHERE";
    } else {
      $search .= " AND";
    }
    $search .= " NOT Tags & " . $excl;
  }

  if (!empty($dept)) {
    if (empty($search)) {
      $search = " WHERE";
    } else {
      $search .= " AND";
    }
    $search .= " InterestedDepartments & " . $dept;
  }

  if (empty($active)) {
    if (empty($search)) {
      $search = " WHERE";
    } else {
      $search .= " AND";
    }
    $search .= " Active IS FALSE";
  } elseif ( $active == TRUE ) {
    if (empty($search)) {
      $search = " WHERE";
    } else {
      $search .= " AND";
    }
    $search .= " Active IS TRUE";
  }

  if (!empty($search)) {
    $query .= $search;
  }

  $result=run_query($query);
  $rows=mysql_fetch_array($result);
  return($rows[0]);
}

function do_logsearch ($tags, $excl, $dept, $active, $reverse, $noupdatebutton) {
  $query="SELECT MasterEntryID FROM " . $GLOBALS['LOGSTABLE'];

  if (!empty($tags)) {
    $search = " WHERE";
    $search .= " Tags & " . $tags;
  }

  if (!empty($excl)) {
    if (empty($search)) {
      $search = " WHERE";
    } else {
      $search .= " AND";
    }
    $search .= " NOT Tags & " . $excl;
  }

  if (!empty($dept)) {
    if (empty($search)) {
      $search = " WHERE";
    } else {
      $search .= " AND";
    }
    $search .= " InterestedDepartments & " . $dept;
  }

  if (empty($active)) {
    if (empty($search)) {
      $search = " WHERE";
    } else {
      $search .= " AND";
    }
    $search .= " Active IS FALSE";
  } elseif ( $active != "EITHER" ) {
    if (empty($search)) {
      $search = " WHERE";
    } else {
      $search .= " AND";
    }
    $search .= " Active IS TRUE";
  }

  if (!empty($search)) {
    $query .= $search;
  }

  if (!empty($reverse)) {
    $query .= " ORDER BY LASTUPDATE DESC";
  } else {
    $query .= " ORDER BY LASTUPDATE ASC";
  }

  $result=run_query($query);

  while ($row=mysql_fetch_array($result)) {
    print_log($row[0], $noupdatebutton);
  }
  // Need some logic to handle paging
  return mysql_num_rows($result);
}

function do_logtextsearch ($text, $noupdatebutton=FALSE) {
  $query="SELECT MasterEntryID FROM " . $GLOBALS['LOGENTRIESTABLE'] . " WHERE LogEntry LIKE '%" . $text . "%' ORDER BY Created ASC";
  $results=run_query($query);
  
  $entries=array();
  while ($row = mysql_fetch_array($results)) {
    $entries[] = $row[0];
  }

  $printable = array_unique($entries);

  foreach ($printable as $log) {
    print_log($log, $noupdatebutton);
  }
  
  return mysql_num_rows($results);
}

function do_logtimesearch ($hours, $noupdatebutton=FALSE) {
  $timestamp = time() - (60 * 60 * $hours);
  
  $query="SELECT MasterEntryID FROM " . $GLOBALS['LOGSTABLE'] . " WHERE LASTUPDATE > " . $timestamp . " ORDER BY LASTUPDATE ASC";
  $results=run_query($query);
    
  while ($row=mysql_fetch_array($results)) {
    print_log($row[0], $noupdatebutton);
  }
  
  return mysql_num_rows($results);
}

// Doing this outside of the fuction incase of printing many log entries so we don't slam database with repetative queries
$query="SELECT SUM(Tag) FROM $LOGTAGSTABLE WHERE SecureView=TRUE";
$result=run_query($query);
$SecureLogTags=mysql_fetch_row($result);

function print_log ($id, $noupdatebutton=FALSE) {
  $query="SELECT Tags FROM " . $GLOBALS['LOGSTABLE'] . " WHERE MasterEntryID=" . $id;
  $result=run_query($query);
  $LogsTags=mysql_fetch_row($result);

  if ($GLOBALS['SecureLogTags'] & $LogsTags && empty($_SESSION['administrator'])) {
    return; // If this is a secured entry and user is not admin, don't display
  }
  
  if (empty($noupdatebutton)) { // Allow for no decoration in case of printing
    echo "<div class='update_button'>\n";
    echo "<a href=\"index.php?Function=log&UpdateID=" . $id . "\" title=\"Update this log entry\">Update</a>\n";
    echo "</div><!--closes update_button div-->\n";
  }


  $query="SELECT LogEntry from " . $GLOBALS['LOGENTRIESTABLE'] . " WHERE MasterEntryID=" . $id . " ORDER BY Created ASC";
  $results=run_query($query);
  require($GLOBALS['PAGESDIR'] . "/base/printlogentry.inc");
}
?>
