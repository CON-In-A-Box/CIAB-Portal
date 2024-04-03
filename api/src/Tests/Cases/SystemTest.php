<?php

namespace App\Tests\TestCase\Controller;

use Atlas\Query\Delete;
use Atlas\Query\Insert;
use App\Tests\Base\CiabTestCase;
use App\Tests\Base\TestRun;

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
        testRun::testRun($this, 'PUT', '/admin/configuration')
            ->setBody(['Value' => 'yes', 'Field' => 'phptest'])
            ->run();

        $data = testRun::testRun($this, 'GET', '/admin/configuration/{field}')
            ->setUriParts(['field' => 'phptest'])
            ->run();
        $this->assertSame($data->value, 'yes');

        testRun::testRun($this, 'GET', '/admin/configuration')
            ->run();

        testRun::testRun($this, 'PUT', '/admin/configuration')
            ->setExpectedResult(400)
            ->run();

        testRun::testRun($this, 'PUT', '/admin/configuration')
            ->setBody(['Field' => 'phptest'])
            ->setExpectedResult(400)
            ->run();

        testRun::testRun($this, 'PUT', '/admin/configuration')
            ->setBody(['Value' => 'yes'])
            ->setExpectedResult(400)
            ->run();

        testRun::testRun($this, 'GET', '/admin/configuration/{field}')
            ->setUriParts(['field' => 'notavalue'])
            ->setExpectedResult(404)
            ->run();

    }


    public function testSystemConfigValue(): void
    {
        Insert::new($this->container->db)
            ->into('ConfigurationField')
            ->columns(['Field' => 'phptest', 'TargetTable' => 'Configuration', 'Type' => 'text', 'InitialValue' => 'no', 'Description' => 'PHPTest value'])
            ->perform();

        $data = testRun::testRun($this, 'GET', '/admin/configuration/{field}')
            ->setUriParts(['field' => 'phptest'])
            ->run();
        $this->assertSame($data->value, 'no');

        testRun::testRun($this, 'PUT', '/admin/configuration')
            ->setBody(['Value' => 'yes', 'Field' => 'phptest'])
            ->run();

        $data = testRun::testRun($this, 'GET', '/admin/configuration/{field}')
            ->setUriParts(['field' => 'phptest'])
            ->run();
        $this->assertSame($data->value, 'yes');

        testRun::testRun($this, 'PUT', '/admin/configuration')
            ->setBody(['Value' => 'yes again', 'Field' => 'phptest2'])
            ->run();

        $data = testRun::testRun($this, 'GET', '/admin/configuration/{field}')
            ->setUriParts(['field' => 'phptest2'])
            ->run();
        $this->assertSame($data->value, 'yes again');

        testRun::testRun($this, 'PUT', '/admin/configuration')
            ->setExpectedResult(400)
            ->run();

        testRun::testRun($this, 'PUT', '/admin/configuration')
            ->setBody(['Field' => 'phptest'])
            ->setExpectedResult(400)
            ->run();

        testRun::testRun($this, 'PUT', '/admin/configuration')
            ->setBody(['Value' => 'yes'])
            ->setExpectedResult(400)
            ->run();

        testRun::testRun($this, 'GET', '/admin/configuration/{field}')
            ->setUriParts(['field' => 'notavalue'])
            ->setExpectedResult(404)
            ->run();

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

        $data = testRun::testRun($this, 'GET', '/admin/configuration/{field}')
            ->setUriParts(['field' => $resource])
            ->run();
        if ($type != 'select') {
            $this->assertSame($data->value, $default);
        } else {
            $this->assertSame($data->value, $default[0]);
        }

        if ($result !== null) {
            testRun::testRun($this, 'PUT', '/admin/configuration')
                ->setBody(['Value' => "$value", 'Field' => "$resource"])
                ->run();

            $data = testRun::testRun($this, 'GET', '/admin/configuration/{field}')
                ->setUriParts(['field' => $resource])
                ->run();
            $this->assertSame($data->value, $result);
        } else {
            testRun::testRun($this, 'PUT', '/admin/configuration')
                ->setBody(['Value' => "$value", 'Field' => "$resource"])
                ->setExpectedResult(409)
                ->run();
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
        testRun::testRun($this, 'GET', '/admin/log')
            ->setVerifyYaml(false)
            ->run();

    }


    /* End */
}
