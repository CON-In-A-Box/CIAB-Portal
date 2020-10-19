<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Announcement;

use Slim\Http\Request;
use Slim\Http\Response;

class ListMemberAnnouncements extends BaseAnnouncement
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        if (array_key_exists('name', $args)) {
            $user = $this->findMember($request, $response, $args, 'name');
            if ($user === null) {
                return $this->errorResponse($request, $response, $error, 'User Not Found', 404);
            }
            $user = $user['id'];
        } else {
            $user = $request->getAttribute('oauth2-token')['user_id'];
        }
        $sth = $this->container->db->prepare(<<<SQL
            SELECT
                *
            FROM
                `Announcements`
            WHERE
                `Scope` = 0 OR
                `Scope` = 1 AND (
                    SELECT
                        COUNT(AccountID)
                    FROM
                        `ConComList`
                    WHERE
                        `AccountID`  = '$user'
                ) > 0 OR
                `Scope` = 2 AND (
                `DepartmentID` IN(
                SELECT
                    `DepartmentID`
                FROM
                    `ConComList`
                WHERE
                    `AccountID` = '$user'
            ) OR `DepartmentID` IN(
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
                        `AccountID` = '$user'
                )
            ))
            ORDER BY `PostedOn` ASC
SQL
        );
        $sth->execute();
        $todos = $sth->fetchAll();
        $output = array();
        $output['type'] = 'announce_list';
        $data = array();
        foreach ($todos as $entry) {
            $announce = new \App\Controller\Announcement\GetAnnouncement($this->container);
            $result = $this->buildAnnouncement(
                $request,
                $response,
                $entry['AnnouncementID'],
                $entry['DepartmentID'],
                $entry['PostedOn'],
                $entry['PostedBy'],
                $entry['Scope'],
                $entry['Text']
            );
            $data[] = $announce->arrayResponse($request, $response, $result);
        }
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        $output];

    }


    public function processIncludes(Request $request, Response $response, $args, $values, &$data)
    {
        $this->baseIncludes($request, $response, $args, $values, $data);

    }


    /* end ListMemberAnnouncements */
}
