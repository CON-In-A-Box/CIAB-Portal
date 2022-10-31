<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"departments"},
 *      path="/department/{id}/children",
 *      summary="Lists children of the department",
 *      @OA\Parameter(
 *          description="Id or name of the deadline",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(
 *              oneOf = {
 *                  @OA\Schema(
 *                      description="Department name",
 *                      type="string"
 *                  ),
 *                  @OA\Schema(
 *                      description="Department id",
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
 *              ref="#/components/schemas/department_list"
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Controller\Department;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;

class GetDepartmentChildren extends BaseDepartment
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $id = $params['name'];
        $select = Select::new($this->container->db);
        $select->columns('DepartmentID')->from('Departments')->whereEquals(['ParentDepartmentID' => $id]);
        $data = $select->fetchAll();
        $output = [];
        foreach ($data as $entry) {
            if ($entry['DepartmentID'] != $id) {
                $output[] = $this->getDepartment($entry['DepartmentID']);
            }
        }

        return [
        \App\Controller\BaseController::LIST_TYPE,
        $output,
        array('type' => 'department_list')];

    }


    /* end GetDepartmentChildren */
}
