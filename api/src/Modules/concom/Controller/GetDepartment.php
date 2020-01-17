<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\concom\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

class GetDepartment extends BaseConcom
{


    public function __invoke(Request $request, Response $response, $args)
    {
        if (!\ciab\RBAC::havePermission('api.get.concom')) {
            return $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403);
        }
        $dept = $this->getDepartment($args['id']);
        if ($dept === null) {
            return $this->errorResponse(
                $request,
                $response,
                'Not Found',
                'Department \''.$args['id'].'\' Not Found',
                404
            );
        }
        $event = $request->getQueryParam('event');
        if ($event !== null) {
            $event = intval($event);
        } else {
            $event = \current_eventID();
        }
        $sql = <<<SQL
    SELECT
        l.AccountID AS member,
        COALESCE(l.Note, "") AS note,
        (
            SELECT
                Name
            FROM
                ConComPositions
            WHERE
                PositionID = l.PositionID
        ) AS position
    FROM
        ConComList AS l
    WHERE
        l.EventID = $event
        AND l.DepartmentID = {$dept['id']}
SQL;
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        $path = $request->getUri()->getBaseUrl();
        $output = [];
        foreach ($data as $entry) {
            $output[] = $this->buildEntry($request, $dept['id'], $entry['member'], $entry['note'], $entry['position']);
        }
        return $this->listResponse($request, $response, array('type' => 'concom_list', 'event' => $event), $output);

    }


    /* end GetDepartment */
}
