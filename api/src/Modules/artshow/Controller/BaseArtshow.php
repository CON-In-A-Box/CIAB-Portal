<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Tag(
 *      name="artshow",
 *      description="Features around event art show"
 *  )
 *
 *  @OA\Parameter(
 *      parameter="target_event",
 *      description="Event being targeted",
 *      in="query",
 *      name="event",
 *      required=false,
 *      style="form",
 *      @OA\Schema(type="integer")
 *  )
 *
 *  @OA\Schema(
 *      schema="BaseArtshow"
 *  )
 **/

namespace App\Modules\artshow\Controller;

use Slim\Container;
use Slim\Http\Request;
use App\Controller\BaseController;
use App\Controller\NotFoundException;
use App\Controller\InvalidParameterException;

abstract class BaseArtshow extends BaseController
{

    use \App\Controller\TraitConfiguration;

    private $config;


    public function __construct(string $api_type, Container $container)
    {
        parent::__construct($api_type, $container);
        $this->config = null;

    }


    public static function install($container): void
    {

    }


    public static function permissions($database): ?array
    {
        $permissions = ['api.post.artshow.customer',
            'api.artshow.printTags',
            'api.post.artshow.artist',
            'api.post.artshow.distribution',
            'api.post.artshow.sale',
            'api.post.artshow.payment',
            'api.update.artshow.customer',
            'api.update.artshow.distribution',
            'api.update.artshow.sale',
            'api.update.artshow.payment',
            "api.update.artshow.artist",
            "api.update.artshow.print",
            "api.update.artshow.show",
            "api.update.artshow.art",
            'api.set.artshow.configuration',
            'api.get.configuration',
            "api.delete.artshow.print",
            "api.delete.artshow.art",
            "api.add.artshow.print",
            "api.add.artshow.show",
            "api.add.artshow.art",
        ];

        return $permissions;

    }


    protected function getEventId(Request $request)
    {
        $event = $request->getQueryParam('event', 'current');
        return $this->getEvent($event)['id'];

    }


    protected function checkParameter($request, $response, $source, $field)
    {
        if ($source == null || !array_key_exists($field, $source)) {
            throw new NotFoundException("Required '$field' parameter not present");
        }

    }


    protected function checkParameters($request, $response, $source, $fields)
    {
        if (is_array($fields)) {
            foreach ($fields as $field) {
                $this->checkParameter($request, $response, $source, $field);
            }
        } else {
            $this->checkParameter($source, $fields);
        }

    }


    protected function onlineCheckinOpen($request, $response)
    {
        if ($this->config == null) {
            $this->config = array();
            $target = new Configuration\GetConfiguration($this->container);
            $data = $target->buildResource($request, $response, [])[1];
            $this->config = $target->arrayResponse($request, $response, $data);
            foreach ($this->config as $value) {
                if (is_array($value)) {
                    $this->config[$value['field']] = $value;
                }
            }
        }

        $target = new \App\Controller\Event\GetEvent($this->container);
        $data = $target->buildResource($request, $response, ['id' => 'current'])[1];
        $event = $target->arrayResponse($request, $response, $data);

        $count = intval($this->config['Artshow_SelfRegistrationClose']['value']);
        if ($count > 0) {
            $date = new \DateTime($event['date_from']." -$count days");
            $date_now = new \DateTime();
            return ($date_now < $date);
        }
        return true;

    }


    protected function executePut($request, $response, $statement)
    {
        $result = $statement->perform();
        if ($result->rowCount() == 0) {
            throw new InvalidParameterException('Put Failed');
        }

        return [null];

    }


    protected function getBuyer($request, $response, $id)
    {
        if ($this->config == null) {
            $this->config = array();
            $target = new Configuration\GetConfiguration($this->container);
            $data = $target->buildResource($request, $response, [])[1];
            $this->config = $target->arrayResponse($request, $response, $data);
            foreach ($this->config as $value) {
                if (is_array($value)) {
                    $this->config[$value['field']] = $value;
                }
            }
        }

        if (boolval($this->config['Artshow_LinkBuyers']['value'])) {
            return $this->getMember($request, $id);
        }

        $target = new \App\Modules\artshow\Controller\Customer\GetCustomer($this->container);
        $data = $target->buildResource($request, $response, ['id' => $id])[1];
        return array($data);

    }


    /* End BaseArtshow */
}
