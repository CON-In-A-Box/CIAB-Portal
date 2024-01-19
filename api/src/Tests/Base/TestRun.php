<?php

namespace App\Tests\Base;

require_once __DIR__.'/CiabTestCase.php';

class TestRun
{


    public function __construct(CiabTestCase $ciabCase, string $method, string $uri)
    {
        $this->ciabCase = $ciabCase;
        $this->method = $method;
        $this->uri = $uri;
        $this->uriParts = null;
        $this->methodParameters = null;
        $this->body = null;
        $this->NPLoginIndex = null;
        $this->token = null;
        $this->verifyYaml = true;
        $this->json = true;
        $this->nullReturn = false;
        if (strcasecmp($method, 'post') == 0) {
            $this->expectedResult = 201;
        } elseif (strcasecmp($method, 'delete') == 0) {
            $this->expectedResult = 204;
        } else {
            $this->expectedResult = 200;
        }

    }


    public function setUriParts(array $parts): TestRun
    {
        $this->uriParts = $parts;
        return $this;

    }


    public function setMethodParameters(array $params): TestRun
    {
        $this->methodParameters = $params;
        return $this;

    }


    public function setBody(array $body): TestRun
    {
        $this->body = $body;
        return $this;

    }


    public function setExpectedResult(int $result): TestRun
    {
        $this->expectedResult = $result;
        return $this;

    }


    public function setNpLoginIndex(?int $index): TestRun
    {
        $this->NPLoginIndex = $index;
        return $this;

    }


    public function setToken(int $token): TestRun
    {
        $this->token = $token;
        return $this;

    }


    public function setVerifyYaml(bool $verify): TestRun
    {
        $this->verifyYaml = $verify;
        return $this;

    }


    public function setJson(bool $json): TestRun
    {
        $this->json = $json;
        return $this;

    }


    public function setNullReturn(): TestRun
    {
        $this->nullReturn = true;
        return $this;

    }


    public function run()
    {
        if (!$this->method) {
            throw new UnexpectedValueException('Test Data does not have method');
        }
        if (!$this->uri) {
            throw new UnexpectedValueException('Test Data does not have uri');
        }
        if ($this->token && $this->NPLoginIndex) {
            throw new UnexpectedValueException('Test Data has both conflicting token and NpLoginIndex.');
        }

        $target = $this->uri;
        if ($this->uriParts) {
            preg_match_all('~\{(.*?)\}~s', $this->uri, $datas);
            foreach ($datas[1] as $value) {
                $target = str_replace('{'.$value.'}', $this->uriParts[$value], $target);
            }
        }

        if ($this->NPLoginIndex !== null) {
            $response = $this->ciabCase->NPRunRequest(
                $this->method,
                $target,
                $this->methodParameters,
                $this->body,
                $this->expectedResult,
                $this->NPLoginIndex,
                ($this->verifyYaml) ? $this->uri : null
            );
        } else {
            $response = $this->ciabCase->runRequest(
                $this->method,
                $target,
                $this->methodParameters,
                $this->body,
                $this->expectedResult,
                $this->token,
                ($this->verifyYaml) ? $this->uri : null
            );
        }
        if ($this->json) {
            $data = json_decode((string)$response->getBody());
            if (!$this->nullReturn) {
                $this->ciabCase->assertNotEmpty($data);
            } else {
                $this->ciabCase->assertNull($data);
            }
            return $data;
        }

        return $response;

    }


    public static function testRun(CiabTestCase $ciabCase, string $method, string $uri): testRun
    {
        return new testRun($ciabCase, $method, $uri);

    }


    /* End TestRun */
}
