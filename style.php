<?php
/*.
    require_module 'standard';
.*/

require_once __DIR__."/vendor/autoload.php";
require_once __DIR__."/functions/functions.inc";


function from_db($input)
{
    $color = $input[0][2][0];
    $sql = "SELECT `Value` FROM `Configuration` where `Field` = 'col.$color'";
    $result = \DB::run($sql);
    $value = $result->fetch();
    if ($value !== false) {
        return $value['Value'];
    }

    switch ($color) {
        case 'primary':
            return '#fff';
        case 'prim-back':
            return '#4CAF50';
        case 'secondary':
            return '#fff';
        case 'second-back':
            return '#2196F3';
        default:
            return '';
    }

}


$resource_cache = @sys_get_temp_dir().'/scss_cache/';

$scss = new scssc();
$scss->addImportPath("scss");
$scss->setFormatter("scss_formatter_compressed");

$scss->registerFunction('db-color', 'from_db');

$uri = explode("/", $_SERVER['REQUEST_URI']);

if (!is_dir($resource_cache)) {
    @mkdir($resource_cache, 0777, true);
    @chmod($resource_cache, 0777);
}

$MODULESDIR = "modules";
if (count($uri) > 3) {
    $tgt_path = $MODULESDIR.'/'.$uri[3];
    $scss_cache = $resource_cache.$uri[3];
    $scss->addImportPath($tgt_path."/scss");
    $scss_dir = "modules";
} elseif ($uri[2] === 'panel.scss') {
    $scss_cache = $resource_cache."panel";
    $source = "@import 'styles';";
    $modules = @scandir($MODULESDIR);
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
    $scss_cache = $resource_cache;
    $scss_dir = "scss";
}

$server = new scss_server($scss_dir, $scss_cache, $scss);
$server->serve();
