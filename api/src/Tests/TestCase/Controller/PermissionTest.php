<?php

namespace App\Tests\TestCase\Controller;

use PHPUnit\Framework\TestCase;
use App\Tests\Traits\AppTestTrait;
use Slim\Http\Response;

class PermissionsTest extends TestCase
{

    use AppTestTrait;


    public function testPermissions(): void
    {
        $request = $this->createRequest('GET', '/permissions/resource/deadline/1/get');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);


        $request = $this->createRequest('GET', '/permissions/method/deadline');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);

        $request = $this->createRequest('GET', '/permissions/resource/announcement/1/put');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);


        $request = $this->createRequest('GET', '/permissions/method/announcement');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);

    }


    /* End */
}
