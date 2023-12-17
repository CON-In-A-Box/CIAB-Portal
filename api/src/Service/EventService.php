<?php declare(strict_types=1);

namespace App\Service;

use Exception;
use App\Repository\EventRepository;

class EventService implements ServiceInterface
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


    private function formatEvent($event)
    {
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

        return $formatted;

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

        $this->currentEvent = $this->formatEvent($event);
        return $this->currentEvent;

    }


    public function post(/*.mixed.*/$data): void
    {
        throw new Exception(__CLASS__.": Method '__FUNCTION__' not implemented");

    }


    public function listAll(): array
    {
        throw new Exception(__CLASS__.": Method '__FUNCTION__' not implemented");

    }


    public function getById(/*.mixed.*/$id): array
    {
        $data = $this->eventRepository->selectById($id);
        if ($data === null) {
            return [];
        }

        $output = [];
        foreach ($data as $event) {
            $output[] = $this->formatEvent($event);
        }

        return $output;

    }


    public function put(/*.string.*/$id, /*.mixed.*/$data): void
    {
        throw new Exception(__CLASS__.": Method '__FUNCTION__' not implemented");

    }


    public function deleteById(/*.mixed.*/$id): void
    {
        $this->eventRepository->deleteById($id);

    }


    /* End EventService */
}
