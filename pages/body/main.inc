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
  $accountResult = $GLOBALS['Neon']->go($request);

  if ($accountResult['operationResult'] != 'SUCCESS') {
    die("Impossible error during account retrieval!"); // Impossible error - Throw exception and die
  }

// Get a list of Custom Fields so we can deal with ConCom Info
  $request = [
    'method' => 'common/listCustomFields',
    'parameters' => [
      'searchCriteria.component' => "Individual",
    ],
  ];
  $customResult = $GLOBALS['Neon']->go($request);

  if ($customResult['operationResult'] != 'SUCCESS') {
    die("Impossible error during custom fields!"); // Impossible error - Throw exception and die
  }

// Display all, with links to menu items

  echo "<p>Address on file:<br />\n";
  echo $accountResult['address'] . "<br />\n";
  echo $accountResult['address'] . "<br />\n";
  echo $accountResult['city'] . ", " . $accountResult['state'] . " " . $accountResult['zip'] . "</p>\n";

  echo "<p>Email on file: ";
  echo $accountResult['email'] . "</p>\n";

  echo "<p>Telephone Number on file: ";
  echo $accountResult['phone'] . "</p>\n";

  echo "<p>ConCom status:</p>\n";

// Look up Hotel info - FUTURE

// Look up Registration Status - FUTURE - but possible! :)

?>
</div><!--close main_content div-->
