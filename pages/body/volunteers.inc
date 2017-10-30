<div id="main_content" class="w3-cell w3-cell-top w3-mobile">

<?php
  $request = [
    'method' => 'customObjectRecord/listCustomObjectRecords',
    'parameters' => [
      'objectApiName' => 'VolHourEntries_c',
    ],
  ];

  $fields = ['VolunteerID_c.id', 'ActualHoursWorked_c', 'EndDateTime_c', 'TimeModifier_c', 'DepartmentWorked_c', 'AuthorizedBy_c'];
  foreach($fields as $key => $field) {
    $request['parameters']['customObjectOutputFieldList.customObjectOutputField.label[' . $key . ']'] = $field;
    $request['parameters']['customObjectOutputFieldList.customObjectOutputField.columnName[' . $key . ']'] = $field;
    echo "$field,";
  }
  echo "<br />\n";
  unset($key);
  unset($value);

  $result = $GLOBALS['Neon']->go1($request);

  if ( isset( $result['operationResult'] ) && $result['operationResult'] == 'SUCCESS' ) {
    foreach($result['searchResults']['nameValuePairs'] as $record) {
      foreach($record as $field) {
        foreach($field as $key => $val) {
          echo $val['value'] . ",";
        }
        echo "<br />\n";
      }
    }
  } else {
    sendError("Fail to display Volunteer Hours " . __FILE__, $result);
  }
?>
