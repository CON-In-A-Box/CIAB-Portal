<?php declare(strict_types=1);

namespace App\Tests\TestCase\Controller\Store;

use Atlas\Query\Insert;
use Atlas\Query\Delete;
use Faker;

use App\Tests\Base\StoreTestCase;
use App\Controller\Stores\BaseStore;
use App\Tests\Base\TestRun;

class GetStoreTest extends StoreTestCase
{


    protected function setUp(): void
    {
        parent::setUp();

        $this->insertSingleFixture();

    }


    public function testGoodGet(): void
    {
        $data = testRun::testRun($this, 'GET', '/stores/{id}')
            ->setUriParts(['id' => $this->store['id']])
            ->run();
        $this->assertEquals(get_object_vars($data), $this->store);

    }


    public function testNotFoundGet(): void
    {
        testRun::testRun($this, 'GET', '/stores/{id}')
            ->setUriParts(['id' => '424242424242'])
            ->setExpectedResult(404)
            ->run();

    }


    /* end */
}
