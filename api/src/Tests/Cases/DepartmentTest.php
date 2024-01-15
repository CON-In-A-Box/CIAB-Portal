<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;

class DepartmentTest extends CiabTestCase
{


    public function testGetDepartmentList(): void
    {
        $data = $this->runSuccessJsonRequest('GET', '/department', null, null, 200, null, '/department');
        $this->assertNotEmpty($data->data);
        $data = $this->runSuccessJsonRequest('GET', '/department/Activities', null, null, 200, null, '/department/{id}');
        $data2 = $this->runSuccessJsonRequest('GET', '/department/2', null, null, 200, null, '/department/{id}');
        $this->assertEquals($data, $data2);
        $this->runSuccessJsonRequest('GET', '/department/2/children', null, null, 200, null, '/department/{id}/children');
        $this->runSuccessJsonRequest('GET', '/department');
        $data = $this->runSuccessJsonRequest(
            'GET',
            '/department/100'
        );
        $this->assertIsObject($data->parent);
        $this->assertIncludes($data, 'parent');

    }


    public function testGetDepartmentByName(): void
    {
        $this->runRequest('GET', '/department/-1', null, null, 404, null, '/department/{id}');
        $this->runRequest('GET', '/department/not-a-dept', null, null, 404, null, '/department/{id}');

    }


    public function testGetDepartmentById(): void
    {
        $data = $this->runSuccessJsonRequest('GET', '/department/2', null, null, 200, null, '/department/{id}');
        $this->assertEquals("2", $data->id);

    }


    public function testGetDepartmentByIdOrName(): void
    {
        $dataByName = $this->runSuccessJsonRequest('GET', '/department/Activities');
        $dataById = $this->runSuccessJsonRequest('GET', '/department/2');
        $this->assertEquals($dataByName, $dataById);

    }

    
    public function testGetDepartmentChildren(): void
    {
        $data = $this->runSuccessJsonRequest('GET', '/department/2/children', null, null, 200, null, '/department/{id}/children');
        $this->assertNotEmpty($data->data);

    }


    public function testGetDepartmentWithParent(): void
    {
        $data = $this->runSuccessJsonRequest('GET', '/department/100');
        $this->assertEquals('100', $data->id);
        $this->assertIncludes($data, 'parent');
        $this->assertIsObject($data->parent);

    }


    public function testPostDepartment(): void
    {
        $body = [
            "Name" => "Test Department"
        ];

        $data = $this->runSuccessJsonRequest('POST', '/department', null, $body, 201, null, '/department');
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

        $parentData = $this->runSuccessJsonRequest('POST', '/department', null, $parentDept, 201, null);

        $withParentRef = [
            "Name" => "Test Department with Parent",
            "ParentID" => $parentData->id
        ];

        $withParentData = $this->runSuccessJsonRequest('POST', '/department', null, $withParentRef, 201, null, '/department');
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

        $fallbackData = $this->runSuccessJsonRequest('POST', '/department', null, $fallbackDept, 201, null);

        $withFallbackRef = [
            "Name" => "Test Department with Fallback",
            "FallbackID" => $fallbackData->id
        ];

        $withFallbackData = $this->runSuccessJsonRequest('POST', '/department', null, $withFallbackRef, 201, null, '/department');
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

        $parentData = $this->runSuccessJsonRequest('POST', '/department', null, $parentDept, 201, null);

        $fallbackDept = [
            "Name" => "Test Fallback Department"
        ];

        $fallbackData = $this->runSuccessJsonRequest('POST', '/department', null, $fallbackDept, 201, null);

        $withParentAndFallbackRef = [
            "Name" => "Test Department with Parent and Fallback",
            "ParentID" => $parentData->id,
            "FallbackID" => $fallbackData->id
        ];

        $withParentFallbackData = $this->runSuccessJsonRequest('POST', '/department', null, $withParentAndFallbackRef, 201, null);
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

        $departmentData = $this->runSuccessJsonRequest('POST', '/department', null, $department, 201, null);

        $updatedDepartment = [
            "Name" => "Updated Department Name"
        ];

        $updatedData = $this->runSuccessJsonRequest('PUT', "/department/$departmentData->id", null, $updatedDepartment, 200, null, '/department/{id}');
        $this->assertEquals($departmentData->id, $updatedData->id);
        $this->assertEquals("Updated Department Name", $updatedData->name);

    }


    public function testPutDepartmentUpdateParentOnly(): void
    {
        $department = [
            "Name" => "Parent Department"
        ];
        $departmentData = $this->runSuccessJsonRequest('POST', '/department', null, $department, 201, null);

        $childDepartment = [
            "Name" => "Child Department"
        ];
        $childDepartmentData = $this->runSuccessJsonRequest('POST', '/department', null, $childDepartment, 201, null);

        $updatedChild = [
            "ParentID" => $departmentData->id
        ];
        
        $updatedChildData = $this->runSuccessJsonRequest('PUT', "/department/$childDepartmentData->id", null, $updatedChild, 200, null);
        $this->assertEquals($childDepartmentData->id, $updatedChildData->id);
        $this->assertEquals($departmentData->id, $updatedChildData->parent->id);

    }


    public function testPutDepartmentUpdateFallbackOnly(): void
    {
        $department = [
            "Name" => "Fallback Department"
        ];
        $departmentData = $this->runSuccessJsonRequest('POST', '/department', null, $department, 201, null);

        $departmentWithFallback = [
            "Name" => "Department with Fallback"
        ];
        $departmentFallbackData = $this->runSuccessJsonRequest('POST', '/department', null, $departmentWithFallback, 201, null);

        $updatedDepartmentWithFallback = [
            "FallbackID" => $departmentData->id
        ];

        $updatedDepartmentData = $this->runSuccessJsonRequest('PUT', "/department/$departmentFallbackData->id", null, $updatedDepartmentWithFallback, 200, null);
        $this->assertEquals($departmentFallbackData->id, $updatedDepartmentData->id);
        $this->assertEquals($departmentData->id, $updatedDepartmentData->fallback->id);

    }


    public function testGetDepartmentInvalidId(): void
    {
        $this->runRequest('GET', '/department/-1', null, null, 404, null, '/department/{id}');

    }


    public function testGetDepartmentInvalidName(): void
    {
        $this->runRequest('GET', '/department/not-real-department', null, null, 404, null, '/department/{id}');

    }

    
    public function testPostDepartmentMissingName(): void
    {
        $body = [];
        $this->runRequest('POST', '/department', null, $body, 400, null, '/department');

    }


    public function testPostDepartmentInvalidParentId(): void
    {
        $body = [
            "Name" => "Test Department",
            "ParentID" => -1
        ];
        $this->runRequest('POST', '/department', null, $body, 400, null, '/department');

    }

    
    public function testPostDepartmentInvalidFallbackId(): void
    {
        $body = [
            "Name" => "Test Department",
            "FallbackID" => -1
        ];
        $this->runRequest('POST', '/department', null, $body, 400, null, '/department');
        
    }


    public function testPutDepartmentInvalidDepartmentIdNumber(): void
    {
        $body = [
            "Name" => "Updated Department Name"
        ];

        $this->runRequest('PUT', '/department/-1', null, $body, 400, null, '/department/{id}');

    }


    public function testPutDepartmentInvalidDepartmentIdValue(): void
    {
        $body = [
            "Name" => "Updated Department Name"
        ];

        // Not validating against OpenAPI schema because it calls this invalid, but we should still protect against it
        $this->runRequest('PUT', '/department/not-an-id', null, $body, 400, null);

    }


    public function testPutDepartmentWithEmptyDepartmentName(): void
    {
        $department = [
            "Name" => "Department Name"
        ];
        $departmentData = $this->runSuccessJsonRequest('POST', '/department', null, $department, 201, null, null);

        $body = [
            "Name" => ""
        ];

        $this->runRequest('PUT', "/department/$departmentData->id", null, $body, 400, null, '/department/{id}');

    }


    public function testPutDepartmentWithInvalidDepartmentName(): void
    {
        $department = [
            "Name" => "Department Name"
        ];
        $departmentData = $this->runSuccessJsonRequest('POST', '/department', null, $department, 201, null, null);

        $body = [
            "Name" => "               "
        ];

        $this->runRequest('PUT', "/department/$departmentData->id", null, $body, 400, null, '/department/{id}');

    }


    public function testPutDepartmentInvalidParentID(): void
    {
        $department = [
            "Name" => "Department Name"
        ];
        $departmentData = $this->runSuccessJsonRequest('POST', '/department', null, $department, 201, null, null);

        $body = [
            "ParentID" => -1
        ];

        $this->runRequest('PUT', "/department/$departmentData->id", null, $body, 400, null, '/department/{id}');

    }


    public function testPutDepartmentInvalidParentIDValue(): void
    {
        $department = [
            "Name" => "Department Name"
        ];
        $departmentData = $this->runSuccessJsonRequest('POST', '/department', null, $department, 201, null, null);

        $body = [
            "ParentID" => 'Parent Department'
        ];

        // Not validating against OpenAPI schema because it calls this invalid, but we should still protect against it
        $this->runRequest('PUT', "/department/$departmentData->id", null, $body, 400, null);

    }


    public function testPutDepartmentInvalidFallbackID(): void
    {
        $department = [
            "Name" => "Department Name"
        ];
        $departmentData = $this->runSuccessJsonRequest('POST', '/department', null, $department, 201, null, null);
        
        $body = [
            "FallbackID" => -1
        ];

        $this->runRequest('PUT', "/department/$departmentData->id", null, $body, 400, null, '/department/{id}');

    }


    public function testPutDepartmentInvalidFallbackIDValue(): void
    {
        $department = [
            "Name" => "Department Name"
        ];
        $departmentData = $this->runSuccessJsonRequest('POST', '/department', null, $department, 201, null, null);
        
        $body = [
            "FallbackID" => "Fallback Department"
        ];

        // Not validating against OpenAPI schema because it calls this invalid, but we should still protect against it
        $this->runRequest('PUT', "/department/$departmentData->id", null, $body, 400, null);

    }
    

    public function testPutDepartmentNoRequestBodyParams(): void
    {
        $department = [
            "Name" => "Department Name"
        ];
        $departmentData = $this->runSuccessJsonRequest('POST', '/department', null, $department, 201, null, null);

        $updateBody = [];
        $this->runRequest('PUT', "/department/$departmentData->id", null, $updateBody, 400, null, '/department/{id}');

    }


    public function testPutDepartmentNoValidRequestBodyParams(): void
    {
        $department = [
            "Name" => "Department Name"
        ];
        $departmentData = $this->runSuccessJsonRequest('POST', '/department', null, $department, 201, null, null);

        $updateBody = [
            "Foo" => "Bar"
        ];
        $this->runRequest('PUT', "/department/$departmentData->id", null, $updateBody, 400, null, '/department/{id}');
        
    }

    
    /* End */
}
