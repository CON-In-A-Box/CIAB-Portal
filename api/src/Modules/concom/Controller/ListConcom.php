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


    public function __invoke(Request $request, Response $response, $args)
    {
        if (!\ciab\RBAC::havePermission('api.get.concom')) {
            return $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403);
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
        return $this->listResponse(
            $request,
            $response,
            array( 'type' => 'concom_list', 'event' => $event ),
            $data
        );

    }


    /* end ListConcom*/
}
