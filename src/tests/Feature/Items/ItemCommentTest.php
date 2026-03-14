<?php

namespace Tests\Feature\Items;

use App\Models\User;
use App\Models\Item;
use App\Models\Comment;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ItemCommentTest extends TestCase
{
    use DatabaseMigrations;

    /**　@test
     * ログイン済みユーザーはコメントを送信できる
     */
    public function test_logged_in_user_can_submit_comment()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('comments.store', $item), [
                'content' => 'テストコメント',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'テストコメント',
        ]);

        $this->assertEquals(1, $item->comments()->count());
    }

    /**　@test
     * ログイン前のユーザーはコメントを送信できない
     */
    public function test_guest_cannot_submit_comment()
    {
        $item = Item::factory()->create();

        $response = $this->post(route('comments.store', $item), [
            'content' => 'テストコメント',
        ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseMissing('comments', [
            'content' => 'テストコメント',
        ]);
    }

    /**　@test
     * コメントが空の場合、バリデーションメッセージが表示される
     */
    public function test_comment_is_required()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('comments.store', $item), [
                'content' => '',
            ]);

        $response->assertSessionHasErrors([
            'content' => 'コメントを入力してください',
        ]);

        $this->assertEquals(0, $item->comments()->count());
    }

    /**　@test
     * コメントが255文字以上の場合、バリデーションメッセージが表示される
     */
    public function test_comment_max_length_validation()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $longComment = str_repeat('a', 256);

        $response = $this->actingAs($user)
            ->post(route('comments.store', $item), [
                'content' => $longComment,
            ]);

        $response->assertSessionHasErrors([
            'content' => 'コメントは255文字以内で入力してください',
        ]);

        $this->assertEquals(0, $item->comments()->count());
    }
}