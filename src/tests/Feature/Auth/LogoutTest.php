<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LogoutTest extends TestCase
{
    use DatabaseMigrations;

    /** @test
     *  ログアウトができるテスト
     */
    public function user_can_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}