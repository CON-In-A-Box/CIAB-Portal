<?php

namespace App\Tests\TestCase\Controller\Deadline;

use App\Controller\Deadline\GetDeadline;
use PHPUnit\Framework\TestCase;
use App\Tests\Traits\AppTestTrait;
use Slim\Http\Response;

class DeadlineTest extends TestCase
{

    use AppTestTrait;


    protected function addDeadline(): string
    {
        $request = $this->createRequest('GET', '/department/1/deadlines');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);

        $data = json_decode((string)$response->getBody());
        $initial_ids = [];
        foreach ($data->data as $item) {
            $initial_ids[] = $item->id;
        }

        $when = date('Y-m-d', strtotime('+1 month'));
        $request = $this->createRequest('POST', '/deadline/1');
        $request = $request->withParsedBody(['Deadline' => $when,
                                             'Note' => 'testing']);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 201);

        $request = $this->createRequest('GET', '/department/1/deadlines');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);

        $data = json_decode((string)$response->getBody());
        # Find New Index
        $target = null;
        foreach ($data->data as $item) {
            if ($target == null && !in_array($item->id, $initial_ids)) {
                $target = $item->id;
                unset($item->links);
                $this->assertSame([
                    'type' => 'deadline',
                    'id' => $target,
                    'departmentId' => '1',
                    'deadline' => "$when",
                    'note' => 'testing'
                ], (array)$item);
            }
        }

        $request = $this->createRequest('GET', '/deadline/'.$target);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        unset($data->links);
        $this->assertSame([
            'type' => 'deadline',
            'id' => $target,
            'departmentId' => '1',
            'deadline' => "$when",
            'note' => 'testing'
        ], (array)$data);

        return $target;

    }


    public function testDeadline(): void
    {
        $target = $this->addDeadline();

        $request = $this->createRequest(
            'GET',
            '/department/1/deadlines',
            'include=departmentId,postedBy'
        );
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data->data);

        $request = $this->createRequest(
            'GET',
            '/member/1000/deadlines',
            'include=departmentId,postedBy'
        );
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data->data);

        $request = $this->createRequest('PUT', '/deadline/'.$target);
        $when = date('Y-m-d', strtotime('+1 year'));
        $request = $request->withParsedBody([
            'Department' => 2,
            'Note' => 'New Message',
            'Deadline' => "$when"
        ]);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);

        $request = $this->createRequest(
            'GET',
            '/deadline/'.$target,
            'include=departmentId,postedBy'
        );
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertSame($data->note, 'New Message');


        $request = $this->createRequest('DELETE', '/deadline/'.$target);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 204);

    }


    public function testAnnounceErrors(): void
    {
        $target = $this->addDeadline();

        $request = $this->createRequest('GET', '/department/-1/deadlines');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 404);

        $request = $this->createRequest('GET', '/deadline/-1');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 404);

        $request = $this->createRequest('PUT', '/deadline/'.$target);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('PUT', '/deadline/-1');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 404);

        $request = $this->createRequest('PUT', '/deadline/'.$target);
        $request = $request->withParsedBody([
            'Department' => -1,
        ]);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 404);

        $request = $this->createRequest('PUT', '/deadline/'.$target);
        $request = $request->withParsedBody(['Deadline' => "not-a-date"]);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $when = date('Y-m-d', strtotime('-1 day'));
        $request = $this->createRequest('PUT', '/deadline/'.$target);
        $request = $request->withParsedBody(['Deadline' => "$when"]);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('POST', '/deadline/1');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $when = date('Y-m-d', strtotime('+1 year'));
        $request = $this->createRequest('POST', '/deadline/-1');
        $request = $request->withParsedBody(['Deadline' => $when,
                                             'Note' => 'testing']);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 404);

        $request = $this->createRequest('POST', '/deadline/1');
        $request = $request->withParsedBody(['Note' => 'testing']);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('POST', '/deadline/1');
        $request = $request->withParsedBody(['Deadline' => "$when"]);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('POST', '/deadline/1');
        $request = $request->withParsedBody(['Deadline' => "not-a-date",
                                             'Note' => 'testing']);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $when = date('Y-m-d', strtotime('-1 day'));
        $request = $this->createRequest('POST', '/deadline/1');
        $request = $request->withParsedBody(['Deadline' => "$when",
                                             'Note' => 'testing']);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('DELETE', '/deadline/-1');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 404);

        $request = $this->createRequest('DELETE', '/deadline/'.$target);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 204);

    }


    /* End */
}
