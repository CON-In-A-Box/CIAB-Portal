<div id="main_content" class="w3-cell w3-cell-top w3-mobile">

<?php
/*.
    require_module 'standard';
.*/

$hour_round_interval = 15;

if (isset($updateComplete)) {
    if ($updateComplete === true) {
        echo '<div class="w3-block w3-center event-color-primary">';
        echo "Hours Recorded";
        echo "</div>\n";
    } else {
        echo '<div class="w3-block w3-center w3-red">';
        echo 'Hour Recording Failed!!  Error!';
        echo "</div>\n";
        $output = print_r($handoff, true);
        $output = trim(preg_replace('~[\r\n]+~', '\\n', $output));
        echo "<script> console.log('".$output."'); </script>\n";
    }
}
?>

<div id="message" class='w3-block w3-center w3-red'></div>

<script>
    userLookup.options({
        message: 'Volunteer Badge Number, E-Mail or Full Name',
        memberName: 'VolunteerID',
        handler: handleResult,
        fail: onFail,
        needForm: false,
        lookupTarget: 'index.php?Function=volunteers/enter&volunteerId='
        });
</script>
<form method="post" action="index.php?Function=volunteers/enter">
<?php
if (isset($uid)) {
    echo "<div id='userLookup_div' data-user='$uid'> </div>\n";
} else {
    echo "<div id='userLookup_div'> </div>\n";
}
?>
  <span class='' id='lookupname'></span>

<div class='w3-bar'>
  <div class='w3-bar-item'>
    <label>Actual hours worked</label>
    <div class='w3-bar'>
      <select class="w3-select w3-bar-item w3-border" type=text name="ActualHours" id="hours" onchange="calculateHours();" title="Actual Hours Worked.">
        <option value=0>0</option>
        <option value=1 selected>1</option>
        <option value=2>2</option>
        <option value=3>3</option>
        <option value=4>4</option>
        <option value=5>5</option>
        <option value=6>6</option>
        <option value=7>7</option>
        <option value=8>8</option>
        <option value=9>9</option>
        <option value=10>10</option>
        <option value=11>11</option>
        <option value=12>12</option>
        <option value=13>13</option>
        <option value=14>14</option>
        <option value=15>15</option>
        <option value=16>16</option>
        <option value=17>17</option>
        <option value=18>18</option>
        <option value=19>19</option>
      </select>
      <select class="w3-select w3-bar-item w3-border" type=text name="ActualMinutes" id="minutes" onchange="calculateHours();" title="Actual Minutes Worked, to the nearest 15 minutes">

<?php
for ($i = 0; $i < 60; $i += $hour_round_interval) {
    echo "<option value = $i ";
    if ($i == 0) {
        echo 'selected';
    }
    echo ">:".sprintf('%02d', $i)."</option>\n";
}
?>
      </select>
    </div>
    <input type=hidden name="ActualHoursWorked" id="actualhours" value=1>
  </div>
  <div class='w3-bar-item'>
    <label>Date/Time that the Shift Ended</label>
    <input class="w3-input w3-border" type="date" name="EndDate" id="end_date" pattern="\d{4}-\d{2}-\d{2}T\d{2}:\d{2}" value="<?php
    echo date('Y-m-d');
    ?>" required onchange="checkHours();">

<?php
$m  = intval(date('i'));
$o = $m % $hour_round_interval;
if ($o != 0) {
    $o = $hour_round_interval - $o;
}
$m += $o;
$h = intval(date('H'));
if ($m >= 60) {
    $m -= 60;
    $h ++;
}
$pm = ($h > 12);
$h %= 12;
?>

    <input class="w3-input w3-bar-item w3-border" type=number id="end_hours" value=<?php echo $h ?> style="width: 75px;" required onchange="checkHours();" name="EndHours" min=0>
    <select class="w3-select w3-bar-item w3-border" type=number name="EndMinutes" id="end_minutes" title="" style="height: 40px;" required onchange="checkHours();" name="EndMinutes">
<?php
for ($i = 0; $i < 60; $i += $hour_round_interval) {
    echo "<option value = $i ";
    if ($m == $i) {
        echo 'selected';
    }
    echo ">:".sprintf('%02d', $i)."</option>\n";
}
?>
    </select>
    <select class="w3-select w3-bar-item w3-border" type=text name="EndAMPM" id="end_ampm" title="" style="height: 40px;" required onchange="checkHours();" name="EndAMPM">
      <option value="AM" <?php
        if (!$pm) {
            echo 'selected';
        }?>>AM</option>
      <option value="PM" <?php
        if ($pm) {
            echo 'selected';
        }?>>PM</option>
    </select>
  </div>
</div>

<div class='w3-container'>
  <div class='w3-block'>
    <label>Volunteer Time Modifier - Must be per policy</label><br />
    <select class="w3-select w3-border" style="width:auto" name="TimeModifier" id="modifier" onchange="calculateHours();">
      <option value=1 selected>Normal - 1 hour = 1 hour</option>
      <option value=0.5>Half Time - 1 hour = 0.5 hours credit</option>
      <option value=1.5>Time Plus Half - 1 hour = 1.5 hours credit</option>
      <option value=2>Double - 1 hour = 2 hours credit</option>
    </select>
  </div>
  <div class='w3-block'>
    <label>Department worked</label><br />
    <select class="w3-select w3-border" style="width:auto" name="DepartmentWorked" id="department">
        <?php
        $isVolunteers = false;
        foreach ($_SESSION['customFields']['currConComPos'] as $position) {
            if ($position['department'] === 'Volunteers') {
                $isVolunteers = true;
                break;
            }
        }
        if ($isVolunteers || isset($_SESSION['IS_ADMIN']) || isset($_SESSION['kioskUser'])) {
            require_once($FUNCTIONDIR."/divisional.inc");
            foreach ($Departments as $dep => $set) {
                echo '<option value="'.$dep.'"';
                if (isset($_REQUEST['department']) && $_REQUEST['department'] == $dep) {
                    echo " selected";
                }
                echo '>'.$dep."</option>\n";
            }
        } else {
            foreach ($_SESSION['customFields']['currConComPos'] as $dep) {
                echo '<option value="'.$dep['department'].'"';
                if (isset($_REQUEST['department']) && $_REQUEST['department'] == $dep['department']) {
                    echo " selected";
                }
                echo '>'.$dep['department']."</option>\n";
            }
        }
        ?>
    </select>
  </div>
</div>

<div class='w3-bar'>
  <div class="w3-bar-item w3-center">
  <p name=summery>
      Crediting
      <span id="totalhours">1 hour</span>
      to
      <span id="volunteername">a Volunteer</span>
    </p>
    <p>
      <b>
        Remember:<br />
        Do Not enter this time on a paper card!
      </b>
    </p>
  </div>
</div>

<div class='w3-bar'>
  <div class="w3-bar-item">
    <?php
    if (($_SESSION['IS_VOLUNTEERS'] || isset($_SESSION['IS_ADMIN'])) &&
        !isset($_SESSION['kioskUser'])) {
        echo '<label class="w3-left">Hours Authorized By</label>';
        echo '<input class="w3-input w3-border" type=text';
    } else {
        echo '<input type=hidden';
    }
    if (isset($_SESSION['kioskUser'])) {
        echo ' name="AuthorizedBy" value="'.$_SESSION['kioskUser'].'" required>';
        echo '<input type=hidden name="EnteredBy" value="'.$_SESSION['kioskUser'].'" required>';
    } else {
        echo ' name="AuthorizedBy" value="'.$_SESSION['accountId'].'" required>';
        echo '<input type=hidden name="EnteredBy" value="'.$_SESSION['accountId'].'" required>';
    }
    ?>
  </div>
</div>

<div class='w3-bar'>
  <div class="w3-bar-item">
    <div class="w3-bar">
      <button id="submitbtn" class="w3-button w3-ripple event-color-primary" disabled>Submit Hours</button>
      <button class="w3-button w3-ripple w3-khaki" type="button" onclick="resetForm()">Clear Form</button>
    </div>
  </div>
</div>

<div class='w3-bar'>
  <div class="w3-bar-item w3-center" style="width:inherit;">
    <p class="w3-small">
        <a href='javascript:void(0)' onclick='batch()'>batch file upload</a>
    </p>
  </div>
</div>

</form>

<div id='csv_import' class='w3-modal'>
  <div class="w3-modal-content">
    <div class=w3-container">
      <div class="w3-row">
        <h2 class="w3-center event-color-primary">Volunteer CSV File To Upload</h2>
      </div>
      <div class="w3-row">
        <p class="w3-margin w3-center">CSV file format:
        <code class="w3-block w3-gray w3-center">&lt;Account,ActualHours,AuthorizedBy,Department,EndDateTime,EnteredBy,Event,TimeModifier&gt;</code></p>
        <p class="w3-margin w3-center"><b>Account fields</b> (Account, AuthorizedBy, EnteredBy)  can be badge number, name, or email. However it has to uniquly identify the user so badge number is always the safest.</p>
        <p class="w3-margin w3-center"><b>Event</b> is optional. If left empty then the current upcoming event is used.</p>
        <p class="w3-margin w3-center"><b>EndDateTime</b> should be quoted. The format is "YYYY-MM-DD HH:MM:SS".</p>
        <h2 class="w3-block w3-center">Example</h2>
        <table class="w3-table w3-center w3-striped w3-border w3-white">
           <tr><td>Attendee Joe,1.000,Connie VonRobot,IT,"2018-06-04 08:15:00",Connie VonRobot,,1.0</tr></td>
           <tr><td>joe@attendee.com,1.000,60055,Gaming,"2018-06-05 08:15:00",Connie VonRobot,,1.0</tr></td>
           <tr><td>60054,1.000,60055,Hotel,"2018-06-06 08:15:00",60055,,1.0</tr></td>
        </table>
      </div>
      <div class="w3-row">
          <div class="w3-left w3-margin w3-half">
              <input style="width: 100%;" type="file" id="batch_file" onchange="fileChange()">
          </div>
          <div class="w3-right">
              <button class="w3-button w3-round w3-red w3-margin" onclick="batchCancel()">Cancel</button>
          </div>
          <div class="w3-right">
              <button class="w3-button w3-round w3-green w3-margin w3-disabled" id="batch_import" onclick="batchImport()">Import</button>
          </div>
      </div>
    </div>
  </div>
</div>

<div id='csv_import_report' class='w3-modal'>
  <div class="w3-modal-content">
    <div class=w3-container">
        <div>
            <h2 class="w3-center event-color-primary">Volunteer CSV Import Result</h2>
        </div>
        <div>
            <table class='w3-table w3-striped w3-bordered w3-white' id="import_report">
            </table>
        </div>
        <div class="w3-center">
              <button class="w3-button w3-round w3-green w3-margin" id="batch_done" onclick="batchDone()">Done</button>
        </div>
    </div>
  </div>
</div>

</div><!--close main_content div-->
