<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;

class DepartmentTest extends CiabTestCase
{


    public function testGetDepartmentList(): void
    {
        $data = $this->runSuccessJsonRequest('GET', '/department', null, null, 200, null);
        $this->assertNotEmpty($data->data);

    }


    public function testGetDepartmentByName(): void
    {
        $data = $this->runSuccessJsonRequest('GET', '/department/Activities', null, null, 200, null, '/department/{id}');
        $this->assertEquals("Activities", $data->name);

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
        $this->runRequest('POST', '/department', null, $body, 400, null, null, '/department');

    }


    public function testPostDepartmentInvalidParentId(): void
    {
        $body = [
            "Name" => "Test Department",
            "ParentID" => -1
        ];
        $this->runRequest('POST', '/department', null, $body, 400, null, null, '/department');

    }

    
    public function testPostDepartmentInvalidFallbackId(): void
    {
        $body = [
            "Name" => "Test Department",
            "FallbackID" => -1
        ];
        $this->runRequest('POST', '/department', null, $body, 400, null, null, '/department');
        
    }


    /* End */
}
