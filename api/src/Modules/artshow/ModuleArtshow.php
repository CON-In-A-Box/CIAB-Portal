<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow;

use App\Modules\BaseModule;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Modules\artshow\Database\ArtshowDBSchema;

class ModuleArtshow extends BaseModule
{


    public function __construct($source)
    {
        parent::__construct($source);

    }


    public function databaseInstall($container)
    {
        $db = new ArtshowDBSchema($container->db);
        $db->update();

    }


    public function valid()
    {
        return true;

    }


    public function handle(Request $request, Response $response, $data, $code)
    {
        return $data;

    }


    /* ModuleArtshow */
}
