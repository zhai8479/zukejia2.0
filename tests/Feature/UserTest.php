<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAdminAddUser()
    {
        $input = [
            'mobile' => '135' . substr(strrev(time()), 0, 8),
            'password' => '123456'
        ];
        $user = \App\Models\User::admin_add_user($input);
        $this->assertTrue($user instanceof User);
    }
}
