<?php declare(strict_types=1);

namespace App\Tests\Base;

use Atlas\Query\Insert;
use Atlas\Query\Delete;
use Faker;

use App\Tests\Base\CiabTestCase;
use App\Controller\Stores\BaseStore;

abstract class StoreTestCase extends CiabTestCase
{

    protected $data = array();

    protected $store = null;


    protected function setUp(): void
    {
        parent::setUp();

        $faker = Faker\Factory::create();

        for ($i = 0; $i < 5; $i++) {
            $this->data[] = [
            'store_slug' => $faker->lexify(),
            'name' => $faker->name(),
            'description' => $faker->text(),
            ];
        }

    }


    protected function tearDown(): void
    {
        $del = Delete::new($this->container->db);
        $del->from('Stores')->perform();
        $this->data = array();

        parent::tearDown();

    }


    protected function insertSingleFixture(): void
    {
        $ins = Insert::new($this->container->db);
        $this->store = $this->data[0];
        $this->store['type'] = 'store'; // Individual gets have types, lists right now don't

        $ins->into('Stores');
        $ins->columns(BaseStore::insertPayloadFromParams($this->store));
        $ins->perform();
        $this->store['id'] = $ins->getLastInsertId();

    }


    /* end */
}
