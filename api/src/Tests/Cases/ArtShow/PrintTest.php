<?php

namespace App\Tests\TestCase\Controller;

use Atlas\Query\Delete;
use Atlas\Query\Insert;
use App\Tests\Base\CiabTestCase;

class ArtshowPrintTest extends CiabTestCase
{

    private $artist = null;

    private $art = [];


    protected function setUp(): void
    {
        parent::setUp();
        $data = $this->runRequest('GET', "/artshow/artist/member/1000");
        if ($data->getStatusCode() !== 200) {
            $data = $this->runSuccessJsonRequest(
                'POST',
                "/artshow/artist",
                null,
                ['member' => '1000'],
                201
            );
            $this->artist = $data->id;
        } else {
            $data = json_decode((string)$data->getBody());
            $this->artist = $data->id;
        }

        $this->runSuccessJsonRequest(
            'POST',
            "/artshow/artist/$this->artist/show",
            null,
            null,
            201
        );

    }


    protected function tearDown(): void
    {
        foreach ($this->art as $id) {
            $data = $this->runRequest(
                'DELETE',
                "/artshow/print/$id"
            );
        }
        $this->art = [];

        Delete::new($this->container->db)
            ->from('Artshow_Registration')
            ->whereEquals(['ArtistID' => $this->artist])
            ->perform();
        Delete::new($this->container->db)
            ->from('Artshow_Artist')
            ->whereEquals(['ArtistID' => $this->artist])
            ->perform();
        parent::tearDown();

    }


    public function testPrint(): void
    {
        $this->runRequest(
            'GET',
            "/artshow/artist/$this->artist/print",
            null,
            null,
            404
        );

        $this->runRequest(
            'POST',
            "/artshow/artist/$this->artist/print",
            null,
            null,
            400
        );

        $this->runRequest(
            'POST',
            "/artshow/artist/$this->artist/print",
            null,
            ['name' => 'Billy', 'art_type' => 'INVALID',
             'quantity' => '6', 'price' => '6'],
            400
        );

        $this->runRequest(
            'POST',
            "/artshow/artist/$this->artist/print",
            null,
            ['name' => 'Billy', 'art_type' => 'INVALID',
             'quantity' => '6'],
            400
        );

        $data = $this->runSuccessJsonRequest(
            'POST',
            "/artshow/artist/$this->artist/print",
            null,
            ['name' => 'Billy', 'art_type' => 'Framed',
             'quantity' => '6', 'price' => '6'],
            201
        );

        $this->art[] = $data->id;

        $data = $this->runSuccessJsonRequest(
            'GET',
            "/artshow/print"
        );

        $data = $this->runSuccessJsonRequest(
            'GET',
            "/artshow/print/".$this->art[0]
        );

        $data = $this->runSuccessJsonRequest(
            'GET',
            "/artshow/artist/$this->artist/print"
        );

        $this->runRequest(
            'PUT',
            "/artshow/print/".$this->art[0],
            null,
            null,
            400
        );

        $data = $this->runSuccessJsonRequest(
            'PUT',
            "/artshow/print/".$this->art[0],
            null,
            ['name' => 'Billy 2']
        );
        $this->assertSame($data->name, 'Billy 2');

        $this->runRequest(
            'DELETE',
            "/artshow/print/".$this->art[0],
            null,
            null,
            204
        );

        $this->art = [];

    }


    /* End */
}
