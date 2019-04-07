<?php
/*.
    require_module 'standard';
.*/

require_once __DIR__."/vendor/autoload.php";

$scss = new scssc();
$scss->addImportPath("scss");
$scss->setFormatter("scss_formatter_compressed");

$uri = explode("/", $_SERVER['REQUEST_URI']);

if (count($uri) > 3) {
    $MODULESDIR = "modules";
    $tgt_path = $MODULESDIR.'/'.$uri[3];
    $scss_cache = "scss/scss_cache/".$uri[3];
    $scss->addImportPath($tgt_path."/scss");
    $scss_dir = "modules";
} else {
    $scss_cache = "scss/scss_cache";
    $scss_dir = "scss";
}

$server = new scss_server($scss_dir, $scss_cache, $scss);
$server->serve();
