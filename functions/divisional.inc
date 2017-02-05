<?php

// the place to keep all the division info - easy to edit
$Divisions = [ "Activities", "Administration", "External Relations and Communications", "Facilities", "Hospitality", "Productions", "Systems", "Committees", "Corporate Staff", ];

$ConComPositions = [ "Head", "SubHead", "Specialist" ];

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
  "COF2E2" => [ 
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
  "Main Stage" => [ 
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
  "Partner Seach" => [
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
  "Smoker's Paradise" => [ 
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
  "WebTeam" => [ 
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

function ConComListBuildSearch($page) {
  $conComFieldId = array_search('CVGConCom Current Position', $_SESSION['definedFields']);

  $search = [ 
    'method' => 'account/listAccounts', 
    'criteria' => [
      [$conComFieldId, 'NOT_BLANK', ''],
    ],
    'columns' => [
      'standardFields' => ['Account ID', 'First Name', 'Last Name', 'Preferred Name', 'Email 1'],
      'customFields' => [$conComFieldId],
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
        $line = [
          'Division'       => getDivision($tmp[0]),
          'Department'     => $tmp[0],
          'Position'       => $tmp[1],
          'Last Name'      => $val['Last Name'],
          'Email'          => $val['Email 1'],
        ];

        if (empty($val['Preferred Name'])) {
          $line['First Name'] = $val['First Name'];
        } else {
          $line['First Name'] = $val['Preferred Name'];
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
    die("Could not retrieve ConComList");
  }
  return $return;
}

function ConComListBuild() {
  foreach( $GLOBALS['Departments'] as $kdep => $dep) {
    if( $dep['Division'] != $kdep ) {
      $fullConComList[] = [
        'Division' => $dep['Division'],
        'Department' => $kdep,
        'Position' => 'Department',
        'First Name' => '',
        'Last Name' => '',
        'Email' => $dep['Email'],
        'Note' => '',
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

function ConComListDisplay() {
  global $Divisions, $ConComPositions;
  
  $list = ConComListBuild();
  // Put each entry into a divisional/department mapped array
  foreach($list as $line) {
    $printList[$line['Division']][$line['Department']][$line['Position']][$line['First Name']][$line['Last Name']] = $line['Email'] . "|" . $line['Note'];
  }
  
  // Sort everything so we don't have to later
  ksort_recursive($printList);
  
  // Start the crazy print routine!
  echo "<table>\n";
    echo "<tr id=\"table_head\">\n";
      echo "<th>Department</th>\n";
      echo "<th>Division</th>\n";
      echo "<th>First Name</th>\n";
      echo "<th>Last Name</th>\n";
      echo "<th>Position</th>\n";
      echo "<th>Email</th>\n";
      echo "<th>Note</th>\n";
    echo "</tr>\n";
    foreach( $Divisions as $div) {
      echo "<tr id=\"division\"><th>Division</th><th>" . $div . "</th><th></th><th></th><th>Division</th><th>" . GetDepartmentEmail($div) . "</th><th></th></tr>\n";
      if ( isset($printList[$div]['Division Director'])) {
        foreach( $printList[$div]['Division Director'] as $pos) {
          foreach( $pos as $kfName => $fName) {
            foreach( $fName as $klName => $lName) {
              $tmp = explode("|", $lName);
              echo "<tr><td>Division Director</td><td>" . $div . "</td><td>" . $kfName . "</td><td>" . $klName . "</td><td>Director</td><td>" . $tmp[0] . "</td><td>" . $tmp[1] . "</td></tr>\n";
            }
            unset($tmp);
          }
        }
      }
      if ( isset($printList[$div]['Division Support'])) {
        foreach( $printList[$div]['Division Support'] as $pos) {
          foreach( $pos as $kfName => $fName) {
            foreach( $fName as $klName => $lName) {
              $tmp = explode("|", $lName);
              echo "<tr><td>Division Support</td><td>" . $div . "</td><td>" . $kfName . "</td><td>" . $klName . "</td><td>Specialist</td><td>" . $tmp[0] . "</td><td>" . $tmp[1] . "</td></tr>\n";
            }
            unset($tmp);
          }
        }
      }
      foreach( $printList[$div] as $kdep => $dep) {
        if($div != 'Corporate Staff' && $kdep != "Division Director" && $kdep != "Division Support") {
          echo "<tr id=\"department\"><th>$kdep</th><th>" . $div . "</th><th></th><th></th><th>Department</th><th>" . GetDepartmentEmail($kdep) . "</th><th></th></tr>\n";
        }
        foreach( $ConComPositions as $pos) {
          if( isset($dep[$pos])) {
            foreach( $dep[$pos] as $kfName => $fName) {
              foreach( $fName as $klName => $lName) {
                $tmp = explode("|", $lName);
                echo "<tr>\n";
                  echo "<td>" . $kdep . "</td>\n";
                  echo "<td>" . $div . "</td>\n";
                  echo "<td>" . $kfName . "</td>\n";
                  echo "<td>" . $klName . "</td>\n";
                  echo "<td>" . $pos . "</td>\n";
                  echo "<td>" . $tmp[0] . "</td>\n";
                  echo "<td>" . $tmp[1] . "</td>\n";
                echo "</tr>\n";
              }
            }
          }
        }
      }
    }
  echo "</table>\n";

}
?>
