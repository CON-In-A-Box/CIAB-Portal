<!--<br class="clearfloat" />-->
<div id="main_nav" class="clearfix">
<ul>
 <li><a <? if ($_REQUEST['Function'] == 'main') echo "id=\"current\""; ?> href="index.php?Function=main" title="Main Page">Main</a></li>
 <li><a <?php
   if ($_REQUEST['Function'] == 'signin') {
     echo "id=\"current\""; 
   } else {
     echo "id=\"signin\"";
   } 
 ?>href="index.php?Function=signin" title="Meeting Sign In">Sign In</a></li>
 <li><a href="https://www.z2systems.com/np/clients/ce/login.jsp" title="Register for CONvergence">Register For CON</a></li>
 <li><a <? if ($_REQUEST['Function'] == 'list') echo "id=\"current\""; ?>href="index.php?Function=list" title="The Current ConCom List List">Current ConCom</a></li>
 <li><a href="https://www.z2systems.com/np/clients/ce/login.jsp" title="Update Profile Informaiton In Neon">Profile</a></li>
 <li><a id="logout" href="index.php?Function=public" title="Log out and Restart">Log Out</a></li>
</ul>
</div><!--close main_nav div-->
