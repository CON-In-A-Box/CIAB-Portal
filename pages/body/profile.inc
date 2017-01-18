<div id="dynamic_content" class="main_screen">
<div id="main_content">
<?php
  echo "<h2>Welcome " . $_SESSION['fullName'] . " - " . $_SESSION['accountId'] . "!</h2>";

  echo "<h3>Functionality coming soon</h3>\n";

  // TEST ONLY!!!!
  echo "<pre>\n";
//  echo "Defined Custom fields from Neon:\n";
//  print_r($_SESSION['definedFields']);
//  echo "\n\n";
//  echo "Returned Custom Field Data:\n";
//  print_r($_SESSION['accountInfo']['individualAccount']['customFieldDataList']);
//  echo "\n\n";
//  echo "Building process:\n";
//  foreach ($_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData'] as $val) {
//    print_r($val);
//  }
//  echo "\n\n";
//  echo "Parsed Custom Field Data:\n";
//  print_r($_SESSION['customFields']);

  echo "\n\nGit Status:\n";
  system("git pull");
  
  echo "</pre>\n";
  // END TEST
  
?>
</div><!--close main_content div-->
