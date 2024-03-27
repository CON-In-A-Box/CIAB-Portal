<?php declare(strict_types=1);

use App\Service\EventService;
use App\Service\DepartmentService;
use App\Service\MemberService;
use App\Modules\staff\Repository\StaffRepository;
use App\Modules\staff\Service\StaffService;
use PHPUnit\Framework\TestCase;

final class StaffServiceTest extends TestCase
{

    private $mockEventService;

    private $mockDepartmentService;

    private $mockMemberSerivce;

    private $mockStaffRepository;

    private $systemUnderTest;

    private $mockEvent = ["id" => "1"];

    private $mockDepartments = [
      "2" => [
        "id" => "2",
        "name" => "Activities"
      ],
      "100" => [
        "id" => "100",
        "name" => "Book Swap",
        "parent" => [
          "id" => "2",
          "name" => "Activities"
        ]
      ]
      ];

    private $mockStaff = [
      0 => [
        "ListRecordID" => "1",
        "AccountID" => "123",
        "DepartmentID" => "2",
        "EventID" => "1",
        "Note" => "",
        "PositionID" => "1",
        "Position" => "Head"
      ],
      1 => [
        "ListRecordID" => "2",
        "AccountID" => "456",
        "DepartmentID" => "2",
        "EventID" => "1",
        "Note" => "",
        "PositionID" => "2",
        "Position" => "Sub-Head"
      ],
      2 => [
        "ListRecordID" => "3",
        "AccountID" => "789",
        "DepartmentID" => "100",
        "EventID" => "1",
        "Note" => "",
        "PositionID" => "1",
        "Position" => "Head"
      ]
      ];

    private $mockMembers = [
      "123" => [
        "id" => "123",
        "first_name" => "Test",
        "last_name" => "Member",
        "email" => "test-member@test-con.org"
      ],
      "456" => [
        "id" => "456",
        "first_name" => "Other Test",
        "last_name" => "Other Member",
        "email" => "other-test-member@test-con.org"
      ],
      "789" => [
        "id" => "789",
        "first_name" => "Last Test",
        "last_name" => "Last Member",
        "email" => "last-test-member@test-con.org"
      ]
      ];


    protected function setUp(): void
    {
        $this->mockEventService = $this->createMock(EventService::class);
        $this->mockDepartmentService = $this->createMock(DepartmentService::class);
        $this->mockMemberSerivce = $this->createMock(MemberService::class);
        $this->mockStaffRepository = $this->createMock(StaffRepository::class);
        $this->systemUnderTest = new StaffService($this->mockEventService, $this->mockDepartmentService, $this->mockMemberSerivce, $this->mockStaffRepository);

    }


    public function testGetStaffInDepartment(): void
    {
        $this->mockEventService->expects($this->once())
            ->method("getCurrentEvent")
            ->willReturn($this->mockEvent);
        $this->mockDepartmentService->expects($this->once())
            ->method("getById")
            ->with("2")
            ->willReturn($this->mockDepartments);
        $this->mockStaffRepository->expects($this->once())
            ->method("findStaffByDepartmentIDs")
            ->with("1", ["2", "100"])
            ->willReturn($this->mockStaff);
        $this->mockMemberSerivce->expects($this->once())
            ->method("getById")
            ->with(["123", "456", "789"])
            ->willReturn($this->mockMembers);

        $result = $this->systemUnderTest->getStaffInDepartment("2");
        $this->assertCount(3, $result);

        $staff = $result[0];
        $this->assertEquals("1", $staff["id"]);
        $this->assertEquals("1", $staff["positionId"]);
        $this->assertEquals("", $staff["note"]);
        $this->assertEquals("Head", $staff["position"]);
        $this->assertEquals($this->mockMembers["123"], $staff["member"]);
        $this->assertEquals($this->mockDepartments["2"], $staff["department"]);

        $staff = $result[1];
        $this->assertEquals("2", $staff["id"]);
        $this->assertEquals("2", $staff["positionId"]);
        $this->assertEquals("", $staff["note"]);
        $this->assertEquals("Sub-Head", $staff["position"]);
        $this->assertEquals($this->mockMembers["456"], $staff["member"]);
        $this->assertEquals($this->mockDepartments["2"], $staff["department"]);

        $staff = $result[2];
        $this->assertEquals("3", $staff["id"]);
        $this->assertEquals("1", $staff["positionId"]);
        $this->assertEquals("", $staff["note"]);
        $this->assertEquals("Head", $staff["position"]);
        $this->assertEquals($this->mockMembers["789"], $staff["member"]);
        $this->assertEquals($this->mockDepartments["100"], $staff["department"]);
        
    }


    public function testPost(): void
    {
        $this->expectException(Exception::class);
        $this->systemUnderTest->post([]);

    }

    
    public function testListAll(): void
    {
        $this->expectException(Exception::class);
        $this->systemUnderTest->listAll();

    }

    
    public function testGetById(): void
    {
        $this->expectException(Exception::class);
        $this->systemUnderTest->getById("123");

    }


    public function testPut(): void
    {
        $this->expectException(Exception::class);
        $this->systemUnderTest->put("123", []);

    }


    public function testDeleteById(): void
    {
        $this->expectException(Exception::class);
        $this->systemUnderTest->deleteById("123");

    }


    /* End StaffServiceTest */
}
