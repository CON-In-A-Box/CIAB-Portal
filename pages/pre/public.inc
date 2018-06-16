<?php
require_once($FUNCTIONDIR."/authentication.inc");
require_once($FUNCTIONDIR."/session.inc");

// Clear any previous login info
LogoutUser();

// Check if there is a login attempt
if (!empty($_REQUEST['user'])) {
    $result = LoginUser($_REQUEST['user'], $_REQUEST['password']);
    if ($result === true) {
        loadAccount($_SESSION['accountId']);
        goSite(); // goto default
    } elseif ($result === false) {
        $RETRY = 1; // Bad password or not authorized to use system
    } elseif ($result == null) {
        $EXPIRED = 1;
    } elseif ($result == -1) {
        $LOCKED = 1;
    }
}
