<?php declare(strict_types=1);

namespace App\Tests\TestCase\Controller\Store;

use Atlas\Query\Insert;
use Faker;

use App\Tests\Base\StoreTestCase;
use App\Controller\Stores\BaseStore;

class DeleteStoreTest extends StoreTestCase
{


    protected function setUp(): void
    {
        parent::setUp();

        $this->insertSingleFixture();

    }


    public function testGoodDelete(): void
    {
        $data = $this->runSuccessJsonRequest('DELETE', '/stores/'.$this->store['id'], null, null, 204);

    }


    public function testBadDelete(): void
    {
        $this->runRequest('DELETE', '/stores/42424242422', null, null, 404);

    }


    /* end */
}
