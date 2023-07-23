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


function _htmlsafeify($array)
{
    $output = [];
    foreach ($array as $key => $value) {
        $output[$key] = htmlspecialchars($value);
    }
    return $output;

}


function _printHours($hours)
{
    $min = floor(($hours - floor($hours)) * 60);
    echo number_format(floor($hours), 0)." Hours ".$min." Minutes ";

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


function acquireable_item($item)
{
    global $totalHours, $hoursRemain;
    if ($item['Promo'] == 'yes') {
        return (round($item['Value'], 3) <= round($totalHours, 3));
    }
    return (round($item['Value'], 3) <= round($hoursRemain, 3));

}


function messages($uid)
{
    global $prizeData, $totalHours, $unclaimed_promo;

    $unclaimed_promo = [];
    $display = false;
    if (isset($uid)) {
        $promo = "<ul>\n";
        foreach ($prizeData as $key => $record) {
            $record['Value'] += 0;
            if ($record['Promo'] == 'yes' &&
                $record['Remaining'] > 0 &&
                $totalHours >= $record['Value'] &&
                !isset($record['Aquired'])) {
                if ($record['RewardGroupID'] !== null) {
                    $promo .= "<li>Free Group Gift <b>".htmlspecialchars($record['Name'])."</b> (at ".$record['Value']." hours) is available and has not been acquired! <b>Pick 1 below!</b></li>\n";
                } else {
                    $promo .= "<li>Free Gift <b>".htmlspecialchars($record['Name'])."</b> (at ".$record['Value']." hours) is available and has not been acquired!</li>\n";
                    $unclaimed_promo[] = $record;
                }
                $display = true;
            }
        }
        $promo .= "</ul>\n";

        if ($display) {
            echo "<div class='VOL-messages-div' ";
            echo "style='max-height:10vh; overflow:scroll;'>\n";
            echo "<div id='volunteer_messages' class='VOL-messages'>\n";
            echo $promo;
            echo "</div>\n";
            if ($unclaimed_promo && sizeof($unclaimed_promo)) {
                echo "<div id='aquire_promo' class='UI-continer UI-padding UI-cell-middle'>\n";
                echo "<button id='promo_check' class='UI-redbutton'";
                $names = array_column($unclaimed_promo, 'Name');
                echo "onclick=addPromoToCheckout();";
                echo ">";
                if ($unclaimed_promo > 1) {
                    echo "Claim items</button>\n";
                } else {
                    echo "Claim item</button>\n";
                }
                echo "</div>\n";
            }
            echo "</div>\n";
        } else {
            echo "<hr>\n";
        }
    } else {
        echo "<hr>\n";
    }

}


function admin_bar($uid)
{
    echo "<div class='VOL-admin' ";
    echo "style='max-height:10vh; overflow:scroll;'>\n";
    echo "<div id='volunteer_admin_bar' class='VOL-admin-bar'>\n";
    echo "<button id='add_prize' class='UI-redbutton'";
    echo "@click='\$refs.edprz.show(null)'>Add New Gift</button>\n";
    echo "<button id='gen_csv' class='UI-redbutton'";
    echo "onclick='window.location.href=\"index.php?Function=volunteers/report\";'>Go To Reports</button>\n";
    echo "</div>\n";
    echo "</div>\n";

}


echo '<div id="main_content" style="height:94vh;" class="VOL-main';
if ($admin_mode) {
    echo ' UI-adminborder">';
    echo "\n";
    echo "<div class='UI-admin-sectionbar'>\n";
    echo "<span>Volunteer Administration</span>\n";
} else {
    echo '">';
    echo "\n";
    echo "<div class='UI-event-sectionbar'>\n";
    echo "<span>Volunteer Management</span>\n";
}
echo "</div>\n"
?>

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

<?php
if (!isset($locked)) {
    if (!$admin_mode) {
        messages($uid);
    } else {
        admin_bar($uid);
    }
}
?>

<gift-table styles="max-height:34vh; overflow:scroll;" :footer=true v-bind:total-hours="totalHours" v-bind:hours-spent="totalSpentHours"></gift-table>

    </div>

    <div class='UI-sidebar-hidden' id='department_report_div'>
        <div class='UI-center'>
            <h2 class='UI-red'>Department Report</h2>
        </div>
        <hr>
        <div>
            <h2 id='dept_name' class='event-color-secondary'></h2>
            <input class="UI-hiddeninput" id="dept_data" readonly>
            <input class="UI-hiddeninput" id="dept_data_name" readonly>
        </div>
        <hr>
        <div class='UI-center'>
            <button id='generate_dept_report' class='UI-eventbutton'
                onclick='generateDeptReport();'>
               Generate Report &lt;CSV&gt;
            </button>
            <button class='UI-redbutton'
                onclick='hideSidebar();'>
              Close
            </button>
        </div>
    </div>

    <edit-prize ref="edprz"></edit-prize>
    <edit-hours ref="edhrs"></edit-hours>
    <process-return ref="psrtn"></process-return>

    <div class='UI-sidebar-hidden' id='checkout_div'>
        <div class='UI-center'>
            <h2>Items claimed now</h2>
        </div>
        <table class='UI-stripedtable' id='checkout_table'>
        </table>
        <div class="VOL-claim-hour-div">
            <span>Hours Left: <span id="hours_left">0</span></span>
            <br>
            <span>Hours Spent: <span id="hours_used">0</span></span>
        </div>
        <div class='UI-center'>
            <button id='checkout_button' class='UI-eventbutton'
                onclick='processCheckout();'>
                Distribute Items
            </button>
            <button id='clear_button' class='UI-redbutton'
                onclick='clearCheckout();'>
               Clear
            </button>
        </div>
    </div>
  </div>
</div>

<div id="success_dlg" class="UI-modal">
  <div class="UI-modal-content">
    <div class="UI-container">
      <span onclick="document.getElementById('success_dlg').style.display='none';
        location.reload();"
      class="VOL-give-close">&times;</span>
      <h2 class="UI-center event-color-primary">Please get the volunteer the following new gifts!</h2>
      <hr>
      <table class='VOL-give-gift-table' id='reward_list'>
      </table>
      <h2 class="UI-center UI-red">Close this window when all gifts have been claimed!</h2>
    </div>
  </div>
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
echo "  var adminMode = ".json_encode($admin_mode).";\n";
?>
</script>
