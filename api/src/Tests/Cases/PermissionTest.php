<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;

class PermissionsTest extends CiabTestCase
{


    public function testPermissions(): void
    {
        $this->runSuccessJsonRequest('GET', '/permissions/resource/deadline/1/get');
        $this->runSuccessJsonRequest('GET', '/permissions/method/deadline');

        $this->runSuccessJsonRequest('GET', '/permissions/resource/announcement/1/put');
        $this->runSuccessJsonRequest('GET', '/permissions/method/announcement');

        $data = $this->runSuccessJsonRequest('GET', '/member/1/permissions');
        $this->assertEquals($data->type, 'permission_list');
        $this->assertNotEmpty($data->data);

        $id = $this->testing_accounts[0];

        $data = $this->NPRunSuccessJsonRequest('GET', "/member/$id/permissions");
        $this->assertEquals($data->type, 'permission_list');
        $this->assertEmpty($data->data);

        $data = $this->NPRunSuccessJsonRequest('GET', '/permissions/generic/announcement/put/all');
        $this->assertEquals($data->type, 'permission_list');
        $this->assertEquals($data->data[0]->allowed, 0);

        $data = $this->NPRunSuccessJsonRequest('GET', '/permissions/generic/bad/get/one');
        $this->assertEquals($data->type, 'permission_list');
        $this->assertEquals($data->data[0]->allowed, 0);

        $data = $this->RunSuccessJsonRequest('GET', '/permissions/generic/announcement/put/all');
        $this->assertEquals($data->type, 'permission_list');
        $this->assertEquals($data->data[0]->allowed, 1);

        $data = $this->RunSuccessJsonRequest('GET', '/permissions/generic/bad/get/one');
        $this->assertEquals($data->type, 'permission_list');
        $this->assertEquals($data->data[0]->allowed, 1);

    }


    public function testStaffPermissions(): void
    {
        $id = $this->testing_accounts[0];

        $data = $this->NPRunSuccessJsonRequest('GET', "/member/$id/permissions");
        $this->assertEquals($data->type, 'permission_list');
        $this->assertEmpty($data->data);

        $staff_record = $this->runSuccessJsonRequest('POST', "/member/$id/staff_membership", null, ['Department' => 1, 'Position' => 1], 201);

        $data = $this->NPRunSuccessJsonRequest('GET', "/member/$id/permissions");
        $this->assertEquals($data->type, 'permission_list');
        $this->assertNotEmpty($data->data);

        $this->runRequest('DELETE', '/staff/membership/'.$staff_record->id, null, null, 204);

    }


    /* End */
}
