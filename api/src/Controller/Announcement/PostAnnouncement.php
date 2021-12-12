<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Post(
 *      tags={"departments"},
 *      path="/department/{id}/announcement",
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
 *      summary="Adds a new announcement",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="text",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="scope",
 *                      type="integer"
 *                  ),
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
 *          description="Department or Member not found in the system",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/error"
 *          )
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Controller\Announcement;

require_once __DIR__.'/../../../../backends/email.inc';

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views;
use Atlas\Query\Insert;
use Atlas\Query\Select;
use App\Controller\InvalidParameterException;

class PostAnnouncement extends BaseAnnouncement
{


    private function sendEmail($department, $scope, $text)
    {
        global $CONSITENAME, $BASEURL;

        $select = Select::new($this->container->db);
        $select->columns('Email', 'firstName');
        $select->from(<<<Q
            (
            SELECT
               m.*,
               CASE WHEN ac.`Value` IS NOT NULL THEN ac.`Value`
               ELSE af.`InitialValue`
            END AS `Value`
            FROM
                `Members` AS m
            LEFT JOIN `AccountConfiguration` AS ac
            ON
                m.AccountID = ac.AccountID AND ac.FIELD = 'AnnounceEmail'
                AND ac.Value IS NOT NULL
            LEFT JOIN `ConfigurationField` AS af
            ON
                af.Field = 'AnnounceEmail'
            ) AS d
Q
        );
        $select->where('Value = 1');

        if ($scope == 1) {
            $subsel = $select->subselect()->columns('COUNT(AccountID)')->from('ConComList')->whereEquals(['AccountID' => 'd.AccountID']);
            $select->where('0 > ', $subsel);
        } elseif ($scope == 2) {
            $sub2a = $select->subselect()->columns('DepartmentID')->from('ConComList')->whereEquals(['AccountID' => 'd.AccountID']);
            $subsel = $select->subselect()->columns('DepartmentID')->from('Departments')->where('ParentDepartmentID IN ', $sub2a);

            $select->andWhere('(');
            $select->catWhere("{$department['id']} IN ", $sub2a);
            $select->catWhere(')');
            $select->orWhere('(');
            $select->catWhere("{$department['id']} IN ", $subsel);
            $select->catWhere(')');
        }
        $members = $select->fetchAll();

        $subject = "$CONSITENAME New Announcement";

        foreach ($members as $target) {
            $phpView = new Views\PhpRenderer(__DIR__.'/../../Templates', [
                'site' => $BASEURL,
                'department' => $department['name'],
                'announcement' => $text,
                'con' => $CONSITENAME,
                'url' => $BASEURL.'?Function=configuration',
            ]);
            $response = new Response();
            $phpView->render($response, 'newAnnouncements.phtml');
            $response->getBody()->rewind();
            $message = $response->getBody()->getContents();

            \ciab\Email::mail($target['Email'], \getNoReplyAddress(), $subject, $message);
        }

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $department = $this->getDepartment($params['name']);
        $permissions = ['api.post.announcement.all',
        'api.post.announcement.'.$department['id']];
        $this->checkPermissions($permissions);

        $required = ['scope', 'text'];
        $body = $this->checkRequiredBody($request, $required);
        $body['posted_by'] = $request->getAttribute('oauth2-token')['user_id'];
        $body['department'] = $department['id'];

        $insert = Insert::new($this->container->db);
        $insert->into('Announcements');
        $insert->columns(BaseAnnouncement::insertPayloadFromParams($body));
        /* This is the column name, not the memeber parameter name */
        $insert->set('postedOn', 'NOW()');
        $insert->perform();

        if (!array_key_exists('email', $body) || boolval($body['email'])) {
            $this->sendEmail($department, intval($body['scope']), $body['text']);
        }
        return [
        \App\Controller\BaseController::RESULT_TYPE,
        [null],
        201
        ];

    }


    /* end PostAnnouncement */
}
