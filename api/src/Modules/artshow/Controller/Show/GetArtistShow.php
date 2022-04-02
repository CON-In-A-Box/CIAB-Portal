<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"artshow"},
 *      path="/artshow/artist/{artist}/show",
 *      summary="Gets the event information for an artist",
 *      @OA\Parameter(
 *          in="path",
 *          name="artist",
 *          required=true,
 *          @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/target_event",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Payment type found",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/artshow_artist_event"
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

namespace App\Modules\artshow\Controller\Show;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\NotFoundException;
use Atlas\Query\Select;
use \App\Controller\IncludeResource;

class GetArtistShow extends BaseShow
{


    public function __construct($container)
    {
        parent::__construct($container);
        $this->includes = [
        new IncludeResource('\App\Controller\Event\GetEvent', 'id', 'event'),
        new IncludeResource('\App\Modules\artshow\Controller\Artist\GetArtist', 'id', 'artist')
        ];

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $eid = $this->getEventId($request);

        $result = Select::new($this->container->db)
            ->columns(...BaseShow::selectMapping())
            ->from('Artshow_Registration')
            ->whereEquals(['ArtistID' => $params['artist'],
                          'EventID' => $eid])
            ->fetchOne();
        if (empty($result)) {
            throw new NotFoundException('Artist Event Registration Not Found');
        }
        $artist = $result;

        $result = Select::new($this->container->db)
            ->columns('*')
            ->from('Artshow_RegistrationAnswer')
            ->whereEquals(['ArtistID' => $params['artist'],
                           'EventID' => $eid])
            ->fetchAll();
        foreach ($result as $value) {
            $artist['custom_question_'.$value['QuestionID']] = $value['Answer'];
        }

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $artist];

    }


    /* end GetArtistShow */
}
