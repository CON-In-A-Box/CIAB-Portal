<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Get(
 *      tags={"members"},
 *      path="/announcement",
 *      summary="Lists announcements for the current member",
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/max_results",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/page_token",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="OK",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/announcement_list"
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/member_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Controller\Announcement;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;

class ListMemberAnnouncements extends BaseAnnouncement
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $data = Select::new($this->container->db)
            ->columns(...BaseAnnouncement::selectMapping())
            ->from('Announcements')
            ->orderBy('`PostedOn` ASC')
            ->fetchAll();

        $data = $this->filterScope($data);

        $output = array();
        $output['type'] = 'announcement_list';
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        $output];

    }


    /* end ListMemberAnnouncements */
}
