<?php

namespace Tests\Feature\Mypage;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserProfileTest extends TestCase
{
    use DatabaseMigrations;

    /** @test
     * 必要な情報が取得できる（プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧）
     */
    public function user_profile_displays_correct_information()
    {
        // ユーザー作成
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'profile_image' => 'profiles/dummy_profile.jpg',
        ]);

        $this->actingAs($user);

        // 出品商品作成
        $items = Item::factory()->count(2)->for($user)->create();
        foreach ($items as $item) {
            ItemImage::factory()->for($item)->create([
                'image_path' => 'items/dummy_item.jpg',
            ]);
        }

        // 購入商品作成
        $purchasedItem = Item::factory()->create();
        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $purchasedItem->id,
        ]);

        // プロフィール（マイページ）取得
        $response = $this->get(route('mypage', ['page' => 'sell'])); // ← ここを修正

        $response->assertStatus(200);

        // ユーザー情報
        $response->assertSeeText('テストユーザー');
        $response->assertSee('profiles/dummy_profile.jpg');

        // 出品商品
        foreach ($items as $item) {
            $response->assertSeeText($item->name);
            $response->assertSee('items/dummy_item.jpg');
        }

        // 購入商品は buy タブで確認
        $responseBuy = $this->get(route('mypage', ['page' => 'buy']));
        $responseBuy->assertStatus(200);
        $responseBuy->assertSeeText($purchasedItem->name);
    }
}