<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;
use App\Tests\Base\TestRun;

class VolunteersTest extends CiabTestCase
{

    protected $staff_hours = 0;


    protected function setUp(): void
    {
        parent::setUp();
        $value = testRun::testRun($this, 'GET', "/admin/configuration/{field}")
            ->setUriParts(['field' => 'CONCOMHOURS'])
            ->run();
        $this->staff_hours = $value->value;
        testRun::testRun($this, 'PUT', "/admin/configuration")
            ->setBody(['Field' => 'CONCOMHOURS', 'Value' => 0])
            ->run();

    }


    protected function tearDown(): void
    {
        testRun::testRun($this, 'PUT', "/admin/configuration")
            ->setBody(['Field' => 'CONCOMHOURS', 'Value' => $this->staff_hours])
            ->run();
        parent::tearDown();

    }


    private function checkHourEntry($entry, $department = 2)
    {
        $this->assertEquals($entry->member->id, 1000);
        $this->assertEquals($entry->authorizer->id, 1000);
        $this->assertEquals($entry->enterer->id, 1000);
        $this->assertEquals($entry->department->id, $department);
        $this->assertObjectHasAttribute('id', $entry->event);
        return($entry->hours * $entry->modifier);

    }


    private function checkHourSummaryEntry($entry)
    {
        if (property_exists($entry, 'member')) {
            $this->assertEquals($entry->member->id, 1000);
        } else {
            $this->assertEquals($entry->department->id, 2);
        }
        return ($entry->total_hours);

    }


    private function checkHourList($data, $ids, $entrycheck): void
    {
        $this->assertNotEmpty($data);
        $total = 0;
        foreach ($data->data as $entry) {
            $total += $this->{$entrycheck}($entry);
        }
        $this->assertEquals($total, $data->total_hours);

    }


    public function testHours(): void
    {
        $ids = [];

        $when = date('Y-m-d h:i:s', strtotime('+2 hour'));
        testRun::testRun($this, 'POST', '/volunteer/hours')
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'POST', '/volunteer/hours')
            ->setBody(['department' => '2'])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'POST', '/volunteer/hours')
            ->setBody(['department' => '2', 'member' => '1000'])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'POST', '/volunteer/hours')
            ->setBody(['department' => '2', 'member' => '1000',  'enterer' => 1000])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'POST', '/volunteer/hours')
            ->setBody(['department' => '2', 'member' => '1000',  'enterer' => 1000, 'authorizer' => '1000'])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'POST', '/volunteer/hours')
            ->setBody(['department' => '2', 'member' => '1000',  'enterer' => 1000, 'authorizer' => '1000', 'hours' => '2'])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'POST', '/volunteer/hours')
            ->setBody(['department' => '2', 'member' => '1000',  'enterer' => 1000, 'authorizer' => '1000', 'hours' => 'alphebet'])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'POST', '/volunteer/hours')
            ->setBody(['department' => '2', 'member' => '1000',  'enterer' => 1000, 'authorizer' => '1000', 'hours' => 2, 'end' => 'gigglypoof'])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'POST', '/volunteer/hours')
            ->setBody(['department' => 'Spaz', 'member' => 1000,  'enterer' => 1000, 'authorizer' => 1000, 'hours' => 2, 'end' => $when])
            ->setExpectedResult(404)
            ->run();
        testRun::testRun($this, 'POST', '/volunteer/hours')
            ->setBody(['department' => 2, 'member' => 'asdf',  'enterer' => 1000, 'authorizer' => 1000, 'hours' => 2, 'end' => $when])
            ->setExpectedResult(404)
            ->run();
        testRun::testRun($this, 'POST', '/volunteer/hours')
            ->setBody(['department' => 2, 'member' => '1000',  'enterer' => -1, 'authorizer' => 1000, 'hours' => 2, 'end' => $when])
            ->setExpectedResult(404)
            ->run();
        testRun::testRun($this, 'POST', '/volunteer/hours')
            ->setBody(['department' => 2, 'member' => '1000',  'enterer' => 1000, 'authorizer' => 'hello', 'hours' => 2, 'end' => $when])
            ->setExpectedResult(404)
            ->run();

        $data = testRun::testRun($this, 'POST', '/volunteer/hours')
            ->setBody(['department' => '2', 'member' => '1000',  'enterer' => 1000, 'authorizer' => '1000', 'hours' => '2', 'end' => $when])
            ->run();
        $this->assertNotEmpty($data);
        $ids[] = $data->id;

        $data = testRun::testRun($this, 'POST', '/volunteer/hours')
            ->setBody(['department' => '2', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '1.2', 'end' => date('Y-m-d h:i:s', strtotime('-2 hour')), 'modifier' => '2.5'])
            ->run();
        $this->assertNotEmpty($data);
        $ids[] = $data->id;

        testRun::testRun($this, 'GET', '/member/{id}/volunteer/hours')
            ->setUriParts(['id' => 1])
            ->setExpectedResult(404)
            ->run();
        testRun::testRun($this, 'GET', '/member/{id}/volunteer/hours')
            ->setUriParts(['id' => 'nobody'])
            ->setExpectedResult(404)
            ->run();
        $data = testRun::testRun($this, 'GET', '/member/{id}/volunteer/hours')
            ->setUriParts(['id' => 1000])
            ->run();
        $this->assertEquals(count($data->data), count($ids));
        $this->checkHourList($data, $ids, 'checkHourEntry');

        $data = testRun::testRun($this, 'GET', '/member/{id}/volunteer/hours/summary')
            ->setUriParts(['id' => 1000])
            ->run();
        $this->assertEquals(count($data->data), 1);
        $this->checkHourList($data, $ids, 'checkHourSummaryEntry');

        $data = testRun::testRun($this, 'GET', '/department/{id}/volunteer/hours')
            ->setUriParts(['id' => 2])
            ->run();
        $this->checkHourList($data, $ids, 'checkHourEntry');

        $data = testRun::testRun($this, 'GET', '/department/{id}/volunteer/hours/summary')
            ->setUriParts(['id' => 2])
            ->run();
        $this->checkHourList($data, $ids, 'checkHourSummaryEntry');

        $data = testRun::testRun($this, 'GET', '/department/{id}/volunteer/hours/summary')
            ->setUriParts(['id' => 'Activities'])
            ->run();
        $this->checkHourList($data, $ids, 'checkHourSummaryEntry');

        $data = testRun::testRun($this, 'GET', '/event/{id}/volunteer/hours')
            ->setUriParts(['id' => 'current'])
            ->run();
        $this->checkHourList($data, $ids, 'checkHourEntry');

        $data = testRun::testRun($this, 'GET', '/event/{id}/volunteer/hours/summary')
            ->setUriParts(['id' => 'current'])
            ->run();
        $this->checkHourList($data, $ids, 'checkHourSummaryEntry');

        $data = testRun::testRun($this, 'GET', '/volunteer/hours/{id}')
            ->setUriParts(['id' => $ids[0]])
            ->run();
        $this->assertNotEmpty($data);
        $this->checkHourEntry($data);

        $data = testRun::testRun($this, 'PUT', "/volunteer/hours/{id}")
            ->setUriParts(['id' => $ids[1]])
            ->setBody(['department' => '2'])
            ->run();
        $this->checkHourEntry($data, 2);

        /* Overlap Testing */
        $date = new \DateTime('+1 Year');
        $base = $date->format('Y-m-d H:i:s');
        $newDate = clone $date;
        $newDate->add(new \DateInterval('PT30M'));
        $plusMin = $newDate->format('Y-m-d H:i:s');
        $newDate = clone $date;
        $newDate->sub(new \DateInterval('PT30M'));
        $minusMin = $newDate->format('Y-m-d H:i:s');
        $newDate = clone $date;
        $newDate->add(new \DateInterval('PT1H'));
        $plusHour = $newDate->format('Y-m-d H:i:s');
        $newDate = clone $date;
        $newDate->sub(new \DateInterval('PT2H'));
        $minusHour = $newDate->format('Y-m-d H:i:s');
        $newDate = clone $date;
        $newDate->add(new \DateInterval('PT4H'));
        $largeEnd = $newDate->format('Y-m-d H:i:s');
        $newDate = clone $date;
        $newDate->sub(new \DateInterval('PT30M'));
        $subEnd = $newDate->format('Y-m-d H:i:s');

        /* Base Entry */
        $data = testRun::testRun($this, 'POST', '/volunteer/hours')
            ->setBody(['department' => '2', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '1', 'end' => $base, 'modifier' => '1'])
            ->run();
        $this->assertNotEmpty($data);
        $ids[] = $data->id;

        /* Identical */
        testRun::testRun($this, 'POST', '/volunteer/hours')
            ->setBody(['department' => '2', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '1', 'end' => $base, 'modifier' => '1'])
            ->setExpectedResult(400)
            ->run();

        /* Overlap */
        testRun::testRun($this, 'POST', '/volunteer/hours')
            ->setBody(['department' => '2', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '1', 'end' => $plusMin, 'modifier' => '1'])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'POST', '/volunteer/hours')
            ->setBody(['department' => '2', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '1', 'end' => $minusMin, 'modifier' => '1'])
            ->setExpectedResult(400)
            ->run();

        /* Superset, subset */
        testRun::testRun($this, 'POST', '/volunteer/hours')
            ->setBody(['department' => '2', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '8', 'end' => $largeEnd, 'modifier' => '1'])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'POST', '/volunteer/hours')
            ->setBody(['department' => '2', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '0.1', 'end' => $subEnd, 'modifier' => '1'])
            ->setExpectedResult(400)
            ->run();

        /* Edges, success */
        $data = testRun::testRun($this, 'POST', '/volunteer/hours')
            ->setBody(['department' => '2', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '1', 'end' => $plusHour, 'modifier' => '1'])
            ->run();
        $this->assertNotEmpty($data);
        $ids[] = $data->id;

        $data = testRun::testRun($this, 'POST', '/volunteer/hours')
            ->setBody(['department' => '2', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '1', 'end' => $minusHour, 'modifier' => '1'])
            ->run();
        $this->assertNotEmpty($data);
        $ids[] = $data->id;


        /* Force overlap success */
        $data = testRun::testRun($this, 'POST', '/volunteer/hours')
            ->setMethodParameters(['force' => '1'])
            ->setBody(['department' => '2', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '2', 'end' => $base, 'modifier' => '1'])
            ->run();
        $this->assertNotEmpty($data);
        $ids[] = $data->id;

        /* Not overlap success */
        $when = date('Y-m-d h:i:s', strtotime('+4 hour'));
        $data = testRun::testRun($this, 'POST', '/volunteer/hours')
            ->setBody(['department' => '2', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '2', 'end' => $when, 'modifier' => '1'])
            ->run();
        $this->assertNotEmpty($data);
        $ids[] = $data->id;

        foreach ($ids as $id) {
            testRun::testRun($this, 'DELETE', "/volunteer/hours/{id}")
                ->setUriParts(['id' => $id])
                ->run();
        }

        testRun::testRun($this, 'GET', "/volunteer/hours/{id}")
            ->setUriParts(['id' => $ids[0]])
            ->setExpectedResult(404)
            ->run();

    }


    public function testPrizes(): void
    {
        $prizes = [];
        testRun::testRun($this, 'POST', '/volunteer/rewards')
            ->setExpectedResult(400)
            ->run();
        $data = testRun::testRun($this, 'POST', '/volunteer/rewards')
            ->setBody(['name' => 'Prize1'])
            ->setExpectedResult(400)
            ->run();
        $data = testRun::testRun($this, 'POST', '/volunteer/rewards')
            ->setBody(['name' => 'Prize1', 'inventory' => '100'])
            ->setExpectedResult(400)
            ->run();
        $data = testRun::testRun($this, 'POST', '/volunteer/rewards')
            ->setBody(['name' => 'Prize1', 'inventory' => '100', 'promo' => 0])
            ->setExpectedResult(400)
            ->run();
        $data = testRun::testRun($this, 'POST', '/volunteer/rewards')
            ->setBody(['name' => 'Prize1', 'inventory' => '100', 'promo' => 0, 'value' => 'black'])
            ->setExpectedResult(400)
            ->run();
        $data = testRun::testRun($this, 'POST', '/volunteer/rewards')
            ->setBody(['name' => 'Prize1', 'inventory' => 'green', 'promo' => 0, 'value' => 1.1])
            ->setExpectedResult(400)
            ->run();
        $data = testRun::testRun($this, 'POST', '/volunteer/rewards')
            ->setBody(['name' => 'Prize1', 'promo' => 0, 'inventory' => 100, 'value' => 1.0])
            ->run();
        $this->assertNotEmpty($data);
        $id = $data->id;

        $data2 = testRun::testRun($this, 'GET', "/volunteer/rewards/{id}")
            ->setUriParts(['id' => $id])
            ->run();
        $this->assertNotEmpty($data);
        $this->assertEquals($data2, $data);

        $data = testRun::testRun($this, 'PUT', "/volunteer/rewards/{id}")
            ->setUriParts(['id' => $id])
            ->setBody(['inventory' => 'blue'])
            ->setExpectedResult(400)
            ->run();
        $data = testRun::testRun($this, 'PUT', "/volunteer/rewards/{id}")
            ->setUriParts(['id' => $id])
            ->setBody(['value' => 'blue'])
            ->setExpectedResult(400)
            ->run();
        $data = testRun::testRun($this, 'PUT', "/volunteer/rewards/{id}")
            ->setUriParts(['id' => $id])
            ->setBody(['name' => 'Prize Alpha', 'inventory' => 5])
            ->run();
        $this->assertNotEmpty($data);
        $this->assertEquals($data->name, 'Prize Alpha');
        $this->assertEquals($data->inventory, 5);

        $data = testRun::testRun($this, 'PUT', "/volunteer/rewards/{id}/inventory")
            ->setUriParts(['id' => $id])
            ->setBody(['difference' => 5])
            ->run();
        $this->assertNotEmpty($data);
        $this->assertEquals($data->inventory, 10);

        $data = testRun::testRun($this, 'PUT', "/volunteer/rewards/{id}/inventory")
            ->setUriParts(['id' => $id])
            ->setBody(['difference' => -2])
            ->run();
        $this->assertNotEmpty($data);
        $this->assertEquals($data->inventory, 8);
        $data2 = $data;

        $data = testRun::testRun($this, 'GET', "/volunteer/rewards")
            ->run();
        $this->assertNotEmpty($data);
        $this->assertEquals(count($data->data), 1);
        $this->assertEquals($data->data[0], $data2);

        testRun::testRun($this, 'DELETE', "/volunteer/rewards/{id}")
            ->setUriParts(['id' => $id])
            ->run();

        /* Reward Groups */

        $data = testRun::testRun($this, 'POST', '/volunteer/reward_group')
            ->setBody(['reward_limit' => 'blue'])
            ->setExpectedResult(400)
            ->run();
        $data = testRun::testRun($this, 'POST', '/volunteer/reward_group')
            ->setBody(['reward_limit' => 1])
            ->run();
        $gid = $data->id;
        $this->assertEquals($data->reward_limit, 1);

        $data2 = testRun::testRun($this, 'GET', "/volunteer/reward_group/{id}")
            ->setUriParts(['id' => $gid])
            ->run();
        $this->assertNotEmpty($data);
        $this->assertEquals($data, $data2);

        $data = testRun::testRun($this, 'PUT', "/volunteer/reward_group/{id}")
            ->setUriParts(['id' => $gid])
            ->setBody(['reward_limit' => 'green'])
            ->setExpectedResult(400)
            ->run();
        $data = testRun::testRun($this, 'PUT', "/volunteer/reward_group/{id}")
            ->setUriParts(['id' => $gid])
            ->setBody(['reward_limit' => '2'])
            ->run();
        $this->assertNotEmpty($data);
        $this->assertEquals($data->reward_limit, 2);

        $data = testRun::testRun($this, 'POST', '/volunteer/rewards')
            ->setBody(['name' => 'Prize A', 'promo' => 0, 'inventory' => 100, 'value' => 1.0, 'reward_group' => $gid])
            ->run();
        $pid1 = $data->id;

        $data = testRun::testRun($this, 'POST', '/volunteer/rewards')
            ->setBody(['name' => 'Prize B', 'promo' => 0, 'inventory' => 100, 'value' => 1.0, 'reward_group' => $gid])
            ->run();
        $pid2 = $data->id;

        $data = testRun::testRun($this, 'GET', "/volunteer/reward_group/{id}/list")
            ->setUriParts(['id' => $gid])
            ->run();
        $this->assertNotEmpty($data);
        $this->assertEquals(count($data->data), 2);

        testRun::testRun($this, 'DELETE', "/volunteer/reward_group/{id}")
            ->setUriParts(['id' => $gid])
            ->run();

        $data = testRun::testRun($this, 'GET', "/volunteer/rewards/{id}")
            ->setUriParts(['id' => $pid1])
            ->run();
        $this->assertNotEmpty($data);
        $this->assertEquals($data->reward_group, null);

        testRun::testRun($this, 'DELETE', "/volunteer/rewards/{id}")
            ->setUriParts(['id' => $pid1])
            ->run();
        testRun::testRun($this, 'DELETE', "/volunteer/rewards/{id}")
            ->setUriParts(['id' => $pid2])
            ->run();

    }


    public function testClaims(): void
    {
        $hours = [];
        $claims = [];
        $rewards = [];

        $data = testRun::testRun($this, 'POST', '/volunteer/rewards')
            ->setBody(['name' => 'Prize1', 'promo' => 0, 'inventory' => 100, 'value' => 1.1])
            ->run();
        $this->assertNotEmpty($data);
        $rewards[] = $data->id;

        $data = testRun::testRun($this, 'POST', '/volunteer/rewards')
            ->setBody(['name' => 'Prize2', 'promo' => 0, 'inventory' => 100, 'value' => 2.2])
            ->run();
        $this->assertNotEmpty($data);
        $rewards[] = $data->id;

        $data = testRun::testRun($this, 'POST', '/volunteer/rewards')
            ->setBody(['name' => 'Prize3', 'promo' => 1, 'inventory' => 100, 'value' => 2])
            ->run();
        $this->assertNotEmpty($data);
        $rewards[] = $data->id;

        testRun::testRun($this, 'POST', '/volunteer/claims')
            ->setBody(['member' => '1000', 'reward' => $rewards[0]])
            ->setExpectedResult(400)
            ->run();

        $when = date('Y-m-d h:i:s', strtotime('+4 hour'));
        $data = testRun::testRun($this, 'POST', '/volunteer/hours')
            ->setBody(['department' => '2', 'member' => '1000',  'enterer' => 1000, 'authorizer' => '1000', 'hours' => '2', 'end' => $when])
            ->run();
        $this->assertNotEmpty($data);
        $hours[] = $data->id;

        testRun::testRun($this, 'POST', '/volunteer/claims')
            ->setBody(['member' => '1000', 'reward' => $rewards[1]])
            ->setExpectedResult(400)
            ->run();

        $data = testRun::testRun($this, 'POST', '/volunteer/claims')
            ->setBody(['member' => '1000', 'reward' => $rewards[0]])
            ->run();
        $this->assertNotEmpty($data);
        $claims[] = $data;

        testRun::testRun($this, 'POST', '/volunteer/claims')
            ->setBody(['member' => '1000', 'reward' => $rewards[0]])
            ->setExpectedResult(400)
            ->run();

        $data = testRun::testRun($this, 'POST', '/volunteer/claims')
            ->setBody(['member' => '1000', 'reward' => $rewards[2]])
            ->run();
        $this->assertNotEmpty($data);
        $claims[] = $data;

        $data = testRun::testRun($this, 'POST', '/volunteer/claims')
            ->setBody(['member' => '1000', 'reward' => $rewards[2]])
            ->run();
        $this->assertNotEmpty($data);
        $claims[] = $data;

        $data = testRun::testRun($this, 'PUT', "/volunteer/rewards/{id}")
            ->setUriParts(['id' => $rewards[2]])
            ->setBody(['inventory' => 0])
            ->run();
        $this->assertNotEmpty($data);

        testRun::testRun($this, 'POST', '/volunteer/claims')
            ->setBody(['member' => '1000', 'reward' => $rewards[2]])
            ->setExpectedResult(400)
            ->run();

        $data = testRun::testRun($this, 'GET', '/volunteer/claims/{id}')
            ->setUriParts(['id' => $claims[0]->id])
            ->run();
        $this->assertNotEmpty($data);
        $claims[0]->reward->inventory = 99;
        $claims[0]->reward->claimed = 1;
        $this->assertEquals($claims[0], $data);

        $data = testRun::testRun($this, 'GET', '/volunteer/claims/{id}')
            ->setUriParts(['id' => $claims[1]->id])
            ->run();
        $this->assertNotEmpty($data);
        $claims[1]->reward->inventory = 0;
        $claims[1]->reward->claimed = 2;
        $this->assertEquals($claims[1], $data);

        $data = testRun::testRun($this, 'GET', "/member/{id}/volunteer/claims")
            ->setUriParts(['id' => 1000])
            ->run();
        $this->assertNotEmpty($data);
        $this->assertEquals(count($data->data), 3);

        $data = testRun::testRun($this, 'GET', "/member/{id}/volunteer/claims/summary")
            ->setUriParts(['id' => 1000])
            ->run();
        $this->assertNotEmpty($data);
        $this->assertEquals($data->spent_hours, 1.1);
        $this->assertEquals($data->reward_count, 3);

        /* Reward Groups */
        $data = testRun::testRun($this, 'POST', '/volunteer/reward_group')
            ->setBody(['reward_limit' => 1])
            ->run();
        $this->assertNotEmpty($data);
        $this->assertEquals($data->reward_limit, 1);
        $group_id = $data->id;

        $data = testRun::testRun($this, 'POST', '/volunteer/rewards')
            ->setBody(['name' => 'Prize4', 'promo' => 1, 'inventory' => 100, 'value' => 2, 'reward_group' => $group_id])
            ->run();
        $this->assertNotEmpty($data);
        $target = $data->id;
        $rewards[] = $data->id;

        $data = testRun::testRun($this, 'POST', '/volunteer/rewards')
            ->setBody(['name' => 'Prize5', 'promo' => 1, 'inventory' => 100, 'value' => 2, 'reward_group' => $group_id])
            ->run();
        $this->assertNotEmpty($data);
        $target2 = $data->id;
        $rewards[] = $data->id;

        $data = testRun::testRun($this, 'POST', '/volunteer/claims')
            ->setBody(['member' => '1000', 'reward' => $target])
            ->run();
        $this->assertNotEmpty($data);
        $claims[] = $data;

        $data = testRun::testRun($this, 'POST', '/volunteer/claims')
            ->setBody(['member' => '1000', 'reward' => $target])
            ->setExpectedResult(400)
            ->run();

        $data = testRun::testRun($this, 'POST', '/volunteer/rewards')
            ->setBody(['name' => 'Prize5', 'promo' => 1, 'inventory' => 100, 'value' => 2, 'reward_group' => $group_id])
            ->run();
        $this->assertNotEmpty($data);
        $target = $data->id;
        $rewards[] = $data->id;

        $data = testRun::testRun($this, 'POST', '/volunteer/claims')
            ->setBody(['member' => '1000', 'reward' => $target])
            ->setExpectedResult(400)
            ->run();

        $data = testRun::testRun($this, 'PUT', '/volunteer/claims/{id}')
            ->setUriParts(['id' => end($claims)->id])
            ->setBody(['reward' => $target])
            ->run();
        $this->assertNotEmpty($data);

        foreach ($claims as $claim) {
            testRun::testRun($this, 'DELETE', "/volunteer/claims/{id}")
                ->setUriParts(['id' => $claim->id])
                ->run();
        }
        foreach ($rewards as $id) {
            testRun::testRun($this, 'DELETE', "/volunteer/rewards/{id}")
                ->setUriParts(['id' => $id])
                ->run();
        }
        foreach ($hours as $id) {
            testRun::testRun($this, 'DELETE', "/volunteer/hours/{id}")
                ->setUriParts(['id' => $id])
                ->run();
        }
        testRun::testRun($this, 'DELETE', "/volunteer/reward_group/{id}")
            ->setUriParts(['id' => $group_id])
            ->run();

    }


    public function testStaff(): void
    {
        testRun::testRun($this, 'PUT', "/admin/configuration")
            ->setBody(['Field' => 'CONCOMHOURS', 'Value' => 10])
            ->run();
        $data = testRun::testRun($this, 'GET', '/member/{id}/volunteer/hours')
            ->setUriParts(['id' => 1000])
            ->run();
        $this->assertEquals($data->total_hours, 10);
        testRun::testRun($this, 'PUT', "/admin/configuration")
            ->setBody(['Field' => 'CONCOMHOURS', 'Value' => 20])
            ->run();
        $data = testRun::testRun($this, 'GET', '/member/{id}/volunteer/hours')
            ->setUriParts(['id' => 1000])
            ->run();
        $this->assertEquals($data->total_hours, 20);
        testRun::testRun($this, 'PUT', "/admin/configuration")
            ->setBody(['Field' => 'CONCOMHOURS', 'Value' => 0])
            ->run();

    }


    /* End */
}
