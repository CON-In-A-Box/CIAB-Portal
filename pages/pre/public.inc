<?php
require_once($FUNCTIONDIR."/authentication.inc");
require_once($FUNCTIONDIR."/session.inc");

// Clear any previous login info
LogoutUser();

// Check if there is a login attempt
if (!empty($_REQUEST['user'])) {
    if (LoginUser($_REQUEST['user'], $_REQUEST['password'])) {
        loadAccount($_SESSION['accountId']);
        goSite(); // goto default
    } else {
        $RETRY = 1; // Bad password or not authorized to use system
    }
}
