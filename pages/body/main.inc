<?php require($PAGESDIR . '/base/leftnav.inc'); ?>
<div id="main_content" class="w3-cell w3-cell-top w3-mobile">
<?php
echo "<div class='w3-container'>\n";
  echo "<h2>Welcome " . $_SESSION['preferredName'] . "!</h2>\n";
echo "</div>\n";

// Display Meeting Sign In Info
echo "<div class='w3-container w3-green'>\n";
  echo "<table class='w3-table w3-striped w3-bordered w3-white'>\n";
    echo "<tr><th class='w3-center w3-green' colspan=3>2017 Official Meeting Attendance</th></tr>\n";
    $nowDate = date("Ymd");
    $attendKey = array_search('2017 Official Meeting Attendance', $_SESSION['definedFields']);
    foreach( $_SESSION['definedFields']['2017Meetings'] as $key => $val ) {
      $ldate = explode("-", $val);
      $checkDate = $ldate[0] . $ldate[1] . rtrim($ldate[2]);
      if ( $nowDate >= $checkDate ) {
        echo "<tr><td>" . $ldate[1] . "/" . rtrim($ldate[2]) . "/" . $ldate[0]. "</td>";
        echo "<td>" . ltrim($ldate[3]) . "</td>";
        if ( isset( $_SESSION['customFields'][$attendKey]) && in_array( $key, $_SESSION['customFields'][$attendKey] )) {
          echo "<td class='w3-lime'>Checked In";
        } else {
          if ($nowDate == $checkDate) {
            echo "<td class='w3-yellow'><a href='index.php?Function=signin&AttendField=" . $attendKey . "&DateFeed=" . $key . "'>Sign In Now!</a>";
          } else {
            echo "<td>Not Checked In";
          }
        }
        echo "</td></tr>\n";
      }
      unset($ldate);
      unset($checkDate);
    }
    unset($key);
    unset($val);
    unset($nowDate);
  echo "</table>\n";
echo "</div>\n";

// Display Hotel Information
if ( isset( $_SESSION['customFields']['hotelLotteryStatus']) && $_SESSION['customFields']['hotelLotteryStatus'][0] != array_search('Declined', $_SESSION['definedFields'])) {
  require_once($FUNCTIONDIR . '/allocations.inc');
  echo "<div class='w3-container w3-purple'>\n";
    echo "<table class='w3-table w3-striped w3-bordered w3-white'>\n";
      echo "<tr><th class='w3-center w3-purple'>Current Hotel Lottery Status</th></tr>\n";
      switch ($_SESSION['definedFields'][$_SESSION['customFields']['hotelLotteryStatus'][0]]) {
        case "Entered":
          echo "<tr><td>\n";
          echo "You are entered in the lottery and on the waiting list for a room.\n";
          echo "</td></tr>\n";
          break;
        case "Waiting On Accept":
          echo "<tr><td class='w3-center w3-large w3-red'>\n";
            echo "<i class='fa fa-bed w3-spin'></i>\n";
            echo "<b>You have won a room from the hotel lottery!</b>\n";
            echo "<i class='fa fa-bed w3-spin'></i>\n";
          echo "</td></tr>\n";
          ?>

          <script>
          function funcClickYes() {
            var cidate = document.forms["lotteryAccept"]["checkIn"].value;
            var codate = document.forms["lotteryAccept"]["checkOut"].value;
            var re = /^(\d{4})-(\d{2})-(\d{2})$/;
            if (cidate==null||cidate=="") {
              alert("Select a valid Check In date");
              return false;
            }
            if (!cidate.match(re)) {
              alert("Check In date must be in the yyyy-mm-dd format");
              return false;
            }
            if (codate==null||codate=="") {
              alert("Select a valid Check Out date");
              return false;
            }
            if (!codate.match(re)) {
              alert("Check Out date must be in the yyyy-mm-dd format");
              return false;
            }
            var txt = "This info will be sent to the hotel, please verify it is accurate.\n\n";
            txt = txt + "<?php
              echo $_SESSION['firstName'] . ' ' . $_SESSION['lastName'] . '\n' . $_SESSION['addr1'] . '\n';
              if (!empty($_SESSION['addr2'])) {
                echo $_SESSION['addr2'] . '\n';
              }
              echo $_SESSION['city'] . ", " . $_SESSION['state'] . " " . $_SESSION['zip'] . '\n';
              echo "Phone: " . $_SESSION['phone'] . '\n\n';
              echo "If this is not correct, cancel and use the Profile tab to update." . '\n\n';
              echo "Is this information correct?";
            ?>";
            return confirm(txt);
          }
          
          function funcClickNo() {
            var txt = "Warning: If you decline, this lottery entry will be removed and the room will be offered to someone else\n\nAre you sure you want to release this lottery entry?";
            return confirm(txt);
          }
          </script>

          <?php
          $rooms = explode("|", $_SESSION['customFields']['hotelAssignedRoom'][0]);
          echo "<form action='index.php?Function=main' method='post' name='lotteryAccept' id='LotteryYesNo'>\n";
            echo "<input type=hidden name='accountId' value='" . $_SESSION['accountId'] . "'>\n";
            // We shouldn't be passing the room info to the page, but it was easier on first draft.  Good clean up opportunity
            echo "<input type=hidden name='rooms' value='" . $_SESSION['customFields']['hotelAssignedRoom'][0] . "'>\n";

            $i=1;
            foreach($rooms as $room) {
              echo "<tr><td>\n";
                echo "<div class='w3-container w3-center w3-border'>\n";
                  if($i == 1) {
                    echo "Your room is waiting for you at the ";
                  } else {
                    echo "Room " . $i . " is waiting for you at the ";
                  }
                  $i++;
                  $roomPrefix = explode("-", $room);
                  foreach($AllocHotels as $hkey => $hotel) {
                    if($roomPrefix[0] == $hotel['Prefix']) {
                      echo $hkey . "<br />\n";
                    }
                  }
                  $roomNumber = substr($roomPrefix[1], 2);
                  $floor = substr($roomPrefix[1], 0, 2);
                  echo "The room is a " . $AllocRooms[$roomPrefix[0]][$floor][$roomNumber]['Type'] . " bed in the ";

                  if($roomNumber < 50) {
                    echo "South/Tall";
                  } else {
                    echo "North/Short";
                  }
                  echo "Tower\n";
                echo "</div>\n";
              echo "</td></tr>\n";
            }

            echo "<tr><td>\n";
              echo "<div class='w3-brown w3-wide w3-large w3-center'>\n";
                echo "Do you want this room?\n";
              echo "</div>\n";
            echo "</td></tr>\n";

            echo "<tr><td>\n";
              echo "<div class='w3-center w3-large w3-container w3-lime'>\n";
                echo "This info is required for a hotel room\n";
              echo "</div>\n";
              echo "<div class='w3-container w3-lime'>\n";
                echo "<div class='w3-half w3-khaki'>\n";
                  echo "<label>Check In</label>\n";
                  echo "<input class='w3-input w3-border' type='date' name='checkIn' min='2017-07-01' max='2017-07-08'>\n";
                echo "</div>\n";
                echo "<div class='w3-half w3-khaki'>\n";
                  echo "<label>Check Out</label>\n";
                  echo "<input class='w3-input w3-border' type='date' name='checkOut' min='2017-07-07' max='2017-07-12'>\n";
                echo "</div>\n";
              echo "</div>\n";
              echo "<div class='w3-container w3-lime'>\n";
                echo "<div class='w3-khaki'>\n";
                  echo "Other people staying with you:\n";
                  echo "<div class='w3-container'>\n";
                    echo "<input type='text' name='other1' class='w3-input w3-border' placeholder='Full Legal Name'>\n";
                    echo "<input type='text' name='other2' class='w3-input w3-border' placeholder='Full Legal Name'>\n";
                    echo "<input type='text' name='other3' class='w3-input w3-border' placeholder='Full Legal Name'>\n";
                  echo "</div>\n";
                echo "</div>\n";
              echo "</div>\n";  
              echo "<button class='w3-button w3-block w3-large w3-green' type=submit name='accept' value='Yes' onclick='return funcClickYes();'><b>Yes, I want it!</b></button>\n";
            echo "</td></tr>\n";
            echo "<tr><td>\n";
              echo "<div class='w3-container'>\n";
                echo "<button class='w3-button w3-block w3-right w3-red' type=submit name='accept' value='No' onclick='return funcClickNo();'>No, release this to someone else</button>\n";
              echo "</div>\n";
            echo "</td></tr>\n";
          echo "</form>\n";
          break;
        case "Waiting On Hotel Assignment":
          echo "<tr><td>\n";
          echo "The Hotel is entering your reservation and you should hear from them soon!\n";
          echo "</td></tr>\n";
          break;
        case "Assigned":
          echo "<tr><td>\n";
          echo "You have a room.  Don't forget to contact the Hotel and use yoru credit card to secure it!\n";
          echo "</td></tr>\n";
          break;
      }
    echo "</table>\n";
  echo "</div>\n";
}
  
// Display Current ConCom Status
if ( isset( $_SESSION['customFields']['currConComPos'])) {
  echo "<div class='w3-container w3-black'>\n";
    echo "<table class='w3-table w3-striped w3-bordered w3-white'>\n";
      echo "<tr><th class='w3-center w3-black'>Current ConCom Status</th></tr>\n";
      foreach( $_SESSION['customFields']['currConComPos'] as $val ) {
        $positionsHeld = explode("|", $val);
        foreach( $positionsHeld as $pos) {
          $tmp = explode(",", $pos);
          echo "<tr><td>" . $tmp[1] . " of " . $tmp[0];
          if ( !empty( $tmp[2] )) {
            echo " - Note: " . $tmp[2];
          }
          echo "</td></tr>\n";
        }
        unset($pos);
        unset($tmp);
      }
      unset($val);
      unset($positionsHeld);
    echo "</table>\n";
  echo "</div>\n";
}

// Display ConCom Service History
/* // Hide this info until ready for updates
echo "<div class='w3-container'>\n";
  echo "<table class='w3-table w3-striped w3-bordered'>\n";
  echo "<tr><th style=\"text-align:center;\" colspan=2>Previous ConCom Years Served</th></tr>";
  foreach( $_SESSION['definedFields']['conComYears'] as $key => $val ) {
    echo "<tr><td>" . $val . "</td>";
    if ( isset( $_SESSION['customFields'][array_search('CVGConCom Years Served', $_SESSION['definedFields'])] ) && in_array( $key, $_SESSION['customFields'][array_search('CVGConCom Years Served', $_SESSION['definedFields'])] )) {
      echo "<td style='background-color:green'>On ConCom";
    } else {
      echo "<td>Not On ConCom";
    }
    echo "</td></tr>\n";
  }
  unset($val);
  echo "</table>\n";
echo "</div>\n";
*/

?>
</div><!--close main_content div-->
