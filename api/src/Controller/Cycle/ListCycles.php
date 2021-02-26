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
        $includesDate = $request->getQueryParam('includesDate', null);

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
        if ($includesDate !== null) {
            $includesDate = strtotime($includesDate);
            if (!$includesDate) {
                throw new InvalidParameterException('\'includesDate\' parameter not valid');
            }
        }

        $condition = false;
        $sql = "SELECT * FROM `AnnualCycles`";
        if ($begin !== null) {
            $sql .= " WHERE `DateFrom` >= '".date("Y-m-d", $begin)."'";
            $condition = true;
        }
        if ($end !== null) {
            if (!$condition) {
                $sql .= " WHERE";
            } else {
                $sql .= " AND";
            }
            $sql .= " DateTo <= '".date("Y-m-d", $end)."'";
            $condition = true;
        }
        if ($includesDate !== null) {
            if (!$condition) {
                $sql .= " WHERE";
            } else {
                $sql .= " AND";
            }
            $target = date("Y-m-d", $includesDate);
            $sql .= " (DateFrom <= '$target' AND DateTo >= '$target')";
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
