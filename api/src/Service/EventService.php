<?php declare(strict_types=1);

namespace App\Service;

use App\Repository\EventRepository;

class EventService
{

  /**
   * @var EventRepository;
   */
    protected $eventRepository;

    protected $currentEvent = null;


    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;

    }


    public function getCurrentEvent()
    {
        if ($this->currentEvent != null) {
            return $this->currentEvent;
        }

        $event = $this->eventRepository->getCurrentEvent();
        if ($event == null) {
      // Fallback to last active event in DB
            $event = $this->eventRepository->getLastActiveEvent();
        }

        $formatted = [];
        $formatted["id"] = $event["EventID"];
        $formatted["name"] = $event["EventName"];
        $formatted["date_from"] = $event["EventFrom"];
        $formatted["date_to"] = $event["EventTo"];
        $formatted["type"] = "event";
        $formatted["cycle"]["id"] = $event["AnnualCycleID"];
        $formatted["cycle"]["date_from"] = $event["AnnualCycleFrom"];
        $formatted["cycle"]["date_to"] = $event["AnnualCycleTo"];
        $formatted["cycle"]["type"] = "cycle";
    
        $this->currentEvent = $formatted;
        return $formatted;

    }


    /* End EventService */
}
