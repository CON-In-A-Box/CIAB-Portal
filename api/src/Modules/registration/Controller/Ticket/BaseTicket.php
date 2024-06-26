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
 *      deprecated=true,
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"ticket"}
 *      ),
 *      @OA\Property(
 *          property="id",
 *          type="string",
 *      ),
 *      @OA\Property(
 *          property="badge_dependent_on",
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
 *          property="badge_id",
 *          type="string",
 *          description="The badge number of unique distinguishing ID"
 *      ),
 *      @OA\Property(
 *          property="badge_name",
 *          nullable=true,
 *          type="string",
 *      ),
 *      @OA\Property(
 *          property="badges_picked_up",
 *          type="string",
 *          nullable=true,
 *          description="The number of times this badge has been printed and picked up"
 *      ),
 *      @OA\Property(
 *          property="emergency_contact",
 *          nullable=true,
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
 *          property="registered_by",
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
 *          property="registration_date",
 *          type="string",
 *          format="date-time",
 *      ),
 *      @OA\Property(
 *          property="boarding_pass_generated",
 *          type="string",
 *          nullable=true,
 *          format="date-time",
 *      ),
 *      @OA\Property(
 *          property="print_requested",
 *          type="string",
 *          nullable=true,
 *          format="date-time",
 *      ),
 *      @OA\Property(
 *          property="last_printed_date",
 *          type="string",
 *          nullable=true,
 *          format="date-time",
 *      ),
 *      @OA\Property(
 *          property="print_request_ip",
 *          type="string",
 *          nullable=true,
 *          format="ip",
 *      ),
 *      @OA\Property(
 *          property="note",
 *          nullable=true,
 *          type="string",
 *      ),
 *      @OA\Property(
 *          property="void_date",
 *          type="string",
 *          nullable=true,
 *          format="date-time",
 *      ),
 *      @OA\Property(
 *          property="void_by",
 *          nullable=true,
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
 *          property="void_reason",
 *          nullable=true,
 *          type="string",
 *      ),
 *      @OA\Property(
 *          property="ticket_type",
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
 *      deprecated=true,
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"ticket_type"}
 *      ),
 *      @OA\Property(
 *          property="id",
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
 *          property="name",
 *          type="string",
 *      ),
 *      @OA\Property(
 *          property="avaliable_from",
 *          type="string",
 *          format="date-time",
 *      ),
 *      @OA\Property(
 *          property="avaliable_to",
 *          type="string",
 *          format="date-time",
 *      ),
 *      @OA\Property(
 *          property="cost",
 *          type="string",
 *      ),
 *      @OA\Property(
 *          property="background_image",
 *          nullable=true,
 *          type="string",
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="ticket_type_list",
 *      deprecated=true,
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
 *      deprecated=true,
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
 *  @OA\Schema(
 *      schema="print_job",
 *      deprecated=true,
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"print_job"}
 *      ),
 *      @OA\Property(
 *          property="id",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="method",
 *          type="string",
 *          enum={"claim"}
 *      ),
 *      @OA\Property(
 *          property="request",
 *          type="string",
 *          enum={"PUT"}
 *      ),
 *      @OA\Property(
 *          property="href",
 *          type="string"
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="print_queue",
 *      deprecated=true,
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"print_queue"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of queued print jobs",
 *          @OA\Items(
 *              ref="#/components/schemas/print_job"
 *          )
 *      )
 *  )
 *
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
use App\Error\PermissionDeniedException;
use App\Error\NotFoundException;
use App\Error\ConflictException;

abstract class BaseTicket extends BaseRegistration
{

    public $id = 0;

    protected static $columnsToAttributes = [
    '"ticket"' => 'type',
    'RegistrationID' => 'id' ,
    'AccountID' => 'member',
    'BadgeDependentOnID' => 'badge_dependent_on',
    'BadgeID' => 'badge_id',
    'BadgeName' => 'badge_name',
    'BadgesPickedUp' => 'badges_picked_up',
    'BadgeTypeID' => 'ticket_type',
    'EmergencyContact' => 'emergency_contact',
    'EventID' => 'event',
    'RegisteredByID' => 'registered_by',
    'RegistrationDate' => 'registration_date',
    'BoardingPassGenerated' => 'boarding_pass_generated',
    'PrintRequested' => 'print_requested' ,
    'LastPrintedDate' => 'last_printed_date',
    'PrintRequestIp' => 'print_request_ip',
    'Note' => 'note',
    'VoidDate' => 'void_date',
    'VoidBy' => 'void_by',
    'VoidReason' => 'void_reason'
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

        if ($user != $aid && $permission) {
            $this->checkPermissions([$permission]);
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


    protected function verifyBadgeId($reg_id, $badge_id, $event_id = null)
    {
        if ($event_id == null) {
            $data = Select::new($this->container->db)
                ->columns('EventID')
                ->from('Registrations')
                ->whereEquals(['RegistrationID' => $reg_id])
                ->fetchOne();
            if (!$data) {
                throw new NotFoundException('Registration Not Found');
            }
            $event_id = $data['EventID'];
        }
        $data = Select::new($this->container->db)
            ->columns('RegistrationID')
            ->from('Registrations')
            ->whereEquals(['EventID' => $event_id, 'BadgeID' => $badge_id])
            ->fetchOne();
        if (!empty($data) && $data['RegistrationID'] != $reg_id) {
            throw new ConflictException('Badge ID already assigned');
        }

    }


    /* End BaseTicket */
}
