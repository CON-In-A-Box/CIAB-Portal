<?php declare(strict_types=1);

namespace App\Tests\TestCase\Service;

use App\Repository\DepartmentRepository;
use App\Service\DepartmentService;
use App\Service\EmailService;
use App\Service\EmailListAccessService;
use App\Service\PermissionService;
use PHPUnit\Framework\TestCase;

final class DepartmentServiceTest extends TestCase
{

    private $emailServiceStub;

    private $emailAccessListServiceStub;

    private $permissionServiceStub;

    private $deptRepositoryStub;

    /**
     * @var DepartmentService
     */
    private $systemUnderTest;


    protected function setUp(): void
    {
        $this->emailServiceStub = $this->createStub(EmailService::class);
        $this->emailAccessListServiceStub = $this->createStub(EmailListAccessService::class);
        $this->permissionServiceStub = $this->createStub(PermissionService::class);
        $this->deptRepositoryStub = $this->createStub(DepartmentRepository::class);
        $this->systemUnderTest = new DepartmentService($this->emailServiceStub, $this->emailAccessListServiceStub, $this->permissionServiceStub, $this->deptRepositoryStub);

    }


    public function testListAll(): void
    {
        $departments = [
          0 => [
            "DepartmentID" => 2,
            "Name" => "Department",
            "ParentID" => null,
            "ParentName" => null,
            "FallbackID" => null,
            "FallbackName" => null,
            "ChildCount" => 1,
            "Email" => "email@test-con.org"
          ],
          1 => [
            "DepartmentID" => 100,
            "Name" => "Child Department",
            "ParentID" => 2,
            "ParentName" => "Department",
            "FallbackID" => null,
            "FallbackName" => null,
            "ChildCount" => 0,
            "Email" => "dept@test-con.org"
          ],
          2 => [
            "DepartmentID" => 3,
            "Name" => "Other Department",
            "ParentID" => null,
            "ParentName" => null,
            "FallbackID" => 2,
            "FallbackName" => "Department",
            "ChildCount" => 5,
            "Email" => "other-dept@test-con.org,extra-email@test-con.org"
          ]
          ];

        $this->deptRepositoryStub->method('selectAll')
            ->willReturn($departments);

        $result = $this->systemUnderTest->listAll();

        $this->assertCount(3, $result);

        $dept = $result[0];
        $this->assertEquals(2, $dept["id"]);
        $this->assertEquals("Department", $dept["name"]);
        $this->assertEquals(1, $dept["child_count"]);
        $this->assertEquals("department", $dept["type"]);
        $this->assertEquals("email@test-con.org", $dept["email"][0]);
        $this->assertNull($dept["parent"]);
        $this->assertNull($dept["fallback"]);

        $dept = $result[1];
        $this->assertEquals(100, $dept["id"]);
        $this->assertEquals("Child Department", $dept["name"]);
        $this->assertEquals(0, $dept["child_count"]);
        $this->assertEquals("department", $dept["type"]);
        $this->assertEquals("dept@test-con.org", $dept["email"][0]);
        $this->assertEquals(2, $dept["parent"]["id"]);
        $this->assertEquals("Department", $dept["parent"]["name"]);
        $this->assertEquals("department", $dept["parent"]["type"]);
        $this->assertNull($dept["fallback"]);

        $dept = $result[2];
        $this->assertEquals(3, $dept["id"]);
        $this->assertEquals("Other Department", $dept["name"]);
        $this->assertEquals(5, $dept["child_count"]);
        $this->assertEquals("department", $dept["type"]);
        $this->assertEquals("other-dept@test-con.org", $dept["email"][0]);
        $this->assertEquals("extra-email@test-con.org", $dept["email"][1]);
        $this->assertNull($dept["parent"]);
        $this->assertEquals(2, $dept["fallback"]["id"]);
        $this->assertEquals("Department", $dept["fallback"]["name"]);
        $this->assertEquals("department", $dept["fallback"]["type"]);

    }


    public function testGetById()
    {
        $testData = [
          0 => [
            "DepartmentID" => 100,
            "Name" => "Child Department"
          ]
          ];

        $this->deptRepositoryStub->method('selectById')
            ->willReturn($testData);

        $result = $this->systemUnderTest->getById("100");

        $this->assertCount(1, $result);

        $dept = $result["100"];
        $this->assertEquals(100, $dept["id"]);
        $this->assertEquals("Child Department", $dept["name"]);
        $this->assertEquals("department", $dept["type"]);
        $this->assertEquals(false, array_key_exists("child_count", $dept));
        $this->assertEquals([], $dept["email"]);
        $this->assertNull($dept["parent"]);
        $this->assertNull($dept["fallback"]);

    }


    public function testPost()
    {
        $deptId = 123;
        $this->deptRepositoryStub->method('insert')
            ->willReturn($deptId);

        $testData = [
          "Name" => "Test Name"
        ];
        
        $result = $this->systemUnderTest->post($testData);
        $this->assertEquals($deptId, $result);

    }


    public function testPut()
    {
        $deptId = 123;
        $data = [
          "Name" => "Updated Name"
        ];

        $this->deptRepositoryStub->expects($this->once())
            ->method('update');

        $this->systemUnderTest->put($deptId, $data);
        
    }


    public function testDeleteById()
    {
        $deptId = 123;

        $this->emailServiceStub->expects($this->once())
            ->method('deleteByDepartmentId');
        $this->emailAccessListServiceStub->expects($this->once())
            ->method('deleteByDepartmentId');
        $this->deptRepositoryStub->expects($this->once())
            ->method('deleteById');

        $this->systemUnderTest->deleteById($deptId);

    }


    public function testListAllEmails()
    {
        $deptId = 123;

        $this->emailServiceStub->expects($this->once())
            ->method('listAllByDepartmentId');

        $this->systemUnderTest->listAllEmails($deptId);
        
    }


    public function testListPermissionsByDept()
    {
        $deptId = 2;

        $this->permissionServiceStub->expects($this->once())
            ->method('getByDepartmentId');
        $this->systemUnderTest->listPermissionsByDepartment($deptId);
        
    }


    /* End DepartmentServiceTest */
}
