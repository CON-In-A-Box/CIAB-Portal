<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;

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
        $this->runRequest('POST', '/token', null, $body, $code);

    }


    public function testRefreshTokens(): void
    {
        /* no token */
        $this->runRequest('GET', '/member', null, null, 401);

        $this->token = $this->runSuccessJsonRequest('POST', '/token', null, ['grant_type' => 'password', 'username' => self::$login, 'password' => self::$password, 'client_id' => self::$client]);

        $this->runSuccessJsonRequest('GET', '/member');

        $this->token = $this->runSuccessJsonRequest('POST', '/token', null, ['grant_type' => 'refresh_token', 'refresh_token' => $this->token->refresh_token, 'client_id' => 'ciab']);

        $this->runSuccessJsonRequest('GET', '/member');

    }


    /* END */
}
