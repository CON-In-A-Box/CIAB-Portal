<?php

/*.
    require_module 'standard';
    require_module 'core';
.*/
require __DIR__."/vendor/autoload.php";

if (is_file(__DIR__.'/.env')) {
    $dotenv = Dotenv\Dotenv::create(__DIR__);
    $dotenv->load();
}

// Start the session so we are ready to go no matter what we do!
session_start();

$DISABLEDMODULES = array(); // TODO: Deglobalize

require_once __DIR__.'/functions/locations.inc';

// Load in basic functions
require_once __DIR__.'/functions/functions.inc';
require_once __DIR__.'/console/console.inc';




$configure = false;
try {
    if (isset($dotenv)) {
        $dotenv->required(['DBHOST', 'DBUSER', 'DBNAME', 'DBPASS', 'DB_BACKEND']);
    } else {
        if (!isset($_ENV['DOCKER'])) {
            throw new RuntimeException();
        }
    }
} catch (RuntimeException $e) {
    if (file_exists(__DIR__."/.ht_meetingsignin_config.php")) {
        require_once __DIR__."/.ht_meetingsignin_config.php";
        if (!array_key_exists('DB_BACKEND', $_ENV)) {
            $_ENV['DB_BACKEND'] = $DB_BACKEND;
        }
        if (!array_key_exists('DBHOST', $_ENV)) {
            $_ENV['DBHOST'] = $DBHOST;
        }
        if (!array_key_exists('DBUSER', $_ENV)) {
            $_ENV['DBUSER'] = $DBUSER;
        }
        if (!array_key_exists('DBNAME', $_ENV)) {
            $_ENV['DBNAME'] = $DBNAME;
        }
        if (!array_key_exists('DBPASS', $_ENV)) {
            $_ENV['DBPASS'] = $DBPASS;
        }
    } else {
        header("Location: ".$BASEURL."/configure_system.php");
    }
}

initializeApplication();

// Divert to public page if we are not under function control
if (empty($_REQUEST['Function'])) {
    goSite('/index.php?Function=public');
}

/*. string .*/$target = trim($_REQUEST['Function']);
$path = explode('/', $target);
$module = $path[0];

// Enforce disabled modules
if (in_array($module, $DISABLEDMODULES)) {
    goSite('/index.php?Function=public');
}

// Force console mode to the proper module
$mode = get_console();
if ($mode !== null) {
    if (strcasecmp($target, 'functions') != 0 && !strstr($target, $mode)) {
        goSite('/index.php?Function='.$mode);
    }
}

$noheader = false;
if (!empty($_REQUEST['DeepLink'])) {
    require_once(dirname(__FILE__).'/functions/session.inc');
    if (validateDeepLink($_REQUEST['DeepLink'])) {
        // Allow only approved DeepLink Functions - Need to make this an array search
        if (strcasecmp($target, 'dumplist') == 0) {
            // Dump and Exit - Automation link
            require($PAGESDIR.'/body/dumplist.inc');
            exit();
        } else {
            goSite('/index.php?Function=public');
        }
    } else {
        goSite('/index.php?Function=public');
    }
} elseif (strcasecmp($target, "update") == 0) {
    // Check the update process, doesn't matter if we are logged in or not.
    $noheader = true; // updates don't need statusbars or logmenus
} elseif (strcasecmp($target, "public") == 0) {
    $noheader = true; // public pages don't need statusbars or logmenus
} elseif (strcasecmp($target, "recovery") == 0) {
    $noheader = true; // public pages don't need statusbars or logmenus
} elseif (strcasecmp($target, "create") == 0) {
    $noheader = true; // public pages don't need statusbars or logmenus
} elseif (!array_key_exists('accountId', $_SESSION) || empty($_SESSION['accountId'])) {
    // if no username is set and we are not calling a public page or a deeplink, redirect for login needs
    goSite('/index.php?Function=public');
}

/* Core Pages */
$valid = core_pages();
if (!in_array($module, $valid)) {
    $target = 'main';
}

// Pre-header process  <process_preheader>
if (is_file($PAGESDIR.'/pre/'.$target.'.inc')) {
    require($PAGESDIR.'/pre/'.$target.'.inc');
} elseif (is_file($MODULESDIR.'/'.$target.'/pages/pre.inc')) {
    require($MODULESDIR.'/'.$target.'/pages/pre.inc');
}

// Header <process_header>
require($PAGESDIR.'/base/header_start.inc');
if (is_file($PAGESDIR.'/head/'.$target.'.inc')) {
    require($PAGESDIR.'/head/'.$target.'.inc');
} elseif (is_file($MODULESDIR.'/'.$target.'/pages/head.inc')) {
    require($MODULESDIR.'/'.$target.'/pages/head.inc');
}

/* SCSS processing */
if (is_file($MODULESDIR.'/'.$module.'/scss/styles.scss')) {
    print "<link rel='stylesheet' href='style.php/".$module."/scss/styles.scss'/>";
} elseif (strcasecmp($target, 'main') == 0) {
    print "<link rel='stylesheet' href='style.php/panel.scss'/>";
} else {
    print "<link rel='stylesheet' href='style.php/styles.scss'/>";
}

require($PAGESDIR.'/base/header_end.inc');

// Page Body <process_body>
require($PAGESDIR.'/base/body_begin.inc');

if (empty($noheader) && empty($_REQUEST['NoHeader'])) {
    require($PAGESDIR.'/base/menubar.inc');
}

if (is_file($PAGESDIR.'/body/'.$target.'.inc')) {
    require($PAGESDIR.'/body/'.$target.'.inc');
} elseif (is_file($MODULESDIR.'/'.$target.'/pages/body.inc')) {
    require($MODULESDIR.'/'.$target.'/pages/body.inc');
}

// footer <process_footer>
require($PAGESDIR.'/base/footer.inc');
require($PAGESDIR.'/base/body_end.inc');
