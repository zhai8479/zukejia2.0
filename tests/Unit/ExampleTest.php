<?php

namespace Tests\Unit;

use App\Library\Recommend;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $this->assertTrue(true);
    }

    public function testRecommend()
    {
        $user_id = 10000;
        $code = Recommend::create_code($user_id);
        $this->assertEquals($code, '0QG7');
        $uid = Recommend::decode($code);
        $this->assertEquals($uid, $user_id);
    }

    public function testSendSms()
    {
//        \Sms::send('13517210601', 'template_register_key_name', ['code' => '123456']);
    }
}
