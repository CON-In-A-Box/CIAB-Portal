<?php
  if( $_SESSION['accountId'] != "3218" && $_SESSION['accountId'] != "42966" ) {
    // This is for ConCom members only!  Silently return to main.
    goSite();
  }
?>

