<?php declare(strict_types=1);

namespace App\Tests\TestCase\Controller\Store;

use Atlas\Query\Insert;
use Faker;

use App\Tests\Base\StoreTestCase;
use App\Controller\Stores\BaseStore;
use App\Tests\Base\TestRun;

class DeleteStoreTest extends StoreTestCase
{


    protected function setUp(): void
    {
        parent::setUp();

        $this->insertSingleFixture();

    }


    public function testGoodDelete(): void
    {
        $data = testRun::testRun($this, 'DELETE', '/stores/{id}')
            ->setUriParts(['id' => $this->store['id']])
            ->run();

    }


    public function testBadDelete(): void
    {
        testRun::testRun($this, 'DELETE', '/stores/{id}')
            ->setUriParts(['id' => '42424242422'])
            ->setExpectedResult(404)
            ->run();

    }


    /* end */
}
