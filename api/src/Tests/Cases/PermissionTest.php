<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;

class PermissionsTest extends CiabTestCase
{


    public function testPermissions(): void
    {
        $this->runSuccessJsonRequest('GET', '/permissions/resource/deadline/2/get');
        $this->runSuccessJsonRequest('GET', '/permissions/method/deadline');

        $this->runSuccessJsonRequest('GET', '/permissions/resource/announcement/2/put');
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

        $staff_record = $this->runSuccessJsonRequest('POST', "/member/$id/staff_membership", null, ['Department' => 100, 'Position' => 3], 201);
        $data3 = $this->NPRunSuccessJsonRequest('GET', "/member/$id/permissions", ['max_results' => 'all']);
        $this->assertEquals($data3->type, 'permission_list');
        $this->assertNotEmpty($data3->data);
        $json_data3 = [];
        foreach ($data3->data as $entry) {
            $json_data3[] = json_encode($entry);
        }

        $this->runSuccessRequest('PUT', "/member/$id/staff_membership", null, ['Department' => 100, 'Position' => 2], 200);
        $data2 = $this->NPRunSuccessJsonRequest('GET', "/member/$id/permissions", ['max_results' => 'all']);
        $this->assertEquals($data2->type, 'permission_list');
        $this->assertNotEmpty($data2->data);
        $json_data2 = [];
        foreach ($data2->data as $entry) {
            $json_data2[] = json_encode($entry);
        }
        foreach ($json_data3 as $entry) {
            $this->assertContains($entry, $json_data2);
        }

        $this->runSuccessRequest('PUT', "/member/$id/staff_membership", null, ['Department' => 100, 'Position' => 1], 200);
        $data1 = $this->NPRunSuccessJsonRequest('GET', "/member/$id/permissions", ['max_results' => 'all']);
        $this->assertEquals($data1->type, 'permission_list');
        $this->assertNotEmpty($data1->data);
        $json_data1 = [];
        foreach ($data1->data as $entry) {
            $json_data1[] = json_encode($entry);
        }
        foreach ($json_data2 as $entry) {
            $this->assertContains($entry, $json_data1);
        }
        $this->runRequest('DELETE', '/staff/membership/'.$staff_record->id, null, null, 204);

        /* parent division */
        $staff_record = $this->runSuccessJsonRequest('POST', "/member/$id/staff_membership", null, ['Department' => 4, 'Position' => 3], 201);
        $data_p3 = $this->NPRunSuccessJsonRequest('GET', "/member/$id/permissions", ['max_results' => 'all']);
        $this->assertEquals($data_p3->type, 'permission_list');
        $this->assertNotEmpty($data_p3->data);
        $json_data_p3 = [];
        foreach ($data_p3->data as $entry) {
            $json_data_p3[] = json_encode($entry);
        }
        foreach ($json_data1 as $entry) {
            $this->assertContains($entry, $json_data_p3);
        }

        $this->runSuccessRequest('PUT', "/member/$id/staff_membership", null, ['Department' => 4, 'Position' => 2], 200);
        $data_p2 = $this->NPRunSuccessJsonRequest('GET', "/member/$id/permissions", ['max_results' => 'all']);
        $this->assertEquals($data_p2->type, 'permission_list');
        $this->assertNotEmpty($data_p2->data);
        $json_data_p2 = [];
        foreach ($data_p2->data as $entry) {
            $json_data_p2[] = json_encode($entry);
        }
        foreach ($json_data_p3 as $entry) {
            $this->assertContains($entry, $json_data_p2);
        }

        $this->runSuccessRequest('PUT', "/member/$id/staff_membership", null, ['Department' => 4, 'Position' => 1], 200);
        $data_p1 = $this->NPRunSuccessJsonRequest('GET', "/member/$id/permissions", ['max_results' => 'all']);
        $this->assertEquals($data_p1->type, 'permission_list');
        $this->assertNotEmpty($data_p1->data);
        $json_data_p1 = [];
        foreach ($data_p1->data as $entry) {
            $json_data_p1[] = json_encode($entry);
        }
        foreach ($json_data_p2 as $entry) {
            $this->assertContains($entry, $json_data_p1);
        }

    }


    public function testInheritPermissions(): void
    {
        $this->container->RBAC->registerPermissions(['Test.One', 'Test.Two']);
        $this->container->RBAC->grantPermission('all.staff', 'Test.One');
        $o = $this->container->RBAC->getPermissions('all.staff', false);
        $this->assertContains("Test.One", $o);
        $this->container->RBAC->grantPermission('all.1', 'Test.Two');
        $o = $this->container->RBAC->getPermissions('all.1', true);
        $this->assertContains("Test.Two", $o);
        $o = $this->container->RBAC->positionHasPermission('admin', 'Test.Two');
        $this->assertTrue($o);
        $o = $this->container->RBAC->getPermissions('1.1', true);
        $this->assertContains("Test.One", $o);
        $this->assertContains("Test.Two", $o);
        $o = $this->container->RBAC->positionHasPermission('1.1', 'Test.One');
        $this->assertTrue($o);
        $o = $this->container->RBAC->positionHasPermission('1.1', 'Test.Two');
        $this->assertTrue($o);
        $o = $this->container->RBAC->getPermissions('2.2', true);
        $this->assertContains("Test.One", $o);
        $this->assertContains("Test.Two", $o);
        $o = $this->container->RBAC->positionHasPermission('2.2', 'Test.One');
        $this->assertTrue($o);
        $o = $this->container->RBAC->positionHasPermission('2.2', 'Test.Two');
        $this->assertTrue($o);
        $o = $this->container->RBAC->getPermissions('105.1', true);
        $this->assertContains("Test.One", $o);
        $this->assertContains("Test.Two", $o);
        $o = $this->container->RBAC->positionHasPermission('105.1', 'Test.One');
        $this->assertTrue($o);
        $o = $this->container->RBAC->positionHasPermission('105.1', 'Test.Two');
        $this->assertTrue($o);
        $o = $this->container->RBAC->positionHasPermission('105.2', 'Test.One');
        $this->assertTrue($o);
        $o = $this->container->RBAC->positionHasPermission('105.2', 'Test.Two');
        $this->assertFalse($o);
        $o = $this->container->RBAC->getPermissions('105.2', true);
        $this->assertContains("Test.One", $o);
        $this->assertNotContains("Test.Two", $o);

    }


    /* End */
}
