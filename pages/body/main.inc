<div id="dynamic_content" class="main_screen">
<div id="left_nav" class="main_screen">
<ul id="left-nav-list">
 <li><a href="index.php?Function=dutyboard" title="View the current duty board">Duty Board</a></li>
 <li><a href="index.php?Function=schedule" title="View the schedule of Ops volunteers">Schedule Grid</a></li>
 <li><a href="index.php?Function=stickynotes" title="View all sticky notes">Sticky Notes</a></li>
</li>
</ul>
</div><!--left_nav-->
<div id="main_content">
<?php
  echo "<h2>Welcome " . $_session['fullName'] . " - " . $_session['accountId'] . "!</h2>";
?>
</div><!--close main_content div-->
