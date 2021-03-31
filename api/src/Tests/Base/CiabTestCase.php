<?php

namespace App\Tests\Base;

use DI\Container;
use PHPUnit\Framework\TestCase;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Environment;
use App\Tests\Base\BlankMiddleWare;
use Chadicus\Slim\OAuth2\Middleware;

if (is_file(__DIR__.'/../../../../.env')) {
    $dotenv = \Dotenv\Dotenv::create(__DIR__.'/../../../..');
    $dotenv->load();
}

require __DIR__.'/../../App/Routes.php';
require __DIR__.'/../../App/Dependencies.php';
require __DIR__.'/../../App/OAuth2.php';

require __DIR__.'/../../../../functions/functions.inc';
require_once __DIR__.'/../../../../backends/oauth2.inc';

abstract class CiabTestCase extends TestCase
{

    /**
     * @var string
     */
    static protected $login = 'allfather@oneeye.com';

    /**
     * @var string
     */
    static protected $password = 'Sleipnir';

    /**
     * @var string
     */
    static protected $client = 'ciab';

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var App
     */
    protected $app;

    /**
     * @var object
     */
    protected $middleware;

    /**
     * @var bool
     */
    protected $setupToken = true;

    /**
     * @var bool
     */
    protected $useOAuth2 = true;

    /**
     * @var object
     */
    protected $token;


    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $already_loaded = array_key_exists('init', $GLOBALS);
        $GLOBALS['init'] = true;

        if (!$already_loaded) {
            $modules = scandir(__DIR__.'/../../Modules');
            foreach ($modules as $key => $value) {
                if (!in_array($value, array(',', '..'))) {
                    if (is_dir(__DIR__.'/../../Modules/'.$value)) {
                        if (is_file(__DIR__.'/../../Modules/'.$value.'/Settings.php')) {
                            if (is_file(__DIR__.'/../../Modules/'.$value.'/App/Dependencies.php')) {
                                include(__DIR__.'/../../Modules/'.$value.'/App/Dependencies.php');
                            }
                            if (is_file(__DIR__.'/../../Modules/'.$value.'/App/Routes.php')) {
                                include(__DIR__.'/../../Modules/'.$value.'/App/Routes.php');
                            }
                        }
                    }
                }
            }
        }

    }


    protected function setUp(): void
    {
        parent::setUp();

        _config_from_Database();

        $settings = require __DIR__.'/../../App/Settings.php';
        $this->app = new \Slim\App($settings);
        setupAPIDependencies($this->app, $settings);

        $container = $this->app->getContainer();
        if ($container === null) {
            throw new UnexpectedValueException('Container must be initialized');
        }

        $this->container = $container;

        if ($this->useOAuth2) {
            $data = setupOAUTH2();
            setupAPIOAuth2($this->app, $data[0]);
            $this->middleware = new Middleware\Authorization($data[0], $this->container);
        } else {
            $this->middleware = new BlankMiddleWare();
        }
        setupAPIRoutes($this->app, $this->middleware);

        $modules = scandir(__DIR__.'/../../Modules');
        foreach ($modules as $key => $value) {
            if (!in_array($value, array(',', '..'))) {
                if (is_dir(__DIR__.'/../../Modules/'.$value)) {
                    if (is_file(__DIR__.'/../../Modules/'.$value.'/Settings.php')) {
                        $module = include(__DIR__.'/../../Modules/'.$value.'/Settings.php');
                        if (array_key_exists('setupRoutes', $module)) {
                            call_user_func($module['setupRoutes'], $this->app, $this->middleware);
                        }
                        if (array_key_exists('setupDependencies', $module)) {
                            call_user_func($module['setupDependencies'], $this->app, $module);
                        }

                        $settings = $container->get('settings');
                        $modules = $settings['modules'];
                        $modules[] = $module['module'];
                        $settings->replace([
                            'modules' => $modules
                        ]);
                    }
                }
            }
        }

        if ($this->setupToken && $this->useOAuth2) {
            $this->token = $this->runSuccessJsonRequest('POST', '/token', null, ['grant_type' => 'password', 'username' => self::$login, 'password' => self::$password, 'client_id' => self::$client]);
        }

    }


    protected function createRequest(
        string $method,
        $uri,
        string $serverParams = null
    ) {
        $env = Environment::mock([
            'REQUEST_METHOD' => $method,
            'REQUEST_URI'    => $uri,
            'QUERY_STRING'   => $serverParams
            ]);
        $request = Request::createFromEnvironment($env);
        if ($this->token) {
            $request = $request->withHeader('Authorization', 'Bearer '.$this->token->access_token);
        }
        return $request;

    }


    protected function runRequest(
        string $method,
        string $uri,
        array $serverParams = null,
        array $body = null,
        int $code = null
    ) {
        if (!empty($serverParams)) {
            $params = [];
            foreach ($serverParams as $key => $value) {
                $params[] = "$key=$value";
            }
            $serverParams = implode('&', $params);
        }
        $request = $this->createRequest($method, $uri, $serverParams);
        if (!empty($body)) {
            $request = $request->withParsedBody($body);
        }
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        if ($code !== null) {
            try {
                $this->assertSame($response->getStatusCode(), $code);
            } catch (\Exception $e) {
                error_log((string)$response->getBody());
                throw($e);
            }
        }
        return $response;

    }


    protected function runSuccessRequest(
        string $method,
        string $uri,
        array $params = null,
        array $body = null,
        int $code = 200
    ) {
        return $this->runRequest($method, $uri, $params, $body, $code);

    }


    protected function runSuccessJsonRequest(
        string $method,
        string $uri,
        array $params = null,
        array $body = null,
        int $code = 200
    ) {
        $response = $this->runRequest($method, $uri, $params, $body, $code);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);
        return $data;

    }


    protected function assertIncludes($data, $id)
    {
        $tid = $data->{$id};
        $this->assertNotEmpty($tid);
        $this->assertIsObject($tid);
        $this->assertObjectHasAttribute('id', $tid);

    }


    /* End */
}
