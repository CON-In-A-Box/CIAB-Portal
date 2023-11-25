<?php declare(strict_types=1);

namespace App\Service;

class EventService
{

    protected $eventMapper;


    public function __construct($eventMapper)
    {
        $this->eventMapper = $eventMapper;

    }


    public function getCurrentEvent()
    {
        $event = $this->eventMapper->getMostRecentEvent();
        if ($event == null) {
      // Fallback to last active event in DB
            $event = $this->eventMapper->getLastActiveEvent();
        }

        $formatted = [];
        $formatted['id'] = $event['EventID'];
        $formatted['cycle'] = $event['AnnualCycleID'];
        $formatted['date_from'] = $event['DateFrom'];
        $formatted['date_to'] = $event['DateTo'];
        $formatted['name'] = $event['EventName'];

        return $formatted;

    }


    /* End EventService */
}
