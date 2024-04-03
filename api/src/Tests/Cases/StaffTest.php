<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;
use App\Tests\Base\TestRun;

class StaffTest extends CiabTestCase
{


    public function testPostMembership(): void
    {
        testRun::testRun($this, 'POST', '/member/{id}/staff_membership')
            ->setUriParts(['id' => 1000])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'POST', '/member/{id}/staff_membership')
            ->setUriParts(['id' => -1])
            ->setExpectedResult(404)
            ->run();
        testRun::testRun($this, 'POST', '/member/{id}/staff_membership')
            ->setUriParts(['id' => 1000])
            ->setBody(['Nothing' => 0])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'POST', '/member/{id}/staff_membership')
            ->setUriParts(['id' => 1000])
            ->setBody(['Department' => -1])
            ->setExpectedResult(404)
            ->run();
        testRun::testRun($this, 'POST', '/member/{id}/staff_membership')
            ->setUriParts(['id' => 1000])
            ->setBody(['Department' => 2])
            ->setExpectedResult(400)
            ->run();
        $data = testRun::testRun($this, 'POST', '/member/{id}/staff_membership')
            ->setUriParts(['id' => 1000])
            ->setBody(['Department' => 2, 'Position' => 1])
            ->run();
        $this->assertNotEmpty($data);
        testRun::testRun($this, 'DELETE', '/staff/membership/{id}')
            ->setUriParts(['id' => $data->id])
            ->run();
        testRun::testRun($this, 'DELETE', '/staff/membership/{id}')
            ->setUriParts(['id' => -1])
            ->setExpectedResult(404)
            ->run();

    }


    public function testPutMembership(): void
    {
        testRun::testRun($this, 'PUT', '/member/{id}/staff_membership')
            ->setUriParts(['id' => 1000])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'PUT', '/member/{id}/staff_membership')
            ->setUriParts(['id' => -1])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'PUT', '/member/{id}/staff_membership')
            ->setUriParts(['id' => 1000])
            ->setBody(['Nothing' => 0])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'PUT', '/member/{id}/staff_membership')
            ->setUriParts(['id' => 1000])
            ->setBody(['Department' => -1, 'Position' => 1])
            ->setExpectedResult(404)
            ->run();
        testRun::testRun($this, 'PUT', '/member/{id}/staff_membership')
            ->setUriParts(['id' => 1000])
            ->setBody(['Department' => 2])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'PUT', '/member/{id}/staff_membership')
            ->setUriParts(['id' => 1000])
            ->setBody(['Department' => 2, 'Position' => 1, 'Note' => 'phpunitAddedNote'])
            ->setNullReturn()
            ->run();
        $data = testRun::testRun($this, 'GET', '/member/{id}/staff_membership')
            ->setUriParts(['id' => 1000])
            ->run();
        $this->assertEquals($data->data[0]->position, 'Head');
        $this->assertEquals($data->data[0]->note, 'phpunitAddedNote');

    }


    public function testMembership(): void
    {
        $position = testRun::testRun($this, 'POST', '/member/{id}/staff_membership')
            ->setUriParts(['id' => 1000])
            ->setBody(['Department' => '105', 'Position' => '1', 'Note' => 'PHPUnit Testing'])
            ->run();
        $this->assertNotEmpty($position);

        $data = testRun::testRun($this, 'GET', '/member/{id}/staff_membership')
            ->setUriParts(['id' => 1000])
            ->run();
        $this->assertNotEmpty($data);

        $data2 = testRun::testRun($this, 'GET', '/member/staff_membership/')
            ->run();
        $this->assertNotEmpty($data);
        $this->assertEquals($data, $data2);
        $this->assertIncludes($data->data[0], 'department');
        $this->assertIncludes($data->data[0], 'member');

        testRun::testRun($this, 'GET', '/staff/membership/{id}')
            ->setUriParts(['id' => -1])
            ->setExpectedResult(404)
            ->run();

        $data = testRun::testRun($this, 'GET', '/staff/membership/{id}')
            ->setUriParts(['id' => $position->id])
            ->run();
        $this->assertNotEmpty($data);

        $data = testRun::testRun($this, 'GET', '/staff')
            ->run();
        $this->assertNotEmpty($data);
        $this->assertNotEmpty($data->data);

        $data = testRun::testRun($this, 'GET', '/department/{id}/staff')
            ->setUriParts(['id' => 105])
            ->run();
        $this->assertNotEmpty($data);
        $this->assertNotEmpty($data->data);

        $data = testRun::testRun($this, 'GET', '/department/{id}/staff')
            ->setUriParts(['id' => 3])
            ->run();
        $this->assertNotEmpty($data);
        $this->assertEmpty($data->data);

        $data = testRun::testRun($this, 'GET', '/department/{id}/staff')
            ->setUriParts(['id' => 2])
            ->setMethodParameters(['subdepartments' => 1])
            ->run();
        $this->assertNotEmpty($data);
        $this->assertNotEmpty($data->data);

        testRun::testRun($this, 'DELETE', '/staff/membership/{id}')
            ->setUriParts(['id' => $position->id])
            ->run();

    }


    /* End */
}
