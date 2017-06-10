<script>
function lookupId(id) {
  document.getElementById('volunteername').innerHTML = id
}

function calculateHours() {
  document.getElementById('totalhours').innerHTML = document.getElementById('hours').value * document.getElementById('modifier').value
}
</script>

<div id="main_content" class="w3-cell w3-cell-top w3-mobile">

<form class="w3-container" method="post" action="index.php?Function=volhours">

<label>Volunteer Badge Number</label>
<input class="w3-input w3-border" type=text name="volunteer" id="volunteer" onchange="lookupId(this.value);" <?php
if(isset($_REQUEST['ID'])) {
  echo 'value=' . $_REQUEST['ID'] . ' ';
}
?>placeholder="Volunteer Badge Number">

<div class="w3-row">
  <div class="w3-col l3 m3 s4">
    <label>Actual Hours</label>
    <input class="w3-input w3-border" type=number name="hours" id="hours" min=0 max=20 onchange="calculateHours();" title="Actual Hours Worked, to the half hour)" pattern="\d+(\.\d)?" value=1 required>
  </div>
  <div class="w3-col l9 m9 s8">
    <label>End time</label>
    <input class="w3-input w3-border" type="datetime-local" name="datetime" id="datetime" pattern="\d{4}-\d{2}-\d{2}T\d{2}:\d{2}" value="<?php
      echo date('Y-m-d');
      echo 'T' . date('H') . ':';
      $min = date('i');
      if($min < 30) {
        echo "00";
      } else {
        echo "30";
      }
    ?>" required>
  </div>
</div>

<label>Volunteer Time Modifier - Must be per policy</label>
<select class="w3-select w3-border" name="modifier" id="modifier" onchange="calculateHours();">
  <option value=1 selected>Normal - 1 hour = 1 hour</option>
  <option value=0.5>Half Time - 1 hour = 0.5 hours credit</option>
  <option value=1.5>Time Plus Half - 1 hour = 1.5 hours credit</option>
  <option value=2>Double - 1 hour = 2 hours credit</option>
</select>

<label>Department worked</label>
<select class="w3-select w3-border" name="department" id="department">
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
    <span id="totalhours">1</span>
    hour(s) to
    <span id="volunteername">a Volunteer</span>
  </p>
  <p>
    <b>
      Remember:<br />
      Do Not enter this time on a paper card!
    </b>
  </p>

  <?php
    if(strpos($_SESSION['customFields']['currConComPos'][0], 'Volunteers') !== false || isset($_SESSION['IS_ADMIN'])) {
      echo '<label class="w3-left">Entering for</label>';
      echo '<input class="w3-input w3-border" type=text';
    } else {
      echo '<input type=hidden';
    }
    echo ' name="enterer" value="' . $_SESSION['preferredName'] . '" required>';
  ?>
</div>

<button class="w3-button w3-block w3-brown">Submit Hours</button>
</form>
</div><!--close main_content div-->
