<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;

class GetTicketTypes extends BaseTicket
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $sql = "SELECT * FROM `BadgeTypes` ";
        $conditional = [];
        if (array_key_exists('event', $params)) {
            $conditional[] = '`EventID` = '.$params['event'];
        }
        if (array_key_exists('id', $params)) {
            $conditional[] = '`BadgeTypeID` = '.$params['id'];
        }
        if (!empty($conditional)) {
            $sql .= 'WHERE '.implode(' AND ', $conditional);
        }

        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        if (empty($data)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Not Found', 'Badge Type Not Found', 404)];
        }
        $badges = [];
        foreach ($data as $entry) {
            $badge = $entry;
            $badge['id'] = $entry['BadgeTypeID'];
            unset($badge['BadgeTypeID']);
            $badge['event'] = $entry['EventID'];
            unset($badge['EventID']);
            $badge['type'] = 'ticket_type';
            $badges[] = $badge;
        }

        if (count($badges) > 1) {
            $output = ['type' => 'ticket_type_list'];
            return [
            \App\Controller\BaseController::LIST_TYPE,
            $badges,
            $output
            ];
        } else {
            return [
            \App\Controller\BaseController::RESOURCE_TYPE,
            $badges[0]
            ];
        }

    }


    public function processIncludes(Request $request, Response $response, $args, $values, &$data)
    {
        if (in_array('event', $values)) {
            $target = new \App\Controller\Event\GetEvent($this->container);
            $newargs = $args;
            $newargs['id'] = $data['event'];
            $newdata = $target->buildResource($request, $response, $newargs)[1];
            $target->processIncludes($request, $response, $args, $values, $newdata);
            $data['event'] = $target->arrayResponse($request, $response, $newdata);
        }

    }


    /* end GetTicketTypes */
}
