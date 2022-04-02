<?php

namespace App\Tests\TestCase\Controller;

use Atlas\Query\Delete;
use Atlas\Query\Insert;
use App\Tests\Base\CiabTestCase;

class ArtshowShowTest extends CiabTestCase
{

    private $artist = null;


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

    }


    protected function tearDown(): void
    {
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


    public function testArtistShow(): void
    {
        $this->runRequest(
            'GET',
            "/artshow/artist/$this->artist/show",
            null,
            null,
            404
        );

        $data = $this->runSuccessJsonRequest(
            'POST',
            "/artshow/artist/$this->artist/show",
            null,
            null,
            201
        );

        $data = $this->runSuccessJsonRequest(
            'GET',
            "/artshow/artist/$this->artist/show"
        );


        $this->runRequest(
            'PUT',
            "/artshow/artist/$this->artist/show",
            null,
            null,
            400
        );

        $data = $this->runSuccessJsonRequest(
            'PUT',
            "/artshow/artist/$this->artist/show",
            null,
            ['check_number' => '100']
        );

    }


    /* End */
}
