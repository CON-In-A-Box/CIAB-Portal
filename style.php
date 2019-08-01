<?php
/*.
    require_module 'standard';
.*/

require_once __DIR__."/vendor/autoload.php";

$scss = new scssc();
$scss->addImportPath("scss");
$scss->setFormatter("scss_formatter_compressed");

$uri = explode("/", $_SERVER['REQUEST_URI']);

$MODULESDIR = "modules";
if (count($uri) > 3) {
    $tgt_path = $MODULESDIR.'/'.$uri[3];
    $scss_cache = "scss/scss_cache/".$uri[3];
    $scss->addImportPath($tgt_path."/scss");
    $scss_dir = "modules";
} elseif ($uri[2] === 'panel.scss') {
    $scss_cache = "scss/scss_cache/panel";
    $source = "@import 'styles';";
    $modules = scandir($MODULESDIR);
    foreach ($modules as $key => $value) {
        if (!in_array($value, array(".", ".."))) {
            $tgt_path = $MODULESDIR.'/'.$value.'/scss';
            if (is_dir($tgt_path)) {
                if (is_file($tgt_path."/panel.scss")) {
                    $source .= "@import \"$tgt_path/panel.scss\";";
                }
            }
        }
    }
    $scss->setFormatter("scss_formatter_compressed");
    header("Content-type: text/css");
    echo $scss->compile($source);
    exit;
} else {
    $scss_cache = "scss/scss_cache";
    $scss_dir = "scss";
}

$server = new scss_server($scss_dir, $scss_cache, $scss);
$server->serve();
