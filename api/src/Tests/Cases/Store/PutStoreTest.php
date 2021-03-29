<?php declare(strict_types=1);

namespace App\Tests\TestCase\Controller\Store;

use Atlas\Query\Insert;
use Faker;

use App\Tests\Base\StoreTestCase;

class PutStoreTest extends StoreTestCase
{


    protected function setUp(): void
    {
        parent::setUp();

        $this->insertSingleFixture();

    }


    public function testGoodUpdate(): void
    {
        $data = $this->runSuccessJsonRequest('PUT', "/stores/".$this->store['id'], null, ['name' => 'Foo']);

    }


    /* end */
}
