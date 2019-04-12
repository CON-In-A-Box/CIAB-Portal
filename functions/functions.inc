<?php

/*.
    require_module 'standard';
.*/

// Main setup function
$MODULENAME  = "Member Portal";

// Discover Self Location
$FUNCTIONDIR    = dirname(__FILE__);
$BASEDIR        = dirname($FUNCTIONDIR);
$WEBSERVER      = $_SERVER['SERVER_NAME'];
$BASEURL        = "http://".$WEBSERVER;
$SITECONFIG     = $BASEDIR.'/.ht_meetingsignin_config.php'; // Location of the module config file

// Static Variables
$SITESUPPORTDIR = $BASEDIR."/sitesupport";
$PAGESDIR       = $BASEDIR."/pages";
$BACKEND        = $BASEDIR."/backends";
$MODULESDIR     = $BASEDIR."/modules";

// Set a Date stamp for use anywhere
$DS             = uniqid('', true);


function getVersion()
{
    $version = parse_ini_file($BASEDIR."version.ini");
    $tag = "";
    $build = intval(exec('git rev-list HEAD|wc -l'));
    if ($build > 0) {
        $output = exec('git rev-parse --short HEAD', $data, $result);
        if ($result != 0) {
            $rev = "unknown";
        } else {
            $rev = $output;
        }
        $dirty = false;
        exec('git rev-list -n 1 HEAD --not --remotes=origin', $data, $result);
        $dirty = ($data == "0");
        if (!$dirty) {
            exec(
                'git update-index --refresh --unmerged -q >/dev/null',
                $data,
                $result
            );
            $dirty = ($result != 0);
        }
        if (!$dirty) {
            exec(
                'git diff-index --ignore-submodules=untracked --quiet HEAD',
                $data,
                $result
            );
            $dirty = ($result != 0);
        }
        $tag .= ', Tree: '.$rev;
        if ($dirty) {
            $tag .= ' local';
        }
    } else {
        $build = $version['build'];
    }

    $result = $version['major'].'.'.$version['minor'].'.'.$build.'  - '.$version['tag'].$tag;
    return $result;

}


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


function _config_from_Database()
{
    $sql = "SELECT * FROM Configuration;";
    $result = DB::run($sql);
    $value = $result->fetch();
    while ($value !== false) {
        $GLOBALS[$value['Field']] = $value['Value'];
        $value = $result->fetch();
    }

    if (array_key_exists('TIMEZONE', $GLOBALS)) {
        date_default_timezone_set($GLOBALS['TIMEZONE']);
    }

    if (array_key_exists('DISABLEDMODULES', $GLOBALS) &&
        !is_array($GLOBALS['DISABLEDMODULES'])) {
        $GLOBALS['DISABLEDMODULES'] = explode(',', $GLOBALS['DISABLEDMODULES']);
    } else {
        $GLOBALS['DISABLEDMODULES'] = [];
    }

    if (array_key_exists('ADMINACCOUNTS', $GLOBALS) &&
        !is_array($GLOBALS['ADMINACCOUNTS'])) {
        $GLOBALS['ADMINACCOUNTS'] = explode(',', $GLOBALS['ADMINACCOUNTS']);
    }

    if (array_key_exists('CONHOST', $GLOBALS) &&
        !array_key_exists('CONSITENAME', $GLOBALS)) {
        $GLOBALS['CONSITENAME'] = $GLOBALS['CONHOST']." ".$GLOBALS['MODULENAME'];
    }

}


// Load Site Specific info
if (is_file($SITECONFIG)) {
    require_once($SITECONFIG);
    // Start the session so we are ready to go no matter what we do!
    session_start();

    // Include the Master Database
    require_once(__DIR__."/database.inc");
    $db = new DB;
    _config_from_Database();

    // Include NeonCRM for DB calls via API
    require_once(__DIR__."/neon.inc");

    // Load the base of all active modules
    $modules = scandir($MODULESDIR);
    foreach ($modules as $key => $value) {
        if (!in_array($value, array(".", ".."))) {
            if (in_array($value, $DISABLEDMODULES)) {
                continue;
            }
            if (is_dir($MODULESDIR.DIRECTORY_SEPARATOR.$value)) {
                if (is_file($MODULESDIR.DIRECTORY_SEPARATOR.$value.DIRECTORY_SEPARATOR.'init.inc')) {
                    require_once($MODULESDIR.DIRECTORY_SEPARATOR.$value.DIRECTORY_SEPARATOR.'init.inc');
                }
            }
        }
    }
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


function add_conf_value($field, $value)
{
    $sql = <<<SQL
        INSERT INTO `Configuration`(`Field`, `Value`)
        VALUES
            ('$field', '$value');
SQL;
    DB::run($sql);

}


function set_conf_value($field, $value)
{
    $sql = <<<SQL
        UPDATE `Configuration`
        SET `Value`='$value'
        WHERE `Field`='$field';
SQL;
    DB::run($sql);

}


function csv_headers($filename)
{
    header("Content-Type: application/CSV; charset=UTF-8");
    header("Content-Language: en");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    header("Pragma: no-cache");

}


function output_csv($report, $filename)
{
    csv_headers($filename);
    print implode(",", array_keys($report[0]));
    print "\n";
    foreach ($report as $entry) {
        print htmlspecialchars_decode(implode(",", $entry), ENT_QUOTES);
        print "\n";
    }

}
