<?php

namespace App\Tests\TestCase\Controller;

use Atlas\Query\Delete;
use Atlas\Query\Insert;
use App\Tests\Base\CiabTestCase;

class ArtshowConfigurationTest extends CiabTestCase
{

    private $question_id = null;


    protected function cleanup(): void
    {
        $list = $this->stringListsProvider();
        foreach ($list as $test) {
            foreach ($test['input'] as $entry) {
                $this->runRequest(
                    'DELETE',
                    $test['uri'].'/'.$entry
                );
            }
        }
        if ($this->question_id !== null) {
            $this->runRequest(
                'DELETE',
                '/artshow/configuration/registrationquestion/'.$this->question_id
            );
            $this->question_id = null;
        }

    }


    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanup();

    }


    protected function tearDown(): void
    {
        $this->cleanup();
        parent::tearDown();

    }


    public function stringListsProvider(): array
    {
        return array(
            [
                'uri' => '/artshow/configuration/paymenttype',
                'type' => 'payment_type',
                'target' => 'payment',
                'input' => ['PhpTestCredit', 'PhpTestCredit2']
            ],
            [
                'uri' => '/artshow/configuration/piecetype',
                'type' => 'piece_type',
                'target' => 'piece',
                'input' => ['PhpTestType', 'PhpTestType2']
            ],
            [
                'uri' => '/artshow/configuration/returnmethod',
                'type' => 'return_method',
                'target' => 'method',
                'input' => ['PhpTestSnail', 'PhpTestBeaver']
            ],
            [
                'uri' => '/artshow/configuration/pricetype',
                'type' => 'price_type',
                'target' => 'price',
                'input' => ['PhpTestGold', 'PhpTestSilver'],
                'xtra' => ['artist_set' => true],
            ]
        );

    }


    /**
     * @test
     * @dataProvider stringListsProvider
     **/
    public function testConfigList($uri, $type, $target, $input, $xtra = null): void
    {
        $param = [$target => $input[0]];
        if ($xtra) {
            $param = array_merge($param, $xtra);
        }
        $data = $this->runSuccessJsonRequest(
            'POST',
            $uri,
            null,
            $param,
            201
        );
        $this->assertNotEmpty($data);
        $this->assertSame($data->type, $type);
        $this->assertSame($data->$target, $input[0]);

        $data = $this->runSuccessJsonRequest('GET', $uri);
        $this->assertNotEmpty($data);
        $this->assertSame($data->type, $type.'_list');
        $found = false;
        foreach ($data->data as $entry) {
            if ($entry->$target == $input[0]) {
                $found = true;
            }
        }
        $this->assertTrue($found);

        $data = $this->runSuccessJsonRequest('GET', $uri.'/'.$input[0]);
        $this->assertNotEmpty($data);
        $this->assertSame($data->type, $type);
        $this->assertSame($data->$target, $input[0]);

        $this->runRequest(
            'PUT',
            $uri.'/'.$input[0],
            null,
            [$target => $input[1]],
            200
        );

        $this->runRequest('GET', $uri.'/'.$input[0], null, null, 404);

        $data = $this->runSuccessJsonRequest('GET', $uri.'/'.$input[1]);
        $this->assertNotEmpty($data);
        $this->assertSame($data->type, $type);
        $this->assertSame($data->$target, $input[1]);

        $data = $this->runSuccessJsonRequest('GET', $uri);
        $this->assertNotEmpty($data);
        $this->assertSame($data->type, $type.'_list');
        $found_old = false;
        $found = false;
        foreach ($data->data as $entry) {
            if ($entry->$target == $input[0]) {
                $found_old = true;
            }
            if ($entry->$target == $input[1]) {
                $found = true;
            }
        }
        $this->assertTrue($found && !$found_old);

        $this->runRequest(
            'DELETE',
            $uri.'/'.$input[0],
            null,
            null,
            404
        );

        $this->runRequest(
            'DELETE',
            $uri.'/'.$input[1],
            null,
            null,
            204
        );

    }


    public function testRegistrationQuesion(): void
    {
        $text = 'Are you PHP Test?';
        $text2 = 'Are you PHP Really Test?';
        $uri = '/artshow/configuration/registrationquestion';
        $type = 'registration_question';
        $data = $this->runSuccessJsonRequest(
            'POST',
            $uri,
            null,
            ['text' => $text, 'boolean' => 1],
            201
        );
        $this->assertNotEmpty($data);
        $this->assertSame($data->type, $type);
        $this->assertSame($data->text, $text);
        $this->question_id = $data->id;

        $data = $this->runSuccessJsonRequest('GET', $uri);
        $this->assertNotEmpty($data);
        $this->assertSame($data->type, 'registration_question_list');
        $found = false;
        foreach ($data->data as $entry) {
            if ($entry->id == $this->question_id) {
                $found = true;
            }
        }
        $this->assertTrue($found);

        $data = $this->runSuccessJsonRequest('GET', $uri.'/'.$this->question_id);
        $this->assertNotEmpty($data);
        $this->assertSame($data->type, $type);
        $this->assertSame($data->text, $text);

        $this->runRequest(
            'PUT',
            $uri.'/'.$this->question_id,
            null,
            ['text' => $text2],
            200
        );

        $data = $this->runSuccessJsonRequest('GET', $uri.'/'.$this->question_id);
        $this->assertNotEmpty($data);
        $this->assertSame($data->type, $type);
        $this->assertSame($data->text, $text2);

        $this->runRequest(
            'DELETE',
            $uri.'/'.$this->question_id,
            null,
            null,
            204
        );

        $this->question_id = null;

    }


    public function testOverview(): void
    {
        $data = $this->runSuccessJsonRequest('GET', '/artshow/');

    }


    /* End */
}
