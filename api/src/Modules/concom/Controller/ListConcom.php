<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\concom\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

require_once __DIR__.'/../../../../../modules/concom/functions/LIST.inc';

class ListConcom extends BaseConcom
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        if (!\ciab\RBAC::havePermission('api.get.concom')) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }
        $concom = \concom\ConcomList::listBuild();
        $data = array();
        foreach ($concom as $entry) {
            $dept = $this->getDepartment($entry['Department']);
            if ($dept) {
                $id = $dept['id'];
            } else {
                $id = $entry['Department'];
            }
            $data[] = $this->buildEntry($request, $id, $entry['Account ID'], $entry['Note'], $entry['Position']);
        }
        $event = \current_eventID();
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        array( 'type' => 'concom_list', 'event' => $event )
        ];

    }


    /* end ListConcom*/
}
