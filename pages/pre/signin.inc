<?php
// Sign in this user for today's meeting

$datesAttend = $_REQUEST['DateFeed'];

$request = [
  'method' => 'account/editIndividualAccount',
  'parameters' => [
    'individualAccount.accountId' => $_SESSION['accountId'],
    'individualAccount.primaryContact.contactId' => $_SESSION['contactId'],
    'individualAccount.primaryContact.firstName' => $_SESSION['firstName'],
    'individualAccount.primaryContact.lastName' => $_SESSION['lastName'],
    'individualAccount.customFieldDataList.customFieldData.fieldId' => $_REQUEST['AttendField'],
    'individualAccount.customFieldDataList.customFieldData.fieldOptionId' => $_REQUEST['DateFeed'],
    'individualAccount.customFieldDataList.customFieldData.fieldValue' => '',
  ],
];

foreach ($_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData'] as $key => $val) {
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
  // Add the option since it doesn't exist.  Since arrays count from 0, the number of current entries
  // makes a perfect 'next' entry to use.
  $i = count($_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData']);
  $_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData'][$i]['fieldId'] = $_REQUEST['AttendField'];
  $_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData'][$i]['fieldOptionId'] = $_REQUEST['DateFeed'];
  $_SESSION['customFields'][$_REQUEST['AttendField']][] = $_REQUEST['DateFeed'];
}

goSite();

?>
