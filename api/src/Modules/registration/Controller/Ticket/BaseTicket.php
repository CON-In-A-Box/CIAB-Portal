<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Tag(
 *      name="registration",
 *      description="Features around members of event registration"
 *  )
 *
 *  @OA\Schema(
 *      schema="ticket",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"ticket"}
 *      ),
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *      ),
 *      @OA\Property(
 *          property="badgeDependentOn",
 *          description="Member badge is dependent on",
 *          oneOf={
 *              @OA\Schema(
 *                  ref="#/components/schemas/member"
 *              ),
 *              @OA\Schema(
 *                  type="integer",
 *                  description="Member Id"
 *              )
 *          }
 *      ),
 *      @OA\Property(
 *          property="badgeName",
 *          type="string",
 *      ),
 *      @OA\Property(
 *          property="badgesPickedUp",
 *          type="integer",
 *          description="The number of times this badge has been printed and picked up"
 *      ),
 *      @OA\Property(
 *          property="emergencyContact",
 *          type="string",
 *      ),
 *      @OA\Property(
 *          property="event",
 *          description="Event the badge is for",
 *          oneOf={
 *              @OA\Schema(
 *                  ref="#/components/schemas/event"
 *              ),
 *              @OA\Schema(
 *                  type="integer",
 *                  description="Event Id"
 *              )
 *          }
 *      ),
 *      @OA\Property(
 *          property="registeredBy",
 *          description="Member who create the ticket",
 *          oneOf={
 *              @OA\Schema(
 *                  ref="#/components/schemas/member"
 *              ),
 *              @OA\Schema(
 *                  type="integer",
 *                  description="Member Id"
 *              )
 *          }
 *      ),
 *      @OA\Property(
 *          property="registrationDate",
 *          type="string",
 *          format="date",
 *      ),
 *      @OA\Property(
 *          property="boardingPassGenerated",
 *          type="string",
 *          format="date",
 *      ),
 *      @OA\Property(
 *          property="printRequested",
 *          type="string",
 *          format="date",
 *      ),
 *      @OA\Property(
 *          property="lastPrintedDate",
 *          type="string",
 *          format="date",
 *      ),
 *      @OA\Property(
 *          property="printRequestIp",
 *          type="string",
 *          format="ip",
 *      ),
 *      @OA\Property(
 *          property="note",
 *          type="string",
 *      ),
 *      @OA\Property(
 *          property="voidDate",
 *          type="string",
 *          format="date",
 *      ),
 *      @OA\Property(
 *          property="voidBy",
 *          description="Member who voided the ticket",
 *          oneOf={
 *              @OA\Schema(
 *                  ref="#/components/schemas/member"
 *              ),
 *              @OA\Schema(
 *                  type="integer",
 *                  description="Member Id"
 *              )
 *          }
 *      ),
 *      @OA\Property(
 *          property="voidReason",
 *          type="string",
 *      ),
 *      @OA\Property(
 *          property="ticketType",
 *          description="Type of the ticket",
 *          oneOf={
 *              @OA\Schema(
 *                  ref="#/components/schemas/ticket_type"
 *              ),
 *              @OA\Schema(
 *                  type="integer",
 *                  description="Ticket Type Id"
 *              )
 *          }
 *      ),
 *      @OA\Property(
 *          property="member",
 *          description="Member who the ticket is for",
 *          oneOf={
 *              @OA\Schema(
 *                  ref="#/components/schemas/member"
 *              ),
 *              @OA\Schema(
 *                  type="integer",
 *                  description="Member Id"
 *              )
 *          }
 *      ),
 *  )
 *
 *  @OA\Schema(
 *      schema="ticket_type",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"ticket_type"}
 *      ),
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *      ),
 *      @OA\Property(
 *          property="event",
 *          description="Event the badge is for",
 *          oneOf={
 *              @OA\Schema(
 *                  ref="#/components/schemas/event"
 *              ),
 *              @OA\Schema(
 *                  type="integer",
 *                  description="Event Id"
 *              )
 *          }
 *      ),
 *      @OA\Property(
 *          property="name",
 *          type="string",
 *      ),
 *      @OA\Property(
 *          property="avaliableFrom",
 *          type="string",
 *          format="date",
 *      ),
 *      @OA\Property(
 *          property="avaliableTo",
 *          type="string",
 *          format="date",
 *      ),
 *      @OA\Property(
 *          property="cost",
 *          type="number",
 *          format="float",
 *      ),
 *      @OA\Property(
 *          property="backgroundImage",
 *          type="string",
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="ticket_type_list",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"ticket_type_list"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          @OA\Items(
 *              ref="#/components/schemas/ticket_type"
 *          )
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="ticket_list",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"ticket_list"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          @OA\Items(
 *              ref="#/components/schemas/ticket"
 *          )
 *      )
 *  )
 *
 *   @OA\Response(
 *      response="ticket_not_found",
 *      description="Ticket not found in the system.",
 *      @OA\JsonContent(
 *          ref="#/components/schemas/error"
 *      )
 *   )
 **/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;
use Atlas\Query\Update;
use App\Modules\registration\Controller\BaseRegistration;
use App\Controller\PermissionDeniedException;
use App\Controller\NotFoundException;
use App\Controller\ConflictException;

abstract class BaseTicket extends BaseRegistration
{

    public $id = 0;

    protected static $columnsToAttributes = [
    '"ticket"' => 'type',
    'RegistrationID' => 'id' ,
    'AccountID' => 'member',
    'BadgeDependentOnID' => 'badgeDependentOn',
    'BadgeName' => 'badgeName',
    'BadgesPickedUp' => 'badgesPickedUp',
    'BadgeTypeID' => 'ticketType',
    'EmergencyContact' => 'emergencyContact',
    'EventID' => 'event',
    'RegisteredByID' => 'registeredBy',
    'RegistrationDate' => 'registrationDate',
    'BoardingPassGenerated' => 'boardingPassGenerated',
    'PrintRequested' => 'printRequested' ,
    'LastPrintedDate' => 'lastPrintedDate',
    'PrintRequestIp' => 'printRequestIp',
    'Note' => 'note',
    'VoidDate' => 'voidDate',
    'VoidBy' => 'voidBy',
    'VoidReason' => 'voidReason'
    ];


    public function __construct(Container $container)
    {
        parent::__construct($container);

    }


    public function getAccount($id, Request $request, Response $response, $permission)
    {
        $user = $request->getAttribute('oauth2-token')['user_id'];
        $data = Select::new($this->container->db)
            ->columns('AccountID')
            ->from('Registrations')
            ->whereEquals(['RegistrationID' => $id])
            ->fetchOne();
        if (!$data) {
            throw new NotFoundException('Registration Not Found');
        }
        $aid = $data['AccountID'];

        if ($user != $aid && $permission &&
            !\ciab\RBAC::havePermission($permission)) {
            throw new PermissionDeniedException();
        }

        return $aid;

    }


    public function printBadge($request, $id)
    {
        $ip = \MyPDO::quote($_SERVER['REMOTE_ADDR']);
        Update::new($this->container->db)
            ->table('Registrations')
            ->columns(['PrintRequestIp' => $ip])
            ->set('PrintRequested', 'NOW()')
            ->whereEquals(['RegistrationID' => $id, 'VoidDate' => null])
            ->perform();

    }


    protected function updateTicket($request, $response, $params, $rbac, $db_request, $error, $getResult = true)
    {
        if ($rbac) {
            $this->checkPermissions([$rbac]);
        }
        $result = $db_request->perform();
        if ($result->rowCount() == 0) {
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


    protected function updateAndPrintTicket($request, $response, $params, $id, $rbac, $db_request, $error)
    {
        $this->getAccount($id, $request, $response, $rbac);

        if (!array_key_exists('id', $params)) {
            $params['id'] = $id;
        }
        $rc = $this->updateTicket(
            $request,
            $response,
            $params,
            null,
            $db_request,
            $error,
            false
        );

        if ($rc == null) {
            $this->printBadge($request, $id);
            return [null];
        }
        return $rc;

    }


    protected function verifyTicket($id)
    {
        $data = Select::new($this->container->db)
            ->columns('*')
            ->from('Registrations')
            ->whereEquals(['RegistrationID' => $id, 'VoidDate' => null])
            ->fetchOne();
        if (!$data) {
            throw new NotFoundException('Registration Not Found');
        }
        return $data;

    }


    /* End BaseTicket */
}
