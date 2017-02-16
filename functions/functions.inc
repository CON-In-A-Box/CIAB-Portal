<?php

// Main setup function
$MODULENAME  = "Meeting Signin";
$BUILD       = explode(" ", '$Rev: 1 $'); // going to have to update this to support git, probably via filters
$VERSION     = "1.0 Build: " . $BUILD[1] . " - Captain America"; // Major.Minor.Revision Format - This is a string, version names allowed

// Discover Self Location
$FUNCTIONDIR    = dirname(__FILE__);
$BASEDIR        = dirname($FUNCTIONDIR);
$WEBSERVER      = $_SERVER['SERVER_NAME'];
$BASEURL        = "http://" . $WEBSERVER;
$SITECONFIG     = $BASEDIR . '/.ht_meetingsignin_config.php'; // Location of the module config file

// Static Variables
$SITESUPPORTDIR = $BASEDIR . "/sitesupport";
$PAGESDIR       = $BASEDIR . "/pages";

// Set a Date stamp for use anywhere
$DS             = uniqid();

// Basic site redirector
function goSite($Site=FALSE) {
	if($Site != FALSE) {
		header ("Location: " . $Site);
	} else {
		header ("Location: /index.php?Function=main");
	}
	exit(); // Dump this run, assume the new page will start from scratch
}

// Load Site Specific info
if (is_file($SITECONFIG)) {
  require_once($SITECONFIG);
} else {
  // Give a default config file
  echo '
    <html>
      <head>CON-In-A-Box Site Config (' . $MODULENAME . ' module)</head>
    <body>
      <pre>
        SITE CONFIGURATION ERROR -- ' . $SITECONFIG . ' missing.
        
	Please see the
	>> ' . $SITECONFIG .'-EXAMPLE <<
	config file, update it for your location, and copy/rename it correctly.
        
        Note: This file should have limited permissions to protect your system
        information and passwords.
      </pre>
    </body>
  </html>';
  die();
}

// Start the session so we are ready to go no matter what we do!
session_start();

// Call the DB and funtions, we always need these
//require_once($FUNCTIONDIR . "/dbfunctions.inc"); // Not Needed for ConCom Module
// Include NeonCRM for DB calls via API
require_once($FUNCTIONDIR . "/neon.inc");

?>
