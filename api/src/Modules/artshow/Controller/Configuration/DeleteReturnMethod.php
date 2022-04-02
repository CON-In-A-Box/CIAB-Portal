<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Delete(
 *      tags={"artshow"},
 *      path="/artshow/configuration/returnmethod/{type}",
 *      summary="Deletes a return method",
 *      @OA\Parameter(
 *          in="path",
 *          name="type",
 *          required=true,
 *          @OA\Schema(type="string")
 *      ),
 *      @OA\Response(
 *          response=204,
 *          description="OK"
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/artshow_configuration_not_found"
 *      ),
 *      security={
 *          {"ciab_auth": {}}
 *       }
 *  )
 **/

namespace App\Modules\artshow\Controller\Configuration;

class DeleteReturnMethod extends BaseReturnMethod
{

    use TraitDelete;

    /* end DeleteReturnMethod */
}
