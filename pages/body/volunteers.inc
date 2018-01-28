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
    global $prizeData;

    if (isset($uid)) {
        $prizeData = volunteer_prizes_for_user($uid);
    } else {
        $prizeData = volunteer_prizes();
    }

    $prizeData = _build_prize_list($prizeData, $uid);

}


function hour_table($uid)
{
    global $totalHours;

    echo '<div class="w3-container w3-center w3-green" ';
    echo 'style="max-height:35vh; overflow:scroll;">';
    echo "<table class='w3-table w3-striped w3-bordered w3-white'>";
    echo "<tr><th class='w3-center w3-green' colspan=9>Hours</th></tr>\n";
    if (isset($uid)) {
        $data = get_volunteer_hours_for_user($uid);
    } else {
        $data = get_volunteer_hour_summary();
    }
    $totalHours = 0;
    if (!empty($data)) {
        echo "<tr><th>";
        $keys = array_keys($data[0]);
        $id_index = array_search("Department ID", $keys);
        if ($id_index !== false) {
            unset($keys[$id_index]);
        }
        echo implode($keys, '</th><th>');
        echo "</th></tr>\n";
        foreach ($data as $key => $record) {
            if (array_key_exists('Time Modifier', $record)) {
                $totalHours += $record['Actual Hours'] * $record['Time Modifier'];
            } else {
                $totalHours += $record['Total Hours'];
            }
            echo "<tr><td>";
            if ($id_index !== false) {
                unset($record['Department ID']);
            }
            echo implode($record, '</td><td>');
            echo "</td></tr>\n";
        }
    } else {
        echo "<tr><th class='w3-center w3-gray w3-opacity' colspan=9>No Entries...</th></tr>\n";
    }
    echo "<tr><th class='w3-center w3-green' colspan=9></th></tr>\n";
    echo "</table>";
    echo "</div>";
    echo '<div class="w3-col w3-center w3-xlarge w3-green">';
    echo "<span>Total Hours : ".$totalHours."</span>";
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
    if ($uid) {
        echo '<div class="w3-col w3-center w3-xlarge w3-blue">';
        echo "<span>Hours Spent  <span id='h_spent'>".$spent."</span>  |  ";
        echo "<span id='h_left'>".$hoursRemain."</span>  Left</span>";
        echo "</div>\n";
    }
    echo "<div class='w3-container w3-center w3-blue' ";
    echo "style='max-height:35vh; overflow:scroll;'>\n";
    echo "<table class='w3-table w3-striped w3-bordered w3-white'";
    echo " id='prizes'>\n";
    echo "<tr>";
    echo "<th class='w3-blue'>";
    echo "<input id='soldoutcheck' type='checkbox' class='w3-check' ";
    echo "onclick='showHideSoldOut()'>";
    echo "<label for='soldoutcheck'>";
    if (isset($uid)) {
        echo "Show volunteer's soldout items";
    } else {
        echo "Show soldout items";
    }
    echo "</label></th>\n";
    echo "<th class='w3-center w3-blue' colspan=3>Prizes</th>";
    echo "<th class='w3-blue' colspan=3></th>\n";
    echo "</tr>\n";

}


function prizes_table($uid)
{
    global $totalHours, $prizeData, $hoursRemain, $groupTotals;

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
        $idx = array_search('RewardGroupID', $keys);
        unset($keys[$idx]);
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
        echo implode($keys, '</th><th>');
        echo "</th></tr>\n";
        foreach ($data as $key => $record) {
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
                    if (isset($uid) && acquireable_item($record)) {
                        echo " w3-hover-green' ";
                        $json = json_encode($record);
                        $json = addslashes($json);
                        echo 'onclick=\'add_to_checkout("'.$json;
                        echo '");\'>';
                    } else {
                        echo "'>";
                    }
                }
            } else {
                echo "<tr ";
                if (isset($uid) && acquireable_item($record)) {
                    echo "class='w3-hover-green' ";
                    $json = json_encode($record);
                    $json = addslashes($json);
                    echo 'onclick=\'add_to_checkout("'.$json.'");\'';
                }
                echo ">";
            }
            unset($record['PrizeID']);
            unset($record['RewardGroupID']);
            unset($record['group_soldout']);
            unset($record['GroupLimit']);
            echo "<td>";
            echo implode($record, '</td><td>');
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
                    $promo .= "<li>Free Group prize <b>".$record['Name']."</b> (at ".$record['Value']." hours) is available and has not been acquired! <b>Pick 1 below!</b></li>\n";
                } else {
                    $promo .= "<li>Free prize <b>".$record['Name']."</b> (at ".$record['Value']." hours) is available and has not been acquired!</li>\n";
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


?>

<script>
  var checkout = [];
  var hours_spent = 0;

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
          document.getElementById('message').innerHTML = "Found " + response['First Name'] + " " + response['Last Name'];
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
    echo "<hr>\n";
}
prizes_table($uid);
?>
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
                cell.innerHTML = txt;
            } else {
                cell.innerHTML = item.prize.Name;
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
        var item = JSON.parse(json);
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
    echo "     add_to_checkout('".json_encode($item)."');\n";
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
                cell.innerHTML = txt;
            } else {
                cell.innerHTML = item.name;
            }
        };
    }
</script>
