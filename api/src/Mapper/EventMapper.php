<?php declare(strict_types=1);

namespace App\Mapper;

use Atlas\Query\Select;

class EventMapper
{

    protected $db;


    public function __construct($db)
    {
        $this->db = $db;

    }


    public function getMostRecentEvent()
    {
        $select = Select::new($this->db);
        return $select
            ->columns('EventID', 'AnnualCycleID', 'DateFrom', 'DateTo', 'EventName')
            ->from('Events')
            ->where('Events.DateTo >= NOW()')
            ->orderBy('Events.DateFrom ASC LIMIT 1')
            ->fetchOne();

    }


    public function getLastActiveEvent()
    {
        $select = Select::new($this->db);
        return $select
            ->columns('EventID', 'AnnualCycleID', 'DateFrom', 'DateTo', 'EventName')
            ->from('Events')
            ->orderBy('Events.EventID DESC LIMIT 1')
            ->fetchOne();

    }

    
    /* End EventMapper */
}
