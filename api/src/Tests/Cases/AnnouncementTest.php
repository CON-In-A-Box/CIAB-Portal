<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;
use App\Tests\Base\TestRun;

class AnnouncementTest extends CiabTestCase
{

    private $target;

    private $position;


    protected function addAnnouncement($scope): string
    {
        $data = testRun::testRun($this, 'GET', '/department/{id}/announcements')
            ->setUriParts(['id' => 2])
            ->run();
        $initial_ids = [];
        foreach ($data->data as $item) {
            $initial_ids[] = $item->id;
        }

        testRun::testRun($this, 'POST', '/department/{id}/announcement')
            ->setUriParts(['id' => 2])
            ->setBody(['scope' => $scope,
                      'text' => 'testing',
                      'email' => 1])
            ->run();

        $data = testRun::testRun($this, 'GET', '/department/{id}/announcements')
            ->setUriParts(['id' => 2])
            ->run();

        # Find New Index
        $target = null;
        foreach ($data->data as $item) {
            if ($target == null && !in_array($item->id, $initial_ids)) {
                $target = $item->id;
                $this->assertIncludes($item, 'department');
                $this->assertIncludes($item, 'posted_by');
                unset($item->department);
                unset($item->posted_by);
                $this->assertEquals([
                    'type' => 'announcement',
                    'id' => $target,
                    'posted_on' => $item->posted_on,
                    'scope' => "$scope",
                    'text' => 'testing',
                ], (array)$item);
            }
        }

        $data = testRun::testRun($this, 'GET', '/announcement/{id}')
            ->setUriParts(['id' => $target])
            ->setMethodParameters(['fields' => 'type,id,scope,text'])
            ->run();

        $this->assertSame([
            'type' => 'announcement',
            'id' => $target,
            'scope' => "$scope",
            'text' => 'testing'
        ], (array)$data);

        return $target;

    }


    protected function setUp(): void
    {
        parent::setUp();
        $this->target = $this->addAnnouncement(0);
        $id = $this->testing_accounts[0];
        $this->position = testRun::testRun($this, 'POST', "/member/{id}/staff_membership")
            ->setUriParts(['id' => $id])
            ->setBody(['Department' => '2', 'Position' => '3', 'Note' => 'PHPUnit Testing'])
            ->run();

    }


    protected function tearDown(): void
    {
        testRun::testRun($this, 'DELETE', '/announcement/{id}')
            ->setUriParts(['id' => $this->target])
            ->run();
        testRun::testRun($this, 'DELETE', '/staff/membership/{id}')
            ->setUriParts(['id' => $this->position->id])
            ->run();

        parent::tearDown();

    }


    public function provider(): array
    {
        /*
         *  $department,
         *  $scope,
         *  $account,
         *  $result
         */
        return [
            /* Loki, concom member, same department */
        [2, 0, 0, true],
        [2, 1, 0, true],
        [2, 2, 0, true],
            /* Loki, concom member, other department*/
        [4, 0, 0, true],
        [4, 1, 0, true],
        [4, 2, 0, false],
            /* Frigga , normal member */
        [2, 0, 1, true],
        [2, 1, 1, false],
        [2, 2, 1, false],
            /* Thor, Admin member, other department */
        [4, 0, null, true],
        [5, 1, null, true],
        [6, 2, null, true],
        ];

    }


    /**
     * @test
     * @dataProvider provider
     **/
    public function testAnnouncmentScope($department, $scope, $account, $result): void
    {
        if ($account === null) {
            $id = 1000;
        } else {
            $id = $this->testing_accounts[$account];
        }

        testRun::testRun($this, 'PUT', '/announcement/{id}')
            ->setUriParts(['id' => $this->target])
            ->setBody(['department' => $department,
                        'scope' => $scope ])
            ->setJson(false)
            ->run();

        /* check member access */


        $data = testRun::testRun($this, 'GET', '/announcement')
            ->setNpLoginIndex($account)
            ->run();
        $found = false;
        foreach ($data->data as $entry) {
            if ($entry->id == $this->target) {
                $found = true;
            }
        }
        if ($result) {
            $this->assertTrue($found);
            $this->assertNotEmpty($data->data);
        } else {
            $this->assertFalse($found);
        }

        /* check department access */
        $data = testRun::testRun($this, 'GET', '/department/{id}/announcements')
            ->setUriParts(['id' => $department])
            ->setNpLoginIndex($account)
            ->run();
        $found = false;
        foreach ($data->data as $entry) {
            if ($entry->id == $this->target) {
                $found = true;
            }
        }
        if ($result) {
            $this->assertTrue($found);
            $this->assertNotEmpty($data->data);
        } else {
            $this->assertFalse($found);
        }

        /* check direct access */

        if ($result) {
            $code = 200;
        } else {
            $code = 403;
        }

        testRun::testRun($this, 'GET', '/announcement/{id}')
            ->setUriParts(['id' => $this->target])
            ->setNpLoginIndex($account)
            ->setExpectedResult($code)
            ->run();

    }


    public function testAnnounceErrorGetBadId(): void
    {
        testRun::testRun($this, 'GET', '/department/{id}/announcements')
            ->setUriParts(['id' => -1])
            ->setExpectedResult(404)
            ->run();

    }


    public function testAnnounceErrorPutDuplicateId(): void
    {
        testRun::testRun($this, 'PUT', '/announcement/{id}')
            ->setUriParts(['id' => $this->target])
            ->setExpectedResult(400)
            ->run();

    }


    public function testAnnounceErrorPutBadId(): void
    {
        testRun::testRun($this, 'PUT', '/announcement/{id}')
            ->setUriParts(['id' => -1])
            ->setExpectedResult(404)
            ->run();

    }


    public function testAnnounceErrorPutBadDepartment(): void
    {
        testRun::testRun($this, 'PUT', '/announcement/{id}')
            ->setUriParts(['id' => $this->target])
            ->setBody(['department' => -1])
            ->setExpectedResult(404)
            ->run();

    }


    public function testAnnounceErrorPostNoBody(): void
    {
        testRun::testRun($this, 'POST', '/department/{id}/announcement')
            ->setUriParts(['id' => 2])
            ->setExpectedResult(400)
            ->run();

    }


    public function testAnnounceErrorPostBadId(): void
    {
        testRun::testRun($this, 'POST', '/department/{id}/announcement')
            ->setUriParts(['id' => -1])
            ->setBody(['scope' => 2,
                       'text' => 'testing',
                       'email' => 0])
            ->setExpectedResult(404)
            ->run();

    }


    public function testAnnounceErrorPostNoScope(): void
    {
        testRun::testRun($this, 'POST', '/department/{id}/announcement')
            ->setUriParts(['id' => 2])
            ->setBody(['text' => 'testing',
                       'email' => 0])
            ->setExpectedResult(400)
            ->run();

    }


    public function testAnnounceErrorPostBadScope(): void
    {
        testRun::testRun($this, 'POST', '/department/{id}/announcement')
            ->setUriParts(['id' => 2])
            ->setBody(['scope' => 2,
                       'email' => 0])
            ->setExpectedResult(400)
            ->run();

    }


    /* End */
}
