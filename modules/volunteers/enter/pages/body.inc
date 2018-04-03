<div id="main_content" class="w3-cell w3-cell-top w3-mobile">

<?php
if (isset($updateComplete)) {
    if ($updateComplete === true) {
        echo '<div class="w3-block w3-center w3-green">';
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
        success: onSuccess,
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
        <option value=0 selected>:00</option>
        <option value=0.25>:15</option>
        <option value=0.5>:30</option>
        <option value=0.75>:45</option>
      </select>
    </div>
    <input type=hidden name="ActualHoursWorked" id="actualhours" value=1>
  </div>
  <div class='w3-bar-item'>
    <label>Date/Time that the Shift Ended</label>
    <input class="w3-input w3-border" type="datetime-local" name="EndDateTime" id="datetime" pattern="\d{4}-\d{2}-\d{2}T\d{2}:\d{2}" value="<?php
    echo date('Y-m-d');
    echo 'T'.date('H').':';
    $min = date('i');
    if ($min < 15) {
        echo "00";
    } elseif ($min < 30) {
        echo "15";
    } elseif ($min < 45) {
        echo "30";
    } else {
        echo "45";
    }
    ?>" required onchange="checkHours();">
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
        if (strpos($_SESSION['customFields']['currConComPos'][0], 'Volunteers') !== false || isset($_SESSION['IS_ADMIN']) || isset($_SESSION['kioskUser'])) {
            require_once($FUNCTIONDIR."/divisional.inc");
            foreach ($Departments as $dep => $set) {
                echo '<option value="'.$dep.'"';
                if (isset($_REQUEST['department']) && $_REQUEST['department'] == $dep) {
                    echo " selected";
                }
                echo '>'.$dep."</option>\n";
            }
        } else {
            $depset = explode('|', $_SESSION['customFields']['currConComPos'][0]);
            foreach ($depset as $set) {
                $dep = explode(',', $set);
                echo '<option value="'.$dep[0].'"';
                if (isset($_REQUEST['department']) && $_REQUEST['department'] == $dep) {
                    echo " selected";
                }
                echo '>'.$dep[0]."</option>\n";
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
    if ((isset($_SESSION['IS_VOLUNTEERS']) || isset($_SESSION['IS_ADMIN'])) &&
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
      <button id="submitbtn" class="w3-button w3-ripple w3-green" disabled>Submit Hours</button>
      <button class="w3-button w3-ripple w3-khaki" type="button" onclick="resetForm()">Clear Form</button>
    </div>
  </div>
</div>

</form>
</div><!--close main_content div-->
