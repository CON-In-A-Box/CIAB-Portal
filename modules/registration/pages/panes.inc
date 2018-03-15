<?php

/*.
    require_module 'standard';
.*/

namespace registration\panes;

require_once(__DIR__.'/../functions/events.inc');


function badges()
{
    $badges = get_tickets($_SESSION['accountId']);
    $lines = [];
    if (!empty($badges)) {
        foreach ($badges as $badge) {
            $name = $badge['BadgeName'];
            if ($name == null) {
                $name = "&lt;unselected&gt;";
            }
            if (array_key_exists($badge['EventName'], $lines)) {
                $lines[$badge['EventName']][] = $name;
            } else {
                $lines[$badge['EventName']] = [$name];
            }
        }
        ksort($lines);

        echo "<div class='w3-container w3-blue'>\n";
        echo "<table class='w3-table w3-striped w3-bordered w3-white'>\n";
        echo "<tr><th class='w3-center w3-blue' colspan=3'>Badges Purchased</th></tr>\n";
        foreach ($lines as $con => $names) {
            sort($names);
            echo "<tr><td>$con</td><td>".count($names)."</td><td>".implode(', ', $names)."</td></tr>\n";
        }
        echo "<tr class='w3-blue'><th colspan=3 class='w3-center'></th></tr>";
        echo "</table>\n";
        echo "</div>\n";
    }

}


$homepage_panes[] = 'registration\panes\badges';
