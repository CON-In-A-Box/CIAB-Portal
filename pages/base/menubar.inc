<!--<br class="clearfloat" />-->
<div id="main_nav" class="clearfix">
<ul>
 <li><a <? if ($_REQUEST['Function'] == 'main') echo "id=\"current\""; ?> href="index.php?Function=main" title="Main Page">Main</a></li>
 <li><a <? if ($_REQUEST['Function'] == 'signin') echo "id=\"current\""; ?>href="index.php?Function=signin" title="Meeting Sign In">Sign In</a></li>
 <li><a <? if ($_REQUEST['Function'] == 'register') echo "id=\"current\""; ?>href="index.php?Function=register" title="Register for CONvergence">Register</a></li>
 <li><a <? if ($_REQUEST['Function'] == 'update') echo "id=\"current\""; ?>href="index.php?Function=update" title="Update ConCom List List">ConCom</a></li>
 <li><a <? if ($_REQUEST['Function'] == 'profile') echo "id=\"current\""; ?>href="index.php?Function=profile" title="Update Profile Informaiton">Profile</a></li>
 <li><a href="index.php?Function=public" title="Log out and Restart">Log Out</a></li>
</ul>
</div><!--close main_nav div-->
