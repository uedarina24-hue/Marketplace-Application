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

        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'profile_image' => 'profiles/dummy_profile.jpg',
        ]);

        $this->actingAs($user);

        $items = Item::factory()->count(2)->for($user)->create();
        foreach ($items as $item) {
            ItemImage::factory()->for($item)->create();
        }

        $purchasedItem = Item::factory()->create();
        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $purchasedItem->id,
        ]);

        $responseSell = $this->get(route('mypage', ['page' => 'sell']));
        $responseSell->assertStatus(200);

        $responseSell->assertSeeText('テストユーザー');
        $responseSell->assertSee($user->profile_image_url);

        foreach ($items as $item) {
            $responseSell->assertSeeText($item->name);
            $responseSell->assertSee($item->firstImage->image_url);
        }

        $responseBuy = $this->get(route('mypage', ['page' => 'buy']));
        $responseBuy->assertStatus(200);
        $responseBuy->assertSeeText($purchasedItem->name);
    }
}