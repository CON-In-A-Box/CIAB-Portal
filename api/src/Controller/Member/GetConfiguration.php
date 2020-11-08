<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;

class GetConfiguration extends BaseMember
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $data = $this->findMember($request, $response, $args, 'name');
        if (gettype($data) === 'object') {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $data];
        }
        $user = $request->getAttribute('oauth2-token')['user_id'];
        if ($user != $data['id'] && !\ciab\RBAC::havePermission("api.get.configuration")) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }

        if (array_key_exists('key', $args)) {
            $target = "AND cf.Field = '{$args['key']}'";
        } else {
            $target = '';
        }
        $sql = <<<SQL
            SELECT
                cf.*,
                a.Value as Value
            FROM
                `ConfigurationField` cf
            LEFT JOIN `AccountConfiguration` a ON
                a.Field = cf.Field AND a.AccountId = {$data['id']}
            WHERE
                cf.TargetTable = 'AccountConfiguration'
                $target
SQL;
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        $output = [];
        $output['type'] = 'configuration_list';
        $result = [];
        foreach ($data as $entry) {
            if ($entry['Value'] === null) {
                $value = $entry['InitialValue'];
            } else {
                $value = $entry['Value'];
            }
            $options = null;
            if ($entry['Type'] == 'select') {
                $options = [];
                $sql = "SELECT Name FROM `ConfigurationOption` WHERE Field = '{$entry['Field']}'";
                $sth = $this->container->db->prepare($sql);
                $sth->execute();
                $opts = $sth->fetchAll();
                foreach ($opts as $o) {
                    $options[] = $o['Name'];
                }
            }
            $result[] = [
            'type' => 'configuration_entry',
            'field' => $entry['Field'],
            'fieldType' => $entry['Type'],
            'value' => $value,
            'description' => $entry['Description'],
            'options' => $options
            ];
        }

        if (count($result) > 1) {
            return [
            \App\Controller\BaseController::LIST_TYPE,
            $result,
            $output
            ];
        } else {
            $result[0]['account'] = $user;
            $result[0]['type'] = 'configuration';
            return [
            \App\Controller\BaseController::RESOURCE_TYPE,
            $result[0]
            ];
        }

    }


    /* end GetConfiguration */
}
