<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"members"},
 *      path="/member/find",
 *      summary="Search for a member based on the query",
 *      @OA\Parameter(
 *          description="Query string",
 *          in="query",
 *          name="q",
 *          required=true,
 *          @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          description="Comma seperated list of attributes to be searched, default = 'all'",
 *          in="query",
 *          name="from",
 *          required=false,
 *          @OA\Schema(
 *             oneOf = {
 *                  @OA\Schema(
 *                      type="array",
 *                      @OA\Items(
 *                          type="string",
 *                          enum={"all","email","id","legal_name","name","badge", "badge_id"}
 *                      )
 *                  ),
 *                  @OA\Schema(
 *                      type="string",
 *                      enum={"all","email","id","legal_name","name","badge", "badge_id"}
 *                  )
 *              }
 *          ),
 *          style="simple",
 *          explode=false
 *      ),
 *      @OA\Parameter(
 *          description="Allow partial matches, default is false",
 *          in="query",
 *          name="partial",
 *          required=false,
 *          @OA\Schema(type="boolean")
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/event",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/max_results",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/page_token",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Member(s) found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/member_list"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=400,
 *          ref="#/components/responses/400"
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/member_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;

use App\Error\NotFoundException;
use App\Error\InvalidParameterException;

class FindMembers extends BaseMember
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $q = $request->getQueryParam('q', null);
        if ($q === null || strlen($q) == 0) {
            throw new InvalidParameterException("'q' not present");
        }
        $q = trim($q);
        $from = $request->getQueryParam('from', 'all');
        if ($from == 'all') {
            $from = "email,id,legal_name,name,badge,badge_id";
        }
        $from = explode(',', trim($from));
        $p = $request->getQueryParam('partial', 'false');
        $partial = filter_var($p, FILTER_VALIDATE_BOOLEAN);

        $select = Select::new($this->container->db)
            ->columns(...BaseMember::selectMapping())
            ->columns('(CASE WHEN `PreferredFirstName` IS NOT NULL THEN `PreferredFirstName` ELSE `FirstName` END) AS `first_name`')
            ->columns('(CASE WHEN `PreferredLastName` IS NOT NULL THEN `PreferredLastName` ELSE `LastName` END) AS `last_name`')
            ->columns('(SELECT GROUP_CONCAT(AccountID SEPARATOR \', \') FROM `Members` m2 WHERE m2.Email = m1.Email AND NOT m2.AccountID = m1.AccountID) AS duplicates')
            ->from('`Members` m1')
            ->orderBy('AccountID ASC');

        if (in_array('email', $from)) {
            if ($partial) {
                $select->orWhere('Email LIKE ', "%$q%");
            } else {
                $select->orWhere('Email = ', $q);
            }
            if (\ciab\CRM::active()) {
                \ciab\CRM::lookupUsersByEmail($q, false, $partial, array());
            }
        }
        if (in_array('id', $from)) {
            if ($q[0] == 'A') {
                $q2 = substr($q, 1);
            } else {
                $q2 = $q;
            }
            $select->orWhere('AccountID = ', $q2);
            if (\ciab\CRM::active()) {
                \ciab\CRM::lookupUsersByIds($q2, false, array());
            }
        }
        if (in_array('legal_name', $from)) {
            $names = explode(" ", $q);
            if ($partial) {
                $select->orWhere("FirstName LIKE '%$names[0]%'");
                if (count($names) > 1) {
                    $select->orWhere("LastName LIKE '%$names[1]%'");
                }
            } else {
                if (count($names) == 2) {
                    $select->orWhere('(');
                    $select->catWhere("FirstName = ", $names[0]);
                    $select->catWhere(" AND LastName = ", $names[1]);
                    $select->catWhere(')');
                }
            }
            if (\ciab\CRM::active()) {
                \ciab\CRM::lookupUsersByName($q, false, $partial, false, array());
            }
        }
        if (in_array('name', $from)) {
            $names = explode(" ", $q);
            if ($partial) {
                if (count($names) > 1) {
                    $select->orWhere("PreferredLastName LIKE '%{$names[1]}%'");
                    $select->orWhere("LastName LIKE '%{$names[1]}%'");
                } else {
                    $select->orWhere("PreferredLastName LIKE '%{$names[0]}%'");
                    $select->orWhere("LastName LIKE '%{$names[0]}%'");
                }
                $select->orWhere("PreferredFirstName LIKE '%{$names[0]}%'");
                $select->orWhere("FirstName LIKE '%{$names[0]}%'");
            } else {
                if (count($names) == 2) {
                    $select->orWhere('((');
                    $select->catWhere(' PreferredFirstName = ', $names[0]);
                    $select->catWhere(' OR FirstName = ', $names[0]);
                    $select->catWhere(' ) AND ( ');
                    $select->catWhere('PreferredLastName = ', $names[1]);
                    $select->catWhere(' OR LastName = ', $names[1]);
                    $select->catWhere(' ))');
                } elseif (count($from) == 1) {
                    throw new NotFoundException('Member Not Found');
                }
            }
            if (\ciab\CRM::active()) {
                \ciab\CRM::lookupUsersByName($q, false, $partial, true, array());
            }
        }
        if (in_array('badge', $from)) {
            $event = $this->getEventID($request);
            $sub = $select->subselect()
                ->columns('AccountID')
                ->from('Registrations');
            if ($partial) {
                $sub->orWhere("BadgeName LIKE '%$q%'");
            } else {
                $sub->orWhere('BadgeName = ', $q);
            }
            $sub->andWhere('EventID = ', $event);
            $select->orWhere('AccountID IN ', $sub);
        }
        if (in_array('badge_id', $from)) {
            $event = $this->getEventID($request);
            $sub = $select->subselect()
                ->columns('AccountID')
                ->from('Registrations')
                ->whereEquals(['BadgeID' => $q, 'EventID' => $event]);
            $select->orWhere('AccountID IN ', $sub);
            if (\ciab\CRM::active()) {
                \ciab\CRM::lookupUsersByIds($q, false, array());
            }
        }

        $result = $select->fetchAll();

        if (!$this->internal && !$this->container->RBAC->havePermission('api.get.member')) {
            $id = $request->getAttribute('oauth2-token')['user_id'];
            foreach ($result as $index => $member) {
                if ($member['id'] != $id) {
                    $result[$index] = [
                        'type' => 'member',
                        'id' => $member['id'],
                        'first_name' => $member['first_name'],
                        'last_name' => $member['last_name']
                    ];
                }
            }
        }

        if (count($result) == 0) {
            throw new NotFoundException('Member Not Found');
        }

        $output = ['type' => 'member_list'];
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $result,
        $output];

    }


    /* end FindMembers */
}
