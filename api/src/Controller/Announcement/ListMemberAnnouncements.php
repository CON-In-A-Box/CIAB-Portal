<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Get(
 *      tags={"members"},
 *      path="/member/{id}/announcements",
 *      summary="Lists announcements for a given member",
 *      @OA\Parameter(
 *          description="The id or login of the member",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(
 *              oneOf = {
 *                  @OA\Schema(
 *                      description="Member login",
 *                      type="string"
 *                  ),
 *                  @OA\Schema(
 *                      description="Member id",
 *                      type="integer"
 *                  )
 *              }
 *          )
 *      ),
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
        $user = $this->getMember($request, $params['id'])[0]['id'];
        $select = Select::new($this->container->db);

        $sub1 = $select->subselect()->columns('COUNT(AccountID)')->from('ConComList')->whereEquals(['AccountID' => $user]);
        $sub2a = $select->subselect()->columns('DepartmentID')->from('ConComList')->whereEquals(['AccountID' => $user]);
        $sub2 = $select->subselect()->columns('DepartmentID')->from('Departments')->where('ParentDepartmentID IN ', $sub2a);

        $select->columns(...BaseAnnouncement::selectMapping());
        $select->from('Announcements');
        $select->whereEquals(['Scope' => 0]);
        $select->orWhere('(');
        $select->catWhere('Scope = 1 AND ');
        $select->catWhere('DepartmentID IN ', $sub1);
        $select->catWhere(')');
        $select->orWhere('(');
        $select->catWhere('Scope = 2 AND (');
        $select->catWhere('DepartmentID IN ', $sub1);
        $select->catWhere(') OR ( DepartmentID IN', $sub2);
        $select->catWhere(')');
        $select->catWhere(')');
        $select->orderBy('`PostedOn` ASC');

        $data = $select->fetchAll();
        $output = array();
        $output['type'] = 'announcement_list';
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        $output];

    }


    /* end ListMemberAnnouncements */
}
