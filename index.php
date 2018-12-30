<?php

/*.
    require_module 'standard';
.*/

if (!file_exists(__DIR__."/.ht_meetingsignin_config.php")) {
    header("Location: http://".$_SERVER['SERVER_NAME']."/configure_system.php");
}

// Load in basic functions
require_once __DIR__.'/functions/functions.inc';
require_once __DIR__.'/kiosk/kiosk.inc';

// Divert to public page if we are not under function control
if (empty($_REQUEST['Function'])) {
    goSite('/index.php?Function=public');
}

// Enforce disabled modules
$arr = explode('/', trim($_REQUEST['Function']));
if (in_array($arr[0], $DISABLEDMODULES)) {
    goSite('/index.php?Function=public');
}

// Force kiosk mode to the proper module
$mode = get_kiosk();
if ($mode !== null) {
    if ($_REQUEST['Function'] != 'functions' && !strstr($_REQUEST['Function'], $mode)) {
        goSite('/index.php?Function='.$mode);
    }
}

if (!empty($_REQUEST['DeepLink'])) {
    require_once(dirname(__FILE__).'/functions/session.inc');
    if (validateDeepLink($_REQUEST['DeepLink'])) {
        // Allow only approved DeepLink Functions - Need to make this an array search
        if ($_REQUEST['Function'] == 'dumplist') {
            // Dump and Exit - Automation link
            require($PAGESDIR.'/body/'.$_REQUEST['Function'].'.inc');
            exit();
        } else {
            goSite('/index.php?Function=public');
        }
    } else {
        goSite('/index.php?Function=public');
    }
} elseif ($_REQUEST['Function'] == "update") {
    // Check the update process, doesn't matter if we are logged in or not.
    $noheader = true; // updates don't need statusbars or logmenus
} elseif ($_REQUEST['Function'] == "public") {
    $noheader = true; // public pages don't need statusbars or logmenus
} elseif ($_REQUEST['Function'] == "recovery") {
    $noheader = true; // public pages don't need statusbars or logmenus
} elseif (empty($_SESSION['username'])) {
    // if no username is set and we are not calling a public page or a deeplink, redirect for login needs
    goSite('/index.php?Function=public');
}

/* Core Pages */

// Pre-header process  <process_preheader>
if (is_file($PAGESDIR.'/pre/'.$_REQUEST['Function'].'.inc')) {
    require($PAGESDIR.'/pre/'.$_REQUEST['Function'].'.inc');
} elseif (is_file($MODULESDIR.'/'.$_REQUEST['Function'].'/pages/pre.inc')) {
    require($MODULESDIR.'/'.$_REQUEST['Function'].'/pages/pre.inc');
}

// Header <process_header>
require($PAGESDIR.'/base/header_start.inc');
if (is_file($PAGESDIR.'/head/'.$_REQUEST['Function'].'.inc')) {
    require($PAGESDIR.'/head/'.$_REQUEST['Function'].'.inc');
} elseif (is_file($MODULESDIR.'/'.$_REQUEST['Function'].'/pages/head.inc')) {
    require($MODULESDIR.'/'.$_REQUEST['Function'].'/pages/head.inc');
}
require($PAGESDIR.'/base/header_end.inc');

// Page Body <process_body>
require($PAGESDIR.'/base/body_begin.inc');

if (empty($noheader) && empty($_REQUEST['NoHeader'])) {
    require($PAGESDIR.'/base/menubar.inc');
}

if (is_file($PAGESDIR.'/body/'.$_REQUEST['Function'].'.inc')) {
    require($PAGESDIR.'/body/'.$_REQUEST['Function'].'.inc');
} elseif (is_file($MODULESDIR.'/'.$_REQUEST['Function'].'/pages/body.inc')) {
    require($MODULESDIR.'/'.$_REQUEST['Function'].'/pages/body.inc');
}

// footer <process_footer>
require($PAGESDIR.'/base/footer.inc');
require($PAGESDIR.'/base/body_end.inc');
