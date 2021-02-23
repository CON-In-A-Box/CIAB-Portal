<?php

namespace App\Tests\TestCase\Controller\Event;

use PHPUnit\Framework\TestCase;
use App\Tests\Traits\AppTestTrait;
use Slim\Http\Response;

class EventTest extends TestCase
{

    use AppTestTrait;


    public function testEvent(): void
    {
        $when = date('Y-m-d', strtotime('-20 years'));
        $to = date('Y-m-d', strtotime('+20 years'));

        $request = $this->createRequest('GET', '/event');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data->data);

        $request = $this->createRequest(
            'GET',
            '/event',
            'begin='.$when
        );
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data->data);

        $request = $this->createRequest(
            'GET',
            '/event',
            'end='.$to
        );
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data->data);

        $request = $this->createRequest(
            'GET',
            '/event',
            'end='.$to.'&begin='.$when
        );
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data->data);

        $id = $data->data[0]->id;
        $request = $this->createRequest('GET', '/event/'.$id);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $cycleid = $data->cycle;

        $request = $this->createRequest(
            'GET',
            '/event/'.$id,
            'include=cycle'
        );
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertSame($data->cycle->id, $cycleid);

    }


    public function testEventErrors(): void
    {
        $request = $this->createRequest(
            'GET',
            '/event',
            'begin=not-a-date'
        );
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest(
            'GET',
            '/event',
            'end=not-a-date'
        );
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('GET', '/event/-1');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 404);

    }


    /* End */
}
