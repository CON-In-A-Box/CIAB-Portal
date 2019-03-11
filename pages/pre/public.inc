<?php

/*.
    require_module 'standard';
.*/

require_once($FUNCTIONDIR."/authentication.inc");
require_once($FUNCTIONDIR."/session.inc");

// Clear any previous login info
LogoutUser();

// Check if there is a login attempt
if (!empty($_REQUEST['user'])) {
    $result = LoginUser($_REQUEST['user'], $_REQUEST['password']);
    if ($result === AUTH_SUCCESS) {
        loadAccount($_SESSION['accountId']);
        goSite(); // goto default
    } else {
        $FAILURE = $result;
    }
}
