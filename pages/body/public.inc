<?php
/*.
    require_module 'standard';
.*/
?>

<header class="UI-page-title-bar">
    <?php require($PAGESDIR.'/base/logo.inc'); ?>
  <div class="UI-page-title">
    <h2><?php echo $CONSITENAME; ?></h2>
  </div><!--close header text div-->
</header><!--close header div-->
<div class="UI-maincontent">
  <div class="UI-login-main-div">
    <h2>Log In</h2>
      <div id="auth_bad" class="UI-center UI-border UI-margin UI-padding UI-red UI-hide">
        <h2 class="UI-error-header">Login Incorrect<br>
          Please Try Again</h2>
        or contact <a href="mailto:<?php echo getHelpEmail(); ?>?subject=Login Issues">
        <?php echo getHelpEmail(); ?></a>
      </div>

      <div id="auth_locked" class="UI-center UI-border UI-margin UI-padding UI-red UI-hide">
        <h2 class="UI-error-header">Account Locked</h2>
        <span class="UI-error-header">Too many unsuccessful login attempts.</span><br>
          <span class="UI-error-header"> Reset the password gain entry</span>
        or contact <a href="mailto:<?php echo getHelpEmail(); ?>?subject=Locked out of account">
            <?php echo getHelpEmail(); ?></a>
      </div>

      <div id='auth_duplicate' class="UI-center UI-border UI-margin UI-padding UI-yellow UI-hide">
        <h2 class="UI-warning-header">Duplicate Accounts Present</h2>
        <div>
          <span class="UI-warning-header">
          This e-mail address has duplicate accounts present.</span><br>
        </div>
        <div class="UI-center UI-border UI-margin UI-padding UI-red">
          <h2 class="UI-error-header">Login Incorrect<br>Please Try Again</h2>
        </div>
      </div>

      <div class='UI-container'>
          <label class="UI-label">Email Address</label>
          <input id='login_user' name="user" type="text" class="UI-input">

          <p></p>

          <label class="UI-label">Password</label>
          <input id='login_password' name="password" type="password" class="UI-input">

          <p>
            <input type="button" id='submit'  value="Log In" class="UI-eventbutton" onclick="loginUser('user', 'password');">
          </p>
          <p>
            <a href='index.php?Function=recovery'>Forgot Password?</a>
          </p>
     </div>
  </div>
  <div class="UI-container UI-cell UI-twothird">
    <p>
      Please sign in using the same email address and password you have used to register for <b><?php echo $CONHOST;?></b> in the past.
    </p>
    <p>
      If you have NEVER registered for <b><?php echo $CONHOST;?></b> using your email before, <a href='index.php?Function=create'>click here to create an account in the registration system</a>.  You do not need to register for the convention to create an account in the system.
    </p>
  </div>
</div>

<script>
document.getElementById("login_password")
  .addEventListener("keyup", function(event) {
  event.preventDefault();
  if (event.keyCode === 13) {
      document.getElementById("submit").click();
  }
});

document.getElementById("login_user")
  .addEventListener("keyup", function(event) {
  event.preventDefault();
  if (event.keyCode === 13) {
    document.getElementById("login_password").focus();
  }
});
</script>
