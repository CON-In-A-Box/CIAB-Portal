<div id="header">
<? require($PAGESDIR . '/base/logo.inc'); ?>
<div id="header_text">
<div id="emergency_button">
<a href="index.php?Function=emergency" title="Open a New Emergency Log Entry">Open a New Emergency</a>
</div><!--closes emergency_button div-->
<?php
require_once($FUNCTIONDIR . "/logsearchs.inc");
$RedFiles=count_logsearch(3, NULL, NULL, TRUE); // Emergency or Medical, Active
$BlueFiles=count_logsearch(NULL, 35, NULL, TRUE); // All Other Active (except sticky)
$YellowFiles = 0;
$Age_Watermark = time() - 720; // 12 minutes ago

//$query="SELECT LASTUPDATE FROM " . $LOGSTABLE . " WHERE Type=256"; // Open Page
//$result=run_query($query);

//while ($row=mysql_fetch_array($result)) {  // ##==## Can the DB Do the time compare for us?
  //list($year,$month,$day,$hour,$minute,$second)=split('[-: ]', $row[0]);
  //$Entry_Time=mktime($hour, $minute, $second, $month, $day, $year);
	//if ($Age_Watermark >= $Entry_Time)
	//  $YellowFiles++;
	//}

$COLOR="GREEN";
$ITEMS="";

if ($RedFiles != 0) {
	$COLOR="RED";
	$ITEMS="*" . $RedFiles . " Emergency/Medical* ";
}

if ($YellowFiles != 0) {
  if ($COLOR == "GREEN")
    $COLOR="YELLOW";
  $ITEMS=$ITEMS . "*" . $YellowFiles . " Old Pages* ";
}

if ($BlueFiles != 0) {
  if ($COLOR == "GREEN")
    $COLOR="BLUE";
  $ITEMS=$ITEMS . "=" . $BlueFiles . " Active Issues=";
}

if ($COLOR == "GREEN") {
  require($FUNCTIONDIR . '/fortune.inc'); // can't fortune go into a DB, or www directory?

  $f = new Fortune;
  $Fortune = $f->quoteFromDir($GLOBALS['SITESUPPORTDIR'] . "/fortunedata/");

  while (strlen($Fortune) > 180 ) {
    unset($Fortune);
    $Fortune = $f->quoteFromDir($GLOBALS['SITESUPPORTDIR'] . "/fortunedata/");
  }
  $ITEMS=htmlspecialchars($Fortune);
}
?>
<p class="header_p"><? echo $ITEMS; ?></p>
<p class="header_p"><? echo date('l, F j, H:i'); ?>
  &mdash;
  User: <? echo $_SESSION['FullName'] . " as " . strtr($_SESSION['position'], "_", " "); ?>
</p>
</div><!--close header_text div-->
</div><!--close header div-->
