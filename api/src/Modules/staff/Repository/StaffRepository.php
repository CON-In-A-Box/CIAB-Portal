<?php declare(strict_types=1);

namespace App\Modules\staff\Repository;

use Atlas\Query\Select;
use App\Repository\RepositoryInterface;

class StaffRepository implements RepositoryInterface
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


    public function insert(/*.mixed.*/$data): void
    {
        throw new Exception(__CLASS__.": Method '__FUNCTION__' not implemented");

    }


    public function selectById(/*.mixed.*/$accountIds, $event = null): array
    {
        throw new Exception(__CLASS__.": Method '__FUNCTION__' not implemented");

    }


    public function selectAll($event = null): array
    {
        throw new Exception(__CLASS__.": Method '__FUNCTION__' not implemented");

    }


    public function update(/*.string.*/$id, /*.mixed.*/$data): void
    {
        throw new Exception(__CLASS__.":Method '__FUNCTION__' not implemented");

    }


    public function deleteById(/*.mixed.*/$id): void
    {
        throw new Exception(__CLASS__.": Method '__FUNCTION__' not implemented");

    }


    /* End StaffRepository */
}
