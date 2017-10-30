<script>
  function lookupId(id) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById('volunteername').innerHTML =
        this.responseText;
      } else {
        document.getElementById('volunteername').innerHTML = id + " <i class='fa fa-spinner w3-spin'></i>";
      }
    };
    xhttp.open("GET", "index.php?Function=volhours&volunteerId=" + id, true);
    xhttp.send();


  }

  function calculateHours() {
    var hours = document.getElementById('hours').value * document.getElementById('modifier').value;
    var tag = " hours";
    if (hours == 1) {
      tag = " hour";
    }
    document.getElementById('totalhours').innerHTML = hours + tag;
  }
</script>

<div id="main_content" class="w3-cell w3-cell-top w3-mobile">

<?php
if(isset($updateComplete)) {
  if($updateComplete === TRUE) {
?>
<div class="w3-block w3-center w3-green">
  Hours Recorded
<?php
  } else {
?>
<div class="w3-block w3-center w3-red">
  Hour Recording Failed!!  Error!
<?php
  }
}
?>
</div>

<form class="w3-container" method="post" action="index.php?Function=volhours">

<label>Volunteer Badge Number</label>
<input class="w3-input w3-border" type=number name="VolunteerID_c" id="volunteer" onchange="lookupId(this.value);" <?php
if(isset($_REQUEST['ID'])) {
  echo 'value=' . $_REQUEST['ID'] . ' ';
}
?>placeholder="Volunteer Badge Number (no letters)">

<div class="w3-row">
  <div class="w3-col l3 m3 s4">
    <label>Actual Hours</label>
    <input class="w3-input w3-border" type=text name="ActualHoursWorked_c" id="hours" onchange="calculateHours();" title="Actual Hours Worked, to the quarter hour.  Less than 20!)" pattern="([1-9]|1[0-9])(\.[257](5)?)?" value=1 required>
  </div>
  <div class="w3-col l9 m9 s8">
    <label>End time</label>
    <input class="w3-input w3-border" type="datetime-local" name="EndDateTime_c" id="datetime" pattern="\d{4}-\d{2}-\d{2}T\d{2}:\d{2}" value="<?php
      echo date('Y-m-d');
      echo 'T' . date('H') . ':';
      $min = date('i');
      if($min < 15) {
        echo "00";
      } elseif($min < 30) {
        echo "15";
      } elseif($min < 45) {
        echo "30";
      } else {
        echo "45";
      }
    ?>" required>
  </div>
</div>

<label>Volunteer Time Modifier - Must be per policy</label>
<select class="w3-select w3-border" name="TimeModifier_c" id="modifier" onchange="calculateHours();">
  <option value=1 selected>Normal - 1 hour = 1 hour</option>
  <option value=0.5>Half Time - 1 hour = 0.5 hours credit</option>
  <option value=1.5>Time Plus Half - 1 hour = 1.5 hours credit</option>
  <option value=2>Double - 1 hour = 2 hours credit</option>
</select>

<label>Department worked</label>
<select class="w3-select w3-border" name="DepartmentWorked_c" id="department">
  <?php
  if(strpos($_SESSION['customFields']['currConComPos'][0], 'Volunteers') !== false || isset($_SESSION['IS_ADMIN'])) {
    require_once($FUNCTIONDIR . "/divisional.inc");
    foreach($Departments as $dep => $set) {
      echo '<option value="' . $dep . '"';
      if(isset($_REQUEST['department']) && $_REQUEST['department'] == $dep) {
        echo " selected";
      }
      echo '>' . $dep . "</option>";
    }
  } else {
    $depset = explode('|', $_SESSION['customFields']['currConComPos'][0]);
    foreach($depset as $set) {
      $dep = explode(',', $set);
      echo '<option value="' . $dep[0] . '"';
      if(isset($_REQUEST['department']) && $_REQUEST['department'] == $dep) {
        echo " selected";
      }
      echo '>' . $dep[0] . "</option>";
    }
  }
  ?>
</select>
<div class="w3-container w3-center">

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

  <?php
    if(isset($_SESSION['IS_VOLUNTEERS']) || isset($_SESSION['IS_ADMIN'])) {
      echo '<label class="w3-left">Entering for</label>';
      echo '<input class="w3-input w3-border" type=text';
    } else {
      echo '<input type=hidden';
    }
    echo ' name="AuthorizedBy_c" value="' . $_SESSION['preferredName'] . '" required>';
  ?>
</div>

<div class="w3-bar">
  <button class="w3-bar-item w3-button w3-ripple w3-green" style="width:50%">Submit Hours</button>
  <button class="w3-bar-item w3-button w3-ripple w3-khaki" style="width:50%">Clear Form</button>
</div>
</form>
</div><!--close main_content div-->
