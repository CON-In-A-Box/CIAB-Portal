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
?>
</div><!--close main_content div-->
