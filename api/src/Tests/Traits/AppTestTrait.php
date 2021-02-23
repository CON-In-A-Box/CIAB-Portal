<?php

namespace App\Tests\Traits;

use DI\Container;
use InvalidArgumentException;
use JsonException;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slim\App;
use Slim\Psr7\Factory\ServerRequestFactory;
use UnexpectedValueException;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;

require __DIR__.'/../../App/Routes.php';
require __DIR__.'/../../App/Dependencies.php';

require __DIR__.'/../../../../functions/functions.inc';

class BlankMiddleWare
{


    /* BlankMiddleWare */

    public function __invoke(Request $request, Response $response, $next)
    {
        return $next($request, $response);

    }


    /* End */
}

/**
 * App Test Trait.
 */
trait AppTestTrait
{

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var App
     */
    protected $app;


    /**
     * Bootstrap app.
     *
     * @throws UnexpectedValueException
     *
     * @return void
     */
    protected function setUp(): void
    {
        if (is_file(__DIR__.'/../../../../.env')) {
            $dotenv = \Dotenv\Dotenv::create(__DIR__.'/../../../..');
            $dotenv->load();
        }
        $settings = require __DIR__.'/../../App/Settings.php';
        $this->app = new \Slim\App($settings);
        $this->middleware = new BlankMiddleWare();
        setupAPIDependencies($this->app, $settings);
        setupAPIRoutes($this->app, $this->middleware);

        $container = $this->app->getContainer();
        if ($container === null) {
            throw new UnexpectedValueException('Container must be initialized');
        }

        $this->container = $container;

        $already_loaded = array_key_exists('init', $GLOBALS);
        $GLOBALS['init'] = true;

        $modules = scandir(__DIR__.'/../../Modules');
        foreach ($modules as $key => $value) {
            if (!in_array($value, array(',', '..'))) {
                if (is_dir(__DIR__.'/../../Modules/'.$value)) {
                    if (is_file(__DIR__.'/../../Modules/'.$value.'/Settings.php')) {
                        $module = include(__DIR__.'/../../Modules/'.$value.'/Settings.php');

                        if (!$already_loaded && is_file(__DIR__.'/../../Modules/'.$value.'/App/Dependencies.php')) {
                            include(__DIR__.'/../../Modules/'.$value.'/App/Dependencies.php');
                        }
                        if (!$already_loaded && is_file(__DIR__.'/../../Modules/'.$value.'/App/Routes.php')) {
                            include(__DIR__.'/../../Modules/'.$value.'/App/Routes.php');
                        }

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

    }


    /**
     * Add mock to container.
     *
     * @param string $class The class or interface
     *
     * @return MockObject The mock
     */
    protected function mock(string $class): MockObject
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf('Class not found: %s', $class));
        }

        $mock = $this->getMockBuilder($class)->disableOriginalConstructor()->getMock();

        $this->container->set($class, $mock);

        return $mock;

    }


    /**
     * Create a server request.
     *
     * @param string $method The HTTP method
     * @param string|UriInterface $uri The URI
     * @param array $serverParams The server parameters
     *
     * @return ServerRequestInterface
     */
    protected function createRequest(
        string $method,
        $uri,
        string $serverParams = null
    ): ServerRequestInterface {
        $env = Environment::mock([
            'REQUEST_METHOD' => $method,
            'REQUEST_URI'    => $uri,
            'QUERY_STRING'   => $serverParams
            ]);
        $request = Request::createFromEnvironment($env);
        $request = $request->withAttribute('oauth2-token', ['user_id' => 1000]);
        return $request;

    }


    /**
     * Create a JSON request.
     *
     * @param string $method The HTTP method
     * @param string|UriInterface $uri The URI
     * @param array|null $data The json data
     *
     * @return ServerRequestInterface
     */
    protected function createJsonRequest(
        string $method,
        $uri,
        array $data = null
    ): ServerRequestInterface {
        $request = $this->createRequest($method, $uri);

        if ($data !== null) {
            $request = $request->withParsedBody($data);
        }

        return $request->withHeader('Content-Type', 'application/json');

    }


    /* End */
}
