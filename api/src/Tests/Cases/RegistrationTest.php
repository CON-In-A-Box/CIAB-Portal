<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;

class RegistrationTest extends CiabTestCase
{

    private $ticket = null;


    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if (!array_key_exists('REMOTE_ADDR', $_SERVER)) {
            $_SERVER['REMOTE_ADDR'] = '10.10.0.0';
        }

    }


    protected function setUp(): void
    {
        parent::setUp();

    }


    protected function tearDown(): void
    {
        if ($this->ticket) {
            $this->runRequest('DELETE', '/registration/ticket/'.$this->ticket->id, null, null, 204);
        }
        parent::tearDown();

    }


    public function testOpen(): void
    {
        $this->runSuccessJsonRequest('GET', '/registration/open');
        $this->runRequest('GET', '/registration/open/-1', null, null, 404);

    }


    public function testAdmin(): void
    {
        $this->runSuccessJsonRequest('GET', '/registration/admin');
        $this->runSuccessJsonRequest('GET', '/registration/configuration');
        $this->runRequest('GET', '/registration/configuration/nothing', null, null, 404);
        $this->runRequest('PUT', '/registration/configuration', null, null, 400);
        $this->runSuccessJsonRequest('PUT', '/registration/configuration', null, ['Field' => 'ForceOpen', 'Value' => '1']);

    }


    public function testTicket(): void
    {
        $this->runRequest('GET', '/registration/ticket/type/-1', null, null, 404);
        $type = $this->runSuccessJsonRequest('GET', '/registration/ticket/type');
        $data = $this->runSuccessJsonRequest('GET', '/registration/ticket/type/'.$type->id);
        $this->assertEquals($type, $data);

        $this->runRequest('POST', '/registration/ticket', null, null, 400);
        $this->runRequest('POST', '/registration/ticket', null, ['bad' => 'body'], 400);
        $this->runRequest('POST', '/registration/ticket', null, ['member' => self::$login], 400);

        $this->runRequest('POST', '/registration/ticket', null, ['member' => 'nobody', 'ticket_type' => $type->id], 404);
        $this->runRequest('POST', '/registration/ticket', null, ['member' => self::$login, 'ticket_type' => '-1'], 404);
        $this->runRequest('POST', '/registration/ticket', null, ['member' => self::$login, 'ticket_type' => $type->id, 'event' => '-1'], 404);
        $this->runRequest('POST', '/registration/ticket', null, ['member' => self::$login, 'ticket_type' => $type->id, 'badge_dependent_on' => '-1'], 404);
        $this->runRequest('POST', '/registration/ticket', null, ['member' => self::$login, 'ticket_type' => $type->id, 'registered_by' => '-1'], 404);

        $this->ticket = $this->runSuccessJsonRequest('POST', '/registration/ticket', null, ['member' => self::$login, 'ticket_type' => $type->id, 'badge_id' => '999'], 201);

        $data = $this->runSuccessJsonRequest('GET', '/registration/ticket/'.$this->ticket->id);
        $this->assertIncludes($data, 'event');
        $this->assertIncludes($data, 'registered_by');
        $this->assertIncludes($data, 'ticket_type');
        $this->assertIncludes($data, 'member');
        $this->assertEquals($data->badge_id, '999');

        $data = $this->runSuccessJsonRequest('GET', '/member/find', ['q' => '999', 'from' => 'badge_id']);
        $this->assertEquals($data->type, 'member_list');
        $this->assertNotEmpty($data->data);
        $this->assertEquals(count($data->data), 1);
        $this->assertEquals($data->data[0]->id, 1000);

        /* PUT */

        $this->runRequest('PUT', '/registration/ticket/'.$this->ticket->id, null, null, 400);
        $this->runRequest('PUT', '/registration/ticket/'.$this->ticket->id, null, [], 400);
        $this->runRequest('PUT', '/registration/ticket/'.$this->ticket->id, null, ['bully' => 'frog'], 400);
        $this->runRequest('PUT', '/registration/ticket/-1', null, ['badge_name' => 'badggy', 'emergency_contact' => 'Mars', 'note'  => 'something important'], 404);

        $data = $this->runSuccessJsonRequest('PUT', '/registration/ticket/'.$this->ticket->id, null, ['badge_name' => 'badggy', 'emergency_contact' => 'Mars', 'note'  => 'something important']);
        $this->assertEquals($data->badge_name, 'badggy');
        $this->assertEquals($data->emergency_contact, 'Mars');
        $this->assertEquals($data->note, 'something important');

        $data = $this->runSuccessJsonRequest('GET', '/member/find', ['q' => 'badggy', 'from' => 'badge']);
        $this->assertEquals($data->type, 'member_list');
        $this->assertNotEmpty($data->data);
        $this->assertEquals(count($data->data), 1);
        $this->assertEquals($data->data[0]->id, 1000);

        $this->runRequest('GET', '/registration/ticket/list/-1', null, null, 404);
        $this->runSuccessJsonRequest('GET', '/registration/ticket/list');

        $this->runRequest('PUT', '/registration/ticket/-1/email', null, null, 404);
        $this->runRequest('PUT', '/registration/ticket/'.$this->ticket->id.'/email', null, null, 200);
        $this->runRequest('PUT', '/registration/ticket/-1/checkin', null, null, 404);
        $this->runRequest('PUT', '/registration/ticket/'.$this->ticket->id.'/checkin', null, null, 200);
        $this->runRequest('PUT', '/registration/ticket/-1/pickup', null, null, 404);
        $this->runRequest('PUT', '/registration/ticket/'.$this->ticket->id.'/pickup', null, null, 200);

        $this->runRequest('PUT', '/registration/ticket/-1/print', null, null, 404);
        $this->runRequest('PUT', '/registration/ticket/'.$this->ticket->id.'/print', null, null, 200);
        $this->runRequest('PUT', '/registration/ticket/-1/lost', null, null, 404);
        $this->runRequest('PUT', '/registration/ticket/'.$this->ticket->id.'/lost', null, null, 200);
        $this->runRequest('PUT', '/registration/ticket/'.$this->ticket->id.'/void', null, null, 400);
        $this->runRequest('PUT', '/registration/ticket/-1/void', null, ['reason' => 'PHP Test'], 404);
        $this->runRequest('PUT', '/registration/ticket/'.$this->ticket->id.'/void', null, ['reason' => 'PHP Test'], 200);
        $this->runRequest('PUT', '/registration/ticket/-1/reinstate', null, null, 409);
        $this->runRequest('PUT', '/registration/ticket/'.$this->ticket->id.'/reinstate', null, null, 200);

        $this->runRequest('GET', '/registration/ticket/printqueue/-1', null, null, 404);
        $queue = $this->runSuccessJsonRequest('get', '/registration/ticket/printqueue');
        $this->runRequest('PUT', '/registration/ticket/printqueue/claim/-1', null, null, 409);
        $this->runSuccessJsonRequest('PUT', '/registration/ticket/printqueue/claim/'.$queue->data[0]->id);

        /* rebuild ticket */
        $this->runRequest('DELETE', '/registration/ticket/'.$this->ticket->id, null, null, 204);
        $this->ticket = $this->runSuccessJsonRequest('POST', '/registration/ticket', null, ['member' => self::$login, 'ticket_type' => $type->id], 201);

        $this->runRequest('PUT', '/registration/ticket/'.$this->ticket->id.'/void', null, ['reason' => 'PHP Test'], 200);

        $this->runRequest('GET', '/registration/ticket/'.$this->ticket->id, null, null, 404);
        $this->runRequest('GET', '/registration/ticket/'.$this->ticket->id, ['show_void' => '1'], null, 200);
        $this->runRequest('PUT', '/registration/ticket/'.$this->ticket->id.'/email', null, null, 404);
        $this->runRequest('PUT', '/registration/ticket/'.$this->ticket->id.'/checkin', null, null, 409);
        $this->runRequest('PUT', '/registration/ticket/'.$this->ticket->id.'/print', null, null, 404);
        $this->runRequest('PUT', '/registration/ticket/'.$this->ticket->id.'/pickup', null, null, 409);
        $this->runRequest('PUT', '/registration/ticket/'.$this->ticket->id.'/lost', null, null, 409);
        $this->runRequest('PUT', '/registration/ticket/'.$this->ticket->id.'/reinstate', null, null, 200);



        $this->runRequest('DELETE', '/registration/ticket/-1', null, null, 404);

    }


/* End */
}
