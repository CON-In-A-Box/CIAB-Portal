<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 **/

namespace App\Modules\artshow\Controller\Artist;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\IncludeResource;
use App\Controller\NotFoundException;
use Atlas\Query\Select;
use Atlas\Query\Insert;
use Atlas\Query\Update;

class GetArtistInvoice extends BaseArtist
{

    use \App\Controller\TraitConfiguration;


    public function buildResource(Request $request, Response $response, $params): array
    {
        $format = $request->getQueryParam('format', 'pdf');
        $artist = Select::new($this->container->db)
            ->columns(...BaseArtist::selectMapping())
            ->from('Artshow_Artist')
            ->whereEquals(['ArtistID' => $params['artist']])
            ->fetchOne();
        if (empty($artist)) {
            throw new NotFoundException('Artist Not Found');
        }

        $data = $this->getConfiguration([], 'Artshow_Configuration');
        foreach ($data as $entry) {
            $config[$entry['field']] = $entry['value'];
        }

        $output = ['artist' => $artist];
        $target = new \App\Controller\Member\GetMember($this->container);
        $output['member'] = $target->buildResource($request, $response, ['id' => $artist['member']])[1];
        $target = new \App\Modules\artshow\Controller\Art\GetArt($this->container);
        try {
            $output['art'] = $target->buildResource($request, $response, ['artist' => $artist['id']])[1];
        } catch (\Exception $e) {
            $output['art'] = [];
        }
        $target = new \App\Modules\artshow\Controller\PrintArt\GetPrint($this->container);
        try {
            $output['print'] = $target->buildResource($request, $response, ['artist' => $artist['id']])[1];
        } catch (\Exception $e) {
            $output['print'] = [];
        }
        $target = new \App\Modules\artshow\Controller\Artist\GetArtistArtSales($this->container);
        try {
            $output['art_sales'] = $target->buildResource($request, $response, ['artist' => $artist['id']])[1];
        } catch (\Exception $e) {
            $output['art_sales'] = [];
        }
        $target = new \App\Modules\artshow\Controller\Artist\GetArtistPrintSales($this->container);
        try {
            $output['print_sales'] = $target->buildResource($request, $response, ['artist' => $artist['id']])[1];
        } catch (\Exception $e) {
            $output['print_sales'] = [];
        }
        $target = new \App\Modules\artshow\Controller\Artist\ListArtistDistribution($this->container);
        try {
            $output['distributions'] = $target->buildResource($request, $response, ['artist' => $artist['id']])[1];
        } catch (\Exception $e) {
            $output['distributions'] = [];
        }

        $eid = $this->getEventId($request);

        $invoice_id = 0;
        $invoice = Select::new($this->container->db)
            ->columns('InvoiceID')
            ->from('Artshow_Artist_Invoice')
            ->whereEquals(['ArtistID' => $params['artist'], 'EventID' => $eid])
            ->fetchOne();
        if (empty($invoice)) {
            $insert = Insert::new($this->container->db)
                ->into('Artshow_Artist_Invoice')
                ->columns([
                    'EventID' => $eid,
                    'ArtistID' => $params['artist']]);
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

        $invoice = new \App\Modules\artshow\Controller\ArtistInvoice($config, $output);
        if ($format == 'pdf') {
            $result = $invoice->buildInvoice();
        } else {
            $result = $output;
        }

        return [
        \App\Controller\BaseController::RESULT_TYPE,
        $result];

    }


    /* end GetArtistInvoice */
}
