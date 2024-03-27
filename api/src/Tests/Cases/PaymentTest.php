<?php
/*.
    require_module 'standard';
.*/

namespace App\Tests\TestCase\Controller;

use Atlas\Query\Delete;
use Atlas\Query\Insert;
use App\Tests\Base\CiabTestCase;
use App\Tests\Base\TestRun;

class PaymentTest extends CiabTestCase
{


    protected function setUp(): void
    {
        parent::setUp();

    }


    protected function tearDown(): void
    {
        parent::tearDown();

    }


    public function testPayment(): void
    {
        global $BASEURL, $_ENV;

        if (array_key_exists('STRIPE_PRIVATE_KEY', $_ENV)) {
            testRun::testRun($this, 'POST', '/payment')
                ->setBody([
                'success' => 'http://localhost:8080/index.php?Function=payment',
                'cancel' => 'http://localhost:8080/index.php?Function=payment',
                'cart' => [[
                    'name' => 'googlie eyes',
                    'price' => 100,
                    'quantity' => 1
                    ]],
                ])
                ->setVerifyYaml(false)
                ->run();
        } else {
            testRun::testRun($this, 'POST', '/payment')
                ->setBody([
                'success' => 'http://localhost:8080/index.php?Function=payment',
                'cancel' => 'http://localhost:8080/index.php?Function=payment',
                'cart' => [[
                    'name' => 'googlie eyes',
                    'price' => 100,
                    'quantity' => 1
                    ]],
                ])
                ->setExpectedResult(500)
                ->setVerifyYaml(false)
                ->run();
        }

    }


    /* End */
}
