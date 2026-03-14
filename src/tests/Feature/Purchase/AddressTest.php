<?php

namespace Tests\Feature\Purchase;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AddressTest extends TestCase
{
    use DatabaseMigrations;

    /** @test
     * 送付先住所変更画面にて登録した住所が商品購入画面に反映されていることを確認する
     */
    public function user_can_update_shipping_address()
    {
        $user = User::factory()->create([
            'postal_code' => '111-1111',
            'address' => '旧住所',
            'building_name' => '旧ビル',
        ]);

        $item = Item::factory()->create();

        $this->actingAs($user)
            ->put(route('purchase.address.update', ['item' => $item->id]), [
                'postal_code' => '222-2222',
                'address' => '新住所',
                'building_name' => '新ビル',
            ])
            ->assertRedirect(route('purchase.index', ['item' => $item->id]));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'postal_code' => '222-2222',
            'address' => '新住所',
            'building_name' => '新ビル',
        ]);
    }

    /** @test
     * 購入した商品に送付先住所が紐づいて登録されることを確認する
     */
    public function purchased_item_is_linked_to_updated_address()
    {
        $user = User::factory()->create([
            'postal_code' => '333-3333',
            'address' => '東京都港区',
            'building_name' => 'ビルA',
        ]);

        $item = Item::factory()->create();

        session(['payment_method' => 'card']);

        $this->actingAs($user)
            ->get(route('payment.success', ['item' => $item->id]));

        $purchase = Purchase::first();

        $this->assertEquals($user->postal_code, $purchase->postal_code);
        $this->assertEquals($user->address, $purchase->address);
        $this->assertEquals($user->building_name, $purchase->building_name);
    }
}