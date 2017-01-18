<div id="dynamic_content" class="main_screen">
<div id="left_nav" class="main_screen">

<?php
  echo "<img src=\"https://www.z2systems.com/neon/resource/ce/images/account/" . $_SESSION['accountId'] . "/0_medium.jpg\">\n";
  echo "<p style=\"font-weight:bold;\">" . $_SESSION['fullName'] . "</p>\n";

  echo "<p><span style=\"font-weight:bold;\">Address:</span><br />\n";
  echo $_SESSION['addr1'] . "<br />\n";
  if ( isset( $_SESSION['addr2'] ) && !empty( $_SESSION['addr2'] )) {
    echo $_SESSION['addr2'] . "<br />\n";
  }
  if ( isset( $_SESSION['addr3'] ) && !empty( $_SESSION['addr3'] )) {
    echo $_SESSION['addr3'] . "<br />\n";
  }
  if ( isset( $_SESSION['addr4'] ) && !empty( $_SESSION['addr4'] )) {
    echo $_SESSION['addr4'] . "<br />\n";
  }
  echo $_SESSION['city'] . ", ";
  echo $_SESSION['state'] . " ";
  echo $_SESSION['zip'] . "</p>\n";

  echo "<p><span style=\"font-weight:bold;\">Email:</span><br />";
  echo $_SESSION['email'] . "</p>\n";

  echo "<p><span style=\"font-weight:bold;\">Telephone Number:</span><br />";
  echo $_SESSION['phone'] . "</p>\n";
?>

</div><!--left_nav-->
<div id="main_content">
<?php
  echo "<h2>Welcome " . $_SESSION['fullName'] . "!</h2>";

  // Display all, with links to menu items

  echo "<p>Current ConCom Status:\n";
  if ( isset( $_SESSION['customFields']['currConComPos'])) {
    echo "<ul>\n";
    foreach( $_SESSION['customFields']['currConComPos'] as $val ) {
      $tmp = explode(",", $val);
      echo "<li> " . $tmp[1] . " of " . $tmp[0]. "</li>\n";
    }
    unset($val);
    echo "</ul></p>\n";
  } else {
    echo "Not currently on ConCom</p>\n";
  }
  
  echo "<p>2017 Meeting attendace:\n";
  echo "<ul>\n";
  foreach( $_SESSION['definedFields']['2017Meetings'] as $val ) {
    echo "<li>" . $val['name'] . " - ";
    if ( isset( $_SESSION['customFields'][$val['id']] ) && array_key_exists( $val['id'], $_SESSION['customFields'][$val['id']] )) {
      echo "Checked In";
    } else {
      echo "Not Checked In";
    }
    echo "</li>\n";
  }
  unset($val);
  echo "</ul></p>\n";
  
  
  echo "<p>Previous ConCom Years Served:\n";
  echo "<ul>\n";
  foreach( $_SESSION['definedFields']['conComYears'] as $val ) {
    echo "<li>" . $val['name'] . " - ";
    if ( isset( $_SESSION['customFields'][$val['id']] ) && array_key_exists( $val['id'], $_SESSION['customFields'][$val['id']] )) {
      echo "On ConCom";
    } else {
      echo "Not On ConCom";
    }
    echo "</li>\n";
  }
  unset($val);
  echo "</ul></p>\n";

// Look up Hotel info - FUTURE

// Look up Registration Status - FUTURE - but possible! :)

?>
</div><!--close main_content div-->
