<?php require($PAGESDIR.'/base/leftnav.inc');

require_once($FUNCTIONDIR.'/volunteer.inc');

function display_volunteer_hours()
{
    // Display Current Volunteer Status
    $data = get_volunteer_hour_summary($_SESSION['accountId']);
    if ($data && !empty($data)) {
        echo "<div class='w3-container w3-green'>\n";
        echo "<table class='w3-table w3-striped w3-bordered w3-white'>\n";
        echo "<tr><th class='w3-center w3-green' colspan=2>Current Volunteer Status</th></tr>\n";
        $output = array();
        $total = 0;
        foreach ($data as $entry) {
            echo "<tr><td>".$entry['Department Worked'].'</td>';
            echo "<td>".$entry['Total Hours'].' hours</td>';
            echo "</tr>\n";
            $total += $entry['Total Hours'];
        }
        echo "<tr><th class='w3-center w3-green' colspan=2>Total of ".$total." hours earned</th></tr>\n";
        echo "</table>\n";
        echo "</div>\n";
    }
}

function display_volunteer_prizes()
{
    $data = volunteer_prizes_for_user($_SESSION['accountId']);
    if ($data && !empty($data)) {
        echo "<div class='w3-container w3-blue'>\n";
        echo "<table class='w3-table w3-striped w3-bordered w3-white'>\n";
        echo "<tr><th class='w3-center w3-blue' colspan=2>Rewards claimed so far this year</th></tr>\n";
        $output = array();
        $total = 0;
        foreach ($data as $entry) {
            if (!isset($entry['Aquired']) || $entry['Aquired'] == 0)
                continue;
            echo "<tr><td>".$entry['Name'].'</td>';
            echo "<td>".$entry['Aquired'].'</td>';
            echo "</tr>\n";
            if ($entry['Promo'] != 'yes') {
                $total += $entry['Value'] * $entry['Aquired'];
            }
        }
        echo "<tr><th class='w3-center w3-blue' colspan=2>Total of  ".$total." hours spent</th></tr>\n";
        echo "</table>\n";
        echo "</div>\n";
    }
}

 ?>
<div id="main_content" class="w3-cell w3-cell-top w3-mobile">
<?php
echo '<div class="w3-container w3-center">';
echo '<b>Welcome '.$_SESSION['preferredName'].'!</b>';
echo "</div>\n";

// If this person is on ConCom, but hasn't elected about their phone number, nudge them
$cvgConComPhoneDisplay = search_definedFields('CVGConCom Publish Phone');
if (isset($_SESSION['customFields']['currConComPos']) && empty($_SESSION['customFields'][$cvgConComPhoneDisplay])) {
    echo '<p class="w3-container w3-center">';
    echo "We've noticed you haven't decided about your phone number.<br />";
    echo 'Please <a href="index.php?Function=profile">Update Your Profile</a> to make that selection.';
    echo '</p>';
}

/*
// Display Hotel Information
if ( isset( $_SESSION['customFields']['hotelLotteryStatus']) && $_SESSION['customFields']['hotelLotteryStatus'][0] != search_definedFields('Declined')) {
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
if($floor > 2) {
echo "The room is a " . $AllocRooms[$roomPrefix[0]][$floor][$roomNumber]['Type'] . " bed in the ";

if($roomNumber < 50) {
echo "South/Tall";
} else {
echo "North/Short";
}
echo "Tower\n";
} else {
echo "The room is a party cabana (you are hosting a room party).";
}
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
echo "<input class='w3-input w3-border' type='date' name='checkIn' min='2017-07-01' max='2017-07-08' placeholder='YYYY-MM-DD'>\n";
echo "</div>\n";
echo "<div class='w3-half w3-khaki'>\n";
echo "<label>Check Out</label>\n";
echo "<input class='w3-input w3-border' type='date' name='checkOut' min='2017-07-07' max='2017-07-12' placeholder='YYYY-MM-DD'>\n";
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
echo "<tr><td class='w3-center'>\n";
echo "The hotel sent you a confirmation.<br />Don't forget to contact the Hotel and use your credit card to secure it!\n";
echo "</td></tr>\n";
$rooms = explode("|", $_SESSION['customFields']['hotelAssignedRoom'][0]);
$i=0;
$confs = explode("|", $_SESSION['customFields']['hotelRoomConfirmation'][0]);
$checkIns = explode("|", $_SESSION['customFields']['hotelRoomCheckIn'][0]);
$checkOuts = explode("|", $_SESSION['customFields']['hotelRoomCheckOut'][0]);
foreach($rooms as $room) {
echo '<tr><td class="w3-center">';
if($i == 0) {
echo 'Your room ';
} else {
$tmp = $i+1;
echo 'Room ' . $tmp . ' ';
unset($tmp);
}
echo 'has confirmation # ';
if(isset($confs[$i])) {
echo $confs[$i]; // breakfix for when hotel doesn't give individual confirmation numbers
} else {
echo $confs[0]; // unless the system has been overrridden, index 0 must exist
}
echo '<br />Checking in: ';
if(isset($checkIns[$i])) {
echo $checkIns[$i];
} else {
echo $checkIns[0];
}
echo ', Checking out: ';
if(isset($checkOuts[$i])) {
echo $checkOuts[$i];
} else {
echo $checkOuts[0];
}
echo '</td></tr>';
$i++;
}
break;
}
echo "</table>\n";
echo "</div>\n";
}
*/

// Display Meeting Sign In Info
echo "<div class='w3-container w3-green'>\n";
echo "<table class='w3-table w3-striped w3-bordered w3-white'>\n";
echo "<tr><th class='w3-center w3-green' colspan=3>Official Meeting Attendance</th></tr>\n";
$nowDate = date("Ymd");
$attendKey = search_definedFields('Official Meeting Attendance');
foreach ($_SESSION['definedFields']['Meetings'] as $key => $val) {
    $ldate = explode("-", $val);
    $checkDate = $ldate[0].$ldate[1].rtrim($ldate[2]);
    if ($nowDate >= $checkDate) {
        echo "<tr><td>".$ldate[1]."/".rtrim($ldate[2])."/".$ldate[0]."</td>";
        echo "<td>".ltrim($ldate[3])."</td>";
        if (isset($_SESSION['customFields'][$attendKey]) && in_array($key, $_SESSION['customFields'][$attendKey])) {
            echo "<td class='w3-lime'>Checked In";
        } else {
            if ($nowDate == $checkDate) {
                echo "<td class='w3-yellow'><a href='index.php?Function=signin&AttendField=".$attendKey."&DateFeed=".$key."'>Sign In Now!</a>";
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

// Display Current ConCom Status
if (isset($_SESSION['customFields']['currConComPos'])) {
    echo "<div class='w3-container w3-black'>\n";
    echo "<table class='w3-table w3-striped w3-bordered w3-white'>\n";
    echo "<tr><th class='w3-center w3-black'>Current ConCom Status</th></tr>\n";
    foreach ($_SESSION['customFields']['currConComPos'] as $val) {
        $positionsHeld = explode("|", $val);
        foreach ($positionsHeld as $pos) {
            $tmp = explode(",", $pos);
            echo "<tr><td>".$tmp[1]." of ".$tmp[0];
            if (!empty($tmp[2])) {
                echo " - Note: ".$tmp[2];
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
if ( isset( $_SESSION['customFields'][search_definedFields('CVGConCom Years Served')] ) && in_array( $key, $_SESSION['customFields'][search_definedFields('CVGConCom Years Served')] )) {
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

display_volunteer_hours();
display_volunteer_prizes();

?>
</div><!--close main_content div-->
