<div id="dynamic_content" class="main_screen">
<div id="left_nav" class="main_screen">

<?php
  echo "<img src=\"https://www.z2systems.com/neon/resource/ce/images/account/" . $_SESSION['accountId'] . "/0_medium.jpg\">\n";
  echo "<p style=\"font-weight:bold;\">" . $_SESSION['fullName'] . "</p>\n";

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
  echo $_SESSION['city'] . ", ";
  echo $_SESSION['state'] . " ";
  echo $_SESSION['zip'] . "</p>\n";

  echo "<p><span style=\"font-weight:bold;\">Email:</span><br />";
  echo $_SESSION['email'] . "</p>\n";

  echo "<p><span style=\"font-weight:bold;\">Telephone Number:</span><br />";
  echo $_SESSION['phone'] . "</p>\n";
?>

</div><!--left_nav-->
<div id="main_content" style="text-align:center;">
<h2>To check to see if you are registered for CONvergence:</h2>
<img src="images/Neon-MainScreen.png" alt="Main login page" style="border:5px solid black;">
<p>Click the 'Update' button below which will take you to the Neon Login page (above).  Login using the same credentials you used to signin here.</p>
<p>&nbsp;</p>

<img src="images/Neon-Menu.png" alt="Drop down menu" style="border:5px solid black;">
<p>After logging in, you will be at the Constituent Login page.<br />
Use the drop down menu to select 'My Registered Events'</p>
<p>&nbsp;</p>

<img src="images/Neon-EventsRegistered.png" alt="Registered Events" style="border:5px solid black;">
<p>This is a list of all events that you have registered for in Neon.<br />
If you are registered for this year, the top listing will show the current convention year</p>
<p>&nbsp;</p>

<p style="text-align:center;"><a href="https://www.z2systems.com/np/clients/ce/login.jsp"><img src="images/submit_button.png" alt="Go to Neon"></a><br />
Click here to get started!</p>

</div><!--close main_content div-->
