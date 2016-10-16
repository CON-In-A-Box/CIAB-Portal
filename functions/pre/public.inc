<?php

// Clear any previous login info
if (isset($_COOKIE[session_name()])) {
  LogoutUser();
}

// Check if there is a login attempt
if (!empty($_REQUEST['user'])) {
  if (LoginUser($_REQUEST['user'],$_REQUEST['password'])) {
      goSite(); // goto default
  } else {
    $RETRY=1; // Bad password or not authorized to use system
  }
}
?>
