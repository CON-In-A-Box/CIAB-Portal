<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller\Ticket;

require_once __DIR__.'/../../../../../../backends/email.inc';

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views;

class EmailTicket extends BaseTicket
{


    use \App\Controller\TraitConfiguration;


    public function buildResource(Request $request, Response $response, $params): array
    {
        $id = $params['id'];
        $aid = $this->getAccount($id, $request, $response, 'api.registration.ticket.email');
        if (is_array($aid)) {
            return $aid;
        }

        $data = $this->getConfiguration([], 'Registration_Configuration');
        $config = [];
        foreach ($data as $entry) {
            $config[$entry['field']] = $entry['value'];
        }

        $target = new GetTicket($this->container);
        $output = $target->buildResource($request, $response, $params);
        $newdata = $output[1];
        if ($newdata['type'] == 'error') {
            return $output;
        }
        $target->processIncludes($request, $response, $params, ['member', 'event', 'ticketType'], $newdata);
        $data = $target->arrayResponse($request, $response, $newdata);

        $subject = $data['event']['name'].' Boarding Pass';
        $phpView = new Views\PhpRenderer(__DIR__.'/../../Templates', [
            'name' => $data['member']['firstName'],
            'event' => $data['event']['name'],
            'badgeNumber' => $data['member']['id'],
            'badgeType' => $data['ticketType']['Name'],
            'fullName' => $data['member']['legalFirstName'].' '.$data['member']['legalLastName'],
            'instructions' => $config['passInstructions'],
        ]);
        $phpView->render($response, 'emailBoardingPass.phtml');
        $response->getBody()->rewind();
        $message = $response->getBody()->getContents();
        \ciab\Email::mail($data['member']['email'], \getNoReplyAddress(), $subject, $message, null, 'text/html');

        return [null];

    }


    /* end EmailTicket */
}
