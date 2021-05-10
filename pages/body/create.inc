<script>
    userProfile.options({
        panes: ['emailPrimary', 'name'],
        inlineUpdateButton: false,
        updateButtonText: 'Create Login',
        noTitleBlock: true
    });
</script>

<header class="w3-cell-row event-color-primary w3-mobile">
  <?php require($PAGESDIR.'/base/logo.inc'); ?>
  <div class="w3-container w3-cell w3-mobile">
    <h2><?php echo $CONSITENAME; ?></h2>
  </div>
</header>

<div class="w3-container w3-mobile">
  <div class="w3-container w3-center w3-border w3-margin w3-padding">
    <div>
    <h2 class='event-color-primary'> Account Creation</h2>
    <hr>
    <span class="w3-center w3-bold">This will create an Login for you. It will NOT register you for the event. </span>
    </div>
    <div id="userProfile">
    </div>
    <button class="UI-redbutton UI-margin" onclick="window.history.back();">Back</button>
    </div>
  </div>
</div>
