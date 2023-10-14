<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;

class AnnouncementTest extends CiabTestCase
{

    private $target;

    private $position;


    protected function addAnnouncement($scope): string
    {
        $data = $this->runSuccessJsonRequest('GET', '/department/1/announcements');
        $initial_ids = [];
        foreach ($data->data as $item) {
            $initial_ids[] = $item->id;
        }

        $this->runSuccessRequest(
            'POST',
            '/department/1/announcement',
            null,
            ['scope' => $scope,
             'text' => 'testing',
             'email' => 1],
            201
        );

        $data = $this->runSuccessJsonRequest('GET', '/department/1/announcements');

        # Find New Index
        $target = null;
        foreach ($data->data as $item) {
            if ($target == null && !in_array($item->id, $initial_ids)) {
                $target = $item->id;
                $this->assertIncludes($item, 'department');
                $this->assertIncludes($item, 'posted_by');
                unset($item->department);
                unset($item->posted_by);
                $this->assertEquals([
                    'type' => 'announcement',
                    'id' => $target,
                    'posted_on' => $item->posted_on,
                    'scope' => "$scope",
                    'text' => 'testing',
                ], (array)$item);
            }
        }

        $data = $this->runSuccessJsonRequest(
            'GET',
            '/announcement/'.$target,
            ['fields' => 'type,id,scope,text']
        );
        $this->assertSame([
            'type' => 'announcement',
            'id' => $target,
            'scope' => "$scope",
            'text' => 'testing'
        ], (array)$data);

        return $target;

    }


    protected function setUp(): void
    {
        parent::setUp();
        $this->target = $this->addAnnouncement(0);
        $id = $this->testing_accounts[0];
        $this->position = $this->runSuccessJsonRequest('POST', "/member/$id/staff_membership", null, ['Department' => '1', 'Position' => '3', 'Note' => 'PHPUnit Testing'], 201);

    }


    protected function tearDown(): void
    {
        $this->runRequest('DELETE', '/announcement/'.$this->target, null, null, 204);
        $this->runRequest('DELETE', '/staff/membership/'.$this->position->id, null, null, 204);

        parent::tearDown();

    }


    public function provider(): array
    {
        return [
            /* Loki, concom member, same department */
        [1, 0, 0, true],
        [1, 1, 0, true],
        [1, 2, 0, true],
            /* Loki, concom member, other department*/
        [4, 0, 0, true],
        [4, 1, 0, true],
        [4, 2, 0, false],
            /* Frigga , normal member */
        [1, 0, 1, true],
        [1, 1, 1, false],
        [1, 2, 1, false],
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
    public function testAnnouncmentScope($department, $scope, $account, $result): void
    {
        if ($account === null) {
            $id = 1000;
        } else {
            $id = $this->testing_accounts[$account];
        }

        $this->runRequest(
            'PUT',
            '/announcement/'.$this->target,
            null,
            ['department' => $department,
             'scope' => $scope ],
            200
        );


        /* check member access */

        if ($account === null) {
            $data = $this->RunSuccessJsonRequest(
                'GET',
                "/announcement"
            );
        } else {
            $data = $this->NPRunSuccessJsonRequest(
                'GET',
                "/announcement",
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
                "/department/$department/announcements"
            );
        } else {
            $data = $this->NPRunSuccessJsonRequest(
                'GET',
                "/department/$department/announcements",
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
                "/announcement/{$this->target}",
                null,
                null,
                $code
            );
        } else {
            $this->NPRunSuccessJsonRequest(
                'GET',
                "/announcement/{$this->target}",
                null,
                null,
                $code,
                $account
            );
        }

    }


    public function testAnnounceErrors(): void
    {
        $this->runRequest(
            'GET',
            '/department/-1/announcements',
            null,
            null,
            404
        );

        $this->runRequest('PUT', '/announcement/'.$this->target, null, null, 400);

        $this->runRequest('PUT', '/announcement/-1', null, null, 404);

        $this->runRequest(
            'PUT',
            '/announcement/'.$this->target,
            null,
            ['department' => -1],
            404
        );

        $this->runRequest('POST', '/department/1/announcement', null, null, 400);

        $this->runRequest(
            'POST',
            '/department/-1/announcement',
            null,
            ['scope' => 2,
             'text' => 'testing',
             'email' => 0],
            404
        );

        $this->runRequest(
            'POST',
            '/department/1/announcement',
            null,
            ['text' => 'testing',
             'email' => 0],
            400
        );

        $this->runRequest(
            'POST',
            '/department/1/announcement',
            null,
            ['scope' => 2,
             'email' => 0],
            400
        );

    }


    /* End */
}
