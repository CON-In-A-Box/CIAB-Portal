<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\InvalidParameterException;
use App\Controller\NotFoundException;
use App\Controller\ConflictException;

trait TraitConfiguration
{


    private function checkConfigValue($table, $field): bool
    {
        $sql = <<<SQL
SELECT
    (SELECT COUNT(*) FROM `$table` WHERE `Field` = '$field') AS f1,
    (SELECT COUNT(*) FROM `ConfigurationField` WHERE `TargetTable` = '$table' AND `Field` = '$field') AS f2
SQL;
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetch();
        return intval($data['f1']) || intval($data['f2']);

    }


    private function getConfiguration($args, $table, $extendCondition = '', $extendSQL = ''): array
    {
        if (array_key_exists('key', $args)) {
            if (!$this->checkConfigValue($table, $args['key'])) {
                throw new NotFoundException("Field '${args['key']}' not present in '$table'");
            }
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
                ) AS `Value`
            FROM
                `ConfigurationField` cf
            LEFT JOIN `$table` a ON
                a.Field = cf.Field $extendCondition
            WHERE
                cf.TargetTable = '$table'
                $target
            $extendSQL
            ORDER BY `Field`;
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
        if ($data === false || empty($data)) {
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


    private function putConfiguration(Request $request, Response $response, $args, $table, $data)
    {
        if (!array_key_exists('Value', $data)) {
            throw new InvalidParameterException('No \'Value\' parameter present');
        }
        if (!array_key_exists('Field', $data)) {
            throw new InvalidParameterException('No \'Field\' parameter present');
        }

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
        try {
            $sth->execute();
        } catch (\Exception $e) {
            throw new ConflictException('Failed to update configuration.');
        }

    }


    /* end TraitConfiguration */
}
