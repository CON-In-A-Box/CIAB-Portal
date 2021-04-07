<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"departments"},
 *      path="/department/{id}/announcements",
 *      summary="Lists announcements for a given department",
 *      @OA\Parameter(
 *          description="The id or name of the department",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(
 *              oneOf = {
 *                  @OA\Schema(
 *                      description="Department id",
 *                      type="integer"
 *                  ),
 *                  @OA\Schema(
 *                      description="Department name",
 *                      type="string"
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
 *          ref="#/components/responses/department_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Controller\Announcement;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;

class ListDepartmentAnnouncements extends BaseAnnouncement
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $department = $this->getDepartment($args['name']);
        $select = Select::new($this->container->db);
        $select->columns(...BaseAnnouncement::selectMapping());
        $select->from('Announcements')->whereEquals(['DepartmentID' => $department['id']]);
        $select->orderBy('`PostedOn` ASC');
        $data = $select->fetchAll();
        $output = array();
        $output['type'] = 'announcement_list';
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        $output];

    }


    /* end ListDepartmentAnnouncements */
}
