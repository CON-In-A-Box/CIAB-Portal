<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Post(
 *      tags={"departments"},
 *      path="/department/{id}/deadline",
 *      summary="Adds a new deadline",
 *      @OA\Parameter(
 *          description="The id or name of the department",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(
 *              oneOf = {
 *                  @OA\Schema(
 *                      description="Department name",
 *                      type="integer"
 *                  ),
 *                  @OA\Schema(
 *                      description="Department id",
 *                      type="string"
 *                  )
 *              }
 *          )
 *      ),
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="Deadline",
 *                      type="string",
 *                      format="date"
 *                  ),
 *                  @OA\Property(
 *                      property="Note",
 *                      type="string"
 *                  )
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=201,
 *          description="OK"
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

namespace App\Controller\Deadline;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\InvalidParameterException;

class PostDeadline extends BaseDeadline
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $department = $this->getDepartment($params['name']);
        $permissions = ['api.post.deadline.'.$department['id'],
        'api.post.deadline.all'];
        $this->checkPermissions($permissions);

        $body = $request->getParsedBody();
        if (!$body) {
            throw new InvalidParameterException("Body required");
        }
        if (!array_key_exists('Deadline', $body)) {
            throw new InvalidParameterException('Required \'Deadline\' parameter not present');
        }
        if (!array_key_exists('Note', $body)) {
            throw new InvalidParameterException('Required \'Note\' parameter not present');
        }
        $date = strtotime($body['Deadline']);
        if ($date == false) {
            throw new InvalidParameterException('\'Deadline\' parameter not valid \''.$body['Deadline'].'\'');
        }
        if ($date < strtotime('now')) {
            throw new InvalidParameterException('\'Deadline\' parameter in the past not valid \''.$body['Deadline'].'\'');
        }
        $sql_date = date("Y-m-d", $date);
        $sth = $this->container->db->prepare("INSERT INTO `Deadlines` (DepartmentID, Deadline, Note) VALUES ({$department['id']}, '$sql_date', '{$body['Note']}')");
        $sth->execute();
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        201
        ];

    }


    /* end PostDeadline */
}
