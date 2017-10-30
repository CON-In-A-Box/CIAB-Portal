<?php
  if (empty( $_SESSION['customFields']['currConComPos'])) {
    // This is for ConCom members only!  Silently return to main.
    goSite();
  }

  if (!empty($_REQUEST)) {
    // Retrieve and sanitize POST data
    $arguments = [
      'VolunteerID_c'       => FILTER_SANITIZE_NUMBER_FLOAT,
      'ActualHoursWorked_c' => FILTER_SANITIZE_NUMBER_FLOAT,
      'EndDateTime_c'       => FILTER_SANITIZE_SPECIAL_CHARS,
      'TimeModifier_c'      => FILTER_SANITIZE_NUMBER_FLOAT,
      'DepartmentWorked_c'  => FILTER_SANITIZE_SPECIAL_CHARS,
      'AuthorizedBy_c'      => FILTER_SANITIZE_SPECIAL_CHARS,
      'volunteerId'         => FILTER_SANITIZE_NUMBER_FLOAT,
    ];
    if (!empty($_POST)) {
      $updateData = filter_input_array( INPUT_POST, $arguments );
    } else {
      $updateData = filter_input_array( INPUT_GET, $arguments );
    }

    if (!empty($updateData['VolunteerID_c']) && !empty($updateData['ActualHoursWorked_c']) && !empty($updateData['EndDateTime_c']) && !empty($updateData['TimeModifier_c']) && !empty($updateData['DepartmentWorked_c']) && !empty($updateData['AuthorizedBy_c'])) {
      $request = [
        'method' => 'customObjectRecord/createCustomObjectRecord',
        'parameters' => [
          'customObjectRecord.objectApiName' => 'VolHourEntries_c',
        ],
      ];

      $fields = ['VolunteerID_c', 'ActualHoursWorked_c', 'EndDateTime_c', 'TimeModifier_c', 'DepartmentWorked_c', 'AuthorizedBy_c'];
      foreach($fields as $key => $field) {
        $request['parameters']['customObjectRecord.customObjectRecordDataList.customObjectRecordData.name[' . $key . ']'] = $field;
        $request['parameters']['customObjectRecord.customObjectRecordDataList.customObjectRecordData.value[' . $key . ']'] = $updateData[$field];
      }

      unset($key);
      unset($value);

      $result = $GLOBALS['Neon']->go1($request);

      if ( isset( $result['operationResult'] ) && $result['operationResult'] == 'SUCCESS' ) {
        $updateComplete = TRUE;
      } else {
        $updateComplete = FALSE;
        sendError("Fail to record Volunteer hours " . __FILE__, $result);
      }

    } elseif (!empty($updateData['volunteerId'])) {
      $preferredFirstName = array_search('Preferred First Name', $_SESSION['definedFields']);
      $preferredLastName = array_search('Preferred Last Name', $_SESSION['definedFields']);

      $search = [ 
        'method' => 'account/listAccounts', 
        'columns' => [
          'standardFields' => ['Account ID', 'First Name', 'Last Name'],
          'customFields' => [$preferredFirstName, $preferredLastName],
        ],
        'criteria' => [
          ['Account ID', 'EQUAL', $updateData['volunteerId'],],
        ],
        'page' => [
          'currentPage' => 1,
          'pageSize' => 2,
          'sortColumn' => 'Last Name',
          'sortDirection' => 'ASC',
        ],
      ];
      $result = $GLOBALS['Neon']->search($search);
      if ( isset( $result['operationResult'] ) && $result['operationResult'] == 'SUCCESS' ) {
        if(isset($result['page']['totalResults']) && $result['page']['totalResults'] == 1) {
          foreach($result['searchResults'] as $r) {
            if(!empty($r['Preferred First Name'])) {
              echo $r['Preferred First Name'];
            } else {
              echo $r['First Name'];
            }
            echo " ";
            if(!empty($r['Preferred Last Name'])) {
              echo $r['Preferred Last Name'];
            } else {
              echo $r['Last Name'];
            }
          }
          echo " (" . $updateData['volunteerId'] . ")";
        } else {
          echo $updateData['volunteerId'] . " - Not Found";
        }
      } else {
        echo $updateData['volunteerId'] . " - Lookup Error";
      }

      exit();
    }    
  }
?>

