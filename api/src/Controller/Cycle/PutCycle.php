<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Cycle;

use Slim\Http\Request;
use Slim\Http\Response;

class PutCycle extends BaseCycle
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $error = $this->getCycle($cycles, $params);
        if ($error) {
            return $error;
        }

        $permissions = ['api.put.cycle'];
        $this->checkPermissions($permissions);

        $sql = "UPDATE `AnnualCycles` SET ";

        $body = $request->getParsedBody();
        $changes = array();

        if (array_key_exists('From', $body)) {
            $from = date_format(new \DateTime($body['From']), 'Y-m-d');
            $changes[] = "`DateFrom` = '$from'";
        }

        if (array_key_exists('To', $body)) {
            $to = date_format(new \DateTime($body['To']), 'Y-m-d');
            $changes[] = "`DateTo` = '$to'";
        }

        if (count($changes) > 0) {
            $sql .= implode(',', $changes);
            $sql .= " WHERE `AnnualCycleID` = '".$params['id']."';";

            print $sql;
            $sth = $this->container->db->prepare($sql);
            $sth->execute();
        }
        return [null];

    }


    /* end PutCycle */
}
