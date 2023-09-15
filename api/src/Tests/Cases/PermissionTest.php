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

    }


    /* End */
}
