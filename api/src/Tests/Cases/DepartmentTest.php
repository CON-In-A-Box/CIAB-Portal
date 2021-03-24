<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;

class DepartmentTest extends CiabTestCase
{


    public function testDepartment(): void
    {
        $data = $this->runSuccessJsonRequest('GET', '/department');
        $this->assertNotEmpty($data->data);
        $data = $this->runSuccessJsonRequest('GET', '/department/Activities');
        $data2 = $this->runSuccessJsonRequest('GET', '/department/1');
        $this->assertEquals($data, $data2);
        $this->runSuccessJsonRequest('GET', '/department/1/children');
        $this->runSuccessJsonRequest('GET', '/department');
        $data = $this->runSuccessJsonRequest(
            'GET',
            '/department/100'
        );
        $this->assertIsObject($data->parent);
        $this->assertIncludes($data, 'parent');

    }


    public function testDepartmentErrors(): void
    {
        $this->runRequest('GET', '/department/-1', null, null, 404);
        $this->runRequest('GET', '/department/not-a-dept', null, null, 404);

    }


    /* End */
}
