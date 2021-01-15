<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Event;

use Slim\Http\Request;
use Slim\Http\Response;

class ListEvents extends BaseEvent
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $begin = $request->getQueryParam('begin', null);
        $end = $request->getQueryParam('end', null);

        if ($begin !== null) {
            $begin = strtotime($begin);
        }
        if ($end !== null) {
            $end = strtotime($end);
        }


        $sql = "SELECT * FROM `Events`";
        if ($begin !== null) {
            $sql .= " WHERE `DateFrom` >= '".date("Y-m-d", $begin)."'";
        }
        if ($end !== null) {
            if ($begin === null) {
                $sql .= " WHERE";
            } else {
                $sql .= " AND";
            }
            $sql .= " DateTo <= '".date("Y-m-d", $end)."'";
        }
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $events = $sth->fetchAll();
        $output = array();
        $output['type'] = 'event_list';
        $data = array();
        foreach ($events as $entry) {
            $data[] = $this->processEvent($entry);
        }
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        $output];

    }


    /* end ListEvents */
}
