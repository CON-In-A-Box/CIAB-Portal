<?php declare(strict_types=1);

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;
use App\Tests\Base\TestRun;

use Atlas\Query\Delete;

class EmailTest extends CiabTestCase
{

    /**
     * @var int
     */
    protected $departmentId = -1;

    /**
     * @var int
     */
    protected $emailId = -1;


    protected function setUp(): void
    {
        parent::setUp();

        $body = [
          "Name" => "Test Department For Email"
        ];

        $data = testRun::testRun($this, 'POST', '/department')
            ->setBody($body)
            ->setVerifyYaml(false)
            ->run();

        $this->departmentId = $data->id;

    }


    protected function tearDown(): void
    {
        // Need to delete email from database directly first before we can tear down the department.
        $db = $this->container->get("db");
        if ($this->emailId != -1) {
            $delete = Delete::new($db);
            $delete->from("EMails")
                ->whereEquals(["EMailAliasID" => $this->emailId])
                ->perform();

            $this->emailId = -1;
        }

        testRun::testRun($this, 'DELETE', '/department/{id}')
            ->setUriParts(["id" => $this->departmentId])
            ->setVerifyYaml(false)
            ->run();

        parent::tearDown();

    }


    public function testPostEmail(): void
    {
        $body = [
          "Email" => "test-email@test.com",
          "DepartmentID" => $this->departmentId
        ];

        $result = testRun::testRun($this, 'POST', '/email')
            ->setBody($body)
            ->run();

        $this->assertNotEmpty($result->id);
        $this->assertEquals($this->departmentId, $result->departmentId);
        $this->assertEquals($body["Email"], $result->email);
        $this->assertEquals(false, $result->isAlias);

        $this->emailId = $result->id;

    }


    public function testPostEmailMissingEmail(): void
    {
        $body = [
          "DepartmentID" => $this->departmentId
        ];

        testRun::testRun($this, 'POST', '/email')
            ->setBody($body)
            ->setExpectedResult(400)
            ->run();

    }


    public function testPostEmailMissingDepartmentID(): void
    {
        $body = [
          "Email" => "test-email@test.com"
        ];

        testRun::testRun($this, 'POST', '/email')
            ->setBody($body)
            ->setExpectedResult(400)
            ->run();

    }


    public function testPostEmailInvalidEmail(): void
    {
        $body = [
          "Email" => "",
          "DepartmentID" => $this->departmentId
        ];

        testRun::testRun($this, 'POST', '/email')
            ->setBody($body)
            ->setExpectedResult(400)
            ->run();

    }


    public function testPostEmailInvalidDepartmnetID(): void
    {
        $body = [
          "Email" => "test-email@test.com",
          "DepartmentID" => -1
        ];

        testRun::testRun($this, 'POST', '/email')
            ->setBody($body)
            ->setExpectedResult(400)
            ->run();

    }


    public function testGetEmail(): void
    {
        $body = [
          "Email" => "test-email@test.com",
          "DepartmentID" => $this->departmentId
        ];

        $emailObj = testRun::testRun($this, 'POST', '/email')
            ->setBody($body)
            ->run();

        $this->emailId = $emailObj->id;

        $result = testRun::testRun($this, 'GET', '/email/{id}')
            ->setUriParts(["id" => $emailObj->id])
            ->run();

        $this->assertEquals($emailObj, $result);

    }


    public function testGetEmailInvalidId(): void
    {
        $result = testRun::testRun($this, 'GET', '/email/{id}')
            ->setUriParts(["id" => -1])
            ->setExpectedResult(404)
            ->run();

    }
    

    public function testPutEmailUpdateEmailOnly(): void
    {
        $originalBody = [
            "Email" => "test-email@test.com",
            "DepartmentID" => $this->departmentId
        ];
        $originalEmail = testRun::testRun($this, 'POST', '/email')
            ->setBody($originalBody)
            ->setVerifyYaml(false)
            ->run();

        $this->emailId = $originalEmail->id;

        $updatedBody = [
            "Email" => "updated-test-email@test.com"
        ];
        $updatedEmail = testRun::testRun($this, 'PUT', '/email/{id}')
            ->setBody($updatedBody)
            ->setUriParts(["id" => $originalEmail->id])
            ->run();

        $this->assertEquals($originalEmail->id, $updatedEmail->id);
        $this->assertEquals("updated-test-email@test.com", $updatedEmail->email);

    }

    
    public function testPutEmailUpdateDepartmentIDOnly(): void
    {
        $otherDeptBody = [
            "Name" => "Test Department For Email"
          ];
  
        $otherDept = testRun::testRun($this, 'POST', '/department')
            ->setBody($otherDeptBody)
            ->setVerifyYaml(false)
            ->run();

        $originalBody = [
            "Email" => "test-email@test.com",
            "DepartmentID" => $otherDept->id
        ];
        $originalEmail = testRun::testRun($this, 'POST', '/email')
            ->setBody($originalBody)
            ->setVerifyYaml(false)
            ->run();

        $this->emailId = $originalEmail->id;

        $updatedBody = [
            "DepartmentID" => $this->departmentId
        ];
        $updatedEmail = testRun::testRun($this, 'PUT', '/email/{id}')
            ->setBody($updatedBody)
            ->setUriParts(["id" => $originalEmail->id])
            ->run();

        $this->assertEquals($originalEmail->id, $updatedEmail->id);
        $this->assertEquals($this->departmentId, $updatedEmail->departmentId);

        testRun::testRun($this, 'DELETE', '/department/{id}')
            ->setUriParts(["id" => $otherDept->id])
            ->setVerifyYaml(false)
            ->run();

    }


    public function testPutEmailUpdateAliasOnly(): void
    {
        $originalBody = [
            "Email" => "test-email@test.com",
            "DepartmentID" => $this->departmentId
        ];
        $originalEmail = testRun::testRun($this, 'POST', '/email')
            ->setBody($originalBody)
            ->setVerifyYaml(false)
            ->run();

        $this->emailId = $originalEmail->id;

        $updatedBody = [
            "IsAlias" => 1
        ];
        $updatedEmail = testRun::testRun($this, 'PUT', '/email/{id}')
            ->setBody($updatedBody)
            ->setUriParts(["id" => $originalEmail->id])
            ->run();

        $this->assertEquals($originalEmail->id, $updatedEmail->id);
        $this->assertEquals(1, $updatedEmail->isAlias);

    }


    public function testPutEmailNoParams(): void
    {
        $originalBody = [
            "Email" => "test-email@test.com",
            "DepartmentID" => $this->departmentId
        ];
        $originalEmail = testRun::testRun($this, 'POST', '/email')
            ->setBody($originalBody)
            ->setVerifyYaml(false)
            ->run();

        $this->emailId = $originalEmail->id;

        $updatedBody = [];
        testRun::testRun($this, 'PUT', '/email/{id}')
            ->setBody($updatedBody)
            ->setUriParts(["id" => $originalEmail->id])
            ->setExpectedResult(400)
            ->run();

    }


    public function testPutEmailInvalidParams(): void
    {
        $originalBody = [
            "Email" => "test-email@test.com",
            "DepartmentID" => $this->departmentId
        ];
        $originalEmail = testRun::testRun($this, 'POST', '/email')
            ->setBody($originalBody)
            ->setVerifyYaml(false)
            ->run();

        $this->emailId = $originalEmail->id;

        $updatedBody = [
            "Unknown" => "Value"
        ];
        testRun::testRun($this, 'PUT', '/email/{id}')
            ->setBody($updatedBody)
            ->setUriParts(["id" => $originalEmail->id])
            ->setExpectedResult(400)
            ->run();

    }


    public function testPutEmailInvalidEmail(): void
    {
        $originalBody = [
            "Email" => "test-email@test.com",
            "DepartmentID" => $this->departmentId
        ];
        $originalEmail = testRun::testRun($this, 'POST', '/email')
            ->setBody($originalBody)
            ->setVerifyYaml(false)
            ->run();

        $this->emailId = $originalEmail->id;

        $updatedBody = [
            "Email" => ""
        ];
        testRun::testRun($this, 'PUT', '/email/{id}')
            ->setBody($updatedBody)
            ->setUriParts(["id" => $originalEmail->id])
            ->setExpectedResult(400)
            ->run();
            
    }


    public function testPutEmailInvalidDepartmentIDNumber(): void
    {
        $originalBody = [
            "Email" => "test-email@test.com",
            "DepartmentID" => $this->departmentId
        ];
        $originalEmail = testRun::testRun($this, 'POST', '/email')
            ->setBody($originalBody)
            ->setVerifyYaml(false)
            ->run();

        $this->emailId = $originalEmail->id;

        $updatedBody = [
            "DepartmentID" => "-1"
        ];
        testRun::testRun($this, 'PUT', '/email/{id}')
            ->setBody($updatedBody)
            ->setUriParts(["id" => $originalEmail->id])
            ->setExpectedResult(400)
            ->run();
            
    }


    public function testPutEmailInvalidDepartmentIDValue(): void
    {
        $originalBody = [
            "Email" => "test-email@test.com",
            "DepartmentID" => $this->departmentId
        ];
        $originalEmail = testRun::testRun($this, 'POST', '/email')
            ->setBody($originalBody)
            ->setVerifyYaml(false)
            ->run();

        $this->emailId = $originalEmail->id;

        $updatedBody = [
            "DepartmentID" => "Value"
        ];
        testRun::testRun($this, 'PUT', '/email/{id}')
            ->setBody($updatedBody)
            ->setUriParts(["id" => $originalEmail->id])
            ->setExpectedResult(400)
            ->run();
            
    }


    public function testPutEmailInvalidAliasNumber(): void
    {
        $originalBody = [
            "Email" => "test-email@test.com",
            "DepartmentID" => $this->departmentId
        ];
        $originalEmail = testRun::testRun($this, 'POST', '/email')
            ->setBody($originalBody)
            ->setVerifyYaml(false)
            ->run();

        $this->emailId = $originalEmail->id;

        $updatedBody = [
            "IsAlias" => 5
        ];
        testRun::testRun($this, 'PUT', '/email/{id}')
            ->setBody($updatedBody)
            ->setUriParts(["id" => $originalEmail->id])
            ->setExpectedResult(400)
            ->run();
            
    }


    public function testPutEmailInvalidAliasValue(): void
    {
        $originalBody = [
            "Email" => "test-email@test.com",
            "DepartmentID" => $this->departmentId
        ];
        $originalEmail = testRun::testRun($this, 'POST', '/email')
            ->setBody($originalBody)
            ->setVerifyYaml(false)
            ->run();

        $this->emailId = $originalEmail->id;

        $updatedBody = [
            "IsAlias" => "Value"
        ];
        testRun::testRun($this, 'PUT', '/email/{id}')
            ->setBody($updatedBody)
            ->setUriParts(["id" => $originalEmail->id])
            ->setExpectedResult(400)
            ->run();
            
    }


  /* End EmailTest */
}
