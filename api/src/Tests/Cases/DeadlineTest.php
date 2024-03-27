<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;
use App\Tests\Base\TestRun;

class DeadlineTest extends CiabTestCase
{

    private $target;

    private $position;


    protected function setUp(): void
    {
        parent::setUp();
        $data = testRun::testRun($this, 'GET', '/department/{id}/deadlines')
            ->setUriParts(['id' => '2'])
            ->run();

        $initial_ids = [];
        foreach ($data->data as $item) {
            $initial_ids[] = $item->id;
        }

        $when = date('Y-m-d', strtotime('+1 month'));
        testRun::testRun($this, 'POST', '/department/{id}/deadline')
            ->setUriParts(['id' => '2'])
            ->setBody(['deadline' => $when,
                'note' => 'testing'])
            ->run();

        $data = testRun::testRun($this, 'GET', '/department/{id}/deadlines')
            ->setUriParts(['id' => '2'])
            ->run();

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

        $data = testRun::testRun($this, 'GET', '/deadline/{id}')
            ->setUriParts(['id' => $target])
            ->run();
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
        $this->position = testRun::testRun($this, 'POST', "/member/{id}/staff_membership")
            ->setUriParts(['id' => $id])
            ->setBody(['Department' => '2', 'Position' => '3', 'Note' => 'PHPUnit Testing'])
            ->run();

    }


    protected function tearDown(): void
    {
        testRun::testRun($this, 'DELETE', '/deadline/{id}')
            ->setUriParts(['id' => $this->target])
            ->run();
        testRun::testRun($this, 'DELETE', '/staff/membership/{id}')
            ->setUriParts(['id' => $this->position->id])
            ->run();

        parent::tearDown();

    }


    public function testDeadline(): void
    {
        $data = testRun::testRun($this, 'GET', '/department/{id}/deadlines')
            ->setUriParts(['id' => 2])
            ->run();
        $this->assertNotEmpty($data->data);
        $this->assertIncludes($data->data[0], 'department');


        $data = testRun::testRun($this, 'GET', '/deadline')->run();
        $this->assertNotEmpty($data->data);
        $this->assertIncludes($data->data[0], 'department');

        $when = date('Y-m-d', strtotime('+1 year'));
        testRun::testRun($this, 'PUT', '/deadline/{id}')
            ->setUriParts(['id' => $this->target])
            ->setBody(['department' => 3,
             'note' => 'New Message',
             'deadline' => "$when"])
            ->setNullReturn()
            ->run();

        $data = testRun::testRun($this, 'GET', '/deadline/{id}')
            ->setUriParts(['id' => $this->target])
            ->run();
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
        testRun::testRun($this, 'PUT', '/deadline/{id}')
            ->setUriParts(['id' => $this->target])
            ->setBody(['department' => $department,
                'scope' => $scope ])
            ->setNullReturn()
            ->run();

        /* check member access */

        $data = testRun::testRun($this, 'GET', "/deadline")
            ->setNpLoginIndex($account)
            ->run();
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

        $data = testRun::testRun($this, 'GET', "/department/{id}/deadlines")
            ->setUriParts(['id' => $department])
            ->setNpLoginIndex($account)
            ->run();
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
        testRun::testRun($this, 'GET', "/deadline/{id}")
            ->setUriParts(['id' => $this->target])
            ->setExpectedResult($code)
            ->setNpLoginIndex($account)
            ->run();

    }


    public function testDeadlineGetErrors(): void
    {
        testRun::testRun($this, 'GET', '/department/{id}/deadlines')
            ->setUriParts(['id' => -1])
            ->setExpectedResult(404)
            ->run();
        testRun::testRun($this, 'GET', '/deadline/{id}')
            ->setUriParts(['id' => -1])
            ->setExpectedResult(404)
            ->run();

    }


    public function testDeadlinePutErrors(): void
    {
        testRun::testRun($this, 'PUT', '/deadline/{id}')
            ->setUriParts(['id' => $this->target])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'PUT', '/deadline/{id}')
            ->setUriParts(['id' => -1])
            ->setExpectedResult(404)
            ->run();

        testRun::testRun($this, 'PUT', '/deadline/{id}')
            ->setUriParts(['id' => $this->target])
            ->setBody(['department' => -1])
            ->setExpectedResult(404)
            ->run();

        testRun::testRun($this, 'PUT', '/deadline/{id}')
            ->setUriParts(['id' => $this->target])
            ->setBody(['nothing' => -1, 'we' => 1, 'Known' => 2])
            ->setExpectedResult(400)
            ->run();

        testRun::testRun($this, 'PUT', '/deadline/{id}')
            ->setUriParts(['id' => $this->target])
            ->setBody(['deadline' => "not-a-date"])
            ->setExpectedResult(400)
            ->run();

        $when = date('Y-m-d', strtotime('-1 day'));
        testRun::testRun($this, 'PUT', '/deadline/{id}')
            ->setUriParts(['id' => $this->target])
            ->setBody(['deadline' => "$when"])
            ->setExpectedResult(400)
            ->run();

    }


    public function testDeadlinePostErrors(): void
    {
        testRun::testRun($this, 'POST', '/department/{id}/deadline')
            ->setUriParts(['id' => -1])
            ->setExpectedResult(404)
            ->run();

        $when = date('Y-m-d', strtotime('+1 year'));
        testRun::testRun($this, 'POST', '/department/{id}/deadline')
            ->setUriParts(['id' => -1])
            ->setBody(['deadline' => $when, 'note' => 'testing'])
            ->setExpectedResult(404)
            ->run();

        testRun::testRun($this, 'POST', '/department/{id}/deadline')
            ->setUriParts(['id' => 2])
            ->setBody(['note' => 'testing'])
            ->setExpectedResult(400)
            ->run();

        testRun::testRun($this, 'POST', '/department/{id}/deadline')
            ->setUriParts(['id' => 2])
            ->setBody(['deadline' => "$when"])
            ->setExpectedResult(400)
            ->run();

        testRun::testRun($this, 'POST', '/department/{id}/deadline')
            ->setUriParts(['id' => 2])
            ->setBody(['deadline' => "not-a-date", 'note' => 'testing'])
            ->setExpectedResult(400)
            ->run();

        $when = date('Y-m-d', strtotime('-1 day'));
        testRun::testRun($this, 'POST', '/department/{id}/deadline')
            ->setUriParts(['id' => 2])
            ->setBody(['deadline' => "$when", 'note' => 'testing'])
            ->setExpectedResult(400)
            ->run();

    }


    public function testDeadlineDeleteErrors(): void
    {
        testRun::testRun($this, 'DELETE', '/deadline/{id}')
            ->setUriParts(['id' => -1])
            ->setExpectedResult(404)
            ->run();

    }


    /* End */
}
