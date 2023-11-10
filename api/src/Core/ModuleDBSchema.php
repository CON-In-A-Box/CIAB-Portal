<?php

/*.
    require_module 'standard';
    require_module 'json';
.*/


namespace App\Core;

use Atlas\Query\Select;
use Atlas\Query\Update;
use Atlas\Query\Insert;
use Atlas\Info\Info;
use Atlas\Pdo\Connection;

abstract class ModuleDBSchema
{

    /**
     * @var array
     */
    private $schema;

    /**
     * @var array
     */
    private $seed;

    /**
     * @var string
     */
    private $field = null;

    private $database = null;


    public function __construct(
        /*.string.*/$field,
        $database,
        /*.array?.*/$tables = null,
        /*.array?.*/$foreignKeys = null,
        /*.array?.*/$primaryKeys = null,
        /*.array?.*/$index = null,
        /*.array?.*/$seed = null
    ) {
        $this->field = $field.'.DBMD5';
        $this->database = $database;
        $this->schema = array();
        $this->schema['tables'] = $tables;
        $this->schema['foreignKeys'] = $foreignKeys;
        $this->schema['primaryKeys'] = $primaryKeys;
        $this->schema['index'] = $index;
        $this->seed = $seed;

    }


    public function update($force = false)
    {
        $oldMD5 = $this->getRecordedDBSchemaMD5();
        $currentMD5 = md5(json_encode($this->schema));

        if ($force || $oldMD5 != $currentMD5) {
            $this->buildMissingTables($force);

            if ($oldMD5 !== false) {
                Update::new($this->database)
                    ->table('Configuration')
                    ->columns(['Value' => $currentMD5])
                    ->whereEquals(['Field' => $this->field])
                    ->perform();
            } else {
                Insert::new($this->database)
                    ->into('Configuration')
                    ->columns(['Value' => $currentMD5,
                        'Field' => $this->field])
                        ->perform();
            }
        }

    }


    public function addTables(array $tables)
    {
        $this->schema['tables'] = array_merge($this->schema['tables'], $tables);

    }


    public function addForeignKeys(array $fks)
    {
        $this->schema['foreignKeys'] = array_merge($this->schema['foreignKeys'], $fks);

    }


    public function addPrimaryKeys(array $pks)
    {
        $this->schema['primaryKeys'] = array_merge($this->schema['primaryKeys'], $pks);

    }


    public function addIndexes(array $indexes)
    {
        $this->schema['index'] = array_merge($this->schema['index'], $indexes);

    }


    private function getRecordedDBSchemaMD5()
    {
        $value = Select::new($this->database)
            ->from('Configuration')
            ->columns('Value')
            ->whereEquals(['Field' => $this->field])
            ->fetchOne();
        if ($value === null) {
            return false;
        } else {
            return $value['Value'];
        }

    }


    private function buildMissingTables($force = false)
    {
        // Capture a list of tables already created
        $connection = Connection::new($this->database);
        $info = Info::new($connection);
        $arr = $info->fetchTableNames();

        $initialize = [];
        // Verify every table we need exists, if not, create.
        foreach ($this->schema['tables'] as $table => $fields) {
            if (in_array($table, $arr)) {
                // Table exists
                // Capture a list of fields already in the table
                $f_arr = array_keys($info->fetchColumns($table));

                // Check the list of fields to make sure we are set
                foreach ($fields as $column => $settings) {
                    if (! in_array($column, $f_arr)) {
                        // Missing field, alter-add it
                        $build = "ALTER TABLE ".$table;
                        $build .= " ADD ".$column." ".$settings;
                        $this->database->query($build);
                    }
                }
            } else {
                // Missing a table, build it!
                $build = 'CREATE TABLE '.$table.' (';
                foreach ($fields as $column => $setting) {
                    $build .= $column.' '.$setting.', ';
                }
                $build = rtrim($build, ', ').");";
                $this->database->query($build);
                $initialize[] = $table;
            }
        }

        if ($this->schema['primaryKeys']) {
            //Verify primary keys
            foreach ($this->schema['primaryKeys'] as $table => $keys) {
                $query = "SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY';";
                $result = $this->database->query($query);
                $value = $result->fetch();
                if ($value === false) {
                    $build = "ALTER TABLE `".$table."` ADD PRIMARY KEY (".implode($keys, ', ').");";
                    $result = $this->database->query($build);
                }
            }
        }

        // Capture a list of all Constraints (Foreign Keys)
        $build = "SELECT TABLE_NAME, COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE";
        $build .= " WHERE TABLE_SCHEMA = '".$_ENV['DBNAME']."' AND CONSTRAINT_NAME <> 'PRIMARY';";
        $result = $this->database->query($build);
        $arr = [];
        foreach ($result as $value) {
            $arr[] = $value['TABLE_NAME'].":".$value['COLUMN_NAME'];
        }
        if ($this->schema['foreignKeys']) {
            // Verify every foreign key we need exists, if not, create.
            foreach ($this->schema['foreignKeys'] as $table => $fields) {
                foreach ($fields as $column => $referto) {
                    $lookfor = $table.":".$column;
                    if (!in_array($lookfor, $arr)) {
                        $build = "ALTER TABLE ".$table;
                        $build .= " ADD FOREIGN KEY (".$column.") REFERENCES ".$referto.";";
                        $this->database->query($build);
                    }
                }
            }
        }

        // Manage indexes
        if ($this->schema['index']) {
            foreach ($this->schema['index'] as $table => $indexes) {
                $build = "SHOW INDEXES FROM `".$table."`;";
                $result = $this->database->query($build);
                $current_indexes = [];
                foreach ($result as $value) {
                    $current_indexes[] = $value['Key_name'];
                }

                $desired_indexes = array_keys($indexes);
                $indexes_to_add = array_diff($desired_indexes, $current_indexes);

                foreach ($indexes_to_add as $index_to_add) {
                    $columns = $indexes[$index_to_add];
                    $sql = "CREATE INDEX ".$index_to_add." ON ".$table." (`".implode("`, `", $columns)."`);";
                    $result = $this->database->query($sql);
                    if ($result->errorCode() != '00000') {
                        error_log("Index update failed for ${table} ${index_to_add}: $result->errorCode()");
                    }
                }
            }
        }

        // If there is seed available for this table, seed it!
        if ($force || !empty($initialize)) {
            if ($this->seed) {
                foreach ($this->seed as $table => $data) {
                    foreach ($data as $entry) {
                        if (in_array($table, $initialize)) {
                            Insert::new($this->database)
                                ->into($table)
                                ->columns(array_merge($entry['index'], $entry['data']))
                                ->perform();
                        } elseif ($force) {
                            Update::new($this->database)
                                ->table($table)
                                ->columns($entry['data'])
                                ->whereEquals($entry['index'])
                                ->perform();
                        }
                    }
                }
            }
        }

    }


   /* end */
}
