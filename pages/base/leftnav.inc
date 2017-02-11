<div id="left_nav" class="main_screen">

<?php
  echo "<img src=\"https://www.z2systems.com/neon/resource/ce/images/account/" . $_SESSION['accountId'] . "/0_medium.jpg\" onerror=\"this.src='http://probablyprogramming.com/wp-content/uploads/2009/03/handtinyblack.gif'\">\n";
  echo "<p style=\"font-weight:bold;\">" . $_SESSION['fullName'] . "</p>\n";

  if ( isset( $_SESSION['addr1'] ) && !empty( $_SESSION['addr1'] )) {
    echo "<p><span style=\"font-weight:bold;\">Address:</span><br />\n";
    echo $_SESSION['addr1'] . "<br />\n";
    
    if ( isset( $_SESSION['addr2'] ) && !empty( $_SESSION['addr2'] )) {
      echo $_SESSION['addr2'] . "<br />\n";
    }
    if ( isset( $_SESSION['addr3'] ) && !empty( $_SESSION['addr3'] )) {
      echo $_SESSION['addr3'] . "<br />\n";
    }
    if ( isset( $_SESSION['addr4'] ) && !empty( $_SESSION['addr4'] )) {
      echo $_SESSION['addr4'] . "<br />\n";
    }
    if ( isset( $_SESSION['city'] ) && !empty( $_SESSION['city'] )) {
      echo $_SESSION['city'] . ", ";
      echo $_SESSION['state'] . " ";
      echo $_SESSION['zip'] . "</p>\n";
    }
  }

  if ( isset( $_SESSION['email'] ) && !empty( $_SESSION['email'] )) {
    echo "<p><span style=\"font-weight:bold;\">Email:</span><br />";
    echo $_SESSION['email'] . "</p>\n";
  }

  if ( isset( $_SESSION['phone'] ) && !empty( $_SESSION['phone'] )) {
    echo "<p><span style=\"font-weight:bold;\">Telephone Number:</span><br />";
    echo $_SESSION['phone'] . "</p>\n";
  }
  
  if($_REQUEST['Function'] == 'list') {
    require_once($FUNCTIONDIR . "/divisional.inc");

    echo "<div id=\"left_list\"><!-- ConCom Left Nav List -->\n";
    echo "<h3>Jump to Section</h3>\n";
    echo "<ul>\n";
    foreach($Divisions as $div) {
      echo "<li><a href=\"#" . $div . "\">" . $div . "</a></li>\n";
    }
    echo "</ul>\n";
    echo "</div><!-- End ConCom Left Nav List -->\n";
  }

?>

</div><!--left_nav-->

