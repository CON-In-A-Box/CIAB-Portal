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
        $permissions = ['api.get.concom'];
        $this->checkPermissions($permissions);
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
