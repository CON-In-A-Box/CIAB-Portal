<?php declare(strict_types=1);

namespace App\Mapper;

use Atlas\Query\Select;

class StaffMapper
{

    protected $db;


    public function __construct($db)
    {
        $this->db = $db;

    }


    public function getStaffDivisions()
    {
        $select = Select::new($this->db);
        $select->columns('DepartmentID as id', 'ParentDepartmentID as parent', 'Name as name', 'FallbackID as fallback')
            ->columns(
                $select->subSelect()
                    ->columns('COUNT(DepartmentID)')
                    ->from('Departments as child_depts')
                    ->where('child_depts.ParentDepartmentID = depts.DepartmentID')
                    ->andWhere('Name != "Historical Placeholder"')
                    ->as('child_count')
                    ->getStatement()
            )
            ->columns(
                $select->subSelect()
                    ->columns('GROUP_CONCAT(Email)')
                    ->from('EMails')
                    ->where('DepartmentID = depts.DepartmentID')
                    ->as('email')
                    ->getStatement()
            )
            ->from('Departments as depts');

        $historicalPlaceholders = $select->subselect()->columns('DepartmentID')->from('Departments')->where('Name = "Historical Placeholder"');
        $select->where('depts.DepartmentID NOT IN ', $historicalPlaceholders);
        $select->where('depts.ParentDepartmentID NOT IN ', $historicalPlaceholders);

        return $select->fetchAll();

    }


    public function getDivisionStaff($eventId, $divisionId)
    {
        $select = Select::new($this->db);

        $historicalPlaceholder = $select->subselect()
            ->columns('DepartmentID')
            ->from('Departments')
            ->where('Name = "Historical Placeholder"');

        $historicalPlaceholderParents = $select->subselect()
            ->columns('DepartmentID')
            ->from('Departments')
            ->where('ParentDepartmentID IN ', $historicalPlaceholder);

        $depts = $select->subselect()
            ->columns('DepartmentID')
            ->from('Departments')
            ->where('ParentDepartmentID = ', $divisionId);

        $select
            ->columns('Staff.ListRecordID as id', 'Staff.AccountID as member', 'Staff.DepartmentID as department')
            ->columns('COALESCE(Staff.Note, "") as note')
            ->columns(
                $select->subSelect()
                    ->columns('Name')
                    ->from('ConComPositions')
                    ->where('PositionID = Staff.PositionID')
                    ->as('position')->getStatement()
            )
            ->columns('Depts.Name as departmentName', 'Depts.ParentDepartmentID as parentId', 'Divs.Name as parentName')
            ->columns('(CASE WHEN Members.PreferredFirstName IS NOT NULL THEN Members.PreferredFirstName ELSE Members.FirstName END) as first_name')
            ->columns('(CASE WHEN Members.PreferredLastName IS NOT NULL THEN Members.PreferredLastName ELSE Members.LastName END) as last_name')
            ->columns('Members.Email as email', 'Members.Pronouns as pronouns')
            ->from('ConComList as Staff')
            ->join('INNER', 'Members', 'Staff.AccountID = Members.AccountID')
            ->join('INNER', 'Departments as Depts', 'Staff.DepartmentID = Depts.DepartmentID')
            ->join('INNER', 'Departments as Divs', 'Depts.ParentDepartmentID = Divs.DepartmentID')
            ->where('Staff.EventID = ', $eventId)
            ->where('Staff.DepartmentID = ', $divisionId)
            ->orWhere('Staff.DepartmentID IN ', $depts)
            ->where('Staff.DepartmentID NOT IN ', $historicalPlaceholder)
            ->where('Staff.DepartmentID NOT IN ', $historicalPlaceholderParents);

        return $select->fetchAll();

    }


    /* End StaffMapper */
}
