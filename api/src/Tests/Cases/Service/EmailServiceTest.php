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
        $this->expectException(Exception::class);
        $this->systemUnderTest->post("id");

    }


    public function testListAll(): void
    {
        $this->expectException(Exception::class);
        $this->systemUnderTest->listAll();

    }


    public function testGetById(): void
    {
        $this->expectException(Exception::class);
        $this->systemUnderTest->getById("id");

    }


    public function testPut(): void
    {
        $this->expectException(Exception::class);
        $this->systemUnderTest->put("id", []);

    }


    public function testDeleteById(): void
    {
        $this->expectException(Exception::class);
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
