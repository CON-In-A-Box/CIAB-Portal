<?php
require_once('config.inc');

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

function sendError($msg, $arrChunk=NULL) {
  $to = $GLOBALS['ADMINEMAIL'];
  $subject = 'SIGNIN ERROR';

  $message = $msg;
  if (!empty($arrChunk)) {
    $message .= "\n\n" . print_r($arrChunk, true);
  }
  $headers = 'From: noreply@convergence-con.org' . "\r\n";
  $headers .= 'Reply-To: noreply@convergence-con.org';

  mail($to, $subject, $message, $headers);
  trigger_error($msg);
}

function ksort_recursive(&$array) {
    if (is_array($array)) {
        ksort($array);
        array_walk($array, 'ksort_recursive');
    }
}

// Start the session so we are ready to go no matter what we do!
session_start();

// Include NeonCRM for DB calls via API
require_once($FUNCTIONDIR . "/neon.inc");

?>
