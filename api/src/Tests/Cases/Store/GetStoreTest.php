<?php declare(strict_types=1);

namespace App\Tests\TestCase\Controller\Store;

use Atlas\Query\Insert;
use Atlas\Query\Delete;
use Faker;

use App\Tests\Base\StoreTestCase;
use App\Controller\Stores\BaseStore;

class GetStoreTest extends StoreTestCase
{


    protected function setUp(): void
    {
        parent::setUp();

        $this->insertSingleFixture();

    }


    public function testGoodGet(): void
    {
        $data = $this->runSuccessJsonRequest('GET', '/stores/'.$this->store['id']);
        $this->assertEquals(get_object_vars($data), $this->store);

    }


    public function testNotFoundGet(): void
    {
        $this->runRequest('GET', '/stores/424242424242', null, null, 404);

    }


    /* end */
}
