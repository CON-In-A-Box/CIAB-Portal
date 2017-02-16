<div id="dynamic_content" class="main_screen">
<?php require($PAGESDIR . '/base/leftnav.inc'); ?>
<div id="main_content">
<?php
echo "<h2>Welcome " . $_SESSION['preferredName'] . "!</h2>\n";

/*
  if( isset($_REQUEST['DoFeed'])) {
    require_once($FUNCTIONDIR . "/divisional.inc");
    $attendKey = array_search('2017 Official Meeting Attendance', $_SESSION['definedFields']);
    $dateOption = array_search('2017-02-12 - February ConCom', $_SESSION['definedFields']['2017Meetings']);

    foreach(file($FUNCTIONDIR . "/directadd.feed", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
//    $myTest = [ "Thomas,Keeley,tskeeley@gmail.com" ]; // TESTING
//    foreach($myTest as $line) { // TESTING
      flush();
      $feed = explode(",", $line);

      echo "Updating " . $feed[0] . " " . $feed[1] . " - " . $feed[2] . ":";

      $search = [ 
        'method' => 'account/listAccounts', 
        'criteria' => [
          ['Email', 'EQUAL', $feed[2]],
        ],
        'columns' => [
          'standardFields' => ['Account ID', 'First Name', 'Last Name', 'Preferred Name', 'Email 1'],
        ],
        'page' => [
          'currentPage' => 1,
          'pageSize' => 200,
          'sortColumn' => 'Account ID',
          'sortDirection' => 'ASC',
        ],
      ];
      $result = $GLOBALS['Neon']->search($search);

      if ( isset( $result['operationResult'] ) && $result['operationResult'] == 'SUCCESS' ) {
        if ( count($result['searchResults']) == 1 ) {
          $request = [
            'method' => 'account/retrieveIndividualAccount',
            'parameters' => [
              'accountId' => $result['searchResults'][0]['Account ID'],
            ],
          ];
          $result2 = $GLOBALS['Neon']->go($request);

          if ( isset( $result2['operationResult'] ) && $result2['operationResult'] == 'SUCCESS' ) {
            $request = [
              'method' => 'account/editIndividualAccount',
              'parameters' => [
                'individualAccount.accountId' => $result2['individualAccount']['accountId'],
                'individualAccount.primaryContact.contactId' => $result2['individualAccount']['primaryContact']['contactId'],
                'individualAccount.primaryContact.firstName' => $result2['individualAccount']['primaryContact']['firstName'],
                'individualAccount.primaryContact.lastName' => $result2['individualAccount']['primaryContact']['lastName'],
                'individualAccount.customFieldDataList.customFieldData.fieldId' => $attendKey,
                'individualAccount.customFieldDataList.customFieldData.fieldOptionId' => $dateOption,
                'individualAccount.customFieldDataList.customFieldData.fieldValue' => '',
              ],
            ];

            foreach ($result2['individualAccount']['customFieldDataList']['customFieldData'] as $key => $val) {
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

            $result = $GLOBALS['Neon']->go1($request);

            if ( isset( $result['operationResult'] ) && $result['operationResult'] == 'SUCCESS' ) {
//            if(TRUE) {  // TESTING
              echo "Complete.<br />\n";
            } else {
              echo "FAILED to update, Manually check.<br />\n";
            }
          } else {
            echo "FAILED to retreive account information.<br />\n";
          }
        } elseif ( count($result['searchResults']) == 0 ) {
          echo "Cannot update - Missing Email address.<br />";
        } else {
          echo "Cannot update - Multiple records with this Email address.<br />";
        }
      } else {
        echo "FAILED Account Lookup by Email<br />\n";
      }
    unset($request);
    unset($result);
    unset($result2);
    unset($feed);
    }
  }
*/
  /* Retrieve and sanitize POST data */
$arguments = [
  'firstName' => FILTER_SANITIZE_SPECIAL_CHARS,
  'lastName'  => FILTER_SANITIZE_SPECIAL_CHARS,
  'email'     => FILTER_SANITIZE_EMAIL,
];
$searchCriteria = filter_input_array( INPUT_POST, $arguments );

// Formulate the search query
$search = [ 
  'method' => 'account/listAccounts', 
  'columns' => [
    'standardFields' => ['First Name', 'Last Name', 'Email 1', 'City', 'State'],
  ],
  'page' => [
    'currentPage' => 1,
    'pageSize' => 200,
    'sortColumn' => 'Last Name',
    'sortDirection' => 'ASC',
  ],
];

// Some search criteria are variable based on our POST data. Add them to the query if applicable
if ( isset( $searchCriteria['firstName'] ) && !empty( $searchCriteria['firstName'] ) ) {
  $search['criteria'][] = ['First Name', 'EQUAL', $searchCriteria['firstName']];
}
if ( isset( $searchCriteria['lastName'] ) && !empty( $searchCriteria['lastName'] ) ) {
  $search['criteria'][] = ['Last Name', 'EQUAL', $searchCriteria['lastName']];
}
if ( isset( $searchCriteria['email'] ) && !empty( $searchCriteria['email'] ) ) {
  $search['criteria'][] = ['Email', 'EQUAL', $searchCriteria['email']];
}

// If there are search criteria present, execute the search query
if ( !empty( $search['criteria'] ) ) {
  $result = $GLOBALS['Neon']->search($search);
  $message = 'No results match your search.';
} else {
  $result = null;
  $message = 'You must specify search criteria.';
}

?>
<form action="index.php?Function=lookup" method="POST" class="form-inline">
  <fieldset>
    <legend>Basic NeonCRM Account Search</legend>
    <p>
      <label>First Name</label>
      <input type="text" class="form-control" name="firstName" value="<?php echo htmlentities( $searchCriteria['firstName'] ); ?>"/>
      <label>Last Name</label>
      <input type="text" class="form-control" name="lastName" value="<?php echo htmlentities( $searchCriteria['lastName'] ); ?>" />
    </p><p>
      <label>Email</label>
      <input type="text" class="form-control" name="email" value="<?php echo htmlentities( $searchCriteria['email'] ); ?>" />
    </p>
    <input type="submit" value="Search" class="btn btn-default" />
  </fieldset>
</form>
<hr>
<?php if( isset($result['page']['totalResults'] ) && $result['page']['totalResults'] >= 1 ): ?>
  <table class="table table-striped">
    <tr>
      <th>Name</th>
      <th>Email</th>
      <th>Location</th>
    </tr>
    <?php foreach($result['searchResults'] as $r): ?>
      <tr>
        <td><?php echo $r['First Name']; ?> <?php echo $r['Last Name']; ?></td>
        <td><?php echo $r['Email 1']; ?></td>
        <td><?php echo $r['City']; ?> <?php echo $r['State']; ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php else: ?>
  <p><?php echo $message; ?></p>
<?php endif; ?>

</div><!--close main_content div-->
