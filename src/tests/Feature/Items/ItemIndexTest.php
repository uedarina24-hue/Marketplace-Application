<?php

namespace Tests\Feature\Items;

use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ItemIndexTest extends TestCase
{
    use DatabaseMigrations;

    /** @test
     * 全商品を取得できるテスト
     */
    public function all_items_are_displayed()
    {
        $user = User::factory()->create();
        $items = Item::factory(3)->create();

        $response = $this->actingAs($user)->get(route('items.index'));

        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }

    /** @test
     * 購入済み商品は「Sold」と表示されるテスト
     */
    public function sold_label_is_displayed_for_purchased_items()
    {
        $user = User::factory()->create();
        $buyer = User::factory()->create();

        $item = Item::factory()->create();

        // 購入情報を作成
        $item->purchase()->create([
            'user_id' => $buyer->id,
            'payment_method' => 'credit_card',
            'stripe_session_id' => 'sess_123',
            'postal_code' => '123-4567',
            'address' => 'Tokyo',
        ]);

        $response = $this->actingAs($user)->get(route('items.index'));

        $response->assertSee('Sold');
    }

    /** @test
     * ユーザーが所有する商品は表示されないテスト
     */
    public function user_items_are_not_displayed_for_owner()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('items.index'));

        $response->assertDontSee($item->name);
    }
}