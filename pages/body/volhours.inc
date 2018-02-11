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

<script>
  var vol_past = null;

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
    checkHours();
  }

  function reset_form() {
    userLookup.clearFailure();
    document.getElementById('submitbtn').disabled = true;
    document.getElementById('volunteername').innerHTML = "a Volunteer";
    document.getElementById('lookupname').innerHTML = "";

     /* Clean out old highlighted departments */
      var depts = document.getElementById('department');
      for (i=0; i < depts.length; i++) {
        if (depts[i].text[0] == '*' || depts[i].text[0]=='-') {
            depts[i+1].selected = true;
            depts.remove(i);
            i--;
        }
      }
    document.getElementById('message').innerHTML = "";
    document.getElementById('datetime').classList.remove('w3-red');
  }

  function format_time(time) {

    var days = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
    if (time.getHours() >= 12) {
        var pmam = ' PM'
    } else {
        var pmam = ' AM'
    }
    var hours = time.getHours()%12;
    if (hours == 0) {
        hours = 12;
    }
    var min = time.getMinutes();
    if (min < 10) {
        min = "0" + min;
    } else {
        min = min.toString();
    }

    return (days[time.getDay()] + ' ' + hours.toString() + ':' + min + pmam);
  }

  function checkHours() {
    document.getElementById('datetime').classList.remove('w3-red');
    document.getElementById('message').innerHTML = "";

    if (vol_past && vol_past.length) {
        var hours = Number(document.getElementById('hours').value);
        var mins  = Number(document.getElementById('minutes').value);
        var new_end = new Date(document.getElementById('datetime').value);
        var new_begin = new Date(document.getElementById('datetime').value);
        new_begin.setHours(new_begin.getHours() - hours);
        new_begin.setMinutes(new_begin.getMinutes() - mins);

        for (i = 0; i < vol_past.length; i++) {
            var _shift = vol_past[i];
            var _end = new Date(_shift['End Date Time']);
            var _begin = new Date(_shift['End Date Time']);
            _begin.setHours(_begin.getHours() - Math.floor(_shift['Actual Hours']));
            _begin.setMinutes(_begin.getMinutes() - Math.floor((_shift['Actual Hours'] % 1) * 60));

            if ((new_begin < _end && new_begin > _begin) ||
                (new_end < _end && new_end > _begin) ||
                (new_end >= _end && new_begin <= _begin)) {
                  document.getElementById('datetime').classList.add('w3-red');
                  document.getElementById('message').innerHTML = 'Overlapping with ' + _shift['Department Worked'] + ' ( ' + format_time(_begin) + ' - ' + format_time(_end) + ' ) ';
                  break;
            }
        }
    }
  }
</script>

<div id="message" class='w3-block w3-center w3-red'></div>

<script>
    function onSuccess(target, resp) {
        document.getElementById('datetime').classList.remove('w3-red');
        document.getElementById('message').innerHTML = "";

        var response = resp[0]
        var name = response['First Name']+' '+response['Last Name'];
        var uid = response['Id'];
        if (response['ConCom']) {
          userLookup.markFailure();
          document.getElementById('submitbtn').disabled = true;
          document.getElementById('volunteername').innerHTML = "a Volunteer";
          document.getElementById('lookupname').innerHTML = name + ' is on ConCom (' + uid + ')';
        } else {
          userLookup.clearFailure();
          userLookup.set(uid);
          document.getElementById('submitbtn').disabled = false;
          document.getElementById('volunteername').innerHTML = name + ' (' + uid + ')';
          document.getElementById('lookupname').innerHTML = name + ' (' + uid + ')';;

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
        if (past.indexOf(dept['Department Worked']) == -1) {
          var option = document.createElement("option");
          option.selected = true;
          option.value= dept['Department Worked'];
          option.text = '* '+dept['Department Worked'];
          depts.add(option, 0);
          past.push(dept['Department Worked']);
        }
      }
    }
EOT;
}
?>
        if ('volunteer' in response) {
            vol_past = response['volunteer'];
            checkHours();
        }
      }
    }

    userLookup.options({
        message: 'Volunteer Badge Number, E-Mail or Full Name',
        memberName: 'VolunteerID',
        success: onSuccess,
        needForm: false,
        lookupTarget: 'index.php?Function=volhours&volunteerId='
        });
</script>
<form method="post" action="index.php?Function=volhours">
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
    echo ' name="AuthorizedBy" value="'.$_SESSION['accountId'].'" required>';
    echo '<input type=hidden name="EnteredBy" value="'.$_SESSION['accountId'].'" required>';
    ?>
  </div>
</div>

<div class='w3-bar'>
  <div class="w3-bar-item">
    <div class="w3-bar">
      <button id="submitbtn" class="w3-button w3-ripple w3-green" disabled>Submit Hours</button>
      <button class="w3-button w3-ripple w3-khaki" type="button" onclick="reset_form()">Clear Form</button>
    </div>
  </div>
</div>

</form>
</div><!--close main_content div-->
