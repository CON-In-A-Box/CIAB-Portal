<!--<br class="clearfloat" />-->
<div id="main_nav" class="clearfix">
<ul>
 <li><a <? if ($_REQUEST['Function'] == 'main') echo "id=\"current\""; ?> href="index.php?Function=main" title="Main Log">Main</a></li>
 <li><a <? if ($_REQUEST['Function'] == 'log') echo "id=\"current\""; ?>href="index.php?Function=log" title="Log Entry">Log Entry</a></li>
 <li><a <? if ($_REQUEST['Function'] == 'lostfound') echo "id=\"current\""; ?>href="index.php?Function=lostfound" title="Lost and Found">Lost/Found</a></li>
 <li><a <? if ($_REQUEST['Function'] == 'messages') echo "id=\"current\""; ?>href="index.php?Function=messages" title="Messages and Phone List">Messages</a></li>
 <li><a <? if ($_REQUEST['Function'] == 'equipment') echo "id=\"current\""; ?>href="index.php?Function=equipment" title="Equipment Checkin and Checkout">Equipment</a></li>
 <li><a <? if ($_REQUEST['Function'] == 'search') echo "id=\"current\""; ?>href="index.php?Function=search" title="Review Log Entries">Search</a></li>
 <? if($_SESSION['administrator']) { ?>
  <li><a <? if ($_REQUEST['Function'] == 'administration') echo "id=\"current\""; ?>href="index.php?Function=administration" title="Site Administration">Administration</a></li>
 <? } ?>
 <li><a href="index.php?Function=public" title="Log out of the application">Log Out</a></li>
</ul>
</div><!--close main_nav div-->
