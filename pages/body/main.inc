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
