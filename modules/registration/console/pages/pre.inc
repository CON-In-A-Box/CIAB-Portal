<?php

/*.
    require_module 'standard';
.*/

/* This should only be accessable from registration staff */
if (class_exists('\\concom\\REGISTRATION') && method_exists('\\concom\\REGISTRATION', 'inRegistration')) {
    $in_registration = concom\REGISTRATION::inRegistration($_SESSION['accountId']);
} else {
    $in_registration = false;
}

if (!isset($_SESSION['IS_ADMIN']) && !$in_registration) {
    goSite();
}

$console_type = 'registration';
require_once $BASEDIR.'/console/pre.inc';
