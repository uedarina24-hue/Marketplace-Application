<?php

namespace Tests\Feature\Purchase;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PurchaseTest extends TestCase
{
    use DatabaseMigrations;

    /** @test
     * 「購入する」ボタンを押下すると購入が完了する
     */
    public function user_can_complete_purchase()
    {
        $user = User::factory()->create([
            'postal_code' => '123-4567',
            'address' => '東京都新宿区',
            'building_name' => 'ビルA',
        ]);

        $item = Item::factory()->create();

        session(['payment_method' => 'card']);

        $this->actingAs($user)
            ->get(route('payment.success', ['item' => $item->id]))
            ->assertRedirect(route('items.index'));

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'card',
        ]);
    }

    /** @test
     * 購入した商品は商品一覧画面にて「sold」と表示される
     */
    public function purchased_item_shows_sold_on_index()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->get(route('items.index'));
        $response->assertSee('sold');
    }

    /** @test
     * 「プロフィール/購入した商品一覧」に追加されている
     */
    public function purchased_item_appears_in_user_mypage()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->get(route('mypage', ['page' => 'buy']));
        $response->assertSee($item->name);
    }
}