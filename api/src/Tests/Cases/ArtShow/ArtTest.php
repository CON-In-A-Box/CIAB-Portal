<?php

namespace App\Tests\TestCase\Controller;

use Atlas\Query\Delete;
use Atlas\Query\Insert;
use App\Tests\Base\CiabTestCase;

class ArtshowArtTest extends CiabTestCase
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
                "/artshow/art/$id"
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


    public function testArt(): void
    {
        $this->runRequest(
            'GET',
            "/artshow/artist/$this->artist/art",
            null,
            null,
            404
        );

        $this->runRequest(
            'POST',
            "/artshow/artist/$this->artist/art",
            null,
            null,
            400
        );

        $data = $this->runRequest(
            'POST',
            "/artshow/artist/$this->artist/art",
            null,
            ['name' => 'Billy', 'art_type' => 'INVALID'],
            400
        );

        $data = $this->runSuccessJsonRequest(
            'POST',
            "/artshow/artist/$this->artist/art",
            null,
            ['name' => 'Billy', 'art_type' => 'Framed'],
            201
        );

        $this->art[] = $data->id;

        $data = $this->runSuccessJsonRequest(
            'GET',
            "/artshow/art"
        );

        $data = $this->runSuccessJsonRequest(
            'GET',
            "/artshow/art/piece/".$this->art[0]
        );

        $data = $this->runSuccessJsonRequest(
            'GET',
            "/artshow/artist/$this->artist/art"
        );

        $this->runRequest(
            'PUT',
            "/artshow/art/".$this->art[0],
            null,
            null,
            400
        );

        $data = $this->runSuccessJsonRequest(
            'PUT',
            "/artshow/art/".$this->art[0],
            null,
            ['name' => 'Billy 2']
        );
        $this->assertSame($data->name, 'Billy 2');

        $this->NPRunRequest(
            'POST',
            "/artshow/artist/$this->artist/art",
            null,
            ['name' => 'Billy again', 'art_type' => 'Framed'],
            403
        );

        $this->NPRunRequest(
            'PUT',
            "/artshow/art/".$this->art[0],
            null,
            ['name' => 'Billy 3'],
            403
        );


        $this->runRequest(
            'DELETE',
            "/artshow/art/".$this->art[0],
            null,
            null,
            204
        );

        $this->art = [];

    }


    /* End */
}
