<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Schema(
 *      schema="artshow_registration_question",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"registration_question"}
 *      ),
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          description="Question Id"
 *      ),
 *      @OA\Property(
 *          property="boolean",
 *          type="integer",
 *          description="Question is a yes/no question"
 *      ),
 *      @OA\Property(
 *          property="text",
 *          type="string",
 *          description="Text of the question."
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="artshow_registration_question_list",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"registration_question_list"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of registration_question types",
 *          @OA\Items(
 *              ref="#/components/schemas/artshow_registration_question"
 *          ),
 *      )
 *  )
 *
 *  @OA\Get(
 *      tags={"artshow"},
 *      path="/artshow/configuration/registrationquestion/{type}",
 *      summary="Gets an art show registration question",
 *      @OA\Parameter(
 *          description="Question ID to get",
 *          in="path",
 *          name="type",
 *          required=true,
 *          @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Payment type found",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/artshow_registration_question"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/artshow_configuration_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 *
 *  @OA\Get(
 *      tags={"artshow"},
 *      path="/artshow/configuration/registrationquestion",
 *      summary="List art show registration questions ",
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Payment type found",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/artshow_registration_question_list"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/artshow_configuration_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\artshow\Controller\Configuration;

class GetRegistrationQuestion extends BaseRegistrationQuestion
{

    use TraitGet;

    protected static $list_type = 'registration_question_list';

    /* end GetRegistrationQuestion */
}
