<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use DatabaseMigrations;

    /** @test
     * 名前が未入力だとエラー
     */
    public function test_name_is_required()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'name' => 'お名前を入力してください'
        ]);
    }

    /** @test
     * メールアドレス未入力
     */
    public function test_email_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください'
        ]);
    }

    /** @test
     * パスワード未入力
     */
    public function test_password_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください'
        ]);
    }

    /** @test
     * パスワード7文字以下
     */
    public function test_password_min_length()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'pass123',
            'password_confirmation' => 'pass123',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください'
        ]);
    }

    /** @test
     * 確認用パスワード不一致
     */
    public function test_password_confirmation_mismatch()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password456',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードと一致しません'
        ]);
    }

    /** @test
     * 正常登録 → メール認証画面へ遷移
     */
    public function test_user_can_register_and_redirect_to_email_verification()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // DBに登録されている
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'テスト太郎',
        ]);

        // メール認証画面へ遷移
        $response->assertRedirect('/email/verify');
    }
}