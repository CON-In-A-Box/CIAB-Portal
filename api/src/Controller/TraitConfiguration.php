<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\NotFoundException;

trait TraitConfiguration
{


    private function getConfiguration($args, $table, $extendCondition = '', $extendSQL = ''): array
    {
        if (array_key_exists('key', $args)) {
            $target = "AND cf.Field = '{$args['key']}'";
        } else {
            $target = '';
        }
        $sql = <<<SQL
            SELECT
                cf.*,
                (
                    CASE WHEN a.Value IS NULL THEN cf.InitialValue ELSE a.Value
                    END
                ) AS Value
            FROM
                `ConfigurationField` cf
            LEFT JOIN `$table` a ON
                a.Field = cf.Field $extendCondition
            WHERE
                cf.TargetTable = '$table'
                $target
            $extendSQL
            ORDER BY Field;
SQL;
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        $result = [];
        foreach ($data as $entry) {
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
            'value' => $entry['Value'],
            'description' => $entry['Description'],
            'options' => $options
            ];
        }

        return $result;

    }


    private static function checkBool($value)
    {
        return (int) filter_var($value, FILTER_VALIDATE_BOOLEAN);

    }


    private static function checkInt($value)
    {
        return (int) filter_var($value, FILTER_VALIDATE_INT);

    }


    private function checkSelect($value, $field)
    {
        $sql = "SELECT * FROM `ConfigurationOption` WHERE Field = '$field' AND Name = '$value';";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        if ($sth->fetch() === false) {
            return null;
        }
        return $value;

    }


    private function verifyValue($value, $field)
    {
        $sql = "SELECT Type FROM `ConfigurationField` WHERE Field = '$field'";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        if ($data === false) {
            return $value;
        }
        switch ($data[0]['Type']) {
            case 'boolean':
                return $this->checkBool($value);
            case 'integer':
                return $this->checkInt($value);
            case 'select':
                return $this->checkSelect($value, $field);
            default:
                return $value;
        }

    }


    private function putConfiguration(Request $request, Response $response, $args, $table, $data): array
    {
        if (!array_key_exists('Value', $data) || !array_key_exists('Field', $data)) {
            throw new NotFoundException('Value Not Found');
        }

        $field = $data['Field'];
        $data['Value'] = $this->verifyValue($data['Value'], $data['Field']);

        $columns = implode(',', array_keys($data));
        $values = "'".implode('\', \'', array_values($data))."'";
        $value = $data['Value'];

        $sql = <<<SQL
            INSERT INTO `$table` ($columns)
            VALUES ($values)
            ON DUPLICATE KEY UPDATE
                Value = '$value';
SQL;
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

        $target = new \App\Controller\Member\GetConfiguration($this->container);

        $args['key'] = $data['Field'];
        $data = $target->buildResource($request, $response, $args)[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data)
        ];

    }


    /* end TraitConfiguration */
}
