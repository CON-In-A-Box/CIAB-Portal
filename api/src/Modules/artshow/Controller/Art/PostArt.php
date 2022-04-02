<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Post(
 *      tags={"artshow"},
 *      path="/artshow/art",
 *      summary="Adds a new piece of art",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      ref="#/components/schemas/artshow_art"
 *                  )
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=201,
 *          description="Art added",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/artshow_art"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\artshow\Controller\Art;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;
use Atlas\Query\Insert;
use App\Controller\InvalidParameterException;

class PostArt extends BaseArt
{


    private function getNextId(array $body) : int
    {
        $data = Select::new($this->container->db)
            ->columns('MAX(PieceID) AS id')
            ->from('Artshow_DisplayArt')
            ->whereEquals(['EventID' => $body['event']])
            ->fetchOne();
        if (empty($data) || $data['id'] == null) {
            return 1;
        } else {
            return intval($data['id']) + 1;
        }

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $body = $this->checkRequiredBody($request, ['name', 'art_type']);

        $prices = array();

        $data = Select::new($this->container->db)
            ->columns('PriceType')
            ->from('Artshow_PriceType')
            ->where('SetPrice')
            ->fetchAll();
        foreach ($data as $priceType) {
            $prices[$priceType['PriceType']] = 0;
        }

        foreach ($body as $key => $value) {
            $key = str_replace('_', ' ', $key);
            if (array_key_exists($key, $prices)) {
                $prices[$key] = $value;
            }
        }

        if (!array_key_exists('event', $body)) {
            $body['event'] = $this->currentEvent();
        }
        if (array_key_exists('artist', $params)) {
            $body['artist'] = $params['artist'];
        }

        $this->checkArtPermission($request, $response, 'add', $body['artist']);

        $fields = BaseArt::insertPayloadFromParams($body, false);

        $fields['EventID'] = $fields['Artshow_DisplayArt.EventID'];
        unset($fields['Artshow_DisplayArt.EventID']);

        $data = Select::new($this->container->db)
            ->columns('PieceType')
            ->from('Artshow_PieceType')
            ->whereEquals(['PieceType' => $body['art_type']])
            ->fetchOne();

        if (empty($data)) {
            throw new InvalidParameterException('\'art_type\' parameter not valid \''.$body['art_type'].'\'');
        }

        $attempt = 0;
        do {
            try {
                $fields['PieceID'] = $this->getNextId($body);

                Insert::new($this->container->db)
                    ->into('Artshow_DisplayArt')
                    ->columns($fields)
                    ->perform();
                break;
            } catch (\Exception $e) {
                $attempt += 1;
                if ($attempt > 20) {
                    throw($e);
                }
            }
        } while (true);

        foreach ($prices as $key => $price) {
            $insert = Insert::new($this->container->db);
            $columns = [
            'PieceID' => $fields['PieceID'],
            'EventID' => $fields['EventID'],
            'PriceType' => $key,
            'Price' => $price
            ];
            $insert->into('Artshow_DisplayArtPrice')
                ->columns($columns)
                ->perform();
        }

        $target = new GetArt($this->container);
        $newparams = ['piece' => $fields['PieceID'], 'event' => $fields['EventID']];
        $data = $target->buildResource($request, $response, $newparams)[1];

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data),
        201
        ];

    }


    /* end PostArt */
}
