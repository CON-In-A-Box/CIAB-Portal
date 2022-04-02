<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Container;
use Atlas\Query\Select;

class GetInformation extends BaseArtshow
{


    public function __construct(Container $container)
    {
        parent::__construct("artshow", $container);

    }


    public function buildResource(Request $request, Response $response, $args): array
    {
        $output = array();

        $target = new Configuration\GetConfiguration($this->container);
        $data = $target->buildResource($request, $response, [])[1];
        $options = $target->arrayResponse($request, $response, $data);
        foreach ($options as $value) {
            if (is_array($value)) {
                $output[$value['field']] = $value;
            }
        }

        $target = new \App\Controller\Event\GetEvent($this->container);
        $data = $target->buildResource($request, $response, ['id' => 'current'])[1];
        $event = $target->arrayResponse($request, $response, $data);

        $count = $output['Artshow_SelfRegistrationClose']['value'];
        $date = strtotime($event['date_from']." -$count days");
        $output['Artshow_OnlineCloses'] = [
        'value' => date("Y-m-d", $date)
        ];
        $date_now = new \DateTime();
        $date2 = new \DateTime(date('Y-m-d', $date));
        if ($date_now > $date2) {
            $output['Artshow_SelfRegistration']['value'] = '0';
        }

        $output['event'] = $event;

        $target = new Configuration\GetPaymentType($this->container);
        $data = $target->buildResource($request, $response, [])[1];
        $output['paymenttype'] = $target->arrayResponse($request, $response, $data);

        $target = new Configuration\GetPieceType($this->container);
        $data = $target->buildResource($request, $response, [])[1];
        $output['piecetype'] = $target->arrayResponse($request, $response, $data);

        $target = new Configuration\GetReturnMethod($this->container);
        $data = $target->buildResource($request, $response, [])[1];
        $output['returnmethod'] = $target->arrayResponse($request, $response, $data);

        $target = new Configuration\GetPriceType($this->container);
        $data = $target->buildResource($request, $response, [])[1];
        $output['pricetype'] = $target->arrayResponse($request, $response, $data);

        $target = new Configuration\GetRegistrationQuestion($this->container);
        $data = $target->buildResource($request, $response, [])[1];
        $output['registrationquestion'] = $target->arrayResponse($request, $response, $data);

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $output
        ];

    }


    /* end GetInformation */
}
