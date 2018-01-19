<?php
require_once $FUNCTIONDIR.'/volunteer.inc';
require_once $FUNCTIONDIR.'/users.inc';

$totalHours = 0;

function hour_table($uid)
{
    global $totalHours;

    echo '<div class="w3-container w3-center w3-green" ';
    echo 'style="max-height:400px; overflow:scroll;">';
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
        $id_index = array_search("Department ID",$keys);
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
    }
    echo "<tr><th class='w3-center w3-green' colspan=9></th></tr>\n";
    echo "</table>";
    echo "</div>";
    echo '<div class="w3-container w3-center w3-xlarge w3-green">';
    echo "<span>Total Hours : ".$totalHours."</span>";
    echo '</div>';
};

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
    foreach($data as $record) {
        if ($record['Remaining'] <= 0) {
            if (isset($uid)) {
                if (isset($record['Aquired']) && $record['Aquired']>0) {
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
                $entry['current'] =  $entry['current'] + 1;
                $groups[$record['RewardGroupID']] = $entry;
            } else {
                array_push($sorted, $record);
            }
        }
    }

    if (isset($uid)) {
        foreach ($groups as $group) {
            if ($group['aquired'] >= $group['limit']) {
                for ($i = $group['start']; $i < $group['start']+sizeof($group['items']); $i++) {
                    $sorted[$i]['group_soldout'] = true;
                }
            }
        }
    }
    return array_merge($sorted, $soldout);

}

function prizes_table($uid)
{
    global $totalHours;

    echo "<div class='w3-container w3-center w3-blue' ";
    echo "style='max-height:200px; overflow:scroll;'>\n";
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
    echo "</tr>";

    if (isset($uid)) {
        $data = volunteer_prizes_for_user($uid);
    } else {
        $data = volunteer_prizes();
    }

    $group_styles  = [
        'w3-amber', 'w3-aqua', 'w3-brown', 'w3-cyan',
        'w3-indigo', 'w3-khaki', 'w3-lime', 'w3-orange',
        'w3-pink', 'w3-purple', 'w3-red', 'w3-sand', 'w3-teal', 'w3-yellow',
        'w3-deep-purple', 'w3-deep-orange', 'w3-light-blue',
        'w3-blue-grey', 'w3-light-green'];

    $data = _build_prize_list($data, $uid);

    $last_group = NULL;
    $group = 0;
    $spent = 0;
    if (!empty($data)) {
        echo "<tr><th>";
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
        echo implode($keys, '</th><th>');
        echo "</th></tr>\n";
        foreach ($data as $key => $record) {
            unset($record['PrizeID']);
            if (isset($uid)) {
                if (isset($record['Aquired'])) {
                    if ($record['Promo'] != 'yes') {
                        $spent += $record['Aquired'] * $record['Value'];
                    }
                } else {
                    $record['Aquired'] = ' ';
                }
            }

            if ($record['Remaining'] <= 0) {
                echo "<tr class='soldoutrow hiddenrow w3-red'>";
            } elseif ($record['Promo'] == 'yes' && $record['Aquired'] > 0) {
                echo "<tr class='soldoutrow hiddenrow w3-disabled'>";
            } elseif ($record['RewardGroupID']) {
                if ($last_group !== null && $last_group != $record['RewardGroupID']) {
                    $group++;
                } elseif ($last_group !== NULL) {
                    $record['Limit'] = '|';
                }
                $last_group = $record['RewardGroupID'];
                $classes = $group_styles[$group];

                if (isset($record['group_soldout'])) {
                    echo "<tr class='$classes soldoutrow hiddenrow w3-disabled'>";
                } else {
                    echo "<tr class='$classes'>";
                }
            } else {
                echo "<tr>";
            }
            unset($record['RewardGroupID']);
            unset($record['group_soldout']);
            echo "<td>";
            echo implode($record, '</td><td>');
            echo "</td></tr>\n";
        }
    }
    echo "<tr><th class='w3-center w3-blue' colspan=8></th></tr>\n";
    echo "</table>\n";
    echo '</div>';
    if ($uid) {
        echo '<div class="w3-container w3-center w3-xlarge w3-blue">';
        $remain = $totalHours - $spent;
        echo "<span>Hours Spent  ".$spent."  |  ".$remain."  Left</span>";
        echo '</div>';
    }
}

$uid = null;
if (!empty($_REQUEST)) {
    // Retrieve and sanitize POST data
    $arguments = [
    'volunteerId'         => FILTER_SANITIZE_SPECIAL_CHARS,
    ];
    $updateData = filter_input_array(INPUT_GET, $arguments);
    $uid = $updateData['volunteerId'];
}
?>

<script>
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
</script>

<div id="main_content" class="w3-cell w3-cell-top w3-mobile">
<div class="w3-container w3-center w3-xlarge w3-green">
<span>Volunteer Management</span>
</div>
<div class="w3-container w3-center">

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
</div>
</form>

</div>
<?php
    hour_table($uid);
    echo '<br>';
    prizes_table($uid);
?>
</div>
