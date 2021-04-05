<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;

class SystemTest extends CiabTestCase
{


    private function clear(): void
    {
        $sql = "DELETE FROM `Configuration`  WHERE `Field` = 'phptest'";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $sql = "DELETE FROM `Configuration`  WHERE `Field` = 'phptest2'";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $sql = "DELETE FROM `ConfigurationField`  WHERE `Field` = 'phptest'";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

    }


    protected function setUp(): void
    {
        parent::setUp();
        $this->clear();

    }


    protected function tearDown(): void
    {
        $this->clear();
        parent::tearDown();

    }


    public function testSystemConfigOnly(): void
    {
        $this->runSuccessJsonRequest(
            'PUT',
            '/admin/configuration',
            null,
            ['Value' => 'yes', 'Field' => 'phptest']
        );

        $data = $this->runSuccessJsonRequest('GET', '/admin/configuration/phptest');
        $this->assertSame($data->value, 'yes');

        $this->runSuccessJsonRequest('GET', '/admin/configuration');

        $this->runRequest('PUT', '/admin/configuration', null, null, 400);

        $this->runRequest(
            'PUT',
            '/admin/configuration',
            null,
            ['Field' => 'phptest'],
            400
        );

        $this->runRequest(
            'PUT',
            '/admin/configuration',
            null,
            ['Value' => 'yes'],
            400
        );

        $this->runRequest('GET', '/admin/configuration/notavalue', null, null, 404);

    }


    public function testSystemConfigValue(): void
    {
        $sql = "INSERT INTO `ConfigurationField` (Field, TargetTable, Type, InitialValue, Description) VALUES ('phptest', 'Configuration', 'text', 'no', 'PHPTest value')";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

        $data = $this->runSuccessJsonRequest('GET', '/admin/configuration/phptest');
        $this->assertSame($data->value, 'no');

        $this->runSuccessJsonRequest(
            'PUT',
            '/admin/configuration',
            null,
            ['Value' => 'yes', 'Field' => 'phptest']
        );

        $data = $this->runSuccessJsonRequest('GET', '/admin/configuration/phptest');
        $this->assertSame($data->value, 'yes');

        $this->runSuccessJsonRequest(
            'PUT',
            '/admin/configuration',
            null,
            ['Value' => 'yes again', 'Field' => 'phptest2']
        );

        $data = $this->runSuccessJsonRequest('GET', '/admin/configuration/phptest2');
        $this->assertSame($data->value, 'yes again');

        $this->runRequest('PUT', '/admin/configuration', null, null, 400);

        $this->runRequest(
            'PUT',
            '/admin/configuration',
            null,
            ['Field' => 'phptest'],
            400
        );

        $this->runRequest(
            'PUT',
            '/admin/configuration',
            null,
            ['Value' => 'yes'],
            400
        );

        $this->runRequest('GET', '/admin/configuration/notavalue', null, null, 404);

    }


    private function systemConfigTypes($type, $default, $value, $result): void
    {
        $resource = 'phptest_'.$type;
        $sql = "DELETE FROM `ConfigurationOption`  WHERE `Field` = '$resource'";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $sql = "DELETE FROM `Configuration`  WHERE `Field` = '$resource'";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $sql = "DELETE FROM `ConfigurationField`  WHERE `Field` = '$resource'";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

        if ($type != 'select') {
            $sql = "INSERT INTO `ConfigurationField` (Field, TargetTable, Type, InitialValue, Description) VALUES ('$resource', 'Configuration', '$type', '$default', 'PHPTest value')";
            $sth = $this->container->db->prepare($sql);
            $sth->execute();
        } else {
            $sql = "INSERT INTO `ConfigurationField` (Field, TargetTable, Type, InitialValue, Description) VALUES ('$resource', 'Configuration', '$type', $default[0], 'PHPTest value')";
            $sth = $this->container->db->prepare($sql);
            $sth->execute();

            foreach ($default as $d) {
                $sql = "INSERT INTO `ConfigurationOption` (Field, Name) VALUES ('$resource', '$d')";
                $sth = $this->container->db->prepare($sql);
                $sth->execute();
            }
        }

        $data = $this->runSuccessJsonRequest('GET', '/admin/configuration/'.$resource);
        if ($type != 'select') {
            $this->assertSame($data->value, $default);
        } else {
            $this->assertSame($data->value, $default[0]);
        }

        if ($result !== null) {
            $this->runSuccessJsonRequest(
                'PUT',
                '/admin/configuration',
                null,
                ['Value' => "$value", 'Field' => "$resource"]
            );

            $data = $this->runSuccessJsonRequest('GET', '/admin/configuration/'.$resource);
            $this->assertSame($data->value, $result);
        } else {
            $this->runRequest(
                'PUT',
                '/admin/configuration',
                null,
                ['Value' => "$value", 'Field' => "$resource"],
                409
            );
        }

        $sql = "DELETE FROM `ConfigurationOption`  WHERE `Field` = '$resource'";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $sql = "DELETE FROM `Configuration`  WHERE `Field` = '$resource'";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $sql = "DELETE FROM `ConfigurationField`  WHERE `Field` = '$resource'";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

    }


    public function testSystemConfigTypes(): void
    {
        $this->systemConfigTypes('boolean', '0', '1', '1');
        $this->systemConfigTypes('boolean', '0', 'banana', '0');
        $this->systemConfigTypes('integer', '1', '2', '2');
        $this->systemConfigTypes('integer', '1', 'banana', '0');
        $this->systemConfigTypes('list', '1,2,3', '4,5,6', '4,5,6');
        $this->systemConfigTypes('list', '1,2,3', 'banana', 'banana');
        $this->systemConfigTypes('select', ['1','2','3'], '3', '3');
        $this->systemConfigTypes('select', ['1','2','3'], '4', null);

    }


    public function testSystemLog(): void
    {
        $this->runSuccessJsonRequest('GET', '/admin/log');
        $data = $this->runSuccessJsonRequest('GET', '/admin/log/1');
        $this->assertSame(count($data->data), 1);

    }


    /* End */
}
