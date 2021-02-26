<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;

class EventTest extends CiabTestCase
{


    public function testEvent(): void
    {
        $when = date('Y-m-d', strtotime('-20 years'));
        $to = date('Y-m-d', strtotime('+20 years'));

        $this->runSuccessJsonRequest('GET', '/event');

        $this->runSuccessJsonRequest(
            'GET',
            '/event',
            ['begin' => $when]
        );

        $this->runSuccessJsonRequest(
            'GET',
            '/event',
            ['end' => $to]
        );

        $data = $this->runSuccessJsonRequest(
            'GET',
            '/event',
            ['end' => $to,'begin' => $when]
        );
        $id = $data->data[0]->id;
        $data = $this->runSuccessJsonRequest('GET', '/event/'.$id);
        $cycleid = $data->cycle;

        $data = $this->runSuccessJsonRequest(
            'GET',
            '/event/'.$id,
            ['include' => 'cycle']
        );
        $this->assertSame($data->cycle->id, $cycleid);

    }


    public function testEventErrors(): void
    {
        $this->runRequest(
            'GET',
            '/event',
            ['begin' => 'not-a-date'],
            null,
            400
        );

        $this->runRequest(
            'GET',
            '/event',
            ['end' => 'not-a-date'],
            null,
            400
        );

        $this->runRequest('GET', '/event/-1', null, null, 404);

    }


    /* End */
}
