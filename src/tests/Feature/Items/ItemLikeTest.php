<?php

namespace Tests\Feature\Items;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ItemLikeTest extends TestCase
{
    use DatabaseMigrations;

    /** @test
     * いいねアイコンを押下することによって、いいねした商品として登録することができる。
     * 追加済みのアイコンは色が変化する
     */
    public function user_can_like_an_item_and_icon_changes()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        // いいね押下
        $response = $this->post(route('likes.toggle', $item));

        $response->assertStatus(302);
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 再度商品ページを取得してアイコンの画像が変わっているか確認
        $response = $this->get(route('items.show', $item));
        $response->assertStatus(200);
        $response->assertSee('images/heart_pink.png');
        $response->assertSee($item->likes()->count());
    }

    /** @test
     * 再度いいねアイコンを押下することによって、いいねを解除することができる。
     */
    public function user_can_unlike_an_item_and_icon_reverts()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $item->likes()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        // いいね解除
        $response = $this->post(route('likes.toggle', $item));

        $response->assertStatus(302);
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->get(route('items.show', $item));
        $response->assertStatus(200);
        $response->assertSee('images/heart_default.png');
        $response->assertSee($item->likes()->count());
    }
}