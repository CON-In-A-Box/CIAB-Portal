<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;
use Atlas\Query\Insert;
use Atlas\Query\Delete;

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


    /* End */
}
