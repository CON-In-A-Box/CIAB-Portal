<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;

class DeadlineTest extends CiabTestCase
{

    private $target;


    protected function setUp(): void
    {
        parent::setUp();
        $data = $this->runSuccessJsonRequest('GET', '/department/1/deadlines');

        $initial_ids = [];
        foreach ($data->data as $item) {
            $initial_ids[] = $item->id;
        }

        $when = date('Y-m-d', strtotime('+1 month'));
        $this->runRequest(
            'POST',
            '/department/1/deadline',
            null,
            ['Deadline' => $when,
             'Note' => 'testing'],
            201
        );

        $data = $this->runSuccessJsonRequest('GET', '/department/1/deadlines');
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

        $data = $this->runSuccessJsonRequest('GET', '/deadline/'.$target);
        unset($data->links);
        $this->assertSame([
            'type' => 'deadline',
            'id' => $target,
            'departmentId' => '1',
            'deadline' => "$when",
            'note' => 'testing'
        ], (array)$data);

        $this->target = $target;

    }


    protected function tearDown(): void
    {
        $this->runRequest('DELETE', '/deadline/'.$this->target, null, null, 204);

    }


    public function testDeadline(): void
    {
        $data = $this->runSuccessJsonRequest(
            'GET',
            '/department/1/deadlines',
            ['include' => 'departmentId,postedBy']
        );
        $this->assertNotEmpty($data->data);

        $data = $this->runSuccessJsonRequest(
            'GET',
            '/member/1000/deadlines',
            ['include' => 'departmentId,postedBy']
        );
        $this->assertNotEmpty($data->data);

        $when = date('Y-m-d', strtotime('+1 year'));
        $this->runRequest(
            'PUT',
            '/deadline/'.$this->target,
            null,
            ['Department' => 2,
             'Note' => 'New Message',
             'Deadline' => "$when" ],
            200
        );

        $data = $this->runSuccessJsonRequest(
            'GET',
            '/deadline/'.$this->target,
            ['include' => 'departmentId,postedBy']
        );
        $this->assertSame($data->note, 'New Message');

    }


    public function testAnnounceErrors(): void
    {
        $this->runRequest('GET', '/department/-1/deadlines', null, null, 404);
        $this->runRequest('GET', '/deadline/-1', null, null, 404);
        $this->runRequest('PUT', '/deadline/'.$this->target, null, null, 400);
        $this->runRequest('PUT', '/deadline/-1', null, null, 404);

        $this->runRequest(
            'PUT',
            '/deadline/'.$this->target,
            null,
            ['Department' => -1],
            404
        );

        $this->runRequest(
            'PUT',
            '/deadline/'.$this->target,
            null,
            ['Deadline' => "not-a-date"],
            400
        );

        $when = date('Y-m-d', strtotime('-1 day'));
        $this->runRequest(
            'PUT',
            '/deadline/'.$this->target,
            null,
            ['Deadline' => "$when"],
            400
        );

        $this->runRequest('POST', '/department/1/deadline', null, null, 400);

        $when = date('Y-m-d', strtotime('+1 year'));
        $this->runRequest(
            'POST',
            '/department/-1/deadline',
            null,
            ['Deadline' => $when, 'Note' => 'testing'],
            404
        );

        $this->runRequest(
            'POST',
            '/department/1/deadline',
            null,
            ['Note' => 'testing'],
            400
        );

        $this->runRequest(
            'POST',
            '/department/1/deadline',
            null,
            ['Deadline' => "$when"],
            400
        );

        $this->runRequest(
            'POST',
            '/department/1/deadline',
            null,
            ['Deadline' => "not-a-date", 'Note' => 'testing'],
            400
        );

        $when = date('Y-m-d', strtotime('-1 day'));
        $this->runRequest(
            'POST',
            '/department/1/deadline',
            null,
            ['Deadline' => "$when", 'Note' => 'testing'],
            400
        );

        $this->runRequest('DELETE', '/deadline/-1', null, null, 404);

    }


    /* End */
}
