<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 **/

namespace App\Modules\artshow\Controller\Sale;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\IncludeResource;
use App\Controller\NotFoundException;
use Atlas\Query\Select;
use Atlas\Query\Insert;
use Atlas\Query\Update;

class GetCustomerInvoice extends BaseSale
{

    use \App\Controller\TraitConfiguration;


    public function buildResource(Request $request, Response $response, $params): array
    {
        $format = $request->getQueryParam('format', 'pdf');

        $id = $params['id'];
        $output = ['type' => 'customer_invoice'];

        $output['customer'] = $this->getBuyer($request, $response, $id);

        $target = new \App\Modules\artshow\Controller\Sale\GetCustomerArtSales($this->container);
        try {
            $data = $target->buildResource($request, $response, ['id' => $id])[1];
            foreach ($data as $index => $entry) {
                $target->processIncludes($request, $response, $params, $data[$index]);
            }
            $output['art_sales'] = $data;
        } catch (\Exception $e) {
            $output['art_sales'] = [];
        }

        $target = new \App\Modules\artshow\Controller\Sale\GetCustomerPrintSales($this->container);
        try {
            $data = $target->buildResource($request, $response, ['id' => $id])[1];
            foreach ($data as $index => $entry) {
                $target->processIncludes($request, $response, $params, $data[$index]);
            }
            $output['print_sale'] = $data;
        } catch (\Exception $e) {
            $output['print_sale'] = [];
        }

        $target = new \App\Modules\artshow\Controller\Sale\GetCustomerPayment($this->container);
        try {
            $output['payment'] = $target->buildResource($request, $response, ['id' => $id])[1];
        } catch (\Exception $e) {
            $output['payment'] = [];
        }

        $eid = $this->getEventId($request);
        $invoice_id = 0;
        $invoice = Select::new($this->container->db)
            ->columns('InvoiceID')
            ->from('Artshow_Buyer_Invoice')
            ->whereEquals(['BuyerID' => $id, 'EventID' => $eid])
            ->fetchOne();
        if (empty($invoice)) {
            $insert = Insert::new($this->container->db)
                ->into('Artshow_Buyer_Invoice')
                ->columns([
                    'EventID' => $eid,
                    'BuyerID' => $id]);
            $insert->perform();
            $invoice_id = $insert->getLastInsertId();
        } else {
            Update::new($this->container->db)
                ->table('Artshow_Artist_Invoice')
                ->set('InvoiceGenerated', 'NOW()')
                ->whereEquals(['InvoiceID' => $invoice['InvoiceID']])
                ->perform();
            $invoice_id = $invoice['InvoiceID'];
        }
        $output['invoice'] = $invoice_id;

        if ($format == 'pdf') {
            $data = $this->getConfiguration([], 'Artshow_Configuration');
            foreach ($data as $entry) {
                $config[$entry['field']] = $entry['value'];
            }

            $invoice = new \App\Modules\artshow\Controller\CustomerInvoice($config, $output);
            $result = $invoice->buildInvoice();
        } else {
            $result = $output;
        }

        return [
        \App\Controller\BaseController::RESULT_TYPE,
        $result];

    }


    /* end GetCustomerInvoice */
}
