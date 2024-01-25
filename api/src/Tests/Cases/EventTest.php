<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;
use App\Tests\Base\TestRun;

class EventTest extends CiabTestCase
{

    private $events = array();

    private $cycles = array();

    private $put_id = null;


    protected function setUp(): void
    {
        parent::setUp();
        $when = date('Y-m-d', strtotime('+1998 years'));
        $to = date('Y-m-d', strtotime('+2002 years'));
        $this->cycles[] = testRun::testRun($this, 'POST', '/cycle')
            ->setBody(['date_from' => $when, 'date_to' => $to])
            ->run();
        $when = date('Y-m-d', strtotime('+2002 years +1 day'));
        $to = date('Y-m-d', strtotime('+2004 years'));
        $this->cycles[] = testRun::testRun($this, 'POST', '/cycle')
            ->setBody(['date_from' => $when, 'date_to' => $to])
            ->run();
        $when = date('Y-m-d', strtotime('+1999 years'));
        $to = date('Y-m-d', strtotime('+1999 years'));
        $this->events[] = testRun::testRun($this, 'POST', '/event')
            ->setBody(['date_from' => $when, 'date_to' => $to, 'name' => 'PHPTest-a-con'])
            ->run();

        $when = date('Y-m-d', strtotime('+1998 years'));
        $to = date('Y-m-d', strtotime("$when +1 day"));

        $event = testRun::testRun($this, 'POST', '/event')
            ->setBody(['date_from' => $when, 'date_to' => $to, 'name' => 'PUT PHPTest-a-con'])
            ->run();
        $this->events[] = $event;
        $this->put_id = $event->id;

    }


    protected function tearDown(): void
    {
        foreach ($this->events as $event) {
            testRun::testRun($this, 'DELETE', '/event/{id}')
                ->setUriParts(['id' => $event->id])
                ->run();
        }
        foreach ($this->cycles as $cycle) {
            testRun::testRun($this, 'DELETE', '/cycle/{id}')
                ->setUriParts(['id' => $cycle->id])
                ->run();
        }
        parent::tearDown();

    }


    public function testEvent(): void
    {
        $when = date('Y-m-d', strtotime('+1999 years'));
        $to = date('Y-m-d', strtotime('+1999 years'));

        $data = testRun::testRun($this, 'GET', '/event')
            ->run();
        $this->assertSame($data->type, 'event_list');

        $data = testRun::testRun($this, 'GET', '/event/{id}')
            ->setUriParts(['id' => 'current'])
            ->run();
        $this->assertSame($data->type, 'event');

        testRun::testRun($this, 'GET', '/event')
            ->setMethodParameters(['begin' => $when])
            ->run();

        testRun::testRun($this, 'GET', '/event')
            ->setMethodParameters(['end' => $to])
            ->run();

        $data = testRun::testRun($this, 'GET', '/event')
            ->setMethodParameters(['end' => $to,'begin' => $when])
            ->run();
        $id = $data->data[0]->id;
        $data = testRun::testRun($this, 'GET', '/event/{id}')
            ->setUriParts(['id' => $id])
            ->run();
        $this->assertIncludes($data, 'cycle');

    }


    public function postProvider(): array
    {
        $when = date('Y-m-d', strtotime('+2000 years'));
        $to = date('Y-m-d', strtotime('+2001 years'));

        $when2 = date('Y-m-d', strtotime('+3000 years'));
        $to2 = date('Y-m-d', strtotime('+3001 years'));

        $when3 = date('Y-m-d', strtotime('+2001 years'));
        $to3 = date('Y-m-d', strtotime('+2003 years'));

        /* when, to, name */
        return [
            [null, null, null],
            [$when, null, null],
            ['not-a-date', 'not-a-date', null],
            [$when, 'not-a-date', null],
            ['not-a-date', $to, null],
            [$when, $to, null],
            [$when2, $to2, 'PHPTest-a-con'],
            [$when3, $to3, 'PHPTest-a-con'],
        ];

    }


    /**
     * @test
     * @dataProvider postProvider
     **/
    public function testPostEventError($when, $to, $name): void
    {
        $body = [];
        if ($when !== null) {
            $body['date_from'] = $when;
        }
        if ($to !== null) {
            $body['date_to'] = $to;
        }
        if ($name !== null) {
            $body['name'] = $name;
        }

        testRun::testRun($this, 'POST', '/event')
            ->setBody($body)
            ->setExpectedResult(400)
            ->run();

    }


    public function testPostEvent(): void
    {
        $when = date('Y-m-d', strtotime('+2000 years'));
        $to = date('Y-m-d', strtotime('+2001 years'));
        $this->events[] = testRun::testRun($this, 'POST', '/event')
            ->setBody(['date_from' => $when, 'date_to' => $to, 'name' => 'NEW PHPTest-a-con'])
            ->run();

    }


    public function putProvider(): array
    {
        $when = date('Y-m-d', strtotime('+1998 years'));
        $date = date('Y-m-d', strtotime("$when +1000 years"));
        $when = date('Y-m-d', strtotime('+2001 years'));
        $to = date('Y-m-d', strtotime('+2003 years'));

        /* id, from, to, name, result */
        return [
            ['unknown', null, null,  404],
            [null, null, null, 400],
            [null, $date, null, 400],
            [null, 'notadate', null, 400],
            [null, null, $date, 400],
            [null, null, 'notadate', 400],
            [null, $when, $to, 400],
        ];

    }


    /**
     * @test
     * @dataProvider putProvider
     **/
    public function testPutEventError($id, $when, $to, $result): void
    {
        $body = [];
        if ($when !== null) {
            $body['date_from'] = $when;
        }
        if ($to !== null) {
            $body['date_to'] = $to;
        }

        if ($id === null) {
            $id = $this->put_id;
        }

        testRun::testRun($this, 'PUT', '/event/{id}')
            ->setUriParts(['id' => $id])
            ->setBody($body)
            ->setExpectedResult($result)
            ->run();

    }


    public function testPutEvent(): void
    {
        $when = date('Y-m-d', strtotime('+2001 years'));
        $to = date('Y-m-d', strtotime('+2003 years'));

        $date = date('Y-m-d', strtotime("$when +1 day"));
        $data = testRun::testRun($this, 'PUT', "/event/{id}")
            ->setUriParts(['id' => $this->put_id])
            ->setBody(['date_from' => $date])
            ->run();
        $this->assertSame($data->date_from, $date);

        $date = date('Y-m-d', strtotime("$when +2 days"));
        $data = testRun::testRun($this, 'PUT', "/event/{id}")
            ->setUriParts(['id' => $this->put_id])
            ->setBody(['date_to' => $date])
            ->run();
        $this->assertSame($data->date_to, $date);

        $data = testRun::testRun($this, 'PUT', "/event/{id}")
            ->setUriParts(['id' => $this->put_id])
            ->setBody(['name' => 'yet another CON'])
            ->run();
        $this->assertSame($data->name, 'yet another CON');

    }


    public function testEventErrors(): void
    {
        testRun::testRun($this, 'GET', '/event/{id}')
            ->setUriParts(['id' => -1])
            ->setExpectedResult(404)
            ->run();

    }


    /* End */
}
