<div id="dynamic_content" class="main_screen">
<div id="left_nav" class="main_screen">

</div><!--left_nav-->
<div id="main_content">
<?php
  echo "<h2>Welcome " . $_SESSION['fullName'] . " - " . $_SESSION['accountId'] . "!</h2>";

  // TEST
  echo "<pre>\n";
  print_r($_SESSION['accountInfo']['individualAccount']['customFieldDataList']);
  echo "\n\n";
  print_r($_SESSION['customFields']);
  echo "</pre>\n";

  // Display all, with links to menu items
  echo "<table width=100%><tr><td width=50%>\n";
    echo "<p>Address on file:<br />\n";
    echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['addressLine1'] . "<br />\n";
    if ( isset( $_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['addressLine2'] ) && !empty( $_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['addressLine2'] )) {
      echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['addressLine2'] . "<br />\n";
    }
    if ( isset( $_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['addressLine3'] ) && !empty( $_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['addressLine3'] )) {
      echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['addressLine3'] . "<br />\n";
    }
    if ( isset( $_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['addressLine4'] ) && !empty( $_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['addressLine4'] )) {
      echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['addressLine4'] . "<br />\n";
    }
    echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['city'] . ", ";
    echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['state']['code'] . " ";
    echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['zipCode'] . "</p>\n";
  echo "</td><td>\n";
    echo "<p>Email on file: ";
    echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['email1'] . "</p>\n";

    echo "<p>Telephone Number on file: ";
    echo $_SESSION['accountInfo']['individualAccount']['primaryContact']['phone1'] . "</p>\n";
  echo "</td></tr></table>\n";

  echo "<p>ConCom status:</p>\n";

// Look up Hotel info - FUTURE

// Look up Registration Status - FUTURE - but possible! :)

?>
</div><!--close main_content div-->
