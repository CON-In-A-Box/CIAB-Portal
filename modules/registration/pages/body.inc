<?php
/*.
    require_module 'standard';
.*/

require_once(__DIR__.'/../functions/events.inc');

$uid = null;

if (!empty($_REQUEST)) {
    // Retrieve and sanitize POST data
    $arguments = [
    'memberId'         => FILTER_SANITIZE_SPECIAL_CHARS,
    ];
    $updateData = filter_input_array(INPUT_GET, $arguments);
    $uid = $updateData['memberId'];
}


function _htmlsafeify($array)
{
    $output = [];
    foreach ($array as $key => $value) {
        $output[$key] = htmlspecialchars($value);
    }
    return $output;

}


function badges($account)
{
?>
    <div class='w3-container event-color-secondary'>
    <div class='w3-center event-color-secondary'>
        <b style="margin-left: 100px;">Badges Purchased</b>
        <div class='w3-right'>
            <input id='refresh' type='button' class='w3-button w3-orange'
                   onclick='refreshBadgeData(<?php echo $account;?>)' value='refresh'>
        </div>
    </div>

    <table class='w3-table w3-striped w3-bordered w3-white'>
<?php
$event = current_eventID();
$badges = get_tickets($account, $event);
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
            'Emergency Contact' => $badge['EmergencyContact'],
            'Purchased' => $badge['RegistrationDate'],
            'Purchaser' => $badge['RegisteredByID']
                                            ];
        } else {
            $lines[$badge['EventName']] = [[
            'Id' => $badge['AccountID'],
            'Badge Name' => $name,
            'Emergency Contact' => $badge['EmergencyContact'],
            'Purchased' => $badge['RegistrationDate'],
            'Purchaser' => $badge['RegisteredByID']
                                           ]];
        }
    }
    ksort($lines);

    foreach ($lines as $con => $names) {
        echo "<table class='w3-table w3-striped w3-bordered w3-white'>\n";
        echo "<tr><th class='w3-center event-color-primary' colspan=5'>".$con;
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
} else {
    echo "<tr><th class='w3-center w3-gray w3-opacity' colspan=9>No Entries...</th></tr>\n";
}
?>
        <tr class='event-color-secondary'><th colspan=5 class='w3-center'></th></tr>
        </table>
        </div>
<?php

}


?>

<div id="main_content" style="height:94vh;" class="w3-row-padding">
  <div class="UI-event-sectionbar">
    <span>Registration</span>
  </div>

<?php
if (isset($uid)) {
    echo "<div class='w3-rest w3-center' id='userLookup_div' data-user='$uid'> </div>\n";
    badges($uid);
} else {
    echo "<div class='w3-rest w3-center' id='userLookup_div'> </div>\n";
}


?>

</div>
