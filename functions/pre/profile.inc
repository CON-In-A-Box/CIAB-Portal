<?php

// Clear any previous login info
if (isset($_COOKIE[session_name()])) {
  require_once($FUNCTIONDIR . "/authentication.inc");
  LogoutUser();
}

goSite("https://www.z2systems.com/np/clients/ce/login.jsp");

?>
