<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Cycle;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\InvalidParameterException;

class ListCycles extends BaseCycle
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $begin = $request->getQueryParam('begin', null);
        $end = $request->getQueryParam('end', null);

        if ($begin !== null) {
            $begin = strtotime($begin);
            if (!$begin) {
                throw new InvalidParameterException('\'begin\' parameter not valid');
            }
        }
        if ($end !== null) {
            $end = strtotime($end);
            if (!$end) {
                throw new InvalidParameterException('\'end\' parameter not valid');
            }
        }

        $sql = "SELECT * FROM `AnnualCycles`";
        if ($begin !== null) {
            $sql .= " WHERE `DateFrom` >= '".date("Y-m-d", $begin)."'";
        }
        if ($end !== null) {
            if ($begin === null) {
                $sql .= " WHERE";
            } else {
                $sql .= " AND";
            }
            $sql .= " DateTo <= '".date("Y-m-d", $end)."'";
        }
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $cycles = $sth->fetchAll();
        $output = array();
        $output['type'] = 'cycle_list';
        $data = array();
        foreach ($cycles as $entry) {
            $entry['type'] = 'cycle';
            $entry['id'] = $entry['AnnualCycleID'];
            unset($entry['AnnualCycleID']);
            $data[] = $entry;
        }
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        $output];

    }


    /* end ListCycles */
}
