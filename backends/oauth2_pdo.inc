<?php

namespace CIAB\OAuth2;

require_once __DIR__."/../functions/functions.inc";
require_once __DIR__."/../vendor/autoload.php";
require_once __DIR__."/../functions/authentication.inc";
require_once __DIR__."/../functions/users.inc";

use \OAuth2\OpenID\Storage\UserClaimsInterface;
use \OAuth2\OpenID\Storage\AuthorizationCodeInterface as OpenIDAuthorizationCodeInterface;

class Pdo implements
    \OAuth2\OpenID\Storage\AuthorizationCodeInterface,
    \OAuth2\Storage\AccessTokenInterface,
    \OAuth2\Storage\ClientCredentialsInterface,
    \OAuth2\Storage\UserCredentialsInterface,
    \OAuth2\Storage\RefreshTokenInterface,
    \OAuth2\Storage\JwtBearerInterface,
    \OAuth2\Storage\ScopeInterface,
    \OAuth2\Storage\PublicKeyInterface,
    \OAuth2\OpenID\Storage\UserClaimsInterface
{

    protected $base;


    public function __construct($connection, $config = array())
    {
        $this->base = new \OAuth2\Storage\Pdo($connection, $config);

    }


    public function checkClientCredentials($client_id, $client_secret = null)
    {
        return $this->base->checkClientCredentials($client_id, $client_secret);

    }


    public function isPublicClient($client_id)
    {
        return $this->base->isPublicClient($client_id);

    }


    public function getClientDetails($client_id)
    {
        return $this->base->getClientDetails($client_id);

    }


    public function setClientDetails($client_id, $client_secret = null, $redirect_uri = null, $grant_types = null, $scope = null, $user_id = null)
    {
        return $this->base->setClientDetails($client_id, $client_secret, $redirect_uri, $grant_types, $scope, $user_id);

    }


    public function checkRestrictedGrantType($client_id, $grant_type)
    {
        return $this->base->checkRestrictedGrantType($client_id, $grant_type);

    }


    public function getAccessToken($access_token)
    {
        return $this->base->getAccessToken($access_token);

    }


    public function setAccessToken($access_token, $client_id, $user_id, $expires, $scope = null)
    {
        return $this->base->setAccessToken($access_token, $client_id, $user_id, $expires, $scope);

    }


    public function unsetAccessToken($access_token)
    {
        return $this->base->unsetAccessToken($access_token);

    }


    public function getAuthorizationCode($code)
    {
        return $this->base->getAuthorizationCode($code);

    }


    public function setAuthorizationCode($code, $client_id, $user_id, $redirect_uri, $expires, $scope = null, $id_token = null)
    {
        return $this->base->setAuthorizationCode($code, $client_id, $user_id, $redirect_uri, $expires, $scope, $id_token);

    }


    public function expireAuthorizationCode($code)
    {
        return $this->base->expireAuthorizationCode($code);

    }


    /* ARIC CUSTOM */
    public function checkUserCredentials($username, $password)
    {
        $ret = \doUserAndPasswordMatch($username, $password);
        return ($ret['entry'] !== null);

    }


    public function getUserDetails($username)
    {
        return $this->getUser($username);

    }


    public function getUserClaims($user_id, $claims)
    {
        return $this->base->getUserClaims($user_id, $claims);

    }


    public function getRefreshToken($refresh_token)
    {
        return $this->base->getRefreshToken($refresh_token);

    }


    public function setRefreshToken($refresh_token, $client_id, $user_id, $expires, $scope = null)
    {
        return $this->base->setRefreshToken($refresh_token, $client_id, $user_id, $expires, $scope);

    }


    public function unsetRefreshToken($refresh_token)
    {
        return $this->base->unsetRefreshToken($refresh_token);

    }


    /* ARIC CUSTOM */
    public function getUser($username)
    {
        $value = \lookup_user_by_login($username);
        if (empty($value)) {
            return false;
        }
        return array_merge(array(
            'user_id' => $username
        ), [$value[0]['AccountID']]);

    }


    /* ARIC CUSTOM  */
    public function setUser($username, $password, $firstName = null, $lastName = null)
    {
        // if it exists, update it.
        if ($this->getUser($username)) {
            \set_password($username, $password);
            return true;
        }
        return false;

    }


    public function scopeExists($scope)
    {
        return $this->base->scopeExists($scope);

    }


    public function getDefaultScope($client_id = null)
    {
        return $this->base->getDefaultScope($client_id);

    }


    public function getClientKey($client_id, $subject)
    {
        return $this->base->getClientKey($client_id, $subject);

    }


    public function getClientScope($client_id)
    {
        return $this->base->getClientScope($client_id);

    }


    public function getJti($client_id, $subject, $audience, $expires, $jti)
    {
        return $this->base->getJti($client_id, $subject, $audience, $expires, $jti);

    }


    public function setJti($client_id, $subject, $audience, $expires, $jti)
    {
        return $this->base->setJti($client_id, $subject, $audience, $expires, $jti);

    }


    public function getPublicKey($client_id = null)
    {
        return $this->base->getPublicKey($client_id);

    }


    public function getPrivateKey($client_id = null)
    {
        return $this->base->getPrivateKey($client_id);

    }


    public function getEncryptionAlgorithm($client_id = null)
    {
        return $this->base->getEncryptionAlgorithm($client_id);

    }


    public function getBuildSql($dbName = 'oauth2_server_php')
    {
        $sql = $this->base->getBuildSql($dbName);
        $pattern = '/CREATE TABLE.*\n+.*username[\w\s(),]*;/';
        $replacement = '';
        return preg_replace($pattern, $replacement, $sql);
    }


    /* END */
}
