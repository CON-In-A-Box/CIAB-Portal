<?php declare(strict_types=1);

namespace App\Service;

use Exception;
use App\Repository\DepartmentRepository;

class DepartmentService implements ServiceInterface
{

  /**
   * @var DepartmentRepository
   */
    protected $departmentRepository;


    public function __construct(DepartmentRepository $departmentRepository)
    {
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


    public function post(/*.mixed.*/$data): void
    {
        throw new Exception(__CLASS__.": Method '__FUNCTION__' not implemented");

    }


    public function put(/*.string.*/$id, /*.mixed.*/$data): void
    {
        throw new Exception(__CLASS__.": Method '__FUNCTION__' not implemented");

    }


    public function deleteById(/*.mixed.*/$id): void
    {
        throw new Exception(__CLASS__.": Method '__FUNCTION__' not implemented");

    }


    /* End DepartmentService */
}
