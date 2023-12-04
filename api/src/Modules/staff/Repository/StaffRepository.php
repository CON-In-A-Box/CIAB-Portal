<?php declare(strict_types=1);

namespace App\Modules\staff\Repository;

use Atlas\Query\Select;

class StaffRepository
{

    protected $db;


    public function __construct($db)
    {
        $this->db = $db;

    }


    public function findStaffByDepartmentIDs($eventId, $departmentIds)
    {
        $select = Select::new($this->db);
        $select->columns(
            'Staff.ListRecordID as ListRecordID',
            'Staff.AccountID as AccountID',
            'Staff.DepartmentID as DepartmentID',
            'Staff.EventID as EventID',
            'COALESCE(Staff.Note, "") as Note',
            'Staff.PositionID as PositionID'
        )
            ->columns(
                $select->subSelect()
                    ->columns('Name')
                    ->from('ConComPositions')
                    ->where('Staff.PositionID = ConComPositions.PositionID')
                    ->as('Position')
                    ->getStatement()
            )
            ->from('ConComList as Staff')
            ->where('Staff.EventID = ', $eventId)
            ->where('Staff.DepartmentID IN ', $departmentIds);

        return $select->fetchAll();

    }


    /* End StaffRepository */
}
