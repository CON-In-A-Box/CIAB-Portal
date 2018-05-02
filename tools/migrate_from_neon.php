<?php

/*.
    require_module 'standard';
.*/

require_once(__DIR__."/../functions/functions.inc");

if (!isset($NEONID)) {
    $add_new = true;
} else {
    $add_new = false;
}

for($i = 0; $i < count($argv)- 1; ++$i) {
    if ($argv[$i] === '--neonid') {
        $NEONID = $argv[$i+1];
        ++$i;
    }

    if ($argv[$i] === '--neonkey') {
        $NEONKEY = $argv[$i+1];
        ++$i;
    }
}

if (!isset($NEONID) || !isset($NEONKEY)) {
    echo "Failed to find or get given proper neonid and neonkey values\n";
    exit();
}

if ($add_new) {
    echo "Adding Neon Keys to configuration\n";
    add_conf_value('NEONID', $NEONID);
    add_conf_value('NEONKEY', $NEONKEY);
}

require_once(__DIR__."/../functions/neon.inc");

/* do work */
require_once(__DIR__."/../functions/update/to_180501.inc");
require_once(__DIR__."/../functions//update/from_neon_to_152.inc");
require_once(__DIR__."/../functions/update.inc");
loadDefinedFields();
_Neon_import_events();
to_180501();

require_once(__DIR__."/sync_neon_members_to_db.php");
require_once(__DIR__."/sync_neon_event_to_db.php");

_build_ConComList();

/* whipout update */
db_do_update(99999999999, 2018050100);

/* pictures ... long */
require_once(__DIR__."/sync_neon_pictures.php");
