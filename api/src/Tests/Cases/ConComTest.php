<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;

class ConcomTest extends CiabTestCase
{

    private $cycle;

    private $event;


    protected function setUp(): void
    {
        parent::setUp();
        $when = date('Y-m-d', strtotime('+1998 years'));
        $to = date('Y-m-d', strtotime('+2002 years'));
        $this->cycle = $this->runSuccessJsonRequest('POST', '/cycle', null, ['From' => $when, 'To' => $to], 201);
        $when = date('Y-m-d', strtotime('+1999 years'));
        $to = date('Y-m-d', strtotime('+1999 years'));
        $this->event = $this->runSuccessJsonRequest('POST', '/event', null, ['From' => $when, 'To' => $to, 'Name' => 'PHPTest-a-con'], 201);

    }


    protected function tearDown(): void
    {
        $this->runRequest('DELETE', '/event/'.$this->event->id, null, null, 204);
        $this->runRequest('DELETE', '/cycle/'.$this->cycle->id, null, null, 204);
        parent::tearDown();

    }


    public function testPostMembership(): void
    {
        $this->runRequest('POST', '/member/1000/staff_membership', null, null, 400);
        $this->runRequest('POST', '/member/-1/staff_membership', null, null, 404);
        $this->runRequest('POST', '/member/1000/staff_membership', null, ['Nothing' => 0], 400);
        $this->runRequest('POST', '/member/1000/staff_membership', null, ['Department' => -1], 404);
        $this->runRequest('POST', '/member/1000/staff_membership', null, ['Department' => 1], 400);
        $data = $this->runSuccessJsonRequest('POST', '/member/1000/staff_membership', null, ['Department' => 1, 'Position' => 1], 201);
        $this->assertNotEmpty($data);
        $this->runRequest('DELETE', '/staff_membership/'.$data->id, null, null, 204);
        $this->runRequest('DELETE', '/staff_membership/-1', null, null, 404);

    }


    public function testMembership(): void
    {
        $position = $this->runSuccessJsonRequest('POST', '/member/1000/staff_membership', null, ['Department' => '1', 'Position' => '1', 'Note' => 'PHPUnit Testing', 'Event' => $this->event->id], 201);
        $this->assertNotEmpty($position);

        $data = $this->runSuccessJsonRequest('GET', '/member/1000/concom');
        $this->assertNotEmpty($data);

        $this->runRequest('GET', '/staff_membership/-1', null, null, 404);

        $data = $this->runSuccessJsonRequest('GET', '/staff_membership/'.$position->id);
        $this->assertNotEmpty($data);

        $this->runRequest('DELETE', '/staff_membership/'.$position->id, null, null, 204);

    }


    /* End */
}
