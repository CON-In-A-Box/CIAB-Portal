<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Tag(
 *      name="members",
 *      description="Features around members of events"
 *  )
 *
 *  @OA\Schema(
 *      schema="member",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"member"}
 *      ),
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          description="member ID"
 *      ),
 *      allOf={
 *          @OA\Schema(ref="#/components/schemas/member_body")
 *      }
 *  )
 *
 *  @OA\Schema(
 *      schema="member_body",
 *      @OA\Property(
 *          property="firstName",
 *          type="string",
 *          description="Members preferred first name"
 *      ),
 *      @OA\Property(
 *          property="lastName",
 *          type="string",
 *          description="Members preferred last name"
 *      ),
 *      @OA\Property(
 *          property="email",
 *          type="string",
 *          description="Members primary email"
 *      ),
 *      @OA\Property(
 *          property="legalFirstName",
 *          type="string",
 *          description="Members legal first name."
 *      ),
 *      @OA\Property(
 *          property="legalLastName",
 *          type="string",
 *          description="Members legal last name."
 *      ),
 *      @OA\Property(
 *          property="middleName",
 *          type="string",
 *          description="Member's middle name."
 *      ),
 *      @OA\Property(
 *          property="suffix",
 *          type="string",
 *          description="Suffix for members name."
 *      ),
 *      @OA\Property(
 *          property="email2",
 *          type="string",
 *          description="Member's second email."
 *      ),
 *      @OA\Property(
 *          property="email3",
 *          type="string",
 *          description="Member's third email"
 *      ),
 *      @OA\Property(
 *          property="phone1",
 *          type="string",
 *          description="Member's primary phone"
 *      ),
 *      @OA\Property(
 *          property="phone2",
 *          type="string",
 *          description="Member's secondary phone"
 *      ),
 *      @OA\Property(
 *          property="addressLine1",
 *          type="string",
 *          description="Member's address line 1"
 *      ),
 *      @OA\Property(
 *          property="addressLine2",
 *          type="string",
 *          description="Member's address line 2"
 *      ),
 *      @OA\Property(
 *          property="city",
 *          type="string",
 *          description="Member's address city."
 *      ),
 *      @OA\Property(
 *          property="state",
 *          type="string",
 *          description="Member's address state"
 *      ),
 *      @OA\Property(
 *          property="zipCode",
 *          type="string",
 *          description="Member's Address Zip code."
 *      ),
 *      @OA\Property(
 *          property="zipPlus4",
 *          type="string",
 *          description="Member's Address Zip code suffix"
 *      ),
 *      @OA\Property(
 *          property="countryName",
 *          type="string",
 *          description="Member's Address country."
 *      ),
 *      @OA\Property(
 *          property="province",
 *          type="string",
 *          description="Member's Address province."
 *      ),
 *      @OA\Property(
 *          property="preferredFirstName",
 *          type="string",
 *          description="Member's Preferred First Name."
 *      ),
 *      @OA\Property(
 *          property="preferredLastName",
 *          type="string",
 *          description="Member's Preferred Last Name."
 *      ),
 *      @OA\Property(
 *          property="Deceased",
 *          type="boolean",
 *          description="Is member deceased."
 *      ),
 *      @OA\Property(
 *          property="DoNotContact",
 *          type="boolean",
 *          description="Do not contact member."
 *      ),
 *      @OA\Property(
 *          property="EmailOptOut",
 *          type="boolean",
 *          description="Do not mass email member."
 *      ),
 *      @OA\Property(
 *          property="Birthdate",
 *          type="string",
 *          format="date",
 *          description="Member's birth date."
 *      ),
 *      @OA\Property(
 *          property="Gender",
 *          type="string",
 *          description="Member's preferred gender string."
 *      ),
 *      @OA\Property(
 *          property="conComDisplayPhone",
 *          type="boolean",
 *          description="If Concom display phone on list."
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="member_reference",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"member_reference"}
 *      ),
 *      @OA\Property(
 *          property="id",
 *          description="Member Id",
 *          type="integer"
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="member_list",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"member_list"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of members",
 *          @OA\Items(
 *              ref="#/components/schemas/member_reference"
 *          )
 *      )
 *  )
 *
 *   @OA\Response(
 *      response="member_not_found",
 *      description="Member not found in the system.",
 *      @OA\JsonContent(
 *          ref="#/components/schemas/error"
 *      )
 *   )
 **/

namespace App\Controller\Member;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\BaseController;
use App\Controller\NotFoundException;

require_once __DIR__.'/../../../../functions/users.inc';

abstract class BaseMember extends BaseController
{

    /**
     * @var int
     */
    protected $id = 0;


    public function __construct(Container $container)
    {
        parent::__construct('member', $container);

    }


    public function findMember(
        Request $request,
        Response $response,
        $args,
        $key,
        $fields = null
    ) {
        $data = parent::findMember($request, $response, $args, $key, $fields);
        $this->id = $data['id'];
        return $data;

    }


    public function findMemberId(
        Request $request,
        Response $response,
        $args,
        $key,
        $fields = null
    ) {
        $data = parent::findMemberId($request, $response, $args, $key, $fields);
        $this->id = $data['id'];
        return $data;

    }


    /* End BaseMember */
}
