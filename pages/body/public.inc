<div id="header" class="public">
  <? require($PAGESDIR . '/base/logo.inc'); ?>
  <div id="header_text">
    <p><?php echo $CONSITENAME; ?></p>
  </div><!--close header_text div-->
</div><!--close header div-->
<!--<br class="clearfloat" />-->
<div id="dynamic_content">
  <div id="main_content" class="public">
    <div id="login_area">
    <h1>Log In</h1>
      <form action="index.php?Function=public" method="post" onsubmit="return validate_form(this)" name="Log In" id="login_form">
        <p>
          <span class="login_label">User Name or Email</span> <br />
          <input name="user" type="text" maxlength="50" /> <br />
          <span class="login_label">Password</span> <br />
          <input name="password" type="password" maxlength="50" /> <br />
          <input type="submit" value="Log In"/>
       </p>
       <p>
         <a href='https://www.z2systems.com/np/clients/ce/requestPassword.jsp'>Forgot Password?</a>
       </p>
     </form>
   </div><!--closes login_area div-->
   <?php
     if(!empty($RETRY)) {
       echo "<h2>Login Incorrect, Please Try Again.</h2>";
     }
   ?>
   <p>
     Please login - This is your login you have used to register for the CONvergence Convention.  Even if you are not yet registered for this year's convention, your previous years logins are still active.
   </p>
   <p>
     If you have NEVER registered for the convention, please <a href:'https://www.z2systems.com/np/clients/ce/account.jsp'>create a account in the registration system</a>.  You do not need to register for the convention to create an account in the system.
   </p>
   <?php require($PAGESDIR . '/base/disclaimer.inc'); ?>
   </div><!--close main_content div-->
</div><!--close dynamic_content div-->
