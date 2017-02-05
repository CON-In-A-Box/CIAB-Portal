<!--<br class="clearfloat" />-->
<div id="main_nav" class="clearfix">
<ul>
 <li><a <?php if ($_REQUEST['Function'] == 'main') echo "id=\"current\" "; ?>href="index.php?Function=main" title="Main Page">Main</a></li>
 <li><a <?php if ($_REQUEST['Function'] == 'register') echo "id=\"current\" "; ?>href="index.php?Function=register" title="Check if I am Registered for CONvergence">Check if Registered</a></li>
 <?php if ( isset( $_SESSION['customFields']['currConComPos'])) { ?>
 <li><a <?php if ($_REQUEST['Function'] == 'list') { echo "id=\"current\""; } else { echo "id=\"list\""; } ?> href="index.php?Function=list" title="The Current ConCom List List">ConCom List</a></li>
 <?php } ?>
 <li><a <?php if ($_REQUEST['Function'] == 'profile') echo "id=\"current\" "; ?>href="index.php?Function=profile" title="Update Profile Informaiton In Neon">Profile</a></li>
 <?php if( $_SESSION['accountId'] == "3218" || $_SESSION['accountId'] == "42966" ) { ?>
 <li><a <?php if ($_REQUEST['Function'] == 'admin') { echo "id=\"current\""; } else {echo "id=\"admin\""; } ?> href="index.php?Function=admin" title="Oh the adminitration">Administration</a></li>
 <?php } ?>
 <li><a id="logout" href="index.php?Function=public" title="Log out and Restart">Log Out</a></li>
</ul>
</div><!--close main_nav div-->
