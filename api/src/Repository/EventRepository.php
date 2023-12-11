<?php declare(strict_types=1);

namespace App\Repository;

use Exception;
use Atlas\Query\Select;

class EventRepository implements RepositoryInterface
{

    protected $db;


    public function __construct($db)
    {
        $this->db = $db;

    }


    public function getCurrentEvent()
    {
        $select = $this->getInitialSelect();
        return $select->where('Events.DateTo >= NOW()')
            ->orderBy('Events.DateFrom ASC LIMIT 1')
            ->fetchOne();

    }


    public function getLastActiveEvent()
    {
        $select = $this->getInitialSelect();
        return $select->orderBy('Events.EventID DESC LIMIT 1')
            ->fetchOne();

    }


    private function getInitialSelect()
    {
        $select = Select::new($this->db);
        $select->columns(
            'Events.EventID as EventID',
            'Events.DateFrom as EventFrom',
            'Events.DateTo as EventTo',
            'Events.EventName as EventName',
            'AnnualCycles.AnnualCycleID as AnnualCycleID',
            'AnnualCycles.DateFrom as AnnualCycleFrom',
            'AnnualCycles.DateTo as AnnualCycleTo'
        )
            ->from('Events')
            ->join('INNER', 'AnnualCycles', 'Events.AnnualCycleID = AnnualCycles.AnnualCycleID');

        return $select;

    }


    public function insert(/*.mixed.*/$data): void
    {
        throw new Exception(__CLASS__.": Method '__FUNCTION__' not implemented");

    }


    public function selectAll(): array
    {
        throw new Exception(__CLASS__.": Method '__FUNCTION__' not implemented");

    }


    public function selectById(/*.mixed.*/$departmentId): array
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


  /* End EventRepository */
}
