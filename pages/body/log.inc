<div id="dynamic_content">
<div id="main_content" class="log_entry">
<h1>Create a new log entry</h1>
<form id="log_entry_form" name="log_entry_form" action="index.php?Function=log" onsubmit="return validate_form(this)" method="post">
<table class="log_entry">
  <caption><? echo $_SESSION['FullName'] . " as " . strtr($_SESSION['position'], "_", " "); ?></caption>
  <tr>
    <td><textarea name="logentry_textarea" rows="15" cols="70"></textarea></td>
    <td>
      <?php
      if (!empty($_REQUEST['UpdateID'])) {
        $query="SELECT Tags,InterestedDepartments,Active FROM $LOGSTABLE WHERE MasterEntryID=" . $_REQUEST['UpdateID'];
        $result=run_query($query);
        $MasterInfo=mysql_fetch_assoc($result);
        echo "<input type=\"hidden\" name=\"UpdateID\" value=\"" . $_REQUEST['UpdateID'] . "\" />\n";
        
        if (!empty($MasterInfo['Active'])) {
          $ActiveTag=TRUE;
        } else {
          $ActiveTag=FALSE;
        }
      } else {
        $ActiveTag=TRUE;
      }
      ?>
      <p>      
        <input type="radio" name="logentry_activetag" value="TRUE" <? if ($ActiveTag) echo "checked"; ?> /> Active
    	  <input type="radio" name="logentry_activetag" value="NULL" <? if (!$ActiveTag) echo "checked"; ?> /> Closed
    	</p>
      <?php
      for ($i = 1; $i < 4; ++$i) {
        $query="SELECT Tag,Label,SecureView,RoleBasedEdit from $LOGTAGSTABLE WHERE DisplayGroup=$i";
        $results=run_query($query);

        echo "<p>\n";
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
            echo "\" name=\"logentry_tag[]\" value=\"" . $row['Tag'] . "\"";

            if (!empty($MasterInfo['Tags']) && $MasterInfo['Tags'] & $row['Tag']) {
              echo " selected"; // This is an update and this tag is selected
              if ($row['RoleBasedEdit'] == TRUE && empty($_SESSION['administrator'])) {
                echo " onclick=\"return lockedTag()\""; // if this is a protected tag and user isn't an admin, refuse to uncheck it
              }
            }

            echo " /> ";
            if ($row['SecureView'] == TRUE) {
              if (!empty($_SESSION['administrator'])) {
                echo $row['Label'] . " <br />";
              }
            } else {
              echo $row['Label'] . " <br />";
            }
            echo "\n";
          }
        echo "</p>\n";
      }
      ?>
    </td>
  </tr>
  <tr>
    <td colspan=2>
      <?php
      $query="SELECT Tag,Department FROM $DEPARTMENTSTABLE WHERE DisplayGroup=1";
      $results=run_query($query);
      while ($row=mysql_fetch_assoc($results)) {
        echo "<input type=\"checkbox\" name=\"logentry_departments[]\" value=\"" . $row['Tag'] . "\"";

        if (!empty($MasterInfo['InterestedDepartments']) && $MasterInfo['InterestedDepartments'] & $row['Tag']) {
          echo " selected"; // This is an update and this tag is selected
          if (empty($_SESSION['administrator'])) {
            echo " onclick=\"return lockedTag()\""; // if user isn't an admin, refuse to uncheck it
          }
        }

        echo " />";
        echo $row['Department'] . "\n";
      }
      ?>
    </td>
  </tr>
  <tr>
    <td colspan=2>
      <?php
      if (!empty($_SESSION['administrator'])) {
        echo "<a id=\"displayOtherDepartments\" href=\"javascript:toggle();\">Select Other Department</a>\n";
      }

      echo "<div id=\"toggleOtherDepartments\" style=\"display: none\">\n";
        $query="SELECT Tag,Department FROM $DEPARTMENTSTABLE WHERE DisplayGroup=2";
        $results=run_query($query);
        while ($row=mysql_fetch_assoc($results)) {
          echo "<input type=\"checkbox\" name=\"logentry_departments[]\" value=\"" . $row['Tag'] . "\"";

          if (!empty($MasterInfo['InterestedDepartments']) && $MasterInfo['InterestedDepartments'] & $row['Tag']) {
            echo " selected"; // This is an update and this tag is selected
          }

          echo " />";
          echo $row['Department'] . "\n";
        }
      echo "</div>\n";
      ?>
    </td>
  </tr>
</table>
<input type="image" src="images/submit_button.png" name="logentry_submit" value="Submit" title="Submit Log Entry" />
<input type=button value="Spell Check" onclick="openSpellChecker();">
</form>
<?php
  if (!empty($_REQUEST['UpdateID'])) {
    require_once($FUNCTIONDIR . '/logsearchs.inc');
    print_log($_REQUEST['UpdateID'], TRUE);
  }
?>
</div><!--close main_content div-->
</div><!--close dynamic_content div-->
