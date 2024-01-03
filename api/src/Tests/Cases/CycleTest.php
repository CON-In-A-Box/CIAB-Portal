<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;

class CycleTest extends CiabTestCase
{

    private $cycle = null;


    protected function setUp(): void
    {
        parent::setUp();
        $when = date('Y-m-d', strtotime('+1999 years'));
        $to = date('Y-m-d', strtotime('+2001 years'));
        $this->cycle = $this->runSuccessJsonRequest(
            'POST',
            '/cycle',
            null,
            ['date_from' => $when, 'date_to' => $to],
            201,
            null,
            '/cycle'
        );

    }


    protected function tearDown(): void
    {
        if ($this->cycle != null) {
            $this->runRequest('DELETE', '/cycle/'.$this->cycle->id, null, null, 204, null, '/cycle/{id}');
        }
        parent::tearDown();

    }


    protected function addCycle(): string
    {
        $data = $this->runSuccessJsonRequest('GET', '/cycle', null, null, 200, null, '/cycle');
        $initial_ids = [];
        foreach ($data->data as $item) {
            $initial_ids[] = $item->id;
        }

        $when = date('Y-m-d', strtotime('-1 month'));
        $to = date('Y-m-d', strtotime('+1 month'));
        $data = $this->runSuccessJsonRequest(
            'POST',
            '/cycle',
            null,
            ['date_from' => $when, 'date_to' => $to],
            201,
            null,
            '/cycle'
        );

        $data2 = $this->runSuccessJsonRequest('GET', '/cycle/'.$data->id, null, null, 200, null, '/cycle/{id}');
        $this->assertSame((array)$data, (array)$data2);

        return $data->id;

    }


    public function testCycle(): void
    {
        $when = date('Y-m-d', strtotime('-2 month'));
        $to = date('Y-m-d', strtotime('+2 month'));
        $target = $this->addCycle();

        $data = $this->runSuccessJsonRequest('GET', '/cycle', null, null, 200, null, '/cycle');
        $this->assertNotEmpty($data->data);

        $data = $this->runSuccessJsonRequest(
            'GET',
            '/cycle',
            ['begin' => $when],
            null,
            200,
            null,
            '/cycle'
        );
        $this->assertNotEmpty($data->data);

        $data = $this->runSuccessJsonRequest(
            'GET',
            '/cycle',
            ['end' => $to],
            null,
            200,
            null,
            '/cycle'
        );
        $this->assertNotEmpty($data->data);

        $data = $this->runSuccessJsonRequest(
            'GET',
            '/cycle',
            ['end' => $to, 'begin' => $when],
            null,
            200,
            null,
            '/cycle'
        );
        $this->assertNotEmpty($data->data);

        $this->runSuccessRequest(
            'PUT',
            '/cycle/'.$target,
            null,
            ['date_from' => $when,
             'date_to' => $to],
            200,
            null,
            '/cycle/{id}'
        );

        $this->runRequest('DELETE', '/cycle/'.$target, null, null, 204, null, '/cycle/{id}');

    }


    public function testIncludesDate(): void
    {
        $target = date('Y-m-d', strtotime('+3000 years'));
        $data = $this->runSuccessJsonRequest('GET', '/cycle', ['includesDate' => $target], null, 200, null, '/cycle');
        $this->assertTrue(empty($data->data));
        $target = date('Y-m-d', strtotime('+2000 years'));
        $data = $this->runSuccessJsonRequest('GET', '/cycle', ['includesDate' => $target], null, 200, null, '/cycle');
        $this->assertTrue(!empty($data->data));
        $this->assertEquals(count($data->data), 1);
        $this->assertEquals($data->data[0], $this->cycle);

    }


    public function testCycleErrors(): void
    {
        $target = $this->addCycle();

        $this->runRequest(
            'GET',
            '/cycle',
            ['begin' => 'not-a-date'],
            null,
            400,
            null,
            '/cycle'
        );

        $this->runRequest(
            'GET',
            '/cycle',
            ['end' => 'not-a-date'],
            null,
            400,
            null,
            '/cycle'
        );

        $this->runRequest('GET', '/cycle/-1', null, null, 404, null, '/cycle/{id}');
        $this->runRequest('PUT', '/cycle/'.$target, null, null, 400, null, '/cycle/{id}');
        $this->runRequest('PUT', '/cycle/-1', null, null, 404, null, '/cycle/{id}');
        $this->runRequest('POST', '/cycle', null, null, 400, null, '/cycle');

        $when = date('Y-m-d', strtotime('-2 month'));
        $to = date('Y-m-d', strtotime('+2 month'));
        $this->runRequest(
            'POST',
            '/cycle',
            null,
            ['date_from' => $when, 'date_to' => 'not-a-date'],
            400,
            null,
            '/cycle'
        );

        $this->runRequest(
            'POST',
            '/cycle',
            null,
            ['date_to' => $to, 'date_from' => 'not-a-date'],
            400,
            null,
            '/cycle'
        );

        $this->runRequest(
            'POST',
            '/cycle',
            null,
            ['date_from' => $when],
            400,
            null,
            '/cycle'
        );

        $this->runRequest('POST', '/cycle', ['date_to' => $to], null, 400, null, '/cycle');
        $this->runRequest('DELETE', '/cycle/-1', null, null, 404, null, '/cycle/{id}');
        $this->runRequest('DELETE', '/cycle/'.$target, null, null, 204, null, '/cycle/{id}');

    }


    /* End */
}
