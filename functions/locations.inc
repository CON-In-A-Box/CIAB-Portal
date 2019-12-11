<?php
/*.
    require_module 'standard';
.*/

// Discover Self Location
$FUNCTIONDIR    = dirname(__FILE__);
$BASEDIR        = dirname($FUNCTIONDIR);
$WEBSERVER      = $_SERVER['SERVER_NAME'];
$PORT           = '';
$PROTOCOL       = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https' : 'http';
if (array_key_exists('SERVER_PORT', $_SERVER) &&
    $_SERVER['SERVER_PORT'] !== '80' && $_SERVER['SERVER_PORT'] !== '443') {
    $PORT       = ':'.$_SERVER['SERVER_PORT'];
}
$BASEURL        = $PROTOCOL."://".$WEBSERVER.$PORT;

// Static Variables
$SITESUPPORTDIR = $BASEDIR."/sitesupport";
$PAGESDIR       = $BASEDIR."/pages";
$BACKEND        = $BASEDIR."/backends";
$MODULESDIR     = $BASEDIR."/modules";
