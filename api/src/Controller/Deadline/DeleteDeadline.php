<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Delete(
 *      tags={"deadlines"},
 *      path="/deadline/{id}",
 *      summary="Deletes a deadline",
 *      @OA\Parameter(
 *          description="Id of the deadline",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=204,
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
 *      security={
 *          {"ciab_auth": {}}
 *       }
 *  )
 **/

namespace App\Controller\Deadline;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Delete;
use Atlas\Query\Select;
use App\Controller\NotFoundException;

class DeleteDeadline extends BaseDeadline
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $select = Select::new($this->container->db);
        $select->columns('DeadlineID', 'DepartmentID')->from('Deadlines')->whereEquals(['DeadlineID' => $params['id']]);
        $target = $select->fetchOne();
        if (empty($target)) {
            throw new NotFoundException('Deadline Not Found');
        }

        $permissions = ['api.delete.deadline.all',
        'api.delete.deadline.'.$target['DepartmentID']];
        $this->checkPermissions($permissions);

        $delete = Delete::new($this->container->db);
        $delete->from('Deadlines')->whereEquals(['DeadlineID' => $target['DeadlineID']])->perform();
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        204
        ];

    }


    /* end DeleteDeadline */
}
