<?php

/*.
    require_module 'standard';
.*/

namespace registration\panes;

require_once(__DIR__.'/../functions/events.inc');


function _htmlsafeify($array)
{
    $output = [];
    foreach ($array as $key => $value) {
        $output[$key] = htmlspecialchars($value);
    }
    return $output;

}


function badges()
{
    $badges = get_tickets($_SESSION['accountId']);
    $lines = [];
    if (!empty($badges)) {
        foreach ($badges as $badge) {
            $name = $badge['BadgeName'];
            if ($name == null) {
                $name = "<unselected>";
            }
            if (array_key_exists($badge['EventName'], $lines)) {
                $lines[$badge['EventName']][] = [
                                            'Id' => $badge['AccountID'],
                                            'Badge Name' =>$name,
                                            'Emergency Contact' => $badge['EmergencyContact'],
                                            'Purchased' => $badge['RegistrationDate'],
                                            'Puchaser' => $badge['RegisteredByID']
                                                ];
            } else {
                $lines[$badge['EventName']] = [[
                                            'Id' => $badge['AccountID'],
                                            'Badge Name' => $name,
                                            'Emergency Contact' => $badge['EmergencyContact'],
                                            'Purchased' => $badge['RegistrationDate'],
                                            'Puchaser' => $badge['RegisteredByID']
                                               ]];
            }
        }
        ksort($lines);

        echo "<div class='w3-container w3-blue'>\n";
        echo "<table class='w3-table w3-striped w3-bordered w3-white'>\n";
        echo "<tr><th class='w3-center w3-blue' colspan=5'>Badges Purchased</th></tr>\n";
        foreach ($lines as $con => $names) {
            echo "<table class='w3-table w3-striped w3-bordered w3-white'>\n";
            echo "<tr><th class='w3-center w3-green' colspan=5'>".$con;
            echo " (".count($names)." badges)</th></tr>\n";
            $keys = array_keys($names[0]);
            echo "<tr><th>";
            echo implode(_htmlsafeify($keys), '</th><th>');
            echo "</th></tr>\n";
            foreach ($names as $badge) {
                echo "<tr><td>";
                echo implode(_htmlsafeify($badge), '</td><td> ');
                echo "</td></tr>\n";
            }
            echo "</table>\n";
        }
        echo "<tr class='w3-blue'><th colspan=5 class='w3-center'></th></tr>";
        echo "</table>\n";
        echo "</div>\n";
    }

}


$homepage_panes[] = 'registration\panes\badges';
