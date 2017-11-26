<div id="main_content" class="w3-cell w3-cell-top w3-mobile">

<?php
if (isset($updateComplete)) {
    if ($updateComplete === true) {
?>
<div class="w3-block w3-center w3-green">
  Hours Recorded
<?php
    } else {
?>
<div class="w3-block w3-center w3-red">
  Hour Recording Failed!!  Error!
  <pre>
<?php
print_r($handoff);
echo "</pre>\n";
    }
}
?>
</div>

<script>
  function lookupId(id) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var response = JSON.parse(this.responseText);
        var name = response['First Name']+' '+response['Last Name'];
        var uid = response['Id'];
        if (response['ConCom']) {
          document.getElementById('volunteer').value = "";
          document.getElementById('volunteer').classList.add('w3-red');
          document.getElementById('submitbtn').disabled = true;
          document.getElementById('volunteername').innerHTML = "a Volunteer";
          document.getElementById('lookupname').innerHTML = name + ' is on ConCom (' + uid + ')';
        } else {
          document.getElementById('volunteer').classList.remove('w3-red');
          document.getElementById('submitbtn').disabled = false;
          document.getElementById('volunteername').innerHTML = name + ' (' + uid + ')';
          document.getElementById('lookupname').innerHTML = name;

          var depts = document.getElementById('department');

         /* Clean out old highlighted departments */
          for (i=0; i < depts.length; i++) {
            if (depts[i].text[0] == '*' || depts[i].text[0]=='-') {
                depts[i+1].selected = true;
                depts.remove(i);
                i--;
            }
          }
<?php
if (strpos($_SESSION['customFields']['currConComPos'][0], 'Volunteers') !== false || isset($_SESSION['IS_ADMIN'])) {
    echo <<<EOT
    if ('volunteer' in response) {
      var past = [];
      var option = document.createElement("option");
      option.text = "------------------";
      option.disabled = true;
      depts.add(option, 0);

      for (var i = 0, len = response['volunteer'].length; i < len; i++) {
        var dept = response['volunteer'][i];
        if (past.indexOf(dept['DepartmentWorked_c']) == -1) {
          var option = document.createElement("option");
          option.selected = true;
          option.value= dept['DepartmentWorked_c'];
          option.text = '* '+dept['DepartmentWorked_c'];
          depts.add(option, 0);
          past.push(dept['DepartmentWorked_c']);
        }
      }
    }
EOT;
}
?>
        }
      } else if (this.readyState == 4 && this.status == 404) {
          document.getElementById('volunteer').value = "";
          document.getElementById('volunteer').classList.add('w3-red');
          document.getElementById('submitbtn').disabled = true;
          document.getElementById('volunteername').innerHTML = "a Volunteer";
          document.getElementById('lookupname').innerHTML = id + " not found";
      } else {
        document.getElementById('volunteername').innerHTML = id + " <i class='fa fa-spinner w3-spin'></i>";
      }
    };
    xhttp.open("GET", "index.php?Function=volhours&volunteerId=" + id, true);
    xhttp.send();
  }

  function calculateHours() {
    var hours = Number(document.getElementById('hours').value);
    var am = Number(document.getElementById('minutes').value);
    hours += am;
    var creditHours = hours * document.getElementById('modifier').value;
    var tag = " hours";
    if (hours == 1) {
      tag = " hour";
    }
    document.getElementById('totalhours').innerHTML = creditHours + tag;
    document.getElementById('actualhours').value = Number(hours);
  }
</script>

<form method="post" action="index.php?Function=volhours">

<div class='w3-bar'>
  <div class='w3-bar-item'>
    <label>Volunteer Badge Number or E-Mail</label>
    <div class='w3-bar'>
      <input class="w3-input w3-border w3-bar-item" name="VolunteerID_c" id="volunteer" onchange="lookupId(this.value);" <?php
        if (isset($_REQUEST['ID'])) {
            echo 'value='.$_REQUEST['ID'].' ';
        }
        ?>placeholder="(badge # or email)" required>
      <span class='w3-bar-item' id='lookupname'></span>
    </div>
  </div>
</div>

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
    <input type=hidden name="ActualHoursWorked_c" id="actualhours" value=1>
  </div>
  <div class='w3-bar-item'>
    <label>Date/Time that the Shift Ended</label>
    <input class="w3-input w3-border" type="datetime-local" name="EndDateTime_c" id="datetime" pattern="\d{4}-\d{2}-\d{2}T\d{2}:\d{2}" value="<?php
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
    ?>" required>
  </div>
</div>

<div class='w3-container'>
  <div class='w3-block'>
    <label>Volunteer Time Modifier - Must be per policy</label><br />
    <select class="w3-select w3-border" style="width:auto" name="TimeModifier_c" id="modifier" onchange="calculateHours();">
      <option value=1 selected>Normal - 1 hour = 1 hour</option>
      <option value=0.5>Half Time - 1 hour = 0.5 hours credit</option>
      <option value=1.5>Time Plus Half - 1 hour = 1.5 hours credit</option>
      <option value=2>Double - 1 hour = 2 hours credit</option>
    </select>
  </div>
  <div class='w3-block'>
    <label>Department worked</label><br />
    <select class="w3-select w3-border" style="width:auto" name="DepartmentWorked_c" id="department">
        <?php
        if (strpos($_SESSION['customFields']['currConComPos'][0], 'Volunteers') !== false || isset($_SESSION['IS_ADMIN'])) {
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
    if (isset($_SESSION['IS_VOLUNTEERS']) || isset($_SESSION['IS_ADMIN'])) {
        echo '<label class="w3-left">Hours Authorized By</label>';
        echo '<input class="w3-input w3-border" type=text';
    } else {
        echo '<input type=hidden';
    }
    echo ' name="AuthorizedBy_c" value="'.$_SESSION['preferredName'].'" required>';
    echo '<input type=hidden name="EnteredBy_c" value="'.$_SESSION['preferredName'].'" required>';
    ?>
  </div>
</div>

<div class='w3-bar'>
  <div class="w3-bar-item">
    <div class="w3-bar">
      <button id="submitbtn" class="w3-button w3-ripple w3-green" disabled>Submit Hours</button>
      <button class="w3-button w3-ripple w3-khaki">Clear Form</button>
    </div>
  </div>
</div>

</form>
</div><!--close main_content div-->
