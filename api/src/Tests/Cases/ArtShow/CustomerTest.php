<?php

namespace App\Tests\TestCase\Controller;

use Atlas\Query\Delete;
use Atlas\Query\Insert;
use App\Tests\Base\CiabTestCase;

class ArtshowCustomerTest extends CiabTestCase
{

    private $customer = null;


    protected function setUp(): void
    {
        parent::setUp();
        $data = $this->runRequest('GET', "/artshow/customer/1");
        if ($data->getStatusCode() == 200) {
            $data = json_decode((string)$data->getBody());
            Delete::new($this->container->db)
                ->from('Artshow_Buyer')
                ->whereEquals(['BuyerID' => $data->id])
                ->perform();
        }

    }


    protected function tearDown(): void
    {
        Delete::new($this->container->db)
            ->from('Artshow_Buyer')
            ->whereEquals(['BuyerID' => $this->customer])
            ->perform();
        parent::tearDown();

    }


    public function testCustomer(): void
    {
        $this->runRequest(
            'POST',
            "/artshow/customer",
            null,
            null,
            400
        );

        $data = $this->runSuccessJsonRequest(
            'POST',
            "/artshow/customer",
            null,
            ['identifier' => 'SillyString'],
            201
        );
        $this->customer = $data->id;

        $this->runRequest(
            'GET',
            "/artshow/customer/",
            null,
            null,
            405
        );

        $data = $this->runSuccessJsonRequest(
            'GET',
            "/artshow/customer/".$this->customer
        );

        $data = $this->runRequest(
            'PUT',
            "/artshow/customer/".$this->customer,
            null,
            null,
            400
        );

        $data = $this->runRequest(
            'PUT',
            "/artshow/customer/".$this->customer,
            null,
            ['identifier' => 'Giggles']
        );

        $data = $this->runSuccessJsonRequest(
            'GET',
            "/artshow/customer/".$this->customer
        );

        $this->assertSame($data->identifier, 'Giggles');

        $data = $this->runSuccessJsonRequest(
            'GET',
            "/artshow/customer/find/Giggles"
        );
        $this->assertSame($data->identifier, 'Giggles');

        $data = $this->runSuccessJsonRequest(
            'GET',
            "/artshow/customer/find/Gig",
            ['partial' => 'true'],
            null
        );

        $this->assertSame($data->identifier, 'Giggles');

        $data = $this->runRequest(
            'GET',
            "/artshow/customer/find/Blah",
            ['partial' => 'true'],
            null,
            404
        );

    }


    /* End */
}
