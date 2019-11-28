<?php

require_once __DIR__.'/kiosk.inc';

if (!in_kiosk()) {
?>
<div id="kiosk_dlg" class="UI-modal">
  <div class="UI-modal-content">
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
<div id="kiosk_dlg" class="UI-modal">
  <div class="UI-modal-content">
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
<?php
if (!in_kiosk()) {
?>
        var param = 'toggleKiosk=true&kioskUser='+uid;
<?php
} else {
?>
        var param = 'toggleKiosk=false&kioskUser='+uid;
<?php
}
?>
      basicBackendRequest('POST', '<?php echo $kiosk_base;?>/kiosk', param,
        function() {
          window.location = 'index.php?Function=<?php echo $kiosk_target;?>';
      });
    }

    function failKiosk(error) {
      document.getElementById('kiosk_slider').checked = true;
      if (error) {
        alertbox('Login Failed (' + error + ')');
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
