<header class="w3-cell-row event-color-primary w3-mobile">
  <?php require($PAGESDIR.'/base/logo.inc'); ?>
  <div class="w3-container w3-cell w3-mobile">
    <h2><?php echo $CONSITENAME; ?></h2>
  </div><!--close header text div-->
<?php
if (!empty($EXPIRED)) {
    echo '<meta http-equiv="refresh" content="0; url=/index.php?Function=recovery&expired=1" />';
}
?>
</header><!--close header div-->
<div class="w3-container w3-mobile">
  <div class="w3-container w3-cell w3-third w3-border">
    <h2>Log In</h2>
    
    <?php
    if (!empty($RETRY)) {
        echo '<hr>';
        echo 'Login Incorrect, Please Try Again.';
        echo "<hr>\n";
    }
    ?>

    <form action="index.php?Function=public" method="post" onsubmit="return validate_form(this)" name="Log In" id="login_form" class="w3-container">
      <label class="w3-label">Email Address</label>
      <input name="user" type="text" class="w3-input w3-border">

      <p></p>
      
      <label class="w3-label">Password</label>
      <input name="password" type="password" class="w3-input w3-border">
      
      <p>
        <input type="submit" value="Log In" class="w3-button w3-round-xlarge event-color-primary">
      </p>
      <p>
        <a href='index.php?Function=recovery'>Forgot Password?</a>
      </p>
    </form>
  </div>
  <div class="w3-container w3-cell w3-twothird">
    <p>
      Please sign in using the same email address and password you have used to register for CONvergence in the past.
    </p>
    <p>
      If you have NEVER registered for CONvergence using your email before, <a href='https://www.z2systems.com/np/clients/ce/account.jsp'>click here to create an account in the registration system</a>.  You do not need to register for the convention to create an account in the system.
    </p>
  </div>
</div>
