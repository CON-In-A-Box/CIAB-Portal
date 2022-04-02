<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Post(
 *      tags={"artshow"},
 *      path="/artshow/artist/{artist}/show",
 *      summary="Adds a artist event information",
 *      @OA\Parameter(
 *          in="path",
 *          name="artist",
 *          required=true,
 *          @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/target_event",
 *      ),
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="mail_in",
 *                      type="string",
 *                      description="Is this mail in art?"
 *                  ),
 *                  @OA\Property(
 *                      property="return_method",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="insurance_amount",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="initial_payment",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="payment_type",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="check_number",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="notes",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="return_labels",
 *                      type="string"
 *                  )
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=201,
 *          description="OK",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/artshow_artist_event"
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Cycle not found in the system which contains event dates.",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/error"
 *          )
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\artshow\Controller\Show;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Insert;
use App\Controller\PermissionDeniedException;

class PostArtistShow extends BaseShow
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $body = $request->getParsedBody();
        if (empty($body)) {
            $body = array();
        }
        $eid = $this->getEventId($request);
        $id = $params['artist'];

        $this->checkShowPermission($request, 'add', $id);

        $body['artist'] = $id;
        $body['event'] = $eid;

        Insert::new($this->container->db)
            ->into('Artshow_Registration')
            ->columns(BaseShow::insertPayloadFromParams($body, false))
            ->perform();

        $len = strlen('custom_question_');
        foreach ($body as $key => $value) {
            if (substr($key, 0, $len) === 'custom_question_') {
                Insert::new($this->container->db)
                    ->into('Artshow_RegistrationAnswer')
                    ->column('ArtistID', $id)
                    ->column('EventID', $eid)
                    ->column('QuestionID', substr($key, $len))
                    ->column('Answer', $value)
                    ->perform();
            }
        }

        $target = new GetArtistShow($this->container);
        $newparams = ['artist' => $id, 'event' => $eid];
        $data = $target->buildResource($request, $response, $newparams)[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data),
        201
        ];

    }


    /* end PostArtistShow */
}
