<?php

namespace volunteers\panes;

require_once($FUNCTIONDIR.'/volunteer.inc');


function display_volunteer_hours()
{
    // Display Current Volunteer Status
    $data = get_volunteer_hour_summary($_SESSION['accountId']);
    $spentdata = volunteer_prizes_for_user($_SESSION['accountId']);
    if ($data && !empty($data)) {
        $total = 0;
        $spent = 0;
        foreach ($data as $entry) {
            $total += $entry['Total Hours'];
        }

        foreach ($spentdata as $entry) {
            if (!isset($entry['Aquired']) || $entry['Aquired'] == 0) {
                continue;
            }
            if ($entry['Promo'] != 'yes') {
                $spent += $entry['Value'] * $entry['Aquired'];
            }
        }
        echo "<div class='w3-container w3-green'>\n";
        echo "<table class='w3-table w3-striped w3-bordered w3-white'>\n";
        echo "<tr><th class='w3-center w3-green' colspan=2>Current Volunteer Status</th></tr>\n";
        echo "<tr><th class='w3-center w3-green' colspan=2>Total of ".$total." hours earned</th></tr>\n";
        echo "<tr><th class='w3-center w3-blue' colspan=2>Total of  ".$spent." hours spent</th></tr>\n";
        $output = array();
        foreach ($data as $entry) {
            echo '<tr><td>'.htmlspecialchars($entry['Department Worked']).'</td>';
            echo "<td>".($entry['Total Hours'] + 0)." hours</td>";
            echo "</tr>\n";
        }
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
        foreach ($data as $entry) {
            if (!isset($entry['Aquired']) || $entry['Aquired'] == 0) {
                continue;
            }
            echo "<tr><td>".htmlspecialchars($entry['Name']).'</td>';
            echo "<td>".$entry['Aquired'].'</td>';
            echo "</tr>\n";
        }
        echo "</table>\n";
        echo "</div>\n";
    }

}


$homepage_panes[] = 'volunteers\panes\display_volunteer_hours';
$homepage_panes[] = 'volunteers\panes\display_volunteer_prizes';
