<?php declare(strict_types=1);

namespace App\Service;

use App\Repository\DepartmentRepository;

class DepartmentService
{

  /**
   * @var DepartmentRepository
   */
    protected $departmentRepository;


    public function __construct(DepartmentRepository $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;

    }


    public function getAllDepartments()
    {
        $result = $this->departmentRepository->findAll();

        $formatted = [];
        foreach ($result as $value) {
            $formatted[] = $this->formatDepartmentValue($value);
        }

        return $formatted;

    }

    
    public function getDepartmentsById($departmentId)
    {
        $result = $this->departmentRepository->findDepartmentsById($departmentId);

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


    /* End DepartmentService */
}
