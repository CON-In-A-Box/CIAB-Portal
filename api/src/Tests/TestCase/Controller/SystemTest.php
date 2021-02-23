<?php

namespace App\Tests\TestCase\Controller\System;

use PHPUnit\Framework\TestCase;
use App\Tests\Traits\AppTestTrait;
use Slim\Http\Response;

class SystemTest extends TestCase
{

    use AppTestTrait;


    public function testSystemConfigOnly(): void
    {
        $sql = "DELETE FROM `Configuration`  WHERE `Field` = 'phptest'";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

        $request = $this->createRequest('PUT', '/admin/configuration');
        $request = $request->withParsedBody(['Value' => 'yes',
                                             'Field' => 'phptest']);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);

        $request = $this->createRequest('GET', '/admin/configuration/phptest');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);
        $this->assertSame($data->value, 'yes');

        $request = $this->createRequest('GET', '/admin/configuration');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);

        $request = $this->createRequest('PUT', '/admin/configuration');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('PUT', '/admin/configuration');
        $request = $request->withParsedBody(['Field' => 'phptest']);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('PUT', '/admin/configuration');
        $request = $request->withParsedBody(['Value' => 'yes']);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('GET', '/admin/configuration/notavalue');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 404);

        $sql = "DELETE FROM `Configuration`  WHERE `Field` = 'phptest'";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

    }


    public function testSystemConfigValue(): void
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
        $sql = "INSERT INTO `ConfigurationField` (Field, TargetTable, Type, InitialValue, Description) VALUES ('phptest', 'Configuration', 'text', 'no', 'PHPTest value')";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

        $request = $this->createRequest('GET', '/admin/configuration/phptest');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);
        $this->assertSame($data->value, 'no');

        $request = $this->createRequest('PUT', '/admin/configuration');
        $request = $request->withParsedBody(['Value' => 'yes',
                                             'Field' => 'phptest']);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);

        $request = $this->createRequest('GET', '/admin/configuration/phptest');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);
        $this->assertSame($data->value, 'yes');

        $request = $this->createRequest('PUT', '/admin/configuration');
        $request = $request->withParsedBody(['Value' => 'yes again',
                                             'Field' => 'phptest2']);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);

        $request = $this->createRequest('GET', '/admin/configuration/phptest2');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);
        $this->assertSame($data->value, 'yes again');

        $request = $this->createRequest('PUT', '/admin/configuration');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('PUT', '/admin/configuration');
        $request = $request->withParsedBody(['Field' => 'phptest']);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('PUT', '/admin/configuration');
        $request = $request->withParsedBody(['Value' => 'yes']);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('GET', '/admin/configuration/notavalue');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 404);

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

        $request = $this->createRequest('GET', '/admin/configuration/'.$resource);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);
        if ($type != 'select') {
            $this->assertSame($data->value, $default);
        } else {
            $this->assertSame($data->value, $default[0]);
        }

        $request = $this->createRequest('PUT', '/admin/configuration');
        $request = $request->withParsedBody(['Value' => "$value",
                                             'Field' => "$resource"]);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);

        $request = $this->createRequest('GET', '/admin/configuration/'.$resource);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);
        $this->assertSame($data->value, $result);

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
        $this->systemConfigTypes('select', ['1','2','3'], '4', '');

    }


    public function testSystemLog(): void
    {
        $request = $this->createRequest('GET', '/admin/log');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);

        $request = $this->createRequest('GET', '/admin/log/1');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);
        $this->assertSame(count($data->data), 1);

    }


    /* End */
}
