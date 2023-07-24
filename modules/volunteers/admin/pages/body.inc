<?php

/*.
    require_module 'standard';
    require_module 'json';
.*/

require_once __DIR__.'/../../functions/volunteer.inc';
require_once $FUNCTIONDIR.'/users.inc';
require_once $BASEDIR.'/console/console.inc';

console_body_data();

$totalHours = 0;
$prizeData = null;
$unclaimed_promo = [];
$hoursRemain = 0;
$groupTotals = [];

$uid = null;
$admin_mode = false;
if (!empty($_REQUEST)) {
    // Retrieve and sanitize POST data
    $arguments = [
    'volunteerId'         => FILTER_SANITIZE_SPECIAL_CHARS,
    ];
    $updateData = filter_input_array(INPUT_GET, $arguments);
    $uid = $updateData['volunteerId'];
}

if (isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS']) {
    if (isset($_COOKIE["CIAB_VOLUNTEERADMIN"])) {
        $admin_mode = $_COOKIE["CIAB_VOLUNTEERADMIN"];
    }
}

if ($admin_mode && in_console()) {
    if (array_key_exists('VOL_ADMINKIOSK', $GLOBALS)) {
        $admin_mode = ($GLOBALS['VOL_ADMINKIOSK'] == '1');
    } else {
        $admin_mode = false;
    }
}

if (!(isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS'])) {
    $locked = array();
    foreach ($_SESSION['customFields']['currConComPos'] as $entry) {
        $locked[] = $entry['departmentId'];
    }
    $admin_mode = false;
}


function _increment_offsets(&$groups, $start_entry)
{
    foreach ($groups as $key => $entry) {
        if ($entry['current'] > $start_entry['current']) {
            $entry['current'] = $entry['current'] + 1;
        }
        if ($entry['start'] > $start_entry['current']) {
            $entry['start'] = $entry['start'] + 1;
        }
        $groups[$key] = $entry;
    }

}


function _build_prize_list($data, $uid)
{
    $sorted = array();
    $soldout = array();
    $groups = array();
    foreach ($data as $record) {
        if ($record['Remaining'] <= 0) {
            if (isset($uid)) {
                if (isset($record['Aquired']) && $record['Aquired'] > 0) {
                    $record['Limit'] = 0;
                    array_push($soldout, $record);
                }
            } else {
                $record['Limit'] = 0;
                array_push($soldout, $record);
            }
        } else {
            if ($record['RewardGroupID']) {
                if (array_key_exists($record['RewardGroupID'], $groups)) {
                    $entry = $groups[$record['RewardGroupID']];
                    array_splice($sorted, $entry['current'], 0, [$record]);
                    _increment_offsets($groups, $entry);
                    array_push($entry['items'], $record);
                    if (array_key_exists('Aquired', $record)) {
                        $entry['aquired'] = $entry['aquired'] + $record['Aquired'];
                    }
                } else {
                    $entry = ['current' => sizeof($sorted),
                    'start' => sizeof($sorted),
                    'items' => array($record),
                    'aquired' => 0,
                    'limit' => $record['Limit']];
                    if (array_key_exists('Aquired', $record)) {
                        $entry['aquired'] = $record['Aquired'];
                    }
                    array_push($sorted, $record);
                }
                $entry['current'] = $entry['current'] + 1;
                $groups[$record['RewardGroupID']] = $entry;
            } else {
                array_push($sorted, $record);
            }
        }
    }

    if (isset($uid)) {
        foreach ($groups as $group) {
            if ($group['aquired'] >= $group['limit']) {
                for ($i = $group['start']; $i < $group['start'] + sizeof($group['items']); $i++) {
                    $sorted[$i]['group_soldout'] = true;
                }
            }
        }
    }
    return array_merge($sorted, $soldout);

}


function initialize($uid)
{
    global $prizeData, $groupTotals, $admin_mode;

    if (isset($uid)) {
        $prizeData = volunteer_prizes_for_user($uid);
    } else {
        $prizeData = volunteer_prizes();
    }

    $prizeData = _build_prize_list($prizeData, $uid);

    if ($admin_mode) {
        $groupTotals = volunteer_prize_groups();
    }

}


function hour_data($uid)
{
    global $totalHours, $ConComHours, $locked;

    if (isset($uid)) {
        if (!isset($locked) && class_exists('\\concom\\POSITION') && method_exists('\\concom\\POSITION', 'getConComPosition')) {
            $concom = concom\POSITION::getConComPosition($uid);
            if (!empty($concom)) {
                $totalHours = $ConComHours;
                return;
            }
        }

        $totalHours = 0;
        $data = get_volunteer_hours_for_user($uid);

        if (!empty($data)) {
            foreach ($data as $key => $record) {
                if (isset($locked) && !in_array($record['Department ID'], $locked)) {
                    continue;
                }
                if (!$concom) {
                    if (array_key_exists('Time Modifier', $record)) {
                        $totalHours += $record['Actual Hours'] * $record['Time Modifier'];
                    } else {
                        $totalHours += $record['Total Hours'];
                    }
                }
            }
        }
    }

}


echo "<script>\n";
echo "  var adminMode = ".json_encode($admin_mode).";\n";
echo "</script>\n";
?>

<div id="main_content">
  <div style="height:94vh;" :class="[isAdmin? 'UI-adminborder': '', 'VOL-main']">
    <div :class='[isAdmin ? "UI-admin-sectionbar" : "UI-event-sectionbar"]'>
        <span>Volunteer Administration</span>
    </div>

<div class="UI-rest UI-center">

<?php
if (isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS']) {
    if (!in_console() || (
            array_key_exists('VOL_ADMINKIOSK', $GLOBALS) &&
            $GLOBALS['VOL_ADMINKIOSK'])) {
        ?>
  <div>
    <div class="VOL-admin-switch">
      <div class='UI-table switch-table'>
        <div class='UI-table-row'>
          <div class='UI-table-cell'>
            Admin Mode
            <label class=switch><input type="checkbox" class=toggle id=admin_slider <?php
            if ($admin_mode) {
                echo "checked";
            }
            ?> onclick='toggleAdminMode();'>
              <div class=slider></div>
            </label>
          </div>
        </div>
      </div>
    </div>
  </div>
        <?php
    }

    generate_console_slider('volunteers');
}
?>

  <div id=user-lookup>
    <lookup-user message='Volunteer Badge Number, E-Mail or Full Name' url-tag='volunteerId'> </lookup-user>
  </div>
</div>

<div class="UI-rest UI-center">

<!------ Main Section ----->

<div class='VOL-row-padding'>
    <div id='info_div' class='UI-rest'>
<?php
if (!isset($locked)) {
    initialize($uid);
}

hour_data($uid);
?>

<volunteer-hour-table ref=vhtbl styles="max-height:34vh; overflow:scroll;" :footer=true @hour-change="handleHourChange"></volunteer-hour-table>

<div v-if="!isAdmin && userId != null && $refs.gfttbl && $refs.gfttbl.loaded && $refs.gfttbl.getUnclaimedCount()[0] > 0" class='VOL-messages-div' style='max-height:10vh; overflow:scroll;'>
 <div id="volunteer_messages" class="VOL-messages">
    <ul>
        <li v-for="u in $refs.gfttbl.getUnclaimed()">
            <span v-if="u.reward_group">Free Group Gift </span>
            <span v-else>Free Gift </span>
            <b>{{u.name}}</b>
            <span> (at {{printHours(u.value)}}) is available and has not been acquired!</span>
            <span v-if="u.reward_group"><b> Pick 1 below!</b></span>
        </li>
    </ul>
  </div>
  <div v-if="$refs.gfttbl.getUnclaimedCount()[1] > 0" id='aquire_promo' class='UI-continer UI-padding UI-cell-middle'>
      <button v-if="$refs.gfttbl.getUnclaimedCount()[1] > 1" id='promo_check' class='UI-redbutton' @click="$refs.chkout.addPromoToCheckout($refs.gfttbl.getUnclaimed())">Claim items</button>
      <button v-else id='promo_check' class='UI-redbutton' @click="$refs.chkout.addPromoToCheckout($refs.gfttbl.getUnclaimed())">Claim item</button>
   </div>
</div>

<div v-if="isAdmin" class='VOL-admin' style='max-height:10vh; overflow:scroll;'>
    <div id='volunteer_admin_bar' class='VOL-admin-bar'>
    <button id='add_prize' class='UI-redbutton' @click='$refs.edprz.show(null)'>Add New Gift</button>
    <button id='gen_csv' class='UI-redbutton' onclick='window.location.href=\"index.php?Function=volunteers/report\";'>Go To Reports</button>
    </div>
</div>

<gift-table ref=gfttbl styles="max-height:34vh; overflow:scroll;" :footer=true v-bind:total-hours="totalHours" v-bind:hours-spent="totalSpentHours"></gift-table>

    </div>

    <edit-prize ref="edprz"></edit-prize>
    <edit-hours ref="edhrs"></edit-hours>
    <process-return ref="psrtn"></process-return>
    <checkout ref="chkout"></checkout>

</div>
<script>
<?php
echo "  var groupData = '".json_encode($groupTotals)."';\n";
if (isset($uid)) {
    echo "  var userId = $uid;\n";
    echo "  var hoursRemain = parseFloat($hoursRemain);\n";
    echo "  var unclaimed = [\n";
    foreach ($unclaimed_promo as $item) {
        echo "    {";
        echo "PrizeID: ".$item['PrizeID'].",";
        echo "Json: '".base64_encode(json_encode($item))."'},\n";
    }
    echo "  ];\n";
} else {
    echo "  var userId = null;\n";
    echo "  var hoursRemain = 0;\n";
    echo "  var unclaimed = [];\n";
}
?>
</script>
