<?php declare(strict_types=1);

use App\Repository\EventRepository;
use App\Service\EventService;
use PHPUnit\Framework\TestCase;

final class EventServiceTest extends TestCase
{

    private $eventRepositoryMock;

    private $systemUnderTest;

    private $eventData = [
      "EventID" => "1",
      "EventFrom" => "01/01/2023",
      "EventTo" => "01/05/2023",
      "EventName" => "Test Event",
      "AnnualCycleID" => "1",
      "AnnualCycleFrom" => "01/01/2023",
      "AnnualCycleTo" => "12/31/2023"
    ];

    private $expectedResult = [
      "id" => "1",
      "name" => "Test Event",
      "date_from" => "01/01/2023",
      "date_to" => "01/05/2023",
      "type" => "event",
      "cycle" => [
        "id" => "1",
        "date_from" => "01/01/2023",
        "date_to" => "12/31/2023",
        "type" => "cycle"
      ]
      ];


    protected function setUp(): void
    {
        $this->eventRepositoryMock = $this->createMock(EventRepository::class);
        $this->systemUnderTest = new EventService($this->eventRepositoryMock);

    }


    public function testGetCurrentEventWithCurrentEvent(): void
    {
        $this->eventRepositoryMock->expects($this->never())->method("getLastActiveEvent");
        $this->eventRepositoryMock->expects($this->once())
            ->method("getCurrentEvent")
            ->willReturn($this->eventData);

        $result = $this->systemUnderTest->getCurrentEvent();
        $this->assertEquals($this->expectedResult, $result);

    }


    public function testGetCurrentEventWithLastActiveEvent(): void
    {
        $this->eventRepositoryMock->expects($this->once())
            ->method("getCurrentEvent")
            ->willReturn(null);
        $this->eventRepositoryMock->expects($this->once())
            ->method("getLastActiveEvent")
            ->willReturn($this->eventData);

        $result = $this->systemUnderTest->getCurrentEvent();
        $this->assertEquals($this->expectedResult, $result);

    }


    public function testGetCurrentEventMultipleConsecutiveCalls(): void
    {
        $this->eventRepositoryMock->expects($this->never())->method("getLastActiveEvent");
        $this->eventRepositoryMock->expects($this->once())
            ->method("getCurrentEvent")
            ->willReturn($this->eventData);

        $result = $this->systemUnderTest->getCurrentEvent();
        $secondResult = $this->systemUnderTest->getCurrentEvent();
        $this->assertEquals($result, $secondResult);

    }


    public function testGetByIdWithEventNotFound(): void
    {
        $this->eventRepositoryMock->expects($this->once())
            ->method("selectById")
            ->with("123")
            ->willReturn([]);

        $result = $this->systemUnderTest->getById("123");
        $this->assertEquals([], $result);

    }


    public function testGetByIdWithEventFound(): void
    {
        $this->eventRepositoryMock->expects($this->once())
            ->method("selectById")
            ->with("1")
            ->willReturn([$this->eventData]);

        $result = $this->systemUnderTest->getById("1");
        $this->assertCount(1, $result);
        $this->assertEquals($this->expectedResult, $result[0]);

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


    public function testPut(): void
    {
        $this->expectException(Exception::class);
        $this->systemUnderTest->put("123", []);

    }


    public function testDeleteById(): void
    {
        $this->eventRepositoryMock->expects($this->once())
            ->method("deleteById")
            ->with("123");
        $this->systemUnderTest->deleteById("123");

    }
    

  /* End EventServiceTest */
}
