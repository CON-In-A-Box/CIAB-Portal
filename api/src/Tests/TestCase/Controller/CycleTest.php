<?php

namespace App\Tests\TestCase\Controller\Cycle;

use PHPUnit\Framework\TestCase;
use App\Tests\Traits\AppTestTrait;
use Slim\Http\Response;

class CycleTest extends TestCase
{

    use AppTestTrait;


    protected function addCycle(): string
    {
        $request = $this->createRequest('GET', '/cycle');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);

        $data = json_decode((string)$response->getBody());
        $initial_ids = [];
        foreach ($data->data as $item) {
            $initial_ids[] = $item->id;
        }

        $when = date('Y-m-d', strtotime('-1 month'));
        $to = date('Y-m-d', strtotime('+1 month'));
        $request = $this->createRequest('POST', '/cycle');
        $request = $request->withParsedBody(['From' => "$when",
                                             'To' => "$to"]);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 201);
        $data = json_decode((string)$response->getBody());

        $request = $this->createRequest('GET', '/cycle/'.$data->id);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data2 = json_decode((string)$response->getBody());
        unset($data2->links);
        unset($data->links);
        $this->assertSame((array)$data, (array)$data2);

        return $data->id;

    }


    public function testCycle(): void
    {
        $when = date('Y-m-d', strtotime('-2 month'));
        $to = date('Y-m-d', strtotime('+2 month'));
        $target = $this->addCycle();

        $request = $this->createRequest('GET', '/cycle');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data->data);

        $request = $this->createRequest(
            'GET',
            '/cycle',
            'begin='.$when
        );
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data->data);

        $request = $this->createRequest(
            'GET',
            '/cycle',
            'end='.$to
        );
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data->data);

        $request = $this->createRequest(
            'GET',
            '/cycle',
            'end='.$to.'&begin='.$when
        );
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data->data);

        $request = $this->createRequest('PUT', '/cycle/'.$target);
        $request = $request->withParsedBody([
            'From' => "$when",
            'To' => "$to"]);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);

        $request = $this->createRequest('DELETE', '/cycle/'.$target);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 204);

    }


    public function testCycleErrors(): void
    {
        $target = $this->addCycle();

        $request = $this->createRequest(
            'GET',
            '/cycle',
            'begin=not-a-date'
        );
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest(
            'GET',
            '/cycle',
            'end=not-a-date'
        );
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('GET', '/cycle/-1');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 404);

        $request = $this->createRequest('PUT', '/cycle/'.$target);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('PUT', '/cycle/-1');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 404);

        $request = $this->createRequest('POST', '/cycle');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $when = date('Y-m-d', strtotime('-2 month'));
        $to = date('Y-m-d', strtotime('+2 month'));
        $request = $this->createRequest('POST', '/cycle');
        $request = $request->withParsedBody(['From' => "$when",
                                             'To' => 'not-a-date']);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('POST', '/cycle');
        $request = $request->withParsedBody(['To' => "$to",
                                             'From' => 'not-a-date']);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('POST', '/cycle');
        $request = $request->withParsedBody(['From' => "$when"]);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('POST', '/cycle');
        $request = $request->withParsedBody(['To' => "$to"]);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('DELETE', '/cycle/-1');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 404);

        $request = $this->createRequest('DELETE', '/cycle/'.$target);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 204);

    }


    /* End */
}
