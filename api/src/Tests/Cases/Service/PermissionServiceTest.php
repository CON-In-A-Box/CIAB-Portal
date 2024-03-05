<?php declare(strict_types=1);

use App\Repository\PermissionRepository;
use App\Service\PermissionService;
use PHPUnit\Framework\TestCase;

final class PermissionServiceTest extends TestCase
{

    private $permissionRepositoryStub;

    /**
     * @var PermissionService
     */
    private $systemUnderTest;


    protected function setUp(): void
    {
        $this->permissionRepositoryStub = $this->createStub(PermissionRepository::class);
        $this->systemUnderTest = new PermissionService($this->permissionRepositoryStub);

    }


    public function testListAll(): void
    {
        $this->expectException(Exception::class);
        $this->systemUnderTest->listAll();

    }


    public function testPost(): void
    {
        $this->expectException(Exception::class);
        $this->systemUnderTest->post(123);

    }


    public function testGetById(): void
    {
        $this->expectException(Exception::class);
        $this->systemUnderTest->getById(123);

    }


    public function testPut(): void
    {
        $this->expectException(Exception::class);
        $this->systemUnderTest->put(123, []);

    }


    public function testDeleteById(): void
    {
        $this->expectException(Exception::class);
        $this->systemUnderTest->deleteById(123);

    }


    public function testGetByDepartmentId(): void
    {
        $expected = [
          0 => [
            "type" => "department_permission",
            "id" => 1,
            "departmentId" => "all",
            "position" => 1,
            "name" => "concom.view",
            "note" => null
          ],
          1 => [
            "type" => "department_permission",
            "id" => 2,
            "departmentId" => "all",
            "position" => 1,
            "name" => "site.concom.permissions",
            "note" => null
          ],
          2 => [
            "type" => "department_permission",
            "id" => 3,
            "departmentId" => "2",
            "position" => 1,
            "name" => "site.concom.structure",
            "note" => null
          ]
        ];

        $this->permissionRepositoryStub->method("selectAll")
            ->willReturn([
              0 => [
                "PermissionID" => 1,
                "Position" => "all.1",
                "Permission" => "concom.view",
                "Note" => null
              ],
              1 => [
                "PermissionID" => 2,
                "Position" => "all.1",
                "Permission" => "site.concom.permissions",
                "Note" => null
              ],
              2 => [
                "PermissionID" => 3,
                "Position" => "2.1",
                "Permission" => "site.concom.structure",
                "Note" => null
              ]
            ]);

        $result = $this->systemUnderTest->getByDepartmentId(2);
        $this->assertEquals($expected, $result);

    }


    /* End PermissionServiceTest */
}
