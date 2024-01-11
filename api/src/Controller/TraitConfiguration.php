<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;
use Atlas\Query\Insert;
use Atlas\Query\Update;
use App\Error\InvalidParameterException;
use App\Error\NotFoundException;
use App\Error\ConflictException;

trait TraitConfiguration
{


    private function checkConfigValue($table, $field): bool
    {
        $select = Select::new($this->container->db);
        $select->columns(
            $select->subselect()->columns('COUNT(*)')->from($table)->whereEquals(['Field' => $field])->as('f1')->getStatement()
        );
        $select->columns(
            $select->subselect()->columns('COUNT(*)')->from('ConfigurationField')->whereEquals(['TargetTable' => $table, 'Field' => $field])->as('f2')->getStatement()
        );
        $data = $select->fetchOne();
        return intval($data['f1']) || intval($data['f2']);

    }


    private function getConfiguration($params, $table, $condition = null): array
    {
        $select = Select::new($this->container->db);
        $select->columns('cf.*');
        $select->columns('(CASE WHEN a.Value IS NULL THEN cf.InitialValue ELSE a.Value END) AS `Value`');
        $select->from('ConfigurationField cf');
        if ($condition === null) {
            $select->join('LEFT', "$table a", 'a.Field = cf.Field');
        } else {
            $select->join('LEFT', "$table a", $condition.' AND a.Field = cf.Field');
        }
        $select->whereEquals(['cf.TargetTable' => $table]);
        if (array_key_exists('key', $params)) {
            if (!$this->checkConfigValue($table, $params['key'])) {
                throw new NotFoundException("Field '${params['key']}' not present in '$table'");
            }
            $select->whereEquals(['cf.Field' => $params['key']]);
        }

        if (method_exists($this, 'buildExtendedConfQuery')) {
            $this->buildExtendedConfQuery($select, $params);
        }

        $select->orderBy('Field');
        $data = $select->fetchAll();
        $result = [];
        foreach ($data as $entry) {
            $options = null;
            if ($entry['Type'] == 'select') {
                $options = [];
                $subsel = Select::new($this->container->db);
                $subsel->columns('Name')->from('ConfigurationOption')->whereEquals(['Field' => $entry['Field']]);
                $opts = $subsel->fetchAll();
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
        $select = Select::new($this->container->db);
        $select->columns('*')->from('ConfigurationOption')->whereEquals(['Field' => $field, 'Name' => $value]);
        if ($select->fetchOne() === null) {
            return null;
        }
        return $value;

    }


    private function verifyValue($value, $field)
    {
        $select = Select::new($this->container->db);
        $select->columns('Type')->from('ConfigurationField')->whereEquals(['Field' => $field]);
        $data = $select->fetchOne();
        if ($data === false || empty($data)) {
            return $value;
        }
        switch ($data['Type']) {
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


    private function putConfiguration(Request $request, Response $response, $params, $table, $data)
    {
        if (empty($data)) {
            throw new InvalidParameterException('No update parameter present');
        }
        if (!array_key_exists('Value', $data)) {
            throw new InvalidParameterException('No \'Value\' parameter present');
        }
        if (!array_key_exists('Field', $data)) {
            throw new InvalidParameterException('No \'Field\' parameter present');
        }

        $data['Value'] = $this->verifyValue($data['Value'], $data['Field']);

        $select = Select::new($this->container->db);
        $select->columns('Field')->from($table)->whereEquals(['Field' => $data['Field']]);
        $exists = $select->fetchOne();
        if ($exists === null) {
            $action = Insert::new($this->container->db);
            $action->into($table);
        } else {
            $action = Update::new($this->container->db);
            $action->table($table);
            $action->whereEquals(['Field' => $data['Field']]);
        }
        $action->columns($data);
        try {
            $action->perform();
        } catch (\Exception $e) {
            throw new ConflictException('Failed to update configuration.');
        }

    }


    /* end TraitConfiguration */
}
