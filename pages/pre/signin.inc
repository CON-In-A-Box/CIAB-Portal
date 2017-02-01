<?php
// Sign in this user for today's meeting

request = [
  'method' => 'account/updateIndividualAccount',
  'parameters' => [
    'individualAccount.accountId' => $_SESSION[accountId],
    'individualAccount.customFieldDataList.customFieldData.fieldId' => $thiny,
    'individualAccount.customFieldDataList.customFieldData.fieldOptionId' => $thingy,
    'individualAccount.customFieldDataList.customFieldData.fieldValue' => '',
  ],
];
//$result = $GLOBALS['Neon']->go($request);
if ( isset( $result['operationResult'] ) && $result['operationResult'] == 'SUCCESS' ) {
  
?>
