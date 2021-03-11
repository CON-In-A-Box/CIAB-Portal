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
 *                      property="Deadline",
 *                      type="string",
 *                      format="date",
 *                      nullable=true
 *                  ),
 *                  @OA\Property(
 *                      property="Note",
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
use App\Controller\NotFoundException;
use App\Controller\InvalidParameterException;

class PutDeadline extends BaseDeadline
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $sth = $this->container->db->prepare("SELECT * FROM `Deadlines` WHERE `DeadlineID` = '".$params['id']."'");
        $sth->execute();
        $deadlines = $sth->fetchAll();
        if (empty($deadlines)) {
            throw new NotFoundException('Deadline Not Found');
        }
        $target = $deadlines[0];

        $department = $target['DepartmentID'];
        $permissions = ['api.put.deadline.'.$department,
        'api.put.deadline.all'];
        $this->checkPermissions($permissions);

        $body = $request->getParsedBody();
        if (!$body) {
            throw new InvalidParameterException("Body required");
        }

        if (array_key_exists('Department', $body)) {
            $department = $this->getDepartment($body['Department']);
            $target['DepartmentID'] = $department['id'];
        }

        if (array_key_exists('Deadline', $body)) {
            $date = strtotime($body['Deadline']);
            if ($date == false) {
                throw new InvalidParameterException('\'Deadline\' parameter not valid \''.$body['Deadline'].'\'');
            }
            if ($date < strtotime('now')) {
                throw new InvalidParameterException('\'Deadline\' parameter in the past not valid \''.$body['Deadline'].'\'');
            }
            $target['Deadline'] = date("Y-m-d", $date);
        }
        if (array_key_exists('Note', $body)) {
            $target['Note'] = $body['Note'];
        }

        $sth = $this->container->db->prepare(<<<SQL
            UPDATE `Deadlines`
            SET
                `DepartmentID` = {$target['DepartmentID']},
                `Deadline` = '{$target['Deadline']}',
                `Note` = '{$target['Note']}'
            WHERE `DeadlineID` = '{$params['id']}';
SQL
        );
        $sth->execute();
        return [null];

    }


    /* end PutDeadline */
}
