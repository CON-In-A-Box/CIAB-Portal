<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Modules\registration\Controller\BaseRegistration;
use App\Controller\PermissionDeniedException;
use App\Controller\NotFoundException;
use App\Controller\ConflictException;

abstract class BaseTicket extends BaseRegistration
{

    public $id = 0;


    public function __construct(Container $container)
    {
        parent::__construct($container);

    }


    protected function buildTicketHateoas(Request $request, $id)
    {
        if ($this->id !== 0) {
            $path = $request->getUri()->getBaseUrl();
            $this->addHateoasLink('self', $path.'/registration/ticket/'.strval($id), 'GET');
            $this->addHateoasLink('checkin', $path.'/registration/ticket/'.strval($id).'/checkin', 'PUT');
            $this->addHateoasLink('lost', $path.'/registration/ticket/'.strval($id).'/lost', 'PUT');
            $this->addHateoasLink('pickup', $path.'/registration/ticket/'.strval($id).'/pickup', 'PUT');
            $this->addHateoasLink('email', $path.'/registration/ticket/'.strval($id).'/email', 'PUT');
            $this->addHateoasLink('print', $path.'/registration/ticket/'.strval($id).'/print', 'PUT');
        }

    }


    public function buildTicket($base, $ticket)
    {
        $ticket['type'] = 'ticket';
        foreach ($base as $key => $value) {
            if (!ctype_lower($key[0])) {
                $ticket[lcfirst($key)] = $value;
                unset($ticket[$key]);
                $key = lcfirst($key);
            }
            $key2 = str_replace('ID', '', $key);
            if ($key2 != $key) {
                $ticket[$key2] = $value;
                unset($ticket[$key]);
            }
        }
        $ticket['ticketType'] = $ticket['badgeType'];
        unset($ticket['badgeType']);
        $ticket['id'] = $ticket['registration'];
        unset($ticket['registration']);
        $ticket['member'] = $ticket['account'];
        unset($ticket['account']);

        return $ticket;

    }


    public function getAccount($id, Request $request, Response $response, $permission)
    {
        $user = $request->getAttribute('oauth2-token')['user_id'];

        $sql = "SELECT `AccountID` FROM `Registrations` WHERE `RegistrationID` = $id";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        if (!$data) {
            throw new NotFoundException('Registration Not Found');
        }
        $aid = $data[0]['AccountID'];

        if ($user != $aid && $permission &&
            !\ciab\RBAC::havePermission($permission)) {
            throw new PermissionDeniedException();
        }

        return $aid;

    }


    public function ticketIncludes(Request $request, Response $response, $args, $values, &$data)
    {
        if (empty($values)) {
            return;
        }
        if (in_array('ticketType', $values)) {
            $target = new GetTicketTypes($this->container);
            $newargs = $args;
            $newargs['id'] = $data['ticketType'];
            $newdata = $target->buildResource($request, $response, $newargs)[1];
            $target->processIncludes($request, $response, $args, $values, $newdata);
            $data['ticketType'] = $target->arrayResponse($request, $response, $newdata);
        }
        if (in_array('member', $values)) {
            $target = new \App\Controller\Member\GetMember($this->container);
            $newargs = $args;
            $newargs['id'] = $data['member'];
            $newdata = $target->buildResource($request, $response, $newargs)[1];
            $target->processIncludes($request, $response, $args, $values, $newdata);
            $data['member'] = $target->arrayResponse($request, $response, $newdata);
        }
        if (in_array('registeredBy', $values)) {
            $target = new \App\Controller\Member\GetMember($this->container);
            $newargs = $args;
            $newargs['id'] = $data['registeredBy'];
            $newdata = $target->buildResource($request, $response, $newargs)[1];
            $target->processIncludes($request, $response, $args, $values, $newdata);
            $data['registeredBy'] = $target->arrayResponse($request, $response, $newdata);
        }
        if (in_array('badgeDependentOn', $values)) {
            $target = new \App\Controller\Member\GetMember($this->container);
            $newargs = $args;
            $newargs['id'] = $data['badgeDependentOn'];
            $newdata = $target->buildResource($request, $response, $newargs)[1];
            $target->processIncludes($request, $response, $args, $values, $newdata);
            $data['badgeDependentOn'] = $target->arrayResponse($request, $response, $newdata);
        }
        if (in_array('event', $values)) {
            $target = new \App\Controller\Event\GetEvent($this->container);
            $newargs = $args;
            $newargs['id'] = $data['event'];
            $newdata = $target->buildResource($request, $response, $newargs)[1];
            $target->processIncludes($request, $response, $args, $values, $newdata);
            $data['event'] = $target->arrayResponse($request, $response, $newdata);
        }

    }


    public function printBadge($request, $id)
    {
        $ip = \MyPDO::quote($_SERVER['REMOTE_ADDR']);
        $sql = "UPDATE `Registrations` SET `PrintRequested` = NOW(), `PrintRequestIp` = $ip  WHERE `RegistrationID` = $id AND `VoidDate` IS NULL";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

    }


    protected function updateTicket($request, $response, $params, $rbac, $sql, $error, $getResult = true)
    {
        if ($rbac) {
            $this->checkPermissions([$rbac]);
        }
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        if ($sth->rowCount() == 0) {
            throw new ConflictException($error);
        }

        if ($getResult) {
            $target = new GetTicket($this->container);
            $newdata = $target->buildResource($request, $response, $params)[1];
            $data = $target->arrayResponse($request, $response, $newdata);

            return [
            \App\Controller\BaseController::RESOURCE_TYPE,
            $data
            ];
        }

        return null;

    }


    protected function updateAndPrintTicket($request, $response, $params, $id, $rbac, $sql, $error)
    {
        $aid = $this->getAccount($id, $request, $response, $rbac);
        if (is_array($aid)) {
            return $aid;
        }

        $rc = $this->updateTicket(
            $request,
            $response,
            $params,
            null,
            $sql,
            $error,
            false
        );

        if ($rc == null) {
            $this->printBadge($request, $id);
            return [null];
        }
        return $rc;

    }


    /* End BaseTicket */
}
