<?php

/*.
    require_module 'standard';
.*/

require_once $FUNCTIONDIR.'/volunteer.inc';
require_once $FUNCTIONDIR.'/users.inc';

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

if ($admin_mode && isset($_SESSION['kioskMode'])) {
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


function hour_table($uid)
{
    global $totalHours,$admin_mode,$ConComHours,$locked;

    echo '<div class="w3-container w3-center event-color-primary" ';
    echo 'style="max-height:34vh; overflow:scroll;">';
    echo "<table class='w3-table w3-striped w3-bordered w3-white'>";
    echo "<tr><th class='w3-center event-color-primary' colspan=9>Hours</th></tr>\n";
    $concom = null;
    if (isset($uid)) {
        $data = get_volunteer_hours_for_user($uid);

        if (!isset($locked) && class_exists('\\concom\\POSITION') && method_exists('\\concom\\POSITION', 'getConComPosition')) {
            $concom = concom\POSITION::getConComPosition($uid);
        }

        if ($concom) {
            $totalHours = $ConComHours;
        } else {
            $totalHours = 0;
        }
    } else {
        $data = get_volunteer_hour_summary();
        $summary = get_volunteer_year_summery();
    }
    if (!empty($data)) {
        echo "<tr><th>";
        $keys = array_keys($data[0]);
        $id_index = array_search("Department ID", $keys);
        if ($id_index !== false) {
            unset($keys[$id_index]);
        }
        $idx = array_search('EntryID', $keys);
        if ($idx !== false) {
            unset($keys[$idx]);
        }
        echo implode(_htmlsafeify($keys), '</th><th>');
        echo "</th></tr>\n";
        foreach ($data as $key => $record) {
            if (isset($locked) && !in_array($record['Department ID'], $locked)) {
                continue;
            }
            if (isset($uid)) {
                if (!$concom) {
                    if (array_key_exists('Time Modifier', $record)) {
                        $totalHours += $record['Actual Hours'] * $record['Time Modifier'];
                    } else {
                        $totalHours += $record['Total Hours'];
                    }
                }
            }
            if ($uid && $admin_mode) {
                echo "<tr class='w3-hover-red' ";
                $json = json_encode($record);
                $json = base64_encode($json);
                echo 'onclick=\'showEditHours("'.$json;
                echo '");\'>';
            } elseif (!isset($uid) && $admin_mode) {
                echo "<tr class='w3-hover-red' ";
                echo 'onclick=\'departmentReport("'.$record['Department Worked'].'", '.$record['Department ID'].');\'';
                echo ">";
            } else {
                echo "<tr>";
            }
            echo "<td>";
            unset($record['EntryID']);
            if ($id_index !== false) {
                unset($record['Department ID']);
            }
            if (!isset($uid)) {
                $record['Total Hours'] += 0;
            } else {
                $record['Actual Hours'] += 0;
                $record['Time Modifier'] += 0;
            }
            echo implode(_htmlsafeify($record), '</td><td>');
            echo "</td></tr>\n";
        }
    } else {
        echo "<tr><th class='w3-center w3-gray w3-opacity' colspan=9>No Entries...</th></tr>\n";
    }
    echo "<tr><th class='w3-center event-color-primary' colspan=9></th></tr>\n";
    echo "</table>";
    echo "</div>";
    echo '<div class="w3-col w3-center w3-xlarge event-color-primary">';
    if (isset($uid)) {
        echo "<span>Total : ";
        _printHours($totalHours);
        echo "</span>";
    } elseif (!isset($locked)) {
        echo "<span class='w3-margin'>Total Hours : ";
        _printHours($summary['TotalHours']);
        echo "</span>";
        echo "<span class='w3-margin'>Total Volunteers : ".$summary['VolunteerCount']."</span>";
        echo "<span class='we-margin'>Total Spent Hours : ";
        _printHours($summary['TotalHoursSpent']);
        echo "</span>";
    }
    echo "</div>\n";

};


function acquireable_item($item)
{
    global $totalHours, $hoursRemain;
    if ($item['Promo'] == 'yes') {
        return (round($item['Value'], 3) <= round($totalHours, 3));
    }
    return (round($item['Value'], 3) <= round($hoursRemain, 3));

}


function _prize_table_start($uid = 0, $spent = 0, $hoursRemain = 0)
{
    global $admin_mode;

    if ($uid) {
        echo '<div class="w3-col w3-center w3-xlarge event-color-secondary">';
        echo "<span>Spent <span id='h_spent'>";
        _printHours($spent);
        echo "</span>  |  ";
        echo "<span id='h_left'>";
        _printHours($hoursRemain);
        echo "</span>  Left</span>";
        echo "</div>\n";
    }
    echo "<div class='w3-container w3-center event-color-secondary' ";
    if ($admin_mode && $uid) {
        echo "style='max-height:29vh; overflow:scroll;'>\n";
    } else {
        echo "style='max-height:33vh; overflow:scroll;'>\n";
    }
    echo "<table class='w3-table w3-striped w3-bordered w3-white'";
    echo " id='prizes'>\n";
    echo "<tr>";
    echo "<th class='event-color-secondary'>";
    if (!$admin_mode) {
        echo "<input id='soldoutcheck' type='checkbox' class='w3-check' ";
        echo "onclick='showHideSoldOut()'>";
        echo "<label for='soldoutcheck'>";
        if (isset($uid)) {
            echo "Show volunteer's soldout items";
        } else {
            echo "Show soldout items";
        }
        echo "</label></th>\n";
    }
    echo "<th class='w3-center event-color-secondary' colspan=3>Gifts</th>";
    echo "<th class='event-color-secondary' colspan=2></th>\n";
    echo "<th class='event-color-secondary'>";
    if (!$admin_mode && isset($uid)) {
        $data = volunteer_prizes_for_user($uid);
        $record = [];
        foreach ($data as $key => $entry) {
            if (!isset($entry['Aquired']) || $entry['Aquired'] == 0) {
                continue;
            }
            $value = 0;
            if ($entry['Promo'] != 'yes') {
                $value = $entry['Value'];
            }
            $record[] = [ 'PrizeID' => $entry['PrizeID'],
            'Name' => $entry['Name'],
            'Aquired' => $entry['Aquired'],
            'Value' => $value,
                        ];
        }
        $json = json_encode($record);
        $json = base64_encode($json);
        echo "<input id='return_items' type='button' class='w3-button w3-orange' ";
        echo "onclick='showReturn(\"".$json."\")' value='Return Items'>";
    }
    echo "</th>\n";
    echo "</tr>\n";

}


function prizes_table($uid)
{
    global $totalHours, $prizeData, $hoursRemain, $groupTotals;
    global $admin_mode;

    $data = $prizeData;

    $group_styles  = [
    'w3-amber', 'w3-aqua', 'w3-brown', 'w3-cyan',
    'w3-indigo', 'w3-khaki', 'w3-lime', 'w3-orange',
    'w3-pink', 'w3-purple', 'w3-red', 'w3-sand', 'w3-teal', 'w3-yellow',
    'w3-deep-purple', 'w3-deep-orange', 'w3-light-blue',
    'event-color-secondary-grey', 'w3-light-green'];

    $last_group = null;
    $group = 0;
    $spent = 0;
    if (!empty($data)) {
        reset($data);
        $first_key = key($data);
        $keys = array_keys($data[$first_key]);
        $idx = array_search('PrizeID', $keys);
        unset($keys[$idx]);
        if (!$admin_mode || isset($uid)) {
            $idx = array_search('RewardGroupID', $keys);
            unset($keys[$idx]);
        }
        if (isset($uid) && !array_search('Aquired', $keys)) {
            array_push($keys, 'Aquired');
        }
        $idx = array_search('group_soldout', $keys);
        unset($keys[$idx]);
        foreach ($data as $key => $record) {
            if (isset($uid)) {
                if (isset($record['Aquired'])) {
                    if ($record['Promo'] != 'yes') {
                        $spent += $record['Aquired'] * $record['Value'];
                    }
                } else {
                    $data[$key]['Aquired'] = ' ';
                }
            }
        }
        $hoursRemain = round($totalHours - $spent, 3);

        _prize_table_start($uid, $spent, $hoursRemain);

        echo "<tr><th>";
        echo implode(_htmlsafeify($keys), '</th><th>');
        echo "</th></tr>\n";
        foreach ($data as $key => $record) {
            if (!isset($uid) && $admin_mode) {
                echo "<tr ";
                echo "class='w3-hover-red' ";
                $json = json_encode($record);
                $json = base64_encode($json);
                echo 'onclick=\'showEditPrize("'.$json.'");\'';
                echo ">";
            } else {
                if ($record['Remaining'] <= 0) {
                    echo "<tr class='soldoutrow hiddenrow w3-red'>";
                } elseif ($record['Promo'] == 'yes' && array_key_exists('Aquired', $record) && $record['Aquired'] > 0) {
                    echo "<tr class='soldoutrow hiddenrow w3-disabled'>";
                } elseif ($record['RewardGroupID']) {
                    $record['GroupLimit'] = $record['Limit'];
                    if ($last_group !== null && $last_group != $record['RewardGroupID']) {
                        $group++;
                    } elseif ($last_group !== null) {
                        $record['Limit'] = '|';
                    }
                    if (array_key_exists('Aquired', $record) && ctype_digit($record['Aquired'])) {
                        $index = intval($record['RewardGroupID']);
                        $groupTotals = array_pad($groupTotals, $index + 1, 0);
                        $groupTotals[$index] += intval($record['Aquired']);
                    } else {
                        $index = intval($record['RewardGroupID']);
                        $groupTotals = array_pad($groupTotals, $index + 1, 0);
                        $groupTotals[$index] += 0;
                    }
                    $last_group = $record['RewardGroupID'];
                    $classes = $group_styles[($group % count($group_styles))];

                    if (isset($record['group_soldout'])) {
                        echo "<tr class='$classes soldoutrow hiddenrow w3-disabled'>";
                    } else {
                        echo "<tr class='$classes";
                        if (!$admin_mode && isset($uid) && acquireable_item($record)) {
                            echo " event-hover-primary' ";
                            $json = json_encode($record);
                            $json = base64_encode($json);
                            echo 'onclick=\'addToCheckout("'.$json;
                            echo '");\'>';
                        } else {
                            echo "'>";
                        }
                    }
                } else {
                    echo "<tr ";
                    if (!$admin_mode && isset($uid) && acquireable_item($record)) {
                        echo "class='event-hover-primary' ";
                        $json = json_encode($record);
                        $json = base64_encode($json);
                        echo 'onclick=\'addToCheckout("'.$json.'");\'';
                    }
                    echo ">";
                }
                unset($record['RewardGroupID']);
            }
            unset($record['PrizeID']);
            unset($record['group_soldout']);
            unset($record['GroupLimit']);
            echo "<td>";
            $record['Value'] += 0;
            echo implode(_htmlsafeify($record), '</td><td>');
            echo "</td></tr>\n";
        }
    } else {
        _prize_table_start();
        echo "<tr><th class='w3-center w3-gray w3-opacity' colspan=9>No Entries...</th></tr>\n";
    }
    echo "<tr><th class='w3-center event-color-secondary' colspan=8></th></tr>\n";
    echo "</table>\n";
    echo '</div>';

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
                    if ($record['group_soldout']) {
                        continue;
                    }
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
            echo "<div class='w3-col w3-cell-row w3-padding' ";
            echo "style='max-height:10vh; overflow:scroll;'>\n";
            echo "<div id='volunteer_messages' class='w3-container w3-padding w3-border-red w3-border w3-margin w3-cell'>\n";
            echo $promo;
            echo "</div>\n";
            if ($unclaimed_promo && sizeof($unclaimed_promo)) {
                echo "<div id='aquire_promo' class='w3-continer w3-padding w3-cell w3-cell-middle'>\n";
                echo "<button id='promo_check' class='w3-button w3-round-xxlarge w3-red'";
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
    echo "<div class='w3-col w3-cell-row w3-padding' ";
    echo "style='max-height:10vh; overflow:scroll;'>\n";
    echo "<div id='volunteer_admin_bar' class='w3-container w3-padding w3-border-red w3-border w3-margin w3-cell'>\n";
    echo "<button id='add_prize' class='w3-button w3-round-xxlarge w3-red'";
    echo "onclick='showEditPrize(null)';>Add New Gift</button>\n";
    echo "<button id='min_hours' class='w3-button w3-round-xxlarge w3-red'";
    echo "onclick='minHourReport()';>Minimum Hour Report</button>\n";
    echo "<button id='gen_der_csv' class='w3-button w3-round-xxlarge w3-red'";
    echo "onclick='generateDerivedCSV()';>Export Derived CSV</button>\n";
    echo "<button id='gen_csv' class='w3-button w3-round-xxlarge w3-red'";
    echo "onclick='generateCSV()';>Export Raw CSV Table</button>\n";
    echo "</div>\n";
    echo "</div>\n";

}


echo '<div id="main_content" style="height:94vh;" class="w3-row-padding';
if ($admin_mode) {
    echo ' w3-topbar w3-bottombar w3-leftbar w3-rightbar w3-border-red">';
    echo "\n";
    echo "<div class='w3-rest w3-center w3-xlarge w3-red'>\n";
    echo "<span>Volunteer Administration</span>\n";
} else {
    echo '">';
    echo "\n";
    echo "<div class='w3-rest w3-center w3-xlarge event-color-primary'>\n";
    echo "<span>Volunteer Management</span>\n";
}
echo "</div>\n"
?>

<div class="w3-rest w3-center">
<?php
if (isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS']) {
    if (!isset($_SESSION['kioskMode']) || (
            array_key_exists('VOL_ADMINKIOSK', $GLOBALS) &&
            $GLOBALS['VOL_ADMINKIOSK'])) {
?>
  <div>
    <div class="w3-show-inline-block w3-right w3-red w3-margin">
      <table class='switch-table'><tr><td>
        Admin Mode
        <label class=switch><input type="checkbox" class=toggle id=admin_slider <?php
        if ($admin_mode) {
            echo "checked";
        }
?> onclick='toggleAdminMode();'>
          <div class=slider></div></label></td></tr>
      </table>
    </div>
  </div>
<?php
    }
?>

  <div>
    <div class="w3-show-inline-block w3-left event-color-secondary w3-margin">
      <table class='switch-table'><tr><td>
        Kiosk Mode
        <label class=switch><input type="checkbox" class=toggle id=kiosk_slider <?php
        if (array_key_exists('kioskMode', $_SESSION) && $_SESSION['kioskMode']) {
            echo "checked";
        }
?> onclick='toggleKioskMode();'>
          <div class=slider></div></label></td></tr>
      </table>
    </div>
  </div>
<?php
}
?>

<script>
    userLookup.options({
        message: 'Volunteer Badge Number, E-Mail or Full Name',
        urlTag: 'volunteerId',
        fail: lookupFail,
        });
</script>

<?php
if (isset($uid)) {
    echo "<div class='w3-bar-item' id='userLookup_div' data-user='$uid'> </div>\n";
} else {
    echo "<div class='w3-bar-item' id='userLookup_div'> </div>\n";
}
?>

</div>

<div class="w3-rest w3-center">

<!------ Main Section ----->

<div class='w3-row-padding'>
    <div id='info_div' class='w3-rest'>
<?php
if (!isset($locked)) {
    initialize($uid);
}
hour_table($uid);
if (!isset($locked)) {
    if (!$admin_mode) {
        messages($uid);
    } else {
        admin_bar($uid);
    }
    prizes_table($uid);
}
?>
    </div>

    <div class='w3-hide w3-padding' id='department_report_div'>
        <div class='w3-center'>
            <h2 class='w3-red'>Department Report</h2>
        </div>
        <hr>
        <div>
            <h2 id='dept_name' class='event-color-secondary'></h2>
            <input class="w3-input w3-disabled w3-hide" id="dept_data" readonly>
            <input class="w3-input w3-disabled w3-hide" id="dept_data_name" readonly>
        </div>
        <hr>
        <div class='w3-center'>
            <button id='generate_dept_report' class='w3-button w3-round-xxlarge event-color-primary'
                onclick='generateDeptReport();'>
               Generate Report &lt;CSV&gt;
            </button>
            <button class='w3-button w3-round-xxlarge w3-red'
                onclick='hideSidebar();'>
              Close
            </button>
        </div>
    </div>

    <div class='w3-hide w3-padding' id='csv_export_div'>
        <div class='w3-center'>
            <h2 class='w3-red'>Export Raw CSV Data</h2>
        </div>
        <div class='w3-center'>
            <label for='csv_table'>
            Table:</label>
            <select class="w3-margin w3-select w3-border" style="width:auto" id="csv_table">
              <option value=HourRedemptions>HourRedemptions</option>
              <option value=VolunteerHours selected>VolunteerHours</option>
              <option value=VolunteerRewards>VolunteerRewards</option>
              <option value=RewardGroup>RewardGroup</option>
            </select>
        </div>
        <div class='w3-center'>
            <button id='generate_csv' class='w3-button w3-round-xxlarge event-color-primary'
                onclick='generatCSVReport();'>
               Generate CSV
            </button>
            <button class='w3-button w3-round-xxlarge w3-red'
                onclick='hideSidebar();'>
              Close
            </button>
        </div>
    </div>

    <div class='w3-hide w3-padding' id='csv_export_derived_div'>
        <div class='w3-center'>
            <h2 class='w3-red'>Export Derived CSV Data</h2>
        </div>
        <div class='w3-center'>
            <label for='der_csv_table'>
            Table:</label>
            <select class="w3-margin w3-select w3-border" style="width:auto" id="der_csv_table">
              <option value="SELECT r.*, (SELECT COUNT(h.PrizeID) FROM HourRedemptions AS h WHERE h.PrizeID = r.PrizeID GROUP BY h.PrizeID) as Claimed FROM VolunteerRewards as r;">Volunteer Rewards</option>
              <option value="SELECT v.*, (SELECT Name FROM `Departments` as d WHERE v.DepartmentID = d.DepartmentID) as DepartmentName FROM `VolunteerHours` as v;">Volunteer Hours</option>
              <option value="SELECT *, (SELECT Name FROM VolunteerRewards WHERE VolunteerRewards.prizeid = HourRedemptions.PrizeID) AS PrizeName FROM `HourRedemptions` ORDER BY AccountID">Prize Redemption</option>
            </select>
        </div>
        <div class='w3-center'>
            <button id='generate_csv' class='w3-button w3-round-xxlarge event-color-primary'
                onclick='generateDerivedCSVReport();'>
               Generate CSV
            </button>
            <button class='w3-button w3-round-xxlarge w3-red'
                onclick='hideSidebar();'>
              Close
            </button>
        </div>
    </div>

    <div class='w3-hide w3-padding' id='hour_report_div'>
        <div class='w3-center'>
            <h2 class='w3-red'>Report of all Volunteers with minimum hours or more</h2>
        </div>
        <div class='w3-center'>
            <label for='report_hour_min'>
            Minimum hours:</label>
            <input class="w3-input w3-border" id="report_hour_min">
        </div>
        <div class='w3-center'>
            <button id='generate_report' class='w3-button w3-round-xxlarge event-color-primary'
                onclick='generateHourReport();'>
               Generate Report &lt;CSV&gt;
            </button>
            <button class='w3-button w3-round-xxlarge w3-red'
                onclick='hideSidebar();'>
              Close
            </button>
        </div>
    </div>

    <div class='w3-hide w3-padding' id='edit_prize_div'>
        <div class='w3-center w3-red'>
            <h2 id='edit_prize_title'>Edit Gift Entry</h2>
        </div>
        <div class='w3-border w3-padding'>
          <form>

            <label for='edit_prize_name' class="w3-tooltip">
                <span
                  style="position:absolute;left:-80px;right:-80px;bottom:18px"
                  class="w3-text w3-tag event-color-primary w3-round-xlarge">Visible name of the item</span>
            Name:</label>
            <input class="w3-input w3-border" id="edit_prize_name" placeholder="<name>">
            <label for='edit_prize_value' class="w3-tooltip">
                <span
                  style="position:absolute;left:-80px;right:-80px;bottom:18px"
                  class="w3-text w3-tag event-color-primary w3-round-xlarge">Hour cost of the item</span>
            Value:</label>
            <input class="w3-input w3-border" id="edit_prize_value">
            <label for='edit_prize_value' class="w3-tooltip">
                <span
                  style="position:absolute;left:-80px;right:-80px;bottom:18px"
                  class="w3-text w3-tag event-color-primary w3-round-xlarge">If 'yes' this item does not cost hours but instead is free at the given hour mark. Also enforces a limit of 1.</span>
            Promo Item:</label>
            <select class="w3-select w3-border" id="edit_prize_promo">
              <option value='yes'>Yes</option>
              <option value='no'>No</option>
            </select>

            <label for='edit_prize_count' class="w3-tooltip">
                <span
                  style="position:absolute;left:-80px;right:-80px;bottom:18px"
                  class="w3-text w3-tag event-color-primary w3-round-xlarge">Changing this will adjust Total items</span>
            Inventory Remaining:</label>
            <input class="w3-input w3-border" id="edit_prize_count">

                    <label for='edit_prize_group' class="w3-tooltip">
                        <span
                          style="position:absolute;left:-80px;right:-80px;bottom:18px"
                          class="w3-text w3-tag event-color-primary w3-round-xlarge">If in a group there can be only a limited total number of the items in the group claimed by the volunteer</span>
                    Gift Group:</label>
            <div class="w3-row">
                <div class='w3-col w3-threequarter w3-padding'>
                    <select class="w3-select w3-border" id="edit_prize_group"
                     onchange="prizeGroupChange()">
                      <option value='none'>&lt;none&gt;</option>
                    </select>
                </div>
                <div class='w3-col w3-quarter w3-padding'>
                    <input class="w3-input w3-border w3-disabled" id="edit_prize_group_count" disabled placeholder="max">
                </div>
            </div>

            <input class="w3-input w3-border w3-disabled w3-hide" id="prize_data" readonly>
          </form>
        </div>
        <div class='w3-center'>
            <button id='set_prize_button' class='w3-button w3-round-xxlarge event-color-primary'
                onclick='commitPrize();'>
                Commit
            </button>
            <button id='delete_prize_button' class='w3-button w3-round-xxlarge event-color-secondary'
                onclick='deletePrize();'>
                Delete
            </button>
            <button id='exit_prize_button' class='w3-button w3-round-xxlarge w3-red'
                onclick='hideSidebar();'>
               Cancel
            </button>
        </div>
    </div>


    <div class='w3-hide w3-padding' id='edit_user_hour_div'>
        <div class='w3-center w3-red'>
            <h2>Edit Hours Entry</h2>
        </div>
        <div class='w3-border w3-padding'>
          <form>
            <label for='edit_name'>Volunteer:</label>
            <input class="w3-input w3-border w3-disabled" id="edit_name" readonly>
            <label for='edit_hours'>Actual Hours:</label>
            <input class="w3-input w3-border" id="edit_hours">
            <label for='edit_mod'>Time Modifier</label><br />
            <select class="w3-select w3-border" style="width:auto" name="TimeModifier" id="edit_mod">
              <option value=0.5>Half Time - 1 hour = 0.5 hours credit</option>
              <option value=1 selected>Normal - 1 hour = 1 hour</option>
              <option value=1.5>Time Plus Half - 1 hour = 1.5 hours credit</option>
              <option value=2>Double - 1 hour = 2 hours credit</option>
            </select>
            <label for='edit_end'>End Time:</label>
            <input class="w3-input w3-border" type="datetime-local" id="edit_end" pattern="\d{4}-\d{2}-\d{2}T\d{2}:\d{2}">
            <label for='edit_dept'>Department</label><br />
            <select class="w3-select w3-border" style="width:auto" name="DepartmentWorked" id="edit_dept">
<?php
require_once($FUNCTIONDIR."/divisional.inc");
foreach ($Departments as $dep => $set) {
    echo '<option value="'.$dep.'"';
    echo '>'.htmlspecialchars($dep)."</option>\n";
}
?>
            </select>
            <label for='edit_enter'>Entered By:</label>
            <input class="w3-input w3-border w3-disabled" id="edit_enter" readonly>
            <label for='edit_auth'>Authorized By:</label>
            <input class="w3-input w3-border" id="edit_auth">
            <input class="w3-input w3-border w3-disabled w3-hide" id="edit_data" readonly>
          </form>
        </div>
        <div class='w3-center'>
            <button id='set_hours_button' class='w3-button w3-round-xxlarge event-color-primary'
                onclick='commitHours();'>
                Commit
            </button>
            <button id='delete_hours_button' class='w3-button w3-round-xxlarge event-color-secondary'
                onclick='deleteHours();'>
                Delete
            </button>
            <button id='exit_hours_button' class='w3-button w3-round-xxlarge w3-red'
                onclick='hideSidebar();'>
               Cancel
            </button>
        </div>
    </div>

    <div class='w3-hide w3-padding' id='return_div'>
        <div class='w3-center'>
            <h2>Items being returned now:</h2>
        </div>
        <table class='w3-table w3-striped w3-border w3-white' id='return_table'>
        </table>
        <div class="w3-container w3-center w3-large event-color-primary w3-margin">
            <span>Hours To Be Credited: <span id="credit_hours">0</span></span>
        </div>
        <div class='w3-center'>
            <button id='return_button' class='w3-button w3-round-xxlarge event-color-primary'
                onclick='processReturn();'>
                Return Items
            </button>
            <button id='return_clear_button' class='w3-button w3-round-xxlarge w3-red'
                onclick='hideSidebar();'>
               Cancel
            </button>
        </div>

        <div id="return_success_dlg" class="w3-modal">
          <div class="w3-modal-content">
            <div class="w3-container">
              <span onclick="document.getElementById('return_success_dlg').style.display='none';
                location.reload();"
              class="w3-button w3-display-topright">&times;</span>
              <h2 class="w3-center event-color-primary">Please collect and return the following gifts</h2>
              <hr>
              <table class='w3-center w3-border w3-table w3-striped w3-white w3-xlarge w3-padding' id='return_list'>
              </table>
              <h2 class="w3-center w3-red">Close this window when all returns have been processed!</h2>
            </div>
          </div>
        </div>
    </div>

    <div class='w3-hide w3-padding' id='checkout_div'>
        <div class='w3-center'>
            <h2>Items claimed now</h2>
        </div>
        <table class='w3-table w3-striped w3-border w3-white' id='checkout_table'>
        </table>
        <div class="w3-container w3-center w3-large event-color-primary w3-margin">
            <span>Hours Left: <span id="hours_left">0</span></span>
            <br>
            <span>Hours Spent: <span id="hours_used">0</span></span>
        </div>
        <div class='w3-center'>
            <button id='checkout_button' class='w3-button w3-round-xxlarge event-color-primary'
                onclick='processCheckout();'>
                Distribute Items
            </button>
            <button id='clear_button' class='w3-button w3-round-xxlarge w3-red'
                onclick='clearCheckout();'>
               Clear
            </button>
        <div>
    </div>
</div>

<div id="success_dlg" class="w3-modal">
  <div class="w3-modal-content">
    <div class="w3-container">
      <span onclick="document.getElementById('success_dlg').style.display='none';
        location.reload();"
      class="w3-button w3-display-topright">&times;</span>
      <h2 class="w3-center event-color-primary">Please get the volunteer the following new gifts!</h2>
      <hr>
      <table class='w3-center w3-border w3-table w3-striped w3-white w3-xlarge w3-padding' id='reward_list'>
      </table>
      <h2 class="w3-center w3-red">Close this window when all gifts have been claimed!</h2>
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
echo "  var userEmail = '".$_SESSION['email']."';\n";
echo "  var kioskMode = ".json_encode(array_key_exists('kioskMode', $_SESSION) && $_SESSION['kioskMode']).";\n";
?>
</script>
