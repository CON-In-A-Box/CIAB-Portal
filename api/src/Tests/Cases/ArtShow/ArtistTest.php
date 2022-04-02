<?php

namespace App\Tests\TestCase\Controller;

use Atlas\Query\Delete;
use Atlas\Query\Insert;
use App\Tests\Base\CiabTestCase;

class ArtshowArtistTest extends CiabTestCase
{

    private $artist = null;


    protected function setUp(): void
    {
        parent::setUp();
        $data = $this->runRequest('GET', "/artshow/artist/member/1000");
        if ($data->getStatusCode() == 200) {
            $data = json_decode((string)$data->getBody());
            Delete::new($this->container->db)
                ->from('Artshow_Registration')
                ->whereEquals(['ArtistID' => $data->id])
                ->perform();
            Delete::new($this->container->db)
                ->from('Artshow_Artist')
                ->whereEquals(['ArtistID' => $data->id])
                ->perform();
        }

    }


    protected function tearDown(): void
    {
        Delete::new($this->container->db)
            ->from('Artshow_Artist')
            ->whereEquals(['ArtistID' => $this->artist])
            ->perform();
        parent::tearDown();

    }


    public function testArtistArtist(): void
    {
        $data = $this->runSuccessJsonRequest(
            'POST',
            "/artshow/artist",
            null,
            null,
            201
        );
        $this->artist = $data->id;

        $this->runRequest(
            'POST',
            "/artshow/artist",
            null,
            null,
            409
        );

        $data = $this->runSuccessJsonRequest(
            'GET',
            "/artshow/artist"
        );

        $data = $this->runSuccessJsonRequest(
            'GET',
            "/artshow/artist/".$this->artist
        );

        $data = $this->runSuccessJsonRequest(
            'GET',
            "/artshow/artist/member/1000"
        );

        $data = $this->runSuccessJsonRequest(
            'GET',
            "/artshow/artist"
        );

        $data = $this->runRequest(
            'PUT',
            "/artshow/artist/".$this->artist,
            null,
            null,
            400
        );

        $data = $this->runRequest(
            'PUT',
            "/artshow/artist/".$this->artist,
            null,
            ['company_name' => 'Giggles']
        );

        $data = $this->runSuccessJsonRequest(
            'GET',
            "/artshow/artist"
        );

        $this->assertSame($data->company_name, 'Giggles');

    }


    /* End */
}
