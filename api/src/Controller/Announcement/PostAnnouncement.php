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
 *      summary="Adds a new announcement",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="Text",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="Scope",
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
use App\Controller\InvalidParameterException;

require_once __DIR__.'/../../../../functions/users.inc';

class PostAnnouncement extends BaseAnnouncement
{


    private function sendEmail($department, $scope, $text)
    {
        global $CONSITENAME, $BASEURL;

        $condition = '';

        if ($scope == 1) {
            $condition = <<<SQL
                AND (
                    SELECT
                        COUNT(AccountID)
                    FROM
                        `ConComList`
                    WHERE
                        `AccountID`  = d.AccountID
                ) > 0
SQL;
        } elseif ($scope == 2) {
            $condition = <<<SQL
              AND (
                {$department['id']} IN(
                SELECT
                    `DepartmentID`
                FROM
                    `ConComList`
                WHERE
                    `AccountID` = d.AccountID
            ) OR {$department['id']} IN(
                SELECT
                    `DepartmentID`
                FROM
                    `Departments`
                WHERE
                    `ParentDepartmentID` IN(
                    SELECT
                        `DepartmentID`
                    FROM
                        `ConComList`
                    WHERE
                        `AccountID` = d.AccountID
                )
            ))
SQL;
        }
        $sql = <<<SQL
            SELECT
               Email, firstName
            FROM
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
            WHERE
                `Value` = 1
                $condition
SQL;
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $members = $sth->fetchAll();

        $subject = "$CONSITENAME New Announcement";

        foreach ($members as $target) {
            $phpView = new Views\PhpRenderer(__DIR__.'/../../Templates', [
                'site' => $BASEURL,
                'department' => $department['Name'],
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


    public function buildResource(Request $request, Response $response, $args): array
    {
        $department = $this->getDepartment($args['name']);
        $permissions = ['api.post.announcement.all',
        'api.post.announcement.'.$department['id']];
        $this->checkPermissions($permissions);

        $body = $request->getParsedBody();
        if ($body == null) {
            throw new InvalidParameterException('Required parameters not present');
        }
        if (!array_key_exists('Scope', $body)) {
            throw new InvalidParameterException('Required \'Scope\' parameter not present');
        }
        if (!array_key_exists('Text', $body)) {
            throw new InvalidParameterException('Required \'Text\' parameter not present');
        }

        $user = $this->findMember($request, $response, null, null);
        $member = $user['id'];
        $text = \MyPDO::quote($body['Text']);

        $sth = $this->container->db->prepare("INSERT INTO `Announcements` (DepartmentID, PostedBy, PostedOn, Scope, Text) VALUES ({$department['id']}, $member, now(), '{$body['Scope']}', $text)");
        $sth->execute();

        if (!array_key_exists('Email', $body) || boolval($body['Email'])) {
            $this->sendEmail($department, intval($body['Scope']), $text);
        }
        return [
        \App\Controller\BaseController::RESULT_TYPE,
        [null],
        201
        ];

    }


    /* end PostAnnouncement */
}
