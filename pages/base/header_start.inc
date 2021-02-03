<!DOCTYPE html>
<html lang="en" xml:lang="en">
<head>
<title><?php echo $CONSITENAME; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/favicon.png">
<link rel="shortcut icon" href="images/favicon.png">

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
<script defer src="https://use.fontawesome.com/releases/v5.11.2/js/all.js"></script>
<?php
if (!function_exists('get_console') || get_console() === null) {
    echo "<script src=\"sitesupport/common.js\"></script>\n";
    echo "<script src=\"sitesupport/menubar.js\"></script>\n";
    $base = basename($MODULESDIR);
    $modules = scandir($MODULESDIR);
    foreach ($modules as $key => $value) {
        if (!in_array($value, array(".", ".."))) {
            if (in_array($value, $DISABLEDMODULES)) {
                continue;
            }
            if (is_dir($MODULESDIR.DIRECTORY_SEPARATOR.$value) &&
                is_file($MODULESDIR.DIRECTORY_SEPARATOR.$value.DIRECTORY_SEPARATOR.'import.js')) {
                echo "<script src=\"$base/$value/import.js\"></script>\n";
            }
        }
    }
}
