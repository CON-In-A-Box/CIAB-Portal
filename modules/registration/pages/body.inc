<?php
/*.
    require_module 'standard';
    require_module 'json';
.*/

require_once __DIR__.'/../functions/events.inc';
require_once $BASEDIR.'/kiosk/kiosk.inc';

/* Parts of his should only be accessable from registration staff */
$access = in_registration();

kiosk_body_data();

$uid = null;

if ($access && !empty($_REQUEST)) {
    // Retrieve and sanitize POST data
    $arguments = [
    'memberId'         => FILTER_SANITIZE_SPECIAL_CHARS,
    ];
    $updateData = filter_input_array(INPUT_GET, $arguments);
    $uid = $updateData['memberId'];
} else {
    $uid = $_SESSION['accountId'];
}


function _htmlsafeify($array)
{
    $output = [];
    foreach ($array as $key => $value) {
        $output[$key] = htmlspecialchars($value);
    }
    return $output;

}


function output_verification($account)
{
    $user = lookup_user_by_id($account);
    $info = get_verification_info($account);
    echo "<h3><b>Verify</b>:&nbsp; ";
    echo $info['Name'];
    echo "&nbsp;&nbsp;:&nbsp; ";
    echo $user["users"][0]["Email"];
    echo "&nbsp;&nbsp;:&nbsp; ";
    echo $info['AddressLine1']."&nbsp;&nbsp;:&nbsp; ";
    echo $info['Phone']."</h3>";

}


function badges($account)
{
    global $access;
?>
<div class='UI-container event-color-secondary'>
    <div class='UI-secondary-sectionbar'>
        <b style="margin-left: 100px;">Badges Purchased</b>
        <div class='UI-right'>
            <input id='refresh' type='button' class='UI-orangebutton'
                   onclick='refreshBadgeData(<?php echo $account;?>)' value='refresh'>
        </div>
    </div>

    <table class='UI-stripedtable'>
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
        $data = [
        'RegID' => $badge['RegistrationID'],
        'Id' => $badge['AccountID'],
        'Badge Name' => $name,
        'Badge Type' => $badge['Badge'],
        'Purchased' => $badge['RegistrationDate'],
        'Purchaser' => $badge['RegisteredByID'],
        'BackgroundImage' => $badge['BackgroundImage']
        ];
        if ($access) {
            $data['Print Count'] = $badge['BadgesPickedUp'];
        } else {
            if ($badge['BadgesPickedUp'] > 0) {
                $data['Picked Up'] = "Yes";
            } else {
                $data['Picked Up'] = "No";
            }
        }
        if (array_key_exists($badge['EventName'], $lines)) {
            $lines[$badge['EventName']][] = $data;
        } else {
            $lines[$badge['EventName']] = [$data];
        }
    }
    ksort($lines);

    foreach ($lines as $con => $names) {
        $len = count($names[0]);
        echo "<table class='UI-stripedtable'>\n";
        echo "<tr><th class='UI-center event-color-primary' colspan=$len'>".$con;
        echo " (".count($names)." badges)</th></tr>\n";
        $keys = array_keys($names[0]);
        unset($keys[0]);
        unset($keys[6]);
        foreach ($names as $badge) {
            echo "<tr><th>";
            echo implode(_htmlsafeify($keys), '</th><th>');
            echo "</th>";
            echo "</tr>\n";
            $regid = $badge['RegID'];
            $image = $badge['BackgroundImage'];
            unset($badge['RegID']);
            unset($badge['BackgroundImage']);
            echo "<tr><td>";
            echo implode(_htmlsafeify($badge), '</td><td> ');
            echo "</td>";
            $badge['RegID'] = $regid;
            $badge['BackgroundImage'] = $image;
            echo "</tr>\n";

            $sublen = $len - 4;
            echo "<tr><td></td><td colspan=$sublen>\n";
            output_verification($badge['Id']);
            echo "</td>\n";
            if ($access) {
                if (isset($_COOKIE["CIAB_REGISTRATIONWORKSTATION"])) {
                    $badge['Workstation'] = $_COOKIE["CIAB_REGISTRATIONWORKSTATION"];
                } else {
                    $badge['Workstation'] = $_SESSION['accountId'];
                }
                $param = json_encode($badge);
                $param = base64_encode($param);
                echo "<td><input id='update' type='button' class='UI-yellowbutton' onclick='showUpdateBadge(\"".$param."\");' value ='Update Badge Info'>&nbsp;";
                echo "<input id='refresh' type='button' onclick='printBadge(\"".$param."\");' ";
                if ($badge['Print Count']) {
                    echo "value ='Reprint Badge' class='UI-redbutton'>";
                } else {
                    echo "value ='Print Badge' class='UI-eventbutton'>";
                }
                echo "</td>";
            }
            echo "</tr>\n";
        }
        echo "</table>\n";
    }
} else {
    echo "<tr><th class='REG-no-entries' colspan=9>No Entries...</th></tr>\n";
}
?>
    <tr class='event-color-secondary'><th colspan=5 class='UI-center'></th></tr>
    </table>
</div>
<?php

}


function customer($account)
{
    global $access;
?>
<div class='UI-container'>
    <div class='UI-event-sectionbar'>
        Attendee Data
    </div>

<?php
$event = current_eventID();
if ($event !== null) {
    $name = current_eventName();
    $badges = get_tickets($account, $event);
    $lines = [];
    $user = lookup_user_by_id($account);
    if (!empty($badges)) {
        echo "<div class='UI-margin UI-center'>\n";
        echo "<h2>'$name' badges associated with ";
        echo "<span class='UI-red'>&nbsp;".$user["users"][0]["First Name"]." ";
        echo $user["users"][0]["Last Name"]."&nbsp;</span>";
        echo "</h2>";
        echo "</div>\n";
    } else {
        echo "<div class='UI-margin UI-center'>\n";
        echo "<h2>NO '$name' badges associated with ";
        echo "<span class='UI-red'>&nbsp;".$user["users"][0]["First Name"]." ";
        echo $user["users"][0]["Last Name"]."&nbsp;</span>";
        echo "</h2>";
        echo "</div>\n";
    }
}
?>
        <div class='UI-rest event-color-secondary'></div>
        </div>
<?php

}


?>
<script>
    userLookup.options({
        message: "Member Badge Number, or E-Mail or Name Partial Match",
        partialMatch: true,
        });
</script>

<div id="main_content" style="height:94vh;" class="REG-main-content">
  <div class="UI-event-sectionbar">
        <span>Registration
<?php
if (isset($_COOKIE["CIAB_REGISTRATIONWORKSTATION"]) &&
    !empty($_COOKIE["CIAB_REGISTRATIONWORKSTATION"])) {
    $id = $_COOKIE["CIAB_REGISTRATIONWORKSTATION"];
} else {
    $id = kiosk_user();
}
if (kiosk_user()) {
    echo " Workstation ".htmlspecialchars($id);
}
?>
    </span>
  </div>

<div class="UI-rest UI-center">
<?php
if ($access) {
    echo '<div id="kiosk_mode"';
    if (empty($id)) {
        echo ' class="UI-disabled"';
    }
    echo '>';
    generate_kiosk_slider("registration");
    echo '</div>';

?>
    <div class="REG-id-div">
        <label class='UI-label UI-half' for='workstation_id'>
        Workstation ID:</label>
        <input class="UI-input UI-half" id="workstation_id" placeholder="Required for Kiosk"
<?php
if (isset($_COOKIE["CIAB_REGISTRATIONWORKSTATION"])) {
    echo " value='".htmlspecialchars($_COOKIE['CIAB_REGISTRATIONWORKSTATION'])."'";
}
if (in_kiosk()) {
    echo " readonly";
}
?> onchange="workstationChange()">
    </div>
<?php

if (isset($uid)) {
    echo "<div class='UI-bar-item' id='userLookup_div' data-user='$uid'> </div>\n";
} else {
    echo "<div class='UI-bar-item' id='userLookup_div'> </div>\n";
}
}
echo "</div>";
if (isset($uid)) {
    customer($uid);
    badges($uid);
}
?>
</div>

<div class='UI-sidebar-hidden' id='update_badge_div'>
    <div class='UI-center'>
        <h2 class='UI-red'>Update Badge Data</h2>
    </div>
    <div class='UI-center'>
        <label class='UI-label' for='badge_name'>
        Badge Name:</label>
        <input class="UI-input" id="badge_name">
    </div>
    <div class='UI-center UI-padding'>
        <button id='update_badge' class='UI-eventbutton'
            onclick='updateBadge();'>
          Update
        </button>
        <button class='UI-redbutton'
            onclick='hideSidebar();'>
          Close
        </button>
    </div>
</div>
