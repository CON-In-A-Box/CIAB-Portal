<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Post(
 *      tags={"members"},
 *      path="/member",
 *      summary="Adds a new member",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  ref="#/components/schemas/member_body"
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=201,
 *          description="OK"
 *      ),
 *      @OA\Response(
 *          response=409,
 *          description="Email address already in use.",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/error"
 *          )
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Required parameters missing.",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/error"
 *          )
 *      )
 *  )
 **/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Insert;
use Atlas\Query\Select;
use App\Error\InvalidParameterException;
use App\Error\ConflictException;

class PostMember extends BaseMember
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $required = ['email'];
        $body = $this->checkRequiredBody($request, $required);
        $good = false;
        try {
            $this->getMember($request, $body['email'], 'email');
        } catch (\Exception $e) {
            $good = true;
        }
        if (!$good) {
            throw new ConflictException("Account with Email Already Exists");
        }

        if (!array_key_exists('legal_first_name', $body) &&
            !array_key_exists('legal_last_name', $body)) {
            throw new InvalidParameterException("Required 'legal_first_name' and/or 'legal_last_name' parameter not present");
        }

        $currentIdTop = Select::new($this->container->db)
            ->columns('MAX(AccountID) AS max')
            ->from('Members')
            ->fetchOne()['max'];

        if ($currentIdTop < 1000) {
            $body['id'] = 1000;
        } else {
            unset($body['id']);
        }

        Insert::new($this->container->db)
            ->into('Members')
            ->columns(BaseMember::insertPayloadFromParams($body))
            ->perform();

        $pwd = new \App\Controller\Member\PostPassword($this->container);
        $pwd->privilaged = true;
        $pwd->buildResource($request, $response, ['email' => $body['email']]);

        $target = $this->getMember($request, $body['email'], 'email');
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target[0],
        201
        ];

    }


    /* end PostMember */
}
