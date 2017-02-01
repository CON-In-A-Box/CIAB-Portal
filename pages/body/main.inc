<div id="dynamic_content" class="main_screen">
<?php require($PAGESDIR . '/base/leftnav.inc'); ?>
<div id="main_content">
<?php
  echo "<h2>Welcome " . $_SESSION['preferredName'] . "!</h2>";

  // Display all, with links to menu items

  echo "<table>\n";
  echo "<tr><th>Current ConCom Status</th></tr>\n";
  if ( isset( $_SESSION['customFields']['currConComPos'])) {
    foreach( $_SESSION['customFields']['currConComPos'] as $val ) {
      $tmp = explode(",", $val);
      echo "<tr><td>" . $tmp[1] . " of " . $tmp[0]. "</td></tr>\n";
    }
    unset($val);
  } else {
    echo "<tr><th>Not currently on ConCom</th></tr>\n";
  }
  echo "</table>\n";
  echo "<p>&nbsp;</p>";
  
  echo "<table>\n";
  echo "<tr><th colspan=3>2017 Meeting attendance</th></tr>\n";
  $nowDate = date("Ymd");
  foreach( $_SESSION['definedFields']['2017Meetings'] as $key => $val ) {
    $ldate = explode("-", $val);
    $checkDate = $ldate[0] . $ldate[1] . rtrim($ldate[2]);
    if ( $nowDate >= $checkDate ) {
      echo "<tr><td>" . $ldate[1] . "/" . rtrim($ldate[2]) . "/" . $ldate[0]. "</td>";
      echo "<td>" . ltrim($ldate[3]) . "</td>";
      if ( isset( $_SESSION['customFields'][array_search('2017 Official Meeting Attendance', $_SESSION['definedFields'])] ) && in_array( $key, $_SESSION['customFields'][array_search('2017 Official Meeting Attendance', $_SESSION['definedFields'])] )) {
        echo "<td style='background-color:green'>Checked In";
      } else {
        if ($nowDate == $checkDate) {
          echo "<td><a href='index.php?Function=signin'>Sign In Now!</a>";
        } else {
          echo "<td>Not Checked In";
        }
      }
      echo "</td></tr>\n";
    }
    unset($ldate);
    unset($checkDate);
  }
  unset($key);
  unset($val);
  unset($nowDate);
  echo "</table>\n";
  echo "<p>&nbsp;</p>";
  
  echo "<table>\n";
  echo "<tr><th colspan=2>Previous ConCom Years Served</th></tr>";
  foreach( $_SESSION['definedFields']['conComYears'] as $key => $val ) {
    echo "<tr><td>" . $val . "</td>";
    if ( isset( $_SESSION['customFields'][array_search('CVGConCom Years Served', $_SESSION['definedFields'])] ) && in_array( $key, $_SESSION['customFields'][array_search('CVGConCom Years Served', $_SESSION['definedFields'])] )) {
      echo "<td style='background-color:green'>On ConCom";
    } else {
      echo "<td>Not On ConCom";
    }
    echo "</td></tr>\n";
  }
  unset($val);
  echo "</table>\n";

// Look up Hotel info - FUTURE
//  echo "<hr>\n";
//  $time = microtime(true);
//  $request = [
//    'method' => 'event/retrieveEventAttendees',
//    'parameters' => [
//      'eventId' => "152",
//      'page.pageSize' => "200",
//    ],
//  ];
//  $result = $GLOBALS['Neon']->go($request);
//  if ( isset( $result['operationResult'] ) && $result['operationResult'] == 'SUCCESS' ) {
//    echo "Done: " . sprintf('%f', $time) . "\n";
//  }
// Look up Registration Status - FUTURE - but possible! :)
$ldate = "2016-09-25"; // date("Y-m-d");
$temp = array_search($ldate, $_SESSION['definedFields']['2017Meetings']);
echo "<p>Found " . $temp . " in dates</p>\n";

echo "<pre>\n" . print_r($_SESSION['definedFields']['2017Meetings']) . "\n</pre>";

?>
</div><!--close main_content div-->
