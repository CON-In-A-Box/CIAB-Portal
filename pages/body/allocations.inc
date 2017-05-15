<?php
require_once($FUNCTIONDIR . '/allocations.inc');
echo '<div id="main_content" class="w3-container">' . "\n";
  if(!empty($_REQUEST['DeepLink'])) { // Handle defined and allowed deeplinks
    echo '<header class="w3-cell-row w3-green w3-mobile">';
      require($PAGESDIR . '/base/logo.inc');
      echo '<div class="w3-container w3-cell w3-mobile">';
        echo '<h2>';
          echo $CONHOST . " Hotel Allocations Portal";
        echo '</h2>';
      echo '</div><!--close header text div-->';
    echo '</header><!--close header div-->';
  } else { // Not a deeplink, So setup the regular page
    if (!empty($_REQUEST['Do']) && $_REQUEST['Do'] == "MOVE" && !empty($_REQUEST['From'])) { // We are moving someone
      // Verify we have Current Allocation Information to work with - In case someone else yoinked the room
      unset($_SESSION['AllocInfo']);
      $_SESSION['AllocInfo'] = populateAllocInfo();

      if(empty($_REQUEST['To'])) { // Collect where we are moving them to
        echo '<div class="w3-container w3-orange">' . "\n";
          echo '<div class="w3-panel w3-center">' . "\n";
            echo 'Moving ' . $_SESSION['AllocInfo'][$_REQUEST['From']]['First Name'] . ' ' . $_SESSION['AllocInfo'][$_REQUEST['From']]['Last Name'] . ' from ' . $_REQUEST['From'] . ' to:' . "\n";
          echo "</div>\n";
        
          echo '<form name="MoveTo" method="post" action="index.php?Function=allocations" class="w3-container">' . "\n";
            echo '<input type=hidden name="Do" value="' . $_REQUEST['Do'] . '">' . "\n";
            echo '<input type=hidden name="From" value="' . $_REQUEST['From'] . '">' . "\n";
            
            echo '<select class="w3-select" name="To" required>' . "\n";
              echo '<option value="" disabled selected>Choose destination room</option>' . "\n";
              $roomSet = explode('-', $_REQUEST['From']);
              foreach($AllocRooms[$roomSet[0]] as $floor => $rooms) {
                foreach($rooms as $room => $set) {
                  $roomLabel = $roomSet[0] . '-' . $floor . $room;
                  echo '<option value="' . $roomLabel . '">' . $roomLabel;
                  if(isset($_SESSION['AllocInfo'][$roomLabel])) {
                    echo ' - ' . $_SESSION['AllocInfo'][$roomLabel]['First Name'] . ' ' . $_SESSION['AllocInfo'][$roomLabel]['Last Name'];
                  } else {
                    if(!empty($set['Reserve'])) {
                      echo ' (' . $set['Reserve'] . ')';
                    }
                  }
                  echo "</option>\n";
                }
              }
            echo "</select>\n";
            echo '<div class="w3-bar">' . "\n";
              echo '<a class="w3-button w3-third w3-left w3-yellow" href="index.php?Function=allocations">Cancel</a>' . "\n";
              echo '<button class="w3-button w3-third w3-right w3-green">Make the move</button>' . "\n";
            echo "</div>\n";
          echo "</form>\n";
        echo "</div>\n";
      } else { // We've got all the info, do the move
        /* Retrieve and sanitize submitted data */
        $arguments = [
          'To'   => FILTER_SANITIZE_SPECIAL_CHARS,
          'From' => FILTER_SANITIZE_SPECIAL_CHARS,
          'Do'   => FILTER_SANITIZE_SPECIAL_CHARS,
        ];
        if (!empty($_POST)) {
          $requestInfo = filter_input_array( INPUT_POST, $arguments );
        } else {
          $requestInfo = filter_input_array( INPUT_GET, $arguments );
        }

        // Formulate the search query
        $assignedRoomFieldId = array_search('Room Assigned', $_SESSION['definedFields']);
        
        // Pull the full info of the person moving and save the current Alloc info for room we are moving to
        $personFrom = $Neon->pullIndividualAccount($_SESSION['AllocInfo'][$requestInfo['From']]['Account ID']);
        if(isset($_SESSION['AllocInfo'][$requestInfo['To']])) {
          $holding = $_SESSION['AllocInfo'][$requestInfo['To']];
        }

        // Update the Room that this person is in
        foreach ($personFrom['individualAccount']['customFieldDataList']['customFieldData'] as $key => $val) {
          if(isset( $val['fieldId'] ) && $val['fieldId'] == $assignedRoomFieldId) {
            $rooms = explode('|', $val['fieldValue']);
            $newRooms = [];
            foreach($rooms as $room) {
              if($room == $requestInfo['From']) {
                $newRooms[] = $requestInfo['To'];
              } else {
                $newRooms[] = $room;
              }
            }
            $rooms = implode('|', $newRooms);
            $personFrom['individualAccount']['customFieldDataList']['customFieldData'][$key]['fieldValue'] = $rooms;
            unset($room);
            unset($rooms);
            unset($newRooms);
          }
        }
        
        // Push out the persons new info
        if($Neon->pushIndividualAccount($personFrom)) {
          $_SESSION['AllocInfo'][$requestInfo['To']] = $_SESSION['AllocInfo'][$requestInfo['From']];
          echo '<p class="w3-panel w3-green">';
            echo 'Successfully moved ' . $_SESSION['AllocInfo'][$requestInfo['To']]['First Name'] . ' ' . $_SESSION['AllocInfo'][$requestInfo['To']]['Last Name'] . ' from ' . $requestInfo['From'] . ' to ' . $requestInfo['To'];
          echo '</p>';
          
          // If there was someone in the room moved to, swap them
          if(isset($holding)) {
            $personTo = $Neon->pullIndividualAccount($holding['Account ID']);
            
            // Swap and Update the Room that this person is in
            foreach ($personTo['individualAccount']['customFieldDataList']['customFieldData'] as $key => $val) {
              if(isset($val['fieldId']) && $val['fieldId'] == $assignedRoomFieldId) {
                $rooms = explode('|', $val['fieldValue']);
                $newRooms = [];
                foreach($rooms as $room) {
                  if($room == $requestInfo['To']) {
                    $newRooms[] = $requestInfo['From'];
                  } else {
                    $newRooms[] = $room;
                  }
                }
                $rooms = implode('|', $newRooms);
                $personTo['individualAccount']['customFieldDataList']['customFieldData'][$key]['fieldValue'] = $rooms;
                unset($room);
                unset($rooms);
                unset($newRooms);
              }
            }

            // Push out the persons new info
            if($Neon->pushIndividualAccount($personTo)) {
              $_SESSION['AllocInfo'][$requestInfo['From']] = $holding;
              echo '<p class="w3-panel w3-yellow">';
                echo 'Successfully moved ' . $_SESSION['AllocInfo'][$requestInfo['From']]['First Name'] . ' ' . $_SESSION['AllocInfo'][$requestInfo['From']]['Last Name'] . ' from ' . $requestInfo['To'] . ' to ' . $requestInfo['From'];
              echo '</p>';
            }
          } else { // No one in the room, we can just clear the old one.
            unset($_SESSION['AllocInfo'][$requestInfo['From']]);
          }
        }

        $hotelNames = array_keys($AllocHotels);
        $roomSetFrom = explode('-', $requestInfo['From']);
        $hotelFrom = array_search($roomSetFrom[0], array_column($AllocHotels, 'Prefix'));
        $floorFrom = substr($roomSetFrom[1], 0, 2);
        $roomSetTo = explode('-', $requestInfo['To']);
        $hotelTo = array_search($roomSetTo[0], array_column($AllocHotels, 'Prefix'));
        $floorTo = substr($roomSetTo[1], 0, 2);
        echo '<div class="w3-bar-block">';
          echo '<a href="index.php?Function=allocations&Hotel=' . $hotelNames[$hotelFrom] . '&Floor=' . $floorFrom . '" class="w3-button w3-half">Return to floor ' . $floorFrom . '</a>';
          echo '<a href="index.php?Function=allocations&Hotel=' . $hotelNames[$hotelTo] . '&Floor=' . $floorTo . '" class="w3-button w3-half">Goto floor ' . $floorTo . '</a>';
        echo '</div>';
      }
    } elseif (!empty($_REQUEST['Do']) && $_REQUEST['Do'] == "ASSIGN" && !empty($_REQUEST['To'])) { // Assign someone to a room manually
      if(empty($_REQUEST['accountId'])) { // We need to know who to put in this room
        echo '<div class="w3-row w3-green w3-large w3-center">';
          echo 'Manually assigning room ' . $_REQUEST['To'];
        echo '</div>';
        $result = $Neon->doGeneralSearch();
        echo '<div class="w3-row w3-purple">';
          echo '<div>';
            echo 'Search Results';
          echo '</div>';
          if( isset($result['page']['totalResults'] ) && $result['page']['totalResults'] >= 1 ) {
            echo '<div class="w3-container">';
              echo '<div class="w3-row w3-white">';
                echo '<div class="w3-quarter">Name</div>';
                echo '<div class="w3-quarter">Eamil</div>';
                echo '<div class="w3-quarter">Lottery Status</div>';
                echo '<div class="w3-quarter">Assigned Room(s)</div>';
              echo '</div>';
              foreach($result['searchResults'] as $r) {
                echo '<div class="w3-row w3-white">';
                  echo '<div class="w3-quarter">';
                    echo '<a class="w3-button" href="index.php?Function=allocations&Do=' . $_REQUEST['Do'] . '&To=' . $_REQUEST['To'] . '&accountId=' . $r['Account ID'] . '">' . $r['First Name'] . ' ' . $r['Last Name'] . '</a>';
                  echo '</div>';
                  echo '<div class="w3-quarter">' . $r['Email 1'] . '</div>';
                  if(empty($r['Lottery Status'])) {
                    echo '<div class="w3-quarter">Not Entered</div>' . "\n";
                  } elseif($r['Lottery Status'] == "Entered") {
                    echo '<div class="w3-quarter">On Wait List' . "</div>\n";
                  } else {
                    echo '<div class="w3-quarter">' . $r['Lottery Status'] . "</div>\n";
                  }
                  echo '<div class="w3-quarter">' . $r['Room Assigned'] . '</div>';
                echo '</div>';
              }
            echo '</div>';
          } else {
            echo "<div class='w3-container'>\n";
              echo "<div class='w3-white'>\n";
                echo $result['message'] . "\n";
              echo "</div>\n";
            echo "</div>\n";
          }
        echo "</div>\n";
      } else { // Do the Assignment
        // Verify we have Current Allocation Information to work with - In case someone else yoinked the room
        unset($_SESSION['AllocInfo']);
        $_SESSION['AllocInfo'] = populateAllocInfo();
        
        if(!empty($_SESSION['AllocInfo'][$_REQUEST['To']])) { // Room is already assigned.  We don't want to boot someone out.
          echo '<p class="w3-panel w3-red">ALERT: Room ' . $_REQUEST['To'] . ' is already assigned to ' . $_SESSION['AllocInfo'][$_REQUEST['To']]['First Name'] . " " . $_SESSION['AllocInfo'][$_REQUEST['To']]['Last Name'] . '.  Aborting Move!</p>';
          
          $hotelNames = array_keys($AllocHotels);
          $roomSetTo = explode('-', $_REQUEST['To']);
          $hotelTo = array_search($roomSetTo[0], array_column($AllocHotels, 'Prefix'));
          $floorTo = substr($roomSetTo[1], 0, 2);
          echo '<div class="w3-bar-block">';
            echo '<a href="index.php?Function=allocations&Hotel=' . $hotelNames[$hotelTo] . '&Floor=' . $floorTo . '" class="w3-button">Return to floor ' . $floorTo . '</a>';
          echo '</div>';
        } else { // Room is empty, proceed with the assignment process
          /* Retrieve and sanitize submitted data */
          $arguments = [
            'To'        => FILTER_SANITIZE_SPECIAL_CHARS,
            'accountId' => FILTER_SANITIZE_SPECIAL_CHARS,
            'Do'        => FILTER_SANITIZE_SPECIAL_CHARS,
          ];
          if (!empty($_POST)) {
            $requestInfo = filter_input_array( INPUT_POST, $arguments );
          } else {
            $requestInfo = filter_input_array( INPUT_GET, $arguments );
          }

          // Formulate the search query
          $assignedRoomFieldId = array_search('Room Assigned', $_SESSION['definedFields']);
          $overrideCatFieldId    = array_search('Hotel Override Category', $_SESSION['definedFields']);
          $lotteryStatusFieldId  = array_search('Lottery Status', $_SESSION['definedFields']);
          $lotteryStatusDefault  = array_search('Waiting On Accept', $_SESSION['definedFields']);
          $lotteryStatusWaiting  = array_search('Waiting On Hotel Assignment', $_SESSION['definedFields']);
          $roomPreferenceFieldId = array_search('Bed Preference', $_SESSION['definedFields']);
          $roomPreferenceDefault = array_search('No Preference', $_SESSION['definedFields']);

          // Pull the full info of the person moving and save the current Alloc info for room we are moving to
          $person = $Neon->pullIndividualAccount($requestInfo['accountId']);

          // Verify fieldId is in place in case they don't have the fields in place before now
          $keyCheck = [ $lotteryStatusFieldId, $assignedRoomFieldId, $roomPreferenceFieldId, ];
          $i = count($person['individualAccount']['customFieldDataList']['customFieldData']);
          foreach ($keyCheck as $check) {
            $key = array_search($check, array_column($person['individualAccount']['customFieldDataList']['customFieldData'], 'fieldId'));
            if($key === FALSE) {
              $person['individualAccount']['customFieldDataList']['customFieldData'][$i]['fieldId'] = $check;
              $i++;
            }
            unset($key);
          }
          unset($i);

          // Update the Room that this person is in
          foreach ($person['individualAccount']['customFieldDataList']['customFieldData'] as $key => $val) {
            if(isset( $val['fieldId'] )) {
              if($val['fieldId'] == $assignedRoomFieldId) {
                if(empty($val['fieldValue'])) {
                  $person['individualAccount']['customFieldDataList']['customFieldData'][$key]['fieldValue'] = $requestInfo['To'];
                } else {
                  $rooms = explode('|', $val['fieldValue']);
                  $newRooms = [$requestInfo['To']];
                  foreach($rooms as $room) {
                    if($room != $requestInfo['To']) { // We don't want to double up - though this should be impossible
                      $newRooms[] = $room;
                    }
                  }
                  $rooms = implode('|', $newRooms);
                  $person['individualAccount']['customFieldDataList']['customFieldData'][$key]['fieldValue'] = $rooms;
                  unset($room);
                  unset($rooms);
                  unset($newRooms);
                }
              } elseif($val['fieldId'] == $lotteryStatusFieldId && (empty($val['fieldOptionId']) || $val['fieldOptionId'] != $lotteryStatusWaiting)) {
                $person['individualAccount']['customFieldDataList']['customFieldData'][$key]['fieldOptionId'] = $lotteryStatusDefault;
              } elseif($val['fieldId'] == $roomPreferenceFieldId && empty($val['fieldOptionId'])) {
                $person['individualAccount']['customFieldDataList']['customFieldData'][$key]['fieldOptionId'] = $roomPreferenceDefault;
              }
            }
          }

          // Push out the persons new info
          if($Neon->pushIndividualAccount($person)) {
            echo '<p class="w3-panel w3-green">';
              echo 'Successfully assigned ' . $person['individualAccount']['primaryContact']['firstName'] . ' ' . $person['individualAccount']['primaryContact']['lastName'] . ' to ' . $requestInfo['To'];
            echo '</p>';
          } else { // this shouldn't be possible either
            echo '<p class="w3-panel w3-yellow">';
              echo 'Failed to assign ' . $person['individualAccount']['primaryContact']['firstName'] . ' ' . $person['individualAccount']['primaryContact']['lastName'] . ' to ' . $requestInfo['To'] . ' due to system error';
            echo '</p>';
          }
          
          $roomSetTo = explode('-', $requestInfo['To']);
          $hotelTo = array_search($roomSetTo[0], array_column($AllocHotels, 'Prefix'));
          $hotelNames = array_keys($AllocHotels);
          $floorTo = substr($roomSetTo[1], 0, 2);
          echo '<div class="w3-bar-block">';
            echo '<a href="index.php?Function=allocations&Hotel=' . $hotelNames[$hotelTo] . '&Floor=' . $floorTo . '" class="w3-button">Return to floor ' . $floorTo . '</a>';
          echo '</div>';
        }
      }
    } elseif (empty($_REQUEST['Hotel']) || empty($_REQUEST['Floor'])) { // Default page - Give search, and list hotel floors. Waitlist?
      $result = $Neon->doGeneralSearch();
      echo '<div class="w3-row w3-purple">';
        echo '<div>';
          echo 'Search Results';
        echo '</div>';
        if( isset($result['page']['totalResults'] ) && $result['page']['totalResults'] >= 1 ) {
          echo '<div class="w3-container">';
            echo '<div class="w3-row w3-white">';
              echo '<div class="w3-quarter">Name</div>';
              echo '<div class="w3-quarter">Eamil</div>';
              echo '<div class="w3-quarter">Lottery Status</div>';
              echo '<div class="w3-quarter">Assigned Room(s)</div>';
            echo '</div>';
            foreach($result['searchResults'] as $r) {
              echo '<div class="w3-row w3-white">';
                echo '<div class="w3-quarter">' . $r['First Name'] . ' ' . $r['Last Name'] . '</div>';
                echo '<div class="w3-quarter">' . $r['Email 1'] . '</div>';
                if(empty($r['Lottery Status'])) {
                  echo '<div class="w3-quarter">Not Entered</div>' . "\n";
                } elseif($r['Lottery Status'] == "Entered") {
                  echo '<div class="w3-quarter">On Wait List' . "</div>\n";
                } else {
                  echo '<div class="w3-quarter">' . $r['Lottery Status'] . "</div>\n";
                }
                echo '<div class="w3-quarter">' . $r['Room Assigned'] . '</div>';
              echo '</div>';
            }
          echo '</div>';
        } else {
          echo "<div class='w3-container'>\n";
            echo "<div class='w3-white'>\n";
              echo $result['message'] . "\n";
            echo "</div>\n";
          echo "</div>\n";
        }
      echo "</div>\n";
      echo "<hr>\n";
      foreach ($AllocHotels as $hotel => $set) {
        echo '<div class="w3-row w3-black">' . "\n";
          echo '<div class="w3-center">' . $hotel . "</div>\n";
    
          $i=0;
          echo '<div class="w3-container">' . "\n";
            echo "<div class='w3-row'>\n";
              foreach ($AllocRooms[$set['Prefix']] as $floor => $set) {
                if($i == 4) {
                  $i=0;
                  echo "</div>\n";
                  echo '<div class="w3-row">' . "\n";
                }
                echo "<a href='index.php?Function=allocations&Hotel=" . $hotel . "&Floor=" . $floor . "'" . ' class="w3-col s3 m3 l3 w3-button w3-border">' . $floor . "</a>\n";
                $i++;
              }
            echo "</div>\n";
          echo "</div>\n";
        echo "</div>\n";
      }
    } else { // Give a floor Map
      echo '<div class="w3-row">' . "\n";
        echo '<div class="w3-col w3-button w3-black w3-center">' . $_REQUEST['Hotel'] . ", Floor " . $_REQUEST['Floor'] . "</div>\n";
      echo "</div>\n";

      // Verify we have Current Allocation Information to work with
      unset($_SESSION['AllocInfo']);
      $_SESSION['AllocInfo'] = populateAllocInfo();
      
      $l=0;
      $r=0;
      echo "<div class='w3-container w3-cell-row'>\n";
        foreach($AllocRooms[$AllocHotels[$_REQUEST['Hotel']]['Prefix']][$_REQUEST['Floor']] as $room => $set) {
          if($room %2 == 0) { // would be on the left side - Arbitrary
            if($l == 1) { //next row
              $l=1;
              $r=0;
              echo "</div>\n<div class='w3-container w3-cell-row'>\n";
            } else {
              $l=1;
            }
            echo '<div class="w3-cell w3-half w3-border w3-left">' . "\n";
          } else { // would be on the right side - Arbitrary
            if($l ==1 || $r == 1) { //next row
              $l=0;
              $r=1;
              echo "</div>\n<div class='w3-container w3-cell-row'>\n";
            } else {
              $r=1;
            }
            echo '<div class="w3-cell w3-half w3-border w3-right">' . "\n";
          }
            $roomLabel = $AllocHotels[$_REQUEST['Hotel']]['Prefix'] . '-' . $_REQUEST['Floor'] . $room;
            echo $roomLabel . ' | ' . $set['Type'] . "<br />\n";
            echo '<div class="w3-container">' . "\n";
              if(isset($_SESSION['AllocInfo'][$roomLabel])) {
                if(!empty($_SESSION['AllocInfo'][$roomLabel]['Hotel Override Category'])) {
                  echo $_SESSION['AllocInfo'][$roomLabel]['Hotel Override Category'] . "<br />\n";
                } elseif(!empty($_SESSION['AllocInfo'][$roomLabel]['Hotel Qualifier'])) {
                  echo $_SESSION['AllocInfo'][$roomLabel]['Hotel Qualifier'] . "<br />\n";
                } else {
                  echo $set['Reserve'] . "<br />\n";
                }

                echo $_SESSION['AllocInfo'][$roomLabel]['First Name'] . ' ' . $_SESSION['AllocInfo'][$roomLabel]['Last Name'] . "\n";
                echo '<a class="w3-button w3-padding-small w3-tiny w3-round-xxlarge" href="index.php?Function=allocations&Do=MOVE&From=' . $roomLabel . '">Move</a><br />' . "\n";
                if(empty($_SESSION['AllocInfo'][$roomLabel]['Check In'])) {
                  echo "<span class='w3-khaki'>PENDING ACCEPT</span><br />\n";
                } else {
                  echo $_SESSION['AllocInfo'][$roomLabel]['Check In'] . ' - ' . $_SESSION['AllocInfo'][$roomLabel]['Check Out'] . "<br />\n";
                }
              } else {
                echo $set['Reserve'] . "<br />\n";
                echo '<span class="w3-blue">UNASSIGNED</span>';
                echo '<a class="w3-button w3-padding-small w3-tiny w3-round-xxlarge" href="index.php?Function=allocations&Do=ASSIGN&To=' . $roomLabel . '">Assign</a><br />' . "\n";
                echo "<br />\n";
              }
            echo "</div>\n";
          echo "</div>\n";
        }
      echo "</div>\n";
    }
  }
echo '</div><!--close main_content div-->';
?>
