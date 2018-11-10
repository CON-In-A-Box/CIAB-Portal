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
                'Badge Name' => $name,
                'Badge Type' => $badge['Badge'],
                'Purchased' => $badge['RegistrationDate'],
                'Purchaser' => $badge['RegisteredByID']
                                                ];
            } else {
                $lines[$badge['EventName']] = [[
                'Id' => $badge['AccountID'],
                'Badge Name' => $name,
                'Badge Type' => $badge['Badge'],
                'Purchased' => $badge['RegistrationDate'],
                'Purchaser' => $badge['RegisteredByID']
                                               ]];
            }
        }
        ksort($lines);
?>
        <script src="sitesupport/common.js"></script>
        <script src="modules/registration/sitesupport/registration.js"></script>
        <div class='w3-container event-color-secondary'>

        <div class='w3-center event-color-secondary'>
            <b style="margin-left: 100px;">Badges Purchased</b>
            <div class='w3-right'>
                <input id='refresh' type='button' class='w3-button w3-orange'
                       onclick='refreshBadgeData(<?php echo $_SESSION['accountId'];?>)' value='refresh'>
            </div>
        </div>

        <table class='w3-table w3-striped w3-bordered w3-white w3-responsive'>
<?php
foreach ($lines as $con => $names) {
    $colspan = count($names[0]);
    echo "<table class='w3-table w3-striped w3-bordered w3-white'>\n";
    echo "<tr><th class='w3-center event-color-primary' colspan='$colspan'>".$con;
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
?>
        <tr class='event-color-secondary'><th colspan=5 class='w3-center'></th></tr>
        </table>
        </div>
<?php
    }

}


$homepage_panes[] = 'registration\panes\badges';
