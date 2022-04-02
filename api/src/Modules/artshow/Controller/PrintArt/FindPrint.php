<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"artshow"},
 *      path="/artshow/print/find",
 *      summary="Search for a piece of print art based on the query",
 *      @OA\Parameter(
 *          description="Query string",
 *          in="query",
 *          name="q",
 *          required=true,
 *          @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          description="Comma separated list of attributes to be searched, default = 'all'",
 *          in="query",
 *          name="from",
 *          required=false,
 *          @OA\Schema(
 *              type="array",
 *              @OA\Items(
 *                  type="string",
 *                  enum={"all","artist_name","piece_name","artist_id","piece_id"}
 *              )
 *          ),
 *          style="simple",
 *          explode=false
 *      ),
 *      @OA\Parameter(
 *          description="Allow partial matches, default is false",
 *          in="query",
 *          name="partial",
 *          required=false,
 *          @OA\Schema(type="boolean")
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/event",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/max_results",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/page_token",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Print art found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/artshow_print_list"
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/print_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 *
 *  @OA\Schema(
 *      schema="artshow_found_print_list",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"artshow_found_print"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of print art found",
 *          @OA\Items(
 *              ref="#/components/schemas/artshow_print"
 *          ),
 *      )
 *  )
 **/

namespace App\Modules\artshow\Controller\PrintArt;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;

use App\Modules\artshow\Controller\BaseArtshow;
use App\Controller\IncludeResource;
use App\Controller\NotFoundException;
use App\Controller\InvalidParameterException;

class FindPrint extends BasePrint
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $q = $request->getQueryParam('q', null);
        if ($q === null) {
            throw new InvalidParameterException("'q' not present");
        }

        $q = trim($q);
        $from = $request->getQueryParam('from', 'all');
        if ($from == 'all') {
            $from = "artist_name,artist_id,piece_name,piece_id";
        }
        $from = explode(',', trim($from));
        $p = $request->getQueryParam('partial', 'false');
        $partial = filter_var($p, FILTER_VALIDATE_BOOLEAN);

        $eid = $this->getEventId($request);

        $select = Select::new($this->container->db)
            ->columns('da.PieceID as PieceID')
            ->from('Artshow_PrintShopArt da')
            ->join('LEFT', 'Artshow_Artist aa', 'da.ArtistID = aa.ArtistID')
            ->join('LEFT', 'Members m', 'm.AccountID = aa.AccountID')
            ->orderBy('da.ArtistID ASC')
            ->whereEquals(['EventID' => $eid])
            ->catWhere(' AND ( False ');

        if (in_array('artist_name', $from)) {
            $names = explode(" ", $q);
            if ($partial) {
                if (count($names) > 1) {
                    $select->orWhere("PreferredLastName LIKE '%{$names[1]}%'");
                    $select->orWhere("LastName LIKE '%{$names[1]}%'");
                } else {
                    $select->orWhere("PreferredLastName LIKE '%{$names[0]}%'");
                    $select->orWhere("LastName LIKE '%{$names[0]}%'");
                }
                $select->orWhere("PreferredFirstName LIKE '%{$names[0]}%'");
                $select->orWhere("FirstName LIKE '%{$names[0]}%'");
            } else {
                if (count($names) == 2) {
                    $select->orWhere('((');
                    $select->catWhere(' PreferredFirstName = ', $names[0]);
                    $select->catWhere(' OR FirstName = ', $names[0]);
                    $select->catWhere(' ) AND ( ');
                    $select->catWhere('PreferredLastName = ', $names[1]);
                    $select->catWhere(' OR LastName = ', $names[1]);
                    $select->catWhere(' ))');
                }
            }
        }
        if (in_array('artist_id', $from)) {
            if ($partial) {
                $select->orWhere('da.ArtistID LIKE ', "%$q%");
            } else {
                $select->orWhere('da.ArtistID =', $q);
            }
        }
        if (in_array('piece_id', $from)) {
            if ($partial) {
                $select->orWhere('da.PieceID LIKE ', "%$q%");
            } else {
                $select->orWhere('da.PieceID =', $q);
            }
        }
        if (in_array('piece_name', $from)) {
            if ($partial) {
                $select->orWhere('da.Name LIKE ', "%$q%");
            } else {
                $select->orWhere('da.Name =', $q);
            }
        }
        $select->catWhere(')');

        $result = $select->fetchAll();
        if (count($result) == 0) {
            throw new NotFoundException('Art Not Found');
        }

        $data = [];
        foreach ($result as $entry) {
            $target = new GetPrint($this->container);
            $newparams = ['piece' => $entry['PieceID'], 'event' => $eid];
            $art = $target->buildResource($request, $response, $newparams)[1];
            $target->processIncludes($request, $response, $params, $art, []);
            $data[] = $art;
        }

        $output = ['type' => 'art_list'];
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        $output];

    }


    /* end FindPrint */
}
