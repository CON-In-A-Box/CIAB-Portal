<?php

/*.
    require_module 'standard';
.*/

require_once(__DIR__."/../backends/email.inc");
require __DIR__."/../vendor/autoload.php";
require_once(__DIR__."/locations.inc");

// Main setup function
$MODULENAME  = "Member Portal";

// Set a Date stamp for use anywhere
$DS             = uniqid('', true);

if (is_file($BASEDIR.'/.env')) {
    $dotenv = Dotenv\Dotenv::create($BASEDIR);
    $dotenv->load();
}


function getVersion()
{
    global $BASEDIR;
    $version = parse_ini_file($BASEDIR."/version.ini");
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
    global $BASEURL;

    if ($Site != false) {
        header("Location: ".$Site);
    } else {
        header("Location: ".$BASEURL."/index.php?Function=main");
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
try {
    if (isset($dotenv)) {
        $dotenv->required(['DBHOST', 'DBUSER', 'DBNAME', 'DBPASS', 'DB_BACKEND']);
    }

    // Include the Master Database
    require_once(__DIR__."/database.inc");
    $db = new DB;
    _config_from_Database();

    // Include NeonCRM for DB calls via API
    if (array_key_exists('NEONID', $GLOBALS) &&
        array_key_exists('NEONKEY', $GLOBALS) &&
        !empty($GLOBALS['NEONID']) &&
        !empty($GLOBALS['NEONKEY'])) {
        require_once(__DIR__."/neon.inc");
    }

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
} catch (RuntimeException $e) {
    // Give a default config file
    echo '
    <html>
      <head>CON-In-A-Box Site Config ('.$MODULENAME.' module)</head>
    <body>
      <pre>
        SITE CONFIGURATION ERROR -- Database Environment missing or incorrect.';
    print "\n";
    if (array_key_exists('DBPASS', $_ENV)) {
        $_ENV['DBPASS'] = "&lt;&lt;hidden&gt;&gt;";
    }
    print_r($_ENV, false);
    print_r($e, false);
    echo '
      </pre>
    </body>
  </html>';
    die();
}


function getAdminEmail()
{
    return $GLOBALS['ADMINEMAIL'];

}


function getSecurityEmail()
{
    if (array_key_exists('SECURITY_EMAIL', $GLOBALS) &&
        !empty($GLOBALS['SECURITY_EMAIL'])) {
        return $GLOBALS['SECURITY_EMAIL'];
    }
    return getAdminEmail();

}


function getFeedbackEmail()
{
    if (array_key_exists('FEEDBACK_EMAIL', $GLOBALS) &&
        !empty($GLOBALS['FEEDBACK_EMAIL'])) {
        return $GLOBALS['FEEDBACK_EMAIL'];
    }
    return getAdminEmail();

}


function getHelpEmail()
{
    if (array_key_exists('HELP_EMAIL', $GLOBALS) &&
        !empty($GLOBALS['HELP_EMAIL'])) {
        return $GLOBALS['HELP_EMAIL'];
    }
    return getAdminEmail();

}


function getNoReplyAddress()
{
    if (array_key_exists('NOREPLY_EMAIL', $GLOBALS) &&
        !empty($GLOBALS['NOREPLY_EMAIL'])) {
        return $GLOBALS['NOREPLY_EMAIL'];
    }
    return "noreply@".$GLOBALS['CONHOST'].'.com';

}


function sendError($msg, $arrChunk = null)
{
    $to = getAdminEmail();
    $subject = 'SIGNIN ERROR';

    $message = $msg;
    if (!empty($arrChunk)) {
        $message .= "\n\n".print_r($arrChunk, true);
    }
    \ciab\Email::mail($to, getNoReplyAddress(), $subject, $message, $headers);
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


function urlsafe_b64decode($string)
{
    $data = str_replace(array('-','_'), array('+','/'), $string);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    return base64_decode($data);

}


function urlsafe_b64encode($string)
{
    $data = base64_encode($string);
    $data = str_replace(array('+','/','='), array('-','_',''), $data);
    return $data;

}


/*************************/
/* Neon Helper Functions */
/*************************/


function loadDefinedFields()
{
    global $_SESSION, $ADMINACCOUNTS;
    if (!array_key_exists('Neon', $GLOBALS)) {
        return;
    }

    // Custom Field Data for Session - Parse it to an easy to use array
    $request = [
    'method' => 'common/listCustomFields',
    'parameters' => [
    'searchCriteria.component' => "Account",
        ],
        ];
    $result = $GLOBALS['Neon']->go($request);
    if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
        $_SESSION['definedFields']['customField'] = $result['customFields']['customField'];
        foreach ($result['customFields']['customField'] as $val) {
            $_SESSION['definedFields'][$val['fieldId']] = $val['fieldName'];
            if (isset($val['fieldOptions'])) {
                foreach ($val['fieldOptions']['fieldOption'] as $fval) {
                    $_SESSION['definedFields'][$fval['id']] = $fval['name'];
                }
                unset($fval);
                if ($val['fieldName'] == "Lottery Status") {
                    foreach ($val['fieldOptions']['fieldOption'] as $fval) {
                        $_SESSION['definedFields']['Lottery Status'][$fval['id']] = $fval['name'];
                    }
                    unset($fval);
                } elseif ($val['fieldName'] == "Bed Preference") {
                    foreach ($val['fieldOptions']['fieldOption'] as $fval) {
                        $_SESSION['definedFields']['Bed Preference'][$fval['id']] = $fval['name'];
                    }
                    unset($fval);
                } elseif ($val['fieldName'] == "Hotel Qualifier") {
                    foreach ($val['fieldOptions']['fieldOption'] as $fval) {
                        $_SESSION['definedFields']['Hotel Qualifier'][$fval['id']] = $fval['name'];
                    }
                    unset($fval);
                }
            }
        }
        unset($val);
    } else {
        die("Impossible error during Defined Custom Fields Download");
    }

}
