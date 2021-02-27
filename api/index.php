<?php

if (!array_key_exists('DISABLEDMODULES', $GLOBALS)) {
    $GLOBALS['DISABLEDMODULES'] = array();
}

require __DIR__.'/src/App/App.php';

error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) {
    if (error_reporting() & $severity) {
        throw new \ErrorException($message, 0, $severity, $file, $line);
    }
});

_config_from_Database();

$app->run();
