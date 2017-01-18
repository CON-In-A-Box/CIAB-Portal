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
  if ( isset( $_SESSION['customFields']['currConComPos')) {
    echo "<ul>\n";
    foreach( $_SESSION['customFields']['currConComPos'] as $val ) {
      $tmp = explode($val, ",");
      echo "<li> " . $val[1] . " of " . $val[0]. "</li>\n";
    }
    echo "</ul></p>\n";
  } else {
    echo "Not currently on ConCom</p>\n";
  }
  
// Look up Hotel info - FUTURE

// Look up Registration Status - FUTURE - but possible! :)

?>
</div><!--close main_content div-->
