<?php

namespace App\Tests\TestCase\Controller\Announcement;

use App\Controller\Announcement\GetAnnouncement;
use PHPUnit\Framework\TestCase;
use App\Tests\Traits\AppTestTrait;
use Slim\Http\Response;

class AnnouncementTest extends TestCase
{

    use AppTestTrait;


    protected function addAnnouncement($scope): string
    {
        $request = $this->createRequest('GET', '/department/1/announcements');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);

        $data = json_decode((string)$response->getBody());
        $initial_ids = [];
        foreach ($data->data as $item) {
            $initial_ids[] = $item->id;
        }

        $request = $this->createRequest('POST', '/announcement/1');
        $request = $request->withParsedBody(['Scope' => $scope,
                                             'Text' => 'testing',
                                             'Email' => 1]);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 201);

        $request = $this->createRequest('GET', '/department/1/announcements');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);

        $data = json_decode((string)$response->getBody());
        # Find New Index
        $target = null;
        foreach ($data->data as $item) {
            if ($target == null && !in_array($item->id, $initial_ids)) {
                $target = $item->id;
                $this->assertSame([
                    'type' => 'announcement',
                    'id' => $target,
                    'departmentId' => '1',
                    'postedOn' => $item->postedOn,
                    'postedBy' => '1000',
                    'scope' => "$scope",
                    'text' => 'testing'
                ], (array)$item);
            }
        }

        $request = $this->createRequest(
            'GET',
            '/announcement/'.$target,
            "fields=type,id,departmentId,postedBy,scope,text"
        );
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertSame([
            'type' => 'announcement',
            'id' => $target,
            'departmentId' => '1',
            'postedBy' => '1000',
            'scope' => "$scope",
            'text' => 'testing'
        ], (array)$data);

        return $target;

    }


    public function testAnnounce(): void
    {
        $target = $this->addAnnouncement(1);

        $request = $this->createRequest(
            'GET',
            '/department/1/announcements',
            'include=departmentId,postedBy'
        );
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data->data);

        $request = $this->createRequest('PUT', '/announcement/'.$target);
        $request = $request->withParsedBody([
            'Department' => 2,
            'Text' => 'New Message',
            'Scope' => 1
        ]);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);

        $request = $this->createRequest(
            'GET',
            '/announcement/'.$target,
            'include=departmentId,postedBy'
        );
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertSame($data->text, 'New Message');

        $request = $this->createRequest(
            'GET',
            '/member/1000/announcements',
            'include=departmentId,postedBy'
        );
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data->data);

        $request = $this->createRequest('DELETE', '/announcement/'.$target);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 204);

    }


    public function testAnnounceErrors(): void
    {
        $target = $this->addAnnouncement(2);

        $request = $this->createRequest(
            'GET',
            '/department/-1/announcements'
        );
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 404);

        $request = $this->createRequest('PUT', '/announcement/'.$target);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('PUT', '/announcement/-1');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 404);

        $request = $this->createRequest('PUT', '/announcement/'.$target);
        $request = $request->withParsedBody([
            'Department' => -1,
        ]);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('POST', '/announcement/1');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('POST', '/announcement/-1');
        $request = $request->withParsedBody(['Scope' => 2,
                                             'Text' => 'testing',
                                             'Email' => 0]);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 404);

        $request = $this->createRequest('POST', '/announcement/1');
        $request = $request->withParsedBody(['Text' => 'testing',
                                             'Email' => 0]);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('POST', '/announcement/1');
        $request = $request->withParsedBody(['Scope' => 2,
                                             'Email' => 0]);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('DELETE', '/announcement/'.$target);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 204);

    }


    /* End */
}
