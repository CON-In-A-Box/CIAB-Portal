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


  /* End EmailTest */
}
