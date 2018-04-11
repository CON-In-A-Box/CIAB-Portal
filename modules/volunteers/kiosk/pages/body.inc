<?php
if (!isset($_SESSION['kioskMode'])) {
?>
<div id="kiosk_dlg" class="w3-modal">
  <div class="w3-modal-content">
    <div class="w3-container">
      <h2 class="w3-center event-color-primary">Entering Volunteer Kiosk Mode</h2>
      <hr>
      <h3 class="w3-center">User operating the Kiosk:</h3>
      <div id="userLookup_div" class="w3-center"/>
    </div>
  </div>
</div>
<?php
} else {
?>
<div id="kiosk_dlg" class="w3-modal">
  <div class="w3-modal-content">
    <div class="w3-container">
      <h2 class="w3-center event-color-primary">Switch Kiosk User Mode</h2>
      <hr>
      <h3 class="w3-center">User operating the Kiosk:</h3>
      <div id="userLookup_div" class="w3-center"/>
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
          window.location = 'index.php?Function=volunteers/admin';
        }
      };

      xhttp.open('POST', 'index.php?Function=volunteers/kiosk', true);
      xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
<?php
if (!isset($_SESSION['kioskMode'])) {
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

    function handleResult(origin, uid) {
        switchKiosk(uid);
    }

    userLookup.options({
        handler: handleResult,
    });

    document.getElementById('kiosk_dlg').style.display = 'block';
</script>
