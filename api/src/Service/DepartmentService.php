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
            $output = [];
            $output["id"] = $value["DepartmentID"];
            $output["name"] = $value["Name"];
            $output["child_count"] = $value["ChildCount"];
            $output["type"] = "department";

            if ($value["Email"]) {
                $output["email"] = explode(',', $value["Email"]);
            } else {
                $output["email"] = [];
            }

            if ($value["ParentID"] && $value["ParentID"] != $value["DepartmentID"]) {
                $output["parent"]["id"] = $value["ParentID"];
                $output["parent"]["name"] = $value["ParentName"];
                $output["parent"]["type"] = "department";
            } else {
                $output["parent"] = null;
            }

            if ($value["FallbackID"]) {
                $output["fallback"]["id"] = $value["FallbackID"];
                $output["fallback"]["name"] = $value["FallbackName"];
                $output["fallback"]["type"] = "department";
            } else {
                $output["fallback"] = null;
            }

            $formatted[] = $output;
        }

        return $formatted;

    }


    /* End DepartmentService */
}
