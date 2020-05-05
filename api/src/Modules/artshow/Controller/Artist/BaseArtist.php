<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Artist;

use Slim\Container;
use App\Controller\BaseController;
use App\Modules\artshow\Controller\BaseArtshow;

abstract class BaseArtist extends BaseArtshow
{


    public function __construct(Container $container)
    {
        parent::__construct('artist', $container);

    }


    protected function checkArtistPermission($request, $method, $ArtistID)
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

        return \ciab\RBAC::havePermission("api.$method.artshow.artist");

    }


    /* End BaseArtist */
}
