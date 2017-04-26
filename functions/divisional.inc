<?php

// the place to keep all the division info - easy to edit
$Divisions = [ "Activities", "Administration", "External Relations and Communications", "Facilities", "Hospitality", "Productions", "Systems", "Committees", "Corporate Staff", ];

$ConComPositions = [ "Head", "Sub-Head", "Specialist" ];

$Departments = [
  "Accessibility & Inclusion" => [
    "Division" => "Facilities",
    "Email" => "accessibility@convergence-con.org",
    "OtherEmails" => "asl@convergence-con.org",
  ],
  "Activities" => [
    "Division" => "Activities",
    "Email" => "activities@convergenceevents.org",
  ],
  "Administration" => [
    "Division" => "Administration",
    "Email" => "administration@convergenceevents.org",
  ],
  "Allocations" => [
    "Division" => "Hospitality",
    "Email" => "allocations@convergence-con.org",
  ],
  "Archives" => [ 
    "Division" => "Administration",
    "Email" => "archives@convergence-con.org",
  ],
  "Artist Alley" => [ 
    "Division" => "External Relations and Communications",
    "Email" => "artistalley@convergence-con.org",
  ],
  "Art Show" => [ 
    "Division" => "Administration",
    "Email" => "artshow@convergence-con.org",
  ],
  "Book Swap" => [ 
    "Division" => "Activities",
    "Email" => "bookswap@convergence-con.org",
  ],
  "Ceremonies" => [ 
    "Division" => "Productions",
    "Email" => "ceremonies@convergence-con.org",
  ],
  "CFO" => [ 
    "Division" => "Corporate Staff",
    "Email" => "cfo@convergenceevents.org",
  ],
  "Cinema Rex" => [ 
    "Division" => "Activities",
    "Email" => "cinemarex@convergence-con.org",
  ],
  "CoF2E2" => [ 
    "Division" => "Hospitality",
    "Email" => "cof2e2@convergence-con.org",
  ],
  "Committees" => [ 
    "Division" => "Committees",
    "Email" => "info@convergenceevents.org",
  ],
  "Connie's Quantum Sandbox" => [ 
    "Division" => "Activities",
    "Email" => "sandbox@convergence-con.org",
  ],
  "ConSuite" => [ 
    "Division" => "Hospitality",
    "Email" => "consuite@convergence-con.org",
  ],
  "Corporate Staff" => [ 
    "Division" => "Corporate Staff",
    "Email" => "directors@convergenceevents.org",
  ],
  "Creative Services" => [ 
    "Division" => "External Relations and Communications",
    "Email" => "creativeservices@convergence-con.org",
  ],
  "CVG-TV/Video" => [ 
    "Division" => "Productions",
    "Email" => "cvgtv@convergence-con.org",
    "OtherEmails" => "video@convergence-con.org",
  ],
  "Dealers Room" => [ 
    "Division" => "External Relations and Communications",
    "Email" => "dealers@convergence-con.org",
  ],
  "Decor" => [ 
    "Division" => "Facilities",
    "Email" => "decor@convergence-con.org",
  ],
  "Dock" => [ 
    "Division" => "Facilities",
    "Email" => "dock@convergence-con.org",
  ],
  "Exhibits" => [ 
    "Division" => "Activities",
    "Email" => "exhibits@convergence-con.org",
  ],
  "External Relations and Communications" => [ 
    "Division" => "External Relations and Communications",
    "Email" => "erac@convergenceevents.org",
  ],
  "Facilities" => [ 
    "Division" => "Facilities",
    "Email" => "facilities@convergenceevents.org",
  ],
  "Finance" => [ 
    "Division" => "Systems",
    "Email" => "finance@convergence-con.org",
  ],
  "First Advisors" => [ 
    "Division" => "Hospitality",
    "Email" => "firstadvisors@convergence-con.org",
  ],
  "Future Visioning" => [ 
    "Division" => "Committees",
    "Email" => "futurevisioning@convergence-con.org",
  ],
  "Gaming" => [ 
    "Division" => "Activities",
    "Email" => "gaming@convergence-con.org",
    "OtherEmails" => "rpggaming@convergence-con.org|langaming@convergence-con.org|cardgaming@convergence-con.org|boardgaming@convergence-con.org",
  ],
  "Guest Search" => [ 
    "Division" => "Committees",
    "Email" => "guestsearch@convergence-con.org",
  ],
  "Guests of Honor" => [ 
    "Division" => "Hospitality",
    "Email" => "guests@convergence-con.org",
  ],
  "Harmonic Convergence" => [ 
    "Division" => "Productions",
    "Email" => "harmonicconvergence@convergence-con.org",
  ],
  "Hospitality" => [ 
    "Division" => "Hospitality",
    "Email" => "hospitality@convergenceevents.org",
  ],
  "Hotel" => [ 
    "Division" => "Facilities",
    "Email" => "hotel@convergence-con.org",
  ],
  "Invited Participants" => [ 
    "Division" => "Activities",
    "Email" => "pros@convergence-con.org",
  ],
  "IT" => [ 
    "Division" => "Systems",
    "Email" => "it@convergence-con.org",
    "OtherEmails" => "shiftboard@convergence-con.org",
  ],
  "Logistics" => [
    "Division" => "Facilities",
    "Email" => "logistics@convergence-con.org",
  ],
  "MainStage" => [ 
    "Division" => "Productions",
    "Email" => "mainstage@convergence-con.org",
  ],
  "Masquerade" => [ 
    "Division" => "Productions",
    "Email" => "masquerade@convergence-con.org",
  ],
  "Meeting Childcare" => [ 
    "Division" => "Systems",
    "Email" => "childcare@convergence-con.org",
  ],
  "Member Advocates" => [ 
    "Division" => "Committees",
    "Email" => "memberadvocats@convergence-con.org",
  ],
  "Merchandise" => [ 
    "Division" => "Administration",
    "Email" => "merchandise@convergence-con.org",
  ],
  "Nerf Herders" => [ 
    "Division" => "Facilities",
    "Email" => "nerfherders@convergence-con.org",
  ],
  "Operations" => [ 
    "Division" => "Hospitality",
    "Email" => "operations@convergence-con.org",
  ],
  "Parties" => [
    "Division" => "Hospitality",
    "Email" => "parties@convergence-con.org",
  ],
  "Partner Management" => [
    "Division" => "Administration",
    "Email" => "partners@convergence-con.org",
  ],
  "Partner Search" => [
    "Division" => "Committees",
    "Email" => "partnersearch@convergence-con.org",
  ],
  "Photography" => [
    "Division" => "External Relations and Communications",
    "Email" => "photography@convergence-con.org",
  ],
  "President" => [ 
    "Division" => "Corporate Staff",
    "Email" => "president@convergenceevents.org",
  ],
  "Productions" => [ 
    "Division" => "Productions",
    "Email" => "productions@convergenceevents.org",
  ],
  "Programming" => [ 
    "Division" => "Activities",
    "Email" => "programming@convergence-con.org",
  ],
  "Publications" => [ 
    "Division" => "External Relations and Communications",
    "Email" => "publications@convergence-con.org",
    "OtherEmails" => "advertising@convergence-con.org",
  ],
  "Registration" => [ 
    "Division" => "Administration",
    "Email" => "registration@convergence-con.org",
  ],
  "Resume" => [ 
    "Division" => "Facilities",
    "Email" => "resume@convergence-con.org",
  ],
  "Secretary" => [ 
    "Division" => "Corporate Staff",
    "Email" => "secretary@convergenceevents.org",
  ],
  "Smokers Paradise" => [ 
    "Division" => "Hospitality",
    "Email" => "smokersparadise@convergence-con.org",
  ],
  "Social Media" => [ 
    "Division" => "External Relations and Communications",
    "Email" => "socialmedia@convergence-con.org",
  ],
  "Space Lounge" => [ 
    "Division" => "Hospitality",
    "Email" => "spacelounge@convergence-con.org",
  ],
  "Systems" => [ 
    "Division" => "Systems",
    "Email" => "systems@convergenceevents.org",
  ],
  "Teen Room" => [ 
    "Division" => "Hospitality",
    "Email" => "teens@convergence-con.org",
  ],
  "Theater Nippon" => [ 
    "Division" => "Activities",
    "Email" => "theaternippon@convergence-con.org",
  ],
  "Treasurer" => [ 
    "Division" => "Corporate Staff",
    "Email" => "treasurer@convergenceevents.org",
  ],
  "Vice-President" => [ 
    "Division" => "Corporate Staff",
    "Email" => "vicepresident@convergenceevents.org",
  ],
  "Volunteer Den" => [ 
    "Division" => "Hospitality",
    "Email" => "volden@convergence-con.org",
  ],
  "Volunteers" => [ 
    "Division" => "Administration",
    "Email" => "volunteers@convergence-con.org",
  ],
  "Webteam" => [ 
    "Division" => "External Relations and Communications",
    "Email" => "webteam@convergence-con.org",
  ],
  "Youth Programming Advisory" => [ 
    "Division" => "Committees",
    "Email" => "youthprogramming@convergence-con.org",
  ],
];

function getDivision($dep) {
  return($GLOBALS['Departments'][$dep]['Division']);
}

function getDepartmentEmail($dep) {
  return($GLOBALS['Departments'][$dep]['Email']);
}

function getOtherDepartmentEmails($dep) {
  if(isset($GLOBALS['Departments'][$dep]['OtherEmails'])) {
    return($GLOBALS['Departments'][$dep]['OtherEmails']);
  } else {
    return(null);
  }
}

function AddConComPosition($accountid, $department, $position, $note) {
  $request = [
      'method' => 'account/retrieveIndividualAccount',
      'parameters' => [
      'accountId' => $accountid,
    ],
  ];
  $result = $GLOBALS['Neon']->go($request);
  if ( isset( $result['operationResult'] ) && $result['operationResult'] == 'SUCCESS' ) {
    $conComFieldId = array_search('CVGConCom Current Position', $_SESSION['definedFields']);

    $key = array_search($conComFieldId, array_column($result['individualAccount']['customFieldDataList']['customFieldData'], 'fieldId'));
    if($key === FALSE) {
      // Add the option since it doesn't exist.  Since arrays count from 0, the number of current entries
      // makes a perfect 'next' entry to use.
      $i = count($result['individualAccount']['customFieldDataList']['customFieldData']);
      $result['individualAccount']['customFieldDataList']['customFieldData'][$i]['fieldId'] = $conComFieldId;
      $result['individualAccount']['customFieldDataList']['customFieldData'][$i]['fieldValue'] = $department . "," . $position . "," . $note;
    } else {
      // if the department is already assigned to this user, wipe it and replace it with the new info
      if(preg_match('/^' . $department . '/', $result['individualAccount']['customFieldDataList']['customFieldData'][$key]['fieldValue']) || preg_match('/\|' . $department . '/', $result['individualAccount']['customFieldDataList']['customFieldData'][$key]['fieldValue'])) {
        RemoveConComPosition($accountid, $department, 'ALL');
        return AddConComPosition($accountid, $department, $position, $note);
      }
      $result['individualAccount']['customFieldDataList']['customFieldData'][$key]['fieldValue'] .= "|" . $department . "," . $position . "," . $note;
    }

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

    $result2 = $GLOBALS['Neon']->go1($request);


    if ( isset( $result2['operationResult'] ) && $result2['operationResult'] == 'SUCCESS' ) {
      // Notify the Division Director, so they know
      $to = getDepartmentEmail(getDivision($department));
      $preferredFirstName = array_search('Preferred First Name', $_SESSION['definedFields']);
      $preferredLastName = array_search('Preferred Last Name', $_SESSION['definedFields']);
      $subject = 'ConCom Division addition to ' . $department;
      if(empty($result['individualAccount']['customFieldDataList']['customFieldData'][$preferredFirstName]['fieldValue'])) {
        $name = $result['individualAccount']['primaryContact']['firstName'];
      } else {
        $name = $result['individualAccount']['customFieldDataList']['customFieldData'][$preferredFirstName]['fieldValue'];
      }
      if(empty($result['individualAccount']['customFieldDataList']['customFieldData'][$preferredLastName]['fieldValue'])) {
        $name .= " " . $result['individualAccount']['primaryContact']['lastName'];
      } else {
        $name .= " " . $result['individualAccount']['customFieldDataList']['customFieldData'][$preferredLastName]['fieldValue'];
      }
      $message = $_SESSION['fullName'] . ' has added ' . $name . " to " . $department . ' as ' . $position . '.';
      $headers = 'From: noreply@convergence-con.org' . "\r\n";
      $headers .= 'Reply-To: noreply@convergence-con.org';
      
      mail($to, $subject, $message, $headers);

      return TRUE;
    } else {
      print_r($result2);
      die("Failed to apply remove to Neon!");
    }
  }
}

function RemoveConComPosition($accountid, $department, $position) {
  $request = [
      'method' => 'account/retrieveIndividualAccount',
      'parameters' => [
      'accountId' => $accountid,
    ],
  ];
  $result = $GLOBALS['Neon']->go($request);
  if ( isset( $result['operationResult'] ) && $result['operationResult'] == 'SUCCESS' ) {
    $conComFieldId = array_search('CVGConCom Current Position', $_SESSION['definedFields']);

    foreach ($result['individualAccount']['customFieldDataList']['customFieldData'] as $key => $val) {
      if ( isset( $val['fieldId'] ) && $val['fieldId'] == $conComFieldId ) {
        // Remove only the position in question from the current list of concom positions
        $currentPos = '';
        $pos = explode('|', $val['fieldValue']);
        foreach($pos as $entry) {
          $field = explode(',', $entry);
          if($field[0] == $department && ($field[1] == $position || $position == 'ALL')) {
            // This is the entry we are deleting
          } else {
            if(!empty($currentPos)) {
              $currentPos .= '|';
            }
            $currentPos .= $entry;
          }
          unset($field);
        }
        if(empty($currentPos)) {
          unset($result['individualAccount']['customFieldDataList']['customFieldData'][$key]);
        } else {
          $result['individualAccount']['customFieldDataList']['customFieldData'][$key]['fieldValue'] = $currentPos;
        }
      }
    }

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

    $result2 = $GLOBALS['Neon']->go1($request);

    if ( isset( $result2['operationResult'] ) && $result2['operationResult'] == 'SUCCESS' ) {
      // Notify the Division Director, so they know
      $to = getDepartmentEmail(getDivision($department));
      $subject = 'ConCom Division Removal to ' . $department;
      $preferredFirstName = array_search('Preferred First Name', $_SESSION['definedFields']);
      $preferredLastName = array_search('Preferred Last Name', $_SESSION['definedFields']);

      if(empty($result['individualAccount']['customFieldDataList']['customFieldData'][$preferredFirstName]['fieldValue'])) {
        $name = $result['individualAccount']['primaryContact']['firstName'];
      } else {
        $name = $result['individualAccount']['customFieldDataList']['customFieldData'][$preferredFirstName]['fieldValue'];
      }
      if(empty($result['individualAccount']['customFieldDataList']['customFieldData'][$preferredLastName]['fieldValue'])) {
        $name .= " " . $result['individualAccount']['primaryContact']['lastName'];
      } else {
        $name .= " " . $result['individualAccount']['customFieldDataList']['customFieldData'][$preferredLastName]['fieldValue'];
      }
      $message = $_SESSION['fullName'] . ' has removed ' . $name . " from " . $department . '.';
      $headers = 'From: noreply@convergence-con.org' . "\r\n";
      $headers .= 'Reply-To: noreply@convergence-con.org';
      
      mail($to, $subject, $message, $headers);

      return TRUE;
    } else {
      print_r($result2);
      die("Failed to apply remove to Neon!");
    }
  }
}

function ConComListBuildSearch($page) {
  $conComFieldId = array_search('CVGConCom Current Position', $_SESSION['definedFields']);
  $preferredFirstName = array_search('Preferred First Name', $_SESSION['definedFields']);
  $preferredLastName = array_search('Preferred Last Name', $_SESSION['definedFields']);

  $search = [ 
    'method' => 'account/listAccounts', 
    'criteria' => [
      [$conComFieldId, 'NOT_BLANK', ''],
    ],
    'columns' => [
      'standardFields' => ['Account ID', 'First Name', 'Last Name', 'Email 1'],
      'customFields' => [$conComFieldId, $preferredFirstName, $preferredLastName],
    ],
    'page' => [
      'currentPage' => $page,
      'pageSize' => 200,
      'sortColumn' => 'Account ID',
      'sortDirection' => 'ASC',
    ],
  ];
  $result = $GLOBALS['Neon']->search($search);

  if ( isset( $result['operationResult'] ) && $result['operationResult'] == 'SUCCESS' ) {
    $return['pages'] = $result['page']['totalPage'];
    foreach( $result['searchResults'] as $val ) {
      $positionsHeld = explode("|", $val['CVGConCom Current Position']);
      foreach( $positionsHeld as $pos) {
        $tmp = explode(",", $pos);
        if(getDivision($tmp[0])) {
        $line = [
          'Account ID' => $val['Account ID'],
          'Division'   => getDivision($tmp[0]),
          'Department' => $tmp[0],
          'Position'   => $tmp[1],
          'Email'      => $val['Email 1'],
        ];
        } else {
          echo "<pre>\n";
          echo "Please report this error and output!\n";
          print_r($val);
          print_r($tmp);
          die();
        }

        if (empty($val['Preferred First Name'])) {
          $line['First Name'] = $val['First Name'];
        } else {
          $line['First Name'] = $val['Preferred First Name'];
        }

        if (empty($val['Preferred Last Name'])) {
          $line['Last Name'] = $val['Last Name'];
        } else {
          $line['Last Name'] = $val['Preferred Last Name'];
        }

        if (isset($tmp[2])) {
          $line['Note'] = $tmp[2];
        } else {
          $line['Note'] = "";
        }

        if ($tmp[0] == $line['Division']) {
          if ($tmp[1] == "Director") {
            $line['Department'] = "Division Director";
          } elseif ($tmp[1] == "Specialist") {
            $line['Department'] = "Division Support";
          } else {
            $line['Department'] = $tmp[0];
          }
        }

        $return['list'][] = $line;
      }
      unset($tmp);
      unset($line);
      unset($pos);
      unset($positionsHeld);
    }
    unset($val);
  } else {
    echo "<pre>\n";
      echo $preferredFirstName . "\n";
      echo $preferredLastName . "\n";
      print_r($result);
    echo "</pre>\n";
    die("Could not retrieve ConComList");
  }
  return $return;
}

function ConComListBuild() {
  foreach( $GLOBALS['Departments'] as $kdep => $dep) {
    if( $dep['Division'] != $kdep ) {
      if( !empty($dep['OtherEmails'])) {
        $addEmail=explode("|", $dep['OtherEmails']);
        foreach($addEmail as $tmp) {
          $dep['Email'] .= "<br />" . $tmp;
        }
        unset($tmp);
      }

      $fullConComList[] = [
        'Division' => $dep['Division'],
        'Department' => $kdep,
        'Position' => 'Department',
        'First Name' => '',
        'Last Name' => '',
        'Email' => $dep['Email'],
        'Note' => '',
        'Account ID' => '',
      ];
    }
  }

  $i=1;
  $runPage=1;
  while ( $i <= $runPage ) {
    $result = ConComListBuildSearch($i);
    $tmp = array_merge_recursive($fullConComList, $result['list']);
    $fullConComList = $tmp;
    unset($tmp);
    $runPage = $result['pages'];
    $i++;
  }

  return($fullConComList);
}

function ksort_recursive(&$array) {
    if (is_array($array)) {
        ksort($array);
        array_walk($array, 'ksort_recursive');
    }
}

function DumpConComList() {
  global $Divisions, $Departments, $ConComPositions;
  
  // Custom Field Data for Session - Parse it to an easy to use array
  $request = [
    'method' => 'common/listCustomFields',
    'parameters' => [
      'searchCriteria.component' => "Account",
    ],
  ];
  $result = $GLOBALS['Neon']->go($request);
  if ( isset( $result['operationResult'] ) && $result['operationResult'] == 'SUCCESS' ) {
    foreach ($result['customFields']['customField'] as $val) {
      if($val['fieldName'] == 'CVGConCom Current Position') {
        $conComFieldId = $val['fieldId'];
      }
    }
  } else {
    die("Failed to pull Custom Fields");
  }

  foreach( $Departments as $kdep => $dep) {
    if($dep['Division'] != 'Corporate Staff' && $dep['Division'] != 'Committees') {
      echo $dep['Email'] . "\n";
    }
  }

  $i=1;
  $runPage=1;
  while ( $i <= $runPage ) {
    $search = [ 
      'method' => 'account/listAccounts', 
      'criteria' => [
        [$conComFieldId, 'NOT_BLANK', ''],
      ],
      'columns' => [
        'standardFields' => ['Account ID', 'Email 1'],
        'customFields' => [$conComFieldId],
      ],
      'page' => [
        'currentPage' => $i,
        'pageSize' => 200,
        'sortColumn' => 'Account ID',
        'sortDirection' => 'ASC',
      ],
    ];
    $result = $GLOBALS['Neon']->search($search);

    if ( isset( $result['operationResult'] ) && $result['operationResult'] == 'SUCCESS' ) {
      $runPage = $result['page']['totalPage'];
      $i++;
      foreach( $result['searchResults'] as $val ) {
        echo $val['Email 1'] . "\n";
      }
    }
  }
  foreach($Divisions as $div) {
    echo GetDepartmentEmail($div) . "\n";
  }
}

function ConComListDisplay() {
  global $Divisions, $ConComPositions;

  $list = ConComListBuild();
  // Put each entry into a divisional/department mapped array
  foreach($list as $line) {
    $printList[$line['Division']][$line['Department']][$line['Position']][$line['First Name']][$line['Last Name']] = $line['Email'] . '|' . $line['Note'] . '|' . $line['Account ID'];
  }
  
  // Sort everything so we don't have to later
  ksort_recursive($printList);
  
  // Start the crazy print routine!
  echo "<div class='w3-responsive'>";
  echo "<table class='w3-table-all w3-hoverable'>\n";
    echo '<tr id="table_head" class="w3-purple">';
      echo '<th class="w3-center">Department</th>';
      echo '<th class="w3-center">Division</th>';
      echo '<th class="w3-center">First Name</th>';
      echo '<th class="w3-center">Last Name</th>';
      echo '<th class="w3-center">Position</th>';
      echo '<th class="w3-center">Email</th>';
      echo '<th class="w3-center" colspan=2>Note</th>';
    echo "</tr>\n";
    foreach($Divisions as $div) {
      $currentDivDirector = FALSE;
      echo '<tr id="division" class="w3-purple"><th id="' . $div . '" class="w3-center" colspan=2>' . $div;
      if($div != 'Committees' && $div != 'Corporate Staff') {
        echo ' Division';
      }
      echo '</th><th class="w3-center" colspan=3>';
      echo '<div class="w3-dropdown-hover w3-purple">';
      echo '  Go To Section';
      echo '  <div class="w3-dropdown-content w3-purple">';
      echo '    <a href="#main_nav" class="w3-border">Top of Page</a>';
      foreach($Divisions as $menuDiv) {
        // ##==## Likely need to add some translation here to blot out '&'
        echo '    <a href="#' . $menuDiv . '" class="w3-border">' . $menuDiv . '</a>';
      }
      echo '  </div>';
      echo '</div>';
      echo '</th><th colspan=3>' . GetDepartmentEmail($div);
      if(getOtherDepartmentEmails($div)) {
        $tmp = explode('|', getOtherDepartmentEmails($div));
        foreach($tmp as $eMail) {
          echo '<br />' . $eMail;
        }
        unset($eMail);
        unset($tmp);
      }
      echo "</th></tr>\n";
      // Division Directors
      if ( isset($printList[$div]['Division Director'])) {
        foreach( $printList[$div]['Division Director'] as $pos) {
          foreach( $pos as $kfName => $fName) {
            foreach( $fName as $klName => $lName) {
              $tmp = explode('|', $lName);
              echo '<tr><td>Division Director</td><td>' . $div . '</td><td>' . $kfName . '</td><td>' . $klName . '</td><td>Director</td><td>' . GetDepartmentEmail($div) . '</td>';
              if(isset($_SESSION['IS_ADMIN'])) {
                echo '<td>';
              } else {
                echo  '<td colspan=2>';
              }
              echo $tmp[1];
              if($_SESSION['accountId'] == $tmp[2]) {
                if(!empty($tmp[1])) {
                  echo '<br />';
                }
                echo 'This is you!';
                $currentDivDirector = TRUE;
                if(isset($_SESSION['DIR_OF'])) {
                  if(!in_array($div, $_SESSION['DIR_OF'])) {
                    $_SESSION['DIR_OF'][] = $div;
                  }
                } else {
                  $_SESSION['DIR_OF'][] = $div;
                }
              }
              echo "</td>";
              // System Admins can remove anyone
              if(isset($_SESSION['IS_ADMIN'])) {
                echo '<td class="w3-red"><a href="index.php?Function=list&Remove=' . urlencode($tmp[2]) . '&Department=' . urlencode($div) . '&Position=Director" onclick="return confirm(\'Are you sure you want to remove ' . $kfName . ' ' . $klName . ' from ' . $div . '?\');">Remove</a></td>';
              }
              echo "</tr>\n";
            }
            unset($tmp);
          }
        }
        unset($pos);
      }
      //Division Support
      if ( isset($printList[$div]['Division Support'])) {
        foreach( $printList[$div]['Division Support'] as $pos) {
          foreach( $pos as $kfName => $fName) {
            foreach( $fName as $klName => $lName) {
              $tmp = explode('|', $lName);
              echo '<tr><td>Division Support</td><td>' . $div . '</td><td>' . $kfName . '</td><td>' . $klName . '</td><td>Specialist</td><td>' . $tmp[0] . '</td>';
              if(isset($_SESSION['IS_ADMIN']) || $currentDivDirector) {
                echo '<td>';
              } else {
                echo '<td colspan=2>';
              }
              echo $tmp[1];
              if($_SESSION['accountId'] == $tmp[2]) {
                if(!empty($tmp[1])) {
                  echo '<br />';
                }
                echo 'This is you!';
              }
              echo '</td>';
              if(isset($_SESSION['IS_ADMIN']) || $currentDivDirector) {
                echo '<td class="w3-red"><a href="index.php?Function=list&Remove=' . urlencode($tmp[2]) . '&Department=' . urlencode($div) . '&Position=Specialist" onclick="return confirm(\'Are you sure you want to remove ' . $kfName . ' ' . $klName . ' from ' . $div . '?\');">Remove</a></td>';
              }
              echo "</tr>\n";
            }
            unset($tmp);
          }
        }
        unset($pos);
      }
      if($div != 'Committees' && $div != 'Corporate Staff' && (isset($_SESSION['IS_ADMIN']) || $currentDivDirector)) {
        echo '<tr><td class="w3-center" colspan=8><a href="index.php?Function=list&AddDepartment=' . $div . '">Add someone to ' . $div . '</a></td></tr>' . "\n";
      }
      // Departments
      foreach( $printList[$div] as $kdep => $dep) {
        $currentDepHead = FALSE;
        if($div != 'Corporate Staff' && $kdep != 'Division Director' && $kdep != 'Division Support') {
          echo '<tr id="department" class="w3-green">';
          if($div == 'Committees') {
            echo '<th id="' . $kdep . '" colspan=5>' . $kdep . '</th>';
          } else {
            echo '<th id="' . $kdep . '">' . $kdep . '</th><th colspan=4>' . $div . '</th>';
          }
          echo '<th colspan=3>' . GetDepartmentEmail($kdep);
          if(getOtherDepartmentEmails($kdep)) {
            $tmp = explode('|', getOtherDepartmentEmails($kdep));
            foreach($tmp as $eMail) {
              echo "<br />" . $eMail;
            }
            unset($eMail);
            unset($tmp);
          }
          echo "</th></tr>\n";
        }
        if($kdep != 'Division Director' && $kdep != 'Division Support') {
          foreach( $ConComPositions as $pos) {
            if( isset($dep[$pos])) {
              foreach( $dep[$pos] as $kfName => $fName) {
                foreach( $fName as $klName => $lName) {
                  $tmp = explode('|', $lName);
                  echo '<tr>';
                  if($div == 'Committees' || $div == 'Corporate Staff') {
                    echo '<td colspan=2>' . $kdep . '</td>';
                  } else {
                    echo '<td>' . $kdep . '</td>';
                    echo '<td>' . $div . '</td>';
                  }
                  echo '<td>' . $kfName . '</td>';
                  if ($div == 'Corporate Staff' || $div == 'Committees') {
                    echo '<td colspan=2>' . $klName . '</td>';
                  } else {
                    echo '<td>' . $klName . '</td>';
                    echo '<td>' . $pos . '</td>';
                  }
                  if ($div == 'Corporate Staff' ) {
                    echo '<td>' . GetDepartmentEmail($kdep) . '</td>';
                  } else {
                    echo '<td>' . $tmp[0] . '</td>';
                  }
                  if(isset($_SESSION['IS_ADMIN']) || $currentDivDirector || ($currentDepHead && $pos != 'Head')) {                  
                    echo '<td>';
                  } else {
                    echo '<td colspan=2>';
                  }
                  echo $tmp[1];

                  // Check to see if this is an entry for the logged in person
                  if($_SESSION['accountId'] == $tmp[2]) {
                    if(!empty($tmp[1])) {
                      echo '<br />';
                    }
                    echo 'This is you!';
                    // Check to see if this person is a head of a department
                    if($pos == 'Head') {
                      $currentDepHead = TRUE;
                      if(isset($_SESSION['HEAD_OF'])) {
                        if(!in_array($kdep, $_SESSION['HEAD_OF'])) {
                          $_SESSION['HEAD_OF'][] = $kdep;
                        }
                      } else {
                        $_SESSION['HEAD_OF'][] = $kdep;
                      }
                    }
                  }
                  echo '</td>';
                  if(isset($_SESSION['IS_ADMIN']) || $currentDivDirector || ($currentDepHead && $pos != "Head")) {
                    echo '<td class="w3-red"><a href="index.php?Function=list&Remove=' . urlencode($tmp[2]) . '&Department=' . urlencode($kdep) . '&Position=' . $pos . '" onclick="return confirm(\'Are you sure you want to remove ' . $kfName . ' ' . $klName . ' from ' . $kdep . '?\');">Remove</a></td>';
                  }

                  echo "</tr>\n";
                }
                unset($tmp);
              }
            }
          }
        }
        if(($div != 'Corporate Staff' && $kdep != 'Division Director' && $kdep != 'Division Support') && (isset($_SESSION['IS_ADMIN']) || $currentDivDirector || ($currentDepHead && $pos != "Head"))) {
          echo '<tr><td class="w3-center" colspan=8><a href="index.php?Function=list&AddDepartment=' . urlencode($kdep) . '">Add someone to ' . $kdep . '</a></td></tr>' . "\n";
        }
      }
    }
  echo "</table>\n";
  echo "</div>";

}
?>
