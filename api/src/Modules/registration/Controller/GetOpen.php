<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"registration"},
 *      path="/registration/open/{event}",
 *      summary="Returns if event registration is open or not..",
 *      @OA\Parameter(
 *          description="Event ID",
 *          in="path",
 *          name="event",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Event registration open status",
 *          @OA\JsonContent(
 *              @OA\Property(
 *                  property="type",
 *                  type="string",
 *                  enum={"registration"}
 *              ),
 *              @OA\Property(
 *                  property="event",
 *                  type="integer",
 *                  description="event Id"
 *              ),
 *              @OA\Property(
 *                  property="open",
 *                  type="boolean",
 *                  description="Is registration open"
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/event_not_found"
 *      )
 *  )
 *
 *  @OA\Get(
 *      tags={"registration"},
 *      path="/registration/open",
 *      summary="Returns if current event registration is open or not..",
 *      @OA\Response(
 *          response=200,
 *          description="Event registration open status",
 *          @OA\JsonContent(
 *              @OA\Property(
 *                  property="type",
 *                  type="string",
 *                  enum={"registration"}
 *              ),
 *              @OA\Property(
 *                  property="event",
 *                  type="integer",
 *                  description="event Id"
 *              ),
 *              @OA\Property(
 *                  property="open",
 *                  type="boolean",
 *                  description="Is registration open"
 *              )
 *          )
 *      )
 *  )
 **/

namespace App\Modules\registration\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\NotFoundException;

class GetOpen extends BaseRegistration
{

    use \App\Controller\TraitConfiguration;


    public function buildResource(Request $request, Response $response, $args): array
    {
        if (array_key_exists('event', $args)) {
            $event = $args['event'];
        } else {
            $event = 'current';
        }
        $event = $this->getEvent($event)['id'];

        if (!$event) {
            throw new NotFoundException('Event not found');
        }

        $sql = "SELECT * FROM `Events` WHERE `EventID` = $event";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetch();
        if (empty($data)) {
            throw new NotFoundException('Event not found');
        }

        $now = strtotime('now');
        $opentime = strtotime($data['DateFrom']);
        $closetime = strtotime($data['DateTo']);

        $open = (($opentime <= $now) && ($now <= $closetime));
        if ($open != false) {
            $data = $this->getConfiguration([], 'Registration_Configuration');
            $config = [];
            foreach ($data as $entry) {
                $config[$entry['field']] = $entry['value'];
            }
            $today = intval(date("H"));
            $open = ($config['ForceOpen']) || (
                    ($config['RegistrationOpen'] <= $today) &&
                    ($today < $config['RegistrationClose']));
        }

        $result = ['type' => 'registration',
        'event' => $event,
        'open' => $open];

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $result];

    }


    /* end GetOpen */
}
