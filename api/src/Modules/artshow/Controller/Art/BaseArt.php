<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Art;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Modules\artshow\Controller\BaseArtshow;

abstract class BaseArt extends BaseArtshow
{


    public function __construct(Container $container)
    {
        parent::__construct('art', $container);

    }


    protected function buildArtHateoas(Request $request, $data)
    {
        $path = $request->getUri()->getBaseUrl();
        $this->addHateoasLink('self', $path.'/artshow/art/piece/'.strval($data['PieceID']).'/'.strval($data['EventID']), 'GET');
        $this->addHateoasLink('artist', $path.'/artshow/artist/'.strval($data['ArtistID']), 'GET');

    }


    public function buildArt(Request $request, Response $response, $data)
    {
        $output = array();
        foreach ($data as $key => $value) {
            if ($key == 'PriceType') {
                continue;
            }
            if ($key == 'Price') {
                continue;
            }
            $output[$key] = $value;
        }
        $output['type'] = 'art';
        $output[$data['PriceType']] = $data['Price'];
        return $output;

    }


    protected function checkArtPermission($request, $method, $ArtistID)
    {
        $logged = $request->getAttribute('oauth2-token')['user_id'];

        $sql = "SELECT `AccountID` FROM `Artshow_Artist` WHERE `ArtistID` = $ArtistID";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll();
        if (empty($result)) {
            return false;
        }
        $accountID = $result[0]['AccountID'];

        if ($logged == $accountID) {
            return true;
        }

        return \ciab\RBAC::havePermission("api.$method.artshow.art");

    }


    /* End BaseArt */
}
