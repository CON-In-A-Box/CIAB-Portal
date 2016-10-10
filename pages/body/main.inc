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
require_once($FUNCTIONDIR . "/logsearchs.inc");
$count = do_logsearch(1, NULL, NULL, TRUE, FALSE, FALSE); // Active emergencies
if ($count == 0) {
  echo "No active Emergencies.<br>\n";
}

$count = do_logsearch(2, NULL, NULL, TRUE, FALSE, FALSE); // Active medicals
if ($count == 0) {
  echo "No active Medicals.<br>\n";
}

$count = do_logsearch(NULL, 35, NULL, TRUE, FALSE, FALSE); // Other active non-sticky issues
if ($count == 0) {
  echo "No other active issues.<br>\n";
}

$count = do_logsearch(32, NULL, NULL, TRUE, FALSE, FALSE); // Active sticky
if ($count == 0) {
  echo "No current sticky notes.\n";
}
?>
</div><!--close main_content div-->
