<?php require($PAGESDIR.'/base/leftnav.inc'); ?>
<div id="main_content"  class="w3-cell w3-cell-top w3-mobile">
<?php
echo "<h2>Welcome ".$_SESSION['preferredName']."!</h2>\n";

require_once($FUNCTIONDIR.'/users.inc');
/*
if( isset($_REQUEST['DoFeed'])) {
echo "<hr><h3>Starting DeFeed Routine:</h3>\n";
$lottery = search_definedFields('Lottery Status');
$status = array_search('Entered', $_SESSION['definedFields']['Lottery Status']);
$bedPref = search_definedFields('Bed Preference');
$noBed = array_search('No Preference', $_SESSION['definedFields']['Bed Preference']);
$kingBed = array_search('One King', $_SESSION['definedFields']['Bed Preference']);
$queenBed = array_search('Two Queens', $_SESSION['definedFields']['Bed Preference']);
$hotelPri = search_definedFields('Hotel Qualifier');
$volPri = array_search('Volunteer Qualifier', $_SESSION['definedFields']['Hotel Qualifier']);
$adaPri = array_search('ADA Wheelchair', $_SESSION['definedFields']['Hotel Qualifier']);
$adoPri = array_search('ADA Other', $_SESSION['definedFields']['Hotel Qualifier']);

foreach(file($FUNCTIONDIR . "/directadd.feed", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
//  $myTest = [ "3218,Thomas,Keeley,King Bed,Volunteer|ADO" ]; // TESTING
//  foreach($myTest as $line) { // TESTING
flush();
$feed = explode(",", $line);
$qual = explode("|", $feed[4]);

echo "Updating " . $feed[0] . " - " . $feed[1] . " " . $feed[2] . ":";

$request = [
'method' => 'account/retrieveIndividualAccount',
'parameters' => [
'accountId' => $feed[0],
],
];
$result = $GLOBALS['Neon']->go($request);

if ( isset( $result['operationResult'] ) && $result['operationResult'] == 'SUCCESS' ) {
$request = [
'method' => 'account/editIndividualAccount',
'parameters' => [
'individualAccount.accountId' => $result['individualAccount']['accountId'],
'individualAccount.primaryContact.contactId' => $result['individualAccount']['primaryContact']['contactId'],
'individualAccount.primaryContact.firstName' => $result['individualAccount']['primaryContact']['firstName'],
'individualAccount.primaryContact.lastName' => $result['individualAccount']['primaryContact']['lastName'],
],
];

foreach ($result['individualAccount']['customFieldDataList']['customFieldData'] as $key => $val) {
$request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldId[' . $key . ']'] = $val['fieldId'];
if ( isset( $val['fieldValue'] )) {
$request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId[' . $key . ']'] = '';
$request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue[' . $key . ']'] = $val['fieldValue'];
} elseif ( isset( $val['fieldOptionId'] )) {
$request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId[' . $key . ']'] = $val['fieldOptionId'];
$request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue[' . $key . ']'] = '';
}
}
unset($val);
unset($key);

$i = 1000000; // If you have more than 1 Million fields.... dude, give up!

// Enter the Lottery
$request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldId[' . $i . ']']       = $lottery;
$request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId[' . $i . ']'] = $status;
$request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue[' . $i . ']']    = '';
$i++;

// Set Bed Type
$request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldId[' . $i . ']']         = $bedPref;
if($feed[3] == 'King Bed') {
$request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId[' . $i . ']'] = $kingBed;
} elseif($feed[3] == 'Two Double Beds') {
$request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId[' . $i . ']'] = $queenBed;
} elseif($feed[3] == 'No Preference') {
$request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId[' . $i . ']'] = $noBed;
}
$request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue[' . $i . ']']      = '';
$i++;

// Set Hotel Piority
if($feed[4] != 'NONE') {
foreach($qual as $val) {
$request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldId[' . $i . ']'] = $hotelPri;
if($val == "Volunteer") {
$request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId[' . $i . ']'] = $volPri;
} elseif($val == "ADA") {
$request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId[' . $i . ']'] = $adaPri;
} elseif($val == "ADO") {
$request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId[' . $i . ']'] = $adoPri;
}
$request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue[' . $i . ']']      = '';
$i++;
}
}

$result2 = $GLOBALS['Neon']->go1($request);

if ( isset( $result2['operationResult'] ) && $result2['operationResult'] == 'SUCCESS' ) {
//      if(TRUE) {  // TESTING
echo "Complete.<br />\n";
} else {
echo "FAILED to update, Manually check.<br />\n";
}
} else {
echo "FAILED to retreive account information.<br />\n";
}
}
unset($request);
unset($result);
unset($result2);
unset($feed);
echo "<h3>End of DoFeed Routine</h3>\n<hr>\n";
}
*/

/* Retrieve and sanitize POST data */
$arguments = [
'firstName' => FILTER_SANITIZE_SPECIAL_CHARS,
'lastName'  => FILTER_SANITIZE_SPECIAL_CHARS,
'email'     => FILTER_SANITIZE_EMAIL,
'ID'        => FILTER_SANITIZE_SPECIAL_CHARS,

];
if (!empty($_POST)) {
    $searchCriteria = filter_input_array(INPUT_POST, $arguments);
} else {
    $searchCriteria = filter_input_array(INPUT_GET, $arguments);
}

// If there are search criteria present, execute the search query
if (!empty($searchCriteria['firstName']) ||
    !empty($searchCriteria['lastName']) ||
    !empty($searchCriteria['email'])) {
    $result = lookup_users_by_name_email(
        $searchCriteria['firstName'],
        $searchCriteria['lastName'],
        $searchCriteria['email'],
        ['City', 'State']
    );
    $message = 'No results match your search.';
} else {
    $result = null;
    $message = 'You must specify search criteria.';
}

?>
<form action="index.php?Function=admin" method="POST" class="form-inline">
  <fieldset>
    <legend>Basic NeonCRM Account Search</legend>
    <p>
      <label>First Name</label>
      <input type="text" class="form-control" name="firstName" value="<?php echo htmlentities($searchCriteria['firstName']); ?>"/>
      <label>Last Name</label>
      <input type="text" class="form-control" name="lastName" value="<?php echo htmlentities($searchCriteria['lastName']); ?>" />
    </p><p>
      <label>Email</label>
      <input type="text" class="form-control" name="email" value="<?php echo htmlentities($searchCriteria['email']); ?>" />
    </p>
    <input type="submit" value="Search" class="btn btn-default" />
  </fieldset>
</form>
<hr>
<?php if ($result && !$result['code']) : ?>
  <table class="table table-striped">
    <tr>
      <th>Name</th>
      <th>Account ID</th>
      <th>Email</th>
      <th>Location</th>
    </tr>
    <?php foreach ($result['users'] as $r) : ?>
      <tr>
        <td><?php echo $r['First Name']; ?> <?php echo $r['Last Name']; ?></td>
        <td>
          <a href="index.php?Function=admin&ID=<?php echo $r['Id']; ?>" class="w3-button">
            <?php echo $r['Id']; ?>
          </a>
        </td>
        <td><?php echo $r['Email']; ?></td>
        <td><?php echo $r['City']; ?> <?php echo $r['State']; ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php else : ?>
  <p><?php echo $message; ?></p>
<?php
endif;

if (!empty($searchCriteria['ID'])) {
    unset($result);
    $result = pullIndividualAccount($searchCriteria['ID']);
    echo '<pre>';
    print_r($result);
    echo '</pre>';
}
?>
</div><!--close main_content div-->

<!--Play Area for random testing code -->
<div>
  <pre><?php
    // A simple place for admins to do random testing.

    ?></pre>
</div>
<!--End Play Area-->

<!-- Debug code for admins, just in case -->
<p></p>
<script>
function myDebug() {
    var x = document.getElementById("DebugInfo");
    if (x.className.indexOf("w3-show") == -1) {
        x.className += " w3-show";
    } else {
        x.className = x.className.replace(" w3-show", "");
    }
}
</script>
<div class="w3-dropdown-click">
  <button class="w3-button w3-tiny" onclick="myDebug()">Debug Array Information</button>
  <div id="DebugInfo" class="w3-dropdown-content w3-small">
    <pre><?php print_r($_SESSION); ?></pre>
  </div>
</div>
