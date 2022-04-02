<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *
 **/

namespace App\Modules\artshow\Controller\Artist;

use Slim\Http\Request;
use Slim\Http\Response;

class GetArtistSalesSummary extends BaseArtist
{

    use \App\Controller\TraitConfiguration;


    public function buildResource(Request $request, Response $response, $params): array
    {
        $artist = $params['artist'];

        $data = $this->getConfiguration([], 'Artshow_Configuration');
        foreach ($data as $entry) {
            $config[$entry['field']] = $entry['value'];
        }

        $target = new \App\Modules\artshow\Controller\Artist\GetArtist($this->container);
        $data = $target->buildResource($request, $response, ['artist' => $artist])[1];

        $output = [
        'id' => $data['id'],
        'type' => 'artist_summary'
        ];

        $output['hung_count'] = 0;
        $output['hung_hanging_fee'] = 0;
        try {
            $target = new \App\Modules\artshow\Controller\Art\GetArt($this->container);
            $data = $target->buildResource($request, $response, ['artist' => $artist])[1];
            if ($data) {
                $output['hung_count'] = count($data);
                $output['hung_hanging_fee'] = 0;
                foreach ($data as $piece) {
                    if ($piece['not_for_sale']) {
                        $output['hung_hanging_fee'] += intval($config['Artshow_NFSHangingFee']);
                    } else {
                        $output['hung_hanging_fee'] += intval($config['Artshow_HangingFee']);
                    }
                }
            }
        } catch (\Exception $e) {
        }

        $output['hung_sale_count'] = 0;
        $output['hung_sale_total'] = 0;
        $output['hung_commission'] = 0;
        try {
            $target = new \App\Modules\artshow\Controller\Artist\GetArtistArtSales($this->container);
            $data = $target->buildResource($request, $response, ['artist' => $artist])[1];
            if ($data) {
                $output['hung_sale_count'] = count($data);
                $output['hung_sale_total'] = 0;
                foreach ($data as $sale) {
                    $output['hung_sale_total'] += intval($sale['price']);
                }
                $com_rate = (intval($config['Artshow_DisplayComission']) / 100.0);
                $output['hung_commission'] = $output['hung_sale_total'] * $com_rate;
            }
        } catch (\Exception $e) {
        }
        $output['hung_sales_net'] = $output['hung_sale_total'] - $output['hung_commission'];

        $output['print_lot_count'] = 0;
        try {
            $target = new \App\Modules\artshow\Controller\PrintArt\GetPrint($this->container);
            $data = $target->buildResource($request, $response, ['artist' => $artist])[1];
            if ($data) {
                $output['print_lot_count'] = count($data);
            }
        } catch (\Exception $e) {
        }

        $output['print_sale_count'] = 0;
        $output['print_sale_total'] = 0;
        $output['print_commission'] = 0;
        try {
            $target = new \App\Modules\artshow\Controller\Artist\GetArtistPrintSales($this->container);
            $data = $target->buildResource($request, $response, ['artist' => $artist])[1];
            if ($data) {
                $output['print_sale_count'] = count($data);
                $output['print_sale_total'] = 0;
                foreach ($data as $sale) {
                    $output['print_sale_total'] += intval($sale['price']);
                }
                $com_rate = (intval($config['Artshow_PrintShopComission']) / 100.0);
                $output['print_commission'] = $output['print_sale_total'] * $com_rate;
            }
        } catch (\Exception $e) {
        }
        $output['print_sales_net'] = $output['print_sale_total'] - $output['print_commission'];

        $output['distribution_count'] = 0;
        $output['distribution_total'] = 0;
        try {
            $target = new \App\Modules\artshow\Controller\Artist\ListArtistDistribution($this->container);
            $data = $target->buildResource($request, $response, ['artist' => $artist])[1];
            if ($data) {
                $output['distribution_count'] = count($data);
                $output['distribution_total'] = 0;
                foreach ($data as $pay) {
                    $output['distribution_total'] += floatval($pay['amount']);
                }
            }
        } catch (\Exception $e) {
        }

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $output];

    }


    /* end GetArtistSalesSummary */
}
