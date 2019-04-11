<?php

/*.
    require_module 'standard';
.*/

require_once($FUNCTIONDIR."/authentication.inc");
?>

<header class="w3-cell-row event-color-primary w3-mobile">
    <?php require($PAGESDIR.'/base/logo.inc'); ?>
  <div class="UI-container w3-cell w3-mobile">
    <h2><?php echo $CONSITENAME; ?></h2>
  </div><!--close header text div-->
<?php
if (!empty($EXPIRED)) {
    echo '<meta http-equiv="refresh" content="0; url=/index.php?Function=recovery&expired=1" />';
}
?>
</header><!--close header div-->
<div class="UI-container w3-mobile">
  <div class="UI-container w3-cell w3-third w3-border">
    <h2>Log In</h2>
    <?php
    if (!empty($FAILURE)) {
        if ($FAILURE == AUTH_BAD) {
            echo '<div class="UI-center w3-border UI-margin w3-padding UI-red">';
            echo '<h2 class="UI-red w3-bold w3-header">Login Incorrect<br>Please Try Again</h2></div>';
        } elseif ($FAILURE == AUTH_LOCKED) {
            echo '<div class="UI-center w3-border UI-margin w3-padding UI-red">';
            echo '<h2 class="UI-red w3-bold w3-header">Account Locked</h2>';
            echo '<span class="UI-red w3-bold w3-header">';
            echo 'Too many unsuccessful login attempts.</span><br>';
            echo '<span class="UI-red w3-bold w3-header">';
            echo 'Reset the password gain entry.';
            echo "</span></div>\n";
        } elseif ($FAILURE == AUTH_DUPLICATE) {
            echo '<div class="UI-center w3-border UI-margin w3-padding w3-yellow">';
            echo '<h2 class="w3-yellow w3-bold w3-header">Duplicate Accounts Present</h2>';
            echo '<span class="w3-yellowd w3-bold w3-header">';
            echo 'This e-mail address has duplicate accounts present.</span><br>';
            echo '</div>';
            echo '<div class="UI-center w3-border UI-margin w3-padding UI-red">';
            echo '<h2 class="UI-red w3-bold w3-header">Login Incorrect<br>Please Try Again</h2></div>';
        }
    }
    ?>

    <form action="index.php?Function=public" method="post" onsubmit="return validate_form(this)" name="Log In" id="login_form" class="UI-container">
      <label class="w3-label">Email Address</label>
      <input name="user" type="text" class="UI-input">

      <p></p>
      
      <label class="w3-label">Password</label>
      <input name="password" type="password" class="UI-input">
      
      <p>
        <input type="submit" value="Log In" class="UI-eventbutton">
      </p>
      <p>
        <a href='index.php?Function=recovery'>Forgot Password?</a>
      </p>
    </form>
  </div>
  <div class="UI-container w3-cell w3-twothird">
    <p>
      Please sign in using the same email address and password you have used to register for CONvergence in the past.
    </p>
    <p>
      If you have NEVER registered for CONvergence using your email before, <a href='https://www.z2systems.com/np/clients/ce/account.jsp'>click here to create an account in the registration system</a>.  You do not need to register for the convention to create an account in the system.
    </p>
  </div>
</div>
