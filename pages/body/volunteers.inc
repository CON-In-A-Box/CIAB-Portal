<?php
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
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          var response = JSON.parse(this.responseText);
          var uid = response['Id'];
          document.getElementById('volunteer').classList.remove('w3-red');
          window.location = "index.php?Function=volunteers&volunteerId="+uid;
        } else if (this.readyState == 4 && this.status == 404) {
          document.getElementById('volunteer').classList.add('w3-red');
          document.getElementById('spinner').innerHTML = "";
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
      <input class="w3-input w3-border w3-bar-item" name="VolunteerID_c" id="volunteer" onchange="lookupId(this.value);"
<?php
if (isset($uid)) {
    echo 'value='.$uid.' ';
 }
?>
        placeholder="(badge #, email, Name)" required>
      <button type="button" class="w3-button w3-bar-item w3-border icon-barcode button-scan" onclick="QuaggaApp.init('volunteer', lookupId)"></button>
      <span class='w3-bar-item' id='spinner'></span>
    </div>
  </div>
</div>
</form>

</div>
<div class="w3-container w3-center" style='max-height:500px; overflow:scroll;'>
<table class='w3-table w3-striped w3-bordered w3-white'>
<?php

include_once($FUNCTIONDIR.'/volunteer.inc');
include_once($FUNCTIONDIR.'/users.inc');
$data = get_volunteer_hours_for_user($uid);
$total = 0;
if (!empty($data)) {
    echo "<tr><th>";
    echo implode(array_keys($data[0]), '</th><th>');
    echo "</th></tr>\n";
    foreach ($data as $key => $record) {
        $total += $record['ActualHoursWorked_c'] * $record['TimeModifier_c'];
         echo "<tr><td>";
         echo implode($record, '</td><td>');
         echo "</td></tr>\n";
    }
}
echo "</table>";
echo "</div>";
echo '<div class="w3-container w3-center w3-xlarge w3-green">';
echo "<span>Total Hours : ".$total."</span>";
echo '</div>';
?>
</div>
