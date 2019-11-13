<?php

namespace volunteers\panes;

require_once($FUNCTIONDIR.'/volunteer.inc');


function display_volunteer_hours()
{
    global $ConComHours;
    // Display Current Volunteer Status
    $concom = null;
    if (class_exists('\\concom\\POSITION') && method_exists('\\concom\\POSITION', 'getConComPosition')) {
        $concom = \concom\POSITION::getConComPosition($_SESSION['accountId']);
        if (empty($concom)) {
            $concom = null;
        }
    }
    $data = get_volunteer_hour_summary($_SESSION['accountId']);
    $spentdata = volunteer_prizes_for_user($_SESSION['accountId']);
    if ($concom || ($data && !empty($data))) {
        $total = 0;
        $spent = 0;
        if ($concom) {
            $total = $ConComHours;
        } else {
            foreach ($data as $entry) {
                $total += $entry['Total Hours'];
            }
        }

        foreach ($spentdata as $entry) {
            if (!isset($entry['Aquired']) || $entry['Aquired'] == 0) {
                continue;
            }
            if ($entry['Promo'] != 'yes') {
                $spent += $entry['Value'] * $entry['Aquired'];
            }
        }
        echo "<div class='UI-container event-color-primary'>\n";
        echo "<table class='UI-stripedtable'>\n";
        echo "<tr><th class='UI-center event-color-primary' colspan=2>Current Volunteer Status</th></tr>\n";
        echo "<tr><th class='UI-center event-color-primary' colspan=2>Total of ".$total." hours earned</th></tr>\n";
        echo "<tr><th class='UI-center event-color-secondary' colspan=2>Total of  ".$spent." hours spent</th></tr>\n";
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


function _cmp_name($a, $b)
{
    return strcmp($a['Name'], $b['Name']);

}


function display_volunteer_prizes()
{
    $data = volunteer_prizes_for_user($_SESSION['accountId']);
    if ($data && !empty($data)) {
        usort($data, "\\volunteers\\panes\\_cmp_name");
        $aquired = [];
        foreach ($data as $entry) {
            if (isset($entry['Aquired']) && $entry['Aquired'] > 0) {
                $aquired[] = $entry;
            }
        }

        if (!empty($aquired)) {
            echo "<div class='UI-container event-color-secondary'>\n";
            echo "<table class='UI-stripedtable'>\n";
            echo "<tr><th class='UI-center event-color-secondary' colspan=2>Gifts claimed so far this year</th></tr>\n";
            $output = array();
            foreach ($aquired as $entry) {
                echo "<tr><td>".htmlspecialchars($entry['Name']).'</td>';
                echo "<td>".$entry['Aquired'].'</td>';
                echo "</tr>\n";
            }
            echo "</table>\n";
            echo "</div>\n";
        }
    }

}


$homepage_panes[] = 'volunteers\panes\display_volunteer_hours';
$homepage_panes[] = 'volunteers\panes\display_volunteer_prizes';
