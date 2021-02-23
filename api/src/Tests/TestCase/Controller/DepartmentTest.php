<?php

namespace App\Tests\TestCase\Controller\Department;

use PHPUnit\Framework\TestCase;
use App\Tests\Traits\AppTestTrait;
use Slim\Http\Response;

class DepartmentTest extends TestCase
{

    use AppTestTrait;


    public function testDepartment(): void
    {
        $request = $this->createRequest('GET', '/department');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data->data);

        $request = $this->createRequest('GET', '/department/Systems');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);

        $request = $this->createRequest('GET', '/department/1');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);

        $request = $this->createRequest('GET', '/department', 'include=id');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);

        $request = $this->createRequest(
            'GET',
            '/department/100',
            'include=division,fallback'
        );
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);

    }


    public function testDepartmentErrors(): void
    {
        $request = $this->createRequest('GET', '/department/-1');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 404);

        $request = $this->createRequest('GET', '/department/not-a-dept');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 404);

    }


    /* End */
}
