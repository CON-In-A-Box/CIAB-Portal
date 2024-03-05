<?php declare(strict_types=1);

namespace App\Service;

use App\Service\EmailService;
use App\Service\EmailListAccessService;
use App\Repository\DepartmentRepository;

class DepartmentService implements ServiceInterface
{

    /**
     * @var EmailService
     */
    protected $emailService;

    /**
     * @var EmailListAccessService
     */
    protected $emailListAccessService;

    /**
     * @var PermissionService
     */
    protected $permissionService;

    /**
     * @var DepartmentRepository
     */
    protected $departmentRepository;


    public function __construct(EmailService $emailService, EmailListAccessService $emailListAccessService, PermissionService $permissionService, DepartmentRepository $departmentRepository)
    {
        $this->emailService = $emailService;
        $this->emailListAccessService = $emailListAccessService;
        $this->permissionService = $permissionService;
        $this->departmentRepository = $departmentRepository;

    }


    public function listAll(): array
    {
        $result = $this->departmentRepository->selectAll();

        $formatted = [];
        foreach ($result as $value) {
            $formatted[] = $this->formatDepartmentValue($value);
        }

        return $formatted;

    }


    public function getById(/*.mixed.*/$departmentId): array
    {
        $result = $this->departmentRepository->selectById($departmentId);

        $formatted = [];
        foreach ($result as $value) {
            $formattedValue = $this->formatDepartmentValue($value);
            $formatted[$formattedValue["id"]] = $formattedValue;
        }

        return $formatted;

    }


    public function post(/*.mixed.*/$data): int
    {
        return $this->departmentRepository->insert($data);
        
    }


    public function put(/*.string.*/$id, /*.mixed.*/$data): void
    {
        $this->departmentRepository->update($id, $data);

    }


    public function deleteById(/*.mixed.*/$id): void
    {
        $this->emailService->deleteByDepartmentId($id);
        $this->emailListAccessService->deleteByDepartmentId($id);
        $this->departmentRepository->deleteById($id);

    }


    public function listAllEmails(/*.mixed.*/$id): array
    {
        return $this->emailService->listAllByDepartmentId($id);
        
    }


    public function listPermissionsByDepartment(/*.mixed.*/$id): array
    {
        return $this->permissionService->getByDepartmentId($id);
        
    }


    private function formatDepartmentValue($value)
    {
        $output = [];
        $output["id"] = $value["DepartmentID"];
        $output["name"] = $value["Name"];

        if (isset($value["ChildCount"])) {
            $output["child_count"] = $value["ChildCount"];
        }
        
        $output["type"] = "department";

        if (isset($value["Email"]) && $value["Email"]) {
            $output["email"] = explode(',', $value["Email"]);
        } else {
            $output["email"] = [];
        }

        if (isset($value["ParentID"]) && $value["ParentID"] != $value["DepartmentID"]) {
            $output["parent"]["id"] = $value["ParentID"];
            $output["parent"]["name"] = $value["ParentName"];
            $output["parent"]["type"] = "department";
        } else {
            $output["parent"] = null;
        }

        if (isset($value["FallbackID"])) {
            $output["fallback"]["id"] = $value["FallbackID"];
            $output["fallback"]["name"] = $value["FallbackName"];
            $output["fallback"]["type"] = "department";
        } else {
            $output["fallback"] = null;
        }

        return $output;

    }


    /* End DepartmentService */
}
