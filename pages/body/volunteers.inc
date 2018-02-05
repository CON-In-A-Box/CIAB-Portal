<?php
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

if (isset($_SESSION['IS_ADMIN']) || isset($_SESSION['IS_VOLUNTEERS'])) {
    if (isset($_COOKIE["CIAB_VOLUNTEERADMIN"])) {
        $admin_mode = $_COOKIE["CIAB_VOLUNTEERADMIN"];
    }
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
                    $entry['aquired'] = $entry['aquired'] + $record['Aquired'];
                } else {
                    $entry = ['current' => sizeof($sorted),
                    'start' => sizeof($sorted),
                    'items' => array($record),
                    'aquired' => $record['Aquired'],
                    'limit' => $record['Limit']];
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


function hour_table($uid)
{
    global $totalHours,$admin_mode;

    echo '<div class="w3-container w3-center w3-green" ';
    echo 'style="max-height:34vh; overflow:scroll;">';
    echo "<table class='w3-table w3-striped w3-bordered w3-white'>";
    echo "<tr><th class='w3-center w3-green' colspan=9>Hours</th></tr>\n";
    if (isset($uid)) {
        $data = get_volunteer_hours_for_user($uid);
        $totalHours = 0;
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
            if (isset($uid)) {
                if (array_key_exists('Time Modifier', $record)) {
                    $totalHours += $record['Actual Hours'] * $record['Time Modifier'];
                } else {
                    $totalHours += $record['Total Hours'];
                }
            }
            if ($uid && $admin_mode) {
                echo "<tr class='w3-hover-red' ";
                $json = json_encode($record);
                $json = base64_encode($json);
                echo 'onclick=\'show_edit_hours("'.$json;
                echo '");\'>';
            } else if (!isset($uid) && $admin_mode) {
                echo "<tr class='w3-hover-red' ";
                echo 'onclick=\'department_report("'.$record['Department Worked'].'", '.$record['Department ID'].');\'';
                echo ">";
            } else {
                echo "<tr>";
            }
            echo "<td>";
            unset($record['EntryID']);
            if ($id_index !== false) {
                unset($record['Department ID']);
            }
            echo implode(_htmlsafeify($record), '</td><td>');
            echo "</td></tr>\n";
        }
    } else {
        echo "<tr><th class='w3-center w3-gray w3-opacity' colspan=9>No Entries...</th></tr>\n";
    }
    echo "<tr><th class='w3-center w3-green' colspan=9></th></tr>\n";
    echo "</table>";
    echo "</div>";
    echo '<div class="w3-col w3-center w3-xlarge w3-green">';
    if (isset($uid)) {
        echo "<span>Total Hours : ".$totalHours."</span>";
    } else {
        echo "<span class='w3-margin'>Total Hours : ".$summary['TotalHours']."</span>";
        echo "<span class='w3-margin'>Total Volunteers : ".$summary['VolunteerCount']."</span>";
        echo "<span class='we-margin'>Total Spent Hours : ".$summary['TotalHoursSpent']."</span>";
    }
    echo "</div>\n";

};


function acquireable_item($item)
{
    global $totalHours, $hoursRemain;
    if ($item['Promo'] == 'yes') {
        return ($item['Value'] <= $totalHours);
    }
    return ($item['Value'] <= $hoursRemain);

}


function _prize_table_start($uid = 0, $spent = 0, $hoursRemain = 0)
{
    global $admin_mode;

    if ($uid) {
        echo '<div class="w3-col w3-center w3-xlarge w3-blue">';
        echo "<span>Hours Spent  <span id='h_spent'>".$spent."</span>  |  ";
        echo "<span id='h_left'>".$hoursRemain."</span>  Left</span>";
        echo "</div>\n";
    }
    echo "<div class='w3-container w3-center w3-blue' ";
    if ($admin_mode && $uid) {
        echo "style='max-height:29vh; overflow:scroll;'>\n";
    } else {
        echo "style='max-height:33vh; overflow:scroll;'>\n";
    }
    echo "<table class='w3-table w3-striped w3-bordered w3-white'";
    echo " id='prizes'>\n";
    echo "<tr>";
    echo "<th class='w3-blue'>";
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
    echo "<th class='w3-center w3-blue' colspan=3>Prizes</th>";
    echo "<th class='w3-blue' colspan=3></th>\n";
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
    'w3-blue-grey', 'w3-light-green'];

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
        $hoursRemain = $totalHours - $spent;

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
                echo 'onclick=\'show_edit_prize("'.$json.'");\'';
                echo ">";
            } else {
                if ($record['Remaining'] <= 0) {
                    echo "<tr class='soldoutrow hiddenrow w3-red'>";
                } elseif ($record['Promo'] == 'yes' && $record['Aquired'] > 0) {
                    echo "<tr class='soldoutrow hiddenrow w3-disabled'>";
                } elseif ($record['RewardGroupID']) {
                    $record['GroupLimit'] = $record['Limit'];
                    if ($last_group !== null && $last_group != $record['RewardGroupID']) {
                        $group++;
                    } elseif ($last_group !== null) {
                        $record['Limit'] = '|';
                    }
                    if (ctype_digit($record['Aquired'])) {
                        $groupTotals[intval($record['RewardGroupID'])] += intval($record['Aquired']);
                    } else {
                        $groupTotals[intval($record['RewardGroupID'])] += 0;
                    }
                    $last_group = $record['RewardGroupID'];
                    $classes = $group_styles[($group % count($group_styles))];

                    if (isset($record['group_soldout'])) {
                        echo "<tr class='$classes soldoutrow hiddenrow w3-disabled'>";
                    } else {
                        echo "<tr class='$classes";
                        if (!$admin_mode && isset($uid) && acquireable_item($record)) {
                            echo " w3-hover-green' ";
                            $json = json_encode($record);
                            $json = base64_encode($json);
                            echo 'onclick=\'add_to_checkout("'.$json;
                            echo '");\'>';
                        } else {
                            echo "'>";
                        }
                    }
                } else {
                    echo "<tr ";
                    if (!$admin_mode && isset($uid) && acquireable_item($record)) {
                        echo "class='w3-hover-green' ";
                        $json = json_encode($record);
                        $json = base64_encode($json);
                        echo 'onclick=\'add_to_checkout("'.$json.'");\'';
                    }
                    echo ">";
                }
                unset($record['RewardGroupID']);
            }
            unset($record['PrizeID']);
            unset($record['group_soldout']);
            unset($record['GroupLimit']);
            echo "<td>";
            echo implode(_htmlsafeify($record), '</td><td>');
            echo "</td></tr>\n";
        }
    } else {
        _prize_table_start();
        echo "<tr><th class='w3-center w3-gray w3-opacity' colspan=9>No Entries...</th></tr>\n";
    }
    echo "<tr><th class='w3-center w3-blue' colspan=8></th></tr>\n";
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
            if ($record['Promo'] == 'yes' &&
                $record['Remaining'] > 0 &&
                $totalHours >= $record['Value'] &&
                !isset($record['Aquired'])) {
                if ($record['RewardGroupID'] !== null) {
                    if ($record['group_soldout']) {
                        continue;
                    }
                    $promo .= "<li>Free Group prize <b>".htmlspecialchars($record['Name'])."</b> (at ".$record['Value']." hours) is available and has not been acquired! <b>Pick 1 below!</b></li>\n";
                } else {
                    $promo .= "<li>Free prize <b>".htmlspecialchars($record['Name'])."</b> (at ".$record['Value']." hours) is available and has not been acquired!</li>\n";
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
                echo "onclick=add_promo_to_checkout();";
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
    echo "onclick='show_edit_prize(null)';>Add New Prize</button>\n";
    echo "<button id='min_hours' class='w3-button w3-round-xxlarge w3-red'";
    echo "onclick='min_hour_report()';>Minimum Hour Report</button>\n";
    echo "</div>\n";
    echo "</div>\n";

}


?>

<script>
  var checkout = [];
  var hours_spent = 0;

  function escapeHtml(text) {
    var map = {
     '&': '&amp;',
     '<': '&lt;',
     '>': '&gt;',
     '"': '&quot;',
     "'": '&#039;'
    };

    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
  }

  function lookupId(id) {
    if (id) {
      var xhttp = new XMLHttpRequest();
      document.getElementById('spinner').innerHTML = "<i class='fa fa-spinner w3-spin'></i>";
      document.getElementById('message').innerHTML = "";
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          var response = JSON.parse(this.responseText);
          var uid = response['Id'];
          document.getElementById('volunteer').classList.remove('w3-red');
          document.getElementById('message').innerHTML = escapeHtml("Found " + response['First Name'] + " " + response['Last Name']);
          window.location = "index.php?Function=volunteers&volunteerId="+uid;
        } else if (this.readyState == 4) {
          document.getElementById('volunteer').classList.add('w3-red');
          document.getElementById('spinner').innerHTML = "";
          if (this.status == 400) {
              document.getElementById('message').innerHTML = id + " invalid lookup.";
          }
          else if (this.status == 404) {
              document.getElementById('message').innerHTML = id + " not found.";
          }
          else if (this.status == 409) {
              document.getElementById('message').innerHTML = id + " has too many matches.";
          }
        }
      };
      xhttp.open("GET", "index.php?Function=volunteers&lookupId=" + id, true);
      xhttp.send();
    } else {
        window.location = "index.php?Function=volunteers";
    }
  }

  function enter_admin(){
    setTimeout(location.reload(), 1000);
  }

  function fail_admin(error){
    document.getElementById('admin_slider').checked = false;
    if (error) {
        alert("Login Failed ("+error+")");
    }
  }

  function toggle_admin_mode(uid)
  {
    document.cookie = 'CIAB_VOLUNTEERADMIN=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    var target = "";
    if (uid) {
        target = "index.php?Function=volunteers&volunteerId="+uid;
    } else {
        target = "index.php?Function=volunteers";
    }
<?php
if (!$admin_mode) {
?>
    var user = "<?php echo $_SESSION['email']; ?>";
    checkAuthentication(user, enter_admin, fail_admin);
<?php
} else {
    echo "setTimeout(function() {window.location = target;}, 1000);";
}
?>
  }

</script>

<?php
echo '<div id="main_content" style="height:94vh;" class="w3-row-padding';
if ($admin_mode) {
    echo ' w3-topbar w3-bottombar w3-leftbar w3-rightbar w3-border-red">';
    echo "\n";
    echo "<div class='w3-rest w3-center w3-xlarge w3-red'>\n";
    echo "<span>Volunteer Administration</span>\n";
} else {
    echo '">';
    echo "\n";
    echo "<div class='w3-rest w3-center w3-xlarge w3-green'>\n";
    echo "<span>Volunteer Management</span>\n";
}
echo "</div>\n"
?>

<div class="w3-rest w3-center">
<?php
if (isset($_SESSION['IS_ADMIN']) || isset($_SESSION['IS_VOLUNTEERS'])) {
?>
  <div>
    <div class="w3-show-inline-block w3-right w3-red w3-margin">
      <table class='switch-table'><tr><td>
        Admin Mode
        <label class=switch><input type="checkbox" class=toggle id=admin_slider <?php
        if ($admin_mode) {
            echo "checked";
        }
?> onclick='toggle_admin_mode(<?php echo $uid; ?>);'>
          <div class=slider></div></label></td></tr>
      </table>
    </div>
  </div>
<?php
}
?>

<div>
<form method="post" action="index.php?Function=volunteers">
<div class='w3-bar'>
  <div class='w3-bar-item'>
    <label>Volunteer Badge Number, E-Mail or Full Name</label>
    <div class='w3-bar'>
      <input class="w3-input w3-border w3-bar-item" name="VolunteerID" id="volunteer" onchange="lookupId(this.value);"
<?php
if (isset($uid)) {
    echo 'value='.$uid.' ';
}
?>
        placeholder="(badge #, email, Name)" required>
      <button type="button" class="w3-button w3-bar-item w3-border icon-barcode button-scan" onclick="QuaggaApp.init('volunteer', lookupId)"></button>
      <span class='w3-bar-item' id='spinner'></span>
      <span class='w3-bar-item' id='message'></span>
    </div>
  </div>
  <div>
  </div>
</div>
</form>
</div>
</div>

<div class="w3-rest w3-center">

<!------ Main Section ----->

<div class='w3-row-padding'>
    <div id='info_div' class='w3-rest'>
<?php
initialize($uid);
hour_table($uid);
if (!$admin_mode) {
    messages($uid);
} else {
    admin_bar($uid);
}
prizes_table($uid);
?>
    </div>

    <div class='w3-hide w3-padding' id='department_report_div'>
        <div class='w3-center'>
            <h2 class='w3-red'>Department Report</h2>
        </div>
        <hr>
        <div>
            <h2 id='dept_name' class='w3-green'></h2>
            <input class="w3-input w3-disabled w3-hide" id="dept_data" readonly>
            <input class="w3-input w3-disabled w3-hide" id="dept_data_name" readonly>
        </div>
        <hr>
        <div class='w3-center'>
            <button id='generate_dept_report' class='w3-button w3-round-xxlarge w3-green'
                onclick='generate_dept_report();'>
               Generate Department Report &lt;CSV&gt;
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
            <button id='generate_report' class='w3-button w3-round-xxlarge w3-green'
                onclick='generate_hour_report();'>
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
            <h2 id='edit_prize_title'>Edit Prize Entry</h2>
        </div>
        <div class='w3-border w3-padding'>
          <form>

            <label for='edit_prize_name' class="w3-tooltip">
                <span
                  style="position:absolute;left:-80px;right:-80px;bottom:18px"
                  class="w3-text w3-tag w3-green w3-round-xlarge">Visible name of the item</span>
            Name:</label>
            <input class="w3-input w3-border" id="edit_prize_name" placeholder="<name>">
            <label for='edit_prize_value' class="w3-tooltip">
                <span
                  style="position:absolute;left:-80px;right:-80px;bottom:18px"
                  class="w3-text w3-tag w3-green w3-round-xlarge">Hour cost of the item</span>
            Value:</label>
            <input class="w3-input w3-border" id="edit_prize_value">
            <label for='edit_prize_value' class="w3-tooltip">
                <span
                  style="position:absolute;left:-80px;right:-80px;bottom:18px"
                  class="w3-text w3-tag w3-green w3-round-xlarge">If 'yes' this item does not cost hours but instead is free at the given hour mark. Also enforces a limit of 1.</span>
            Promo Item:</label>
            <select class="w3-select w3-border" id="edit_prize_promo">
              <option value='yes'>Yes</option>
              <option value='no'>No</option>
            </select>

            <label for='edit_prize_count' class="w3-tooltip">
                <span
                  style="position:absolute;left:-80px;right:-80px;bottom:18px"
                  class="w3-text w3-tag w3-green w3-round-xlarge">Changing this will adjust Total items</span>
            Inventory Remaining:</label>
            <input class="w3-input w3-border" id="edit_prize_count">

                    <label for='edit_prize_group' class="w3-tooltip">
                        <span
                          style="position:absolute;left:-80px;right:-80px;bottom:18px"
                          class="w3-text w3-tag w3-green w3-round-xlarge">If in a group there can be only a limited total number of the items in the group claimed by the volunteer</span>
                    Prize Group:</label>
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
            <button id='set_prize_button' class='w3-button w3-round-xxlarge w3-green'
                onclick='commit_prize();'>
                Commit
            </button>
            <button id='delete_prize_button' class='w3-button w3-round-xxlarge w3-blue'
                onclick='delete_prize();'>
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
            <button id='set_hours_button' class='w3-button w3-round-xxlarge w3-green'
                onclick='commit_hours();'>
                Commit
            </button>
            <button id='delete_hours_button' class='w3-button w3-round-xxlarge w3-blue'
                onclick='delete_hours();'>
                Delete
            </button>
            <button id='exit_hours_button' class='w3-button w3-round-xxlarge w3-red'
                onclick='hideSidebar();'>
               Cancel
            </button>
        </div>
    </div>

    <div class='w3-hide w3-padding' id='checkout_div'>
        <div class='w3-center'>
            <h2>Items claimed now</h2>
        </div>
        <table class='w3-table w3-striped w3-border w3-white' id='checkout_table'>
        </table>
        <div class="w3-container w3-center w3-large w3-green w3-margin">
            <span>Hours Left: <span id="hours_left">0</span></span>
            <br>
            <span>Hours Spent: <span id="hours_used">0</span></span>
        </div>
        <div class='w3-center'>
            <button id='checkout_button' class='w3-button w3-round-xxlarge w3-green'
                onclick='process_checkout();'>
                Distribute Items
            </button>
            <button id='clear_button' class='w3-button w3-round-xxlarge w3-red'
                onclick='clear_checkout();'>
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
      <h2 class="w3-center w3-green">Please get the volunteer the following new rewards!</h2>
      <hr>
      <table class='w3-center w3-border w3-table w3-striped w3-white w3-xlarge w3-padding' id='reward_list'>
      </table>
      <h2 class="w3-center w3-red">Close this window when all prizes have been claimed!</h2>
    </div>
  </div>
</div>

<script>
  var groupsNow = JSON.parse('<?php print(json_encode($groupTotals));?>');
  var currentSidebar = null;

    function hideSidebar() {
        if (currentSidebar) {
            currentSidebar.classList.add("w3-hide");
            currentSidebar.classList.remove("w3-quarter");
            var section = document.getElementById("info_div");
            section.classList.add("w3-rest");
            section.classList.remove("w3-threequarter");
            currentSidebar = null;
        }
    }

    function showSidebar(Id) {
        if (currentSidebar) {
            hideSidebar();
        }

        currentSidebar = document.getElementById(Id);
        currentSidebar.classList.remove("w3-hide");
        currentSidebar.classList.add("w3-quarter");
        var section = document.getElementById("info_div");
        section.classList.remove("w3-rest");
        section.classList.add("w3-threequarter");
    }

    function process_checkout() {
        applyReward();
    }

    function clear_checkout() {
        hideSidebar();
        hours_spent = 0;
        checkout = [];

        var table = document.getElementById("checkout_table");
        while(table.hasChildNodes())
        {
           table.removeChild(table.firstChild);
        }
        groupsNow = JSON.parse('<?php print(json_encode($groupTotals));?>');
    }

    function update_cost(cost) {
        hours_spent += cost;

        var hours = document.getElementById("hours_left");
        var remain = <?php echo "$hoursRemain";?> - hours_spent;
        hours.innerHTML = remain.toFixed(2);

        var hours_u = document.getElementById("hours_used");
        hours_u.innerHTML = hours_spent.toFixed(2);
    }

    function update_checkout() {
        var table = document.getElementById("checkout_table");
        while(table.hasChildNodes())
        {
           table.removeChild(table.firstChild);
        }

        var output = {};
        for (var index in checkout) {
            item = checkout[index];
            if (item.PrizeID in output) {
                output[item.PrizeID]['count'] += 1;
            } else {
                output[item.PrizeID] = {prize: item, count: 1};
            }
        }

        var table = document.getElementById("checkout_table");
        for (var index in output) {
            item = output[index];
            var row = table.insertRow(-1);
            row.setAttribute('data-prizeid', item.prize.PrizeID);
            row.classList.add('w3-hover-red');
            row.setAttribute("onclick", "remove_from_checkout("+item.prize.PrizeID+", "+item.prize.cost+", "+item.prize.RewardGroupID+");");
            var cell = row.insertCell(0);

            if (item.count > 1) {
                txt = item.prize.Name + ' (x' + item.count+')';
                cell.innerHTML = escapeHtml(txt);
            } else {
                cell.innerHTML = escapeHtml(item.prize.Name);
            }

            var cell = row.insertCell(1);
            cell.innerHTML = item.prize.cost.toFixed(2);
        }

        var rows = table.getElementsByTagName("tr").length;
        if (rows == 0) {
            clear_checkout();
        }
    }

    function remove_from_checkout(PrizeID, cost, group) {
        var found = checkout.findIndex(function(element) {
            return element.PrizeID == PrizeID;
        });
        if (found != -1) {
            update_cost(-1 * cost);
            checkout.splice(found, 1);
        }

        if (group != null) {
            groupsNow[group] -= 1;
        }

        update_checkout();
    }

    function add_to_checkout(json) {
        var item = JSON.parse(atob(json));
        cost = parseFloat(item.Value);
        if (item.Promo == 'yes') {
            var found = checkout.find(function(element) {
                return element.PrizeID == item.PrizeID;
            });
            if (found) {
                alert("Promo item '"+item.Name+"' cannot be added twice");
                return;
            }
            cost = 0;
        }
        if (<?php echo $hoursRemain?> < hours_spent + cost) {
            alert("Volunteer does not have enough hours for the "+item.Name);
            return;
        }
        if (groupsNow[item.RewardGroupID] + 1 > item.GroupLimit) {
            alert("Too many items from limited group");
            return;
        }
        var count = 0;
        for (var i = 0; i < checkout.length; i++) {
            if (checkout[i].PrizeID == item.PrizeID)
                count++;
        }
        if (count + 1 > item.Remaining) {
            alert("Not enough items in inventory!");
            return;
        }

        item.cost = cost;
        show_checkout();
        checkout.push(item);
        update_checkout();
        update_cost(cost);
        groupsNow[item.RewardGroupID] += 1;
    }

    function show_checkout() {
        showSidebar("checkout_div");

        var hours = document.getElementById("hours_left");
        var remain = parseFloat(<?php echo "$hoursRemain";?>) - hours_spent;
        hours.innerHTML = remain.toFixed(2);

        var hours_u = document.getElementById("hours_used");
        hours_u.innerHTML = hours_spent;
    }

    function  add_promo_to_checkout(names) {
<?php
foreach ($unclaimed_promo as $item) {
    echo "  var found = checkout.find(function(element) {\n";
    echo "      return element[0] == ".$item['PrizeID'].";\n";
    echo " });\n";
    echo "   if (!found) {\n";
    echo "     add_to_checkout('".base64_encode(json_encode($item))."');\n";
    echo "      }\n";
    echo "\n";
}
?>
  }

  function applyReward() {
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            fill_reward();
            document.getElementById('success_dlg').style.display='block'
        }
        else if (this.status == 404) {
            alert("404!");
        }
        else if (this.status == 409) {
            alert("409!");
        }
      }
      xhttp.open("POST", "index.php?Function=volunteers", true);
      xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhttp.send("rewardId=<?php echo "$uid";?>&rewards="+JSON.stringify(checkout));
  }

    function fill_reward() {
        var table = document.getElementById("reward_list");
        while(table.hasChildNodes())
        {
           table.removeChild(table.firstChild);
        }

        var list = {};
        checkout.forEach(function(item, index) {
            if (item.PrizeID in list) {
                list[item.PrizeID]['count'] +=1;
            } else {
                list[item.PrizeID] = {name: item.Name, count: 1};
            }
        });

        for (var key in list) {
            item = list[key];
            var row = table.insertRow(-1);
            var cell = row.insertCell(0);
            if (item.count > 1) {
                txt = item.name + ' (x' + item.count+')';
                cell.innerHTML = escapeHtml(txt);
            } else {
                cell.innerHTML = escapeHtml(item.name);
            }
        };
    }

    function show_edit_hours(data) {
        showSidebar("edit_user_hour_div");
        var item = JSON.parse(atob(data));
        document.getElementById("edit_name").value = item.Volunteer;
        document.getElementById("edit_hours").value = item['Actual Hours'];
        var options = document.getElementById("edit_mod");
        var value = parseFloat(item['Time Modifier']);
        options.selectedIndex = 0;
        for (var i = 0, n = options.length; i < n ; i++) {
            if (options[i].value == value) {
                options.selectedIndex = i;
                break;
            } else if (options[i].value < value) {
                options.selectedIndex = i;
            } else {
                break;
            }
        }
        document.getElementById("edit_enter").value = item['Entered By'];
        document.getElementById("edit_auth").value = item['Authorized By'];
        document.getElementById("edit_dept").value = item['Department Worked'];
        var date = item['End Date Time'];
        date = date.replace(/\s+/g, 'T');
        document.getElementById("edit_end").value = date;
        document.getElementById("edit_data").value = data;
    }

    function commit_hours() {
        if (!confirm("=============================\nPlease! double check entries!\n=============================\n\nProceed with Volunteer Hour Update?"))
            return;

        data = document.getElementById("edit_data").value;
        var item = JSON.parse(data);

        item['Actual Hours'] = parseFloat(document.getElementById("edit_hours").value);
        item['End Date Time'] = document.getElementById("edit_end").value;
        var e = document.getElementById("edit_mod");
        item['Time Modifier'] = e.options[e.selectedIndex].value;
        item['Department Worked'] = document.getElementById("edit_dept").value;
        item['Authorized By'] = document.getElementById("edit_auth").value;

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            location.reload();
          }
          else if (this.status == 404) {
              alert("ERROR 404!");
          }
          else if (this.status == 409) {
              alert("ERROR 409!");
          }
        }
        xhttp.open("POST", "index.php?Function=volunteers", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("update_hour="+JSON.stringify(item));
    }

    function delete_hours()
    {
        if (!confirm("DELETE Volunteer Entry?"))
            return;

        data = document.getElementById("edit_data").value;
        var item = JSON.parse(data);

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            location.reload();
          }
          else if (this.status == 404) {
              alert("ERROR 404!");
          }
          else if (this.status == 409) {
              alert("ERROR 409!");
          }
        }
        xhttp.open("POST", "index.php?Function=volunteers", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("delete_hour="+item['EntryID']);
    }

    function show_edit_prize(data)
    {
        showSidebar("edit_prize_div");

        if (!data) {
            document.getElementById("edit_prize_title").innerHTML = "Enter New Prize";
            document.getElementById("delete_prize_button").style.visibility = 'hidden';
            var item = {};
        } else {
            document.getElementById("edit_prize_title").innerHTML = "Edit Prize Entry";
            document.getElementById("delete_prize_button").style.visibility = 'visible';
            var item = JSON.parse(atob(data));
        }

        var grp = document.getElementById("edit_prize_group");
        if (grp.length == 1) {
            for (var key in groupsNow) {
                var option = document.createElement("option");
                option.text = "Group #"+key+" : Limit "+groupsNow[key];
                option.value = key;
                grp.add(option);
            }
            var option = document.createElement("option");
            option.text = "New Group";
            option.value = 'new';
            grp.add(option);
        }

        if (data) {
            var item = JSON.parse(atob(data));
            document.getElementById("edit_prize_name").value = item.Name;
            document.getElementById("edit_prize_value").value = item.Value;
            document.getElementById("edit_prize_promo").value = item.Promo;
            if (item.RewardGroupID) {
                grp.value = item.RewardGroupID;
            } else {
                grp.value = 'none';
            }
            prizeGroupChange();

            document.getElementById("edit_prize_count").value = item.Remaining;

            document.getElementById("prize_data").value = data;
        } else {
            document.getElementById("edit_prize_name").value = "";
            document.getElementById("edit_prize_value").value = 0.0;
            document.getElementById("edit_prize_count").value = 0;
            document.getElementById("edit_prize_promo").value = 'no';
            grp.value = 'none';
            prizeGroupChange();
            document.getElementById("prize_data").value = null;
        }
    }

    function delete_prize()
    {
        if (!confirm("DELETE Prize Entry?\n\nWARNING!!!  Only do this if NONE of this prize has been distributed. It will lead to corrupt reward records. To DELETE a prize that has been rewarded set inventory to '0'"))
            return;

        data = document.getElementById("prize_data").value;
        var item = JSON.parse(atob(data));

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            location.reload();
          }
          else if (this.status == 404) {
              alert("ERROR 404!");
          }
          else if (this.status == 409) {
              alert("ERROR 409!");
          }
        }
        xhttp.open("POST", "index.php?Function=volunteers", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("delete_prize="+item['PrizeID']);
    }

    function commit_prize()
    {
        data = document.getElementById("prize_data").value;
        var item = null;

        if (data) {
            if (!confirm("=============================\nPlease! double check entries!\n=============================\n\nProceed with Volunteer Prize Update?"))
                return;
            item = JSON.parse(atob(data));
        } else {
            if (!confirm("=============================\nPlease! double check entries!\n=============================\n\nProceed with Addition of new Volunteer Prize?"))
                return;
            item = {Name:'', Value:0, RewardGroupID:null, GroupLimit:0,
                    Promo:'no', TotalInventory:0, Remaining:0 };
        }

        item['Name'] = document.getElementById("edit_prize_name").value;
        item['Value'] = parseFloat(document.getElementById("edit_prize_value").value);
        var grp = document.getElementById("edit_prize_group").value;
        if (grp === 'none') {
            item['RewardGroupID'] = '';
        } else {
            item['RewardGroupID'] = grp;
        }

        item['GroupLimit'] = parseInt(document.getElementById("edit_prize_group_count").value);

        var e = document.getElementById("edit_prize_promo");
        item['Promo'] = e.options[e.selectedIndex].value;

        if (item['Remaining'] != document.getElementById("edit_prize_count").value)
        {
            var amount = parseInt(item['Remaining']) - parseInt(document.getElementById("edit_prize_count").value);
            if (amount !== 0) {
                var newValue = parseInt(item['TotalInventory']) - amount;
                item['TotalInventory'] = newValue;
            }
        }

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            location.reload();
          }
          else if (this.status == 404) {
              alert("ERROR 404!");
          }
          else if (this.status == 409) {
              alert("ERROR 409!");
          }
        }
        xhttp.open("POST", "index.php?Function=volunteers", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        if (data) {
            xhttp.send("update_prize="+JSON.stringify(item));
        } else {
            xhttp.send("new_prize="+JSON.stringify(item));
        }

    }

    function prizeGroupChange()
    {
        var grp = document.getElementById("edit_prize_group").value;
        var cnt = document.getElementById("edit_prize_group_count");
        if (grp === 'none') {
            cnt.disabled = true;
            cnt.value = '';
            cnt.classList.add("w3-disabled");
        } else if ( grp === 'new') {
            cnt.value = 1;
            cnt.disabled = false;
            cnt.classList.remove("w3-disabled");
        } else {
            cnt.value = groupsNow[grp];
            cnt.disabled = false;
            cnt.classList.remove("w3-disabled");
        }
    }

    function generate_hour_report()
    {
        var hours = document.getElementById("report_hour_min").value;
        window.location = "index.php?Function=volunteers&min_hour="+hours;
    }

    function min_hour_report() {
        showSidebar("hour_report_div");
        document.getElementById("report_hour_min").value = 30;
    }

    function generate_dept_report() {
        var name = document.getElementById("dept_data_name").value;
        var deptid = document.getElementById("dept_data").value;
        window.location = "index.php?Function=volunteers&dept_report="+deptid+"&dept_name="+name;
    }

    function department_report(name, dept) {
        showSidebar("department_report_div");
        document.getElementById("dept_name").innerHTML = name;
        document.getElementById("dept_data").value = dept;
        document.getElementById("dept_data_name").value = name;
    }

</script>
