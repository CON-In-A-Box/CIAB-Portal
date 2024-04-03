<?php declare(strict_types=1);

namespace App\Tests\TestCase\Controller\Store;

use Faker;

use App\Tests\Base\StoreTestCase;
use App\Tests\Base\TestRun;

class PostStoreTest extends StoreTestCase
{


    public function testGoodInsert(): void
    {
        $data = testRun::testRun($this, 'POST', '/stores')
            ->setBody($this->data[0])
            ->run();

    }


    public function testBadInsert(): void
    {
        $store = $this->data[0];
        $store['name'] = null;

        testRun::testRun($this, 'POST', '/stores')
            ->setBody($store)
            ->setExpectedResult(400)
            ->run();

    }


    /* end */
}
