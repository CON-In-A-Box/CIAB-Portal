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
                  echo '<option value="' . $roomLabel . '">' . $roomLabel . "</option>\n";
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
        $personFrom = $GLOBALS['Neon']->pullIndividualAccount($_SESSION['AllocInfo'][$requestInfo['From']]['Account ID']);
        if(isset($_SESSION['AllocInfo'][$requestInfo['To']])) {
          $holding = $_SESSION['AllocInfo'][$requestInfo['To']];
        }

        // Update the Room that this person is in
        foreach ($personFrom['individualAccount']['customFieldDataList']['customFieldData'] as $key => $val) {
          if(isset( $val['fieldId'] ) && $val['fieldId'] == $assignedRoomFieldId) {
            $rooms = explode('|', $personFrom['individualAccount']['customFieldDataList']['customFieldData'][$key]['fieldValue']);
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
        if($GLOBALS['Neon']->pushIndividualAccount($personFrom)) {
          $_SESSION['AllocInfo'][$requestInfo['To']] = $_SESSION['AllocInfo'][$requestInfo['From']];
          echo '<p class="w3-panel w3-green">';
            echo 'Successfully moved ' . $_SESSION['AllocInfo'][$requestInfo['To']]['First Name'] . ' ' . $_SESSION['AllocInfo'][$requestInfo['To']]['Last Name'] . ' from ' . $requestInfo['From'] . ' to ' . $requestInfo['To'];
          echo '</p>';
          
          // If there was someone in the room moved to, swap them
          if(isset($holding)) {
            $personTo = $GLOBALS['Neon']->pullIndividualAccount($holding['Account ID']);
            
            // Swap and Update the Room that this person is in
            foreach ($personTo['individualAccount']['customFieldDataList']['customFieldData'] as $key => $val) {
              if(isset($val['fieldId']) && $val['fieldId'] == $assignedRoomFieldId) {
                $rooms = explode('|', $personTo['individualAccount']['customFieldDataList']['customFieldData'][$key]['fieldValue']);
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
            if($GLOBALS['Neon']->pushIndividualAccount($personTo)) {
              $_SESSION['AllocInfo'][$requestInfo['From']] = $holding;
              echo '<p class="w3-panel w3-yellow">';
                echo 'Successfully moved ' . $_SESSION['AllocInfo'][$requestInfo['From']]['First Name'] . ' ' . $_SESSION['AllocInfo'][$requestInfo['From']]['Last Name'] . ' from ' . $requestInfo['To'] . ' to ' . $requestInfo['From'];
              echo '</p>';
            }
          } else { // No one in the room, we can just clear the old one.
            unset($_SESSION['AllocInfo'][$requestInfo['From']]);
          }
        }
        $roomSetFrom = explode('-', $requestInfo['From']);
        $hotelFrom = array_search($roomSetFrom[0], array_column($AllocHotels, 'Prefix'));
        $floorFrom = substr($roomSetFrom[1], 0, 2);
        $roomSetTo = explode('-', $requestInfo['To']);
        $hotelTo = array_search($roomSetTo[0], array_column($AllocHotels, 'Prefix'));
        $floorTo = substr($roomSetTo[1], 0, 2);
        echo '<div class="w3-bar-block">';
          echo '<a href="index.php?Function=allocations&Hotel=' . $hotelFrom . '&Floor=' . $floorFrom . '" class="w3-button w3-half">Return to floor ' . $floorFrom . '</a>';
          echo '<a href="index.php?Function=allocations&Hotel=' . $hotelTo . '&Floor=' . $floorTo . '" class="w3-button w3-half">Goto floor ' . $floorTo . '</a>';
        echo '</div>';
      }
    } elseif (empty($_REQUEST['Hotel']) || empty($_REQUEST['Floor'])) { // Default page - Give search, and list hotel floors. Waitlist?
      /* Retrieve and sanitize POST data */
      $arguments = [
        'firstName' => FILTER_SANITIZE_SPECIAL_CHARS,
        'lastName'  => FILTER_SANITIZE_SPECIAL_CHARS,
        'email'     => FILTER_SANITIZE_EMAIL,
      ];
      if (!empty($_POST)) {
        $searchCriteria = filter_input_array( INPUT_POST, $arguments );
      } else {
        $searchCriteria = filter_input_array( INPUT_GET, $arguments );
      }

      // Formulate the search query
      $assignedRoomFieldId   = array_search('Room Assigned', $_SESSION['definedFields']);
      $overrideCatFieldId    = array_search('Hotel Override Category', $_SESSION['definedFields']);
      $overrideRoomFieldId   = array_search('Hotel Override', $_SESSION['definedFields']);
      $lotteryStatusFieldId  = array_search('Lottery Status', $_SESSION['definedFields']);
      $qualifierFieldId      = array_search('Hotel Qualifier', $_SESSION['definedFields']);
      $roomPreferenceFieldId = array_search('Bed Preference', $_SESSION['definedFields']);
      $checkInFieldId        = array_search('Check In', $_SESSION['definedFields']);
      $checkOutFieldId       = array_search('Check Out', $_SESSION['definedFields']);
      $other1FieldId         = array_search('Other In Room 1', $_SESSION['definedFields']);
      $other2FieldId         = array_search('Other In Room 2', $_SESSION['definedFields']);
      $other3FieldId         = array_search('Other In Room 3', $_SESSION['definedFields']);

      $search = [ 
        'method' => 'account/listAccounts', 
        'columns' => [
          'standardFields' => ['Account ID', 'First Name', 'Last Name', 'Email 1', 'City', 'State'],
          'customFields' => [
            $assignedRoomFieldId,
            $overrideCatFieldId,
            $overrideRoomFieldId,
            $lotteryStatusFieldId,
            $qualifierFieldId,
            $roomPreferenceFieldId,
            $checkInFieldId,
            $checkOutFieldId,
            $other1FieldId,
            $other2FieldId,
            $other3FieldId,
          ],
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
      <form action="index.php?Function=allocations" method="POST">
        <div class="w3-row w3-green">
          <div>
            Hotel Allocation Account Search
            <button class="w3-button w3-brown w3-right" style="padding: 0px 16px;">Search</button>
          </div>
          <div class="w3-container">
            <div class="w3-half">
              <input type="text" class="w3-input w3-border" name="firstName" placeholder="First Name" value="<?php echo htmlentities( $searchCriteria['firstName'] ); ?>" />
            </div>
            <div class="w3-half">
              <input type="text" class="w3-input w3-border" name="lastName" placeholder="Last Name" value="<?php echo htmlentities( $searchCriteria['lastName'] ); ?>" />
            </div>
            <div>
              <input type="text" class="w3-input w3-border" name="email" placeholder="Email" value="<?php echo htmlentities( $searchCriteria['email'] ); ?>" />
            </div>
            <button class="w3-button w3-block w3-brown">Search</button>
          </div>
        </div>
      </form>
      <div class="w3-row w3-purple">
        <div>
          Search Results
        </div>
        <?php if( isset($result['page']['totalResults'] ) && $result['page']['totalResults'] >= 1 ) { ?>
          <div class="w3-container">
            <div class="w3-row w3-white">
              <div class="w3-quarter">Name</div>
              <div class="w3-quarter">Eamil</div>
              <div class="w3-quarter">Lottery Status</div>
              <div class="w3-quarter">Assigned Room(s)</div>
            </div>
            <?php foreach($result['searchResults'] as $r) { ?>
              <div class="w3-row w3-white">
                <div class="w3-quarter"><?php echo $r['First Name'] . " " . $r['Last Name']; ?></div>
                <div class="w3-quarter"><?php echo $r['Email 1']; ?></div>
                <?php
                if(empty($r['Lottery Status'])) {
                  echo '<div class="w3-quarter">Not Entered</div>' . "\n";
                } elseif($r['Lottery Status'] == "Entered") {
                  echo '<div class="w3-quarter">On Wait List' . "</div>\n";
                } else {
                  echo '<div class="w3-quarter">' . $r['Lottery Status'] . "</div>\n";
                }
                ?>
                <div class="w3-quarter"><?php echo $r['Room Assigned']; ?></div>
              </div>
            <?php } ?>
          </div>
        <?php
        } else {
          echo "<div class='w3-container'>\n";
            echo "<div class='w3-white'>\n";
              echo $message . "\n";
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
                echo "<span class='w3-blue'>UNASSIGNED</span><br />\n";
              }
            echo "</div>\n";
          echo "</div>\n";
        }
      echo "</div>\n";
    }
  }
echo '</div><!--close main_content div-->';
?>
