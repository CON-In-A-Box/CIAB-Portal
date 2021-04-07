<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;

class DeadlineTest extends CiabTestCase
{

    private $target;

    private $position;

    private $cycle;

    private $event;


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
            ['deadline' => $when,
             'note' => 'testing'],
            201
        );

        $data = $this->runSuccessJsonRequest('GET', '/department/1/deadlines');
        # Find New Index
        $target = null;
        foreach ($data->data as $item) {
            if ($target == null && !in_array($item->id, $initial_ids)) {
                $target = $item->id;
                $this->assertIncludes($item, 'department');
                unset($item->department);
                $this->assertSame([
                    'type' => 'deadline',
                    'id' => $target,
                    'deadline' => "$when",
                    'note' => 'testing'
                ], (array)$item);
            }
        }

        $data = $this->runSuccessJsonRequest('GET', '/deadline/'.$target);
        $this->assertIncludes($data, 'department');
        unset($data->department);
        $this->assertSame([
            'type' => 'deadline',
            'id' => $target,
            'deadline' => "$when",
            'note' => 'testing'
        ], (array)$data);

        $this->target = $target;

        $when = date('Y-m-d', strtotime('+1998 years'));
        $to = date('Y-m-d', strtotime('+2002 years'));
        $this->cycle = $this->runSuccessJsonRequest('POST', '/cycle', null, ['date_from' => $when, 'date_to' => $to], 201);
        $when = date('Y-m-d', strtotime('+1999 years'));
        $to = date('Y-m-d', strtotime('+1999 years'));
        $this->event = $this->runSuccessJsonRequest('POST', '/event', null, ['date_from' => $when, 'date_to' => $to, 'name' => 'PHPTest-a-con'], 201);
        $this->position = $this->runSuccessJsonRequest('POST', '/member/1000/staff_membership', null, ['Department' => '1', 'Position' => '1', 'note' => 'PHPUnit Testing', 'Event' => $this->event->id], 201);

    }


    protected function tearDown(): void
    {
        $this->runRequest('DELETE', '/deadline/'.$this->target, null, null, 204);
        $this->runRequest('DELETE', '/staff_membership/'.$this->position->id, null, null, 204);
        $this->runRequest('DELETE', '/event/'.$this->event->id, null, null, 204);
        $this->runRequest('DELETE', '/cycle/'.$this->cycle->id, null, null, 204);

        parent::tearDown();

    }


    public function testDeadline(): void
    {
        $data = $this->runSuccessJsonRequest(
            'GET',
            '/department/1/deadlines'
        );
        $this->assertNotEmpty($data->data);
        $this->assertIncludes($data->data[0], 'department');


        $data = $this->runSuccessJsonRequest(
            'GET',
            '/member/1000/deadlines'
        );
        $this->assertNotEmpty($data->data);
        $this->assertIncludes($data->data[0], 'department');

        $when = date('Y-m-d', strtotime('+1 year'));
        $this->runRequest(
            'PUT',
            '/deadline/'.$this->target,
            null,
            ['department' => 2,
             'note' => 'New Message',
             'deadline' => "$when" ],
            200
        );

        $data = $this->runSuccessJsonRequest(
            'GET',
            '/deadline/'.$this->target
        );
        $this->assertSame($data->note, 'New Message');
        $this->assertIncludes($data, 'department');

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
            ['department' => -1],
            404
        );

        $this->runRequest(
            'PUT',
            '/deadline/'.$this->target,
            null,
            ['nothing' => -1, 'we' => 1, 'Known' => 2],
            400
        );

        $this->runRequest(
            'PUT',
            '/deadline/'.$this->target,
            null,
            ['deadline' => "not-a-date"],
            400
        );

        $when = date('Y-m-d', strtotime('-1 day'));
        $this->runRequest(
            'PUT',
            '/deadline/'.$this->target,
            null,
            ['deadline' => "$when"],
            400
        );

        $this->runRequest('POST', '/department/1/deadline', null, null, 400);

        $when = date('Y-m-d', strtotime('+1 year'));
        $this->runRequest(
            'POST',
            '/department/-1/deadline',
            null,
            ['deadline' => $when, 'note' => 'testing'],
            404
        );

        $this->runRequest(
            'POST',
            '/department/1/deadline',
            null,
            ['note' => 'testing'],
            400
        );

        $this->runRequest(
            'POST',
            '/department/1/deadline',
            null,
            ['deadline' => "$when"],
            400
        );

        $this->runRequest(
            'POST',
            '/department/1/deadline',
            null,
            ['deadline' => "not-a-date", 'note' => 'testing'],
            400
        );

        $when = date('Y-m-d', strtotime('-1 day'));
        $this->runRequest(
            'POST',
            '/department/1/deadline',
            null,
            ['deadline' => "$when", 'note' => 'testing'],
            400
        );

        $this->runRequest('DELETE', '/deadline/-1', null, null, 404);

    }


    /* End */
}
