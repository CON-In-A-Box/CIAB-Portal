<?php declare(strict_types=1);

namespace App\Modules\staff\Service;

use App\Service\EventService;
use App\Service\DepartmentService;
use App\Service\MemberService;
use App\Modules\staff\Repository\StaffRepository;

class StaffService
{

  /**
   * @var EventService
   */
    protected $eventService;

  /**
   * @var DepartmentService
   */
    protected $departmentService;

  /**
   * @var MemberService
   */
    protected $memberService;

  /**
   * @var StaffRepository
   */
    protected $staffRepository;


    public function __construct(
        EventService $eventService,
        DepartmentService $departmentService,
        MemberService $memberService,
        StaffRepository $staffRepository
    ) {
        $this->eventService = $eventService;
        $this->departmentService = $departmentService;
        $this->memberService = $memberService;
        $this->staffRepository = $staffRepository;

    }


    public function getStaffInDepartment($departmentId)
    {
        $currentEvent = $this->eventService->getCurrentEvent();
        $divisionDepartments = $this->departmentService->getDepartmentsById($departmentId);

        $eventId = $currentEvent["id"];
        $departmentIds = [];
        foreach ($divisionDepartments as $department) {
            $departmentIds[] = $department["id"];
        }

        $divisionStaff = $this->staffRepository->findStaffByDepartmentIDs($eventId, $departmentIds);
        $accountIds = [];
        foreach ($divisionStaff as $staff) {
            $accountIds[] = $staff["AccountID"];
        }

        $members = $this->memberService->getMembersByIds($accountIds);

        $formatted = [];
        foreach ($divisionStaff as $staff) {
            $output = [];
            $output["id"] = $staff["ListRecordID"];
            $output["positionId"] = $staff["PositionID"];
            $output["note"] = $staff["Note"];
            $output["position"] = $staff["Position"];
            $output["member"] = $members[$staff["AccountID"]];
            $output["department"] = $divisionDepartments[$staff["DepartmentID"]];

            $formatted[] = $output;
        }

        return $formatted;

    }


    /* End StaffService */
}
