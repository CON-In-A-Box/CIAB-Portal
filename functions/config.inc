<?php

// Main setup function
$MODULENAME  = "Member Portal";
$BUILD       = "134";
$VERSION     = "1.3 - Captain America, Build: " . $BUILD; // Major.Minor.Revision Format - This is a string, version names allowed

// Discover Self Location
$FUNCTIONDIR    = dirname(__FILE__);
$BASEDIR        = dirname($FUNCTIONDIR);
$WEBSERVER      = $_SERVER['SERVER_NAME'];
$BASEURL        = "http://" . $WEBSERVER;
$SITECONFIG     = $BASEDIR . '/.ht_meetingsignin_config.php'; // Location of the module config file

// Data
$DATADIR        = $BASEDIR . '/data';

// Static Variables
$SITESUPPORTDIR = $BASEDIR . "/sitesupport";
$PAGESDIR       = $BASEDIR . "/pages";

// Set a Date stamp for use anywhere
$DS             = uniqid('', TRUE);

