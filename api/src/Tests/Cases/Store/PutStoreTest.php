<?php declare(strict_types=1);

namespace App\Tests\TestCase\Controller\Store;

use Atlas\Query\Insert;
use Faker;

use App\Tests\Base\StoreTestCase;
use App\Tests\Base\TestRun;

class PutStoreTest extends StoreTestCase
{


    protected function setUp(): void
    {
        parent::setUp();

        $this->insertSingleFixture();

    }


    public function testGoodUpdate(): void
    {
        $data = testRun::testRun($this, 'PUT', "/stores/{id}")
            ->setUriParts(['id' => $this->store['id']])
            ->setBody(['name' => 'Foo'])
            ->run();

    }


    /* end */
}
