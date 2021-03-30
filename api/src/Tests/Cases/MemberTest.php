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

        $this->runSuccessJsonRequest('PUT', '/member/1000', null, ['email2' => '']);
        $when = date('Y-m-d', strtotime('+1 year'));
        $sql = "UPDATE `Authentication` SET `FailedAttempts` = 0, `Expires` = '$when' WHERE `AccountID` = 1000";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

    }


    private function deleteTestUser(): void
    {
        $sql = "DELETE FROM `Members` WHERE `Email` = 'phpunit@unit.test'";
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
        $this->runSuccessJsonRequest('PUT', '/member/1000', null, ['email2' => '']);

        $this->deleteTestUser();

        $when = date('Y-m-d', strtotime('+1 year'));
        $sql = "UPDATE `Authentication` SET `FailedAttempts` = 0, `Expires` = '$when' WHERE `AccountID` = 1000";
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


    public function testMemberGet(): void
    {
        $this->runRequest('GET', '/member/-1/status', null, null, 404);
        $this->runSuccessJsonRequest('GET', '/member/allfather@oneeye.com/status');
        $data = $this->runSuccessJsonRequest('GET', '/member/1000/status');
        $this->assertEquals($data->status, 0);

        $sql = "UPDATE `Authentication` SET `FailedAttempts` = 9999999 WHERE `AccountID` = 1000";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

        $data = $this->runSuccessJsonRequest('GET', '/member/1000/status');
        $this->assertEquals($data->status, 3);

        $when = date('Y-m-d', strtotime('-1 month'));
        $sql = "UPDATE `Authentication` SET `FailedAttempts` = 0, `Expires` = '$when' WHERE `AccountID` = 1000";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $this->runSuccessJsonRequest('GET', '/member/1000/status');
        $this->assertEquals($data->status, 2);

        $when = date('Y-m-d', strtotime('+1 year'));
        $sql = "UPDATE `Authentication` SET `Expires` = '$when' WHERE `AccountID` = 1000";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

        $this->runRequest('GET', '/member/-1', null, null, 404);

        $basedata = $this->runSuccessJsonRequest('GET', '/member');
        $this->assertSame($basedata->id, '1000');

        $data = $this->runSuccessJsonRequest('GET', '/member/current');
        $this->assertEquals($basedata, $data);

        $data = $this->runSuccessJsonRequest('GET', '/member/1000');
        $this->assertEquals($basedata, $data);

        $data = $this->runSuccessJsonRequest('GET', '/member/find');
        $this->assertEquals($data->type, 'member_list');
        $this->assertEmpty($data->data);

        $data = $this->runSuccessJsonRequest('GET', '/member/find', ['q' => 'thiswillnotbefound']);
        $this->assertEquals($data->type, 'member_list');
        $this->assertEmpty($data->data);

        $data = $this->runSuccessJsonRequest('GET', '/member/find', ['q' => $basedata->email]);
        $this->assertEquals($data->type, 'member_list');
        $this->assertNotEmpty($data->data);
        $this->assertEquals($data->data[0]->id, 1000);

    }


    public function testMemberPut(): void
    {
        $this->runRequest('PUT', '/member/-1', null, null, 404);

        $basedata = $this->runSuccessJsonRequest('GET', '/member');
        $this->assertSame($basedata->id, '1000');

        $this->runRequest('PUT', '/member/1000', null, null, 404);

        $data = $this->runSuccessJsonRequest('PUT', '/member/1000', null, ['email2' => 'phpunit@testing.test']);
        $this->assertEquals($data->email2, 'phpunit@testing.test');

        $data = $this->runSuccessJsonRequest('GET', '/member/current');
        $this->assertEquals($data->email2, 'phpunit@testing.test');
        unset($data->email2);
        $this->assertEquals($basedata, $data);

        $this->deleteTestUser();
        $userdata = $this->runSuccessJsonRequest('POST', '/member', null, ['email' => 'phpunit@unit.test', 'legalFirstName' => 'Testie', 'legalLastName' => 'McTester'], 201);

        $when = date('Y-m-d', strtotime('-1 year'));
        $data = $this->runSuccessJsonRequest(
            'PUT',
            '/member/'.$userdata->id,
            null,
            ['firstName' => 'Testie2',
            'lastName' => 'McTestie2',
            'Deceased' => '1',
            'DoNotContact' => '1',
            'EmailOptOut' => '1',
            'Birthdate' => $when,
            'email2' => 'email2@com.com',
            'email3' => 'email3@com.com',
            'phone1' => '1231231234',
            'middleName' => 'tost',
            'phone2' => '3213213213',
            'addressLine1' => '123 1st st.',
            'addressLine2' => 'Apt 13',
            'city' => 'Minneapolis',
            'state' => 'MN',
            'zipCode' => '55405',
            'zipPlus4' => '1111',
            'countryName' => 'USA',
            'province' => 'Northland',
            'Gender' => 'alien',
            'preferredFirstName' => 'tee',
            'preferredLastName' => 'Micky']
        );
        $this->assertEquals($data->firstName, 'tee');
        $this->assertEquals($data->lastName, 'Micky');
        $this->assertEquals($data->legalFirstName, 'Testie2');
        $this->assertEquals($data->legalLastName, 'McTestie2');
        $this->assertEquals($data->Deceased, 1);
        $this->assertEquals($data->DoNotContact, 1);
        $this->assertEquals($data->EmailOptOut, 1);
        $this->assertEquals($data->Birthdate, $when);
        $this->assertEquals($data->email2, 'email2@com.com');
        $this->assertEquals($data->email3, 'email3@com.com');
        $this->assertEquals($data->phone1, '1231231234');
        $this->assertEquals($data->middleName, 'tost');
        $this->assertEquals($data->phone2, '3213213213');
        $this->assertEquals($data->addressLine1, '123 1st st.');
        $this->assertEquals($data->addressLine2, 'Apt 13');
        $this->assertEquals($data->city, 'Minneapolis');
        $this->assertEquals($data->state, 'MN');
        $this->assertEquals($data->zipCode, '55405');
        $this->assertEquals($data->zipPlus4, '1111');
        $this->assertEquals($data->countryName, 'USA');
        $this->assertEquals($data->province, 'Northland');
        $this->assertEquals($data->Gender, 'alien');
        $this->assertEquals($data->preferredFirstName, 'tee');
        $this->assertEquals($data->preferredLastName, 'Micky');

        $data = $this->runRequest('PUT', '/member/'.$userdata->id, null, ['Birthdate' => 'not a date'], 400);
        $data = $this->runRequest('PUT', '/member/'.$userdata->id, null, ['Deceased' => 'not a boolean'], 400);
        $data = $this->runRequest('PUT', '/member/'.$userdata->id, null, ['DoNotContact' => 'not a boolean'], 400);
        $data = $this->runRequest('PUT', '/member/'.$userdata->id, null, ['EmailOptOut' => 'not a boolean'], 400);

    }


    public function testNewMember(): void
    {
        $this->runRequest('POST', '/member', null, null, 400);
        $this->runRequest('POST', '/member', null, ['email1' => 'phpunit@unit.test'], 400);
        $this->runRequest('POST', '/member', null, ['firstName' => 'phpunit'], 400);
        $this->runRequest('POST', '/member', null, ['lastName' => 'phpunit'], 400);

        $userdata = $this->runSuccessJsonRequest('POST', '/member', null, ['email1' => 'phpunit@unit.test', 'firstName' => 'Testie'], 201);

        $this->runRequest('POST', '/member', null, ['email1' => 'phpunit@unit.test', 'firstName' => 'Testie'], 409);

        $this->runRequest('POST', '/member/phpunit@unit.test/password', null, null, 201);

        $this->deleteTestUser();
        $userdata = $this->runSuccessJsonRequest('POST', '/member', null, ['email' => 'phpunit@unit.test', 'legalFirstName' => 'Testie', 'legalLastName' => 'McTester'], 201);


        $this->runRequest('PUT', '/member/phpunit@unit.test/password/recovery', null, null, 400);
        $this->runRequest('PUT', '/member/phpunit@unit.test/password/recovery', null, ['OneTimeCode' => 'asdfasdf'], 403);
        $this->runRequest('PUT', '/member/phpunit@unit.test/password/recovery', null, ['NewPassword' => 'asdfasdf'], 200);
        $this->runRequest('PUT', '/member/phpunit@unit.test/password/recovery', null, ['NewPassword' => 'asdfasdf', 'OneTimeCode' => 'asdfasdf'], 403);
        $this->runRequest('PUT', '/member/phpunit@unit.test/password', null, null, 400);
        $this->runRequest('PUT', '/member/phpunit@unit.test/password', null, ['NewPassword' => 'asdfasdf'], 200);
        $this->runRequest('PUT', '/member/badmember@bad.bad/password/recovery', null, ['NewPassword' => 'asdfasdf'], 404);

    }


    /* End */
}
