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


function registration()
{
    ?>
    &nbsp;
    <script type="module" src="/modules/registration/sitesupport/badgeMenuPane.js"></script>
    <div id="componentland">
        <badge-menu-pane :open="open" :event-name="eventName" :tickets="tickets">
        </badge-menu-pane>
    </div>
    <?php
    /* done */

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
        <script src="modules/registration/sitesupport/registration.js"></script>
        <div class='UI-container event-color-secondary'>

        <div class='UI-center event-color-secondary'>
            <strong style="margin-left: 100px;">Badges Purchased</strong>
            <div class='UI-right'>
                <input id='refresh' type='button' class='UI-orangebutton'
                       onclick='refreshBadgeData(<?php echo $_SESSION['accountId'];?>)' value='refresh'>
            </div>
        </div>

        <table class='UI-stripedtable'>
        <caption>Badges to the events</caption>
        <?php
        foreach ($lines as $con => $names) {
            $colspan = count($names[0]);
            echo "<table class='UI-stripedtable'>\n";
            echo "<tr><th class='UI-center event-color-primary' colspan='$colspan'>".$con;
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
        <tr class='event-color-secondary'><th id="placeholder" colspan=5 class='UI-center'></th></tr>
        </table>
        </div>
        <?php
    }

}


$homepage_panes[] = 'registration\panes\registration';
$homepage_panes[] = 'registration\panes\badges';
