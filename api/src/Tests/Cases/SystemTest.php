<?php

namespace App\Tests\TestCase\Controller;

use Atlas\Query\Delete;
use Atlas\Query\Insert;
use App\Tests\Base\CiabTestCase;

class SystemTest extends CiabTestCase
{


    private function clear(): void
    {
        Delete::new($this->container->db)
            ->from('Configuration')
            ->whereEquals(['Field' => 'phptest'])
            ->perform();
        Delete::new($this->container->db)
            ->from('Configuration')
            ->whereEquals(['Field' => 'phptest2'])
            ->perform();
        Delete::new($this->container->db)
            ->from('ConfigurationField')
            ->whereEquals(['Field' => 'phptest'])
            ->perform();

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
        Insert::new($this->container->db)
            ->into('ConfigurationField')
            ->columns(['Field' => 'phptest', 'TargetTable' => 'Configuration', 'Type' => 'text', 'InitialValue' => 'no', 'Description' => 'PHPTest value'])
            ->perform();

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
        Delete::new($this->container->db)
            ->from('ConfigurationOption')
            ->whereEquals(['Field' => $resource])
            ->perform();
        Delete::new($this->container->db)
            ->from('Configuration')
            ->whereEquals(['Field' => $resource])
            ->perform();
        Delete::new($this->container->db)
            ->from('ConfigurationField')
            ->whereEquals(['Field' => $resource])
            ->perform();

        if ($type != 'select') {
            Insert::new($this->container->db)
                ->into('ConfigurationField')
                ->columns(['Field' => $resource, 'TargetTable' => 'Configuration', 'Type' => $type, 'InitialValue' => $default, 'Description' => 'PHPTest value'])
                ->perform();
        } else {
            Insert::new($this->container->db)
                ->into('ConfigurationField')
                ->columns(['Field' => $resource, 'TargetTable' => 'Configuration', 'Type' => $type, 'InitialValue' => $default[0], 'Description' => 'PHPTest value'])
                ->perform();

            foreach ($default as $d) {
                Insert::new($this->container->db)
                    ->into('ConfigurationOption')
                    ->columns(['Field' => $resource, 'Name' => $d])
                    ->perform();
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
        Delete::new($this->container->db)
            ->from('ConfigurationOption')
            ->whereEquals(['Field' => $resource])
            ->perform();
        Delete::new($this->container->db)
            ->from('Configuration')
            ->whereEquals(['Field' => $resource])
            ->perform();
        Delete::new($this->container->db)
            ->from('ConfigurationField')
            ->whereEquals(['Field' => $resource])
            ->perform();

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
