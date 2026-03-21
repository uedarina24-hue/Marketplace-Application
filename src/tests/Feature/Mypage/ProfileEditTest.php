<?php

namespace Tests\Feature\Mypage;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProfileEditTest extends TestCase
{
    use DatabaseMigrations;

    /** @test
     * 変更項目が初期値として過去設定されていること（プロフィール画像、ユーザー名、郵便番号、住所）
     */
    public function profile_edit_form_displays_initial_values()
    {

        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'profile_image' => 'profiles/dummy_profile.jpg',
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区',
            'building_name' => 'テストビル101',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('mypage.profile.edit'));

        $response->assertStatus(200);

        $response->assertSee('value="テストユーザー"', false);
        $response->assertSee('value="123-4567"', false);
        $response->assertSee('value="東京都渋谷区"', false);
        $response->assertSee('value="テストビル101"', false);

        $response->assertSee('profiles/dummy_profile.jpg');
    }
}