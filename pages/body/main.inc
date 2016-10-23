<div id="dynamic_content" class="main_screen">
<div id="left_nav" class="main_screen">

</div><!--left_nav-->
<div id="main_content">
<?php
  echo "<h2>Welcome " . $_SESSION['fullName'] . " - " . $_SESSION['accountId'] . "!</h2>";

// Lookup Adddress info (including email)

// Lookup ConCom info (combine with previous?)

// Display all, with links to menu items

  echo "<p>Address on file:</p>\n";

  echo "<p>Email on file:</p>\n";
  
  echo "<p>Telephone Number on file:</p>\n";

  echo "<p>ConCom status:</p>\n";

// Look up Hotel info - FUTURE

// Look up Registration Status - FUTURE - but possible! :)

?>
</div><!--close main_content div-->
