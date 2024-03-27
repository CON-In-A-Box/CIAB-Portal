<?php

namespace App\Tests\TestCase\Controller;

use Atlas\Query\Delete;
use Atlas\Query\Insert;
use Atlas\Query\Update;
use App\Tests\Base\CiabTestCase;
use App\Tests\Base\TestRun;

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

        testRun::testRun($this, 'PUT', '/member/{id}')
            ->setUriParts(['id' => 1000])
            ->setBody(['email2' => ''])
            ->run();
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

        testRun::testRun($this, 'PUT', '/member/{id}')
            ->setUriParts(['id' => 1000])
            ->setBody(['email2' => ''])
            ->run();

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
        $data = testRun::testRun($this, 'GET', '/member/{id}/configuration/{field}')
            ->setUriParts(['id' => 1000, 'field' => 'phptestmember'])
            ->run();
        $this->assertSame($data->value, 'no');

        testRun::testRun($this, 'PUT', '/member/{id}/configuration')
            ->setUriParts(['id' => 1000])
            ->setBody(['Value' => 'yes', 'Field' => 'phptestmember'])
            ->run();

        $data = testRun::testRun($this, 'GET', '/member/{id}/configuration/{field}')
            ->setUriParts(['id' => 1000, 'field' => 'phptestmember'])
            ->run();
        $this->assertSame($data->value, 'yes');

        testRun::testRun($this, 'GET', '/member/{id}/configuration')
            ->setUriParts(['id' => 1000])
            ->run();

        testRun::testRun($this, 'PUT', '/member/{id}/configuration')
            ->setUriParts(['id' => 1000])
            ->setBody(['Value' => 'yes', 'Field' => 'notafield'])
            ->setExpectedResult(409)
            ->run();

        testRun::testRun($this, 'PUT', '/member/{id}/configuration')
            ->setUriParts(['id' => 1000])
            ->setExpectedResult(400)
            ->run();

        testRun::testRun($this, 'PUT', '/member/{id}/configuration')
            ->setUriParts(['id' => 1000])
            ->setBody(['Field' => 'phptestmember'])
            ->setExpectedResult(400)
            ->run();

        testRun::testRun($this, 'PUT', '/member/{id}/configuration')
            ->setUriParts(['id' => 1000])
            ->setBody(['Value' => 'yes'])
            ->setExpectedResult(400)
            ->run();

        testRun::testRun($this, 'GET', '/member/{id}/configuration/{field}')
            ->setUriParts(['id' => 1000, 'field' => 'notavalue'])
            ->setExpectedResult(404)
            ->run();

    }


    public function getStatusProvider(): array
    {
        return [
            [-1, 404],
            ['billybob', 404],
            ['allfather@oneeye.com', 200],
            [1000, 200],
        ];

    }


    /**
     * @test
     * @dataProvider getStatusProvider
     **/
    public function testMemberGetStatus($id, $expected)
    {
        testRun::testRun($this, 'GET', '/member/{id}/status')
            ->setUriParts(['id' => $id])
            ->setExpectedResult($expected)
            ->run();

    }


    public function getProvider(): array
    {
        return [
            [-1, 404],
            ['billybob', 404],
            [null, 200, '1000'],
            ['allfather@oneeye.com', 200, '1000'],
            ['current', 200, '1000'],
            [1000, 200, '1000'],
        ];

    }


    /**
     * @test
     * @dataProvider getProvider
     **/
    public function testMemberGet($id, $expected, $resultId = null)
    {
        if ($id !== null) {
            $data = testRun::testRun($this, 'GET', '/member/{id}')
                ->setUriParts(['id' => $id])
                ->setExpectedResult($expected)
                ->run();
        } else {
            $data = testRun::testRun($this, 'GET', '/member')
                ->setExpectedResult($expected)
                ->run();
        }
        if ($resultId !== null) {
            $this->assertSame($data->id, $resultId);
        }

    }


    public function testMemberGetSpecial(): void
    {
        Update::new($this->container->db)
            ->table('Authentication')
            ->columns(['FailedAttempts' => 999999999])
            ->whereEquals(['AccountID' => 1000])
            ->perform();

        $data = testRun::testRun($this, 'GET', '/member/{id}/status')
            ->setUriParts(['id' => 1000])
            ->run();
        $this->assertEquals($data->status, 3);

        $when = date('Y-m-d', strtotime('-1 month'));
        Update::new($this->container->db)
            ->table('Authentication')
            ->columns(['FailedAttempts' => 0, 'Expires' => $when])
            ->whereEquals(['AccountID' => 1000])
            ->perform();
        $data = testRun::testRun($this, 'GET', '/member/{id}/status')
            ->setUriParts(['id' => 1000])
            ->run();
        $this->assertEquals($data->status, 2);

        $when = date('Y-m-d', strtotime('+1 year'));
        Update::new($this->container->db)
            ->table('Authentication')
            ->columns(['FailedAttempts' => 0, 'Expires' => $when])
            ->whereEquals(['AccountID' => 1000])
            ->perform();

    }


    public function putProvider(): array
    {
        return [
            [-1, 404],
            [1000, 404],
            [1000, 200, ['email2' => 'phpunit@testing.test']],
        ];

    }


    /**
     * @test
     * @dataProvider putProvider
     **/
    public function testMemberPut($id, $expected, $body = null)
    {
        $test = testRun::testRun($this, 'PUT', '/member/{id}')
            ->setUriParts(['id' => $id])
            ->setExpectedResult($expected);
        if ($body !== null) {
            $test->setBody($body);
        }
        $data = $test->run();

        if ($expected == 200 && $body !== null) {
            foreach ($body as $key => $value) {
                $this->assertEquals($data->$key, $value);
            }
        }

    }


    public function testMemberPutSpecial(): void
    {
        $this->deleteTestUser();
        $userdata = testRun::testRun($this, 'POST', '/member')
            ->setBody(['email' => 'phpunit@unit.test', 'legal_first_name' => 'Testie', 'legal_last_name' => 'McTester'])
            ->run();

        $when = date('Y-m-d', strtotime('-1 year'));
        $data = testRun::testRun($this, 'PUT', '/member/{id}')
            ->setUriParts(['id' => $userdata->id])
            ->setBody(['legal_first_name' => 'Testie2',
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
                        'preferred_last_name' => 'Micky'])
            ->run();

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

        $data = testRun::testRun($this, 'PUT', '/member/{id}')
            ->setUriParts(['id' => $userdata->id])
            ->setBody(['preferred_first_name' => 'a&#39;d',
                'preferred_last_name' => 'b&#39;c'])
            ->run();
        $this->assertEquals($data->first_name, 'a\'d');
        $this->assertEquals($data->last_name, 'b\'c');

        testRun::testRun($this, 'PUT', '/member/{id}')
            ->setUriParts(['id' => $userdata->id])
            ->setBody(['birthdate' => 'not a date'])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'PUT', '/member/{id}')
            ->setUriParts(['id' => $userdata->id])
            ->setBody(['deceased' => 'not a boolean'])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'PUT', '/member/{id}')
            ->setUriParts(['id' => $userdata->id])
            ->setBody(['do_not_contact' => 'not a boolean'])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'PUT', '/member/{id}')
            ->setUriParts(['id' => $userdata->id])
            ->setBody(['email_optout' => 'not a boolean'])
            ->setExpectedResult(400)
            ->run();

    }


    public function testNewMember(): void
    {
        testRun::testRun($this, 'POST', '/member')
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'POST', '/member')
            ->setBody(['email' => 'phpunit@unit.test'])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'POST', '/member')
            ->setBody(['legal_first_name' => 'phpunit'])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'POST', '/member')
            ->setBody(['legal_last_name' => 'phpunit'])
            ->setExpectedResult(400)
            ->run();

        testRun::testRun($this, 'POST', '/member')
            ->setBody(['email' => 'phpunit@unit.test', 'legal_first_name' => 'Testie'])
            ->run();

        testRun::testRun($this, 'POST', '/member')
            ->setBody(['email' => 'phpunit@unit.test', 'legal_first_name' => 'Testie'])
            ->setExpectedResult(409)
            ->run();

        testRun::testRun($this, 'POST', '/member/{id}/password')
            ->setUriParts(['id' => 'phpunit@unit.test'])
            ->run();

        $this->deleteTestUser();
        $userdata = testRun::testRun($this, 'POST', '/member')
            ->setBody(['email' => 'phpunit@unit.test', 'legal_first_name' => 'Testie', 'legal_last_name' => 'McTester'])
            ->run();

        testRun::testRun($this, 'PUT', '/member/{email}/password/recovery')
            ->setUriParts(['email' => 'phpunit@unit.test'])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'PUT', '/member/{email}/password/recovery')
            ->setUriParts(['email' => 'phpunit@unit.test'])
            ->setBody(['OneTimeCode' => 'asdfasdf'])
            ->setExpectedResult(403)
            ->run();
        testRun::testRun($this, 'PUT', '/member/{email}/password/recovery')
            ->setUriParts(['email' => 'phpunit@unit.test'])
            ->setBody(['NewPassword' => 'asdfasdf'])
            ->setNullReturn()
            ->run();
        testRun::testRun($this, 'PUT', '/member/{email}/password/recovery')
            ->setUriParts(['email' => 'phpunit@unit.test'])
            ->setBody(['NewPassword' => 'asdfasdf', 'OneTimeCode' => 'asdfasdf'])
            ->setExpectedResult(403)
            ->run();
        testRun::testRun($this, 'PUT', '/member/{email}/password')
            ->setUriParts(['email' => 'phpunit@unit.test'])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'PUT', '/member/{email}/password')
            ->setUriParts(['email' => 'phpunit@unit.test'])
            ->setBody(['NewPassword' => 'asdfasdf'])
            ->setNullReturn()
            ->run();
        testRun::testRun($this, 'PUT', '/member/{email}/password/recovery')
            ->setUriParts(['email' => 'badmember@bad.bad'])
            ->setBody(['NewPassword' => 'asdfasdf'])
            ->setExpectedResult(404)
            ->run();

    }


    public function testFindMember() :void
    {
        testRun::testRun($this, 'GET', '/member/find')
            ->setMethodParameters(['q' => ''])
            ->setExpectedResult(400)
            ->run();

        testRun::testRun($this, 'GET', '/member/find')
            ->setMethodParameters(['q' => 'Billy Bob'])
            ->setExpectedResult(404)
            ->run();

        $data = testRun::testRun($this, 'GET', '/member/find')
            ->setMethodParameters(['q' => 'A1000', 'from' => 'id'])
            ->run();
        $this->assertEquals($data->type, 'member_list');
        $this->assertNotEmpty($data->data);
        $this->assertEquals(count($data->data), 1);
        $this->assertEquals($data->data[0]->id, 1000);

        $data = testRun::testRun($this, 'GET', '/member/find')
            ->setMethodParameters(['q' => '1000', 'from' => 'id'])
            ->run();
        $this->assertEquals($data->type, 'member_list');
        $this->assertNotEmpty($data->data);
        $this->assertEquals(count($data->data), 1);
        $this->assertEquals($data->data[0]->id, 1000);

        $data = testRun::testRun($this, 'GET', '/member/find')
            ->setMethodParameters(['q' => 'Odin'])
            ->setExpectedResult(404)
            ->run();

        $data = testRun::testRun($this, 'GET', '/member/find')
            ->setMethodParameters(['q' => 'Od', 'partial' => 'true'])
            ->run();
        $this->assertEquals($data->type, 'member_list');
        $this->assertNotEmpty($data->data);
        $this->assertEquals($data->data[0]->id, 1000);

        testRun::testRun($this, 'GET', '/member/find')
            ->setMethodParameters(['q' => 'Od', 'partial' => 'false'])
            ->setExpectedResult(404)
            ->run();
        testRun::testRun($this, 'GET', '/member/find')
            ->setMethodParameters(['q' => 'Odin', 'from' => 'name', 'partial' => 'false'])
            ->setExpectedResult(404)
            ->run();
        testRun::testRun($this, 'GET', '/member/find')
            ->setMethodParameters(['q' => 'Odin Allf', 'from' => 'name', 'partial' => 'false'])
            ->setExpectedResult(404)
            ->run();

        $data = testRun::testRun($this, 'GET', '/member/find')
            ->setMethodParameters(['q' => 'Odin Allfather', 'from' => 'name', 'partial' => 'false'])
            ->run();
        $this->assertEquals($data->type, 'member_list');
        $this->assertNotEmpty($data->data);
        $this->assertEquals($data->data[0]->id, 1000);

        $data = testRun::testRun($this, 'GET', '/member/find')
            ->setMethodParameters(['q' => 'Odin billybob', 'from' => 'name', 'partial' => 'false'])
            ->setExpectedResult(404)
            ->run();

        $data = testRun::testRun($this, 'GET', '/member/find')
            ->setMethodParameters(['q' => 'Odin', 'from' => 'legal_name', 'partial' => 'true'])
            ->run();
        $this->assertEquals($data->type, 'member_list');
        $this->assertNotEmpty($data->data);
        $this->assertEquals($data->data[0]->id, 1000);

        testRun::testRun($this, 'GET', '/member/find')
            ->setMethodParameters(['q' => 'Odin', 'from' => 'email'])
            ->setExpectedResult(404)
            ->run();

        testRun::testRun($this, 'GET', '/member/find')
            ->setMethodParameters(['q' => 'thiswillnotbefound'])
            ->setExpectedResult(404)
            ->run();

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


        $token1 = testRun::testRun($this, 'POST', '/token')
            ->setBody(['grant_type' => 'password', 'username' => 'phpDupUnit@unit.test', 'password' => 'PassWord1', 'client_id' => 'ciab'])
            ->setVerifyYaml(false)
            ->setExpectedResult(200)
            ->run();

        $basedata = testRun::testRun($this, 'GET', '/member')
            ->setToken($token1)
            ->run();
        $this->assertSame($basedata->id, '8000');
        $this->assertSame($basedata->duplicates, '8001');

        $token2 = testRun::testRun($this, 'POST', '/token')
            ->setBody(['grant_type' => 'password', 'username' => 'phpDupUnit@unit.test', 'password' => 'PassWord2', 'client_id' => 'ciab'])
            ->setVerifyYaml(false)
            ->setExpectedResult(200)
            ->run();

        $basedata = testRun::testRun($this, 'GET', '/member')
            ->setToken($token2)
            ->run();
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
