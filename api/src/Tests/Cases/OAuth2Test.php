<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;
use App\Tests\Base\TestRun;

class OAuth2Test extends CiabTestCase
{


    protected function setUp(): void
    {
        $this->setupToken = false;
        parent::setUp();

    }


    public function provider(): array
    {
        return array(
            array(['grant_type' => 'passwordx', 'username' => self::$login, 'password' => self::$password, 'client_id' => self::$client], 400),
            array(['grant_type' => 'password', 'username' => 'nope@nope.nope', 'password' => self::$password, 'client_id' => self::$client], 401),
            array(['grant_type' => 'password', 'username' => self::$login, 'password' => 'nope', 'client_id' => self::$client], 401),
            array(['grant_type' => 'password', 'username' => self::$login, 'password' => self::$password, 'client_id' => 'nope'], 400)
        );

    }


    /**
     * @test
     * @dataProvider provider
     **/
    public function testFailures($body, $code): void
    {
        testRun::testRun($this, 'POST', '/token')
            ->setBody($body)
            ->setExpectedResult($code)
            ->setVerifyYaml(false)
            ->run();

    }


    public function testRefreshTokens(): void
    {
        /* no token */
        testRun::testRun($this, 'GET', '/member')
            ->setExpectedResult(401)
            ->setNullReturn()
            ->run();

        $this->token = testRun::testRun($this, 'POST', '/token')
            ->setBody(['grant_type' => 'password', 'username' => self::$login, 'password' => self::$password, 'client_id' => self::$client])
            ->setVerifyYaml(false)
            ->setExpectedResult(200)
            ->run();

        testRun::testRun($this, 'GET', '/member')->run();

        $this->token = testRun::testRun($this, 'POST', '/token')
            ->setBody(['grant_type' => 'refresh_token', 'refresh_token' => $this->token->refresh_token, 'client_id' => 'ciab'])
            ->setVerifyYaml(false)
            ->setExpectedResult(200)
            ->run();

        testRun::testRun($this, 'GET', '/member')->run();

    }


    /* END */
}
