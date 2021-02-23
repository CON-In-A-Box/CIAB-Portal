<?php

namespace App\Tests\TestCase\Controller\Member;

use PHPUnit\Framework\TestCase;
use App\Tests\Traits\AppTestTrait;
use Slim\Http\Response;

class MemberTest extends TestCase
{

    use AppTestTrait;


    public function testMemberConfiguration(): void
    {
        $sql = "DELETE FROM `AccountConfiguration`  WHERE `Field` = 'phptestmember'";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $sql = "DELETE FROM `ConfigurationField`  WHERE `Field` = 'phptestmember'";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

        $sql = "INSERT INTO `ConfigurationField` (Field, TargetTable, Type, InitialValue, Description) VALUES ('phptestmember', 'AccountConfiguration', 'text', 'no', 'PHPTest value') ON DUPLICATE KEY UPDATE `Field` = 'phptestmember'";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

        $request = $this->createRequest('GET', '/member/1000/configuration/phptestmember');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);
        $this->assertSame($data->value, 'no');

        $request = $this->createRequest('PUT', '/member/1000/configuration');
        $request = $request->withParsedBody(['Value' => 'yes',
                                             'Field' => 'phptestmember']);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);

        $request = $this->createRequest('GET', '/member/1000/configuration/phptestmember');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);
        $this->assertSame($data->value, 'yes');

        $request = $this->createRequest('GET', '/member/1000/configuration');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 200);
        $data = json_decode((string)$response->getBody());
        $this->assertNotEmpty($data);

        $sql = "DELETE FROM `AccountConfiguration`  WHERE `Field` = 'phptestmember'";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $sql = "DELETE FROM `ConfigurationField`  WHERE `Field` = 'phptestmember'";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

        $request = $this->createRequest('PUT', '/member/1000/configuration');
        $request = $request->withParsedBody(['Value' => 'yes',
                                             'Field' => 'notafield']);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 409);

        $request = $this->createRequest('PUT', '/member/1000/configuration');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('PUT', '/member/1000/configuration');
        $request = $request->withParsedBody(['Field' => 'phptestmember']);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('PUT', '/member/1000/configuration');
        $request = $request->withParsedBody(['Value' => 'yes']);
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 400);

        $request = $this->createRequest('GET', '/member/1000/configuration/notavalue');
        $this->container['request'] = $request;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 404);

    }


    /* End */
}
