<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;
use App\Tests\Base\TestRun;

class DepartmentTest extends CiabTestCase
{


    public function testGetDepartmentList(): void
    {
        testRun::testRun($this, 'GET', '/department')->run();
        $data = testRun::testRun($this, 'GET', '/department/{id}')
            ->setUriParts(['id' => 'Activities'])
            ->run();
        $data2 = testRun::testRun($this, 'GET', '/department/{id}')
            ->setUriParts(['id' => 2])
            ->run();
        $this->assertEquals($data, $data2);
        testRun::testRun($this, 'GET', '/department/{id}/children')
            ->setUriParts(['id' => 2])
            ->run();
        $data = testRun::testRun($this, 'GET', '/department/{id}')
            ->setUriParts(['id' => 100])
            ->run();
        $this->assertIsObject($data->parent);
        $this->assertIncludes($data, 'parent');

    }


    public function testGetDepartmentByName(): void
    {
        testRun::testRun($this, 'GET', '/department/{id}')
            ->setUriParts(['id' => -1])
            ->setExpectedResult(404)
            ->run();
        testRun::testRun($this, 'GET', '/department/{id}')
            ->setUriParts(['id' => 'not-a-department'])
            ->setExpectedResult(404)
            ->run();

    }


    public function testGetDepartmentById(): void
    {
        $data = testRun::testRun($this, 'GET', '/department/{id}')
            ->setUriParts(['id' => 2])
            ->run();
        $this->assertEquals("2", $data->id);

    }


    public function testGetDepartmentByIdOrName(): void
    {
        $dataByName = testRun::testRun($this, 'GET', '/department/{id}')
            ->setUriParts(['id' => 'Activities'])
            ->run();
        $dataById = testRun::testRun($this, 'GET', '/department/{id}')
            ->setUriParts(['id' => 2])
            ->run();
        $this->assertEquals($dataByName, $dataById);

    }


    public function testGetDepartmentChildren(): void
    {
        testRun::testRun($this, 'GET', '/department/{id}/children')
            ->setUriParts(['id' => 2])
            ->run();

    }


    public function testGetDepartmentWithParent(): void
    {
        $data = testRun::testRun($this, 'GET', '/department/{id}')
            ->setUriParts(['id' => 100])
            ->run();
        $this->assertEquals('100', $data->id);
        $this->assertIncludes($data, 'parent');
        $this->assertIsObject($data->parent);

    }


    public function testPostDepartment(): void
    {
        $body = [
            "Name" => "Test Department"
        ];

        $data = testRun::testRun($this, 'POST', '/department')
            ->setBody($body)
            ->run();
        $this->assertEquals('Test Department', $data->name);
        $this->assertNotEmpty($data->id);
        $this->assertEmpty($data->parent);
        $this->assertEmpty($data->fallback);

    }


    public function testPostDepartmentWithParent(): void
    {
        $parentDept = [
            "Name" => "Test Parent Department"
        ];

        $parentData = testRun::testRun($this, 'POST', '/department')
            ->setBody($parentDept)
            ->run();

        $withParentRef = [
            "Name" => "Test Department with Parent",
            "ParentID" => $parentData->id
        ];

        $withParentData = testRun::testRun($this, 'POST', '/department')
            ->setBody($withParentRef)
            ->run();
        $this->assertEquals('Test Department with Parent', $withParentData->name);
        $this->assertNotEmpty($withParentData->id);
        $this->assertEmpty($withParentData->fallback);
        $this->assertEquals($parentData->id, $withParentData->parent->id);

    }


    public function testPostDepartmentWithFallback(): void
    {
        $fallbackDept = [
            "Name" => "Test Fallback Department"
        ];

        $fallbackData = testRun::testRun($this, 'POST', '/department')
            ->setBody($fallbackDept)
            ->run();

        $withFallbackRef = [
            "Name" => "Test Department with Fallback",
            "FallbackID" => $fallbackData->id
        ];

        $withFallbackData = testRun::testRun($this, 'POST', '/department')
            ->setBody($withFallbackRef)
            ->run();
        $this->assertEquals('Test Department with Fallback', $withFallbackData->name);
        $this->assertNotEmpty($withFallbackData->id);
        $this->assertEmpty($withFallbackData->parent);
        $this->assertEquals($fallbackData->id, $withFallbackData->fallback->id);

    }


    public function testPostDepartmentWithParentAndFallback(): void
    {
        $parentDept = [
            "Name" => "Test Parent Department"
        ];

        $parentData = testRun::testRun($this, 'POST', '/department')
            ->setBody($parentDept)
            ->run();

        $fallbackDept = [
            "Name" => "Test Fallback Department"
        ];

        $fallbackData = testRun::testRun($this, 'POST', '/department')
            ->setBody($fallbackDept)
            ->run();

        $withParentAndFallbackRef = [
            "Name" => "Test Department with Parent and Fallback",
            "ParentID" => $parentData->id,
            "FallbackID" => $fallbackData->id
        ];

        $withParentFallbackData = testRun::testRun($this, 'POST', '/department')
            ->setBody($withParentAndFallbackRef)
            ->run();
        $this->assertEquals('Test Department with Parent and Fallback', $withParentFallbackData->name);
        $this->assertNotEmpty($withParentFallbackData->id);
        $this->assertEquals($parentData->id, $withParentFallbackData->parent->id);
        $this->assertEquals($fallbackData->id, $withParentFallbackData->fallback->id);

    }


    public function testPutDepartmentUpdateNameOnly(): void
    {
        $department = [
            "Name" => "Department Name"
        ];

        $departmentData = testRun::testRun($this, 'POST', '/department')
            ->setBody($department)
            ->run();

        $updatedDepartment = [
            "Name" => "Updated Department Name"
        ];

        $updatedData = testRun::testRun($this, 'PUT', "/department/{id}")
            ->setUriParts(['id' => $departmentData->id])
            ->setBody($updatedDepartment)
            ->run();
        $this->assertEquals($departmentData->id, $updatedData->id);
        $this->assertEquals("Updated Department Name", $updatedData->name);

    }


    public function testPutDepartmentUpdateParentOnly(): void
    {
        $department = [
            "Name" => "Parent Department"
        ];
        $departmentData = testRun::testRun($this, 'POST', '/department')
            ->setBody($department)
            ->run();

        $childDepartment = [
            "Name" => "Child Department"
        ];
        $childDepartmentData = testRun::testRun($this, 'POST', '/department')
            ->setBody($childDepartment)
            ->run();

        $updatedChild = [
            "ParentID" => $departmentData->id
        ];

        $updatedChildData = testRun::testRun($this, 'PUT', "/department/{id}")
            ->setUriParts(['id' => $childDepartmentData->id])
            ->setBody($updatedChild)
            ->run();
        $this->assertEquals($childDepartmentData->id, $updatedChildData->id);
        $this->assertEquals($departmentData->id, $updatedChildData->parent->id);

    }


    public function testPutDepartmentUpdateFallbackOnly(): void
    {
        $department = [
            "Name" => "Fallback Department"
        ];
        $departmentData = testRun::testRun($this, 'POST', '/department')
            ->setBody($department)
            ->run();

        $departmentWithFallback = [
            "Name" => "Department with Fallback"
        ];
        $departmentFallbackData = testRun::testRun($this, 'POST', '/department')
            ->setBody($departmentWithFallback)
            ->run();

        $updatedDepartmentWithFallback = [
            "FallbackID" => $departmentData->id
        ];

        $updatedDepartmentData = testRun::testRun($this, 'PUT', "/department/{id}")
            ->setUriParts(['id' => $departmentFallbackData->id])
            ->setBody($updatedDepartmentWithFallback)
            ->run();
        $this->assertEquals($departmentFallbackData->id, $updatedDepartmentData->id);
        $this->assertEquals($departmentData->id, $updatedDepartmentData->fallback->id);

    }


    public function testDeleteDepartment(): void
    {
        $department = [
            "Name" => "Test Delete Department"
        ];
        $departmentData = testRun::testRun($this, 'POST', '/department')
            ->setBody($department)
            ->run();

        testRun::testRun($this, 'DELETE', "/department/{id}")
            ->setUriParts(["id" => $departmentData->id])
            ->setVerifyYaml(true)
            ->run();

    }


    public function testGetDepartmentInvalidId(): void
    {
        testRun::testRun($this, 'GET', '/department/{id}')
            ->setUriParts(['id' => -1])
            ->setExpectedResult(404)
            ->run();

    }


    public function testGetDepartmentInvalidName(): void
    {
        testRun::testRun($this, 'GET', '/department/{id}')
            ->setUriParts(['id' => 'not-real-department'])
            ->setExpectedResult(404)
            ->run();

    }


    public function testPostDepartmentMissingName(): void
    {
        $body = [];
        testRun::testRun($this, 'POST', '/department')
            ->setBody($body)
            ->setExpectedResult(400)
            ->run();

    }


    public function testPostDepartmentInvalidParentId(): void
    {
        $body = [
            "Name" => "Test Department",
            "ParentID" => -1
        ];
        testRun::testRun($this, 'POST', '/department')
            ->setBody($body)
            ->setExpectedResult(400)
            ->run();

    }


    public function testPostDepartmentInvalidFallbackId(): void
    {
        $body = [
            "Name" => "Test Department",
            "FallbackID" => -1
        ];
        testRun::testRun($this, 'POST', '/department')
            ->setBody($body)
            ->setExpectedResult(400)
            ->run();

    }


    public function testPutDepartmentInvalidDepartmentIdNumber(): void
    {
        $body = [
            "Name" => "Updated Department Name"
        ];

        testRun::testRun($this, 'PUT', '/department/{id}')
            ->setUriParts(['id' => -1])
            ->setBody($body)
            ->setExpectedResult(400)
            ->run();

    }


    public function testPutDepartmentInvalidDepartmentIdValue(): void
    {
        $body = [
            "Name" => "Updated Department Name"
        ];

        // Not validating against OpenAPI schema because it calls this invalid, but we should still protect against it
        testRun::testRun($this, 'PUT', '/department/{id}')
            ->setUriParts(['id' => 'not-an-id'])
            ->setBody($body)
            ->setExpectedResult(400)
            ->setVerifyYaml(false)
            ->run();

    }


    public function testPutDepartmentWithEmptyDepartmentName(): void
    {
        $department = [
            "Name" => "Department Name"
        ];
        $departmentData = testRun::testRun($this, 'POST', '/department')
            ->setBody($department)
            ->run();

        $body = [
            "Name" => ""
        ];

        testRun::testRun($this, 'PUT', "/department/{id}")
            ->setUriParts(['id' => $departmentData->id])
            ->setBody($body)
            ->setExpectedResult(400)
            ->run();

    }


    public function testPutDepartmentWithInvalidDepartmentName(): void
    {
        $department = [
            "Name" => "Department Name"
        ];
        $departmentData = testRun::testRun($this, 'POST', '/department')
            ->setBody($department)
            ->run();

        $body = [
            "Name" => "               "
        ];

        testRun::testRun($this, 'PUT', "/department/{id}")
            ->setUriParts(['id' => $departmentData->id])
            ->setBody($body)
            ->setExpectedResult(400)
            ->run();

    }


    public function testPutDepartmentInvalidParentID(): void
    {
        $department = [
            "Name" => "Department Name"
        ];
        $departmentData = testRun::testRun($this, 'POST', '/department')
            ->setBody($department)
            ->run();

        $body = [
            "ParentID" => -1
        ];

        testRun::testRun($this, 'PUT', "/department/{id}")
            ->setUriParts(['id' => $departmentData->id])
            ->setBody($body)
            ->setExpectedResult(400)
            ->run();

    }


    public function testPutDepartmentInvalidParentIDValue(): void
    {
        $department = [
            "Name" => "Department Name"
        ];
        $departmentData = testRun::testRun($this, 'POST', '/department')
            ->setBody($department)
            ->run();

        $body = [
            "ParentID" => 'Parent Department'
        ];

        // Not validating against OpenAPI schema because it calls this invalid, but we should still protect against it
        testRun::testRun($this, 'PUT', "/department/{id}")
            ->setUriParts(['id' => $departmentData->id])
            ->setBody($body)
            ->setExpectedResult(400)
            ->setVerifyYaml(false)
            ->run();

    }


    public function testPutDepartmentInvalidFallbackID(): void
    {
        $department = [
            "Name" => "Department Name"
        ];
        $departmentData = testRun::testRun($this, 'POST', '/department')
            ->setBody($department)
            ->run();

        $body = [
            "FallbackID" => -1
        ];

        testRun::testRun($this, 'PUT', "/department/{id}")
            ->setUriParts(['id' => $departmentData->id])
            ->setBody($body)
            ->setExpectedResult(400)
            ->run();

    }


    public function testPutDepartmentInvalidFallbackIDValue(): void
    {
        $department = [
            "Name" => "Department Name"
        ];
        $departmentData = testRun::testRun($this, 'POST', '/department')
            ->setBody($department)
            ->run();

        $body = [
            "FallbackID" => "Fallback Department"
        ];

        // Not validating against OpenAPI schema because it calls this invalid, but we should still protect against it
        testRun::testRun($this, 'PUT', "/department/{id}")
            ->setUriParts(['id' => $departmentData->id])
            ->setBody($body)
            ->setExpectedResult(400)
            ->setVerifyYaml(false)
            ->run();

    }


    public function testPutDepartmentNoRequestBodyParams(): void
    {
        $department = [
            "Name" => "Department Name"
        ];
        $departmentData = testRun::testRun($this, 'POST', '/department')
            ->setBody($department)
            ->run();

        $updateBody = [];
        testRun::testRun($this, 'PUT', "/department/{id}")
            ->setUriParts(['id' => $departmentData->id])
            ->setBody($updateBody)
            ->setExpectedResult(400)
            ->run();

    }


    public function testPutDepartmentNoValidRequestBodyParams(): void
    {
        $department = [
            "Name" => "Department Name"
        ];
        $departmentData = testRun::testRun($this, 'POST', '/department')
            ->setBody($department)
            ->run();

        $updateBody = [
            "Foo" => "Bar"
        ];
        testRun::testRun($this, 'PUT', "/department/{id}")
            ->setUriParts(['id' => $departmentData->id])
            ->setBody($updateBody)
            ->setExpectedResult(400)
            ->run();

    }


    public function testDeleteDepartmentInvalidId(): void
    {
        testRun::testRun($this, 'DELETE', '/department/{id}')
            ->setUriParts(["id" => "-1"])
            ->setExpectedResult(400)
            ->setVerifyYaml(true)
            ->run();

    }


    public function testDeleteDepartmentInvalidVeryHighId(): void
    {
        testRun::testRun($this, 'DELETE', '/department/{id}')
            ->setUriParts(["id" => "99999"])
            ->setVerifyYaml(true)
            ->run();

    }


    public function testGetDepartmentEmail(): void
    {
        $emailBody = [
            "Email" => "test-email@example.com",
            "DepartmentID" => 2
        ];
        $emailResult = testRun::testRun($this, 'POST', '/email')
            ->setBody($emailBody)
            ->setVerifyYaml(false)
            ->run();

        $result = testRun::testRun($this, 'GET', '/department/{id}/email')
            ->setUriParts(["id" => 2])
            ->run();

        $this->assertEquals($emailResult->id, $result->data[0]->id);
        $this->assertEquals($emailResult->departmentId, $result->data[0]->departmentId);
        $this->assertEquals($emailResult->email, $result->data[0]->email);
        $this->assertEquals($emailResult->isAlias, $result->data[0]->isAlias);

        testRun::testRun($this, 'DELETE', '/email/{id}')
            ->setUriParts(["id" => $emailResult->id])
            ->setVerifyYaml(false)
            ->run();

    }


    public function testGetDepartmentEmailInvalidId(): void
    {
        testRun::testRun($this, 'GET', '/department/{id}/email')
            ->setUriParts(["id" => -1])
            ->setExpectedResult(400)
            ->run();

    }


    public function testGetDepartmentPermissions(): void
    {
        $result = testRun::testRun($this, 'GET', '/department/{id}/permission')
            ->setUriParts(["id" => 2])
            ->run();

        foreach ($result->data as $permission) {
            $deptValue = $permission->departmentId;
            $this->assertTrue($deptValue == "all" || $deptValue == 2);
        }

    }


    public function testGetDepartmentPermissionsNewDept(): void
    {
        $result = testRun::testRun($this, 'GET', '/department/{id}/permission')
            ->setUriParts(["id" => -1])
            ->run();

        foreach ($result->data as $permission) {
            $this->assertTrue($permission->departmentId == "all");
        }

    }


    public function testPostDepartmentPermissionWithDeptId(): void
    {
        $data = [
            "PositionID" => 1,
            "Permission" => "concom.view"
        ];

        $result = testRun::testRun($this, 'POST', '/department/{id}/permission')
            ->setUriParts(["id" => "2"])
            ->setBody($data)
            ->run();

        $this->assertNotNull($result->id);
        $this->assertEquals(2, $result->departmentId);
        $this->assertEquals(1, $result->position);
        $this->assertEquals("concom.view", $result->name);
        $this->assertNull($result->note);

    }


    public function testPostDepartmentPermissionWithAll(): void
    {
        $data = [
            "PositionID" => 1,
            "Permission" => "concom.view"
        ];

        $result = testRun::testRun($this, 'POST', '/department/{id}/permission')
            ->setUriParts(["id" => "all"])
            ->setBody($data)
            ->run();

        $this->assertNotNull($result->id);
        $this->assertEquals("all", $result->departmentId);

    }


    public function testPostDepartmentPermissionInvalidDeptId(): void
    {
        $data = [
            "PositionID" => 1,
            "Permission" => "concom.view"
        ];

        testRun::testRun($this, 'POST', '/department/{id}/permission')
            ->setUriParts(["id" => "invalid"])
            ->setBody($data)
            ->setExpectedResult(400)
            ->run();

    }


    public function testPostDepartmentPermissionNegativeDeptId(): void
    {
        $data = [
            "PositionID" => 1,
            "Permission" => "concom.view"
        ];

        testRun::testRun($this, 'POST', '/department/{id}/permission')
            ->setUriParts(["id" => "-1"])
            ->setBody($data)
            ->setExpectedResult(400)
            ->run();

    }


    public function testPostDepartmentPermissionMissingPosition(): void
    {
        $data = [
            "Permission" => "concom.view"
        ];

        testRun::testRun($this, 'POST', '/department/{id}/permission')
            ->setUriParts(["id" => "2"])
            ->setBody($data)
            ->setExpectedResult(400)
            ->run();

    }


    public function testPostDepartmentPermissionMissingPermission(): void
    {
        $data = [
            "PositionID" => 1
        ];

        testRun::testRun($this, 'POST', '/department/{id}/permission')
            ->setUriParts(["id" => "all"])
            ->setBody($data)
            ->setExpectedResult(400)
            ->run();

    }


    public function testPostDepartmentPermissionEmptyPermission(): void
    {
        $data = [
            "PositionID" => 1,
            "Permission" => ""
        ];

        testRun::testRun($this, 'POST', '/department/{id}/permission')
            ->setUriParts(["id" => "all"])
            ->setBody($data)
            ->setExpectedResult(400)
            ->run();
            
    }


    public function testDeleteDepartmentPermission(): void
    {
        $data = [
            "PositionID" => 1,
            "Permission" => "concom.view"
        ];

        $result = testRun::testRun($this, 'POST', '/department/{id}/permission')
            ->setUriParts(["id" => "all"])
            ->setBody($data)
            ->run();

        testRun::testRun($this, 'DELETE', '/department/{id}/permission/{permissionId}')
            ->setUriParts(["id" => "all", "permissionId" => $result->id])
            ->run();

    }


    public function testDeleteDepartmentPermissionInvalidDeptId(): void
    {
        testRun::testRun($this, 'DELETE', '/department/{id}/permission/{permissionId}')
            ->setUriParts(["id" => "invalid", "permissionId" => 123])
            ->setExpectedResult(400)
            ->run();

    }


    public function testDeleteDepartmentPermissionNegativeDeptId(): void
    {
        testRun::testRun($this, 'DELETE', '/department/{id}/permission/{permissionId}')
            ->setUriParts(["id" => "-2", "permissionId" => 123])
            ->setExpectedResult(400)
            ->run();

    }


    public function testDeleteDepartmentPermissionNegativePermissionId(): void
    {
        testRun::testRun($this, 'DELETE', '/department/{id}/permission/{permissionId}')
            ->setUriParts(["id" => "all", "permissionId" => -1])
            ->setExpectedResult(400)
            ->run();

    }


    /* End */
}
