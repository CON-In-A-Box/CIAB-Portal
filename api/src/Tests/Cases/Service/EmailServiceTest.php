<?php declare(strict_types=1);

namespace App\Tests\TestCase\Service;

use App\Repository\EmailRepository;
use App\Service\EmailService;
use PHPUnit\Framework\TestCase;
use Exception;

final class EmailServiceTest extends TestCase
{

    private $emailRepositoryStub;

    /**
     * @var EmailService
     */
    private $systemUnderTest;


    protected function setUp(): void
    {
        $this->emailRepositoryStub = $this->createStub(EmailRepository::class);
        $this->systemUnderTest = new EmailService($this->emailRepositoryStub);

    }


    public function testPost(): void
    {
        $emailId = 123;
        $this->emailRepositoryStub->method('insert')
            ->willReturn($emailId);
        
        $data = [
            "Email" => "test-email@test.com",
            "DepartmentID" => 100
        ];

        $result = $this->systemUnderTest->post($data);
        $this->assertEquals($emailId, $result);

    }


    public function testListAll(): void
    {
        $this->expectException(Exception::class);
        $this->systemUnderTest->listAll();

    }


    public function testGetById(): void
    {
        $emailId = 123;
        $this->emailRepositoryStub->method("selectById")
            ->willReturn([
                "EMailAliasID" => $emailId,
                "DepartmentID" => 456,
                "IsAlias" => null,
                "EMail" => "test-email@test.com"
            ]);

        $result = $this->systemUnderTest->getById($emailId);
        $this->assertEquals($emailId, $result["id"]);
        $this->assertEquals(456, $result["departmentId"]);
        $this->assertEquals(false, $result["isAlias"]);
        $this->assertEquals("test-email@test.com", $result["email"]);
                
    }


    public function testGetByIdWithAlias(): void
    {
        $emailId = 123;
        $this->emailRepositoryStub->method("selectById")
            ->willReturn([
                "EMailAliasID" => $emailId,
                "DepartmentID" => 456,
                "IsAlias" => 1,
                "EMail" => "test-email@test.com"
            ]);

        $result = $this->systemUnderTest->getById($emailId);
        $this->assertEquals(1, $result["isAlias"]);

    }


    public function testPut(): void
    {
        $this->emailRepositoryStub->expects($this->once())
            ->method('update');

        $this->systemUnderTest->put("id", []);

    }


    public function testDeleteById(): void
    {
        $this->emailRepositoryStub->expects($this->once())
            ->method('deleteById');
        $this->systemUnderTest->deleteById("id");

    }


    public function testDeleteByDepartmentId(): void
    {
        $departmentId = 123;
        $this->emailRepositoryStub->expects($this->once())
            ->method('deleteByDepartmentId');

        $this->systemUnderTest->deleteByDepartmentId($departmentId);
        
    }


    /* End EmailServiceTest */
}
