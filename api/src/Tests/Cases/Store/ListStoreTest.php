<?php declare(strict_types=1);

namespace App\Tests\TestCase\Controller\Store;

use Atlas\Query\Insert;
use Atlas\Query\Delete;
use Faker;

use App\Tests\Base\StoreTestCase;
use App\Controller\Stores\BaseStore;

class ListStoreTest extends StoreTestCase
{


    protected function setUp(): void
    {
        parent::setUp();

        foreach ($this->data as &$store) {
            $store['id'] = $this->insert($store);
        }

    }


    protected function insert($store): String
    {
        $ins = Insert::new($this->container->db);
        $ins->into('Stores');
        $ins->columns(BaseStore::insertPayloadFromParams($store));
        $ins->perform();
        return $ins->getLastInsertId();

    }


    public function testGoodEventList(): void
    {
        $data = $this->runSuccessJsonRequest('GET', '/stores');
        $this->assertEquals(count($data->data), 5);
        for ($i = 0; $i < 5; $i++) {
            $this->assertEquals(get_object_vars($data->data[$i]), $this->data[$i]);
        }

    }


    /* end */
}
