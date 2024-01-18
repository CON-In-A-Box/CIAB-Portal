<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;
use App\Tests\Base\TestRun;

class CycleTest extends CiabTestCase
{

    private $cycle = null;


    protected function setUp(): void
    {
        parent::setUp();
        $when = date('Y-m-d', strtotime('+1999 years'));
        $to = date('Y-m-d', strtotime('+2001 years'));
        $this->cycle = testRun::testRun($this, 'POST', '/cycle')
            ->setBody(['date_from' => $when, 'date_to' => $to])
            ->run();

    }


    protected function tearDown(): void
    {
        if ($this->cycle != null) {
            testRun::testRun($this, 'DELETE', '/cycle/{id}')
                ->setUriParts(['id' => $this->cycle->id])
                ->run();
        }
        parent::tearDown();

    }


    protected function addCycle(): string
    {
        $data = testRun::testRun($this, 'GET', '/cycle')->run();
        $initial_ids = [];
        foreach ($data->data as $item) {
            $initial_ids[] = $item->id;
        }

        $when = date('Y-m-d', strtotime('-1 month'));
        $to = date('Y-m-d', strtotime('+1 month'));
        $data = testRun::testRun($this, 'POST', '/cycle')
            ->setBody(['date_from' => $when, 'date_to' => $to])
            ->run();

        $data2 = testRun::testRun($this, 'GET', '/cycle/{id}')
            ->setUriParts(['id' => $data->id])
            ->run();
        $this->assertSame((array)$data, (array)$data2);

        return $data->id;

    }


    public function testCycle(): void
    {
        $when = date('Y-m-d', strtotime('-2 month'));
        $to = date('Y-m-d', strtotime('+2 month'));
        $target = $this->addCycle();

        $data = testRun::testRun($this, 'GET', '/cycle')->run();
        $this->assertNotEmpty($data->data);

        $data = testRun::testRun($this, 'GET', '/cycle')
            ->setMethodParameters(['begin' => $when])
            ->run();
        $this->assertNotEmpty($data->data);

        $data = testRun::testRun($this, 'GET', '/cycle')
            ->setMethodParameters(['end' => $to])
            ->run();
        $this->assertNotEmpty($data->data);

        $data = testRun::testRun($this, 'GET', '/cycle')
            ->setMethodParameters(['end' => $to, 'begin' => $when])
            ->run();
        $this->assertNotEmpty($data->data);

        testRun::testRun($this, 'PUT', '/cycle/{id}')
            ->setUriParts(['id' => $target])
            ->setBody(['date_from' => $when,
                       'date_to' => $to])
            ->run();

        testRun::testRun($this, 'DELETE', '/cycle/{id}')
            ->setUriParts(['id' => $target])
            ->run();

    }


    public function testIncludesDate(): void
    {
        $target = date('Y-m-d', strtotime('+3000 years'));
        $data = testRun::testRun($this, 'GET', '/cycle')
            ->setMethodParameters(['includesDate' => $target])
            ->run();
        $this->assertTrue(empty($data->data));
        $target = date('Y-m-d', strtotime('+2000 years'));
        $data = testRun::testRun($this, 'GET', '/cycle')
            ->setMethodParameters(['includesDate' => $target])
            ->run();
        $this->assertTrue(!empty($data->data));
        $this->assertEquals(count($data->data), 1);
        $this->assertEquals($data->data[0], $this->cycle);

    }


    public function testCycleGetErrors(): void
    {
        testRun::testRun($this, 'GET', '/cycle')
            ->setMethodParameters(['begin' => 'not-a-date'])
            ->setExpectedResult(400)
            ->run();

        testRun::testRun($this, 'GET', '/cycle')
            ->setMethodParameters(['end' => 'not-a-date'])
            ->setExpectedResult(400)
            ->run();

        testRun::testRun($this, 'GET', '/cycle/{id}')
            ->setUriParts(['id' => -1])
            ->setExpectedResult(404)
            ->run();

    }


    public function testCyclePutErrors(): void
    {
        $target = $this->addCycle();

        testRun::testRun($this, 'PUT', '/cycle/{id}')
            ->setUriParts(['id' => $target])
            ->setExpectedResult(400)
            ->run();

        testRun::testRun($this, 'PUT', '/cycle/{id}')
            ->setUriParts(['id' => -1])
            ->setExpectedResult(404)
            ->run();

        testRun::testRun($this, 'DELETE', '/cycle/{id}')
            ->setUriParts(['id' => $target])
            ->run();

    }


    public function testCyclePostErrors(): void
    {
        testRun::testRun($this, 'POST', '/cycle')
            ->setExpectedResult(400)
            ->run();

        $when = date('Y-m-d', strtotime('-2 month'));
        $to = date('Y-m-d', strtotime('+2 month'));
        testRun::testRun($this, 'POST', '/cycle')
            ->setBody(['date_from' => $when, 'date_to' => 'not-a-date'])
            ->setExpectedResult(400)
            ->run();

        testRun::testRun($this, 'POST', '/cycle')
            ->setBody(['date_to' => $to, 'date_from' => 'not-a-date'])
            ->setExpectedResult(400)
            ->run();

        testRun::testRun($this, 'POST', '/cycle')
            ->setBody(['date_from' => $when])
            ->setExpectedResult(400)
            ->run();

        testRun::testRun($this, 'POST', '/cycle')
            ->setBody(['date_to' => $to])
            ->setExpectedResult(400)
            ->run();

    }


    public function testCycleDelete(): void
    {
        $target = $this->addCycle();

        testRun::testRun($this, 'DELETE', '/cycle/{id}')
            ->setUriParts(['id' => -1])
            ->setExpectedResult(404)
            ->run();

        testRun::testRun($this, 'DELETE', '/cycle/{id}')
            ->setUriParts(['id' => $target])
            ->run();

    }


    /* End */
}
