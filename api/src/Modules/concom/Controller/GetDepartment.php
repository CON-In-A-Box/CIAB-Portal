<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\concom\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\NotFoundException;

class GetDepartment extends BaseConcom
{


    public function buildResource(Request $request, Response $response, $args) :array
    {
        $permissions = ['api.get.concom'];
        $this->checkPermissions($permissions);
        $dept = $this->getDepartment($args['id']);
        if ($dept === null) {
            throw new NotFoundException('Department \''.$args['id'].'\' Not Found');
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
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $output,
        array('type' => 'concom_list', 'event' => $event)];

    }


    /* end GetDepartment */
}
