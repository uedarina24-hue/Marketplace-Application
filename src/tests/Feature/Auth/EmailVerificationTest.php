<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use DatabaseMigrations;

    /**　@test
     *  会員登録すると認証メールが送信される
     */
    public function test_verification_email_is_sent_after_registration()
    {
        \Notification::fake();

        $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        \Notification::assertSentTo($user, \Illuminate\Auth\Notifications\VerifyEmail::class);
    }

    /** @test
     * 認証リンクにアクセスできる
     */
    public function test_verification_link_can_be_opened()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
            'postal_code' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertStatus(302);
        $response->assertRedirect(route('mypage.profile.edit'));
    }

    /** @test
     * メール認証完了後、プロフィール画面に遷移する
     */
    public function test_user_is_redirected_to_profile_after_verification()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
            'postal_code' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $response = $this->actingAs($user)->get($verificationUrl);
        $this->assertNotNull($user->fresh()->email_verified_at);
        $response->assertRedirect(route('mypage.profile.edit'));
    }
}