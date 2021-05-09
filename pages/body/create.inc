<script>
    userProfile.options({
        title: 'Account Creation',
        panes: ['name', 'prefName', 'emailPrimary'],
        inlineUpdateButton: false,
        updateButtonText: 'Create Account'
    });
</script>

<header class="w3-cell-row event-color-primary w3-mobile">
  <?php require($PAGESDIR.'/base/logo.inc'); ?>
  <div class="UI-page-title w3-container w3-cell w3-mobile">
    <h2><?php echo $CONSITENAME; ?></h2>
  </div>
</header>

<div class="w3-container w3-mobile">
  <div class="w3-container w3-center w3-border w3-margin w3-padding">
    <div>
    <h2 class='event-color-primary'> Account Creation</h2>
    <hr>
    <span class="w3-center w3-bold">If you would like to create a new account, fill out the following form and you will be e-mailed a temporary password. <br>You can then use that e-mail address and the password to log in.</span>
    </div>
    <div id="userProfile">
    </div>
    <button class="UI-redbutton UI-margin" onclick="window.history.back();">Back</button>
    </div>
  </div>
</div>
