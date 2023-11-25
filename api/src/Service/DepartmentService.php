<?php declare(strict_types=1);

namespace App\Service;

class DepartmentService
{

    protected $staffMapper;


    public function __construct($staffMapper)
    {
        $this->staffMapper = $staffMapper;

    }


    public function getStaffDivisions()
    {
        $data = $this->staffMapper->getStaffDivisions();

        $formatted = [];
        foreach ($data as $value) {
            if ($value['parent'] == $value['id']) {
                $value['parent'] = null;
            }

            if ($value['email']) {
                $value['email'] = explode(',', $value['email']);
            } else {
                $value['email'] = [];
            }

            $formatted[] = $value;
        }

        return $formatted;

    }


    public function getDivisionStaff($eventId, $divisionId)
    {
        $data = $this->staffMapper->getDivisionStaff($eventId, $divisionId);

        $formatted = [];
        foreach ($data as $value) {
            $member = [];
            $member['id'] = $value['member'];
            $member['first_name'] = $value['first_name'];
            $member['last_name'] = $value['last_name'];
            $member['email'] = $value['email'];
            $member['pronouns'] = $value['pronouns'];

            $value['member'] = $member;

            $dept = [];
            $dept['id'] = $value['department'];
            $dept['name'] = $value['departmentName'];
      
            if ($value['department'] != $value['parentId']) {
                $parent = [];
                $parent['id'] = $value['parentId'];
                $parent['name'] = $value['parentName'];

                $dept['parent'] = $parent;
            }

            $value['department'] = $dept;

      // Remove metadata fields that we've modified
            unset($value['departmentName']);
            unset($value['parentId']);
            unset($value['parentName']);
            unset($value['email']);
            unset($value['first_name']);
            unset($value['last_name']);
            unset($value['pronouns']);

            $formatted[] = $value;
        }

        return $formatted;

    }


    /* End DepartmentService */
}
