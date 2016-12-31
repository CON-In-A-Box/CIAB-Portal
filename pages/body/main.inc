<div id="dynamic_content" class="main_screen">
<div id="left_nav" class="main_screen">

</div><!--left_nav-->
<div id="main_content">
<?php
  echo "<h2>Welcome " . $_SESSION['fullName'] . " - " . $_SESSION['accountId'] . "!</h2>";

// Lookup Account Info (Address, Email, Phone, ConCom Status/Custom Fields
  $request = [
    'method' => 'account/retrieveIndividualAccount',
    'parameters' => [
      'accountId' => $_SESSION['accountID'],
    ],
  ];
  $result = $GLOBALS['Neon']->go($request);

  if ($result['operationResult'] == 'SUCCESS') {
    return $result; ##==##
  } else {
    return FALSE;  // Impossible error - Throw exception and die
  }

// Get a list of Custom Fields so we can deal with ConCom Info

// Display all, with links to menu items

  echo "<p>Address on file:</p>\n";

  echo "<p>Email on file:</p>\n";
  
  echo "<p>Telephone Number on file:</p>\n";

  echo "<p>ConCom status:</p>\n";

// Look up Hotel info - FUTURE

// Look up Registration Status - FUTURE - but possible! :)

?>
</div><!--close main_content div-->
