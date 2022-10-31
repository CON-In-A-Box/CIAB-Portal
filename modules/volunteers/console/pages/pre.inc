<?php

/*.
    require_module 'standard';
.*/

if (!(isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS'])) {
    goSite();
}

$console_type = 'volunteers';
require_once $BASEDIR.'/console/pre.inc';
