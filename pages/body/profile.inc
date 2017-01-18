<div id="dynamic_content" class="main_screen">
<div id="left_nav" class="main_screen">

</div><!--left_nav-->
<div id="main_content">
<?php
  echo "<h2>Welcome " . $_SESSION['fullName'] . " - " . $_SESSION['accountId'] . "!</h2>";

  // TEST ONLY!!!!
  echo "<pre>\n";
  echo "Returned Custom Field Data:\n";
  print_r($_SESSION['accountInfo']['individualAccount']['customFieldDataList']);
  echo "\n\n";
  echo "Parsed Custom Field Data:\n";
  echo "\n\n";
  print_r($_SESSION['customFields']);

  echo "\n\nGit Status:\n";
  system("git pull");
  
  echo "</pre>\n";
  // END TEST
  
?>
</div><!--close main_content div-->
