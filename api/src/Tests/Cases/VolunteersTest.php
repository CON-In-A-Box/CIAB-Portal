<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;

class VolunteersTest extends CiabTestCase
{

    protected $staff_hours = 0;


    protected function setUp(): void
    {
        parent::setUp();
        $value = $this->runSuccessJsonRequest('GET', "/admin/configuration/CONCOMHOURS");
        $this->staff_hours = $value->value;
        $this->runSuccessJsonRequest('PUT', "/admin/configuration", null, ['Field' => 'CONCOMHOURS', 'Value' => 0]);

    }


    protected function tearDown(): void
    {
        $this->runSuccessJsonRequest('PUT', "/admin/configuration", null, ['Field' => 'CONCOMHOURS', 'Value' => $this->staff_hours]);
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
        $this->runRequest('POST', '/volunteer/hours/', null, null, 400, null, '/volunteer/hours');
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => '2'], 400, null, '/volunteer/hours');
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => '2', 'member' => '1000'], 400, null, '/volunteer/hours');
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => '2', 'member' => '1000',  'enterer' => 1000], 400, null, '/volunteer/hours');
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => '2', 'member' => '1000',  'enterer' => 1000, 'authorizer' => '1000'], 400, null, '/volunteer/hours');
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => '2', 'member' => '1000',  'enterer' => 1000, 'authorizer' => '1000', 'hours' => '2'], 400, null, '/volunteer/hours');
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => '2', 'member' => '1000',  'enterer' => 1000, 'authorizer' => '1000', 'hours' => 'alphebet'], 400, null, '/volunteer/hours');
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => '2', 'member' => '1000',  'enterer' => 1000, 'authorizer' => '1000', 'hours' => 2, 'end' => 'gigglypoof'], 400, null, '/volunteer/hours');
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => 'Spaz', 'member' => 1000,  'enterer' => 1000, 'authorizer' => 1000, 'hours' => 2, 'end' => $when], 404, null, '/volunteer/hours');
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => 2, 'member' => 'asdf',  'enterer' => 1000, 'authorizer' => 1000, 'hours' => 2, 'end' => $when], 404, null, '/volunteer/hours');
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => 2, 'member' => '1000',  'enterer' => -1, 'authorizer' => 1000, 'hours' => 2, 'end' => $when], 404, null, '/volunteer/hours');
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => 2, 'member' => '1000',  'enterer' => 1000, 'authorizer' => 'hello', 'hours' => 2, 'end' => $when], 404, null, '/volunteer/hours');

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/hours/', null, ['department' => '2', 'member' => '1000',  'enterer' => 1000, 'authorizer' => '1000', 'hours' => '2', 'end' => $when], 201, null, '/volunteer/hours');
        $this->assertNotEmpty($data);
        $ids[] = $data->id;

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/hours/', null, ['department' => '2', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '1.2', 'end' => date('Y-m-d h:i:s', strtotime('-2 hour')), 'modifier' => '2.5'], 201, null, '/volunteer/hours');
        $this->assertNotEmpty($data);
        $ids[] = $data->id;

        $this->runRequest('GET', '/member/1/volunteer/hours', null, null, 404, null, '/member/{id}/volunteer/hours');
        $this->runRequest('GET', '/member/nobody/volunteer/hours', null, null, 404, null, '/member/{id}/volunteer/hours');
        $data = $this->runSuccessJsonRequest('GET', '/member/1000/volunteer/hours', null, null, 200, null, '/member/{id}/volunteer/hours');
        $this->assertEquals(count($data->data), count($ids));
        $this->checkHourList($data, $ids, 'checkHourEntry');

        $data = $this->runSuccessJsonRequest('GET', '/member/1000/volunteer/hours/summary', null, null, 200, null);//, '/member/{id}/volunteer/hours/summary');
        $this->assertEquals(count($data->data), 1);
        $this->checkHourList($data, $ids, 'checkHourSummaryEntry');

        $data = $this->runSuccessJsonRequest('GET', '/department/2/volunteer/hours', null, null, 200, null, '/department/{id}/volunteer/hours');
        $this->checkHourList($data, $ids, 'checkHourEntry');

        $data = $this->runSuccessJsonRequest('GET', '/department/2/volunteer/hours/summary', null, null, 200, null, '/department/{id}/volunteer/hours/summary');
        $this->checkHourList($data, $ids, 'checkHourSummaryEntry');

        $data = $this->runSuccessJsonRequest('GET', '/department/Activities/volunteer/hours/summary', null, null, 200, null, '/department/{id}/volunteer/hours/summary');
        $this->checkHourList($data, $ids, 'checkHourSummaryEntry');

        $data = $this->runSuccessJsonRequest('GET', '/event/current/volunteer/hours', null, null, 200, null, '/event/{id}/volunteer/hours');
        $this->checkHourList($data, $ids, 'checkHourEntry');

        $data = $this->runSuccessJsonRequest('GET', '/event/current/volunteer/hours/summary', null, null, 200, null, '/event/{id}/volunteer/hours/summary');
        $this->checkHourList($data, $ids, 'checkHourSummaryEntry');

        $data = $this->runSuccessJsonRequest('GET', '/volunteer/hours/'.$ids[0], null, null, 200, null, '/volunteer/hours/{id}');
        $this->assertNotEmpty($data);
        $this->checkHourEntry($data);

        $data = $this->runSuccessJsonRequest('PUT', "/volunteer/hours/".$ids[1], null, ['department' => '2'], 200, null, "/volunteer/hours/{id}");
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
        $data = $this->runSuccessJsonRequest('POST', '/volunteer/hours/', null, ['department' => '2', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '1', 'end' => $base, 'modifier' => '1'], 201, null, '/volunteer/hours');
        $this->assertNotEmpty($data);
        $ids[] = $data->id;

        /* Identical */
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => '2', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '1', 'end' => $base, 'modifier' => '1'], 400, null, '/volunteer/hours');

        /* Overlap */
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => '2', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '1', 'end' => $plusMin, 'modifier' => '1'], 400, null, '/volunteer/hours');
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => '2', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '1', 'end' => $minusMin, 'modifier' => '1'], 400, null, '/volunteer/hours');

        /* Superset, subset */
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => '2', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '8', 'end' => $largeEnd, 'modifier' => '1'], 400, null, '/volunteer/hours');
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => '2', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '0.1', 'end' => $subEnd, 'modifier' => '1'], 400, null, '/volunteer/hours');

        /* Edges, success */
        $data = $this->runSuccessJsonRequest('POST', '/volunteer/hours/', null, ['department' => '2', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '1', 'end' => $plusHour, 'modifier' => '1'], 201, null, '/volunteer/hours');
        $this->assertNotEmpty($data);
        $ids[] = $data->id;

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/hours/', null, ['department' => '2', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '1', 'end' => $minusHour, 'modifier' => '1'], 201, null, '/volunteer/hours');
        $this->assertNotEmpty($data);
        $ids[] = $data->id;


        /* Force overlap success */
        $data = $this->runSuccessJsonRequest('POST', '/volunteer/hours/', ['force' => '1'], ['department' => '2', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '2', 'end' => $base, 'modifier' => '1'], 201, null, '/volunteer/hours');
        $this->assertNotEmpty($data);
        $ids[] = $data->id;

        /* Not overlap success */
        $when = date('Y-m-d h:i:s', strtotime('+4 hour'));
        $data = $this->runSuccessJsonRequest('POST', '/volunteer/hours/', null, ['department' => '2', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '2', 'end' => $when, 'modifier' => '1'], 201, null, '/volunteer/hours');
        $this->assertNotEmpty($data);
        $ids[] = $data->id;

        foreach ($ids as $id) {
            $this->runSuccessJsonRequest('DELETE', "/volunteer/hours/$id", null, null, 204, null, '/volunteer/hours/{id}');
        }

        $this->runRequest('GET', "/volunteer/hours/".$ids[0], null, null, 404, null, '/volunteer/hours/{id}');

    }


    public function testPrizes(): void
    {
        $prizes = [];
        $this->runRequest('POST', '/volunteer/rewards', null, null, 400, null, '/volunteer/rewards');
        $data = $this->runRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize1'], 400, null, '/volunteer/rewards');
        $data = $this->runRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize1', 'inventory' => '100'], 400, null, '/volunteer/rewards');
        $data = $this->runRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize1', 'inventory' => '100', 'promo' => 0], 400, null, '/volunteer/rewards');
        $data = $this->runRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize1', 'inventory' => '100', 'promo' => 0, 'value' => 'black'], 400, null, '/volunteer/rewards');
        $data = $this->runRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize1', 'inventory' => 'green', 'promo' => 0, 'value' => 1.1], 400, null, '/volunteer/rewards');
        $data = $this->runSuccessJsonRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize1', 'promo' => 0, 'inventory' => 100, 'value' => 1.0], 201, null, '/volunteer/rewards');
        $this->assertNotEmpty($data);
        $id = $data->id;

        $data2 = $this->runSuccessJsonRequest('GET', "/volunteer/rewards/$id", null, null, 200, null, '/volunteer/rewards/{id}');
        $this->assertNotEmpty($data);
        $this->assertEquals($data2, $data);

        $data = $this->runRequest('PUT', "/volunteer/rewards/$id", null, ['inventory' => 'blue'], 400, null, '/volunteer/rewards/{id}');
        $data = $this->runRequest('PUT', "/volunteer/rewards/$id", null, ['value' => 'blue'], 400, null, '/volunteer/rewards/{id}');
        $data = $this->runSuccessJsonRequest('PUT', "/volunteer/rewards/$id", null, ['name' => 'Prize Alpha', 'inventory' => 5], 200, null, '/volunteer/rewards/{id}');
        $this->assertNotEmpty($data);
        $this->assertEquals($data->name, 'Prize Alpha');
        $this->assertEquals($data->inventory, 5);

        $data = $this->runSuccessJsonRequest('PUT', "/volunteer/rewards/$id/inventory", null, ['difference' => 5], 200, null, '/volunteer/rewards/{id}/inventory');
        $this->assertNotEmpty($data);
        $this->assertEquals($data->inventory, 10);

        $data = $this->runSuccessJsonRequest('PUT', "/volunteer/rewards/$id/inventory", null, ['difference' => -2], 200, null, '/volunteer/rewards/{id}/inventory');
        $this->assertNotEmpty($data);
        $this->assertEquals($data->inventory, 8);
        $data2 = $data;

        $data = $this->runSuccessJsonRequest('GET', "/volunteer/rewards", null, null, 200, null, '/volunteer/rewards');
        $this->assertNotEmpty($data);
        $this->assertEquals(count($data->data), 1);
        $this->assertEquals($data->data[0], $data2);

        $this->runSuccessJsonRequest('DELETE', "/volunteer/rewards/$id", null, null, 204, null, '/volunteer/rewards/{id}');

        /* Reward Groups */

        $data = $this->runRequest('POST', '/volunteer/reward_group/', null, ['reward_limit' => 'blue'], 400, null, '/volunteer/reward_group');
        $data = $this->runSuccessJsonRequest('POST', '/volunteer/reward_group/', null, ['reward_limit' => 1], 201, null, '/volunteer/reward_group');
        $gid = $data->id;
        $this->assertEquals($data->reward_limit, 1);

        $data2 = $this->runSuccessJsonRequest('GET', "/volunteer/reward_group/$gid", null, null, 200, null, '/volunteer/reward_group/{id}');
        $this->assertNotEmpty($data);
        $this->assertEquals($data, $data2);

        $data = $this->runRequest('PUT', "/volunteer/reward_group/$gid", null, ['reward_limit' => 'green'], 400, null, '/volunteer/reward_group/{id}');
        $data = $this->runSuccessJsonRequest('PUT', "/volunteer/reward_group/$gid", null, ['reward_limit' => '2'], 200, null, '/volunteer/reward_group/{id}');
        $this->assertNotEmpty($data);
        $this->assertEquals($data->reward_limit, 2);

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize A', 'promo' => 0, 'inventory' => 100, 'value' => 1.0, 'reward_group' => $gid], 201, null, '/volunteer/rewards');
        $pid1 = $data->id;

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize B', 'promo' => 0, 'inventory' => 100, 'value' => 1.0, 'reward_group' => $gid], 201, null, '/volunteer/rewards');
        $pid2 = $data->id;

        $data = $this->runSuccessJsonRequest('GET', "/volunteer/reward_group/$gid/list", null, null, 200, null, '/volunteer/reward_group/{id}/list');
        $this->assertNotEmpty($data);
        $this->assertEquals(count($data->data), 2);

        $this->runSuccessJsonRequest('DELETE', "/volunteer/reward_group/$gid", null, null, 204, null, '/volunteer/reward_group/{id}');

        $data = $this->runSuccessJsonRequest('GET', "/volunteer/rewards/$pid1", null, null, 200, null, '/volunteer/rewards/{id}');
        $this->assertNotEmpty($data);
        $this->assertEquals($data->reward_group, null);

        $this->runSuccessJsonRequest('DELETE', "/volunteer/rewards/$pid1", null, null, 204, null, '/volunteer/rewards/{id}');
        $this->runSuccessJsonRequest('DELETE', "/volunteer/rewards/$pid2", null, null, 204, null, '/volunteer/rewards/{id}');

    }


    public function testClaims(): void
    {
        $hours = [];
        $claims = [];
        $rewards = [];

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize1', 'promo' => 0, 'inventory' => 100, 'value' => 1.1], 201, null, '/volunteer/rewards');
        $this->assertNotEmpty($data);
        $rewards[] = $data->id;

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize2', 'promo' => 0, 'inventory' => 100, 'value' => 2.2], 201, null, '/volunteer/rewards');
        $this->assertNotEmpty($data);
        $rewards[] = $data->id;

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize3', 'promo' => 1, 'inventory' => 100, 'value' => 2], 201, null, '/volunteer/rewards');
        $this->assertNotEmpty($data);
        $rewards[] = $data->id;

        $this->runRequest('POST', '/volunteer/claims', null, ['member' => '1000', 'reward' => $rewards[0]], 400, null, '/volunteer/claims');

        $when = date('Y-m-d h:i:s', strtotime('+4 hour'));
        $data = $this->runSuccessJsonRequest('POST', '/volunteer/hours/', null, ['department' => '2', 'member' => '1000',  'enterer' => 1000, 'authorizer' => '1000', 'hours' => '2', 'end' => $when], 201, null, '/volunteer/hours');
        $this->assertNotEmpty($data);
        $hours[] = $data->id;

        $this->runRequest('POST', '/volunteer/claims', null, ['member' => '1000', 'reward' => $rewards[1]], 400, null, '/volunteer/claims');

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/claims', null, ['member' => '1000', 'reward' => $rewards[0]], 201, null, '/volunteer/claims');
        $this->assertNotEmpty($data);
        $claims[] = $data;

        $this->runRequest('POST', '/volunteer/claims', null, ['member' => '1000', 'reward' => $rewards[0]], 400, null, '/volunteer/claims');

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/claims', null, ['member' => '1000', 'reward' => $rewards[2]], 201, null, '/volunteer/claims');
        $this->assertNotEmpty($data);
        $claims[] = $data;

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/claims', null, ['member' => '1000', 'reward' => $rewards[2]], 201, null, '/volunteer/claims');
        $this->assertNotEmpty($data);
        $claims[] = $data;

        $data = $this->runSuccessJsonRequest('PUT', "/volunteer/rewards/$rewards[2]", null, ['inventory' => 0], 200, null, '/volunteer/rewards/{id}');
        $this->assertNotEmpty($data);

        $this->runRequest('POST', '/volunteer/claims', null, ['member' => '1000', 'reward' => $rewards[2]], 400, null, '/volunteer/claims');

        $data = $this->runSuccessJsonRequest('GET', '/volunteer/claims/'.$claims[0]->id, null, null, 200, null, '/volunteer/claims/{id}');
        $this->assertNotEmpty($data);
        $claims[0]->reward->inventory = 99;
        $claims[0]->reward->claimed = 1;
        $this->assertEquals($claims[0], $data);

        $data = $this->runSuccessJsonRequest('GET', '/volunteer/claims/'.$claims[1]->id, null, null, 200, null, '/volunteer/claims/{id}');
        $this->assertNotEmpty($data);
        $claims[1]->reward->inventory = 0;
        $claims[1]->reward->claimed = 2;
        $this->assertEquals($claims[1], $data);

        $data = $this->runSuccessJsonRequest('GET', "/member/1000/volunteer/claims", null, null, 200, null, '/member/{id}/volunteer/claims');
        $this->assertNotEmpty($data);
        $this->assertEquals(count($data->data), 3);

        $data = $this->runSuccessJsonRequest('GET', "/member/1000/volunteer/claims/summary", null, null, 200, null, '/member/{id}/volunteer/claims/summary');
        $this->assertNotEmpty($data);
        $this->assertEquals($data->spent_hours, 1.1);
        $this->assertEquals($data->reward_count, 3);

        /* Reward Groups */
        $data = $this->runSuccessJsonRequest('POST', '/volunteer/reward_group/', null, ['reward_limit' => 1], 201, null, '/volunteer/reward_group');
        $this->assertNotEmpty($data);
        $this->assertEquals($data->reward_limit, 1);
        $group_id = $data->id;

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize4', 'promo' => 1, 'inventory' => 100, 'value' => 2, 'reward_group' => $group_id], 201, null, '/volunteer/rewards');
        $this->assertNotEmpty($data);
        $target = $data->id;
        $rewards[] = $data->id;

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize5', 'promo' => 1, 'inventory' => 100, 'value' => 2, 'reward_group' => $group_id], 201, null, '/volunteer/rewards');
        $this->assertNotEmpty($data);
        $target2 = $data->id;
        $rewards[] = $data->id;

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/claims', null, ['member' => '1000', 'reward' => $target], 201, null, '/volunteer/claims');
        $this->assertNotEmpty($data);
        $claims[] = $data;

        $data = $this->runRequest('POST', '/volunteer/claims', null, ['member' => '1000', 'reward' => $target], 400, null, '/volunteer/claims');

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize5', 'promo' => 1, 'inventory' => 100, 'value' => 2, 'reward_group' => $group_id], 201, null, '/volunteer/rewards');
        $this->assertNotEmpty($data);
        $target = $data->id;
        $rewards[] = $data->id;

        $data = $this->runRequest('POST', '/volunteer/claims', null, ['member' => '1000', 'reward' => $target], 400, null, '/volunteer/claims');

        $data = $this->runSuccessJsonRequest('PUT', '/volunteer/claims/'.end($claims)->id, null, ['reward' => $target], 200, null, '/volunteer/claims/{id}');
        $this->assertNotEmpty($data);

        foreach ($claims as $claim) {
            $this->runSuccessJsonRequest('DELETE', "/volunteer/claims/".$claim->id, null, null, 204);
        }
        foreach ($rewards as $id) {
            $this->runSuccessJsonRequest('DELETE', "/volunteer/rewards/$id", null, null, 204);
        }
        foreach ($hours as $id) {
            $this->runSuccessJsonRequest('DELETE', "/volunteer/hours/$id", null, null, 204);
        }
        $this->runSuccessJsonRequest('DELETE', "/volunteer/reward_group/$group_id", null, null, 204);

    }


    public function testStaff(): void
    {
        $this->runSuccessJsonRequest('PUT', "/admin/configuration", null, ['Field' => 'CONCOMHOURS', 'Value' => 10], 200, null, '/admin/configuration');
        $data = $this->runSuccessJsonRequest('GET', '/member/1000/volunteer/hours', null, null, 200, null, '/member/{id}/volunteer/hours');
        $this->assertEquals($data->total_hours, 10);
        $this->runSuccessJsonRequest('PUT', "/admin/configuration", null, ['Field' => 'CONCOMHOURS', 'Value' => 20], 200, null, '/admin/configuration');
        $data = $this->runSuccessJsonRequest('GET', '/member/1000/volunteer/hours', null, null, 200, null, '/member/{id}/volunteer/hours');
        $this->assertEquals($data->total_hours, 20);
        $this->runSuccessJsonRequest('PUT', "/admin/configuration", null, ['Field' => 'CONCOMHOURS', 'Value' => 0], 200, null, '/admin/configuration');

    }


    /* End */
}
