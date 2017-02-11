<div id="dynamic_content" class="main_screen">
<?php require($PAGESDIR . '/base/leftnav.inc'); ?>
<div id="main_content">
<?php
  echo "<h2>Welcome " . $_SESSION['preferredName'] . "!</h2>";

  // Display all, with links to menu items

  echo "<table>\n";
  if ( isset( $_SESSION['customFields']['currConComPos'])) {
    echo "<tr><th>Current ConCom Status</th></tr>\n";
    foreach( $_SESSION['customFields']['currConComPos'] as $val ) {
      $positionsHeld = explode("|", $val);
      foreach( $positionsHeld as $pos) {
        $tmp = explode(",", $pos);
        echo "<tr><td>" . $tmp[1] . " of " . $tmp[0];
        if ( !empty( $tmp[2] )) {
          echo " - Note: " . $tmp[2];
        }
        echo "</td></tr>\n";
      }
      unset($pos);
      unset($tmp);
    }
    unset($val);
    unset($positionsHeld);
  } else {
    echo "<tr><th>Not currently on ConCom</th></tr>\n";
  }
  echo "<tr><th>Need to update? - Coming soon</th></tr>\n";
  echo "</table>\n";
  echo "<p>&nbsp;</p>";
  
  echo "<table>\n";
  echo "<tr><th colspan=3>2017 Meeting Attendance</th></tr>\n";
  $nowDate = date("Ymd");
  $attendKey = array_search('2017 Official Meeting Attendance', $_SESSION['definedFields']);
  foreach( $_SESSION['definedFields']['2017Meetings'] as $key => $val ) {
    $ldate = explode("-", $val);
    $checkDate = $ldate[0] . $ldate[1] . rtrim($ldate[2]);
    if ( $nowDate >= $checkDate ) {
      echo "<tr><td>" . $ldate[1] . "/" . rtrim($ldate[2]) . "/" . $ldate[0]. "</td>";
      echo "<td>" . ltrim($ldate[3]) . "</td>";
      if ( isset( $_SESSION['customFields'][$attendKey]) && in_array( $key, $_SESSION['customFields'][$attendKey] )) {
        echo "<td style='background-color:green'>Checked In";
      } else {
        if ($nowDate == $checkDate) {
          echo "<td><a href='index.php?Function=signin&AttendField=" . $attendKey . "&DateFeed=" . $key . "'>Sign In Now!</a>";
        } else {
          echo "<td>Not Checked In";
        }
      }
      echo "</td></tr>\n";
    }
    unset($ldate);
    unset($checkDate);
  }
  unset($key);
  unset($val);
  unset($nowDate);
  echo "<th colspan=3>Missing Sign In Info? - Coming soon</th>\n";
  echo "</table>\n";
  echo "<p>&nbsp;</p>";
  
  echo "<table>\n";
  echo "<tr><th colspan=2>Previous ConCom Years Served</th></tr>";
  foreach( $_SESSION['definedFields']['conComYears'] as $key => $val ) {
    echo "<tr><td>" . $val . "</td>";
    if ( isset( $_SESSION['customFields'][array_search('CVGConCom Years Served', $_SESSION['definedFields'])] ) && in_array( $key, $_SESSION['customFields'][array_search('CVGConCom Years Served', $_SESSION['definedFields'])] )) {
      echo "<td style='background-color:green'>On ConCom";
    } else {
      echo "<td>Not On ConCom";
    }
    echo "</td></tr>\n";
  }
  unset($val);
  echo "<th colspan=2>Missing ConCom Years? - Coming soon</th>\n";
  echo "</table>\n";

?>
</div><!--close main_content div-->
