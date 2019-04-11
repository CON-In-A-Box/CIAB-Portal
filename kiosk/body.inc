<?php

require_once __DIR__.'/kiosk.inc';

if (!in_kiosk()) {
?>
<div id="kiosk_dlg" class="w3-modal">
  <div class="w3-modal-content">
    <div class="UI-container">
      <h2 class="UI-center event-color-primary"><?php echo $kiosk_title;?></h2>
      <hr>
      <h3 class="UI-center">User operating the Kiosk:</h3>
      <div id="userLookup_div" class="UI-center"/>
    </div>
  </div>
</div>
<?php
} else {
?>
<div id="kiosk_dlg" class="w3-modal">
  <div class="w3-modal-content">
    <div class="UI-container">
      <h2 class="UI-center event-color-primary">Switch Kiosk User Mode</h2>
      <hr>
      <h3 class="UI-center">User operating the Kiosk:</h3>
      <div id="userLookup_div" class="UI-center"/>
    </div>
  </div>
</div>
<?php
}
?>

<script>
    function switchKiosk(uid) {
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          window.location = 'index.php?Function=<?php echo $kiosk_target;?>';
        }
      };

      xhttp.open('POST', 'index.php?Function=<?php echo $kiosk_base;?>/kiosk', true);
      xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
<?php
if (!in_kiosk()) {
?>
        xhttp.send('toggleKiosk=true&kioskUser='+uid);
<?php
} else {
?>
        xhttp.send('toggleKiosk=false&kioskUser='+uid);
<?php
}
?>
    }

    function failKiosk(error) {
      document.getElementById('kiosk_slider').checked = true;
      if (error) {
        window.alert('Login Failed (' + error + ')');
      }
    }

    function handleResult(origin, item) {
        var uid = item.Id;
        switchKiosk(uid);
    }

    userLookup.options({
        handler: handleResult,
    });

    document.getElementById('kiosk_dlg').style.display = 'block';
</script>
