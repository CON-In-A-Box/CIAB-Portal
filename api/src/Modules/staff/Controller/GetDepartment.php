<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\staff\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

class GetDepartment extends BaseStaff
{


    public function buildResource(Request $request, Response $response, $args) :array
    {
        $permissions = ['api.get.staff'];
        $this->checkPermissions($permissions);
        $dept = $this->getDepartment($args['id']);
        $event = $request->getQueryParam('event');
        if ($event !== null) {
            $event = intval($event);
        } else {
            $event = \current_eventID();
        }
        $sql = <<<SQL
    SELECT
        l.ListRecordID AS record,
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
            $output[] = $this->buildEntry($request, $entry['record'], $dept['id'], $entry['member'], $entry['note'], $entry['position']);
        }
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $output,
        array('type' => 'staff_list', 'event' => $event)];

    }


    /* end GetDepartment */
}
