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
 *          property="first_name",
 *          type="string",
 *          description="Members preferred first name"
 *      ),
 *      @OA\Property(
 *          property="last_name",
 *          type="string",
 *          description="Members preferred last name"
 *      ),
 *      @OA\Property(
 *          property="email",
 *          type="string",
 *          description="Members primary email"
 *      ),
 *      @OA\Property(
 *          property="legal_first_name",
 *          type="string",
 *          description="Members legal first name."
 *      ),
 *      @OA\Property(
 *          property="legal_last_name",
 *          type="string",
 *          description="Members legal last name."
 *      ),
 *      @OA\Property(
 *          property="middle_name",
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
 *          property="phone",
 *          type="string",
 *          description="Member's primary phone"
 *      ),
 *      @OA\Property(
 *          property="phone2",
 *          type="string",
 *          description="Member's secondary phone"
 *      ),
 *      @OA\Property(
 *          property="address_line1",
 *          type="string",
 *          description="Member's address line 1"
 *      ),
 *      @OA\Property(
 *          property="address_line2",
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
 *          property="zip_code",
 *          type="string",
 *          description="Member's Address Zip code."
 *      ),
 *      @OA\Property(
 *          property="zip_plus4",
 *          type="string",
 *          description="Member's Address Zip code suffix"
 *      ),
 *      @OA\Property(
 *          property="country",
 *          type="string",
 *          description="Member's Address country."
 *      ),
 *      @OA\Property(
 *          property="province",
 *          type="string",
 *          description="Member's Address province."
 *      ),
 *      @OA\Property(
 *          property="preferred_first_name",
 *          type="string",
 *          description="Member's Preferred First Name."
 *      ),
 *      @OA\Property(
 *          property="preferred_last_name",
 *          type="string",
 *          description="Member's Preferred Last Name."
 *      ),
 *      @OA\Property(
 *          property="deceased",
 *          type="boolean",
 *          description="Is member deceased."
 *      ),
 *      @OA\Property(
 *          property="do_not_contact",
 *          type="boolean",
 *          description="Do not contact member."
 *      ),
 *      @OA\Property(
 *          property="email_optout",
 *          type="boolean",
 *          description="Do not mass email member."
 *      ),
 *      @OA\Property(
 *          property="birthdate",
 *          type="string",
 *          format="date",
 *          description="Member's birth date."
 *      ),
 *      @OA\Property(
 *          property="gender",
 *          type="string",
 *          description="Member's preferred gender string."
 *      ),
 *      @OA\Property(
 *          property="concom_display_phone",
 *          type="boolean",
 *          description="If Concom display phone on list."
 *      ),
 *      @OA\Property(
 *          property="duplicates",
 *          type="string",
 *          description="Comma seperated list of other account IDs with the same email address"
 *      ),
 *      @OA\Property(
 *          property="pronouns",
 *          type="string",
 *          description="Prefered pronouns."
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

require_once __DIR__.'/../../../../backends/CRM.inc';

abstract class BaseMember extends BaseController
{

    protected static $columnsToAttributes = [
    '"member"' => 'type',
    'AccountID' => 'id',
    'FirstName' => 'legal_first_name',
    'LastName' => 'legal_last_name',
    'MiddleName' => 'middle_name',
    'Suffix' => 'suffix',
    'Email' => 'email',
    'Email2' => 'email2',
    'Email3' => 'email3',
    'Phone' => 'phone',
    'Phone2' => 'phone2',
    'AddressLine1' => 'address_line1',
    'AddressLine2' => 'address_line2',
    'AddressCity' => 'city',
    'AddressState' => 'state',
    'AddressZipCode' => 'zip_code',
    'AddressZipCodeSuffix' => 'zip_plus4',
    'AddressCountry' => 'country',
    'AddressProvince' => 'province',
    'PreferredFirstName' => 'preferred_first_name',
    'PreferredLastName' => 'preferred_last_name',
    'Deceased' => 'deceased',
    'DoNotContact' => 'do_not_contact',
    'EmailOptOut' => 'email_optout',
    'Birthdate' => 'birthdate',
    'Gender' => 'gender',
    'DisplayPhone' => 'concom_display_phone',
    'dependentOnID' => 'dependent_on',
    'Pronouns' => 'pronouns'
    ];

    /**
     * @var int
     */
    protected $id = 0;


    public function __construct(Container $container)
    {
        parent::__construct('member', $container);

    }


    public static function install($database): void
    {

    }


    public static function permissions($database): ?array
    {
        return ['api.get.member', 'admin.sudo', 'api.put.member', 'api.put.member.password'];

    }


    /* End BaseMember */
}
