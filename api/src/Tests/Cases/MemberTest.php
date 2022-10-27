<?php

namespace App\Tests\TestCase\Controller;

use Atlas\Query\Delete;
use Atlas\Query\Insert;
use Atlas\Query\Update;
use App\Tests\Base\CiabTestCase;

class MemberTest extends CiabTestCase
{


    protected function setUp(): void
    {
        parent::setUp();
        Delete::new($this->container->db)
            ->from('AccountConfiguration')
            ->whereEquals(['Field' => 'phptestmember'])
            ->perform();
        Delete::new($this->container->db)
            ->from('ConfigurationField')
            ->whereEquals(['Field' => 'phptestmember'])
            ->perform();

        Insert::new($this->container->db)
            ->into('ConfigurationField')
            ->columns(['Field' => 'phptestmember', 'TargetTable' => 'AccountConfiguration', 'Type' => 'text', 'InitialValue' => 'no', 'Description' => 'PHPTest value'])
            ->perform();

        $this->runSuccessJsonRequest('PUT', '/member/1000', null, ['email2' => '']);
        $when = date('Y-m-d', strtotime('+1 year'));
        Update::new($this->container->db)
            ->table('Authentication')
            ->columns(['FailedAttempts' => 0, 'Expires' => $when])
            ->whereEquals(['AccountID' => 1000])
            ->perform();

    }


    private function deleteTestUser(): void
    {
        Delete::new($this->container->db)
            ->from('Members')
            ->whereEquals(['Email' => 'phpunit@unit.test'])
            ->perform();

    }


    protected function tearDown(): void
    {
        Delete::new($this->container->db)
            ->from('AccountConfiguration')
            ->whereEquals(['Field' => 'phptestmember'])
            ->perform();
        Delete::new($this->container->db)
            ->from('ConfigurationField')
            ->whereEquals(['Field' => 'phptestmember'])
            ->perform();

        $this->runSuccessJsonRequest('PUT', '/member/1000', null, ['email2' => '']);

        $this->deleteTestUser();

        $when = date('Y-m-d', strtotime('+1 year'));
        Update::new($this->container->db)
            ->table('Authentication')
            ->columns(['FailedAttempts' => 0, 'Expires' => $when])
            ->whereEquals(['AccountID' => 1000])
            ->perform();

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
        $this->runRequest('GET', '/member/billybob/status', null, null, 404);
        $this->runSuccessJsonRequest('GET', '/member/allfather@oneeye.com/status');
        $data = $this->runSuccessJsonRequest('GET', '/member/1000/status');
        $this->assertEquals($data->status, 0);

        Update::new($this->container->db)
            ->table('Authentication')
            ->columns(['FailedAttempts' => 999999999])
            ->whereEquals(['AccountID' => 1000])
            ->perform();

        $data = $this->runSuccessJsonRequest('GET', '/member/1000/status');
        $this->assertEquals($data->status, 3);

        $when = date('Y-m-d', strtotime('-1 month'));
        Update::new($this->container->db)
            ->table('Authentication')
            ->columns(['FailedAttempts' => 0, 'Expires' => $when])
            ->whereEquals(['AccountID' => 1000])
            ->perform();
        $data = $this->runSuccessJsonRequest('GET', '/member/1000/status');
        $this->assertEquals($data->status, 2);

        $when = date('Y-m-d', strtotime('+1 year'));
        Update::new($this->container->db)
            ->table('Authentication')
            ->columns(['FailedAttempts' => 0, 'Expires' => $when])
            ->whereEquals(['AccountID' => 1000])
            ->perform();
        $this->runRequest('GET', '/member/-1', null, null, 404);

        $basedata = $this->runSuccessJsonRequest('GET', '/member');
        $this->assertSame($basedata->id, '1000');

        $data = $this->runSuccessJsonRequest('GET', '/member/current');
        $this->assertEquals($basedata, $data);

        $data = $this->runSuccessJsonRequest('GET', '/member/1000');
        $this->assertEquals($basedata, $data);

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
        $data->email2 = null;
        $this->assertEquals($basedata, $data);

        $this->deleteTestUser();
        $userdata = $this->runSuccessJsonRequest('POST', '/member', null, ['email' => 'phpunit@unit.test', 'legal_first_name' => 'Testie', 'legal_last_name' => 'McTester'], 201);

        $when = date('Y-m-d', strtotime('-1 year'));
        $data = $this->runSuccessJsonRequest(
            'PUT',
            '/member/'.$userdata->id,
            null,
            ['legal_first_name' => 'Testie2',
            'legal_last_name' => 'McTestie2',
            'deceased' => '1',
            'do_not_contact' => '1',
            'email_optout' => '1',
            'birthdate' => $when,
            'email2' => 'email2@com.com',
            'email3' => 'email3@com.com',
            'phone' => '1231231234',
            'middle_name' => 'tost',
            'phone2' => '3213213213',
            'address_line1' => '123 1st st.',
            'address_line2' => 'Apt 13',
            'city' => 'Minneapolis',
            'state' => 'MN',
            'zip_code' => '55405',
            'zip_plus4' => '1111',
            'country' => 'USA',
            'province' => 'Northland',
            'gender' => 'alien',
            'preferred_first_name' => 'tee',
            'preferred_last_name' => 'Micky']
        );
        $this->assertEquals($data->first_name, 'tee');
        $this->assertEquals($data->last_name, 'Micky');
        $this->assertEquals($data->legal_first_name, 'Testie2');
        $this->assertEquals($data->legal_last_name, 'McTestie2');
        $this->assertEquals($data->deceased, 1);
        $this->assertEquals($data->do_not_contact, 1);
        $this->assertEquals($data->email_optout, 1);
        $this->assertEquals($data->birthdate, $when);
        $this->assertEquals($data->email2, 'email2@com.com');
        $this->assertEquals($data->email3, 'email3@com.com');
        $this->assertEquals($data->phone, '1231231234');
        $this->assertEquals($data->middle_name, 'tost');
        $this->assertEquals($data->phone2, '3213213213');
        $this->assertEquals($data->address_line1, '123 1st st.');
        $this->assertEquals($data->address_line2, 'Apt 13');
        $this->assertEquals($data->city, 'Minneapolis');
        $this->assertEquals($data->state, 'MN');
        $this->assertEquals($data->zip_code, '55405');
        $this->assertEquals($data->zip_plus4, '1111');
        $this->assertEquals($data->country, 'USA');
        $this->assertEquals($data->province, 'Northland');
        $this->assertEquals($data->gender, 'alien');
        $this->assertEquals($data->preferred_first_name, 'tee');
        $this->assertEquals($data->preferred_last_name, 'Micky');

        $data = $this->runSuccessJsonRequest(
            'PUT',
            '/member/'.$userdata->id,
            null,
            ['preferred_first_name' => 'a&#39;d',
            'preferred_last_name' => 'b&#39;c']
        );
        $this->assertEquals($data->first_name, 'a\'d');
        $this->assertEquals($data->last_name, 'b\'c');

        $data = $this->runRequest('PUT', '/member/'.$userdata->id, null, ['birthdate' => 'not a date'], 400);
        $data = $this->runRequest('PUT', '/member/'.$userdata->id, null, ['deceased' => 'not a boolean'], 400);
        $data = $this->runRequest('PUT', '/member/'.$userdata->id, null, ['do_not_contact' => 'not a boolean'], 400);
        $data = $this->runRequest('PUT', '/member/'.$userdata->id, null, ['email_optout' => 'not a boolean'], 400);

    }


    public function testNewMember(): void
    {
        $this->runRequest('POST', '/member', null, null, 400);
        $this->runRequest('POST', '/member', null, ['email' => 'phpunit@unit.test'], 400);
        $this->runRequest('POST', '/member', null, ['legal_first_name' => 'phpunit'], 400);
        $this->runRequest('POST', '/member', null, ['legal_last_name' => 'phpunit'], 400);

        $userdata = $this->runSuccessJsonRequest('POST', '/member', null, ['email' => 'phpunit@unit.test', 'legal_first_name' => 'Testie'], 201);

        $this->runRequest('POST', '/member', null, ['email' => 'phpunit@unit.test', 'legal_first_name' => 'Testie'], 409);

        $this->runRequest('POST', '/member/phpunit@unit.test/password', null, null, 201);

        $this->deleteTestUser();
        $userdata = $this->runSuccessJsonRequest('POST', '/member', null, ['email' => 'phpunit@unit.test', 'legal_first_name' => 'Testie', 'legal_last_name' => 'McTester'], 201);


        $this->runRequest('PUT', '/member/phpunit@unit.test/password/recovery', null, null, 400);
        $this->runRequest('PUT', '/member/phpunit@unit.test/password/recovery', null, ['OneTimeCode' => 'asdfasdf'], 403);
        $this->runRequest('PUT', '/member/phpunit@unit.test/password/recovery', null, ['NewPassword' => 'asdfasdf'], 200);
        $this->runRequest('PUT', '/member/phpunit@unit.test/password/recovery', null, ['NewPassword' => 'asdfasdf', 'OneTimeCode' => 'asdfasdf'], 403);
        $this->runRequest('PUT', '/member/phpunit@unit.test/password', null, null, 400);
        $this->runRequest('PUT', '/member/phpunit@unit.test/password', null, ['NewPassword' => 'asdfasdf'], 200);
        $this->runRequest('PUT', '/member/badmember@bad.bad/password/recovery', null, ['NewPassword' => 'asdfasdf'], 404);

    }


    public function testFindMember() :void
    {
        $this->runRequest('GET', '/member/find', null, null, 400);

        $this->runRequest('GET', '/member/find', ['q' => 'Billy Bob'], null, 404);

        $data = $this->runSuccessJsonRequest('GET', '/member/find', ['q' => 'A1000', 'from' => 'id']);
        $this->assertEquals($data->type, 'member_list');
        $this->assertNotEmpty($data->data);
        $this->assertEquals(count($data->data), 1);
        $this->assertEquals($data->data[0]->id, 1000);

        $data = $this->runSuccessJsonRequest('GET', '/member/find', ['q' => '1000', 'from' => 'id']);
        $this->assertEquals($data->type, 'member_list');
        $this->assertNotEmpty($data->data);
        $this->assertEquals(count($data->data), 1);
        $this->assertEquals($data->data[0]->id, 1000);

        $data = $this->runRequest('GET', '/member/find', ['q' => 'Odin'], null, 404);

        $data = $this->runSuccessJsonRequest('GET', '/member/find', ['q' => 'Od', 'partial' => 'true']);
        $this->assertEquals($data->type, 'member_list');
        $this->assertNotEmpty($data->data);
        $this->assertEquals($data->data[0]->id, 1000);

        $this->runRequest('GET', '/member/find', ['q' => 'Od', 'partial' => 'false'], null, 404);
        $this->runRequest('GET', '/member/find', ['q' => 'Odin', 'from' => 'name', 'partial' => 'false'], null, 404);
        $this->runRequest('GET', '/member/find', ['q' => 'Odin Allf', 'from' => 'name', 'partial' => 'false'], null, 404);

        $data = $this->runSuccessJsonRequest('GET', '/member/find', ['q' => 'Odin Allfather', 'from' => 'name', 'partial' => 'false']);
        $this->assertEquals($data->type, 'member_list');
        $this->assertNotEmpty($data->data);
        $this->assertEquals($data->data[0]->id, 1000);

        $data = $this->runRequest('GET', '/member/find', ['q' => 'Odin billybob', 'from' => 'name', 'partial' => 'false'], null, 404);

        $data = $this->runSuccessJsonRequest('GET', '/member/find', ['q' => 'Odin', 'from' => 'legal_name', 'partial' => 'true']);
        $this->assertEquals($data->type, 'member_list');
        $this->assertNotEmpty($data->data);
        $this->assertEquals($data->data[0]->id, 1000);

        $this->runRequest('GET', '/member/find', ['q' => 'Odin', 'from' => 'email'], null, 404);

        $this->runRequest('GET', '/member/find', ['q' => 'thiswillnotbefound'], null, 404);

    }


    public function testDuplicateMember() :void
    {
        Delete::new($this->container->db)
            ->from('Members')
            ->whereEquals(['Email' => 'phpDupUnit@unit.test'])
            ->perform();

        $insert = Insert::new($this->container->db);

        $insert->into('Members')->columns([
            'AccountID' => 8000,
            'FirstName' => 'First',
            'LastName' => 'User',
            'Email' => 'phpDupUnit@unit.test',
            'Gender' => 'Both'
        ])->perform();

        $insert->into('Members')->columns([
            'AccountID' => 8001,
            'FirstName' => 'Second',
            'LastName' => 'User',
            'Email' => 'phpDupUnit@unit.test',
            'Gender' => 'Both'
        ])->perform();

        $auth = \password_hash('PassWord1', PASSWORD_DEFAULT);
        $insert = Insert::new($this->container->db);
        $insert->into('Authentication')->columns([
            'AccountID' => 8000,
            'Authentication' => $auth,
            'LastLogin' => null,
            'Expires' => date('Y-m-d', strtotime('+1 year')),
            'FailedAttempts' => 0,
            'OneTime' => null,
            'OneTimeExpires' => null
        ])->perform();

        $auth = \password_hash('PassWord2', PASSWORD_DEFAULT);
        $insert = Insert::new($this->container->db);
        $insert->into('Authentication')->columns([
            'AccountID' => 8001,
            'Authentication' => $auth,
            'LastLogin' => null,
            'Expires' => date('Y-m-d', strtotime('+1 year')),
            'FailedAttempts' => 0,
            'OneTime' => null,
            'OneTimeExpires' => null
        ])->perform();


        $token1 = $this->runSuccessJsonRequest('POST', '/token', null, ['grant_type' => 'password', 'username' => 'phpDupUnit@unit.test', 'password' => 'PassWord1', 'client_id' => 'ciab']);

        $basedata = $this->runSuccessJsonRequest('GET', '/member', null, null, 200, $token1);
        $this->assertSame($basedata->id, '8000');
        $this->assertSame($basedata->duplicates, '8001');

        $token2 = $this->runSuccessJsonRequest('POST', '/token', null, ['grant_type' => 'password', 'username' => 'phpDupUnit@unit.test', 'password' => 'PassWord2', 'client_id' => 'ciab']);

        $basedata = $this->runSuccessJsonRequest('GET', '/member', null, null, 200, $token2);
        $this->assertSame($basedata->id, '8001');
        $this->assertSame($basedata->duplicates, '8000');

        Delete::new($this->container->db)
            ->from('Members')
            ->whereEquals(['Email' => 'phpDupUnit@unit.test'])
            ->perform();

        Delete::new($this->container->db)
            ->from('Authentication')
            ->whereEquals(['AccountId' => '8000'])
            ->perform();
        Delete::new($this->container->db)
            ->from('Authentication')
            ->whereEquals(['AccountId' => '8001'])
            ->perform();

    }


    /* End */
}
