<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Put(
 *      tags={"artshow"},
 *      path="/artshow/artist/{artist}/show",
 *      summary="Updates an artist's show registration",
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
 *          response=200,
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
use Atlas\Query\Update;
use Atlas\Query\Insert;
use Atlas\Query\Select;
use App\Controller\InvalidParameterException;

class PutArtistShow extends BaseShow
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $body = $request->getParsedBody();
        if (!$body) {
            throw new InvalidParameterException('No body present');
        }
        $eid = $this->getEventId($request);
        $id = $params['artist'];

        $this->checkShowPermission($request, 'update', $id);

        Update::new($this->container->db)
            ->table('Artshow_Registration')
            ->columns(BaseShow::insertPayloadFromParams($body, false))
            ->whereEquals(['ArtistID' => $id,
                          'EventID' => $eid])
            ->perform();

        $len = strlen('custom_question_');
        foreach ($body as $key => $value) {
            if (substr($key, 0, $len) === 'custom_question_') {
                $data = Select::new($this->container->db)
                    ->columns('QuestionID')
                    ->from('Artshow_RegistrationAnswer')
                    ->whereEquals(['ArtistID' => $id,
                                   'EventID' => $eid,
                                   'QuestionID' => substr($key, $len)])
                   ->fetchOne();
                if (empty($data)) {
                    Insert::new($this->container->db)
                        ->into('Artshow_RegistrationAnswer')
                        ->column('ArtistID', $id)
                        ->column('EventID', $eid)
                        ->column('QuestionID', substr($key, $len))
                        ->column('Answer', $value)
                        ->perform();
                } else {
                    Update::new($this->container->db)
                        ->table('Artshow_RegistrationAnswer')
                        ->column('Answer', $value)
                        ->whereEquals(['ArtistID' => $id,
                                       'EventID' => $eid,
                                       'QuestionID' => substr($key, $len)])
                       ->perform();
                }
            }
        }

        $target = new GetArtistShow($this->container);
        $newparams = ['artist' => $id, 'event' => $eid];
        return $target->buildResource($request, $response, $newparams);

    }


    /* end PutArtistShow */
}
