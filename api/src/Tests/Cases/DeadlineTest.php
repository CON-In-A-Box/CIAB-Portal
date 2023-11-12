<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;

class DeadlineTest extends CiabTestCase
{

    private $target;

    private $position;


    protected function setUp(): void
    {
        parent::setUp();
        $data = $this->runSuccessJsonRequest('GET', '/department/2/deadlines');

        $initial_ids = [];
        foreach ($data->data as $item) {
            $initial_ids[] = $item->id;
        }

        $when = date('Y-m-d', strtotime('+1 month'));
        $this->runRequest(
            'POST',
            '/department/2/deadline',
            null,
            ['deadline' => $when,
             'note' => 'testing'],
            201
        );

        $data = $this->runSuccessJsonRequest('GET', '/department/2/deadlines');
        # Find New Index
        $target = null;
        foreach ($data->data as $item) {
            if ($target == null && !in_array($item->id, $initial_ids)) {
                $target = $item->id;
                $this->assertIncludes($item, 'department');
                $this->assertEquals($item->posted_by->id, '1000');
                $this->assertEquals($item->department->id, '2');
                unset($item->department);
                unset($item->posted_by);
                $this->assertSame([
                    'type' => 'deadline',
                    'id' => $target,
                    'deadline' => "$when",
                    'note' => 'testing',
                    'scope' => '2'
                ], (array)$item);
            }
        }

        $data = $this->runSuccessJsonRequest('GET', '/deadline/'.$target);
        $this->assertIncludes($data, 'department');
        $this->assertEquals($data->posted_by->id, '1000');
        $this->assertEquals($data->department->id, '2');
        unset($data->department);
        unset($data->posted_by);
        $this->assertSame([
            'type' => 'deadline',
            'id' => $target,
            'deadline' => "$when",
            'note' => 'testing',
            'scope' => '2'
        ], (array)$data);

        $this->target = $target;

        $id = $this->testing_accounts[0];
        $this->position = $this->runSuccessJsonRequest('POST', "/member/$id/staff_membership", null, ['Department' => '2', 'Position' => '3', 'Note' => 'PHPUnit Testing'], 201);

    }


    protected function tearDown(): void
    {
        $this->runRequest('DELETE', '/deadline/'.$this->target, null, null, 204);
        $this->runRequest('DELETE', '/staff/membership/'.$this->position->id, null, null, 204);

        parent::tearDown();

    }


    public function testDeadline(): void
    {
        $data = $this->runSuccessJsonRequest(
            'GET',
            '/department/2/deadlines'
        );
        $this->assertNotEmpty($data->data);
        $this->assertIncludes($data->data[0], 'department');


        $data = $this->runSuccessJsonRequest(
            'GET',
            '/deadline'
        );
        $this->assertNotEmpty($data->data);
        $this->assertIncludes($data->data[0], 'department');

        $when = date('Y-m-d', strtotime('+1 year'));
        $this->runRequest(
            'PUT',
            '/deadline/'.$this->target,
            null,
            ['department' => 3,
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


    public function provider(): array
    {
        return [
            /* Loki, concom member, same department */
        [2, 0, 0, true],
        [2, 1, 0, true],
        [2, 2, 0, true],
            /* Loki, concom member, other department*/
        [4, 0, 0, true],
        [4, 1, 0, true],
        [4, 2, 0, false],
            /* Frigga , normal member */
        [2, 0, 1, true],
        [2, 1, 1, false],
        [2, 2, 1, false],
            /* Thor, Admin member, other department */
        [4, 0, null, true],
        [5, 1, null, true],
        [6, 2, null, true],
        ];

    }


    /**
     * @test
     * @dataProvider provider
     **/
    public function testDeadlineScope($department, $scope, $account, $result): void
    {
        if ($account === null) {
            $id = 1000;
        } else {
            $id = $this->testing_accounts[$account];
        }
        $this->runRequest(
            'PUT',
            '/deadline/'.$this->target,
            null,
            ['department' => $department,
             'scope' => $scope ],
            200
        );


        /* check member access */

        if ($account === null) {
            $data = $this->RunSuccessJsonRequest(
                'GET',
                "/deadline"
            );
        } else {
            $data = $this->NPRunSuccessJsonRequest(
                'GET',
                "/deadline",
                null,
                null,
                200,
                $account
            );
        }
        $found = false;
        foreach ($data->data as $entry) {
            if ($entry->id == $this->target) {
                $found = true;
            }
        }
        if ($result) {
            $this->assertTrue($found);
            $this->assertNotEmpty($data->data);
        } else {
            $this->assertFalse($found);
        }

        /* check department access */

        if ($account === null) {
            $data = $this->RunSuccessJsonRequest(
                'GET',
                "/department/$department/deadlines"
            );
        } else {
            $data = $this->NPRunSuccessJsonRequest(
                'GET',
                "/department/$department/deadlines",
                null,
                null,
                200,
                $account
            );
        }
        $found = false;
        foreach ($data->data as $entry) {
            if ($entry->id == $this->target) {
                $found = true;
            }
        }
        if ($result) {
            $this->assertTrue($found);
            $this->assertNotEmpty($data->data);
        } else {
            $this->assertFalse($found);
        }

        /* check direct access */

        if ($result) {
            $code = 200;
        } else {
            $code = 403;
        }
        if ($account === null) {
            $this->RunSuccessJsonRequest(
                'GET',
                "/deadline/{$this->target}",
                null,
                null,
                $code
            );
        } else {
            $this->NPRunSuccessJsonRequest(
                'GET',
                "/deadline/{$this->target}",
                null,
                null,
                $code,
                $account
            );
        }

    }


    public function testDeadlineErrors(): void
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

        $this->runRequest('POST', '/department/2/deadline', null, null, 400);

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
            '/department/2/deadline',
            null,
            ['note' => 'testing'],
            400
        );

        $this->runRequest(
            'POST',
            '/department/2/deadline',
            null,
            ['deadline' => "$when"],
            400
        );

        $this->runRequest(
            'POST',
            '/department/2/deadline',
            null,
            ['deadline' => "not-a-date", 'note' => 'testing'],
            400
        );

        $when = date('Y-m-d', strtotime('-1 day'));
        $this->runRequest(
            'POST',
            '/department/2/deadline',
            null,
            ['deadline' => "$when", 'note' => 'testing'],
            400
        );

        $this->runRequest('DELETE', '/deadline/-1', null, null, 404);

    }


    /* End */
}
