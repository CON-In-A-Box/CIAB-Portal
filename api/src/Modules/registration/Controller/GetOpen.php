<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

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
            $event = \current_eventID();
        }

        if (!$event) {
            throw new NotFoundException('Event not found');
        }

        $sql = "SELECT * FROM `Events` WHERE NOW() BETWEEN `dateFrom` AND `dateTo` AND `EventID` = $event";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        $open = ($data != false);
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
