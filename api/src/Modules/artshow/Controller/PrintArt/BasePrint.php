<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\PrintArt;

use Slim\Container;
use Slim\Http\Request;
use App\Modules\artshow\Controller\BaseArtshow;

abstract class BasePrint extends BaseArtshow
{


    public function __construct(Container $container)
    {
        parent::__construct('print', $container);

    }


    protected function buildHateoas(Request $request, $data)
    {
        $path = $request->getUri()->getBaseUrl();
        $this->addHateoasLink('self', $path.'/artshow/print/'.strval($data['PieceID']).'/'.strval($data['EventID']), 'GET');
        $this->addHateoasLink('artist', $path.'/artshow/artist/'.strval($data['ArtistID']), 'GET');

    }


    protected function checkPermission($request, $method, $ArtistID)
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

        return \ciab\RBAC::havePermission("api.$method.artshow.print");

    }


    /* End BasePrint */
}
