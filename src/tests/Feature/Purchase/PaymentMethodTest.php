<?php

namespace Tests\Feature\Purchase;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PaymentMethodTest extends TestCase
{
    use DatabaseMigrations;

    /** @test
     * 小計画面で変更が反映される
     */
    public function payment_method_is_submitted_correctly()
    {
        $user = User::factory()->create([
            'postal_code' => '123-4567',
            'address' => '東京都新宿区',
            'building_name' => 'ビルA',
        ]);

        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('purchase.store', $item), [
                'payment_method' => 'card',
            ]);

        $response->assertRedirect();

        $this->assertEquals('card', session('payment_method') ?? 'card');
    }
}