<?php

namespace App\Tests\TestCase\Controller;

use App\Tests\Base\CiabTestCase;
use App\Tests\Base\TestRun;

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
            testRun::testRun($this, 'DELETE', '/registration/ticket/{id}')
                ->setUriParts(['id' => $this->ticket->id])
                ->run();
        }
        parent::tearDown();

    }


    public function testOpen(): void
    {
        testRun::testRun($this, 'GET', '/registration/open');

    }


    public function testAdmin(): void
    {
        testRun::testRun($this, 'GET', '/registration/admin')->run();
        testRun::testRun($this, 'GET', '/registration/configuration')->run();
        testRun::testRun($this, 'GET', '/registration/configuration/{field}')
            ->setUriParts(['field' => 'nothing'])
            ->setExpectedResult(404)
            ->run();
        testRun::testRun($this, 'PUT', '/registration/configuration')
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'PUT', '/registration/configuration')
            ->setBody(['Field' => 'ForceOpen', 'Value' => '1'])
            ->run();

    }


    public function testTicket(): void
    {
        testRun::testRun($this, 'GET', '/registration/ticket/type/{id}')
            ->setUriParts(['id' => '-1'])
            ->setExpectedResult(404)
            ->run();
        $type = testRun::testRun($this, 'GET', '/registration/ticket/type')->setVerifyYaml(false)->run();
        $data = testRun::testRun($this, 'GET', '/registration/ticket/type/{id}')
            ->setUriParts(['id' => $type->id])
            ->run();
        $this->assertEquals($type, $data);

        testRun::testRun($this, 'POST', '/registration/ticket')
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'POST', '/registration/ticket')
            ->setBody(['bad' => 'body'])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'POST', '/registration/ticket')
            ->setBody(['member' => self::$login])
            ->setExpectedResult(400)
            ->run();

        testRun::testRun($this, 'POST', '/registration/ticket')
            ->setBody(['member' => 'nobody', 'ticket_type' => $type->id])
            ->setExpectedResult(404)
            ->run();
        testRun::testRun($this, 'POST', '/registration/ticket')
            ->setBody(['member' => self::$login, 'ticket_type' => '-1'])
            ->setExpectedResult(404)
            ->run();
        testRun::testRun($this, 'POST', '/registration/ticket')
            ->setBody(['member' => self::$login, 'ticket_type' => $type->id, 'event' => '-1'])
            ->setExpectedResult(404)
            ->run();
        testRun::testRun($this, 'POST', '/registration/ticket')
            ->setBody(['member' => self::$login, 'ticket_type' => $type->id, 'badge_dependent_on' => '-1'])
            ->setExpectedResult(404)
            ->run();
        testRun::testRun($this, 'POST', '/registration/ticket')
            ->setBody(['member' => self::$login, 'ticket_type' => $type->id, 'registered_by' => '-1'])
            ->setExpectedResult(404)
            ->run();

        $this->ticket = testRun::testRun($this, 'POST', '/registration/ticket')
            ->setBody(['member' => self::$login, 'ticket_type' => $type->id, 'badge_id' => '999'])
            ->run();

        $data = testRun::testRun($this, 'GET', '/registration/ticket/{id}')
            ->setUriParts(['id' => $this->ticket->id])
            ->run();
        $this->assertIncludes($data, 'event');
        $this->assertIncludes($data, 'registered_by');
        $this->assertIncludes($data, 'ticket_type');
        $this->assertIncludes($data, 'member');
        $this->assertEquals($data->badge_id, '999');

        $data = testRun::testRun($this, 'GET', '/member/find')
            ->setMethodParameters(['q' => '999', 'from' => 'badge_id'])
            ->run();
        $this->assertEquals($data->type, 'member_list');
        $this->assertNotEmpty($data->data);
        $this->assertEquals(count($data->data), 1);
        $this->assertEquals($data->data[0]->id, 1000);

        /* PUT */

        testRun::testRun($this, 'PUT', '/registration/ticket/{id}')
            ->setUriParts(['id' => $this->ticket->id])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'PUT', '/registration/ticket/{id}')
            ->setUriParts(['id' => $this->ticket->id])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'PUT', '/registration/ticket/{id}')
            ->setUriParts(['id' => $this->ticket->id])
            ->setBody(['bully' => 'frog'])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'PUT', '/registration/ticket/{id}')
            ->setUriParts(['id' => '-1'])
            ->setBody(['badge_name' => 'badggy', 'emergency_contact' => 'Mars', 'note'  => 'something important'])
            ->setExpectedResult(404)
            ->run();

        $data = testRun::testRun($this, 'PUT', '/registration/ticket/{id}')
            ->setUriParts(['id' => $this->ticket->id])
            ->setBody(['badge_name' => 'badggy', 'emergency_contact' => 'Mars', 'note'  => 'something important'])
            ->run();
        $this->assertEquals($data->badge_name, 'badggy');
        $this->assertEquals($data->emergency_contact, 'Mars');
        $this->assertEquals($data->note, 'something important');

        $data = testRun::testRun($this, 'GET', '/member/find')
            ->setMethodParameters(['q' => 'badggy', 'from' => 'badge'])
            ->run();
        $this->assertEquals($data->type, 'member_list');
        $this->assertNotEmpty($data->data);
        $this->assertEquals(count($data->data), 1);
        $this->assertEquals($data->data[0]->id, 1000);

        testRun::testRun($this, 'GET', '/registration/ticket/list/{id}')
            ->setUriParts(['id' => '-1'])
            ->setExpectedResult(404)
            ->run();
        testRun::testRun($this, 'GET', '/registration/ticket/list')->run();

        testRun::testRun($this, 'PUT', '/registration/ticket/{id}/email')
            ->setUriParts(['id' => '-1'])
            ->setExpectedResult(404)
            ->run();
        testRun::testRun($this, 'PUT', '/registration/ticket/{id}/email')
            ->setUriParts(['id' => $this->ticket->id])
            ->setNullReturn()
            ->run();
        testRun::testRun($this, 'PUT', '/registration/ticket/{id}/checkin')
            ->setUriParts(['id' => '-1'])
            ->setExpectedResult(404)
            ->run();
        testRun::testRun($this, 'PUT', '/registration/ticket/{id}/checkin')
            ->setUriParts(['id' => $this->ticket->id])
            ->setNullReturn()
            ->run();
        testRun::testRun($this, 'PUT', '/registration/ticket/{id}/pickup')
            ->setUriParts(['id' => '-1'])
            ->setExpectedResult(404)
            ->run();
        testRun::testRun($this, 'PUT', '/registration/ticket/{id}/pickup')
            ->setUriParts(['id' => $this->ticket->id])
            ->setNullReturn()
            ->run();

        testRun::testRun($this, 'PUT', '/registration/ticket/{id}/print')
            ->setUriParts(['id' => '-1'])
            ->setExpectedResult(404)
            ->run();
        testRun::testRun($this, 'PUT', '/registration/ticket/{id}/print')
            ->setUriParts(['id' => $this->ticket->id])
            ->setNullReturn()
            ->run();
        testRun::testRun($this, 'PUT', '/registration/ticket/{id}/lost')
            ->setUriParts(['id' => '-1'])
            ->setExpectedResult(404)
            ->run();
        testRun::testRun($this, 'PUT', '/registration/ticket/{id}/lost')
            ->setUriParts(['id' => $this->ticket->id])
            ->setNullReturn()
            ->run();
        testRun::testRun($this, 'PUT', '/registration/ticket/{id}/void', null, null, 400)
            ->setUriParts(['id' => $this->ticket->id])
            ->setExpectedResult(400)
            ->run();
        testRun::testRun($this, 'PUT', '/registration/ticket/{id}/void')
            ->setUriParts(['id' => '-1'])
            ->setBody(['reason' => 'PHP Test'])
            ->setExpectedResult(404)
            ->run();
        testRun::testRun($this, 'PUT', '/registration/ticket/{id}/void')
            ->setUriParts(['id' => $this->ticket->id])
            ->setBody(['reason' => 'PHP Test'])
            ->setNullReturn()
            ->run();
        testRun::testRun($this, 'PUT', '/registration/ticket/{id}/reinstate')
            ->setUriParts(['id' => '-1'])
            ->setExpectedResult(409)
            ->run();
        testRun::testRun($this, 'PUT', '/registration/ticket/{id}/reinstate')
            ->setUriParts(['id' => $this->ticket->id])
            ->run();

        $queue = testRun::testRun($this, 'GET', '/registration/ticket/printqueue')
            ->run();
        testRun::testRun($this, 'PUT', '/registration/ticket/printqueue/claim/{id}')
            ->setUriParts(['id' => '-1'])
            ->setExpectedResult(409)
            ->run();
        testRun::testRun($this, 'PUT', '/registration/ticket/printqueue/claim/{id}')
            ->setUriParts(['id' => $queue->data[0]->id])
            ->run();

        /* rebuild ticket */
        testRun::testRun($this, 'DELETE', '/registration/ticket/{id}')
            ->setUriParts(['id' => $this->ticket->id])
            ->run();

        $this->ticket = testRun::testRun($this, 'POST', '/registration/ticket')
            ->setBody(['member' => self::$login, 'ticket_type' => $type->id, 'badge_id' => '10101'])
            ->run();

        testRun::testRun($this, 'PUT', '/registration/ticket/{id}/void')
            ->setUriParts(['id' => $this->ticket->id])
            ->setBody(['reason' => 'PHP Test'])
            ->setNullReturn()
            ->run();

        testRun::testRun($this, 'GET', '/registration/ticket/{id}')
            ->setUriParts(['id' => $this->ticket->id])
            ->setExpectedResult(404)
            ->run();

        testRun::testRun($this, 'GET', '/registration/ticket/{id}')
            ->setUriParts(['id' => $this->ticket->id])
            ->setMethodParameters(['show_void' => '1'])
            ->run();
        testRun::testRun($this, 'PUT', '/registration/ticket/{id}/email')
            ->setUriParts(['id' => $this->ticket->id])
            ->setExpectedResult(404)
            ->run();
        testRun::testRun($this, 'PUT', '/registration/ticket/{id}/checkin')
            ->setUriParts(['id' => $this->ticket->id])
            ->setExpectedResult(409)
            ->run();
        testRun::testRun($this, 'PUT', '/registration/ticket/{id}/print')
            ->setUriParts(['id' => $this->ticket->id])
            ->setExpectedResult(404)
            ->run();
        testRun::testRun($this, 'PUT', '/registration/ticket/{id}/pickup')
            ->setUriParts(['id' => $this->ticket->id])
            ->setExpectedResult(409)
            ->run();
        testRun::testRun($this, 'PUT', '/registration/ticket/{id}/lost')
            ->setUriParts(['id' => $this->ticket->id])
            ->setExpectedResult(409)
            ->run();
        testRun::testRun($this, 'PUT', '/registration/ticket/{id}/reinstate')
            ->setUriParts(['id' => $this->ticket->id])
            ->run();

        testRun::testRun($this, 'DELETE', '/registration/ticket/{id}')
            ->setUriParts(['id' => -1])
            ->setExpectedResult(404)
            ->run();

    }


/* End */
}
