<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;

class DepartmentTest extends CiabTestCase
{


    public function testDepartment(): void
    {
        $data = $this->runSuccessJsonRequest('GET', '/department');
        $this->assertNotEmpty($data->data);

        $data = $this->runSuccessJsonRequest('GET', '/department/Systems');
        $this->runSuccessJsonRequest('GET', '/department/1');
        $data = $this->runSuccessJsonRequest('GET', '/department', ['include' => 'id']);
        $data = $this->runSuccessJsonRequest(
            'GET',
            '/department/100',
            ['include' => 'parent,fallback']
        );
        $this->assertIsObject($data->parent);
        $this->assertObjectHasAttribute('id', $data->parent);

    }


    public function testDepartmentErrors(): void
    {
        $this->runRequest('GET', '/department/-1', null, null, 404);
        $this->runRequest('GET', '/department/not-a-dept', null, null, 404);

    }


    /* End */
}
