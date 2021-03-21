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

class GetDepartmentChildren extends BaseDepartment
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $id = $params['name'];
        $sql = "SELECT DepartmentID FROM `Departments` WHERE `ParentDepartmentID` = $id";
        $result = $this->container->db->prepare($sql);
        $result->execute();
        $data = $result->fetchAll();
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
