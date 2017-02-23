<header class="w3-cell-row w3-green">
  <?php require($PAGESDIR . '/base/logo.inc'); ?>
  <div id="header text" class="w3-container w3-cell">
    <h2><?php echo $CONSITENAME; ?></h2>
  </div><!--close header text div-->
</header><!--close header div-->
<div class="w3-container w3-mobile">
  <div class="w3-container w3-cell w3-third w3-border">
    <h2>Log In</h2>
    <form action="index.php?Function=public" method="post" onsubmit="return validate_form(this)" name="Log In" id="login_form" class="w3-container">
      <label class="w3-label">User Name or Email</label>
      <input name="user" type="text" class="w3-input">
      
      <label class="w3-label">Password</label>
      <input name="password" type="password" class="w3-input">
      
      <p>
        <input type="submit" value="Log In" class="w3-button w3-round-xlarge w3-green">
      </p>
      <p>
        <a href='https://www.z2systems.com/np/clients/ce/requestPassword.jsp'>Forgot Password?</a>
      </p>
    </form>
  </div>
  <div class="w3-container w3-cell w3-twothird">
    <?php
      if(!empty($RETRY)) {
        echo "<h2>Login Incorrect, Please Try Again.</h2><hr>";
      }
    ?>
    <p>
      Please login - This is the login that you have used to register for the CONvergence Convention.  Even if you are not yet registered for this year's convention, your previous years logins are still active.
    </p>
    <p>
      If you have NEVER registered for the convention, please <a href='https://www.z2systems.com/np/clients/ce/account.jsp'>create a account in the registration system</a>.  You do not need to register for the convention to create an account in the system.
    </p>
  </div>
</div>
