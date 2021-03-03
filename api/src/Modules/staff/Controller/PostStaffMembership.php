<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\staff\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\NotFoundException;
use App\Controller\InvalidParameterException;

class PostStaffMembership extends BaseStaff
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $user = $request->getAttribute('oauth2-token')['user_id'];
        $data = \lookup_users_by_key($params['id']);
        if (empty($data['users'])) {
            if (empty($data['error'])) {
                $error = 'No Members Found';
            } else {
                $error = $data['error'];
            }
            throw new NotFoundException($error);
        }
        $user = $data['users'][0]['Id'];

        $body = $request->getParsedBody();
        if (!$body) {
            throw new InvalidParameterException("Body required");
        }

        if (!array_key_exists('Department', $body)) {
            throw new InvalidParameterException('Required \'Department\' parameter not present');
        }

        $department = $this->getDepartment($body['Department']);
        if ($department === null) {
            throw new NotFoundException("Department '${body['Department']}' Not Found");
        }

        $permissions = ['api.post.staff.'.$department['id'],
        'api.post.staff.all'];
        $this->checkPermissions($permissions);

        if (!array_key_exists('Position', $body)) {
            throw new InvalidParameterException('Required \'Position\' parameter not present');
        }
        $position = $body['Position'];

        if (array_key_exists('Note', $body)) {
            $note = "'".$body['Note']."'";
        } else {
            $note = 'NULL';
        }

        if (array_key_exists('Event', $body)) {
            $event = $body['Event'];
        } else {
            $event = \current_eventID();
        }

        $sql = "INSERT INTO `ConComList` (AccountID, DepartmentID, EventID, Note, PositionID) VALUES ($user, {$department['id']}, $event, $note, $position)";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

        $target = new GetStaffMembership($this->container);
        $data = $target->buildResource($request, $response, ['id' => $this->container->db->lastInsertId()])[1];

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data),
        201
        ];

    }


    /* end PostStaffMembership */
}
