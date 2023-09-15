<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;

class StaffTest extends CiabTestCase
{


    public function testPostMembership(): void
    {
        $this->runRequest('POST', '/member/1000/staff_membership', null, null, 400);
        $this->runRequest('POST', '/member/-1/staff_membership', null, null, 404);
        $this->runRequest('POST', '/member/1000/staff_membership', null, ['Nothing' => 0], 400);
        $this->runRequest('POST', '/member/1000/staff_membership', null, ['Department' => -1], 404);
        $this->runRequest('POST', '/member/1000/staff_membership', null, ['Department' => 1], 400);
        $data = $this->runSuccessJsonRequest('POST', '/member/1000/staff_membership', null, ['Department' => 1, 'Position' => 1], 201);
        $this->assertNotEmpty($data);
        $this->runRequest('DELETE', '/staff/membership/'.$data->id, null, null, 204);
        $this->runRequest('DELETE', '/staff/membership/-1', null, null, 404);

    }


    public function testMembership(): void
    {
        $position = $this->runSuccessJsonRequest('POST', '/member/1000/staff_membership', null, ['Department' => '102', 'Position' => '1', 'Note' => 'PHPUnit Testing'], 201);
        $this->assertNotEmpty($position);

        $data = $this->runSuccessJsonRequest('GET', '/member/1000/staff_membership');
        $this->assertNotEmpty($data);

        $data2 = $this->runSuccessJsonRequest('GET', '/member/staff_membership/');
        $this->assertNotEmpty($data);
        $this->assertEquals($data, $data2);
        $this->assertIncludes($data->data[0], 'department');
        $this->assertIncludes($data->data[0], 'member');

        $this->runRequest('GET', '/staff_membership/-1', null, null, 404);

        $data = $this->runSuccessJsonRequest('GET', '/staff/membership/'.$position->id);
        $this->assertNotEmpty($data);

        $data = $this->runSuccessJsonRequest('GET', '/staff');
        $this->assertNotEmpty($data);
        $this->assertNotEmpty($data->data);

        $data = $this->runSuccessJsonRequest('GET', '/department/102/staff');
        $this->assertNotEmpty($data);
        $this->assertNotEmpty($data->data);

        $data = $this->runSuccessJsonRequest('GET', '/department/2/staff');
        $this->assertNotEmpty($data);
        $this->assertEmpty($data->data);

        $data = $this->runSuccessJsonRequest('GET', '/department/2/staff', ['subdepartments' => 1]);
        $this->assertNotEmpty($data);
        $this->assertNotEmpty($data->data);

        $this->runRequest('DELETE', '/staff/membership/'.$position->id, null, null, 204);

    }


    /* End */
}
