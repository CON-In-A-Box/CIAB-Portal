<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;

class AnnouncementTest extends CiabTestCase
{

    private $aid = array();


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
        for ($i = 0; $i < 4; $i ++) {
            $this->aid[$i] = $this->addAnnouncement($i);
        }

    }


    protected function tearDown(): void
    {
        parent::tearDown();
        foreach ($this->aid as $target) {
            $this->runSuccessRequest('DELETE', '/announcement/'.$target, null, null, 204);
        }

    }


    public function testAnnounce(): void
    {
        $data = $this->runSuccessJsonRequest(
            'GET',
            '/department/1/announcements'
        );
        $this->assertNotEmpty($data->data);
        $this->assertIncludes($data->data[0], 'department');
        $this->assertIncludes($data->data[0], 'posted_by');

        $this->runSuccessRequest(
            'PUT',
            '/announcement/'.$this->aid[1],
            null,
            ['department' => 2,
             'text' => 'New Message',
             'scope' => 1]
        );

        $data = $this->runSuccessJsonRequest(
            'GET',
            '/announcement/'.$this->aid[1]
        );
        $this->assertSame($data->text, 'New Message');
        $this->assertIncludes($data, 'department');
        $this->assertIncludes($data, 'posted_by');

        $data = $this->runSuccessJsonRequest(
            'GET',
            '/member/1000/announcements'
        );
        $this->assertNotEmpty($data->data);
        $this->assertIncludes($data->data[0], 'department');
        $this->assertIncludes($data->data[0], 'posted_by');

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

        $this->runRequest('PUT', '/announcement/'.$this->aid[2], null, null, 400);

        $this->runRequest('PUT', '/announcement/-1', null, null, 404);

        $this->runRequest(
            'PUT',
            '/announcement/'.$this->aid[2],
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
