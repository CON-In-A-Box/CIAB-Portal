<?php

/* This should only be accessable from registration staff */
if (class_exists('\\concom\\REGISTRATION') && method_exists('\\concom\\REGISTRATION', 'inRegistration')) {
    $in_registration = concom\REGISTRATION::inRegistration($_SESSION['accountId']);
} else {
    $in_registration = false;
}

if (!isset($_SESSION['IS_ADMIN']) && !$in_registration) {
    goSite();
}
