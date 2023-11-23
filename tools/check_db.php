<?php declare(strict_types=1);

require_once(__DIR__.'/../vendor/autoload.php');
require_once(__DIR__.'/../backends/mysqlpdo.inc');

/* Initializes the api */
require_once(__DIR__.'/../api/src/App/App.php');

use Atlas\Query\Select;

$attempt = 0;

while ($attempt < 10) {
    try {
        $db = MyPDO::instance();
        Select::new($db)->columns('table_name')->from('information_schema.tables')->perform();
    } catch (Exception $e) {
        if ($attempt < 10) {
            print("Sleeping 5\n");
            sleep(5);
            $attempt += 1;
        }
        continue;
    }
    break;
}
