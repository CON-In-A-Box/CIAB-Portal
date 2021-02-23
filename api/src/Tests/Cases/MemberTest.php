<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;

class MemberTest extends CiabTestCase
{


    protected function setUp(): void
    {
        parent::setUp();
        $sql = "DELETE FROM `AccountConfiguration` WHERE `Field` = 'phptestmember'";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $sql = "DELETE FROM `ConfigurationField` WHERE `Field` = 'phptestmember'";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

        $sql = "INSERT INTO `ConfigurationField` (Field, TargetTable, Type, InitialValue, Description) VALUES ('phptestmember', 'AccountConfiguration', 'text', 'no', 'PHPTest value') ON DUPLICATE KEY UPDATE `Field` = 'phptestmember'";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

    }


    protected function tearDown(): void
    {
        $sql = "DELETE FROM `AccountConfiguration` WHERE `Field` = 'phptestmember'";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $sql = "DELETE FROM `ConfigurationField` WHERE `Field` = 'phptestmember'";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        parent::tearDown();

    }


    public function testMemberConfiguration(): void
    {
        $data = $this->runSuccessJsonRequest('GET', '/member/1000/configuration/phptestmember');
        $this->assertSame($data->value, 'no');

        $this->runSuccessJsonRequest(
            'PUT',
            '/member/1000/configuration',
            null,
            ['Value' => 'yes', 'Field' => 'phptestmember']
        );

        $data = $this->runSuccessJsonRequest('GET', '/member/1000/configuration/phptestmember');
        $this->assertSame($data->value, 'yes');

        $this->runSuccessJsonRequest('GET', '/member/1000/configuration');
        $this->runRequest(
            'PUT',
            '/member/1000/configuration',
            null,
            ['Value' => 'yes', 'Field' => 'notafield'],
            409
        );

        $this->runRequest('PUT', '/member/1000/configuration', null, null, 400);

        $this->runRequest(
            'PUT',
            '/member/1000/configuration',
            null,
            ['Field' => 'phptestmember'],
            400
        );

        $this->runRequest(
            'PUT',
            '/member/1000/configuration',
            null,
            ['Value' => 'yes'],
            400
        );

        $this->runRequest('GET', '/member/1000/configuration/notavalue', null, null, 404);

    }


    /* End */
}
