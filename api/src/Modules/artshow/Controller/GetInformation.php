<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Container;

class GetInformation extends BaseArtshow
{

    use \App\Controller\TraitConfiguration;


    public function __construct(Container $container)
    {
        parent::__construct("artshow", $container);

    }


    public function buildResource(Request $request, Response $response, $args): array
    {
        $output = array();

        $config = $this->getConfiguration([], 'Artshow_Configuration');
        foreach ($config as $entry) {
            $output[$entry['field']] = $entry;
        }

        $sth = $this->container->db->prepare("SELECT * FROM `Artshow_PaymentType`");
        $sth->execute();
        $data = $sth->fetchAll();
        $value = array();
        foreach ($data as $entry) {
            $value[] = $entry['PaymentType'];
        }
        $output['PaymentType'] = [
        'type' => 'configuration_entry',
        'field' => 'PaymentType',
        'value' => $value
        ];

        $sth = $this->container->db->prepare("SELECT * FROM `Artshow_PieceType`");
        $sth->execute();
        $data = $sth->fetchAll();
        $value = array();
        foreach ($data as $entry) {
            $value[] = $entry['PieceType'];
        }
        $output['PieceType'] = [
        'type' => 'configuration_entry',
        'field' => 'PieceType',
        'value' => $value
        ];

        $sth = $this->container->db->prepare("SELECT * FROM `Artshow_ReturnMethod`");
        $sth->execute();
        $data = $sth->fetchAll();
        $value = array();
        foreach ($data as $entry) {
            $value[] = $entry['ReturnMethod'];
        }
        $output['ReturnMethod'] = [
        'type' => 'configuration_entry',
        'field' => 'ReturnMethod',
        'value' => $value
        ];

        $sth = $this->container->db->prepare("SELECT * FROM `Artshow_PriceType` ORDER BY `Position` ASC");
        $sth->execute();
        $data = $sth->fetchAll();
        $value = array();
        foreach ($data as $entry) {
            $value[] = $entry;
        }
        $output['PriceType'] = [
        'type' => 'configuration_entry',
        'field' => 'PriceType',
        'value' => $value
        ];


        $sth = $this->container->db->prepare("SELECT * FROM `Artshow_RegistrationQuestion`");
        $sth->execute();
        $data = $sth->fetchAll();
        $value = array();
        foreach ($data as $entry) {
            $value[] = $entry;
        }
        $output['RegistrationQuestion'] = [
        'type' => 'configuration_entry',
        'field' => 'RegistrationQuestion',
        'value' => $value
        ];

        return [
        \App\Controller\BaseController::LIST_TYPE,
        $output,
        array('type' => 'configuration_list')];

    }


    /* end GetInformation */
}
