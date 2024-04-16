<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"registration"},
 *      path="/registration/admin",
 *      summary="Returns is the current account is a registration admin.",
 *      deprecated=true,
 *      @OA\Response(
 *          response=200,
 *          description="Member status found",
 *          @OA\JsonContent(
 *              @OA\Property(
 *                  property="admin",
 *                  type="boolean",
 *                  enum={True}
 *              ),
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\registration\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

class IsAdmin extends BaseRegistration
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $this->checkPutPermission();
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        ['admin' => true]];

    }


    /* end IsAdmin */
}
