<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Info(
 *      title="CIAB backend API",
 *      description="The CIAB RESTful Web API is designed to allow access to the Con In A Box functionality from a variety of web clients as well as more cleanly divide the front end and back ends of the main CIAB web site.",
 *      version="0.1",
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      ),
 *   )
 *
 *   @OA\Server(
 *      description="Sign-in",
 *      url="http://localhost:8080/api"
 *   )
 *
 *   @OA\ExternalDocumentation(
 *      description="View us on GitHub",
 *      url="https://github.com/CON-In-A-Box/CIAB-Portal/tree/master/api/doc"
 *   )
 *
 *   @OA\SecurityScheme(
 *      type="oauth2",
 *      securityScheme="ciab_auth",
 *      description="Authentication to the API server",
 *      @OA\Flow(
 *          flow="password",
 *          tokenUrl="api/token",
 *          refreshUrl="api/token",
 *          scopes={ "" }
 *      )
 *   )
 *
 *   @OA\Response(
 *      response=401,
 *      description="User is not authenticated or not authorized for the API."
 *   )
 **/

use Chadicus\Slim\OAuth2\Middleware;

require __DIR__.'/../../../vendor/autoload.php';

if (is_file(__DIR__.'/../../../.env')) {
    $dotenv = Dotenv\Dotenv::create(__DIR__.'/../../..');
    $dotenv->load();
}

require __DIR__.'/../../../functions/functions.inc';
require_once __DIR__.'/../../../backends/oauth2.inc';
require __DIR__.'/Dependencies.php';
require __DIR__.'/Routes.php';
require __DIR__.'/OAuth2.php';

$settings = require __DIR__.'/Settings.php';
$app = new \Slim\App($settings);
$container = $app->getContainer();
setupAPIDependencies($app, $settings);
$oauth = setupOAUTH2();
$server = $oauth[0];
$storage = $oauth[1];
setupAPIOAuth2($app, $server);
$authMiddleware = new Middleware\Authorization($server, $container);
setupAPIRoutes($app, $authMiddleware);
setupAPICORSRoutes($app, $authMiddleware);

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

setupAPICORSFinalRoute($app, $authMiddleware);
