<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Put(
 *      tags={"deadlines"},
 *      path="/deadline/{id}",
 *      summary="Updates a deadline",
 *      @OA\Parameter(
 *          description="Id of the deadline",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="deadline",
 *                      type="string",
 *                      format="date",
 *                      nullable=true
 *                  ),
 *                  @OA\Property(
 *                      property="note",
 *                      type="string",
 *                      nullable=true
 *                  )
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="OK"
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/deadline_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Controller\Deadline;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Update;
use Atlas\Query\Select;
use App\Error\NotFoundException;
use App\Error\InvalidParameterException;

class PutDeadline extends BaseDeadline
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $select = Select::new($this->container->db);
        $select->columns('*')->from('Deadlines')->whereEquals(['DeadlineID' => $params['id']]);
        $target = $select->fetchOne();
        if (empty($target)) {
            throw new NotFoundException('Deadline Not Found');
        }

        $department = $target['DepartmentID'];
        $permissions = ['api.put.deadline.'.$department,
        'api.put.deadline.all'];
        $this->checkPermissions($permissions);

        $body = $request->getParsedBody();
        if (!$body) {
            throw new InvalidParameterException("Body required");
        }

        if (array_key_exists('department', $body)) {
            $department = $this->getDepartment($body['department']);
            $body['department'] = $department['id'];

            $permissions = ['api.put.deadline.'.$department['id'],
            'api.put.deadline.all'];
            $this->checkPermissions($permissions);
        }

        if (array_key_exists('deadline', $body)) {
            $date = strtotime($body['deadline']);
            if ($date == false) {
                throw new InvalidParameterException('\'deadline\' parameter not valid \''.$body['deadline'].'\'');
            }
            if ($date < strtotime('now')) {
                throw new InvalidParameterException('\'deadline\' parameter in the past not valid \''.$body['deadline'].'\'');
            }
            $body['deadline'] = date("Y-m-d", $date);
        }

        $update = Update::new($this->container->db);
        $update->table('Deadlines');
        $update->columns(BaseDeadline::insertPayloadFromParams($body, false));
        $update->whereEquals(['DeadlineID' => $params['id']]);
        $update->perform();
        return [null];

    }


    /* end PutDeadline */
}
