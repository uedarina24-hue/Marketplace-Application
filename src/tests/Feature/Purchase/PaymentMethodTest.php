<?php

namespace Tests\Feature\Purchase;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PaymentMethodTest extends TestCase
{
    use DatabaseMigrations;

    /** @test
     * 小計画面で変更が反映されることを確認するテスト
     */
    public function card_payment_method_creates_purchase_record()
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
}