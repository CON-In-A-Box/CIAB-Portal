<div id="main_content" class="UI-cell-top UI-maincontent">

<?php
/*.
    require_module 'standard';
.*/

require_once $BASEDIR.'/kiosk/kiosk.inc';

$hour_round_interval = 15;

if (isset($updateComplete)) {
    if ($updateComplete === true) {
        echo '<div class="VOL-enter-msg">';
        echo "Hours Recorded";
        echo "</div>\n";
    } else {
        echo '<div class="VOL-enter-msg-error">';
        echo 'Hour Recording Failed!!  Error!';
        echo "</div>\n";
        $output = print_r($handoff, true);
        $output = trim(preg_replace('~[\r\n]+~', '\\n', $output));
        echo "<script> console.log('".$output."'); </script>\n";
    }
}
?>

<div id="message" class='VOL-enter-msg-error'></div>

<script>
    userLookup.options({
        message: 'Volunteer Badge Number, E-Mail or Full Name',
        memberName: 'VolunteerID',
        handler: handleResult,
        fail: onFail,
        needForm: false,
        lookupTarget: 'volunteers/enter',
        lookupParam: 'volunteerId'
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

<div class='UI-bar'>
  <div class='UI-bar-item'>
    <label class='UI-label'>Actual hours worked</label>
    <div class='UI-bar'>
      <select class="UI-select UI-bar-item" type=text name="ActualHours" id="hours" onchange="calculateHours();" title="Actual Hours Worked.">
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
      <select class="UI-select UI-bar-item" type=text name="ActualMinutes" id="minutes" onchange="calculateHours();" title="Actual Minutes Worked, to the nearest 15 minutes">

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
  <div class='UI-bar-item'>
    <label class='UI-label'>Date/Time that the Shift Ended</label>
    <input class="UI-input" type="date" name="EndDate" id="end_date" pattern="\d{4}-\d{2}-\d{2}T\d{2}:\d{2}" value="<?php
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

    <input class="UI-input UI-bar-item" type=number id="end_hours" value=<?php echo $h ?> style="width: 75px;" required onchange="checkHours();" name="EndHours" min=0>
    <select class="UI-select UI-bar-item" type=number name="EndMinutes" id="end_minutes" title="" style="height: 40px;" required onchange="checkHours();" name="EndMinutes">
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
    <select class="UI-select UI-bar-item" type=text name="EndAMPM" id="end_ampm" title="" style="height: 40px;" required onchange="checkHours();" name="EndAMPM">
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

<div class='UI-container'>
  <div class='VOL-enter-block'>
    <label class='UI-label'>Volunteer Time Modifier - Must be per policy</label><br />
    <select class="UI-select" style="width:auto" name="TimeModifier" id="modifier" onchange="calculateHours();">
      <option value=1 selected>Normal - 1 hour = 1 hour</option>
      <option value=0.5>Half Time - 1 hour = 0.5 hours credit</option>
      <option value=1.5>Time Plus Half - 1 hour = 1.5 hours credit</option>
      <option value=2>Double - 1 hour = 2 hours credit</option>
    </select>
  </div>
  <div class='VOL-enter-block'>
    <label class='UI-label'>Department worked</label><br />
    <select class="UI-select" style="width:auto" name="DepartmentWorked" id="department">
        <?php
        $isVolunteers = false;
        foreach ($_SESSION['customFields']['currConComPos'] as $position) {
            if ($position['department'] === 'Volunteers') {
                $isVolunteers = true;
                break;
            }
        }
        if ($isVolunteers || isset($_SESSION['IS_ADMIN']) || kiosk_user()) {
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

<div class='UI-bar'>
  <div class="UI-bar-item UI-center">
  <p name=summery>
      Crediting
      <span id="totalhours">1 hour</span>
      to
      <span id="volunteername">a Volunteer</span>
    </p>
    <p>
      <strong>
        Remember:<br />
        Do Not enter this time on a paper card!
      </strong>
    </p>
  </div>
</div>

<div class='UI-bar'>
  <div class="UI-bar-item">
    <?php
    if (($_SESSION['IS_VOLUNTEERS'] || isset($_SESSION['IS_ADMIN'])) &&
        !kiosk_user()) {
        echo '<label class="VOL-authorized-label">Hours Authorized By</label>';
        echo '<input class="UI-input" type=text';
    } else {
        echo '<input type=hidden';
    }
    if (kiosk_user()) {
        echo ' name="AuthorizedBy" value="'.kiosk_user().'" required>';
        echo '<input type=hidden name="EnteredBy" value="'.kiosk_user().'" required>';
    } else {
        echo ' name="AuthorizedBy" value="'.$_SESSION['accountId'].'" required>';
        echo '<input type=hidden name="EnteredBy" value="'.$_SESSION['accountId'].'" required>';
    }
    ?>
  </div>
</div>

<div class='UI-bar'>
  <div class="UI-bar-item">
    <div class="UI-bar">
      <button id="submitbtn" class="UI-eventbutton VOL-ripple-button" disabled>Submit Hours</button>
      <button class="UI-secondarybutton VOL-ripple-button" type="button" onclick="resetForm()">Clear Form</button>
    </div>
  </div>
</div>

<div class='UI-bar'>
  <div class="UI-bar-item UI-center" style="width:inherit;">
    <p class="VOL-batch-link">
        <a href='javascript:void(0)' onclick='batch()'>batch file upload</a>
    </p>
  </div>
</div>

</form>

<div id='csv_import' class='UI-modal'>
  <div class="UI-modal-content">
    <div class=UI-container">
      <div class="VOL-row">
        <h2 class="UI-center event-color-primary">Volunteer CSV File To Upload</h2>
      </div>
      <div class="VOL-row">
        <p class="UI-margin UI-center">CSV file format:
        <code class="VOL-code-block">&lt;Account,ActualHours,AuthorizedBy,Department,EndDateTime,EnteredBy,Event,TimeModifier&gt;</code></p>
        <p class="UI-margin UI-center"><strong>Account fields</strong> (Account, AuthorizedBy, EnteredBy)  can be badge number, name, or email. However it has to uniquly identify the user so badge number is always the safest.</p>
        <p class="UI-margin UI-center"><strong>Event</strong> is optional. If left empty then the current upcoming event is used.</p>
        <p class="UI-margin UI-center"><strong>EndDateTime</strong> should be quoted. The format is "YYYY-MM-DD HH:MM:SS".</p>
        <h2 class="VOL-enter-block UI-center">Example</h2>
        <table class="UI-stripedtable">
           <tr><td>Attendee Joe,1.000,Connie VonRobot,IT,"2018-06-04 08:15:00",Connie VonRobot,,1.0</tr></td>
           <tr><td>joe@attendee.com,1.000,60055,Gaming,"2018-06-05 08:15:00",Connie VonRobot,,1.0</tr></td>
           <tr><td>60054,1.000,60055,Hotel,"2018-06-06 08:15:00",60055,,1.0</tr></td>
        </table>
      </div>
      <div class="VOL-row">
          <div class="VOL-code-enter">
              <input style="width: 100%;" type="file" id="batch_file" onchange="fileChange()">
          </div>
          <div class="UI-right">
              <button class="UI-redbutton UI-margin" onclick="batchCancel()">Cancel</button>
          </div>
          <div class="UI-right">
              <button class="UI-eventbutton UI-margin UI-disabled" id="batch_import" onclick="batchImport()">Import</button>
          </div>
      </div>
    </div>
  </div>
</div>

<div id='csv_import_report' class='UI-modal'>
  <div class="UI-modal-content">
    <div class=UI-container">
        <div>
            <h2 class="UI-center event-color-primary">Volunteer CSV Import Result</h2>
        </div>
        <div>
            <table class='UI-stripedtable' id="import_report">
            <caption>Report from the CSV Import</caption>
            <th scope="col" id="ReportResult">Result</th>
            </table>
        </div>
        <div class="UI-center">
              <button class="UI-eventbutton UI-margin" id="batch_done" onclick="batchDone()">Done</button>
        </div>
    </div>
  </div>
</div>

</div><!--close main_content div-->
