<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

use Chadicus\Slim\OAuth2\Middleware;

require __DIR__.'/../../../vendor/autoload.php';
require __DIR__.'/../../../functions/functions.inc';
require __DIR__.'/Dependencies.php';
require __DIR__.'/Routes.php';
require __DIR__.'/OAuth2.php';

if (is_file(__DIR__.'/../../../.env')) {
    $dotenv = Dotenv\Dotenv::create(__DIR__.'/../../..');
    $dotenv->load();
}

/* Defines $server as the OAuth2 server */
require_once __DIR__.'/../../../backends/oauth2.inc';

$settings = require __DIR__.'/Settings.php';
$app = new \Slim\App($settings);
$container = $app->getContainer();
setupAPIDependencies($app, $settings);
setupAPIOAuth2($app, $server);
$authMiddleware = new Middleware\Authorization($server, $container);
setupAPIRoutes($app, $authMiddleware);

global $DISABLEDMODULES;

$modules = scandir(__DIR__.'/../Modules');
foreach ($modules as $key => $value) {
    if (!in_array($value, array(',', '..'))) {
        if (in_array($value, $DISABLEDMODULES)) {
            continue;
        }
        if (is_dir(__DIR__.'/../Modules/'.$value)) {
            if (is_file(__DIR__.'/../Modules/'.$value.'/Settings.php')) {
                $module_settings = include(__DIR__.'/../Modules/'.$value.'/Settings.php');
                if (is_file(__DIR__.'/../Modules/'.$value.'/App/Dependencies.php')) {
                    include(__DIR__.'/../Modules/'.$value.'/App/Dependencies.php');
                }
                if (is_file(__DIR__.'/../Modules/'.$value.'/App/Routes.php')) {
                    include(__DIR__.'/../Modules/'.$value.'/App/Routes.php');
                }

                if (array_key_exists('setupRoutes', $module_settings)) {
                    call_user_func($module_settings['setupRoutes'], $app, $authMiddleware);
                }
                if (array_key_exists('setupDependencies', $module_settings)) {
                    call_user_func($module_settings['setupDependencies'], $app, $module_settings);
                }

                $app_settings = $container->get('settings');
                $app_modules = $app_settings['modules'];
                $app_modules[] = $module_settings['module'];
                $app_settings->replace([
                    'modules' => $app_modules
                ]);
            }
        }
    }
}
