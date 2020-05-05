<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Show;

use Slim\Container;
use App\Modules\artshow\Controller\BaseArtshow;

abstract class BaseShow extends BaseArtshow
{


    public function __construct(Container $container)
    {
        parent::__construct('artshow', $container);

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

        return \ciab\RBAC::havePermission("api.$method.artshow.show");

    }


    /* End BaseArtShow */
}
