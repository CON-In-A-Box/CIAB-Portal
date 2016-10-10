<div id="dynamic_content">
<div id="left_nav" class="search_screen">
<div id="common_searches" class="search_screen">
Common Searches:
<ul id="left-nav-list">
  <li><a href="index.php?Function=search&searchOn=ALL" title="Print All Log Entries">All Entries</a></li>
</ul>
</div><!--common_searches-->
<a href="index.php?Function=search" title="Restart Search">Restart Search</a>
</div><!--left_nav-->
<div id="main_content" class="log_entry">
<?php
  if (!empty($Results)) {
    require_once($FUNCTIONDIR . '/logsearchs.inc');
    echo "<h1>Search Log Results</h1>";
    if (!empty($Results['ID'])) {
      print_log($Results['ID']);
      $printnum=1;
    } elseif (!empty($Results['text'])) {
      $printnum=do_logtextsearch($Results['text']);
    } elseif (!empty($Results['time'])) {
      $printnum=do_logtimesearch($Results['text']);
    } else {
      $printnum=do_logsearch($Results['tags'], $Results['excl'], $Results['dept'], $Results['active'], $Results['reverse'], FALSE);
    }
    if (empty($printnum)) {
      echo "No search results found.\n";
      echo "<a href='index.php?Function=search' title='Restart Search'>Restart Search</a>";
    }
  } else {
?>
<h1>Search Log Entries</h1>

<form id="search_log_form" name="search_log_form" action="index.php" onsubmit="return validate_form(this)" method="put">
<input type=hidden name=Function value=search>
<table class="search_log" width=100%>
  <tr>
    <td>
      <input type=radio name='searchOn' value='OnText' checked>
    </td>
    <td>
      Search for text in a log entry<br />
      <input size=70 name='log_text_search'>
    </td>
  </tr>
  <tr></tr>
  <tr>
    <td>
      <input type=radio name='searchOn' value='OnID'>
    </td>
    <td>
      Search for Log ID<br />
      <input size=70 name='log_id_search'>
    </td>
  </tr>
  <tr></tr>
  <tr>
    <td>
      <input type=radio name='searchOn' value='ByTime'>
    </td>
    <td>
      Display log entries for the previous <input size=2 name='hours_search'> hours
    </td>
  </tr>
  <tr></tr>
  <tr>
    <td>
      <input type=radio name='searchOn' value='ByTags'>
    </td>
    <td>
      Display log entries with these tags:
    </td>
  </tr>
  <tr>
    <td></td>
    <td wrap>
      <input type="checkbox" name="search_activetag[]" value="1" />Active
      <input type="checkbox" name="search_activetag[]" value="2" />Closed
      <br />
      <?php
        $query="SELECT Tag,Label,SecureView,RoleBasedEdit from $LOGTAGSTABLE";
        $results=run_query($query);
        while ($row=mysql_fetch_assoc($results)) {
          echo "<input type=\"";
          if ($row['SecureView'] == TRUE) { // Show admins extra options
            if (empty($_SESSION['administrator'])) {
              echo "hidden";
            } else {
              echo "checkbox";
            }
          } else {
            echo "checkbox";
          }
          echo "\" name=\"search_tag[]\" value=\"" . $row['Tag'] . "\" />";
          if ($row['SecureView'] == TRUE) {
            if (!empty($_SESSION['administrator'])) {
              echo $row['Label'];
            }
          } else {
            echo $row['Label'];
          }
          echo "\n";
        }
      ?>
    </td>
  </tr>
  <tr></tr>
  <tr>
    <td colspan=2>
      <input type=submit value="Search">
    </td>
  </tr>
</table>
<? } ?>
</div><!--close main_content div-->
</div><!--close dynamic_content div-->
