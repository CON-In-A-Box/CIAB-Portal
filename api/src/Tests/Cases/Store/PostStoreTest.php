<?php declare(strict_types=1);

namespace App\Tests\TestCase\Controller\Store;

use Faker;

use App\Tests\Base\StoreTestCase;

class PostStoreTest extends StoreTestCase
{


    public function testGoodInsert(): void
    {
        $data = $this->runSuccessJsonRequest('POST', '/stores', null, $this->data[0], 201);

    }


    public function testBadInsert(): void
    {
        $store = $this->data[0];
        $store['name'] = null;

        $data = $this->runRequest('POST', '/stores', null, $store, 400);

    }


    /* end */
}
