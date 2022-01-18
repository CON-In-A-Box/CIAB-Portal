<?php

require_once __DIR__.'/console.inc';

if (!in_console()) {
    ?>
<div id="console_dlg" class="UI-modal">
  <div class="UI-modal-content">
    <div class="UI-container">
      <h2 class="UI-center event-color-primary"><?php echo $console_title;?></h2>
      <hr>
      <h3 class="UI-center">User operating the Console:</h3>
      <div id="userLookup_div" class="UI-center"/>
    </div>
  </div>
</div>
    <?php
} else {
    ?>
<div id="console_dlg" class="UI-modal">
  <div class="UI-modal-content">
    <div class="UI-container">
      <h2 class="UI-center event-color-primary">Switch Console User Mode</h2>
      <hr>
      <h3 class="UI-center">User operating the Console:</h3>
      <div id="userLookup_div" class="UI-center"/>
    </div>
  </div>
</div>
    <?php
}
?>

<script>
    function switchConsole(uid) {
<?php
if (!in_console()) {
    ?>
        var param = 'toggleConsole=true&consoleUser='+uid;
    <?php
} else {
    ?>
        var param = 'toggleConsole=false&consoleUser='+uid;
    <?php
}
?>
      basicBackendRequest('POST', '<?php echo $console_base;?>/console', param,
        function() {
          window.location = 'index.php?Function=<?php echo $console_target;?>';
      });
    }

    function failConsole(error) {
      document.getElementById('console_slider').checked = true;
      if (error) {
        alertbox('Login Failed (' + error + ')');
      }
    }

    function handleResult(origin, item) {
        var uid = item.Id;
        switchConsole(uid);
    }

    userLookup.options({
        handler: handleResult,
    });

    document.getElementById('console_dlg').style.display = 'block';
</script>
