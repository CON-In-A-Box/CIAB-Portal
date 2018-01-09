<?php

// Main setup function
$MODULENAME  = "Member Portal";
$BUILD       = "152";
$VERSION     = "1.3 - Captain America, Build: ".$BUILD; // Major.Minor.Revision Format - This is a string, version names allowed

// Discover Self Location
$FUNCTIONDIR    = dirname(__FILE__);
$BASEDIR        = dirname($FUNCTIONDIR);
$WEBSERVER      = $_SERVER['SERVER_NAME'];
$BASEURL        = "http://".$WEBSERVER;
$SITECONFIG     = $BASEDIR.'/.ht_meetingsignin_config.php'; // Location of the module config file
$SITECONFIGLEG  = $BASEDIR.'/.ht_meetingsignin_config-legacy.php'; // Location of the module config file - THIS WILL GO AWAY with DB

// Static Variables
$SITESUPPORTDIR = $BASEDIR."/sitesupport";
$PAGESDIR       = $BASEDIR."/pages";
$BACKEND        = $BASEDIR."/backends";
$DATADIR        = $BASEDIR.'/data'; // Really hoping this one disapears into the DB

// Set a Date stamp for use anywhere
$DS             = uniqid('', true);

// Basic site redirector
function goSite($Site = false)
{
    if ($Site != false) {
        header("Location: ".$Site);
    } else {
        header("Location: /index.php?Function=main");
    }
    exit(); // Dump this run, assume the new page will start from scratch

}


// Load Site Specific info
if (is_file($SITECONFIG)) {
    require_once($SITECONFIG);
    // SUPPORT LEGACY UNTIL IN DB
    require_once($SITECONFIGLEG);
} else {
    // Give a default config file
    echo '
    <html>
      <head>CON-In-A-Box Site Config ('.$MODULENAME.' module)</head>
    <body>
      <pre>
        SITE CONFIGURATION ERROR -- '.$SITECONFIG.' missing.
        
	Please see the
	>> '.$SITECONFIG.'-EXAMPLE <<
	config file, update it for your location, and copy/rename it correctly.
        
        Note: This file should have limited permissions to protect your system
        information and passwords.
      </pre>
    </body>
  </html>';
    die();
}


function sendError($msg, $arrChunk = null)
{
    $to = $GLOBALS['ADMINEMAIL'];
    $subject = 'SIGNIN ERROR';

    $message = $msg;
    if (!empty($arrChunk)) {
        $message .= "\n\n".print_r($arrChunk, true);
    }
    $headers = 'From: noreply@convergence-con.org'."\r\n";
    $headers .= 'Reply-To: noreply@convergence-con.org';

    mail($to, $subject, $message, $headers);
    trigger_error($msg);

}


function ksort_recursive(&$array)
{
    if (is_array($array)) {
        ksort($array);
        array_walk($array, 'ksort_recursive');
    }

}


function search_definedFields($key)
{
    if (isset($_SESSION) && array_key_exists('definedFields', $_SESSION)) {
        return array_search($key, $_SESSION['definedFields']);
    }
    return false;

}


// Start the session so we are ready to go no matter what we do!
session_start();

// Include the Master Database
require_once($FUNCTIONDIR."/database.inc");
$db = new DB;

// Include NeonCRM for DB calls via API
require_once($FUNCTIONDIR."/neon.inc");
