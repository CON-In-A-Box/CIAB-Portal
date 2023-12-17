<?php declare(strict_types=1);

use App\Repository\MemberRepository;
use App\Service\MemberService;
use PHPUnit\Framework\TestCase;

final class MemberServiceTest extends TestCase
{

    private $memberRepositoryMock;

    private $systemUnderTest;

    private $testData = [
      0 => [
        "AccountID" => "123",
        "LegalFirstName" => "First",
        "LegalLastName" => "Last",
        "MiddleName" => "Middle",
        "Suffix" => null,
        "Email" => "email@test-con.org",
        "Email2" => null,
        "Email3" => null,
        "Phone" => "555-555-5555",
        "Phone2" => null,
        "AddressLine1" => "123 Any Street",
        "AddressLine2" => null,
        "AddressCity" => "Anywhere",
        "AddressState" => "CA",
        "AddressZipCode" => "90210",
        "AddressZipCodeSuffix" => null,
        "AddressCountry" => "United States",
        "AddressProvince" => null,
        "PreferredFirstName" => "Pref First",
        "PreferredLastName" => "Pref Last",
        "Deceased" => 0,
        "DoNotContact" => 0,
        "EmailOptOut" => 0,
        "Birthdate" => "01/01/1980",
        "Gender" => null,
        "DisplayPhone" => null,
        "DependentOnID" => null,
        "Pronouns" => null,
        "FirstName" => "Pref First",
        "LastName" => "Pref Last",
        "Duplicates" => null
      ],
      1 => [
        "AccountID" => "456",
        "LegalFirstName" => "Other First",
        "LegalLastName" => "Other Last",
        "MiddleName" => "Other Middle",
        "Suffix" => null,
        "Email" => "other-email@test-con.org",
        "Email2" => null,
        "Email3" => null,
        "Phone" => "777-777-7777",
        "Phone2" => null,
        "AddressLine1" => "456 Any Street",
        "AddressLine2" => null,
        "AddressCity" => "Anywhere",
        "AddressState" => "CA",
        "AddressZipCode" => "90210",
        "AddressZipCodeSuffix" => null,
        "AddressCountry" => "United States",
        "AddressProvince" => null,
        "PreferredFirstName" => "Pref Other First",
        "PreferredLastName" => "Pref Other Last",
        "Deceased" => 0,
        "DoNotContact" => 0,
        "EmailOptOut" => 0,
        "Birthdate" => "01/05/1980",
        "Gender" => null,
        "DisplayPhone" => null,
        "DependentOnID" => null,
        "Pronouns" => null,
        "FirstName" => "Pref Other First",
        "LastName" => "Pref Other Last",
        "Duplicates" => null
      ]
      ];
    

    protected function setUp(): void
    {
        $this->memberRepositoryMock = $this->createMock(MemberRepository::class);
        $this->systemUnderTest = new MemberService($this->memberRepositoryMock);

    }


    public function testGetByIdWithNoIds(): void
    {
        $this->memberRepositoryMock->expects($this->never())->method("selectById");

        $result = $this->systemUnderTest->getById([]);
        $this->assertCount(0, $result);

    }

    
    public function testGetByIdSingleId(): void
    {
        $this->memberRepositoryMock->expects($this->once())
            ->method("selectById")
            ->with("123")
            ->willReturn([$this->testData[0]]);

        $result = $this->systemUnderTest->getById("123");
        $this->assertCount(1, $result);

        $data = $result["123"];
        $this->assertEquals("123", $data["id"]);
        $this->assertEquals("First", $data["legal_first_name"]);
        $this->assertEquals("Last", $data["legal_last_name"]);
        $this->assertEquals("Middle", $data["middle_name"]);
        $this->assertNull($data["suffix"]);
        $this->assertEquals("email@test-con.org", $data["email"]);
        $this->assertNull($data["email2"]);
        $this->assertNull($data["email3"]);
        $this->assertEquals("555-555-5555", $data["phone"]);
        $this->assertNull($data["phone2"]);
        $this->assertEquals("123 Any Street", $data["address_line1"]);
        $this->assertNull($data["address_line2"]);
        $this->assertEquals("Anywhere", $data["city"]);
        $this->assertEquals("CA", $data["state"]);
        $this->assertEquals("90210", $data["zip_code"]);
        $this->assertNull($data["zip_plus4"]);
        $this->assertEquals("United States", $data["country"]);
        $this->assertNull($data["province"]);
        $this->assertEquals("Pref First", $data["preferred_first_name"]);
        $this->assertEquals("Pref Last", $data["preferred_last_name"]);
        $this->assertEquals(0, $data["deceased"]);
        $this->assertEquals(0, $data["do_not_contact"]);
        $this->assertEquals(0, $data["email_optout"]);
        $this->assertEquals("01/01/1980", $data["birthdate"]);
        $this->assertNull($data["gender"]);
        $this->assertNull($data["concom_display_phone"]);
        $this->assertNull($data["dependent_on"]);
        $this->assertNull($data["pronouns"]);
        $this->assertEquals("Pref First", $data["first_name"]);
        $this->assertEquals("Pref Last", $data["last_name"]);
        $this->assertNull($data["duplicates"]);

    }


    public function testGetByIdArray(): void
    {
        $this->memberRepositoryMock->expects($this->once())
            ->method("selectById")
            ->with(["123", "456"])
            ->willReturn($this->testData);

        $result = $this->systemUnderTest->getById(["123", "456"]);
        $this->assertCount(2, $result);

        $this->assertArrayHasKey("123", $result);
        $this->assertArrayHasKey("456", $result);

    }


    public function testGetByIdNullMemberId(): void
    {
        $this->memberRepositoryMock->expects($this->never())->method("selectById");

        $result = $this->systemUnderTest->getById(null);
        $this->assertCount(0, $result);

    }


    public function testGetByIdEmptyMemberIdString(): void
    {
        $this->memberRepositoryMock->expects($this->never())->method("selectById");

        $result = $this->systemUnderTest->getById("");
        $this->assertCount(0, $result);
        
    }


    public function testListAll(): void
    {
        $this->expectException(Exception::class);
        $this->systemUnderTest->listAll();

    }

    
    public function testPost(): void
    {
        $this->expectException(Exception::class);
        $this->systemUnderTest->post([]);

    }

    
    public function testPut(): void
    {
        $this->expectException(Exception::class);
        $this->systemUnderTest->put("1", []);

    }


    public function testDeleteById(): void
    {
        $this->expectException(Exception::class);
        $this->systemUnderTest->deleteById("1");

    }


    /* End MemberServiceTest */
}
