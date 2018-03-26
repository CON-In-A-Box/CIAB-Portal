<?php

namespace concom\panes;

require_once(__DIR__.'/../functions/concom.inc');


function draw_status()
{
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

}


function draw_history()
{
    $events = events_served($_SESSION['accountId']);
    $c = count($events);

    echo "<div class='w3-container w3-green'>\n";
    echo "<table class='w3-table w3-striped w3-bordered w3-white'>\n";
    echo "<tr><th style=\"text-align:center;\" class=\"w3-green\" colspan=5>Served on ConCom for ".$c;
    if ($c > 1) {
        echo " Events!  ";
    } else {
        echo " Event!  ";
    }
    echo "</th></tr>";

    $i = 0;
    foreach ($events as $val) {
        if ($i % 5 == 0) {
            if ($i != 0) {
                echo "</tr>\n";
            }
            echo "<tr>";
        }
        echo "<td>".$val."</td>";
        $i++;
    }
    echo "</tr>\n";
    echo "<tr class=\"w3-green\" ><th colspan=5 class='w3-center'>";
    echo "Thank you for your help!</th></tr>";
    echo "</table>\n";
    echo "</div>\n";

}


function phone_number()
{
    // If this person is on ConCom, but hasn't elected about their phone number, nudge them
    $cvgConComPhoneDisplay = search_definedFields('CVGConCom Publish Phone');
    if (isset($_SESSION['customFields']['currConComPos']) && empty($_SESSION['customFields'][$cvgConComPhoneDisplay])) {
        echo '<p class="w3-container w3-center">';
        echo "We've noticed you haven't decided about your phone number.<br />";
        echo 'Please <a href="index.php?Function=profile">Update Your Profile</a> to make that selection.';
        echo '</p>';
    }

}


function meetings()
{
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

}

 
if (isset($_SESSION['customFields']['currConComPos'])) {
    $homepage_panes[] = 'concom\panes\phone_number';
    $homepage_panes[] = 'concom\panes\draw_status';
    $homepage_panes[] = 'concom\panes\meetings';
    $homepage_panes[] = 'concom\panes\draw_history';
}
