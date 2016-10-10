<div id="header" class="public">
<? require($PAGESDIR . '/base/logo.inc'); ?>
<div id="header_text">
<p>CONvergence Public Access</p>
</div><!--close header_text div-->
</div><!--close header div-->
<!--<br class="clearfloat" />-->
<div id="dynamic_content">
<div id="main_content" class="public">
<div id="login_area">
<h1>Log In</h1>
<form action="index.php?Function=public" method="post" onsubmit="return validate_form(this)" name="Log In" id="login_form">
<?php
  if(!empty($RETRY)) {
    switch($RETRY) {
      case 1:
        echo "<span class=\"login_error\">Login Incorrect, Please Try Again</span>\n";
        break;
      case 2:
        echo "<span class=\"login_error\">Not authorized for that position.  Please try again</span>\n";
        break;
    }
  }
?>
<p>
<span class="login_label">User Name</span> <br />
<input name="user" type="text" maxlength="50" /> <br />
<span class="login_label">Password</span> <br />
<input name="password" type="password" maxlength="50" /> <br />
<span class="login_label">Position</span><br />
<select name="position" size="1">
  <option>Select Position</option>
  <?php
    require_once($FUNCTIONDIR . '/schedules.inc');
	  $MShift=array_merge($ShiftSet['Staff'], $AppendSet['LogIn']);
    foreach($MShift as $PShift) {
	    echo "<option value='" . $PShift . "'>" . strtr($PShift, "_", " ") . "</option>\n";
	  }
  ?>
</select> <br />
<input type="submit" value="Log In"/>
</p>
</form>
</div><!--closes login_area div-->
<ul class="public">
	<li><a href="index.php?Function=publicdutyboard">Current Duty Board</a></li>
  <li><a href="index.php?Function=publicschedule">Schedule Grid</a></li>
  <li><a href="index.php?Function=publictraining">Training</a></li>
  <li><a href="index.php?Function=publicdocumentation">Documentation &amp; Policies</a></li>
</ul>
<? require($PAGESDIR . '/base/disclaimer.inc'); ?>
</div><!--close main_content div-->
</div><!--close dynamic_content div-->
