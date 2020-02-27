<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Member;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\BaseController;

require_once __DIR__.'/../../../../functions/users.inc';

abstract class BaseMember extends BaseController
{

    /**
     * @var int
     */
    protected $id = 0;


    public function __construct(Container $container)
    {
        parent::__construct('member', $container);

    }


    protected function buildMemberHateoas(Request $request)
    {
        if ($this->id !== 0) {
            $path = $request->getUri()->getBaseUrl();
            $this->addHateoasLink('self', $path.'/member/'.strval($this->id), 'GET');
            $this->addHateoasLink('deadlines', $path.'/member/'.strval($this->id).'/deadlines', 'GET');
        }

    }


    public function findMember(Request $request, Response $response, $args, $key)
    {
        $data = parent::findMember($request, $response, $args, $key);
        if ($data === null) {
            return $this->errorResponse($request, $response, $error, 'Not Found', 404);
        }
        $this->id = $data['Id'];
        return $data;

    }


    /* End BaseMember */
}
