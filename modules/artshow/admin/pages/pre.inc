<?php

/*.
    require_module 'standard';
.*/

/* This should only be accessable from art show staff */
require_once($BACKEND."/RBAC.inc");

if (!\ciab\RBAC::havePermission("artshow.admin")) {
    // This is for Art Show admins only!  Silently return to main.
    goSite();
}
