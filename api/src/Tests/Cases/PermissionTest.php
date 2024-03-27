<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;
use App\Tests\Base\TestRun;

class PermissionsTest extends CiabTestCase
{


    public function testPermissions(): void
    {
        testRun::testRun($this, 'GET', '/permissions/resource/{resource}/{department}/{method}')
            ->setUriParts(['resource' => 'deadline', 'department' => '2', 'method' => 'get'])
            ->run();
        testRun::testRun($this, 'GET', '/permissions/method/{resource}')
            ->setUriParts(['resource' => 'deadline'])
            ->run();

        testRun::testRun($this, 'GET', '/permissions/resource/{resource}/{department}/{method}')
            ->setUriParts(['resource' => 'announcement', 'department' => '2', 'method' => 'put'])
            ->run();
        testRun::testRun($this, 'GET', '/permissions/method/{resource}')
            ->setUriParts(['resource' => 'announcement'])
            ->run();

        $data = testRun::testRun($this, 'GET', '/member/{id}/permissions')
            ->setUriParts(['id' => '1'])
            ->run();
        $this->assertEquals($data->type, 'permission_list');
        $this->assertNotEmpty($data->data);

        $id = $this->testing_accounts[0];

        $data = testRun::testRun($this, 'GET', "/member/{id}/permissions")
            ->setUriParts(['id' => $id])
            ->setNpLoginIndex(0)
            ->run();
        $this->assertEquals($data->type, 'permission_list');
        $this->assertEmpty($data->data);

        $data = testRun::testRun($this, 'GET', '/permissions/generic/{resource}/{method}/{detail}')
            ->setUriParts(['resource' => 'announcement', 'method' => 'put', 'detail' => 'all'])
            ->setNpLoginIndex(0)
            ->run();
        $this->assertEquals($data->type, 'permission_list');
        $this->assertEquals($data->data[0]->allowed, 0);

        $data = testRun::testRun($this, 'GET', '/permissions/generic/{resource}/{method}/{detail}')
            ->setUriParts(['resource' => 'bad', 'method' => 'get', 'detail' => 'one'])
            ->setNpLoginIndex(0)
            ->run();
        $this->assertEquals($data->type, 'permission_list');
        $this->assertEquals($data->data[0]->allowed, 0);

        $data = testRun::testRun($this, 'GET', '/permissions/generic/{resource}/{method}/{detail}')
            ->setUriParts(['resource' => 'announcement', 'method' => 'put', 'detail' => 'all'])
            ->run();
        $this->assertEquals($data->type, 'permission_list');
        $this->assertEquals($data->data[0]->allowed, 1);

        $data = testRun::testRun($this, 'GET', '/permissions/generic/{resource}/{method}/{detail}')
            ->setUriParts(['resource' => 'bad', 'method' => 'get', 'detail' => 'one'])
            ->run();
        $this->assertEquals($data->type, 'permission_list');
        $this->assertEquals($data->data[0]->allowed, 1);

    }


    public function testStaffPermissions(): void
    {
        $id = $this->testing_accounts[0];

        $data = testRun::testRun($this, 'GET', "/member/{id}/permissions")
            ->setUriParts(['id' => $id])
            ->setNpLoginIndex(0)
            ->run();
        $this->assertEquals($data->type, 'permission_list');
        $this->assertEmpty($data->data);

        $staff_record = testRun::testRun($this, 'POST', "/member/{id}/staff_membership")
            ->setUriParts(['id' => $id])
            ->setBody(['Department' => 100, 'Position' => 3])
            ->run();
        $data3 = testRun::testRun($this, 'GET', "/member/{id}/permissions")
            ->setUriParts(['id' => $id])
            ->setNpLoginIndex(0)
            ->setMethodParameters(['max_results' => 'all'])
            ->run();
        $this->assertEquals($data3->type, 'permission_list');
        $this->assertNotEmpty($data3->data);
        $json_data3 = [];
        foreach ($data3->data as $entry) {
            $json_data3[] = json_encode($entry);
        }

        testRun::testRun($this, 'PUT', "/member/{id}/staff_membership")
            ->setUriParts(['id' => $id])
            ->setBody(['Department' => 100, 'Position' => 2])
            ->setNullReturn()
            ->run();
        $data2 = testRun::testRun($this, 'GET', "/member/{id}/permissions")
            ->setUriParts(['id' => $id])
            ->setMethodParameters(['max_results' => 'all'])
            ->setNpLoginIndex(0)
            ->run();
        $this->assertEquals($data2->type, 'permission_list');
        $this->assertNotEmpty($data2->data);
        $json_data2 = [];
        foreach ($data2->data as $entry) {
            $json_data2[] = json_encode($entry);
        }
        foreach ($json_data3 as $entry) {
            $this->assertContains($entry, $json_data2);
        }

        testRun::testRun($this, 'PUT', "/member/{id}/staff_membership")
            ->setUriParts(['id' => $id])
            ->setBody(['Department' => 100, 'Position' => 1])
            ->setNullReturn()
            ->run();
        $data1 = testRun::testRun($this, 'GET', "/member/{id}/permissions")
            ->setUriParts(['id' => $id])
            ->setNpLoginIndex(0)
            ->setMethodParameters(['max_results' => 'all'])
            ->run();
        $this->assertEquals($data1->type, 'permission_list');
        $this->assertNotEmpty($data1->data);
        $json_data1 = [];
        foreach ($data1->data as $entry) {
            $json_data1[] = json_encode($entry);
        }
        foreach ($json_data2 as $entry) {
            $this->assertContains($entry, $json_data1);
        }
        testRun::testRun($this, 'DELETE', '/staff/membership/{id}')
            ->setUriParts(['id' => $staff_record->id])
            ->run();

        /* parent division */
        $staff_record = testRun::testRun($this, 'POST', "/member/{id}/staff_membership")
            ->setUriParts(['id' => $id])
            ->setBody(['Department' => 4, 'Position' => 3])
            ->run();
        $data_p3 = testRun::testRun($this, 'GET', "/member/{id}/permissions")
            ->setUriParts(['id' => $id])
            ->setNpLoginIndex(0)
            ->setMethodParameters(['max_results' => 'all'])
            ->run();
        $this->assertEquals($data_p3->type, 'permission_list');
        $this->assertNotEmpty($data_p3->data);
        $json_data_p3 = [];
        foreach ($data_p3->data as $entry) {
            $json_data_p3[] = json_encode($entry);
        }
        foreach ($json_data1 as $entry) {
            $this->assertContains($entry, $json_data_p3);
        }

        testRun::testRun($this, 'PUT', "/member/{id}/staff_membership")
            ->setUriParts(['id' => $id])
            ->setBody(['Department' => 4, 'Position' => 2])
            ->setNullReturn()
            ->run();
        $data_p2 = testRun::testRun($this, 'GET', "/member/{id}/permissions")
            ->setUriParts(['id' => $id])
            ->setNpLoginIndex(0)
            ->setMethodParameters(['max_results' => 'all'])
            ->run();
        $this->assertEquals($data_p2->type, 'permission_list');
        $this->assertNotEmpty($data_p2->data);
        $json_data_p2 = [];
        foreach ($data_p2->data as $entry) {
            $json_data_p2[] = json_encode($entry);
        }
        foreach ($json_data_p3 as $entry) {
            $this->assertContains($entry, $json_data_p2);
        }

        testRun::testRun($this, 'PUT', "/member/{id}/staff_membership")
            ->setUriParts(['id' => $id])
            ->setBody(['Department' => 4, 'Position' => 1])
            ->setNullReturn()
            ->run();
        $data_p1 = testRun::testRun($this, 'GET', "/member/{id}/permissions")
            ->setUriParts(['id' => $id])
            ->setMethodParameters(['max_results' => 'all'])
            ->run();
        $this->assertEquals($data_p1->type, 'permission_list');
        $this->assertNotEmpty($data_p1->data);
        $json_data_p1 = [];
        foreach ($data_p1->data as $entry) {
            $json_data_p1[] = json_encode($entry);
        }
        foreach ($json_data_p2 as $entry) {
            $this->assertContains($entry, $json_data_p1);
        }
        testRun::testRun($this, 'DELETE', '/staff/membership/{id}')
            ->setUriParts(['id' => $staff_record->id])
            ->run();

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
