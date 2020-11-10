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
            $this->addHateoasLink('update', $path.'/member/'.strval($this->id), 'PUT');
            $this->addHateoasLink('updatePassword', $path.'/member/'.strval($this->id).'/password', 'PUT');
            $this->addHateoasLink('deadlines', $path.'/member/'.strval($this->id).'/deadlines', 'GET');
        }

    }


    public function findMember(
        Request $request,
        Response $response,
        $args,
        $key,
        $fields = null
    ) {
        $data = parent::findMember($request, $response, $args, $key, $fields);
        if ($data === null) {
            return $this->errorResponse($request, $response, 'Member Not Found', 'Not Found', 404);
        }
        $this->id = $data['id'];
        return $data;

    }


    public function findMemberId(
        Request $request,
        Response $response,
        $args,
        $key,
        $fields = null
    ) {
        $data = parent::findMemberId($request, $response, $args, $key, $fields);
        if ($data === null) {
            return $this->errorResponse($request, $response, 'Member Not Found', 'Not Found', 404);
        }
        $this->id = $data['id'];
        return $data;

    }


    /* End BaseMember */
}
