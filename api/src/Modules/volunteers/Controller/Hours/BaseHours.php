<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Tag(
 *      name="volunteers",
 *      description="Features around volunteers at an event"
 *  )
 *
 *  @OA\Schema(
 *      schema="volunteer_hour_entry",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"volunteer_hour_entry"}
 *      ),
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *      ),
 *      @OA\Property(
 *          property="member",
 *          description="Member badge who volunteered",
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
 *          property="department",
 *          description="The department of the staff position.",
 *          oneOf={
 *              @OA\Schema(
 *                  ref="#/components/schemas/department"
 *              ),
 *              @OA\Schema(
 *                  type="integer",
 *                  description="departemnt Id"
 *              )
 *          }
 *      ),
 *      @OA\Property(
 *          property="hours",
 *          type="number",
 *          description="Actual time in entry."
 *      ),
 *      @OA\Property(
 *          property="authorizer",
 *          description="Member authorizing the entry",
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
 *          property="end",
 *          type="string",
 *          format="date-time",
 *          description="Time entry ended"
 *      ),
 *      @OA\Property(
 *          property="enterer",
 *          description="Member entering the entry",
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
 *          property="modifier",
 *          type="number",
 *          description="Time Modifier."
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="volunteer_hour_summary",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"volunteer_hour_summary_entry"}
 *      ),
 *      oneOf={
 *          @OA\Property(
 *              property="member",
 *              description="Member badge who volunteered",
 *              oneOf={
 *                  @OA\Schema(
 *                      ref="#/components/schemas/member"
 *                  ),
 *                  @OA\Schema(
 *                      type="integer",
 *                      description="Member Id"
 *                  )
 *              }
 *          ),
 *          @OA\Property(
 *              property="department",
 *              description="The department of the staff position.",
 *              oneOf={
 *                  @OA\Schema(
 *                      ref="#/components/schemas/department"
 *                  ),
 *                  @OA\Schema(
 *                      type="integer",
 *                      description="departemnt Id"
 *                  )
 *              }
 *          ),
 *      },
 *      @OA\Property(
 *          property="entry_count",
 *          type="number"
 *      ),
 *      @OA\Property(
 *          property="total_hours",
 *          type="number"
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="volunteer_hour_entry_list",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"volunteer_hour_entry_list"}
 *      ),
 *      @OA\Property(
 *          property="total_hours",
 *          type="in",
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of volunteer hour entries",
 *          @OA\Items(
 *              ref="#/components/schemas/volunteer_hour_entry"
 *          )
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="volunteer_hour_summary_list",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"volunteer_hour_summary_list"}
 *      ),
 *      @OA\Property(
 *          property="total_hours",
 *          type="in",
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of volunteer hour summaries",
 *          @OA\Items(
 *              ref="#/components/schemas/volunteer_hour_summary"
 *          )
 *      )
 *  )
 *
 *   @OA\Response(
 *      response="volunteer_entry_not_found",
 *      description="Volunteer entry not found in the system.",
 *      @OA\JsonContent(
 *          ref="#/components/schemas/error"
 *      )
 *   )
 **/

namespace App\Modules\volunteers\Controller\Hours;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;
use App\Controller\BaseController;
use App\Controller\IncludeResource;

abstract class BaseHours extends BaseController
{
    use \App\Controller\TraitScope;

    protected static $columnsToAttributes = [
        '"volunteer_hour_entry"' => 'type',
        'HourEntryID' => 'id',
        'AccountID' => 'member',
        'DepartmentID' => 'department',
        'ActualHours' => 'hours',
        'AuthorizedByID' => 'authorizer',
        'EndDateTime' => 'end',
        'EnteredByID' => 'enterer',
        'EventID' => 'event',
        'TimeModifier' => 'modifier'
    ];


    public function __construct(Container $container)
    {
        parent::__construct('volunteer_hour_entry', $container);

        $this->includes = [
        new IncludeResource('\App\Controller\Member\GetMember', 'id', 'member'),
        new IncludeResource('\App\Controller\Department\GetDepartment', 'name', 'department'),
        new IncludeResource('\App\Controller\Member\GetMember', 'id', 'authorizer'),
        new IncludeResource('\App\Controller\Member\GetMember', 'id', 'enterer'),
        new IncludeResource('\App\Controller\Event\GetEvent', 'id', 'event')
        ];

    }


    protected function checkOverlap($memberId, $endtime, $hours, $event = null)
    {
        if ($event === null) {
            $event = $this->getEventId();
        }

        $data = Select::new($this->container->db)
        ->columns('`HourEntryID` AS id')
        ->columns('EndDateTime')
        ->columns("SUBTIME(`EndDateTime`, SEC_TO_TIME(`ActualHours` * 3600)) AS StartTime")
        ->columns("SUBTIME('$endtime', SEC_TO_TIME($hours * 3600)) AS CheckStartTime")
        ->from('VolunteerHours')
        ->whereEquals(['AccountID' => $memberId])
        ->having("(`CheckStartTime` <= `StartTime` AND `StartTime` <= '$endtime')")
        ->orHaving("(`StartTime` <= '$endtime' AND '$endtime' <= `EndDateTime`)")
        ->fetchOne();

        if (empty($data)) {
            return null;
        }
        return $data['id'];

    }


    /* END BaseHours */
}
