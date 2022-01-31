<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;

class VolunteersTest extends CiabTestCase
{


    private function checkHourEntry($entry, $department = 1)
    {
        $this->assertEquals($entry->type, 'volunteer_hour_entry');
        $this->assertEquals($entry->member->id, 1000);
        $this->assertEquals($entry->authorizer->id, 1000);
        $this->assertEquals($entry->enterer->id, 1000);
        $this->assertEquals($entry->department->id, $department);
        $this->assertObjectHasAttribute('id', $entry->event);
        return($entry->hours * $entry->modifier);

    }


    private function checkHourSummaryEntry($entry)
    {
        $this->assertEquals($entry->type, 'volunteer_hour_summary');
        if (property_exists($entry, 'member')) {
            $this->assertEquals($entry->member->id, 1000);
        } else {
            $this->assertEquals($entry->department->id, 1);
        }
        return ($entry->total_hours);

    }


    private function checkHourList($data, $ids, $entrycheck): void
    {
        $this->assertNotEmpty($data);
        $this->assertEquals($data->type, 'volunteer_hour_entry_list');
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
        $this->runRequest('POST', '/volunteer/hours/', null, null, 400);
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => '1'], 400);
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => '1', 'member' => '1000'], 400);
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => '1', 'member' => '1000',  'enterer' => 1000], 400);
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => '1', 'member' => '1000',  'enterer' => 1000, 'authorizer' => '1000'], 400);
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => '1', 'member' => '1000',  'enterer' => 1000, 'authorizer' => '1000', 'hours' => '2'], 400);
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => '1', 'member' => '1000',  'enterer' => 1000, 'authorizer' => '1000', 'hours' => 'alphebet'], 400);
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => '1', 'member' => '1000',  'enterer' => 1000, 'authorizer' => '1000', 'hours' => 2, 'end' => 'gigglypoof'], 400);
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => 'Spaz', 'member' => 1000,  'enterer' => 1000, 'authorizer' => 1000, 'hours' => 2, 'end' => $when], 404);
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => 1, 'member' => 'asdf',  'enterer' => 1000, 'authorizer' => 1000, 'hours' => 2, 'end' => $when], 404);
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => 1, 'member' => '1000',  'enterer' => -1, 'authorizer' => 1000, 'hours' => 2, 'end' => $when], 404);
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => 1, 'member' => '1000',  'enterer' => 1000, 'authorizer' => 'hello', 'hours' => 2, 'end' => $when], 404);

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/hours/', null, ['department' => '1', 'member' => '1000',  'enterer' => 1000, 'authorizer' => '1000', 'hours' => '2', 'end' => $when], 201);
        $this->assertNotEmpty($data);
        $ids[] = $data->id;

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/hours/', null, ['department' => '1', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '1.2', 'end' => date('Y-m-d h:i:s', strtotime('-2 hour')), 'modifier' => '2.5'], 201);
        $this->assertNotEmpty($data);
        $ids[] = $data->id;

        /* Overlap Fail */
        $this->runRequest('POST', '/volunteer/hours/', null, ['department' => '1', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '2', 'end' => $when, 'modifier' => '1'], 400);

        /* Force overlap success */
        $data = $this->runSuccessJsonRequest('POST', '/volunteer/hours/', ['force' => '1'], ['department' => '1', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '2', 'end' => $when, 'modifier' => '1'], 201);
        $this->assertNotEmpty($data);
        $ids[] = $data->id;

        /* Not overlap success */
        $when = date('Y-m-d h:i:s', strtotime('+4 hour'));
        $data = $this->runSuccessJsonRequest('POST', '/volunteer/hours/', null, ['department' => '1', 'member' => '1000', 'enterer' => '1000', 'authorizer' => '1000', 'hours' => '2', 'end' => $when, 'modifier' => '1'], 201);
        $this->assertNotEmpty($data);
        $ids[] = $data->id;

        $this->runRequest('GET', '/member/1/volunteer/hours', null, null, 404);
        $this->runRequest('GET', '/member/nobody/volunteer/hours', null, null, 404);
        $data = $this->runSuccessJsonRequest('GET', '/member/1000/volunteer/hours');
        $this->assertEquals(count($data->data), count($ids));
        $this->checkHourList($data, $ids, 'checkHourEntry');

        $data = $this->runSuccessJsonRequest('GET', '/member/1000/volunteer/hours/summary');
        $this->assertEquals(count($data->data), 1);
        $this->checkHourList($data, $ids, 'checkHourSummaryEntry');

        $data = $this->runSuccessJsonRequest('GET', '/department/1/volunteer/hours');
        $this->checkHourList($data, $ids, 'checkHourEntry');

        $data = $this->runSuccessJsonRequest('GET', '/department/1/volunteer/hours/summary');
        $this->checkHourList($data, $ids, 'checkHourSummaryEntry');

        $data = $this->runSuccessJsonRequest('GET', '/department/Activities/volunteer/hours/summary');
        $this->checkHourList($data, $ids, 'checkHourSummaryEntry');

        $data = $this->runSuccessJsonRequest('GET', '/event/current/volunteer/hours');
        $this->checkHourList($data, $ids, 'checkHourEntry');

        $data = $this->runSuccessJsonRequest('GET', '/event/current/volunteer/hours/summary');
        $this->checkHourList($data, $ids, 'checkHourSummaryEntry');

        $data = $this->runSuccessJsonRequest('GET', '/volunteer/hours/'.$ids[0]);
        $this->assertNotEmpty($data);
        $this->checkHourEntry($data);

        $data = $this->runSuccessJsonRequest('PUT', "/volunteer/hours/".$ids[1], null, ['department' => '2']);
        $this->checkHourEntry($data, 2);

        foreach ($ids as $id) {
            $this->runSuccessJsonRequest('DELETE', "/volunteer/hours/$id", null, null, 204);
        }

        $this->runRequest('GET', "/volunteer/hours/".$ids[0], null, null, 404);

    }


    public function testPrizes(): void
    {
        $prizes = [];
        $this->runRequest('POST', '/volunteer/rewards', null, null, 400);
        $data = $this->runRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize1'], 400);
        $data = $this->runRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize1', 'inventory' => '100'], 400);
        $data = $this->runRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize1', 'inventory' => '100', 'promo' => 0], 400);
        $data = $this->runRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize1', 'inventory' => '100', 'promo' => 0, 'value' => 'black'], 400);
        $data = $this->runRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize1', 'inventory' => 'green', 'promo' => 0, 'value' => 1.1], 400);
        $data = $this->runSuccessJsonRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize1', 'promo' => 0, 'inventory' => 100, 'value' => 1.0], 201);
        $this->assertNotEmpty($data);
        $id = $data->id;

        $data2 = $this->runSuccessJsonRequest('GET', "/volunteer/rewards/$id");
        $this->assertNotEmpty($data);
        $this->assertEquals($data2, $data);

        $data = $this->runRequest('PUT', "/volunteer/rewards/$id", null, ['inventory' => 'blue'], null, null, 400);
        $data = $this->runRequest('PUT', "/volunteer/rewards/$id", null, ['value' => 'blue'], null, null, 400);
        $data = $this->runSuccessJsonRequest('PUT', "/volunteer/rewards/$id", null, ['name' => 'Prize Alpha', 'inventory' => 5]);
        $this->assertNotEmpty($data);
        $this->assertEquals($data->name, 'Prize Alpha');
        $this->assertEquals($data->inventory, 5);

        $data = $this->runSuccessJsonRequest('PUT', "/volunteer/rewards/$id/inventory", null, ['difference' => 5]);
        $this->assertNotEmpty($data);
        $this->assertEquals($data->inventory, 10);

        $data = $this->runSuccessJsonRequest('PUT', "/volunteer/rewards/$id/inventory", null, ['difference' => -2]);
        $this->assertNotEmpty($data);
        $this->assertEquals($data->inventory, 8);
        $data2 = $data;

        $data = $this->runSuccessJsonRequest('GET', "/volunteer/rewards");
        $this->assertNotEmpty($data);
        $this->assertEquals(count($data->data), 1);
        $this->assertEquals($data->data[0], $data2);

        $this->runSuccessJsonRequest('DELETE', "/volunteer/rewards/$id", null, null, 204);

        /* Reward Groups */

        $data = $this->runRequest('POST', '/volunteer/reward_group/', null, ['reward_limit' => 'blue'], 400);
        $data = $this->runSuccessJsonRequest('POST', '/volunteer/reward_group/', null, ['reward_limit' => 1], 201);
        $gid = $data->id;
        $this->assertEquals($data->reward_limit, 1);

        $data2 = $this->runSuccessJsonRequest('GET', "/volunteer/reward_group/$gid");
        $this->assertNotEmpty($data);
        $this->assertEquals($data, $data2);

        $data = $this->runRequest('PUT', "/volunteer/reward_group/$gid", null, ['reward_limit' => 'green'], 400);
        $data = $this->runSuccessJsonRequest('PUT', "/volunteer/reward_group/$gid", null, ['reward_limit' => '2']);
        $this->assertNotEmpty($data);
        $this->assertEquals($data->reward_limit, 2);

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize A', 'promo' => 0, 'inventory' => 100, 'value' => 1.0, 'reward_group' => $gid], 201);
        $pid1 = $data->id;

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize B', 'promo' => 0, 'inventory' => 100, 'value' => 1.0, 'reward_group' => $gid], 201);
        $pid2 = $data->id;

        $data = $this->runSuccessJsonRequest('GET', "/volunteer/reward_group/$gid");
        $this->assertNotEmpty($data);
        $this->assertEquals(count($data->data), 2);

        $this->runSuccessJsonRequest('DELETE', "/volunteer/reward_group/$gid", null, null, 204);

        $data = $this->runSuccessJsonRequest('GET', "/volunteer/rewards/$pid1");
        $this->assertNotEmpty($data);
        $this->assertEquals($data->reward_group, null);

        $this->runSuccessJsonRequest('DELETE', "/volunteer/rewards/$pid1", null, null, 204);
        $this->runSuccessJsonRequest('DELETE', "/volunteer/rewards/$pid2", null, null, 204);

    }


    public function testClaims(): void
    {
        $hours = [];
        $claims = [];
        $rewards = [];

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize1', 'promo' => 0, 'inventory' => 100, 'value' => 1.1], 201);
        $this->assertNotEmpty($data);
        $rewards[] = $data->id;

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize2', 'promo' => 0, 'inventory' => 100, 'value' => 2.2], 201);
        $this->assertNotEmpty($data);
        $rewards[] = $data->id;

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize3', 'promo' => 1, 'inventory' => 100, 'value' => 2], 201);
        $this->assertNotEmpty($data);
        $rewards[] = $data->id;

        $this->runRequest('POST', '/volunteer/claims', null, ['member' => '1000', 'reward' => $rewards[0]], 400);

        $when = date('Y-m-d h:i:s', strtotime('+4 hour'));
        $data = $this->runSuccessJsonRequest('POST', '/volunteer/hours/', null, ['department' => '1', 'member' => '1000',  'enterer' => 1000, 'authorizer' => '1000', 'hours' => '2', 'end' => $when], 201);
        $this->assertNotEmpty($data);
        $hours[] = $data->id;

        $this->runRequest('POST', '/volunteer/claims', null, ['member' => '1000', 'reward' => $rewards[1]], 400);

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/claims', null, ['member' => '1000', 'reward' => $rewards[0]], 201);
        $this->assertNotEmpty($data);
        $claims[] = $data;

        $this->runRequest('POST', '/volunteer/claims', null, ['member' => '1000', 'reward' => $rewards[0]], 400);

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/claims', null, ['member' => '1000', 'reward' => $rewards[2]], 201);
        $this->assertNotEmpty($data);
        $claims[] = $data;

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/claims', null, ['member' => '1000', 'reward' => $rewards[2]], 201);
        $this->assertNotEmpty($data);
        $claims[] = $data;

        $data = $this->runSuccessJsonRequest('PUT', "/volunteer/rewards/$rewards[2]", null, ['inventory' => 0]);
        $this->assertNotEmpty($data);

        $this->runRequest('POST', '/volunteer/claims', null, ['member' => '1000', 'reward' => $rewards[2]], 400);

        $data = $this->runSuccessJsonRequest('GET', '/volunteer/claims/'.$claims[0]->id);
        $this->assertNotEmpty($data);
        $claims[0]->reward->inventory = 98;
        $claims[0]->reward->claimed = 2;
        $this->assertEquals($claims[0], $data);

        $data = $this->runSuccessJsonRequest('GET', '/volunteer/claims/'.$claims[1]->id);
        $this->assertNotEmpty($data);
        $claims[1]->reward->inventory = 0;
        $claims[1]->reward->claimed = 2;
        $this->assertEquals($claims[1], $data);

        $data = $this->runSuccessJsonRequest('GET', "/member/1000/volunteer/claims");
        $this->assertNotEmpty($data);
        $this->assertEquals(count($data->data), 3);

        $data = $this->runSuccessJsonRequest('GET', "/member/1000/volunteer/claims/summary");
        $this->assertNotEmpty($data);
        $this->assertEquals($data->spent_hours, 1.1);
        $this->assertEquals($data->reward_count, 3);

        /* Reward Groups */
        $data = $this->runSuccessJsonRequest('POST', '/volunteer/reward_group/', null, ['reward_limit' => 1], 201);
        $this->assertNotEmpty($data);
        $this->assertEquals($data->reward_limit, 1);
        $group_id = $data->id;

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize4', 'promo' => 1, 'inventory' => 100, 'value' => 2, 'reward_group' => $group_id], 201);
        $this->assertNotEmpty($data);
        $target = $data->id;
        $rewards[] = $data->id;

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize5', 'promo' => 1, 'inventory' => 100, 'value' => 2, 'reward_group' => $group_id], 201);
        $this->assertNotEmpty($data);
        $target2 = $data->id;
        $rewards[] = $data->id;

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/claims', null, ['member' => '1000', 'reward' => $target], 201);
        $this->assertNotEmpty($data);
        $claims[] = $data;

        $data = $this->runRequest('POST', '/volunteer/claims', null, ['member' => '1000', 'reward' => $target], 400);

        $data = $this->runSuccessJsonRequest('POST', '/volunteer/rewards', null, ['name' => 'Prize5', 'promo' => 1, 'inventory' => 100, 'value' => 2, 'reward_group' => $group_id], 201);
        $this->assertNotEmpty($data);
        $target = $data->id;
        $rewards[] = $data->id;

        $data = $this->runRequest('POST', '/volunteer/claims', null, ['member' => '1000', 'reward' => $target], 400);

        $data = $this->runSuccessJsonRequest('PUT', '/volunteer/claims/'.end($claims)->id, null, ['reward' => $target], 200);
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


    /* End */
}
